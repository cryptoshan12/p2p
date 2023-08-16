@extends($activeTemplate.'layouts.frontend')
@section('content')
    @include($activeTemplate.'partials.breadcrumb')

    <section class="pt-120 pb-120">
        <div class="container">
          <div class="row">

                <div class="col-lg-12 text-center mb-4">
                    <h3 class="mb-1">{{$title}}</h3>
                    <h4>{{$title2}}</h4>
                </div>

                <div class="col-lg-6 pl-lg-5 mt-lg-0 mt-5">
                    <div class="chat-box">
                        <div class="chat-box__header">
                            <div class="chat-author">
                                <div class="thumb">
                                    @if ($tradeRequestsDetails->buyer_id == Auth::user()->id)
                                        <img src="{{ getImage(imagePath()['profile']['user']['path'].'/'. $tradeRequestsDetails->seller->image,imagePath()['profile']['user']['size']) }}" alt="image">
                                    @elseif ($tradeRequestsDetails->seller_id == Auth::user()->id)
                                        <img src="{{ getImage(imagePath()['profile']['user']['path'].'/'. $tradeRequestsDetails->buyer->image,imagePath()['profile']['user']['size']) }}" alt="image">
                                    @endif
                                </div>

                                <div class="content">
                                    @if ($tradeRequestsDetails->buyer_id == Auth::user()->id)
                                        <h6 class="name">{{__($tradeRequestsDetails->seller->fullname)}}</h6>
                                    @elseif ($tradeRequestsDetails->seller_id == Auth::user()->id)
                                        <h6 class="name">{{__($tradeRequestsDetails->buyer->fullname)}}</h6>
                                    @endif
                                </div>
                            </div>
                            <div class="trade-status">
                                <a href="javascript:void(0)" class="badge text-white badge--paid refresh" data-toggle="tooltip" data-placement="top"
                                title="@lang('Click this to load new chat')"><i class="las la-sync-alt"></i> @lang('Refresh')</a>
                            </div>
                        </div>

                        <div class="chat-box__thread" id="chatajax">

                            @foreach ($tradeRequestsDetails->chats()->get() as $item)
                                @if ($item->user_id == $tradeRequestsDetails->buyer_id)
                                    <div class="single-message clearfix message--left">
                                        <div class="message-author">
                                            <img src="{{ getImage(imagePath()['profile']['user']['path'].'/'. $tradeRequestsDetails->buyer->image,imagePath()['profile']['user']['size']) }}" alt="image" class="thumb">
                                            <h6 class="name">{{__($tradeRequestsDetails->buyer->fullname)}}</h6>
                                        </div>
                                        <span class="message-time">{{$item->created_at->diffForHumans()}}</span>
                                        <p class="message-text">{{__($item->message)}}.</p>

                                        @if ($item->file)
                                            <div class="messgae-attachment">
                                                <b class="text-sm d-block mb-2"> @lang('Attachment') </b>
                                                    <a href="{{route('user.download',[$tradeRequestsDetails->id,$item->id])}}" class="file-demo-btn">
                                                    {{__($item->file)}}
                                                </a>
                                            </div>
                                        @endif

                                    </div><!-- single-message end -->
                                @endif

                                @if ($item->user_id == $tradeRequestsDetails->seller_id)
                                    <div class="single-message clearfix message--right">
                                        <div class="message-author">
                                            <img src="{{ getImage(imagePath()['profile']['user']['path'].'/'. $tradeRequestsDetails->seller->image,imagePath()['profile']['user']['size']) }}" alt="image" class="thumb">
                                            <h6 class="name">{{__($tradeRequestsDetails->seller->fullname)}}</h6>
                                        </div>
                                        <span class="message-time">{{$item->created_at->diffForHumans()}}</span>
                                        <p class="message-text">{{__($item->message)}}</p>

                                        @if ($item->file)
                                            <div class="messgae-attachment">
                                                <b class="text-sm d-block mb-2"> @lang('Attachment') </b>
                                                    <a href="{{route('user.download',[$tradeRequestsDetails->id,$item->id])}}" class="file-demo-btn">
                                                    {{__($item->file)}}
                                                </a>
                                            </div>
                                        @endif
                                    </div><!-- single-message end -->
                                @endif

                                @if ($item->admin)
                                    <div class="single-message clearfix message--right">
                                        <div class="message-author">
                                            <img src="{{ getImage(imagePath()['logoIcon']['path'] .'/favicon.png') }}" alt="image" class="thumb">
                                            <h6 class="name">@lang('System')</h6>
                                        </div>
                                        <span class="message-time">{{$item->created_at->diffForHumans()}}</span>
                                        <p class="message-text">{{__($item->message)}}</p>

                                        @if ($item->file)
                                            <div class="messgae-attachment">
                                                <b class="text-sm d-block mb-2"> @lang('Attachment') </b>
                                                <a href="{{route('user.download',[$tradeRequestsDetails->id,$item->id])}}" class="file-demo-btn">
                                                    {{__($item->file)}}
                                                </a>
                                            </div>
                                        @endif
                                    </div><!-- single-message end -->
                                @endif
                            @endforeach

                        </div><!-- chat-box__thread end -->
                        
                        
                        
                        @if (($tradeRequestsDetails->status == 0) || ($tradeRequestsDetails->status == 2) || ($tradeRequestsDetails->status == 8))
                            <div class="chat-box__footer">
                                <form action="{{route('user.trade.request.chat.store',$tradeRequestsDetails->id)}}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="chat-send-area">
                                        <div class="chat-send-field">
                                            <textarea name="message" id="chat-message-field" placeholder="@lang('Type here')" class="form-control" required></textarea>
                                        </div>
                                        <div class="chat-send-btn">
                                            <button type="sbumit" class="cmn-btn btn-md"><i class="far fa-paper-plane"></i></button>
                                        </div>
                                        <div class="chat-send-file">
                                            <div class="position-relative">
                                                <input type="file" id="upload-field" name="file" class="custom-file" accept=".jpg , .png, ,jpeg .pdf">
                                                <label for="upload-field">@lang('File Upload')</label>
                                            </div>
                                        </div>
                                    </div>

                                </form>
                            </div>
                        @endif
                    </div><!-- chat-box end -->
                </div>

                <div class="col-lg-6 mt-lg-0 mt-4">
                    <div class="card style--two">
                        <div class="card-header d-flex justify-content-between">
                            <h5>@lang('Trade Details')</h5>
                            <div class="trade-status">
                                @if ($tradeRequestsDetails->status == 0)
                                    <span class="badge text-white badge--pending">@lang('Escrow Funded')</span>
                                @elseif($tradeRequestsDetails->status == 9)
                                    <span class="badge text-white badge--cancel">@lang('Canceled')</span>
                                @elseif($tradeRequestsDetails->status == 2)
                                    <span class="badge text-white badge--paid">@lang('Buyer Paid')</span>
                                @elseif($tradeRequestsDetails->status == 8)
                                    <span class="badge text-white badge--reported">@lang('Reported')</span>
                                @elseif($tradeRequestsDetails->status == 1)
                                    <span class="badge text-white badge--completed">@lang('Completed')</span>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">

                            @if (Auth::user()->id == $tradeRequestsDetails->buyer_id)
                                @include($activeTemplate.'user.trade.buyer')
                            @endif

                            @if (Auth::user()->id == $tradeRequestsDetails->seller_id)
                                @include($activeTemplate.'user.trade.seller')
                            @endif

                            <h6><i class="las la-question-circle"></i> @lang('Instructions to be followed')</h6>

                            <div class="accordion cmn-accordion accordion-arrow mt-3" id="accordionExample">
                                <div class="card shadow-none">
                                    <div class="card-header" id="headingTwo">
                                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                            <p class="text-dark">@lang('Terms of trade')</p>
                                        </button>
                                    </div>
                                    <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
                                        <div class="card-body border-top-0">
                                            <p>{{__($tradeRequestsDetails->advertisement->terms)}}</p>
                                        </div>
                                    </div>
                                </div><!-- card end -->

                                <div class="card shadow-none">
                                    <div class="card-header" id="headingThree">
                                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                            <p class="text-dark">@lang('Payment details')</p>
                                        </button>
                                    </div>
                                    <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample">
                                        <div class="card-body border-top-0">
                                            <p>{{__($tradeRequestsDetails->advertisement->details)}}</p>
                                        </div>
                                    </div>
                                </div><!-- card end -->
                            </div>

                        </div>
                    </div>
                </div>

          </div>
        </div>
    </section>
    
