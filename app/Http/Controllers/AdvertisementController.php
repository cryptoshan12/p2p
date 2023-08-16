<?php

namespace App\Http\Controllers;

use App\Models\Advertisement;
use App\Models\Crypto;
use App\Models\Fiat;
use App\Models\FiatGateway;
use App\Models\Limit;
use App\Models\TradeRequest;
use App\Models\Window;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdvertisementController extends Controller
{
    public function __construct()
    {
        $this->activeTemplate = activeTemplate();
    }

    public function index()
    {
        $pageTitle = 'My Advertisements';
        $advertisements = Advertisement::where('user_id', auth()->user()->id)->latest()->with(['crypto','fiatGateway','fiat'])->paginate(getPaginate());
        $emptyMessage = 'No data found';

        return view($this->activeTemplate . 'user.advertisement.index', compact('pageTitle', 'advertisements', 'emptyMessage'));
    }

    public function new()
    {
        $pageTitle = 'New Advertisement';
        $user = auth()->user();

        $completedTrade = TradeRequest::where('status' ,1)->where(function($q) use($user){
            $q->orWhere('buyer_id', $user->id)->orWhere('seller_id', $user->id);
        })->count();

        $totalAdd = Advertisement::where('user_id', $user->id)->count();
        $limit = Limit::where('completed_trade','<=',$completedTrade)->orderBy('completed_trade','DESC')->first();

        $isPermitted = true;

        if($limit && $totalAdd >= $limit->ad_limit){
            $isPermitted = false;
        }


        $cryptos = Crypto::where('status', 1)->get();
        $fiatGateways = FiatGateway::where('status', 1)->get();
        $paymentWindows = Window::latest()->get();

        return view($this->activeTemplate . 'user.advertisement.new', compact('pageTitle', 'cryptos', 'fiatGateways', 'paymentWindows','isPermitted'));

    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:1,2',
            'crypto_id' => 'required|integer:gt:0',
            'fiat_gateway_id' => 'required|integer:gt:0',
            'fiat_id' => 'required|integer:gt:0',
            'margin' => 'required|numeric',
            'window' => 'required|integer|gt:0',
            'min' => 'required|numeric|gt:0',
            'max' => 'required|numeric|gt:min',
            'details' => 'required',
            'terms' => 'required'
        ]);


        $user = auth()->user();
        $completedTrade = TradeRequest::where('status' ,1)
                            ->where(function($q) use($user){
                                $q->orWhere('buyer_id', $user->id)->orWhere('seller_id', $user->id);
                            })->count();
        $totalAdd = Advertisement::where('user_id', $user->id)->count();

        $limit = Limit::where('completed_trade','<=',$completedTrade)->orderBy('completed_trade','DESC')->first();

        if($limit && $totalAdd >= $limit->ad_limit){
            $notify[] = ['error', 'You need to complete more trade to post more advertisement'];
            return back()->withNotify($notify);
        }

        $crypto = Crypto::where('status', 1)->findOrFail($request->crypto_id);
        $fiatGateway = FiatGateway::where('status', 1)->findOrFail($request->fiat_gateway_id);
        $fiat = Fiat::where('status', 1)->findOrFail($request->fiat_id);

        $advertisement = new Advertisement();
        $advertisement->user_id = $user->id;
        $advertisement->type = $request->type;
        $advertisement->crypto_id = $crypto->id;
        $advertisement->fiat_gateway_id = $fiatGateway->id;
        $advertisement->fiat_id = $fiat->id;
        $advertisement->margin = $request->margin;
        $advertisement->window = $request->window;
        $advertisement->min = $request->min;
        $advertisement->max = $request->max;
        $advertisement->details = $request->details;
        $advertisement->terms = $request->terms;
        $advertisement->status = 1;
        $advertisement->save();

        $notify[] = ['success', 'Your advertisement has been added'];
        return back()->withNotify($notify);
    }

    public function edit($id)
    {
        $advertisement = Advertisement::where('user_id', auth()->user()->id)->findOrFail($id);

        $pageTitle = 'Update Advertisement';
        $cryptos = Crypto::where('status', 1)->get();
        $fiatGateways = FiatGateway::where('status', 1)->get();
        $paymentWindows = Window::latest()->get();

        return view($this->activeTemplate . 'user.advertisement.edit', compact('pageTitle', 'advertisement', 'cryptos', 'fiatGateways', 'paymentWindows'));
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $advertisement = Advertisement::where('user_id', $user->id)->findOrFail($id);

        $request->validate([
            'type' => 'required|in:1,2',
            'crypto_id' => 'required|integer:gt:0',
            'fiat_gateway_id' => 'required|integer:gt:0',
            'fiat_id' => 'required|integer:gt:0',
            'margin' => 'required|numeric',
            'window' => 'required|integer|gt:0',
            'min' => 'required|numeric|gt:0',
            'max' => 'required|numeric|gt:min',
            'details' => 'required',
            'terms' => 'required'
        ]);

        $crypto = Crypto::where('status', 1)->findOrFail($request->crypto_id);
        $fiatGateway = FiatGateway::where('status', 1)->findOrFail($request->fiat_gateway_id);
        $fiat = Fiat::where('status', 1)->findOrFail($request->fiat_id);

        $advertisement->type = $request->type;
        $advertisement->crypto_id = $crypto->id;
        $advertisement->fiat_gateway_id = $fiatGateway->id;
        $advertisement->fiat_id = $fiat->id;
        $advertisement->margin = $request->margin;
        $advertisement->window = $request->window;
        $advertisement->min = $request->min;
        $advertisement->max = $request->max;
        $advertisement->details = $request->details;
        $advertisement->terms = $request->terms;
        $advertisement->status = 1;
        $advertisement->save();

        $notify[] = ['success', 'Your advertisement has been updated'];
        return back()->withNotify($notify);
    }

    function statusUpdate(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|gt:0'
        ]);

        $advertisement = Advertisement::where('user_id', auth()->user()->id)->findOrFail($request->id);

        if ($advertisement->status == 1) {
            $advertisement->status = 0;
            $notify[] = ['success', 'Advertisement deactivated successfully'];
        }else{
            $advertisement->status = 1;
            $notify[] = ['success', 'Advertisement activated successfully'];
        }

        $advertisement->save();

        return back()->withNotify($notify);
    }

    public function supportedCurrency(Request $request)
    {
        $validate = Validator::make($request->all(),[
            'id' => 'required|integer|gt:0'
        ]);

        if($validate->fails()){
            return response()->json(['error' => $validate->errors()]);
        }

        $fiatGateway = FiatGateway::where('status', 1)->find($request->id);

        if (!$fiatGateway) {
            return response()->json(['error' => 'Gateway not found or disabled']);
        }

         $supportedCurrencies = Fiat::where('status', 1)->find($fiatGateway->code);

         if ($supportedCurrencies->count() <= 0) {
            return response()->json(['error' => 'This gateway does not contain any currency']);
         }

         return response()->json([
            'success' => true,
            'supportedCurrencies' => $supportedCurrencies
        ]);
    }
    protected function sellChargeMessage()
    {
        $general = GeneralSetting::first();
        return $general->trade_charge > 0 ? 'For selling ' . getAmount($general->trade_charge) . '% will be charged for each completed trade' : '';
    }
}
