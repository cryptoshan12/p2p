<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use App\Models\Limit;
use Illuminate\Http\Request;

class ManageAdvertisementController extends Controller
{
    public function advertisements()
    {
        $pageTitle = 'Advertisements';

        $advertisements = Advertisement::with(['fiat','fiatGateway','crypto','user'])->latest()->paginate(getPaginate());

        $emptyMessage = 'No advertisement found';

        return view('admin.advertisement.index',compact('pageTitle', 'advertisements', 'emptyMessage'));
    }

    public function advertisementVew($id)
    {
        $advertisement = Advertisement::findOrFail($id);
        $pageTitle = 'Advertisement Details';

        return view('admin.advertisement.view',compact('pageTitle', 'advertisement'));
    }

    public function advertisementUpdate(Request $request, $id)
    {
        $advertisement = Advertisement::findOrFail($id);

        $advertisement->status = $request->status ? 1:0;
        $advertisement->save();

        $notify[] = ['success', 'Advertisement status has been updated'];
        return back()->withNotify($notify);
    }

    public function advertisementSearch(Request $request)
    {
        $search = $request->search;

        $pageTitle = 'Advertisement Search - '. $search;
        $emptyMessage = 'No data found';

        $advertisements = Advertisement::whereHas('user', function($q) use($search){
            $q->where('status', 1)->where('username', 'like',"%$search%");
        })->with(['fiat','fiatGateway','crypto','user'])->latest()->paginate(getPaginate());

        return view('admin.advertisement.index',compact('pageTitle', 'advertisements', 'search', 'emptyMessage'));
    }

    /*
        Advertisement Limit
    */

    public function limitIndex()
    {
        $pageTitle      = 'Advertisement Limit';
        $limits         = Limit::latest()->paginate(getPaginate());
        $emptyMessage   = 'No data found';

        return view('admin.advertisement.limit',compact('pageTitle', 'limits', 'emptyMessage'));
    }

    public function limitStore(Request $request)
    {
        $request->validate([
            'completed_trade'   => 'required|integer',
            'ad_limit'          => 'required|integer'
        ]);

        $limit = new Limit();
        $limit->completed_trade = $request->completed_trade;
        $limit->ad_limit = $request->ad_limit;
        $limit->save();

        $notify[] = ['success', 'Advertisement limit has been added'];
        return back()->withNotify($notify);
    }

    public function limitUpdate(Request $request, $id)
    {
        $request->validate([
            'completed_trade' => 'required|integer',
            'ad_limit' => 'required|integer'
        ]);

        $limit = Limit::findOrFail($id);

        $limit->completed_trade = $request->completed_trade;
        $limit->ad_limit = $request->ad_limit;
        $limit->save();

        $notify[] = ['success', 'Advertisement limit has been updated'];
        return back()->withNotify($notify);
    }

}
