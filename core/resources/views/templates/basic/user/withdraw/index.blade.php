@extends($activeTemplate.'layouts.frontend')
@section('content')

    @include($activeTemplate.'partials.breadcrumb')

    <section class="pt-120 pb-120 section--bg">
        <div class="container">

            <div class="row justify-content-center">
                <div class="col-lg-4 col-md-6">
                    <div class="d-widget2">
                        <div class="icon">
                            <i class="las la-wallet"></i>
                        </div>
                        <div class="content">
                            <span class="d-widget2__caption">@lang('Current Balance')</span>
                            <h4 class="d-widget2__amount">{{showAmount($userBalance->balance,8)}} {{$crypto->code}}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mt-md-0 mt-4">
                    <div class="d-widget2">
                        <div class="icon">
                            <i class="las la-percent"></i>
                        </div>
                        <div class="content">
                            <span class="d-widget2__caption">@lang('Withdraw Charge')</span>
                            <h4 class="d-widget2__amount">@if($crypto->wc_fixed > 0) {{$crypto->wc_fixed}} {{$crypto->code}} +  @endif {{$crypto->wc_percent}}%</h4>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mt-lg-0 mt-4">
                    <div class="d-widget2">
                        <div class="icon">
                            <i class="las la-coins"></i>
                        </div>
                        <div class="content">
                            @php
                                $charge = $crypto->wc_fixed + ($userBalance->balance * $crypto->wc_percent / 100);
                                $maxWithdrawAmount = $userBalance->balance - $charge;
                            @endphp
                            <span class="d-widget2__caption">@lang('Withdrawal Limit')</span>
                            <h4 class="d-widget2__amount">@if($maxWithdrawAmount > 0) {{showAmount($userBalance->balance - $charge,8)}} {{$crypto->code}} @else 0.00000000 {{$crypto->code}} @endif</h4>
                        </div>
                    </div>
                </div>
            </div>

            <form class="bitcoin-form mt-4" action="{{route('user.withdraw.store')}}" method="POST">
                @csrf
                <div class="bitcoin-form-wrapper p-md-4 p-3 mb-5">
                    <h5 class="title text-white mb-3">@lang('Make Withdraw')</h5>

                    <div class="row">
                        <input type="hidden"  name="crypto" value="{{$crypto->code}}">

                        <div class="col-lg-8 form-group">
                            <input type="text"  name="wallet" class="form-control" placeholder="@lang('Wallet Address')">
                        </div>

                        <div class="col-lg-4 form-group">
                            <input type="number" step="any" name="amount" class="form-control" placeholder="@lang('Withdraw Amount')" autocomplete="off">
                            <small class="text--base"><span>@lang('Charge :')</span> <span class="withdraw-charge">0.00 {{$crypto->code}}</span></small>
                        </div>
                        <div class="col-lg-12">
                            <button type="submit" class="cmn-btn w-100">@lang('Request Withdraw')</button>
                        </div>
                    </div>
                </div>
            </form>

            <h4>@lang('Your Past') {{$crypto->code}} @lang('Withdrawals')</h4>

            <div class="row mt-3">
                <div class="col-xl-12">
                    <div class="custom--card">
                        <div class="card-body p-0">
                            <div class="table-responsive--lg">
                                <table class="table custom--table">
                                <thead>
                                    <tr>
                                        <th>@lang('Transaction ID')</th>
                                        <th>@lang('Wallet Address')</th>
                                        <th>@lang('Amount')</th>
                                        <th>@lang('Charge')</th>
                                        <th>@lang('After Charge')</th>
                                        <th>@lang('Status')</th>
                                        <th>@lang('Time')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($pastWithdrawals as $item)
                                        <tr>
                                            <td data-label="@lang('Transaction ID')">{{$item->trx}}</td>
                                            <td data-label="@lang('Wallet Address')">{{$item->wallet_address}}</td>
                                            <td data-label="@lang('Amount')">
                                                <strong>{{showAmount($item->amount,8)}} {{__($item->crypto->code)}}</strong>
                                            </td>
                                            <td data-label="@lang('Charge')" class="text-danger">
                                                {{showAmount($item->charge,8)}} {{__($item->crypto->code)}}
                                            </td>
                                            <td data-label="@lang('After Charge')">
                                                {{showAmount($item->payable,8)}} {{__($item->crypto->code)}}
                                            </td>

                                            <td data-label="@lang('Status')">
                                                @if($item->status == 2)
                                                    <span class="badge badge-warning">@lang('Pending')</span>
                                                @elseif($item->status == 1)
                                                    <span class="badge badge-success">@lang('Completed')</span>
                                                    <button class="bg--base btn-rounded  badge text-white approveBtn" data-admin_feedback="{{$item->admin_feedback}}"><i class="fa fa-info"></i></button>
                                                @elseif($item->status == 3)
                                                    <span class="badge badge-danger">@lang('Rejected')</span>
                                                    <button class="bg--base btn-rounded badge text-white approveBtn" data-admin_feedback="{{$item->admin_feedback}}"><i class="fa fa-info"></i></button>
                                                @endif

                                            </td>
                                            <td data-label="@lang('Time')">
                                                <i class="fa fa-calendar text--base"></i> {{showDateTime($item->created_at)}}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="100%" class="text-center text-muted">{{__($emptyMessage)}}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    {{$pastWithdrawals->links()}}
                </div>
            </div>
        </div>
    </section>

    {{-- Detail MODAL --}}
    <div id="detailModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Details')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="withdraw-detail"></div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">@lang('Close')</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        (function($){
            "use strict";
            $('.approveBtn').on('click', function() {
                var modal = $('#detailModal');
                var feedback = $(this).data('admin_feedback');
                modal.find('.withdraw-detail').html(`<p> ${feedback} </p>`);
                modal.modal('show');
            });

            $('input[name="amount"]').on('input',function(){
                var value = $(this).val();
                var fixedCharge = '{{$crypto->wc_fixed}}';
                var percentCharge = '{{$crypto->wc_percent}}';

                var charge = parseFloat(fixedCharge) + (parseFloat(value) * parseFloat(percentCharge) / 100);
                if (charge) {
                    $('.withdraw-charge').text(parseFloat(charge).toFixed(8) + ' {{$crypto->code}}');
                }else{
                    $('.withdraw-charge').text('0.00 {{$crypto->code}}');
                }
            });
        })(jQuery);

    </script>
@endpush

