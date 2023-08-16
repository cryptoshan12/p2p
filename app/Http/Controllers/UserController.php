<?php

namespace App\Http\Controllers;

use App\Lib\GoogleAuthenticator;
use App\Models\AdminNotification;
use App\Models\Advertisement;
use App\Models\Crypto;
use App\Models\GeneralSetting;
use App\Models\TradeRequest;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Models\Withdrawal;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
//added
use App\Models\Deposit;
use App\Models\GatewayCurrency;

class UserController extends Controller
{
    public function __construct()
    {
        $this->activeTemplate = activeTemplate();
    }

    public function home()
    {
        $pageTitle = 'Dashboard';
        $user = auth()->user();
        $walletId = Wallet::where('user_id', $user->id)->pluck('crypto_id');
        $cryptos = Crypto::latest()->whereNotIn('id', $walletId)->pluck('id');
        $data = [];

        foreach ($cryptos as $id) {
            $wallet['crypto_id']    = $id;
            $wallet['user_id']      = $user->id;
            $wallet['balance']      = 0;
            $data[] = $wallet;
        }

        if(!empty($data)){
            Wallet::insert($data);
        }

        $wallets = Wallet::where('user_id', $user->id)->with('crypto')->latest()->get();
        $totalAdd = Advertisement::where('user_id', $user->id)->count();

        $totalTradeReq = TradeRequest::where(function($q) use($user){
            $q->orWhere('buyer_id', $user->id)->orWhere('seller_id', $user->id);
        })->count();

        $completedTrade = TradeRequest::where('status' ,1)->where(function($q) use($user){
            $q->orWhere('buyer_id', $user->id)->orWhere('seller_id', $user->id);
        })->count();

        $latestAdds =  Advertisement::where('user_id', auth()->user()->id)->latest()->with(['crypto','fiatGateway','fiat'])->latest()->limit(10)->get();
        $emptyMessage = 'No data found';

        return view($this->activeTemplate . 'user.dashboard', compact('pageTitle', 'user', 'wallets', 'totalAdd', 'totalTradeReq', 'completedTrade', 'latestAdds','emptyMessage'));
    }

    public function transactionIndex()
    {
        $pageTitle = 'Transactions';
        $cryptos = Crypto::latest()->get();
        $transactions = Transaction::where('user_id',auth()->user()->id)->with('crypto')->latest()->paginate(getPaginate());
        $emptyMessage = 'No transaction found';

        return view($this->activeTemplate . 'user.transaction.index', compact('pageTitle', 'transactions', 'cryptos', 'emptyMessage'));
    }

    public function singleTransaction($id, $code)
    {
        $pageTitle = $code.' Transactions';
        $crypto = Crypto::findOrFail($id);
        $cryptos = Crypto::latest()->get();
        $transactions = Transaction::where('user_id',auth()->user()->id)->where('crypto_id', $crypto->id)->with('crypto')->latest()->paginate(getPaginate());
        $emptyMessage = 'No transaction found';

        return view($this->activeTemplate . 'user.transaction.index', compact('pageTitle', 'transactions', 'cryptos', 'emptyMessage'));
    }

    public function profile()
    {
        $pageTitle = "Profile Setting";
        $user = Auth::user();
        return view($this->activeTemplate. 'user.profile_setting', compact('pageTitle','user'));
    }

    public function submitProfile(Request $request)
    {
        $request->validate([
            'firstname' => 'required|string|max:50',
            'lastname' => 'required|string|max:50',
            'address' => 'sometimes|required|max:80',
            'state' => 'sometimes|required|max:80',
            'zip' => 'sometimes|required|max:40',
            'city' => 'sometimes|required|max:50',
            'image' => ['image',new FileTypeValidate(['jpg','jpeg','png'])]
        ],[
            'firstname.required'=>'First name field is required',
            'lastname.required'=>'Last name field is required'
        ]);

        $user = Auth::user();

        $in['firstname'] = $request->firstname;
        $in['lastname'] = $request->lastname;

        $in['address'] = [
            'address' => $request->address,
            'state' => $request->state,
            'zip' => $request->zip,
            'country' => @$user->address->country,
            'city' => $request->city,
        ];


        if ($request->hasFile('image')) {
            $location = imagePath()['profile']['user']['path'];
            $size = imagePath()['profile']['user']['size'];
            $filename = uploadImage($request->image, $location, $size, $user->image);
            $in['image'] = $filename;
        }
        $user->fill($in)->save();
        $notify[] = ['success', 'Profile updated successfully.'];
        return back()->withNotify($notify);
    }

