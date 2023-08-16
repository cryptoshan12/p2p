@extends('admin.layouts.app')

@section('panel')
    <section>
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-6">
                    <div class="card style--two">
                        <div class="card-header">
                            <h5> @lang('Information') </h5>
                        </div>

                        <div class="card-body">

                            <h6 class="mb-3"><b>@lang('Trade Information')</b></h6>
                            <div class="row">
                                <div class="col-lg-6">
                                <span>@lang('Buyer Name')</span>
                                </div>
                                <div class="col-lg-6">
                                <span>{{__($tradeRequestDetails->buyer->fullname)}}</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <span>@lang('Seller Name')</span>
                                </div>
                                <div class="col-lg-6">
                                    <span>{{__($tradeRequestDetails->seller->fullname)}}</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <span>@lang('Amount')</span>
                                </div>
                                <div class="col-lg-6">
                                    <span> {{showAmount($tradeRequestDetails->amount)}} {{__($tradeRequestDetails->fiat->code)}} </span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <span>{{__($tradeRequestDetails->crypto->code)}}</span>
                                </div>
                                <div class="col-lg-6">
                                    <span class="badge badge-info">{{showAmount($tradeRequestDetails->crypto_amount,8)}}</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <span>Trade Completetion Charges</span>
                                </div>
                                <div class="col-lg-6">
                                    <span class="badge badge-info">{{showAmount($tradeRequestDetails->trade_charge,8)}}</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <span>@lang('Unique Id')</span>
                                </div>
                                <div class="col-lg-6">
                                    <span>{{$tradeRequestDetails->uid}}</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <span>@lang('Payment Window')</span>
                                </div>
                                <div class="col-lg-6">
                                    <span> {{$tradeRequestDetails->window}}  @lang('Minutes')</span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-6">
                                <span>@lang('Status')</span>
                                </div>
                                <div class="col-lg-6">
                                    @if ($tradeRequestDetails->status == 0)
                                        <span class="badge text-white badge--pendingg">@lang('Escrow Funded')</span>
                                    @elseif($tradeRequestDetails->status == 2)
                                        <span class="badge text-white badge--paid">@lang('Buyer Paid')</span>
                                    @elseif($tradeRequestDetails->status == 9)
                                        <span class="badge text-white badge--cancel">@lang('Canceled')</span>
                                    @elseif($tradeRequestDetails->status == 8)
                                        <span class="badge text-white badge--reported">@lang('Reported')</span>
                                    @elseif($tradeRequestDetails->status == 1)
                                        <span class="badge text-white badge--completed">@lang('Completed')</span>
                                    @endif
                                </div>
                            </div>

                            <hr>

                            @if ($tradeRequestDetails->status == 8)
                                <h6 class="mb-3"><b>@lang('Action')</b></h6>
                                <div class="row mt-4">
                                    <div class="col-xl-6">
                                        <a href="javascript:void(0)" class="btn btn-sm btn--info release releaseBtn"> {{__($tradeRequestDetails->crypto->code)}} @lang('Release')</a>

                                        <div class="form-group mt-2">
                                            <small class="text--info"><b>@lang('Amount will be added with buyer wallet.')</b></small>
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <a href="javascript:void(0)" class="btn btn-sm btn--primary return returnBtn"> {{__($tradeRequestDetails->crypto->code)}} @lang('Return')</a>

                                        <div class="form-group mt-2">
                                            <small class="text--primary"><b>@lang('Amount will be added with seller wallet.')</b></small>
                                        </div>
                                    </div>
                                </div><!-- row end -->

                                <hr>
                            @endif

                            <h6 class="font-weight-bold mb-2">@lang('Terms of Trade')</h6>
                            <p>{{__($tradeRequestDetails->advertisement->terms)}}</p>

                            <h6 class="font-weight-bold mt-4 mb-2">@lang('Payment Details')</h6>
                            <p>{{__($tradeRequestDetails->advertisement->details)}}</p>
                        </div>

                    </div>
                </div>
                <div class="col-lg-6 pl-lg-5 mt-lg-0 mt-5">
                    <div class="chat-box">
                        <div class="chat-box__header">
                            <div class="chat-author">
                                <div class="thumb">
                                    <img src="{{ getImage(imagePath()['profile']['admin']['path'].'/'. auth()->guard('admin')->user()->image,imagePath()['profile']['admin']['size'])}}" alt="image">
                                </div>
                                <div class="content">
                                <h6 class="name">
                                    @lang('Admin')
                                </h6>
                                <span class="text-sm active-status"></span>
                                </div>
                            </div>
                        </div>

                        <div class="chat-box__thread">

                            @foreach ($tradeRequestDetails->chats()->get() as $item)
                                @if ($item->user_id == $tradeRequestDetails->buyer_id)
                                    <div class="single-message clearfix message--left">
                                        <div class="message-author">
                                            <img src="{{ getImage(imagePath()['profile']['user']['path'].'/'. $tradeRequestDetails->buyer->image,imagePath()['profile']['user']['size']) }}" alt="image" class="thumb">
                                            <h6 class="name">{{__($tradeRequestDetails->buyer->fullname)}}</h6>
                                        </div>
                                        <span class="message-time">{{$item->created_at->diffForHumans()}}</span>
                                        <p class="message-text">{{__($item->message)}}.</p>

                                        @if ($item->file)
                                            <div class="messgae-attachment">
                                                <b class="text-sm d-block mb-2"> @lang('Attachment') </b>
                                                    <a href="{{route('admin.download',[$tradeRequestDetails->id,$item->id])}}" class="file-demo-btn">
                                                    {{__($item->file)}}
                                                </a>
                                            </div>
                                        @endif

                                    </div><!-- single-message end -->
                                @endif

                                @if ($item->user_id == $tradeRequestDetails->seller_id)
                                    <div class="single-message clearfix message--right">
                                        <div class="message-author">
                                            <img src="{{ getImage(imagePath()['profile']['user']['path'].'/'. $tradeRequestDetails->seller->image,imagePath()['profile']['user']['size']) }}" alt="image" class="thumb">
                                            <h6 class="name">{{__($tradeRequestDetails->seller->fullname)}}</h6>
                                        </div>
                                        <span class="message-time">{{$item->created_at->diffForHumans()}}</span>
                                        <p class="message-text">{{__($item->message)}}</p>

                                        @if ($item->file)
                                            <div class="messgae-attachment">
                                                <b class="text-sm d-block mb-2"> @lang('Attachment') </b>
                                                    <a href="{{route('user.download',[$tradeRequestDetails->id,$item->id])}}" class="file-demo-btn">
                                                    {{__($item->file)}}
                                                </a>
                                            </div>
                                        @endif
                                    </div><!-- single-message end -->
                                @endif

                                @if ($item->admin)
                                    <div class="single-message clearfix message--right admin-bg-reply">
                                        <div class="message-author">
                                            <img src="{{ getImage(imagePath()['profile']['admin']['path'].'/'. auth()->guard('admin')->user()->image,imagePath()['profile']['admin']['size'])}}" alt="image" class="thumb">
                                            <h6 class="name">@lang('Admin')</h6>
                                        </div>
                                        <span class="message-time">{{$item->created_at->diffForHumans()}}</span>
                                        <p class="message-text">{{__($item->message)}}</p>

                                        @if ($item->file)
                                            <div class="messgae-attachment">
                                                <b class="text-sm d-block mb-2 text-dark"> @lang('Attachment') </b>
                                                <a href="{{route('user.download',[$tradeRequestDetails->id,$item->id])}}" class="file-demo-btn">
                                                    {{__($item->file)}}
                                                </a>
                                            </div>
                                        @endif
                                    </div><!-- single-message end -->
                                @endif
                            @endforeach

                        </div><!-- chat-box__thread end -->

                        @if (($tradeRequestDetails->status == 0) || ($tradeRequestDetails->status == 2) || ($tradeRequestDetails->status == 8))
                            <div class="chat-box__footer">
                                <form action="{{route('admin.trade.request.chat.store',$tradeRequestDetails->id)}}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row align-items-center">
                                        <div class="col-md-10 order-1">
                                            <textarea name="message" id="chat-message-field" placeholder="@lang('Type here')" class="form-control" required></textarea>
                                        </div>
                                        <div class="col-md-2 col-sm-4 text-right order-md-2 order-sm-3 order-2 mt-md-0 mt-3">
                                            <button type="sbumit" class="cmn-btn btn-md"><i class="far fa-paper-plane"></i></button>
                                        </div>
                                        <div class="col-md-12 col-sm-8 mt-sm-3 order-md-2 order-sm-2 order-3">
                                            <div class="position-relative">
                                                <input type="file" id="upload-field" name="file" class="custom-file" accept=".jpg ,.pdf">
                                                <label for="upload-field">@lang('File Upload')</label>
                                            </div>
                                        </div>
                                    </div>

                                </form>
                            </div>
                        @endif
                    </div><!-- chat-box end -->
                </div>
            </div>
        </div>
    </section>

    {{-- Release METHOD MODAL --}}
    <div id="releaseModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"> @lang('Confirmation!')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{route('admin.trade.request.release',$tradeRequestDetails->id)}}" method="GET">
                    @csrf
                    <div class="modal-body">
                        <h4>@lang('Are you sure to release this trade?')</h4>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn--primary">@lang('Release')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Return METHOD MODAL --}}
    <div id="returnModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"> @lang('Confirmation!')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{route('admin.trade.request.return',$tradeRequestDetails->id)}}" method="GET">
                    @csrf
                    <div class="modal-body">
                        <h4>@lang('Are you sure to return this trade?')</h4>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn--primary">@lang('Return')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
<script>
    'use strict';

    (function ($) {
        $('.releaseBtn').on('click', function () {
            var modal = $('#releaseModal');
            modal.modal('show');
        });

        $('.returnBtn').on('click', function () {
            var modal = $('#returnModal');
            modal.modal('show');
        });

        document.querySelector('.chat-box__thread').scrollTop = document.querySelector('.chat-box__thread').scrollHeight;
    })(jQuery);
</script>
@endpush

