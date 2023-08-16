<?php

namespace App\Http\Controllers;

use App\Models\Advertisement;
use App\Models\Chat;
use App\Models\TradeRequest;
use App\Models\GeneralSetting;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Rules\FileTypeValidate;
use Carbon\Carbon;
use Illuminate\Http\Request;


class TradeController extends Controller
{
    public function __construct()
    {
        $this->activeTemplate = activeTemplate();
    }

    public function tradeRequests()
    {
        $pageTitle = 'Trade Requests';
        $emptyMessage = 'No data found';

        $runningTradeRequests = TradeRequest::where([['status', '!=', 1],['status', '!=', 9]])->where(function ($q){
            $q->where('buyer_id', auth()->user()->id)->orWhere('seller_id', auth()->user()->id);
        })->with(['fiat','fiatGateway','advertisement','crypto','buyer','seller'])->latest()->paginate(getPaginate(10), ['*'],'running');

        $endedTradeRequests = TradeRequest::where([['status', '!=', 0],['status', '!=', 2],['status', '!=', 8]])->where(function ($q){
            $q->where('buyer_id', auth()->user()->id)->orWhere('seller_id', auth()->user()->id);
        })->with(['fiat','fiatGateway','advertisement','crypto','buyer','seller'])->latest()->paginate(getPaginate(10), ['*'],'ended');

        return view($this->activeTemplate . 'user.trade.request', compact('pageTitle', 'emptyMessage', 'runningTradeRequests', 'endedTradeRequests'));
    }

    public function tradeRequestNew($id)
    {
        $advertisement = Advertisement::where('status', 1)->findOrFail($id);

        if ($advertisement->user_id == auth()->user()->id) {
            $notify[] = ['error', 'This is your own trade'];
            return back()->withNotify($notify);
        }

        $maxLimit = $advertisement->max;

        if ($advertisement->type == 2) {

            $userWallet = $advertisement->user->wallets->where('crypto_id',$advertisement->crypto_id)->first();
            $rate = rate($advertisement->type,$advertisement->crypto->rate,$advertisement->fiat->rate,$advertisement->margin);
            $userMax = $userWallet->balance * $rate;
            $maxLimit = $advertisement->max < $userMax ? $advertisement->max : $userMax;

            if ($maxLimit < $advertisement->min) {
                $notify[] = ['error', 'Seller does not have enough balance'];
                return back()->withNotify($notify);
            }
        }

        $pageTitle = 'New Trade Request';

        return view($this->activeTemplate . 'trade_request', compact('pageTitle', 'advertisement', 'maxLimit'));
    }

