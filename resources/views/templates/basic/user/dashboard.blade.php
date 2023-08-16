@extends($activeTemplate.'layouts.frontend')
@section('content')
    @include($activeTemplate.'partials.breadcrumb')
    @php
        $widgetContent = getContent('widget_bg.content',true)
    @endphp
    <section class="pt-120 pb-120 section--bg">
        <div class="container">
          <div class="row">
            <div class="col-xl-3 col-lg-4">
              <div class="profile-sidebar">
                <div class="profile-sidebar__widget border--base">
                  <div class="profile-author">
                    <div class="thumb">
                      <img src="{{ getImage(imagePath()['profile']['user']['path'].'/'.$user->image)}}" alt="image">
                    </div>
                    <div class="content text-center">
                      <h5>{{$user->username}}</h5>
                    </div>
                    <a href="{{route('user.profile.setting')}}" class="cmn-btn d-block text-center btn-md mt-4">@lang('Profile Settings')</a>
                    <a href="{{route('user.advertisement.index')}}" class="border-btn d-block text-center btn-md mt-3">@lang('Advertisements')</a>
                  </div>
                </div><!-- profile-sidebar__widget end -->

                @if ($general->ev || $general->sv)
                    <div class="profile-sidebar__widget border--base">
                        <h4 class="profile-sidebar__title">@lang('Verifications')</h4>
                        <ul class="profile-verify-list">
                            @if ($general->ev)
                                <li class="@if($user->ev) verified @else unverified @endif"><i class="far fa-envelope"></i> @lang('Email Verified')</li>
                            @endif

                            @if ($general->sv)
                                <li class="@if($user->sv) verified @else unverified @endif"><i class="fas fa-mobile-alt"></i> @lang('SMS Verified')</li>
                            @endif
                        </ul>
                    </div><!-- profile-sidebar__widget end -->
                @endif

                <div class="profile-sidebar__widget border--base">
                  <h4 class="profile-sidebar__title">@lang('Informations')</h4>
                  <ul class="profile-info-list">
                    <li>
                      <span class="caption">@lang('Joined At')</span>
                      <span class="value">{{showDateTime($user->created_at, 'F Y')}}</span>
                    </li>
                    <li>
                      <span class="caption">@lang('Advertisements')</span>
                      <span class="value">{{$totalAdd}}</span>
                    </li>
                    <li>
                      <span class="caption">@lang('Trades Requests')</span>
                      <span class="value">{{$totalTradeReq}}</span>
                    </li>
                    <li>
                      <span class="caption">@lang('Completed Trade') </span>
                      <span class="value">{{$completedTrade}}</span>
                    </li>
                  </ul>
                </div><!-- profile-sidebar__widget end -->
              </div><!-- profile-sidebar end -->
            </div>
            <div class="col-xl-9 col-lg-8 mt-lg-0 mt-5">
              <div class="row">
                  @foreach ($wallets as $item)
                    <div class="col-xl-6 col-lg-12 col-md-6 mb-30 d-widget-item">
                        <a class="d-block" href="{{route('user.transaction.single',[$item->crypto->id,$item->crypto->code])}}">
                            <div class="d-widget bg_img" data-background="{{ getImage('assets/images/frontend/widget_bg/'.@$widgetContent->data_values->image,'990x290') }}">
                              <div class="d-widget__icon">{{__($item->crypto->symbol)}}</div>
                              <div class="d-widget__content">
                                <p class="d-widget__caption">{{__($item->crypto->code)}}</p>
                                <h2 class="d-widget__amount">{{showAmount($item->balance,8)}}</h2>
                              </div>
                            </div><!-- d-widget end -->
                        </a>
                    </div>
                  @endforeach
              </div>

              <h4 class="mb-3">@lang('Latest Advertisements')</h4>

              <div class="custom--card">
                <div class="card-body p-0">
                  <div class="table-responsive table-responsive--lg">
                    <table class="table custom--table">
                      <thead>
                        <tr>
                            <th>@lang('Type')</th>
                            <th>@lang('Currency')</th>
                            <th>@lang('Payment Method')</th>
                            <th>@lang('Margin')</th>
                            <th>@lang('Rate')</th>
                            <th>@lang('Payment Window')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Action')</th>
                        </tr>
                      </thead>
                      <tbody>
                            @forelse ($latestAdds as $item)
                                <tr>
                                    <td data-label="@lang('Type')">
                                        @if ($item->type == 1)
                                            <span class="badge text-white badge--released">@lang('Buy')</span>
                                        @elseif($item->type == 2)
                                            <span class="badge text-white badge--fund">@lang('Sell')</span>
                                        @else
                                        @endif
                                    </td>
                                    <td data-label="@lang('Currency')">{{__($item->fiat->code)}}</td>
                                    <td data-label="@lang('Payment Method')">{{__($item->fiatGateway->name)}}</b></td>
                                    <td data-label="@lang('Margin')">{{$item->margin}} %</td>
                                    <td data-label="@lang('Rate')">{{rate($item->type, $item->crypto->rate, $item->fiat->rate, $item->margin)}} {{__($item->fiat->code)}}/ {{__($item->crypto->code)}}</td>
                                    <td data-label="@lang('Payment Window')">{{$item->window}} @lang('Minutes')</td>
                                    <td data-label="@lang('Status')">
                                        @if ($item->status == 1)
                                            <span class="badge text-white badge--released">@lang('Open')</span>
                                        @else
                                            <span class="badge text-white badge--cancel">@lang('Closed')</span>
                                        @endif
                                    </td>
                                    <td data-label="@lang('Action')"><a href="{{route('user.advertisement.edit',$item->id)}}" class="cmn-btn btn-sm"><i class="las la-edit"></i></a></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="100%" class="text-center">{{__($emptyMessage)}}</td>
                                </tr>
                            @endforelse
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>
    </section>
@endsection
