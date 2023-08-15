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
                                    <th>@lang('Chain')</th>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Wallet Address')</th>
                                    <th>@lang('Gas Limit')</th>
                                    <th>@lang('Net Confirmations')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody class="list">
                                @forelse ($metamaskconf as $item)
                                    <tr>
                                        <td data-label="@lang('Chainid')">{{__($item->chain_id)}}</td>
                                        <td data-label="@lang('Name')">{{__($item->name)}}</td>
                                        <td data-label="@lang('Wallet_address')">{{__($item->wallet_address)}}</td>
                                        <td data-label="@lang('Wallet_address')">{{__($item->gas_limit)}}</td>
                                        <td data-label="@lang('Wallet_address')">{{__($item->network_confirmations)}}</td>

                                        <td data-label="@lang('Status')">
                                            @if ($item->status == 1)
                                                <span class="badge badge--success">@lang('Active')</span>
                                            @else
                                                <span class="badge badge--danger">@lang('Deactive')</span>
                                            @endif
                                        </td>
                                        <td data-label="@lang('Action')">
                                            <a href="{{route('admin.crypto.metamask.edit',$item->id)}}" class="icon-btn" ><i class="la la-pencil-alt"></i></a>
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
                @if($metamaskconf->hasPages())
                <div class="card-footer py-4">
                    {{ $metamaskconf->links('admin.partials.paginate') }}
                </div>
                @endif
            </div>
        </div>
    </div>

    @push('breadcrumb-plugins')

        <form action="{{ route('admin.crypto.search') }}" method="GET" class="form-inline float-sm-right bg--white mt-2">
            <div class="input-group has_append">
                <input type="text" name="search" class="form-control" placeholder="@lang('Name / Code')" value="{{ request()->search??null }}">
                <div class="input-group-append">
                    <button class="btn btn--primary" type="submit"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </form>

    @endpush
@endsection