    public function tradeRequestStore(Request $request, $id)
    {
        $advertisement = Advertisement::where('status', 1)->where('user_id', '!=', auth()->id())->findOrFail($id);
        $maxLimit = $advertisement->max;
        if ($advertisement->type == 2) {

            $userWallet = $advertisement->user->wallets->where('crypto_id',$advertisement->crypto_id)->first();
            $rate = rate($advertisement->type,$advertisement->crypto->rate,$advertisement->fiat->rate,$advertisement->margin);
            $userMax = $userWallet->balance * $rate;
            $maxLimit = $advertisement->max < $userMax ? $advertisement->max : $userMax;

            if ($maxLimit < $advertisement->min) {
                $notify[] = ['error', 'Seller does not have enough balance'];
                return back()->withNotify($notify);
            }
        }

        $request->validate([
            'amount' => "required|numeric|min:$advertisement->min|max:$advertisement->max",
            'details' => 'required',
        ]);

        $user = auth()->user();

        $finalAmount = (1 / rate($advertisement->type, $advertisement->crypto->rate, $advertisement->fiat->rate,$advertisement->margin)) * $request->amount;
        $general     = GeneralSetting::first();
        $tradeCharge = ($finalAmount * $general->trade_charge) / 100;
        
        if ($advertisement->type == 1) {
            $sellerId = $user->id;
            $buyerId = $advertisement->user->id;
            $sellerName = $user->fullname;
            $buyerName = $advertisement->user->fullname;
        }else{
            $sellerId = $advertisement->user->id;
            $buyerId = $user->id;
            $sellerName = $advertisement->user->fullname;
            $buyerName = $user->fullname;
        }

        $wallet = Wallet::where('user_id', $sellerId)->where('crypto_id',$advertisement->crypto_id)->first();

        if (!$wallet) {
            $notify[] = ['error', 'You can not proceed this action'];
            return back()->withNotify($notify);
        }

        if ($wallet->balance < $finalAmount) {
            $notify[] = ['error', 'Seller doesn\'t  have sufficient balance'];
            return back()->withNotify($notify);
        }

        $wallet->balance -= ($finalAmount + $tradeCharge);
        $wallet->save();

        $transaction = new Transaction();
        $transaction->user_id = $sellerId;
        $transaction->crypto_id = $advertisement->crypto_id;
        $transaction->amount = $finalAmount;
        $transaction->post_balance = $wallet->balance;
        $transaction->charge     = $tradeCharge;
        $transaction->trx_type = '-';
        $transaction->details = 'Subtracted from ' . $wallet->crypto->code .' wallet for a sell trade';
        $transaction->trx = getTrx();
        $transaction->save();

        $tradeRequest = new TradeRequest();
        $tradeRequest->uid = getTrx(6);
        $tradeRequest->advertisement_id = $advertisement->id;
        $tradeRequest->seller_id = $sellerId;
        $tradeRequest->buyer_id = $buyerId;
        $tradeRequest->amount = $request->amount;
        $tradeRequest->crypto_amount = $finalAmount;
        $tradeRequest->trade_charge       = $tradeCharge;
        $tradeRequest->crypto_id = $advertisement->crypto->id;
        $tradeRequest->fiat_gateway_id = $advertisement->fiatGateway->id;
        $tradeRequest->fiat_id = $advertisement->fiat->id;
        $tradeRequest->window = $advertisement->window;
        $tradeRequest->exchange_rate = rate($advertisement->type,$advertisement->crypto->rate,$advertisement->fiat->rate,$advertisement->margin);
        $tradeRequest->status = 0;
        $tradeRequest->save();

        $chat = new Chat();
        $chat->trade_request_id = $tradeRequest->id;
        $chat->user_id = auth()->user()->id;
        $chat->message = $request->details;
        $chat->file = null;
        $chat->save();

        notify($advertisement->user, 'NEW_TRADE', [
            'buyer' => $buyerName,
            'seller' => $sellerName,
            'fiat_amount' => showAmount($tradeRequest->amount,2),
            'fiat_currency' => $advertisement->fiat->code,
            'crypto_amount' => showAmount($tradeRequest->crypto_amount,8),
            'crypto_currency' => $advertisement->crypto->code,
            'window' => $tradeRequest->window
        ]);


        $notify[] = ['success', 'Your request has been taken'];
        return redirect()->route('user.trade.request.details',$tradeRequest->uid)->withNotify($notify);
    }

    public function tradeRequestDetails($id)
    {

        $tradeRequestsDetails = TradeRequest::where('uid', $id)->where(function($q){
            $q->orWhere('buyer_id', auth()->user()->id)->orWhere('seller_id', auth()->user()->id);
        })->firstOrFail();

        $pageTitle = 'Trade#'.$tradeRequestsDetails->uid;

        $title = '';

        if($tradeRequestsDetails->seller_id == auth()->user()->id){
            $title .= 'Selling ';
        }else{
            $title .= 'Buying ';
        }

        $title .= showAmount($tradeRequestsDetails->crypto_amount,8).' '.$tradeRequestsDetails->crypto->code.' For '.showAmount($tradeRequestsDetails->amount).' '.$tradeRequestsDetails->fiat->code.' via '.$tradeRequestsDetails->fiatGateway->name;
        $title2 = ' Exchange Rate '. showAmount($tradeRequestsDetails->exchange_rate). ' '.$tradeRequestsDetails->fiat->code.'/'.$tradeRequestsDetails->crypto->code;

        return view($this->activeTemplate . 'user.trade.details', compact('pageTitle', 'tradeRequestsDetails', 'title', 'title2'));
    }
    
