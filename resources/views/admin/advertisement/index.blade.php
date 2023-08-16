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
                                    <th>@lang('User')</th>
                                    <th>@lang('Type')</th>
                                    <th>@lang('Payment Method')</th>
                                    <th>@lang('Margin')</th>
                                    <th>@lang('Rate')</th>
                                    <th>@lang('Payment Window')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($advertisements as $item)
                                    <tr>
                                        <td data-label="@lang('User')">
                                            <span class="font-weight-bold d-block">{{$item->user->fullname}}</span>
                                            <span class="small">
                                                <a href="{{ route('admin.users.detail', $item->user->id) }}"><span>@</span>{{ $item->user->username }}</a>
                                            </span>
                                        </td>

                                        <td data-label="@lang('Type')">
                                            @if ($item->type == 1)
                                                <span class="badge badge--primary">@lang('Buy')</span>
                                            @else
                                                <span class="badge badge--success">@lang('Sell')</span>
                                            @endif
                                        </td>

                                        <td data-label="@lang('Payment Method')">
                                            {{__($item->fiatGateway->name)}}
                                            <span class="d-block font-weight-bold">{{__($item->fiat->code)}}</span>
                                        </td>

                                        <td data-label="@lang('Margin')">{{ getAmount($item->margin) }}%</td>

                                        <td data-label="@lang('Rate')">
                                            <span class="font-weight-bold">{{showAmount(rate($item->type, $item->crypto->rate, $item->fiat->rate, $item->margin))}} {{__($item->fiat->code)}}</span>
                                            <span class="small d-block">/ {{__($item->crypto->code)}}</span>
                                        </td>

                                        <td data-label="@lang('Payment Window')">{{$item->window}} @lang('Minutes')</td>

                                        <td data-label="@lang('Status')">
                                            @if ($item->status == 1)
                                                <span class="badge badge--primary">@lang('Active')</span>
                                            @else
                                                <span class="badge badge--danger">@lang('Deactive')</span>
                                            @endif
                                        </td>

                                        <td data-label="@lang('Action')">
                                            <a href="{{route('admin.ad.view',$item->id)}}" class="icon-btn"><i class="las la-eye"></i></a>
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

                @if($advertisements->hasPages())
                <div class="card-footer py-4">
                    {{ $advertisements->links('admin.partials.paginate') }}
                </div>
                @endif
            </div>
        </div>
    </div>

    @push('breadcrumb-plugins')
        <form action="{{ route('admin.ad.search') }}" method="GET" class="form-inline float-sm-right bg--white mt-2">
            <div class="input-group has_append">
                <input type="text" name="search" class="form-control" placeholder="@lang('Username')" value="{{ request()->search??null }}">
                <div class="input-group-append">
                    <button class="btn btn--primary" type="submit"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </form>
    @endpush

@endsection
