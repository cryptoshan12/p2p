<div class="alert alert-warning" role="alert">
    <p class="text-md">@lang('Buy') {{__($tradeRequestsDetails->crypto->name)}} @lang('using') {{__($tradeRequestsDetails->fiatGateway->name)}} @lang('with') {{__($tradeRequestsDetails->fiat->name)}} ({{__($tradeRequestsDetails->fiat->code)}}). {{__($general->sitename)}} @lang('user named') {{__($tradeRequestsDetails->seller->fullname)}} @lang('wishes to sell') {{__($tradeRequestsDetails->crypto->name)}} @lang('to you')</p>
</div>

<h6>@lang('Please make a payment of') {{showAmount($tradeRequestsDetails->amount)}} {{__($tradeRequestsDetails->fiat->code)}} @lang('using') {{__($tradeRequestsDetails->fiatGateway->name)}} @lang('E-Wallet')</h6>

<p class="text-md mt-2">@lang('Once you confirmed your payment to seller') <span class="badge text-white badge--released">{{showAmount($tradeRequestsDetails->crypto_amount,8)}}</span> {{__($tradeRequestsDetails->advertisement->crypto->code)}}  @lang('will be released')</p>

<hr>

<div class="accordion cmn-accordion accordion-arrow" id="accordionExample">
    <div class="card">
        <div class="card-header" id="headingOne">
        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
            <i class="las la-question-circle"></i>
            <h6>@lang('Trade Information')</h6>
        </button>
        </div>

        <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">
        <div class="card-body">
            <ul class="caption-list">
                <li>
                    <span class="caption">@lang('Buyer Name')</span>
                    <span class="value">{{__($tradeRequestsDetails->buyer->fullname)}}</span>
                </li>
                <li>
                    <span class="caption">@lang('Seller Name')</span>
                    <span class="value">{{__($tradeRequestsDetails->seller->fullname)}}</span>
                </li>
                <li>
                    <span class="caption">@lang('Amount')</span>
                    <span class="value">{{showAmount($tradeRequestsDetails->amount)}} {{__($tradeRequestsDetails->fiat->code)}}</span>
                </li>
                <li>
                    <span class="caption">{{__($tradeRequestsDetails->crypto->code)}}</span>
                    <span class="value">{{showAmount($tradeRequestsDetails->crypto_amount,8)}}</span>
                </li>
                <li>
                    <span class="caption">@lang('Payment Window')</span>
                    <span class="value">{{$tradeRequestsDetails->window}} @lang('Minutes')</span>
                </li>
            </ul>
        </div>
        </div>
    </div><!-- card end -->
</div>
<hr>

@if ($tradeRequestsDetails->status == 0)
    @php
        $endTime = $tradeRequestsDetails->created_at->addMinutes($tradeRequestsDetails->window);
        $remainingMinitues = $endTime->diffInMinutes(Carbon\Carbon::now());
    @endphp

    @if ($endTime > Carbon\Carbon::now())
        <div class="alert alert-warning" role="alert">
            <p class="text-md">@lang('The seller can cancel this trade after') <span id="cancel-min">{{$remainingMinitues}}</span> @lang('minutes. Please make the payment within that time and mark the payment as paid.')</p>
        </div>
    @else
        <div class="alert alert-warning" role="alert">
            <p class="text-md">@lang('The seller can cancel this trade anytime. Please make the payment and mark the payment as paid.')</p>
        </div>
    @endif

    <hr>
    <div class="row mt-4">
        <div class="col-md-6">
            <button type="button" class="cmn-btn bg--success w-100 user-action" data-value="1" data-route="{{route('user.trade.request.paid')}}">@lang('I Have Paid') <i class="las la-check"></i>
            </button>
        </div>
        <div class="col-md-6 mt-md-0 mt-3">
            <input type="hidden" name="id" value="{{$tradeRequestsDetails->id}}">
            <button type="button" class="cmn-btn btn--danger w-100 user-action" data-value="2" data-route="{{route('user.trade.request.cancel')}}">@lang('Cancel') <i class="las la-times"></i></button>
        </div>
    </div>

    <hr>
@endif

@if ($tradeRequestsDetails->status == 2)

    @php
        $lastTime = Carbon\Carbon::parse($tradeRequestsDetails->paid_at)->addMinutes($tradeRequestsDetails->window);
        $remainingMin = $lastTime->diffInMinutes(Carbon\Carbon::now());
    @endphp

    @if ($lastTime > Carbon\Carbon::now())
        <div class="alert alert-warning" role="alert">
            <p class="text-md">@lang('You can dispute this trade after') <span id="dispute-min">{{$remainingMin}}</span> @lang('minutes.')</p>
        </div>
        <hr>
    @endif

    @if ($lastTime <= Carbon\Carbon::now())

        <div class="row mt-4">
            <div class="col-xl-12">
                <button type="button" class="cmn-btn btn--danger w-100 user-action" data-value="3" data-route="{{route('user.trade.request.dispute')}}">@lang('Dispute') <i class="las la-times"></i></button>
            </div>
        </div>
        <hr>
    @endif
@endif


<div class="modal fade" id="actionModal" tabindex="-1" role="dialog" aria-labelledby="existModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="existModalLongTitle">@lang('Confirmation')!</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <form class="action-route" method="POST">
            @csrf
            <input type="hidden" name="id" value="{{$tradeRequestsDetails->id}}">
            <div class="modal-body">
                <p class="action-msz"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal"><i class="fa fa-times"></i>
                    @lang('No')
                </button>
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-check"></i> @lang("Yes")
                </button>
            </div>
        </form>
      </div>
    </div>
</div>

@push('script')

    <script>
        (function($) {
            "use strict";
            var actionModal = $('#actionModal');

            $('.user-action').on('click', function() {
                var value = $(this).data('value');
                var route = $(this).data('route');
                var actionMsz;

                if (value == 1) {
                    actionMsz =`@lang('By proceeding with this action, this trade will be marked as paid by you')`;
                } else if (value == 2) {
                    actionMsz =`@lang('By proceeding with this action, this trade will be marked as canceled by you')`;
                }
                else {
                    actionMsz =`@lang('By proceeding with this action, this trade will be marked as dispute by you')`;
                }

                actionModal.find('.action-msz').html(actionMsz);
                actionModal.find('.action-route').attr('action', route);
                actionModal.modal('show');
            });

            function startTimer(duration, display) {
                var timer = duration, minutes, seconds;
                setInterval(function () {
                    minutes = parseInt(timer / 60, 10);
                    seconds = parseInt(timer % 60, 10);

                    minutes = minutes < 10 ? "0" + minutes : minutes;
                    seconds = seconds < 10 ? "0" + seconds : seconds;

                    display.textContent = minutes + ":" + seconds;

                    if (--timer < 0) {
                        timer = duration;
                    }
                }, 1000);
            }

            @if ($tradeRequestsDetails->status == 0)
                window.onload = function () {
                    var cancelMinutes = 60 * '{{$remainingMinitues}}',
                    display = document.querySelector('#cancel-min');
                    startTimer(cancelMinutes, display);
                };

            @endif

            @if ($tradeRequestsDetails->status == 2)
                window.onload = function () {
                    var disputeMinutes = 60 * '{{$remainingMin}}',
                    display = document.querySelector('#dispute-min');
                    startTimer(disputeMinutes, display);
                };
            @endif


        })(jQuery);
    </script>

@endpush