    public function chatsAjax(Request $request)
    {

        $tradeRequestsDetails = TradeRequest::where('uid', $request->id)->where(function($q){
            $q->orWhere('buyer_id', auth()->user()->id)->orWhere('seller_id', auth()->user()->id);
        })->firstOrFail();


        //return view($this->activeTemplate . 'user.trade.details', compact('pageTitle', 'tradeRequestsDetails', 'title', 'title2'));
        //return json_encode(array('data'=>$tradeRequestsDetails));
        //return ['success' => true, 'data' => 'New user created !!'];
        // $wallet = Wallet::where('user_id', auth()->user()->id)->findOrFail($request->wallet_id);
       //  $wallet->balance += 5;
       //     $wallet->save();
       
       foreach ($tradeRequestsDetails->chats()->get() as $item){
            $item['buyer_id'] = $tradeRequestsDetails->buyer_id;
            $item['seller_id'] = $tradeRequestsDetails->seller_id;
            $item['buyer_fullname'] = $tradeRequestsDetails->buyer->fullname;
            $item['seller_fullname'] = $tradeRequestsDetails->seller->fullname;
            $item['created_at_diff'] = $item->created_at->diffForHumans();
            $item['trade_id'] = $tradeRequestsDetails->id;
            $item['buyer_image'] = getImage(imagePath()['profile']['user']['path'].'/'. $tradeRequestsDetails->buyer->image,imagePath()['profile']['user']['size']);
            $item['seller_image'] = getImage(imagePath()['profile']['user']['path'].'/'. $tradeRequestsDetails->seller->image,imagePath()['profile']['user']['size']);
            
            $chatdetails[] = $item;
       }

        return $chatdetails;
    }
    

