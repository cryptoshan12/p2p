@extends($activeTemplate.'layouts.frontend')
@section('content')
@include($activeTemplate.'partials.breadcrumb')

    <section class="pt-120 pb-120">
        <div class="container">

          <div class="row">
            <div class="col-xl-12">
                <div class="custom--card">
                    <div class="card-body p-0">
                        <div class="table-responsive--md">
                            <table class="table custom--table">
                            <thead>
                                <tr>
                                    <th>@lang('Crypto Currency')</th>
                                    <th>@lang('Transaction ID')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Time')</th>
                                    <th> @lang('More')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($logs) >0)
                                    @foreach($logs as $k=>$data)
                                        <tr>
                                            <td data-label="@lang('Crypto Currency')"><span class="text--base">{{__($data->crypto->code)}}</span></td>
                                            <td data-label="#@lang('Transaction ID')">{{$data->trx}}</td>
                                            <td data-label="@lang('Amount')">
                                                <strong>{{showAmount($data->amount,8)}} {{__($data->crypto->code)}}</strong>
                                            </td>
                                            <td>
                                                @if($data->status == 1)
                                                    <span class="badge text-white badge--completed">@lang('Completed')</span>
                                                @endif

                                            </td>
                                            <td data-label="@lang('Time')">
                                                <i class="fa fa-calendar text--base"></i> {{showDateTime($data->created_at)}}
                                            </td>


                                            <td data-label="@lang('More')">
                                                <a href="javascript:void(0)" class="cmn-btn btn-sm approveBtn"
                                                    data-id="{{ $data->id }}"
                                                    data-amount="{{ showAmount($data->amount,8)}} {{ __($data->crypto->code) }}"
                                                    data-charge="{{ showAmount($data->charge,8)}} {{ __($data->crypto->code) }}"
                                                    data-after_charge="{{ showAmount($data->amount + $data->charge,8)}} {{ __($data->crypto->code) }}"
                                                    data-payable="{{ showAmount($data->final_amo,8)}} {{ __($data->crypto->code) }}">
                                                    <i class="las la-desktop"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="100%" class="text-center">{{__($emptyMessage)}}</td>
                                    </tr>
                                @endif

                            </tbody>
                            </table>
                        </div>
                    </div>
                </div>

              {{$logs->links()}}
            </div>
          </div>
        </div>
    </section>

    {{-- APPROVE MODAL --}}
    <div id="approveModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Details')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <ul class="caption-list">
                        <li>
                            <span class="caption">@lang('Amount')</span>
                            <span class="value withdraw-amount"></span>
                        </li>
                        <li>
                            <span class="caption">@lang('Charge')</span>
                            <span class="value withdraw-charge "></span>
                        </li>
                        <li>
                            <span class="caption">@lang('After Charge')</span>
                            <span class=" valuewithdraw-after_charge"></span>
                        </li>
                        <li>
                            <span class="caption">@lang('Payable Amount')</span>
                            <span class="value withdraw-payable"></span>
                        </li>
                    </ul>
                    <ul class="caption-list withdraw-detail mt-2">
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dark" data-dismiss="modal">@lang('Close')</button>
                </div>
            </div>
        </div>
    </div>

@endsection


@push('script')
    <script>
        (function ($) {
            "use strict";
            $('.approveBtn').on('click', function() {
                var modal = $('#approveModal');
                modal.find('.withdraw-amount').text($(this).data('amount'));
                modal.find('.withdraw-charge').text($(this).data('charge'));
                modal.find('.withdraw-after_charge').text($(this).data('after_charge'));
                modal.find('.withdraw-payable').text($(this).data('payable'));

                modal.modal('show');
            });

        })(jQuery);
    </script>
@endpush

