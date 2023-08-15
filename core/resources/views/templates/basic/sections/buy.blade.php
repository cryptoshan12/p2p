@php
    $buyContent = getContent('buy.content',true);
    $cryptos = App\Models\Crypto::where('status', 1)->get();
@endphp
<section class="pt-120 pb-120 bg_img" data-background="{{ getImage('assets/images/frontend/buy/'.@$buyContent->data_values->image,'1920x1340') }}">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-6">
          <div class="section-header text-center">
            <h2 class="section-title">{{__(@$buyContent->data_values->heading)}}</h2>
            <p>{{__(@$buyContent->data_values->sub_heading)}}</p>
          </div>
        </div>
      </div><!-- row end -->

      <div class="row">
        <div class="col-lg-12">
            <ul class="nav nav-tabs custom--style-two justify-content-center" id="myTab" role="tablist">
                @foreach ($cryptos as $item)
                    <li class="nav-item goto-more-buy" role="presentation">
                        <a class="nav-link crypto-currency-buy @if($loop->first) active @endif" data-id="{{$item->id}}" data-code="{{$item->code}}" id="{{$item->code}}-tab" data-toggle="tab" href="#{{$item->code}}" role="tab">{{__($item->code)}}</a>
                    </li>
                @endforeach
            </ul>


            <div class="tab-content mt-4" id="myTabContent">

                @foreach ($cryptos as $item)
                    <div class="tab-pane bg-transparent fade content-load-buy @if($loop->first) show active @endif" id="{{$item->code}}" role="tabpanel" aria-labelledby="{{$item->code}}-tab">

                    </div>
                @endforeach
            </div>
            <div class="btn-group mt-4 justify-content-center">
                <form action="{{route('advertisement.search')}}" method="GET" class="buy-submit">
                    <input type="hidden" name="type" value="buy">
                    <input type="hidden" name="crypto_code">
                    <button id="buy-more" class="cmn-btn2 btn-sm px-4 py-2">@lang('More')</button>
                </form>
            </div>
        </div>
      </div>
    </div>
</section>

@push('script')
    <script>
        'use strict';

        (function ($) {
            var cryptoCount = '{{count($cryptos)}}';

            if (parseInt(cryptoCount) > 0) {
                var cryptoId = '{{ @$cryptos[0]->id }}';

                $.ajax({
                    type: "get",
                    url: "{{route('advertisement.currency.buy')}}",
                    data: {id:parseInt(cryptoId)},
                    dataType: "json",

                    success: function (response) {
                        if(response.html){
                            $('.content-load-buy').html(response.html);
                        }else{
                            notify('error', response.error);
                        }
                    }
                });
            }

            $('.crypto-currency-buy').on('click', function () {
                var id = $(this).data('id');

                $.ajax({
                    type: "get",
                    url: "{{route('advertisement.currency.buy')}}",
                    data: {id:id},
                    dataType: "json",

                    success: function (response) {
                        if(response.html){
                            $('.content-load-buy').html(response.html);
                        }else{
                            notify('error', response.error);
                        }
                    }
                });
            });

            $('#buy-more').on('click', function () {
                if ($('.crypto-currency-buy').hasClass('active')) {
                    var cryptoCode = $('.goto-more-buy').find('.active').data('code');
                    $('.buy-submit').find('input[name="crypto_code"]').val(cryptoCode);
                }
            });

        })(jQuery);
    </script>
@endpush