    public function changePassword()
    {
        $pageTitle = 'Change password';
        return view($this->activeTemplate . 'user.password', compact('pageTitle'));
    }

    public function submitPassword(Request $request)
    {

        $password_validation = Password::min(6);
        $general = GeneralSetting::first();
        if ($general->secure_password) {
            $password_validation = $password_validation->mixedCase()->numbers()->symbols()->uncompromised();
        }

        $this->validate($request, [
            'current_password' => 'required',
            'password' => ['required','confirmed',$password_validation]
        ]);


        try {
            $user = auth()->user();
            if (Hash::check($request->current_password, $user->password)) {
                $password = Hash::make($request->password);
                $user->password = $password;
                $user->save();
                $notify[] = ['success', 'Password changes successfully.'];
                return back()->withNotify($notify);
            } else {
                $notify[] = ['error', 'The password doesn\'t match!'];
                return back()->withNotify($notify);
            }
        } catch (\PDOException $e) {
            $notify[] = ['error', $e->getMessage()];
            return back()->withNotify($notify);
        }
    }

    /*
     * Deposit History
     */
    public function depositHistory()
    {
        $pageTitle = 'Deposit History';
        $emptyMessage = 'No history found.';
        $logs = auth()->user()->deposits()->with('crypto')->orderBy('id','desc')->paginate(getPaginate());
        return view($this->activeTemplate.'user.deposit_history', compact('pageTitle', 'emptyMessage', 'logs'));
    }

    /*
     * Withdraw Operation
     */

    public function withdraw($code)
    {
        $crypto = Crypto::where('status',1)->where('code',$code)->firstOrFail();
        $userBalance = Wallet::where('user_id',auth()->user()->id)->where('crypto_id',$crypto->id)->firstOrFail();
        $pageTitle = 'Withdraw '.$crypto->code;
        $pastWithdrawals = Withdrawal::where('user_id', auth()->user()->id)->where('crypto_id', $crypto->id)->with('crypto')->paginate(getPaginate());
        $emptyMessage = 'No previous '.$crypto->code.' withdrawals found';

        return view($this->activeTemplate.'user.withdraw.index', compact('pageTitle','userBalance','crypto','pastWithdrawals','emptyMessage'));
    }

    public function withdrawStore(Request $request)
    {
        $this->validate($request, [
            'crypto' => 'required',
            'wallet' => 'required',
            'amount' => 'required|numeric'
        ]);

        $user = auth()->user();
        $crypto = Crypto::where('status',1)->where('code',$request->crypto)->firstOrFail();
        $userBalance = Wallet::where('user_id',$user->id)->where('crypto_id',$crypto->id)->firstOrFail();


        $charge = $crypto->wc_fixed + ($request->amount * $crypto->wc_percent / 100);
        $finalWithdrawAmount = $request->amount + $charge;

        if ($finalWithdrawAmount > $userBalance->balance) {
            $notify[] = ['error', 'You do not have sufficient balance for withdraw.'];
            return back()->withNotify($notify);
        }

        $userBalance->balance -= $finalWithdrawAmount;
        $userBalance->save();

        $withdraw = new Withdrawal();
        $withdraw->crypto_id = $crypto->id;
        $withdraw->wallet_address = $request->wallet;
        $withdraw->user_id = $user->id;
        $withdraw->amount = $finalWithdrawAmount;
        $withdraw->charge = $charge;
        $withdraw->payable = $request->amount;
        $withdraw->trx = getTrx();
        $withdraw->status = 2;
        $withdraw->save();

        $transaction = new Transaction();
        $transaction->user_id = $withdraw->user_id;
        $transaction->crypto_id = $withdraw->crypto_id;
        $transaction->amount = $withdraw->amount;
        $transaction->post_balance = $userBalance->balance;
        $transaction->charge = $withdraw->charge;
        $transaction->trx_type = '-';
        $transaction->details = showAmount($withdraw->payable,8) . ' ' . $withdraw->crypto->code . ' Withdraw Successful';
        $transaction->trx =  $withdraw->trx;
        $transaction->save();

        $adminNotification = new AdminNotification();
        $adminNotification->user_id = $user->id;
        $adminNotification->title = 'New withdraw request from '.$user->username;
        $adminNotification->click_url = urlPath('admin.withdraw.details',$withdraw->id);
        $adminNotification->save();

        notify($user, 'WITHDRAW_REQUEST', [
            'amount' => showAmount($withdraw->amount,8),
            'payable' => showAmount($withdraw->payable,8) ,
            'charge' => showAmount($withdraw->charge,8),
            'currency' => $withdraw->crypto->code,
            'trx' => $withdraw->trx,
            'post_balance' => showAmount($userBalance->balance,8)
        ]);

        $notify[] = ['success', 'Withdraw request sent successfully'];
        return redirect()->route('user.withdraw.history')->withNotify($notify);

    }


