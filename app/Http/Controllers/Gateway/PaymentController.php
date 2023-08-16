<?php

namespace App\Http\Controllers\Gateway;

use App\Http\Controllers\Controller;
use App\Models\Crypto;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletCrypto;
use Illuminate\Http\Request;

use App\Http\Controllers\Gateway\Coinpayments\CoinPaymentHosted;
use App\Models\AdminNotification;
use App\Models\Deposit;
use App\Models\GeneralSetting;
use App\Models\Transaction;
use App\Models\MetaMaskConfiguration; //added new

class PaymentController extends Controller
{
    public function __construct()
    {
        return $this->activeTemplate = activeTemplate();
    }

    public function wallets()
    {
        $pageTitle = 'Your Receiving Wallets';
        $wallets = Wallet::where('user_id', auth()->user()->id)->whereHas('crypto', function($q){
            $q->where('status', 1);
        })->with('crypto')->latest()->get();

        $cryptoWallets = WalletCrypto::where('user_id', auth()->user()->id)->whereHas('crypto', function($q){
            $q->where('status', 1);
        })->latest()->with('crypto')->paginate(getPaginate());
        $emptyMessage = 'No wallet found';

        return view($this->activeTemplate . 'user.wallet', compact('pageTitle', 'wallets','cryptoWallets','emptyMessage'));
    }

    public function singleWallet($id, $code)
    {
        $pageTitle = $code.' Wallets';
        $crypto = Crypto::findOrFail($id);
        //added new
        if($crypto->ismetamask){
            
            //$metamaskConf = MetaMaskConfiguration::where('status', 1)->whereNotNull('chain_id')->whereNotNull('name')->whereNotNull('wallet_address')->latest()->get();
            $metamaskConf = MetaMaskConfiguration::where('status',1)->get()->first();
            if($metamaskConf){
            $wallets = Wallet::where('user_id', auth()->user()->id)->whereHas('crypto', function($q){
                $q->where('status', 1);
            })->with('crypto')->latest()->get();
            
            //$cryptoWallets = WalletCrypto::where('user_id', auth()->user()->id)->whereHas('crypto', function($q) use ($id){
             //   $q->where('id', $id)->where('status', 1);
            //})->latest()->with('crypto')->paginate(getPaginate());
            
            
            $cryptoWallets = null;
            $emptyMessage = 'No wallet found';
            
            return view($this->activeTemplate . 'user.wallet', compact('pageTitle', 'wallets','cryptoWallets','crypto','emptyMessage','metamaskConf'));
            }else{
                    $notify[] = ['error', 'MetaMask : Confguration Error'];
                 return back()->withNotify($notify);
            }

            
        }
        else{
            $wallets = Wallet::where('user_id', auth()->user()->id)->whereHas('crypto', function($q){
                $q->where('status', 1);
            })->with('crypto')->latest()->get();
    
            $cryptoWallets = WalletCrypto::where('user_id', auth()->user()->id)->whereHas('crypto', function($q) use ($id){
                $q->where('id', $id)->where('status', 1);
            })->latest()->with('crypto')->paginate(getPaginate());
    
            $emptyMessage = 'No wallet found';
    
            return view($this->activeTemplate . 'user.wallet', compact('pageTitle', 'wallets','cryptoWallets','crypto','emptyMessage'));
        }
    }

    public function walletGenerate($code)
    {
        $crypto = Crypto::where('status',1)->where('code',$code)->firstOrFail();


        $coinPayAcc = GeneralSetting::first(['public_key','private_key']);

            $cps = new CoinPaymentHosted();
            $cps->Setup($coinPayAcc->private_key, $coinPayAcc->public_key);
            $callbackUrl = route('ipn.crypto');
            $result = $cps->GetCallbackAddress($crypto->code,$callbackUrl);
            if ($result['error'] == 'ok') {
                $newCryptoWallet = new WalletCrypto();
                $newCryptoWallet->user_id = auth()->user()->id;
                $newCryptoWallet->crypto_id = $crypto->id;
                $newCryptoWallet->wallet_address = $result['result']['address'];
                $newCryptoWallet->save();
                $notify[] = ['success', 'New Wallet Address Generated Successfully.'];
            } else {
                $notify[] = ['error', 'API Error : '. $result['error']];
            }

            return back()->withNotify($notify);
    }



    public function cryptoipn(Request $request)
    {

        if ($request->status >= 100 || $request->status == 2) {


            $userCryptoWallet = WalletCrypto::where('wallet_address', $request->address)->first();
            $user       = $userCryptoWallet->user;
            $general = GeneralSetting::first();

            if($general->merchant_id == $request->merchant){

                $exist =  Deposit::where('cp_trx',$request->txn_id)->count();
                if($exist == 0){

                    $crypto = Crypto::find($userCryptoWallet->crypto_id);
                    $sentAmount = $request->amount;

                    $charge                 = $crypto->dc_fixed + ($sentAmount * $crypto->dc_percent / 100);
                    $finalAmount            = $sentAmount - $charge;

                    if($finalAmount > 0){
                        $data                   = new Deposit();
                        $data->user_id          = $user->id;
                        $data->crypto_id        = $crypto->id;
                        $data->wallet_address   = $request->address;
                        $data->amount           = $sentAmount;
                        $data->charge           = $charge;
                        $data->final_amo        = $finalAmount;
                        $data->trx              = getTrx();
                        $data->status           = 1;
                        $data->cp_trx           = $request->txn_id;
                        $data->save();


                        $userWallet = Wallet::where('user_id',$userCryptoWallet->user_id)->where('crypto_id',$userCryptoWallet->crypto_id)->first();
                        $userWallet->balance +=  $finalAmount;
                        $userWallet->save();


                        $transaction = new Transaction();
                        $transaction->user_id = $data->user_id;
                        $transaction->crypto_id = $crypto->id;
                        $transaction->amount = $data->amount;
                        $transaction->post_balance = getAmount($userWallet->balance,8);
                        $transaction->charge = getAmount($data->charge,8);
                        $transaction->trx_type = '+';
                        $transaction->details = 'Deposit Via ' . $data->crypto->code;
                        $transaction->trx = $data->trx;
                        $transaction->save();


                        $adminNotification = new AdminNotification();
                        $adminNotification->user_id = $user->id;
                        $adminNotification->title = 'Deposit successful for '.$data->crypto->code;
                        $adminNotification->click_url = urlPath('admin.deposit.successful');
                        $adminNotification->save();

                        notify($user, 'DEPOSIT_COMPLETE', [
                            'amount' => showAmount($data->amount,8),
                            'charge' => showAmount($data->charge,8),
                            'currency' => $data->crypto->code,
                            'payable' => showAmount($data->final_amo,8),
                            'trx' => $data->trx,
                            'post_balance' => showAmount($userWallet->balance,8)
                        ]);
                    }
                }
            }
        }
    }

}
