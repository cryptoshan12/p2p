@extends('admin.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 col-md-12 mb-30">
            <div class="card">
                <div class="card-body">
                    <form action="" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <h4>@lang('Cron Setting')</h4>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label class="form-control-label font-weight-bold"> @lang('Fiat Currency Rate Api Key') </label>
                                    ( <small>@lang('For the api key please visit :')
                                        <a target="_blank" class="text--info" href="https://currencylayer.com/">@lang('Currency Layer')</a>
                                     </small> )
                                    <input class="form-control form-control-lg" type="text" placeholder="@lang('Fiat Currency Rate Api Key')" name="fiat_api_key" value="{{$general->fiat_api_key}}">

                                    <div class="form-group">
                                        <small class="font-weight-bold">@lang('Set up cron job for update fiat price rate :')</small>
                                        <small class="text--danger">{{route('cron.fiat.rate')}}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label class="form-control-label font-weight-bold"> @lang('Crypto Currency Rate Api Key') </label>
                                    ( <small>@lang('For the api key please visit :')
                                        <a target="_blank" class="text--info" href="https://coinmarketcap.com/">@lang('CoinMarketCap')</a>
                                    </small> )
                                    <input class="form-control form-control-lg" type="text" placeholder="@lang('Crypto Currency Rate Api Key')" name="crypto_api_key" value="{{$general->crypto_api_key}}">

                                    <div class="form-group">
                                        <small class="font-weight-bold">@lang('Set up cron job for update crypto price rate :')</small>
                                        <small class="text--danger">{{route('cron.crypto.rate')}}</small>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-4 mt-4">
                                <h4>@lang('Coin Payment Setting')</h4>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label class="form-control-label font-weight-bold"> @lang('Public Key') <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-lg" name="public_key" value="{{$general->public_key}}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label class="form-control-label font-weight-bold"> @lang('Private Key') <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-lg" name="private_key" value="{{$general->private_key}}" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group ">
                                    <label class="form-control-label font-weight-bold"> @lang('Merchant ID') <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-lg" name="merchant_id" value="{{$general->merchant_id}}" required>
                                </div>
                            </div>
                        </div>
                        {{-- <div class="row mt-4">
                            <div class="col-md-3">
                                <div class="form-group ">
                                    <label class="form-control-label font-weight-bold"> @lang('Site Title') </label>
                                    <input class="form-control form-control-lg" type="text" name="sitename" value="{{$general->sitename}}">
                                </div>
                            </div>

                            <div class="form-group col-md-3">
                                <label class="form-control-label font-weight-bold"> @lang('Timezone')</label>
                                <select class="select2-basic" name="timezone">
                                    @foreach($timezones as $timezone)
                                    <option value="'{{ @$timezone}}'" @if(config('app.timezone') == $timezone) selected @endif>{{ __($timezone) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-3">
                                <label class="form-control-label font-weight-bold"> @lang('Site Base Color')</label>
                                <div class="input-group">
                                <span class="input-group-addon ">
                                    <input type='text' class="form-control form-control-lg colorPicker" value="{{$general->base_color}}"/>
                                </span>
                                    <input type="text" class="form-control form-control-lg colorCode" name="base_color" value="{{ $general->base_color }}"/>
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label class="form-control-label font-weight-bold"> @lang('Site Secondary Color')</label>
                                <div class="input-group">
                                <span class="input-group-addon">
                                    <input type='text' class="form-control form-control-lg colorPicker" value="{{$general->secondary_color}}"/>
                                </span>
                                    <input type="text" class="form-control form-control-lg colorCode" name="secondary_color" value="{{ $general->secondary_color }}"/>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-3">
                                <label class="form-control-label font-weight-bold">@lang('Force Secure Password')</label>
                                <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-toggle="toggle" data-on="@lang('Enable')" data-off="@lang('Disabled')" name="secure_password" @if($general->secure_password) checked @endif>
                            </div>
                            <div class="form-group col-md-3">
                                <label class="form-control-label font-weight-bold">@lang('Agree policy')</label>
                                <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-toggle="toggle" data-on="@lang('Enable')" data-off="@lang('Disabled')" name="agree" @if($general->agree) checked @endif>
                            </div>
                            <div class="form-group col-md-3">
                                <label class="form-control-label font-weight-bold">@lang('User Registration')</label>
                                <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-toggle="toggle" data-on="@lang('Enable')" data-off="@lang('Disabled')" name="registration" @if($general->registration) checked @endif>
                            </div>

                            <div class="form-group col-md-3">
                                <label class="form-control-label font-weight-bold">@lang('Force SSL')</label>
                                <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-toggle="toggle" data-on="@lang('Enable')" data-off="@lang('Disabled')" name="force_ssl" @if($general->force_ssl) checked @endif>
                            </div>
                            <div class="form-group col-md-3">
                                <label class="form-control-label font-weight-bold"> @lang('Email Verification')</label>
                                <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-toggle="toggle" data-on="@lang('Enable')" data-off="@lang('Disable')" name="ev" @if($general->ev) checked @endif>
                            </div>
                            <div class="form-group col-md-3">
                                <label class="form-control-label font-weight-bold">@lang('Email Notification')</label>
                                <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-toggle="toggle" data-on="@lang('Enable')" data-off="@lang('Disable')" name="en" @if($general->en) checked @endif>
                            </div>
                            <div class="form-group col-md-3">
                                <label class="form-control-label font-weight-bold"> @lang('SMS Verification')</label>
                                <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-toggle="toggle" data-on="@lang('Enable')" data-off="@lang('Disable')" name="sv" @if($general->sv) checked @endif>
                            </div>
                            <div class="form-group col-md-3">
                                <label class="form-control-label font-weight-bold">@lang('SMS Notification')</label>
                                <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-toggle="toggle" data-on="@lang('Enable')" data-off="@lang('Disable')" name="sn" @if($general->sn) checked @endif>
                            </div>
                        </div> --}}

                        <div class="form-group">
                            <button type="submit" class="btn btn--primary btn-block btn-lg">@lang('Update')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

