@extends($activeTemplate.'layouts.frontend')
@section('content')
@include($activeTemplate.'partials.breadcrumb')

<section class="pb-120">
    <div class="coin-search-area">
        <div class="container">
            <form class="coin-search-form" action="{{route('advertisement.search')}}" method="GET">
                <div class="row align-items-end">

                    <div class="col-xl-2 col-sm-6 form-group">
                        <label>@lang('Buy or Sell')</label>
                        <select class="select" name="type" required>
                            <option value="buy">@lang('Buy')</option>
                            <option value="sell">@lang('Sell')</option>
                        </select>
                    </div>
                    <div class="col-xl-2 col-sm-6 form-group">
                        <label>@lang('Crypto Currency')</label>
                        <select class="select" name="crypto_code" required>
                            @foreach ($cryptos as $item)
                                <option value="{{$item->code}}">{{__($item->name)}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-xl-2 col-sm-6 form-group">
                        <label>@lang('Payment Method')</label>
                        <select class="select" id="fiat-gateway" name="fiat_gateway_slug" required>
                            <option value="" selected disabled>@lang('Select One')</option>
                            @foreach ($fiatGateways as $item)
                                <option value="{{$item->slug}}" data-id="{{$item->id}}">{{__($item->name)}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-xl-2 col-sm-6 form-group">
                        <label>@lang('Currency')</label>
                        <select class="select" id="fiat-currency" name="fiat_code" required>

                        </select>
                    </div>
                    <div class="col-xl-2 col-sm-6 form-group">
                        <label>@lang('Amount')</label>
                        <input type="number" step="any" name="amount" value="{{@$amount}}" class="form-control" placeholder="@lang('Optional')">
                    </div>
                    <div class="col-xl-2 col-sm-6 form-group">
                        <button type="submit" class="cmn-btn w-100">@lang('Search')</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="container pt-120">
        <div class="row">
            <div class="col-lg-12">
            <div class="custom--card">
                <div class="card-body p-0">
                <div class="table-responsive--md">
                    <table class="table custom--table mb-0">
                        <thead>
                            <tr>
                                @if ($type == 'buy')
                                    <th>@lang('Seller')</th>
                                @else
                                    <th>@lang('Buyer')</th>
                                @endif
                            <th>@lang('Payment method')</th>
                            <th>@lang('Rate')</th>
                            <th>@lang('Limits')</th>
                            <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($advertisements as $item)
                                @if ($type == 'buy')
                                    @php
                                        $userWallet = App\Models\Wallet::where('user_id',$item->user_id)->where('crypto_id',$item->crypto_id)->first();
                                        $rate = rate($item->type,$item->crypto->rate,$item->fiat->rate,$item->margin);
                                        $userMax = $userWallet->balance * $rate;
                                        $maxLimit = $item->max < $userMax ? $item->max : $userMax;
                                    @endphp

                                    @if ($maxLimit >= $item->min)
                                        <tr>
                                            <td data-label="@lang('Seller')">{{__($item->user->fullname)}}</td>
                                            <td data-label="@lang('Payment method')">{{__($item->fiatGateway->name)}}</td>
                                            <td data-label="@lang('Rate')"><b>{{rate($item->type, $item->crypto->rate, $item->fiat->rate, $item->margin)}} {{__($item->fiat->code)}}/ {{__($item->crypto->code)}}</b></td>
                                            <td data-label="@lang('Limits')">{{showAmount($item->min)}} {{__($item->fiat->code)}} - {{showAmount($maxLimit)}} {{__($item->fiat->code)}}</td>
                                            <td data-label="@lang('Action')">
                                                <a href="{{route('user.trade.request.new',$item->id)}}" class="cmn-btn btn-sm">@lang('Buy')</a>
                                            </td>
                                        </tr>
                                    @endif
                                @else
                                    <tr>
                                        <td data-label="@lang('Buyer')">{{__($item->user->fullname)}}</td>
                                        <td data-label="@lang('Payment method')">{{__($item->fiatGateway->name)}}</td>
                                        <td data-label="@lang('Rate')"><b>{{rate($item->type, $item->crypto->rate, $item->fiat->rate, $item->margin)}} {{__($item->fiat->code)}}/ {{__($item->crypto->code)}}</b></td>
                                        <td data-label="@lang('Limits')">{{showAmount($item->min)}} {{__($item->fiat->code)}} - {{showAmount($item->max)}} {{__($item->fiat->code)}}</td>
                                        <td data-label="@lang('Action')"><a href="{{route('user.trade.request.new',$item->id)}}" class="cmn-btn btn-sm">@lang('Sell')</a></td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="100%" class="text-center">{{__($emptyMessage)}}</td>
                                </tr>
                            @endforelse

                        </tbody>
                        </table>
                    </div>
                </div>
            </div>
            {{$advertisements->links()}}
            </div>
        </div>
    </div>
</section>
@endsection

@push('script')
    <script>
        (function($){
            "use strict";

            $('select[name="type"]').val('{{@$type}}');
            $('select[name="crypto_code"]').val('{{@$crypto}}');
            $('select[name="fiat_gateway_slug"]').val('{{@$fiatGateway}}');

            $('#fiat-gateway').on('change',function(){
                var methodId = $(this).find('option:selected').data('id');
                var html = ``;

                if (methodId) {

                    $.ajax({
                        type: "get",
                        url: "{{route('advertisement.supported.currency')}}",
                        data: {id:methodId},
                        dataType: "json",

                        success: function (response) {
                            if(response.success){

                                if(response.supportedCurrencies.length > 0) {
                                    $.each(response.supportedCurrencies, function (index, value) {
                                        html += `<option value="${value.code}" ${value.code == `{{@$fiat}}` ? 'selected': '' }>${value.code}</option>`;
                                    });
                                }

                                $('#fiat-currency').html(html);

                            }else{
                                notify('error', response.error);
                            }
                        }
                    });
                }else{
                    html = `<option value="">@lang('Select One')</option>`;
                    $('#fiat-currency').html(html);
                }

            }).change();
        })(jQuery)
    </script>
@endpush
