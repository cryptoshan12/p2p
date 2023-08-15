<?php

namespace App\Http\Controllers;
use App\Models\AdminNotification;
use App\Models\Advertisement;
use App\Models\Crypto;
use App\Models\Fiat;
use App\Models\FiatGateway;
use App\Models\Frontend;
use App\Models\Language;
use App\Models\Page;
use App\Models\SupportAttachment;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class SiteController extends Controller
{
    public function __construct(){
        $this->activeTemplate = activeTemplate();
    }

    public function index(){
        $count = Page::where('tempname',$this->activeTemplate)->where('slug','home')->count();
        if($count == 0){
            $page = new Page();
            $page->tempname = $this->activeTemplate;
            $page->name = 'HOME';
            $page->slug = 'home';
            $page->save();
        }

        $reference = @$_GET['reference'];
        if ($reference) {
            session()->put('reference', $reference);
        }

        $pageTitle = 'Home';
        $sections = Page::where('tempname',$this->activeTemplate)->where('slug','home')->first();

        $cryptos = Crypto::where('status', 1)->get();
        $fiatGateways = FiatGateway::where('status', 1)->get();

        return view($this->activeTemplate . 'home', compact('pageTitle','sections', 'cryptos' , 'fiatGateways'));
    }

    public function pages($slug)
    {
        $page = Page::where('tempname',$this->activeTemplate)->where('slug',$slug)->firstOrFail();
        $pageTitle = $page->name;
        $sections = $page->secs;
        return view($this->activeTemplate . 'pages', compact('pageTitle','sections'));
    }

    public function contact()
    {
        $pageTitle = "Contact Us";
        $page = Page::where('tempname',$this->activeTemplate)->where('slug','contact')->firstOrFail();
        $sections = $page->secs;
        return view($this->activeTemplate . 'contact',compact('pageTitle', 'sections'));
    }


    public function contactSubmit(Request $request)
    {
if(!auth()->id()){
     $notify[] = ['error', 'Please Login First'];
            return redirect()->route('home')->withNotify($notify);
}else{
        $this->validate($request, [
            'name' => 'required|max:191',
            'email' => 'required|max:191',
            'subject' => 'required|max:100',
            'message' => 'required',
        ]);


        $random = getNumber();

        $ticket = new SupportTicket();
        $ticket->user_id = auth()->id() ?? 0;
        $ticket->name = $request->name;
        $ticket->email = $request->email;
        $ticket->priority = 2;


        $ticket->ticket = $random;
        $ticket->subject = $request->subject;
        $ticket->last_reply = Carbon::now();
        $ticket->status = 0;
        $ticket->save();

        $adminNotification = new AdminNotification();
        $adminNotification->user_id = auth()->user() ? auth()->user()->id : 0;
        $adminNotification->title = 'A new support ticket has opened ';
        $adminNotification->click_url = urlPath('admin.ticket.view',$ticket->id);
        $adminNotification->save();

        $message = new SupportMessage();
        $message->supportticket_id = $ticket->id;
        $message->message = $request->message;
        $message->save();

        $notify[] = ['success', 'ticket created successfully!'];

        return redirect()->route('ticket.view', [$ticket->ticket])->withNotify($notify);
    }
}
    public function changeLanguage($lang = null)
    {
        $language = Language::where('code', $lang)->first();
        if (!$language) $lang = 'en';
        session()->put('lang', $lang);
        return redirect()->back();
    }

    public function blogDetails($id,$slug){
        $blog = Frontend::where('id',$id)->where('data_keys','blog.element')->firstOrFail();
        $pageTitle = $blog->data_values->title;
        return view($this->activeTemplate.'blog_details',compact('blog','pageTitle'));
    }


    public function cookieAccept(){
        session()->put('cookie_accepted',true);
        return response()->json(['success' => 'Cookie accepted successfully']);
    }

    public function placeholderImage($size = null){
        $imgWidth = explode('x',$size)[0];
        $imgHeight = explode('x',$size)[1];
        $text = $imgWidth . '×' . $imgHeight;
        $fontFile = realpath('assets/font') . DIRECTORY_SEPARATOR . 'RobotoMono-Regular.ttf';
        $fontSize = round(($imgWidth - 50) / 8);
        if ($fontSize <= 9) {
            $fontSize = 9;
        }
        if($imgHeight < 100 && $fontSize > 30){
            $fontSize = 30;
        }

        $image     = imagecreatetruecolor($imgWidth, $imgHeight);
        $colorFill = imagecolorallocate($image, 100, 100, 100);
        $bgFill    = imagecolorallocate($image, 175, 175, 175);
        imagefill($image, 0, 0, $bgFill);
        $textBox = imagettfbbox($fontSize, 0, $fontFile, $text);
        $textWidth  = abs($textBox[4] - $textBox[0]);
        $textHeight = abs($textBox[5] - $textBox[1]);
        $textX      = ($imgWidth - $textWidth) / 2;
        $textY      = ($imgHeight + $textHeight) / 2;
        header('Content-Type: image/jpeg');
        imagettftext($image, $fontSize, 0, $textX, $textY, $colorFill, $fontFile, $text);
        imagejpeg($image);
        imagedestroy($image);
    }

    public function searchAdvertisements(Request $request)
    {
        $request->validate([
            'type' => 'required|in:buy,sell',
            'crypto_code' => 'required',
            'amount' => 'nullable|numeric|gt:0',
        ]);

        $crypto = $request->crypto_code;
        $fiatGateway = $request->fiat_gateway_slug;
        $fiat = $request->fiat_code;
        $amount = $request->amount;

        return redirect()->route('buy.sell',[$request->type, $crypto, $fiatGateway, $fiat, $amount]);
    }

    public function currencyWiseBuyAd (Request $request)
    {
        $validate = Validator::make($request->all(),[
            'id' => 'required|integer|gt:0'
        ]);

        if($validate->fails()){
            return response()->json(['error' => $validate->errors()]);
        }

        $buyAds = Advertisement::where('crypto_id', $request->id)
        ->whereHas('user', function($q) {
            $q->where('status', 1);
        })->where('type' ,2)->where('status' , 1)
        ->latest()->with(['crypto','user','fiatGateway','fiat'])
        ->limit(6)
        ->get();

        $view = view($this->activeTemplate.'buy_ad',compact('buyAds'))->render();

        return response()->json([
            'html' => $view
        ]);
    }

    public function currencyWiseSellAd (Request $request)
    {
        $validate = Validator::make($request->all(),[
            'id' => 'required|integer|gt:0'
        ]);

        if($validate->fails()){
            return response()->json(['error' => $validate->errors()]);
        }

        $sellAds = Advertisement::where('crypto_id', $request->id)->whereHas('user', function($q) {
            $q->where('status', 1);
        })->where('type' ,1)->where('status' , 1)->latest()->with(['crypto','user','fiatGateway','fiat'])->limit(6)->get();

        $view = view($this->activeTemplate.'sell_ad',compact('sellAds'))->render();

        return response()->json([
            'html' => $view
        ]);
    }

    public function policyDetails($slug,$id)
    {
        $policyDetails = Frontend::where('data_keys', 'policy_pages.element')->findOrFail($id);
        $pageTitle = $policyDetails->data_values->title;

        return view($this->activeTemplate.'policy',compact('pageTitle', 'policyDetails'));
    }

    public function buySell($type, $crypto, $fiatGateway = null, $fiat = null, $amount = null)
    {

        if (($type == 'buy') || ($type == 'sell')) {
            $cryptoCheck = Crypto::where('status', 1)->where('code', $crypto)->firstOrFail();

            $fiatGatewayCheck = null;
            $fiatCheck = null;

            if ($fiatGateway) {
                $fiatGatewayCheck = FiatGateway::where('status', 1)->where('slug', $fiatGateway)->first();
            }

            if ($fiat) {
                $fiatCheck = Fiat::where('status', 1)->where('code', $fiat)->first();
            }

            $advertisements = Advertisement::whereHas('user', function($q) {
                $q->where('status', 1);
            })->where('status', 1);

            if ($type == 'buy') {
                $advertisements = $advertisements->where('type', 2);
                $pageTitle = 'Buy '.$cryptoCheck->name;
            }

            if ($type == 'sell') {
                $advertisements = $advertisements->where('type', 1);
                $pageTitle = 'Sell '.$cryptoCheck->name;
            }

            if ($cryptoCheck) {
                $cryptoCheck = $advertisements->where('crypto_id', $cryptoCheck->id);
            }

            if ($fiatGatewayCheck) {
                $advertisements = $advertisements->where('fiat_gateway_id', $fiatGatewayCheck->id);
            }

            if ($fiatCheck) {
                $advertisements = $advertisements->where('fiat_id', $fiatCheck->id);
            }

            if ($amount) {
                $advertisements = $advertisements->where('min', '<=' ,$amount)->where('max', '>=', $amount);
            }

            $advertisements = $advertisements->with(['user','fiatGateway','crypto','fiat'])->paginate(getPaginate());

            $emptyMessage = 'No data found';

            $cryptos = Crypto::where('status', 1)->get();
            $fiatGateways = FiatGateway::where('status', 1)->get();

            return view($this->activeTemplate.'search',compact('pageTitle', 'advertisements', 'emptyMessage', 'type', 'crypto', 'cryptos', 'fiatGateways', 'fiat', 'amount', 'fiatGateway'));

        }else{
            $notify[] = ['error', 'You have to choose buy or sell'];
            return redirect()->route('home')->withNotify($notify);
        }

    }
}