    public function withdrawLog()
    {
        $pageTitle = "Withdraw Log";
        $withdraws = Withdrawal::where('user_id', Auth::id())->where('status', '!=', 0)->with('crypto')->orderBy('id','desc')->paginate(getPaginate());
        $emptyMessage = "No Data Found!";
        return view($this->activeTemplate.'user.withdraw.log', compact('pageTitle','withdraws','emptyMessage'));
    }

    public function show2faForm()
    {
        $general = GeneralSetting::first();
        $ga = new GoogleAuthenticator();
        $user = auth()->user();
        $secret = $ga->createSecret();
        $qrCodeUrl = $ga->getQRCodeGoogleUrl($user->username . '@' . $general->sitename, $secret);
        $pageTitle = 'Two Factor';
        return view($this->activeTemplate.'user.twofactor', compact('pageTitle', 'secret', 'qrCodeUrl'));
    }

    public function create2fa(Request $request)
    {
        $user = auth()->user();
        $this->validate($request, [
            'key' => 'required',
            'code' => 'required',
        ]);
        $response = verifyG2fa($user,$request->code,$request->key);
        if ($response) {
            $user->tsc = $request->key;
            $user->ts = 1;
            $user->save();
            $userAgent = getIpInfo();
            $osBrowser = osBrowser();
            notify($user, '2FA_ENABLE', [
                'operating_system' => @$osBrowser['os_platform'],
                'browser' => @$osBrowser['browser'],
                'ip' => @$userAgent['ip'],
                'time' => @$userAgent['time']
            ]);
            $notify[] = ['success', 'Google authenticator enabled successfully'];
            return back()->withNotify($notify);
        } else {
            $notify[] = ['error', 'Wrong verification code'];
            return back()->withNotify($notify);
        }
    }


    public function disable2fa(Request $request)
    {
        $this->validate($request, [
            'code' => 'required',
        ]);

        $user = auth()->user();
        $response = verifyG2fa($user,$request->code);
        if ($response) {
            $user->tsc = null;
            $user->ts = 0;
            $user->save();
            $userAgent = getIpInfo();
            $osBrowser = osBrowser();
            notify($user, '2FA_DISABLE', [
                'operating_system' => @$osBrowser['os_platform'],
                'browser' => @$osBrowser['browser'],
                'ip' => @$userAgent['ip'],
                'time' => @$userAgent['time']
            ]);
            $notify[] = ['success', 'Two factor authenticator disable successfully'];
        } else {
            $notify[] = ['error', 'Wrong verification code'];
        }
        return back()->withNotify($notify);
    }
    
    public function depositMetamask(Request $request){
        $request->validate([
            'wallet_id' => 'required|integer|gt:0',
            'amount' => 'required|numeric|gt:0',
            'details' => 'required|max:255'
        ]);

        $user = auth()->user();
        $wallet = Wallet::where('user_id', $user->id)->findOrFail($request->wallet_id);

        $amount = $request->amount;
        $trx = getTrx();

        if ($request->amount) {
            $wallet->balance += $amount;
            $wallet->save();
            $notify[] = ['success', $wallet->crypto->symbol . $amount . ' has been added to ' . $user->username . '\'s balance'];

            $transaction = new Transaction();
            $transaction->user_id = $user->id;
            $transaction->crypto_id = $wallet->crypto->id;
            $transaction->amount = $amount;
            $transaction->post_balance = $wallet->balance;
            $transaction->charge = 0;
            $transaction->trx_type = '+';
            $transaction->details = $request->details;
            $transaction->trx =  $trx;
            $transaction->save();

            notify($user, 'BAL_ADD', [
                'trx' => $trx,
                'amount' => showAmount($amount),
                'currency' => $wallet->crypto->code,
                'post_balance' => showAmount($wallet->balance,8)
            ]);

        }
        return back()->withNotify($notify);
    }
}
