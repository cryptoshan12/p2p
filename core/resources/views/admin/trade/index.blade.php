@extends('admin.layouts.app')

@section('panel')

    <div class="row">
        <div class="col-lg-12 col-md-12 mb-30">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Buyer')</th>
                                    <th>@lang('Seller')</th>
                                    <th>@lang('Unique Id')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Payment Method')</th>
                                    <th>@lang('Exchange Rate')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody class="list">
                                @forelse ($tradeRequests as $item)
                                    <tr>
                                        <td data-label="@lang('Buyer')">
                                            <span class="font-weight-bold d-block">{{$item->buyer->fullname}}</span>

                                            <span class="small">
                                            <a href="{{ route('admin.users.detail', $item->buyer->id) }}"><span>@</span>{{ $item->buyer->username }}</a>
                                            </span>
                                        </td>
                                        <td data-label="@lang('Seller')">
                                            <span class="font-weight-bold d-block">
                                                {{$item->seller->fullname}}
                                            </span>
                                            <span class="small">
                                                <a href="{{ route('admin.users.detail', $item->seller->id) }}">
                                                    <span>@</span>{{ $item->seller->username }}
                                                </a>
                                            </span>
                                        </td>
                                        <td data-label="@lang('Unique Id')"><b>{{$item->uid}}</b></td>
                                        <td data-label="@lang('Amount')"><b>{{showAmount($item->amount,2)}}</b> {{__($item->fiat->code)}}</td>
                                        <td data-label="@lang('Payment Method')">{{__($item->fiatGateway->name)}}<br> {{__($item->fiat->code)}}</td>
                                        <td data-label="@lang('Exchange Rate')">
                                            <span class="font-weight-bold">{{showAmount($item->exchange_rate)}}</span>
                                            <br>
                                            <span class="small">{{__($item->fiat->code)}}/ {{__($item->crypto->code)}}</span>
                                        </td>
                                        <td data-label="@lang('Status')">
                                            @if ($item->status == 0)
                                                <span class="badge text-white badge--pendingg">@lang('Escrow Funded')</span>
                                            @elseif($item->status == 2)
                                                <span class="badge text-white badge--paid">@lang('Buyer Paid')</span>
                                            @elseif($item->status == 9)
                                                <span class="badge text-white badge--cancel">@lang('Canceled')</span>
                                            @elseif($item->status == 8)
                                                <span class="badge text-white badge--reported">@lang('Reported')</span>
                                            @elseif($item->status == 1)
                                                <span class="badge text-white badge--completed">@lang('Completed')</span>
                                            @endif
                                        </td>

                                        <td data-label="@lang('Action')">
                                            <a href="{{route('admin.trade.request.details',$item->id)}}" class="icon-btn"><i class="las la-eye"></i></a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($tradeRequests->hasPages())
                    <div class="card-footer py-4">
                        {{ $tradeRequests->links('admin.partials.paginate') }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('breadcrumb-plugins')
        <form action="{{ route('admin.trade.request.search') }}" method="GET" class="form-inline float-sm-right bg--white mt-2">
            <div class="input-group has_append">
                <input type="text" name="search" class="form-control" placeholder="@lang('Unique Id / Username')" value="{{ request()->search??null }}">
                <div class="input-group-append">
                    <button class="btn btn--primary" type="submit"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </form>
    @endpush

@endsection
