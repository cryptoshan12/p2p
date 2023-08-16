@auth
    @php
        $runningTradeCount = App\Models\TradeRequest::where([['status', '!=', 1],['status', '!=', 9]])->where(function ($q){
            $q->where('buyer_id', auth()->user()->id)->orWhere('seller_id', auth()->user()->id);
        })->count();
    @endphp
@endauth

@php
    $cryptos = App\Models\Crypto::where('status', 1)->get();
@endphp

<header class="header">
    <div class="header__bottom">
      <div class="container">
        <nav class="navbar navbar-expand-xl p-0 align-items-center">
          <a class="site-logo site-title" href="{{route('home')}}"><img src="{{ getImage(imagePath()['logoIcon']['path'] .'/logo.png') }}" alt="site-logo"></a>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="menu-toggle"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav main-menu m-auto">
                @auth
                    @if (request()->routeIs('user*'))
                        <li><a href="{{route('user.home')}}">@lang('Dashboard')</a></li>
                        <li class="menu_has_children"><a href="javascript:void(0)">@lang('Buy')</a>
                            <ul class="sub-menu">
                                @foreach ($cryptos as $item)
                                    <li><a href="{{route('buy.sell',['buy',$item->code])}}">{{$item->code}}</a></li>
                                @endforeach
                            </ul>
                        </li>
                        <li class="menu_has_children"><a href="javascript:void(0)">@lang('Sell')</a>
                            <ul class="sub-menu">
                                @foreach ($cryptos as $item)
                                    <li><a href="{{route('buy.sell',['sell',$item->code])}}">{{$item->code}}</a></li>
                                @endforeach
                            </ul>
                        </li>
                        <li><a href="{{route('user.advertisement.index')}}">@lang('Advertisements')</a></li>
                        <li>
                            <a href="{{route('user.trade.requests')}}">@lang('Trade Requests')</a>
                            <span class="menu-badge">{{__($runningTradeCount)}}</span>
                        </li>
                        <li><a href="{{route('user.wallets')}}">@lang('Wallets')</a></li>
                        <li><a href="{{route('user.transaction.index')}}">@lang('Transactions')</a></li>
                        <li><a href="{{route('ticket')}}">@lang('Support')</a></li>
                    @else

                        <li> <a href="{{route('home')}}">@lang('Home')</a></li>
                        <li class="menu_has_children"><a href="javascript:void(0)">@lang('Buy')</a>
                            <ul class="sub-menu">
                                @foreach ($cryptos as $item)
                                    <li><a href="{{route('buy.sell',['buy',$item->code])}}">{{$item->code}}</a></li>
                                @endforeach
                            </ul>
                        </li>
                        <li class="menu_has_children"><a href="javascript:void(0)">@lang('Sell')</a>
                            <ul class="sub-menu">
                                @foreach ($cryptos as $item)
                                    <li><a href="{{route('buy.sell',['sell',$item->code])}}">{{$item->code}}</a></li>
                                @endforeach
                            </ul>
                        </li>

                        @foreach($pages as $k => $data)
                            <li><a href="{{route('pages',[$data->slug])}}"  class="nav-link">{{__($data->name)}}</a></li>
                        @endforeach
                        <li><a href="{{route('contact')}}">@lang('Contact')</a></li>
                    @endif
                @else
                    <li> <a href="{{route('home')}}">@lang('Home')</a></li>
                    <li class="menu_has_children"><a href="javascript:void(0)">@lang('Buy')</a>
                        <ul class="sub-menu">
                            @foreach ($cryptos as $item)
                                <li><a href="{{route('buy.sell',['buy',$item->code])}}">{{$item->code}}</a></li>
                            @endforeach
                        </ul>
                    </li>
                    <li class="menu_has_children"><a href="javascript:void(0)">@lang('Sell')</a>
                        <ul class="sub-menu">
                            @foreach ($cryptos as $item)
                                <li><a href="{{route('buy.sell',['sell',$item->code])}}">{{$item->code}}</a></li>
                            @endforeach
                        </ul>
                    </li>
                    @foreach($pages as $k => $data)
                        <li><a href="{{route('pages',[$data->slug])}}"  class="nav-link">{{__($data->name)}}</a></li>
                    @endforeach

                    <li><a href="{{route('contact')}}">@lang('Contact')</a></li>
                @endauth

            </ul>
            <div class="nav-right">

                <select class="language-select langSel">
                    <option value="">@lang('Select')</option>
                    @foreach($language as $item)
                        <option value="{{$item->code}}" @if(session('lang') == $item->code) selected  @endif>{{ __($item->name) }}</option>
                    @endforeach

                </select>

                <ul class="account-menu ml-3">
                    @auth
                        @if (request()->routeIs('user*'))
                            <li class="icon"><i class="las la-user"></i>
                            <ul class="account-submenu">
                                    <li><a href="{{route('user.change.password')}}">@lang('Password')</a></li>
                                    <li><a href="{{route('user.profile.setting')}}">@lang('Profile Setting')</a></li>
                                    <li><a href="{{route('user.twofactor')}}">@lang('2FA Security')</a></li>
                                    <li><a href="{{route('user.deposit.history')}}">@lang('Deposit')</a></li>
                                    <li><a href="{{route('user.withdraw.history')}}">@lang('Withdraw')</a></li>
                                    <li><a href="{{route('user.logout')}}">@lang('Logout')</a></li>
                                </ul>
                            </li>
                        @else
                            <li class="icon"><a href="{{route('user.home')}}"><i class="las la-tachometer-alt"></i></a></li>
                        @endif
                    @else
                        <li class="icon"><i class="las la-user"></i>
                            <ul class="account-submenu">
                                <li><a href="{{route('user.login')}}">@lang('Login')</a></li>
                                <li><a href="{{route('user.register')}}">@lang('Registration')</a></li>
                            </ul>
                        </li>
                    @endauth
                </ul>
            </div>
          </div>
        </nav>
      </div>
    </div><!-- header__bottom end -->
</header>