    public function tradeRequestCancel(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|gt:0'
        ]);

        $tradeRequest = TradeRequest::where('status', 0)->where(function($q){
            $q->orWhere('buyer_id', auth()->user()->id)->orWhere('seller_id', auth()->user()->id);
        })->findOrFail($request->id);

        $endTime = $tradeRequest->created_at->addMinutes($tradeRequest->window);
        $remainingMinutes = $endTime->diffInMinutes(now());

        if(($tradeRequest->seller_id == auth()->id() ) && ($endTime > now()) ) {
            $notify[] = ['error', "You can cancel this trade after $remainingMinutes minutes"];
            return back()->withNotify($notify);
        }

        $wallet = Wallet::where('user_id', $tradeRequest->seller->id)->where('crypto_id',$tradeRequest->crypto_id)->first();
        if (!$wallet) {
            $notify[] = ['error', 'You can not proceed this action'];
            return back()->withNotify($notify);
        }

        $tradeRequest->status = 9;
        $tradeRequest->details = 'Canceled by '.auth()->user()->fullname;
        $tradeRequest->save();

        $wallet->balance += ($tradeRequest->crypto_amount + $tradeRequest->trade_charge);
        $wallet->save();

        $transaction = new Transaction();
        $transaction->user_id = $tradeRequest->seller->id;
        $transaction->crypto_id = $tradeRequest->crypto_id;
        $transaction->amount = $tradeRequest->crypto_amount;
        $transaction->post_balance = $wallet->balance;
        $transaction->charge = 0;
        $transaction->trx_type = '+';
        $transaction->details = 'Added With Your ' . $wallet->crypto->code .' Wallet as Refund For Cancellation of a Sell Trade';
        $transaction->trx = getTrx();
        $transaction->save();

        $chat = new Chat();
        $chat->trade_request_id = $tradeRequest->id;
        $chat->user_id = auth()->user()->id;
        $chat->message = auth()->user()->fullname.' canceled this trade';
        $chat->file = null;
        $chat->save();

        notify($tradeRequest->buyer, 'TRADE_CANCELED', [
            'name' => auth()->user()->fullname,
            'crypto_amount' => showAmount($tradeRequest->crypto_amount,8),
            'crypto_currency' => $tradeRequest->crypto->code,
            'fiat_currency' => $tradeRequest->fiat->code,
            'buyer' => $tradeRequest->buyer->fullname,
            'seller' => $tradeRequest->seller->fullname,
            'fiat_amount' => showAmount($tradeRequest->amount,2),
            'window' => $tradeRequest->window
        ]);

        notify($tradeRequest->seller, 'TRADE_CANCELED', [
            'name' => auth()->user()->fullname,
            'crypto_amount' => showAmount($tradeRequest->crypto_amount,8),
            'crypto_currency' => $tradeRequest->crypto->code,
            'fiat_currency' => $tradeRequest->fiat->code,
            'buyer' => $tradeRequest->buyer->fullname,
            'seller' => $tradeRequest->seller->fullname,
            'fiat_amount' => showAmount($tradeRequest->amount,2),
            'window' => $tradeRequest->window
        ]);

        $notify[] = ['success', 'Cancelled Successfully'];
        return back()->withNotify($notify);
    }

    public function tradeRequestPaid(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|gt:0'
        ]);

        $tradeRequest = TradeRequest::where('status', 0)->where('buyer_id', auth()->user()->id)->findOrFail($request->id);

        $tradeRequest->status = 2;
        $tradeRequest->details = 'Paid by '.auth()->user()->fullname;
        $tradeRequest->paid_at = Carbon::now();
        $tradeRequest->save();

        $chat = new Chat();
        $chat->trade_request_id = $tradeRequest->id;
        $chat->user_id = auth()->user()->id;
        $chat->message = auth()->user()->fullname.' has marked this trade as paid. Check if you have received the payment';
        $chat->file = null;
        $chat->save();

        notify($tradeRequest->seller, 'BUYER_PAID', [
            'fiat_currency' => $tradeRequest->fiat->code,
            'buyer' => $tradeRequest->buyer->fullname,
            'seller' => $tradeRequest->seller->fullname,
            'fiat_amount' => showAmount($tradeRequest->amount,2),
            'crypto_amount' => showAmount($tradeRequest->crypto_amount,8),
            'crypto_currency' => $tradeRequest->crypto->code,
            'window' => $tradeRequest->window
        ]);

        $notify[] = ['success', 'Marked as paid successfully'];
        return back()->withNotify($notify);
    }

    public function tradeRequestDispute(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|gt:0'
        ]);

        $user = auth()->user();

        $tradeRequest = TradeRequest::where('status', 2)->where(function($q) use($user){
            $q->orWhere('buyer_id', $user->id)->orWhere('seller_id', $user->id);
        })->findOrFail($request->id);

        if ($tradeRequest->buyer_id == $user->id && $tradeRequest->paid_at && ((Carbon::parse($tradeRequest->paid_at)->addMinutes($tradeRequest->window)) > Carbon::now())) {

            $notify[] = ['error', 'You can not proceed this action right now'];
            return back()->withNotify($notify);
        }

        if ($tradeRequest->seller_id == $user->id && (($tradeRequest->created_at->addMinutes($tradeRequest->window)) > Carbon::now())) {

            $notify[] = ['error', 'You can not proceed this action right now'];
            return back()->withNotify($notify);
        }

        $tradeRequest->status = 8;
        $tradeRequest->details = 'Reported by '.$user->fullname;
        $tradeRequest->save();

        $chat = new Chat();
        $chat->trade_request_id = $tradeRequest->id;
        $chat->user_id = null;
        $chat->admin = 1;
        $chat->message = 'Reported By - ' .$user->fullname. ' Please Solve Issue with buyer and seller';
        $chat->file = null;
        $chat->save();

        notify($tradeRequest->seller, 'TRADE_REPORTED', [
            'name' => $user->fullname,
            'crypto_amount' => showAmount($tradeRequest->crypto_amount,8),
            'crypto_currency' => $tradeRequest->crypto->code,
            'fiat_currency' => $tradeRequest->fiat->code,
            'buyer' => $tradeRequest->buyer->fullname,
            'seller' => $tradeRequest->seller->fullname,
            'fiat_amount' => showAmount($tradeRequest->amount,2),
            'window' => $tradeRequest->window
        ]);

        notify($tradeRequest->buyer, 'TRADE_REPORTED', [
            'name' => $user->fullname,
            'crypto_amount' => showAmount($tradeRequest->crypto_amount,8),
            'crypto_currency' => $tradeRequest->crypto->code,
            'fiat_currency' => $tradeRequest->fiat->code,
            'buyer' => $tradeRequest->buyer->fullname,
            'seller' => $tradeRequest->seller->fullname,
            'fiat_amount' => showAmount($tradeRequest->amount,2),
            'window' => $tradeRequest->window
        ]);

        $notify[] = ['success', 'Reported successfully'];
        return back()->withNotify($notify);
    }

    public function tradeRequestRelease(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|gt:0'
        ]);

        $tradeRequest = TradeRequest::where('status', 2)->where('seller_id', auth()->user()->id)->findOrFail($request->id);
        $wallet = Wallet::where('user_id', $tradeRequest->buyer->id)->where('crypto_id',$tradeRequest->crypto_id)->first();

        if (!$wallet) {
            $notify[] = ['error', 'You can not proceed this action'];
            return back()->withNotify($notify);
        }

        $tradeRequest->status = 1;
        $tradeRequest->save();

        $wallet->balance += $tradeRequest->crypto_amount;
        $wallet->save();

        $transaction = new Transaction();
        $transaction->user_id = $tradeRequest->buyer->id;
        $transaction->crypto_id = $tradeRequest->crypto_id;
        $transaction->amount = $tradeRequest->crypto_amount;
        $transaction->post_balance = $wallet->balance;
        $transaction->charge = 0;
        $transaction->trx_type = '+';
        $transaction->details = 'Added With Your ' . $wallet->crypto->code .' Wallet For Buying '.$wallet->crypto->name;
        $transaction->trx = getTrx();
        $transaction->save();

        $chat = new Chat();
        $chat->trade_request_id = $tradeRequest->id;
        $chat->user_id = auth()->user()->id;
        $chat->message = auth()->user()->fullname.' has marked this as completed';
        $chat->file = null;
        $chat->save();

        notify($tradeRequest->seller, 'TRADE_COMPLETED', [
            'name' => $tradeRequest->buyer->fullname,
            'crypto_amount' => showAmount($tradeRequest->crypto_amount,8),
            'crypto_currency' => $tradeRequest->crypto->code,
            'fiat_currency' => $tradeRequest->fiat->code,
            'buyer' => $tradeRequest->buyer->fullname,
            'seller' => $tradeRequest->seller->fullname,
            'fiat_amount' => showAmount($tradeRequest->amount,2),
            'window' => $tradeRequest->window
        ]);

        notify($tradeRequest->buyer, 'TRADE_COMPLETED', [
            'name' => $tradeRequest->buyer->fullname,
            'crypto_amount' => showAmount($tradeRequest->crypto_amount,8),
            'crypto_currency' => $tradeRequest->crypto->code,
            'fiat_currency' => $tradeRequest->fiat->code,
            'buyer' => $tradeRequest->buyer->fullname,
            'seller' => $tradeRequest->seller->fullname,
            'fiat_amount' => showAmount($tradeRequest->amount,2),
            'window' => $tradeRequest->window
        ]);

        $notify[] = ['success', 'Released Successfully'];
        return back()->withNotify($notify);

    }

    public function tradeRequestChatStore(Request $request, $id)
    {
        $tradeRequest = TradeRequest::where(function($q){
            $q->orWhere('buyer_id', auth()->user()->id)->orWhere('seller_id', auth()->user()->id);
        })->where(function($status){
            $status->where('status', 0)->orWhere('status', 2)->orWhere('status', 8);
        })->where('id', $id)->first();

        if (!$tradeRequest) {
            return response()->json(['success' => 'You are not allowed to proceed this action']);
        }

        $request->validate([
            'message' => 'required',
            'file' => ['nullable',new FileTypeValidate(['jpg', 'jpeg', 'png', 'pdf']),'max:2000'],
        ]);

        $file = null;

        if ($request->file) {
            if($request->hasFile('file')) {
                try{

                    $location = imagePath()['chat_file']['path'];
                    $file = uploadFile($request->file, $location);

                }catch(\Exception $exp) {
                    return back()->withNotify(['error', 'Could not upload the file.']);
                }
            }
        }

        $chat = new Chat();
        $chat->trade_request_id = $tradeRequest->id;
        $chat->user_id = auth()->user()->id;
        $chat->message = $request->message;
        $chat->file = $file;
        $chat->save();

        $notify[] = ['success', 'Your response has been taken'];
        return back()->withNotify($notify);
    }

    public function download($tradeId, $id)
    {
        $chat = Chat::where('trade_request_id', $tradeId)->findOrFail($id);

        if ($chat->file) {

            $file = $chat->file;
            $full_path = 'assets/images/trade_files/' . $file;
            $title = $chat->file;
            $ext = pathinfo($file, PATHINFO_EXTENSION);
            $mimetype = mime_content_type($full_path);
            header('Content-Disposition: attachment; filename="' . $title . '.' . $ext . '";');
            header("Content-Type: " . $mimetype);
            return readfile($full_path);

        }else{
            $notify[] = ['error', 'No downloadable file found'];
            return back()->withNotify($notify);
        }

    }
}
