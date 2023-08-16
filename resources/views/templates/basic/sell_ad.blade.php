<div class="custom--card">
  <div class="card-body p-0">
    <div class="table-responsive--md">
      <table class="table custom--table mb-0">
        <thead>
          <tr>
              <th>@lang('Seller')</th>
              <th>@lang('Payment method')</th>
              <th>@lang('Rate')</th>
              <th>@lang('Limits')</th>
              <th>@lang('Action')</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($sellAds as $item)
              <tr>
                  <td data-label="@lang('Seller')">{{__($item->user->fullname)}}</td>
                  <td data-label="@lang('Payment method')">{{__($item->fiatGateway->name)}}</td>
                  <td data-label="@lang('Rate')"><b>{{rate($item->type, $item->crypto->rate, $item->fiat->rate, $item->margin)}} {{__($item->fiat->code)}}/ {{__($item->crypto->code)}}</b></td>
                  <td data-label="@lang('Limits')">{{showAmount($item->min)}} - {{showAmount($item->max)}} {{__($item->fiat->code)}}</td>
                  <td data-label="@lang('Action')"><a href="{{route('user.trade.request.new',$item->id)}}" class="cmn-btn btn-sm">@lang('Sell')</a></td>
              </tr>
          @empty
              <tr>
                  <td colspan="100%" class="text-center">@lang('No data found')</td>
              </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
