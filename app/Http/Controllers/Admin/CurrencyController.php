<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Crypto;
use App\Models\Fiat;
use App\Models\FiatGateway;
use App\Models\Window;
use App\Models\MetaMaskConfiguration; //added new
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CurrencyController extends Controller
{
    public function cryptoIndex()
    {
        $pageTitle = 'Crypto Currencies';
        $emptyMessage = 'No data found';

        $cryptos = Crypto::latest()->paginate(getPaginate());

        return view('admin.crypto.index',compact('pageTitle', 'emptyMessage', 'cryptos'));
    }

    public function cryptoNew()
    {
        $pageTitle = 'Add New Crypto Currency';

        return view('admin.crypto.new',compact('pageTitle'));
    }

    public function cryptoStore(Request $request)
    {
        $request->validate([
            'name'          => 'required|max:40',
            'code'          => 'required|max:10',
            'symbol'        => 'required|max:5',
            'rate'          => 'required|numeric|gt:0',
            'dc_fixed'      => 'required|numeric',
            'dc_percent'    => 'required|numeric|max:100',
            'wc_fixed'      => 'required|numeric',
            'wc_percent'    => 'required|numeric|max:100',
            'image'         => ['required','image',new FileTypeValidate(['jpeg', 'jpg', 'png'])]
        ]);

        $cryptoImage = null;

        if($request->hasFile('image')) {
            try{
                $location   = imagePath()['crypto']['path'];
                $size       = imagePath()['crypto']['size'];

                $cryptoImage = uploadImage($request->image, $location , $size);

            }catch(\Exception $exp) {
                return back()->withNotify(['error', 'Could not upload the image.']);
            }
        }

        $crypto             = new Crypto();
        $crypto->name       = $request->name;
        $crypto->code       = $request->code;
        $crypto->symbol     = $request->symbol;
        $crypto->rate       = $request->rate;
        $crypto->dc_fixed   = $request->dc_fixed;
        $crypto->dc_percent = $request->dc_percent;
        $crypto->wc_fixed   = $request->wc_fixed;
        $crypto->wc_percent = $request->wc_percent;
        $crypto->ismetamask = $request->isMetaMask ? 1 : 0; //added new
        $crypto->bsc_address= $request->bsc_address; //added new
        $crypto->image      = $cryptoImage;
        $crypto->save();

        $notify[] = ['success', 'Crypto currency has been added'];
        return back()->withNotify($notify);
    }

    public function cryptoEdit($id)
    {
        $crypto = Crypto::findOrFail($id);
        $pageTitle = 'Update '.$crypto->name;

        return view('admin.crypto.edit',compact('pageTitle' ,'crypto'));
    }

    public function cryptoUpdate(Request $request, $id)
    {
        $crypto = Crypto::findOrFail($id);

        $request->validate([
            'name' => 'required|max:40',
            'code' => 'required|max:10',
            'symbol' => 'required|max:5',
            'rate' => 'required|numeric|gt:0',
            'dc_fixed' => 'required|numeric',
            'dc_percent' => 'required|numeric|max:100',
            'wc_fixed' => 'required|numeric',
            'wc_percent' => 'required|numeric|max:100',
            'image' => ['nullable','image',new FileTypeValidate(['jpeg', 'jpg', 'png'])]
        ]);

        $cryptoImage = $crypto->image;

        if($request->hasFile('image')) {
            try{

                $location = imagePath()['crypto']['path'];
                $size = imagePath()['crypto']['size'];
                $old = $crypto->image;
                $cryptoImage = uploadImage($request->image, $location , $size, $old);

            }catch(\Exception $exp) {
                return back()->withNotify(['error', 'Could not upload the image.']);
            }
        }

        $crypto->name = $request->name;
        $crypto->code = $request->code;
        $crypto->symbol = $request->symbol;
        $crypto->rate = $request->rate;
        $crypto->dc_fixed = $request->dc_fixed;
        $crypto->dc_percent = $request->dc_percent;
        $crypto->wc_fixed = $request->wc_fixed;
        $crypto->wc_percent = $request->wc_percent;
        $crypto->status = $request->status ? 1 : 0;
        $crypto->ismetamask = $request->isMetaMask ? 1 : 0; //added new
        $crypto->bsc_address= $request->bsc_address; //added new
        $crypto->image = $cryptoImage;
        $crypto->save();

        $notify[] = ['success', 'Crypto currency has been updated'];
        return back()->withNotify($notify);
    }

    public function cryptoSearch(Request $request)
    {
        $search = $request->search;
        $pageTitle = 'Crypto Currency Search - '. $search;
        $emptyMessage = 'No data found';
        $cryptos = Crypto::where('name', 'like',"%$search%")->orWhere('code', 'like',"%$search%")->latest()->paginate(getPaginate());

        return view('admin.crypto.index',compact('search', 'pageTitle' , 'emptyMessage','cryptos'));
    }

    public function fiatIndex()
    {
        $pageTitle = 'Fiat Currencies';
        $fiats = Fiat::latest()->paginate(getPaginate());
        $emptyMessage = 'No data found';

        return view('admin.fiat.index',compact('pageTitle', 'fiats', 'emptyMessage'));
    }

    public function fiatStore(Request $request)
    {
        $request->validate([
            'name' => 'required|max:40',
            'code' => 'required|max:10',
            'symbol' => 'required|max:5',
            'rate' => 'required|numeric|gt:0',
        ]);


        $fiat = new Fiat();
        $fiat->name = $request->name;
        $fiat->code = $request->code;
        $fiat->symbol = $request->symbol;
        $fiat->rate = $request->rate;
        $fiat->save();

        $notify[] = ['success', 'Fiat currency has been added'];
        return back()->withNotify($notify);
    }

    public function fiatUpdate(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:40',
            'code' => 'required|max:10',
            'symbol' => 'required|max:5',
            'rate' => 'required|numeric|gt:0',
        ]);

        $fiat = Fiat::findOrFail($id);

        $fiat->name = $request->name;
        $fiat->code = $request->code;
        $fiat->symbol = $request->symbol;
        $fiat->rate = $request->rate;
        $fiat->status = $request->status ? 1 : 0;
        $fiat->save();

        $notify[] = ['success', 'Fiat currency has been updated'];
        return back()->withNotify($notify);
    }

    public function fiatSearch(Request $request)
    {
        $search = $request->search;
        $pageTitle = 'Fiat Search - ' . $search;
        $emptyMessage = 'No data found';

        $fiats = Fiat::where('name', 'like',"%$search%")->orWhere('code', 'like',"%$search%")->paginate(getPaginate());

        return view('admin.fiat.index', compact('pageTitle', 'fiats', 'emptyMessage', 'search'));
    }

    public function fiatGatewayIndex()
    {
        $pageTitle = 'Fiat Gateways';
        $fiatGateways = FiatGateway::latest()->paginate(getPaginate());
        $fiatCodes = Fiat::latest()->get();
        $emptyMessage = 'No data found';

        return view('admin.fiat.gateways',compact('pageTitle', 'fiatGateways', 'emptyMessage', 'fiatCodes'));
    }

    public function fiatGatewayStore(Request $request)
    {
        Validator::extend('alfanum', function($attr, $value){
            return preg_match('/^[\w.-]*$/', $value);
        });

        $request->validate([
            'name' => 'required|max:40',
            'slug' => 'required|alfanum|max:100|unique:fiat_gateways,slug',
            'code' => 'required|array|min:1',
            'code.*' => 'required|integer|gt:0'
        ],[
            'slug.alfanum' => 'Only alpha numeric value. No space or special char is allowed',
            'code.required' => 'Supported currencies must not be empty',
        ]);

        $fiat = Fiat::pluck('id')->toArray();

        foreach ($request->code as $item) {
            if (!in_array($item, $fiat)) {
                $notify[] = ['error', 'Fiat currency code not found'];
                return back()->withNotify($notify);
            }
        }

        $fiatGateway = new FiatGateway();
        $fiatGateway->name = $request->name;
        $fiatGateway->slug = $request->slug;
        $fiatGateway->code = $request->code;
        $fiatGateway->save();

        $notify[] = ['success', 'Fiat gateway has been added'];
        return back()->withNotify($notify);
    }

    public function fiatGatewayUpdate(Request $request, $id)
    {
        $fiatGateway = FiatGateway::findOrFail($id);

        Validator::extend('alfanum', function($attr, $value){
            return preg_match('/^[\w.-]*$/', $value);
        });

        $request->validate([
            'name' => 'required|max:40',
            'slug' => 'required|alfanum|max:100|unique:fiat_gateways,slug,'.$id,
            'code' => 'required|array|min:1',
            'code.*' => 'required|integer|gt:0'
        ],[
            'slug.alfanum' => 'Only alpha numeric value. No space or special char is allowed',
            'code.required' => 'Supported currencies must not be empty',

        ]);


        $fiat = Fiat::pluck('id')->toArray();

        foreach ($request->code as $item) {
            if (!in_array($item, $fiat)) {
                $notify[] = ['error', 'Fiat currency code not found'];
                return back()->withNotify($notify);
            }
        }

        $fiatGateway->name = $request->name;
        $fiatGateway->slug = $request->slug;
        $fiatGateway->code = $request->code;
        $fiatGateway->status = $request->status ? 1 : 0;
        $fiatGateway->save();

        $notify[] = ['success', 'Fiat gateway has been updated'];
        return back()->withNotify($notify);
    }

    public function fiatGatewaySearch(Request $request)
    {
        $search = $request->search;
        $pageTitle = 'Fiat Gateway Search - ' . $search;
        $emptyMessage = 'No data found';

        $fiatCodes = Fiat::latest()->get();
        $fiatGateways = FiatGateway::where('name', 'like',"%$search%")->paginate(getPaginate());

        return view('admin.fiat_gateway', compact('pageTitle', 'fiatGateways', 'emptyMessage', 'search', 'fiatCodes'));
    }

    public function paymentWindowIndex()
    {
        $pageTitle = 'Payment Windows';
        $paymentWindows = Window::latest()->paginate(getPaginate());
        $emptyMessage = 'No data found';

        return view('admin.payment_window',compact('pageTitle', 'paymentWindows', 'emptyMessage'));
    }

    public function paymentWindowStore(Request $request)
    {
        $request->validate([
            'minute' => 'required|integer|gt:0'
        ]);

        $paymentWindow = new Window();
        $paymentWindow->minute = $request->minute;
        $paymentWindow->save();

        $notify[] = ['success', 'Payment Window has been added'];
        return back()->withNotify($notify);
    }

    public function paymentWindowUpdate(Request $request, $id)
    {
        $paymentWindow = Window::findOrFail($id);

        $request->validate([
            'minute' => 'required|integer|gt:0'
        ]);

        $paymentWindow->minute = $request->minute;
        $paymentWindow->save();

        $notify[] = ['success', 'Payment Window has been updated'];
        return back()->withNotify($notify);
    }

    public function paymentWindowRemove(Request $request)
    {

        $request->validate([
            'id' => 'required|integer|gt:0'
        ]);

        $paymentWindow = Window::findOrFail($request->id);
        $paymentWindow->delete();

        $notify[] = ['success', 'Payment Window has been removed'];
        return back()->withNotify($notify);
    }
    
    public function metamask()
    {
        $pageTitle = 'Metmask Configuration';
        $emptyMessage = 'No data found';

        $metamaskconf = MetaMaskConfiguration::latest()->paginate(getPaginate());

        return view('admin.crypto.metamask',compact('pageTitle', 'emptyMessage', 'metamaskconf'));
    }

    public function metamaskEdit($id)
    {
        $metamaskConf = MetaMaskConfiguration::findOrFail($id);
        $pageTitle = 'Update '.$metamaskConf->name;

        return view('admin.crypto.metamask_edit',compact('pageTitle' ,'metamaskConf'));
    }
    public function metamaskUpdate(Request $request, $id) //added new
    {
        $metamaskConf = MetaMaskConfiguration::findOrFail($id);

        $request->validate([
            'chain_id' => 'required|max:40',
            'name' => 'required|max:40',
            'wallet_address' => 'required|max:200'
        ]);

        $metamaskConf->chain_id = $request->chain_id;
        $metamaskConf->name = $request->name;
        $metamaskConf->wallet_address = $request->wallet_address;
        $metamaskConf->gas_limit = $request->gas_limit;
        $metamaskConf->network_confirmations = $request->network_confirmations;
        $metamaskConf->status = $request->status ? 1 : 0;
        $metamaskConf->save();

        $notify[] = ['success', 'MetaMask Configuration has been updated'];
        return back()->withNotify($notify);
    }


}
