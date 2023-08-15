@extends($activeTemplate.'layouts.frontend')
@section('content')
    @include($activeTemplate.'partials.breadcrumb')

    <section class="pt-120 pb-120">
        <div class="container">
          <div class="row">
            <div class="col-xl-12">
                <h3 class="mb-3">@lang('Running Trades')</h3>
                <div class="custom--card">
                    <div class="card-body p-0">
                        <div class="table-responsive--md">
                            <table class="table custom--table">
                            <thead>
                                <tr>
                                <th>@lang('With')</th>
                                <th>@lang('Type')</th>
                                <th>@lang('Currency')</th>
                                <th>@lang('Payment Method')</th>
                                <th>@lang('Rate')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Requested At')</th>
                                <th>@lang('Action')</th>
                                </tr>
                            </thead>
                                <tbody>
                                    @forelse ($runningTradeRequests as $item)
                                        <tr>
                                            <td data-label="@lang('With')">
                                                @if ($item->buyer_id == Auth::user()->id)
                                                    {{__($item->seller->fullname)}}
                                                @endif

                                                @if ($item->seller_id == Auth::user()->id)
                                                    {{__($item->buyer->fullname)}}
                                                @endif
                                            </td>
                                            <td data-label="@lang('Type')">
                                                @if ($item->buyer_id == Auth::user()->id)
                                                    <span class="badge text-white badge--released">@lang('Buy')</span>
                                                @endif

                                                @if ($item->seller_id == Auth::user()->id)
                                                    <span class="badge text-white badge--fund">@lang('Sell')</span>
                                                @endif
                                            </td>
                                            <td data-label="@lang('Currency')">{{__($item->fiat->code)}}</td>
                                            <td data-label="@lang('Payment Method')">{{__($item->fiatGateway->name)}}</b></td>
                                            <td data-label="@lang('Rate')">{{showAmount($item->exchange_rate,2)}} {{__($item->fiat->code)}}/ {{__($item->crypto->code)}}</td>
                                            <td data-label="@lang('Status')">
                                                @if ($item->status == 0)
                                                    <span class="badge text-white badge--pending">@lang('Escrow Funded')</span>
                                                @elseif($item->status == 9)
                                                    <span class="badge text-white badge--cancel">@lang('Canceled')</span>
                                                @elseif($item->status == 2)
                                                    <span class="badge text-white badge--paid">@lang('Buyer Paid')</span>
                                                @elseif($item->status == 8)
                                                    <span class="badge text-white badge--reported">@lang('Reported')</span>
                                                @elseif($item->status == 1)
                                                    <span class="badge text-white badge--completed">@lang('Completed')</span>
                                                @endif
                                            </td>
                                            <td data-label="@lang('Requested At')">{{$item->created_at->diffForHumans()}}</td>
                                            <td data-label="@lang('Action')"><a href="{{route('user.trade.request.details',$item->uid)}}" class="cmn-btn btn-sm"><i class="las la-info-circle"></i></a></td>
                                        </tr>
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
                {{paginateLinks($runningTradeRequests, null)}}
            </div>

            <div class="col-xl-12">
                <h3 class="mb-3 mt-5">@lang('Completed Trades')</h3>
                <div class="custom--card">
                    <div class="card-body p-0">
                        <div class="table-responsive--md">
                            <table class="table custom--table">
                            <thead>
                                <tr>
                                <th>@lang('With')</th>
                                <th>@lang('Type')</th>
                                <th>@lang('Currency')</th>
                                <th>@lang('Payment Method')</th>
                                <th>@lang('Rate')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Requested At')</th>
                                <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($endedTradeRequests as $item)
                                    <tr>
                                        <td data-label="@lang('With')">
                                            @if ($item->buyer_id == Auth::user()->id)
                                                {{__($item->seller->fullname)}}
                                            @endif

                                            @if ($item->seller_id == Auth::user()->id)
                                                {{__($item->buyer->fullname)}}
                                            @endif
                                        </td>
                                        <td data-label="@lang('Type')">
                                            @if ($item->buyer_id == Auth::user()->id)
                                                <span class="badge text-white badge--released">@lang('Buy')</span>
                                            @endif

                                            @if ($item->seller_id == Auth::user()->id)
                                                <span class="badge text-white badge--fund">@lang('Sell')</span>
                                            @endif
                                        </td>
                                        <td data-label="@lang('Currency')">{{__($item->fiat->code)}}</td>
                                        <td data-label="@lang('Payment Method')">{{__($item->fiatGateway->name)}}</b></td>
                                        <td data-label="@lang('Rate')">{{showAmount($item->exchange_rate,2)}} {{__($item->fiat->code)}}/ {{__($item->crypto->code)}}</td>
                                        <td data-label="@lang('Status')">
                                            @if ($item->status == 0)
                                                <span class="badge text-white badge--pending">@lang('Escrow Funded')</span>
                                            @elseif($item->status == 9)
                                                <span class="badge text-white badge--cancel">@lang('Canceled')</span>
                                            @elseif($item->status == 2)
                                                <span class="badge text-white badge--paid">@lang('Buyer Paid')</span>
                                            @elseif($item->status == 8)
                                                <span class="badge text-white badge--reported">@lang('Reported')</span>
                                            @elseif($item->status == 1)
                                                <span class="badge text-white badge--completed">@lang('Completed')</span>
                                            @endif
                                        </td>
                                        <td data-label="@lang('Requested At')">{{$item->created_at->diffForHumans()}}</td>
                                        <td data-label="@lang('Action')"><a href="{{route('user.trade.request.details',$item->uid)}}" class="cmn-btn btn-sm"><i class="las la-info-circle"></i></a></td>
                                    </tr>
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
                {{paginateLinks($endedTradeRequests, null)}}
            </div>
          </div>
        </div>
    </section>
@endsection
