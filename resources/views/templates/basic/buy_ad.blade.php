<div class="custom--card">
  <div class="card-body p-0">
    <div class="table-responsive--md">
      <table class="table custom--table mb-0">
        <thead>
            <tr>
                <th>@lang('Buyer')</th>
                <th>@lang('Payment method')</th>
                <th>@lang('Rate')</th>
                <th>@lang('Limits')</th>
                <th>@lang('Action')</th>
            </tr>
        </thead>

        <tbody>
            @php $i = 0;@endphp
            @foreach($buyAds as $item)
                @php
                    $userWallet = App\Models\Wallet::where('user_id',$item->user_id)->where('crypto_id',$item->crypto_id)->first();
                    $rate = rate($item->type,$item->crypto->rate,$item->fiat->rate,$item->margin);
                    $userMax = $userWallet->balance * $rate;
                    $maxLimit = $item->max < $userMax ? $item->max : $userMax;
                @endphp
                @if($maxLimit >= $item->min)
                    @php $i ++;@endphp
                    <tr>
                        <td data-label="@lang('Seller')">{{__($item->user->fullname)}}</td>
                        <td data-label="@lang('Payment method')">{{__($item->fiatGateway->name)}}</td>
                        <td data-label="@lang('Rate')"><b>{{rate($item->type, $item->crypto->rate, $item->fiat->rate, $item->margin)}} {{__($item->fiat->code)}}/ {{__($item->crypto->code)}}</b></td>
                        <td data-label="@lang('Limits')">{{showAmount($item->min)}} - {{showAmount($maxLimit)}} {{__($item->fiat->code)}}</td>
                        <td data-label="@lang('Action')">
                            <a href="{{route('user.trade.request.new',$item->id)}}" class="cmn-btn btn-sm">@lang('Buy')</a>
                        </td>
                    </tr>
                @endif
            @endforeach
            @if(!$i)
                <td colspan="100%" class="text-center">@lang('No data found')</td>
            @endif
        </tbody>
      </table>
    </div>
  </div>
</div>