@endsection

@push('script')
    <script>
        var uid =  '{{$tradeRequestsDetails->uid}}';
        function getchats(){

            $.ajax({
                url: '/user/chats/ajax',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: '{{ $tradeRequestsDetails->uid }}',
                },
                dataType: 'JSON',
                success: function (data) {
                   var first = "";
                   $('#chatajax').html('');
                    for (let i = 0; i < data.length; i++) {
                        
                        if(data[i]['user_id'] ==data[i]['buyer_id']){
 first+='   <div class="single-message clearfix message--left"><div class="message-author"><img src="'+data[i]['buyer_image']+'" alt="image" class="thumb"><h6 class="name">'+data[i]["buyer_fullname"]+'</h6></div><span class="message-time">'+data[i]["created_at_diff"]+'</span><p class="message-text">'+data[i]["message"]+'.</p>';
if(data[i]['file']){
    first+='<div class="messgae-attachment"><b class="text-sm d-block mb-2"> Attachment </b><a href="/user/file-download/'+data[i]["trade_id"]+'/'+data[i]["id"]+'" class="file-demo-btn">'+data[i]["file"]+'</a></div>'
}
first += '</div>' ;

                        }
                        if(data[i]['user_id'] ==data[i]['seller_id']){
 first+='   <div class="single-message clearfix message--right"><div class="message-author"><img src="'+data[i]['seller_image']+'" alt="image" class="thumb"><h6 class="name">'+data[i]["seller_fullname"]+'</h6></div><span class="message-time">'+data[i]["created_at_diff"]+'</span><p class="message-text">'+data[i]["message"]+'.</p>';
if(data[i]['file']){
    first+='<div class="messgae-attachment"><b class="text-sm d-block mb-2"> @lang("Attachment")  </b><a href="/user/file-download/'+data[i]["trade_id"]+'/'+data[i]["id"]+'" class="file-demo-btn">'+data[i]["file"]+'</a></div>'
}
first += '</div>' ;

                        }
                        if(data[i]['admin']){
 first+='   <div class="single-message clearfix message--right"><div class="message-author"><img src="{{ getImage(imagePath()['logoIcon']['path'] .'/favicon.png') }}" alt="image" class="thumb"><h6 class="name">@lang("System")</h6></div><span class="message-time">'+data[i]["created_at_diff"]+'</span><p class="message-text">'+data[i]["message"]+'.</p>';
if(data[i]['file']){
    first+='<div class="messgae-attachment"><b class="text-sm d-block mb-2"> @lang("Attachment") </b><a href="/user/file-download/'+data[i]["trade_id"]+'/'+data[i]["id"]+'" class="file-demo-btn">'+data[i]["file"]+'</a></div>'
}
first += '</div>' ;

                        }
                        //$('#chatajax').html("");
                        
                    }
                    $('#chatajax').append(first);

                }
            });
        }

        //getchats();
        setInterval(function(){
  getchats();

 }, 5000);
    </script>
    <script>
        (function($) {
            "use strict";
            $('.refresh').on('click', function(){
                location.reload();
            });

            document.querySelector('.chat-box__thread').scrollTop = document.querySelector('.chat-box__thread').scrollHeight;
        })(jQuery);
    </script>
@endpush
