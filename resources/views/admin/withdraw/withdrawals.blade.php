@extends('admin.layouts.app')

@section('panel')
<div class="row justify-content-center">
    <div class="col-lg-12">
        <div class="card b-radius--10 ">
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
                            @forelse($withdrawals as $withdraw)
                            <tr>
                                <td data-label="@lang('Initiated')">
                                    {{ showDateTime($withdraw->created_at) }} <br>  {{ diffForHumans($withdraw->created_at) }}
                                </td>

                                <td data-label="@lang('Transaction ID')">
                                    {{ $withdraw->trx }}
                                </td>

                                <td data-label="@lang('User')">
                                    <span class="font-weight-bold">{{ $withdraw->user->fullname }}</span>
                                    <br>
                                    <span class="small"> <a href="{{ route('admin.users.detail', $withdraw->user_id) }}"><span>@</span>{{ $withdraw->user->username }}</a> </span>
                                </td>


                                <td data-label="@lang('Amount')">
                                   {{ __($withdraw->crypto->symbol) }}{{ showAmount($withdraw->amount,8 ) }} - <span class="text-danger" data-toggle="tooltip" data-original-title="@lang('charge')">{{ showAmount($withdraw->charge,8)}} {{ __($withdraw->crypto->code) }}</span>
                                    <br>
                                    <strong data-toggle="tooltip" data-original-title="@lang('Amount after charge')">
                                    {{ showAmount($withdraw->amount - $withdraw->charge,8) }} {{ __($withdraw->crypto->code) }}
                                    </strong>

                                </td>

                                <td data-label="@lang('Status')">
                                    @if($withdraw->status == 2)
                                    <span class="text--small badge font-weight-normal badge--warning">@lang('Pending')</span>
                                    @elseif($withdraw->status == 1)
                                    <span class="text--small badge font-weight-normal badge--success">@lang('Approved')</span>
                                    <br>{{ diffForHumans($withdraw->updated_at) }}
                                    @elseif($withdraw->status == 3)
                                    <span class="text--small badge font-weight-normal badge--danger">@lang('Rejected')</span>
                                    <br>{{ diffForHumans($withdraw->updated_at) }}
                                    @endif
                                </td>

                                <td data-label="@lang('Action')">
                                    <a href="{{ route('admin.withdraw.details', $withdraw->id) }}" class="icon-btn ml-1 " data-toggle="tooltip" data-original-title="@lang('Detail')">
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
        @if($withdrawals->hasPages())
            <div class="card-footer py-4">
                {{ paginateLinks($withdrawals) }}
            </div>
        @endif
    </div><!-- card end -->
</div>
</div>

@endsection




@push('breadcrumb-plugins')

    @if(!request()->routeIs('admin.users.withdrawals') && !request()->routeIs('admin.users.withdrawals.method'))

    <form action="{{ route('admin.withdraw.search', $scope ?? str_replace('admin.withdraw.', '', request()->route()->getName())) }}" method="GET" class="form-inline float-sm-right bg--white mb-2 ml-0 ml-xl-2 ml-lg-0">
        <div class="input-group has_append">
            <input type="text" name="search" class="form-control" placeholder="@lang('Trx number/Username')" value="{{ $search ?? '' }}">
            <div class="input-group-append">
                <button class="btn btn--primary" type="submit"><i class="fa fa-search"></i></button>
            </div>
        </div>
    </form>
    <form action="{{route('admin.withdraw.dateSearch',$scope ?? str_replace('admin.withdraw.', '', request()->route()->getName()))}}" method="GET" class="form-inline float-sm-right bg--white">
        <div class="input-group has_append">
            <input name="date" type="text" data-range="true" data-multiple-dates-separator=" - " data-language="en" class="datepicker-here form-control" data-position='bottom right' placeholder="@lang('Min Date - Max date')" autocomplete="off" value="{{ @$dateSearch }}">
            <input type="hidden" name="method" value="{{ @$method->id }}">
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
        'use strict';
        if(!$('.datepicker-here').val()){
            $('.datepicker-here').datepicker();
        }
    })(jQuery)
</script>
@endpush
