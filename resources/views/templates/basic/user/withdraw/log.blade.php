@extends($activeTemplate.'layouts.frontend')

@section('content')
    @include($activeTemplate.'partials.breadcrumb')

    <div class="container">
        <section class="pt-120 pb-120">
            <div class="row">
                <div class="col-xl-12">
                    <div class="custom--card">
                        <div class="card-body p-0">
                            <div class="table-responsive--lg">
                                <table class="table custom--table">
                                    <thead>
                                        <tr>
                                            <th>@lang('Crypto Currency')</th>
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
                                        @forelse($withdraws as $k=>$item)
                                            <tr>
                                                <td data-label="@lang('Crypto Currency')"><span class="text--base">{{__($item->crypto->code)}}</span></td>
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
                                                        <span class="badge text-white badge--pending">@lang('Pending')</span>
                                                    @elseif($item->status == 1)
                                                        <span class="badge text-white badge--completed">@lang('Completed')</span>
                                                        <button class="bg--base btn-rounded badge text-white approveBtn" data-admin_feedback="{{$item->admin_feedback}}"><i class="fa fa-info"></i></button>
                                                    @elseif($item->status == 3)
                                                        <span class="badge text-white badge--reported">@lang('Rejected')</span>
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
                    {{$withdraws->links()}}
                </div>
            </div>
        </section>
    </div>



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
        })(jQuery);

    </script>
@endpush
