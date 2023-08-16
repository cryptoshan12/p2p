@extends('admin.layouts.app')

@section('panel')

<div class="row justify-content-center">

    <div class="col-md-12">
        <div class="card b-radius--10">
            <div class="card-body p-0">
                <div class="table-responsive--sm table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                        <tr>
                            <th>@lang('Initiated')</th>
                            <th>@lang('Transaction ID')</th>
                            <th>@lang('User')</th>
                            <th>@lang('Amount')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Action')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($deposits as $deposit)
                            <tr>

                                <td data-label="@lang('Initiated')">
                                    {{ showDateTime($deposit->created_at) }}<br>{{ diffForHumans($deposit->created_at) }}
                                </td>
                                <td data-label="@lang('Transaction ID')">
                                    {{ $deposit->trx }}
                                </td>
                                <td data-label="@lang('User')">
                                    <span class="font-weight-bold">{{ $deposit->user->fullname }}</span>
                                    <br>
                                    <span class="small">
                                    <a href="{{ route('admin.users.detail', $deposit->user_id) }}"><span>@</span>{{ $deposit->user->username }}</a>
                                    </span>
                                </td>
                                <td data-label="@lang('Amount')">
                                   {{ __($deposit->crypto->symbol) }}{{ showAmount($deposit->amount,8 ) }} + <span class="text-danger" data-toggle="tooltip" data-original-title="@lang('charge')">{{ showAmount($deposit->charge,8)}} {{ __($deposit->crypto->code) }}</span>
                                    <br>
                                    <strong data-toggle="tooltip" data-original-title="@lang('Amount with charge')">
                                    {{ showAmount($deposit->amount + $deposit->charge,8) }} {{ __($deposit->crypto->code) }}
                                    </strong>
                                </td>
                                <td data-label="@lang('Status')">
                                    @if($deposit->status == 2)
                                        <span class="badge badge--warning">@lang('Pending')</span>
                                    @elseif($deposit->status == 1)
                                        <span class="badge badge--success">@lang('Approved')</span>
                                         <br>{{ diffForHumans($deposit->updated_at) }}
                                    @elseif($deposit->status == 3)
                                        <span class="badge badge--danger">@lang('Rejected')</span>
                                        <br>{{ diffForHumans($deposit->updated_at) }}
                                    @endif
                                </td>
                                <td data-label="@lang('Action')">
                                    <a href="{{ route('admin.deposit.details', $deposit->id) }}"
                                       class="icon-btn ml-1 " data-toggle="tooltip" title="" data-original-title="@lang('Detail')">
                                        <i class="la la-desktop"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table><!-- table end -->
                </div>
            </div>

            @if($deposits->hasPages())
            <div class="card-footer py-4">
                {{ paginateLinks($deposits) }}
            </div>
            @endif
        </div><!-- card end -->
    </div>
</div>


@endsection


@push('breadcrumb-plugins')
    @if(!request()->routeIs('admin.users.deposits') && !request()->routeIs('admin.users.deposits.method'))

        <form action="{{route('admin.deposit.search')}}" method="GET" class="form-inline float-sm-right bg--white mb-2 ml-0 ml-xl-2 ml-lg-0">
            <div class="input-group has_append  ">
                <input type="text" name="search" class="form-control" placeholder="@lang('Trx number/Username')" value="{{ $search ?? '' }}">
                <div class="input-group-append">
                    <button class="btn btn--primary" type="submit"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </form>

        <form action="{{route('admin.deposit.dateSearch')}}" method="GET" class="form-inline float-sm-right bg--white">
            <div class="input-group has_append ">
                <input name="date" type="text" data-range="true" data-multiple-dates-separator=" - " data-language="en" class="datepicker-here form-control bg-white text--black" data-position='bottom right' placeholder="@lang('Min date - Max date')" autocomplete="off" value="{{ @$dateSearch }}" readonly>
                <input type="hidden" name="method" value="{{ @$methodAlias }}">
                <div class="input-group-append">
                    <button class="btn btn--primary" type="submit"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </form>

    @endif
@endpush


@push('script-lib')
  <script src="{{ asset('assets/admin/js/vendor/datepicker.min.js') }}"></script>
  <script src="{{ asset('assets/admin/js/vendor/datepicker.en.js') }}"></script>
@endpush
@push('script')
  <script>
    (function($){
        "use strict";
        if(!$('.datepicker-here').val()){
            $('.datepicker-here').datepicker();
        }
    })(jQuery)
  </script>
@endpush
