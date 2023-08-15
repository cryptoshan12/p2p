<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\TradeRequest;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;

class TradeController extends Controller
{
    public function tradeRequests()
    {
        $pageTitle = 'Trade Requests';
        $tradeRequests = TradeRequest::whereNotIn('status',[1,9,8])->with(['buyer','seller','fiatGateway','crypto','fiat'])->latest()->paginate(getPaginate());
        $emptyMessage = 'No data found';

        return view('admin.trade.index',compact('pageTitle', 'tradeRequests', 'emptyMessage'));
    }

    public function reportedTradeRequests()
    {
        $pageTitle = 'Reported Trade Requests';

        $tradeRequests = TradeRequest::where('status', 8)->with(['buyer','seller','fiatGateway','crypto','fiat'])->latest()->paginate(getPaginate());
        $emptyMessage = 'No data found';

        return view('admin.trade.index',compact('pageTitle', 'tradeRequests', 'emptyMessage'));
    }

    public function completedTradeRequests()
    {
        $pageTitle = 'Completed Trade Requests';
        $tradeRequests = TradeRequest::whereIn('status',[1,9])->with(['buyer','seller','fiatGateway','crypto','fiat'])->latest()->paginate(getPaginate());
        $emptyMessage = 'No data found';

        return view('admin.trade.index',compact('pageTitle', 'tradeRequests', 'emptyMessage'));
    }

    public function tradeRequestDetails($id)
    {
        $pageTitle = 'Trade Request Details';
        $tradeRequestDetails = TradeRequest::findOrFail($id);

        return view('admin.trade.view',compact('pageTitle', 'tradeRequestDetails'));
    }

    public function tradeRequestDetailsSearch(Request $request)
    {
        $search = $request->search;

        $pageTitle = 'Trade Request Search - '. $search;
        $emptyMessage = 'No data found';

        $tradeRequests = TradeRequest::where('uid', $search)->orWhereHas('buyer', function($buyer) use($search){
            $buyer->where('status', 1)->where('username', 'like',"%$search%");
        })->orWhereHas('seller', function($seller) use($search){
            $seller->where('status', 1)->where('username', 'like',"%$search%");
        })->with(['buyer','seller','fiatGateway','crypto','fiat'])->latest()->paginate(getPaginate());

        return view('admin.trade.index',compact('pageTitle', 'tradeRequests', 'emptyMessage'));
    }

    public function tradeRequestChatStore(Request $request, $id)
    {
        $tradeRequest = TradeRequest::findOrFail($id);

        $request->validate([
            'message' => 'required',
            'file' => ['nullable',new FileTypeValidate(['jpg', 'pdf']),'max:2000'],
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
        $chat->user_id = null;
        $chat->admin = 1;
        $chat->message = $request->message;
        $chat->file = $file;
        $chat->save();

        $notify[] = ['success', 'Your response has been taken'];
        return back()->withNotify($notify);
    }

    public function release($id)
    {
        $tradeRequest = TradeRequest::findOrFail($id);

        $tradeRequest->status = 1;
        $tradeRequest->details = 'Crypto Amount Released By System';
        $tradeRequest->save();

        $wallet = Wallet::where('user_id', $tradeRequest->buyer->id)->where('crypto_id',$tradeRequest->crypto_id)->first();

        if (!$wallet) {
            $notify[] = ['error', 'You can not proceed this action'];
            return back()->withNotify($notify);
        }

        $wallet->balance += $tradeRequest->crypto_amount;
        $wallet->save();

        $transaction = new Transaction();
        $transaction->user_id = $tradeRequest->buyer->id;
        $transaction->crypto_id = $tradeRequest->crypto_id;
        $transaction->amount = $tradeRequest->crypto_amount;
        $transaction->post_balance = $wallet->balance;
        $transaction->charge = 0;
        $transaction->trx_type = '+';
        $transaction->details = 'Added With Your ' . $wallet->crypto->code .' Wallet For Buying '.$wallet->crypto->name.' By System';
        $transaction->trx = getTrx();
        $transaction->save();

        notify($tradeRequest->buyer, 'TRADE_SETTLED', [
            'name' => $tradeRequest->buyer->fullname,
            'crypto_amount' => showAmount($tradeRequest->crypto_amount,8),
            'crypto_currency' => ($tradeRequest->crypto->code)
        ]);

        notify($tradeRequest->seller, 'TRADE_SETTLED', [
            'name' => $tradeRequest->buyer->fullname,
            'crypto_amount' => showAmount($tradeRequest->crypto_amount,8),
            'crypto_currency' => ($tradeRequest->crypto->code)
        ]);

        $notify[] = ['success', 'Released Successfully'];
        return back()->withNotify($notify);
    }

    public function return($id)
    {
        $tradeRequest = TradeRequest::findOrFail($id);

        $tradeRequest->status = 9;
        $tradeRequest->details = 'Crypto Amount Returned By System';
        $tradeRequest->save();

        $wallet = Wallet::where('user_id', $tradeRequest->seller->id)->where('crypto_id',$tradeRequest->crypto_id)->first();

        if (!$wallet) {
            $notify[] = ['error', 'You can not proceed this action'];
            return back()->withNotify($notify);
        }

        $wallet->balance += ($tradeRequest->crypto_amount + $tradeRequest->trade_charge);
        $wallet->save();

        $transaction = new Transaction();
        $transaction->user_id = $tradeRequest->seller->id;
        $transaction->crypto_id = $tradeRequest->crypto_id;
        $transaction->amount = $tradeRequest->crypto_amount;
        $transaction->post_balance = $wallet->balance;
        $transaction->charge = 0;
        $transaction->trx_type = '+';
        $transaction->details = 'Added With Your ' . $wallet->crypto->code .' Wallet as Refund Form System';
        $transaction->trx = getTrx();
        $transaction->save();

        notify($tradeRequest->buyer, 'TRADE_SETTLED', [
            'name' => $tradeRequest->seller->fullname,
            'crypto_amount' => showAmount($tradeRequest->crypto_amount,8),
            'crypto_currency' => ($tradeRequest->crypto->code)
        ]);

        notify($tradeRequest->seller, 'TRADE_SETTLED', [
            'name' => $tradeRequest->seller->fullname,
            'crypto_amount' => showAmount($tradeRequest->crypto_amount,8),
            'crypto_currency' => ($tradeRequest->crypto->code)
        ]);

        $notify[] = ['success', 'Returned Successfully'];
        return back()->withNotify($notify);
    }

    public function download($id)
    {
        $chat = Chat::findOrFail($id);

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
