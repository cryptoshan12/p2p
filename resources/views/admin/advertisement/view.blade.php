@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form method="POST" action="{{route('admin.ad.update',$advertisement->id)}}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-control-label font-weight-bold">@lang('Type')</label>
                                            <input type="text"class="form-control" value="@if($advertisement->type == 1) @lang('Buy') @else @lang('Sell') @endif" disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-control-label font-weight-bold">@lang('Crypto Currency')</label>
                                            <input type="text"class="form-control" value="{{__($advertisement->crypto->name)}}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-control-label font-weight-bold">@lang('Payment Methos')</label>
                                            <input type="text"class="form-control" value="{{__($advertisement->fiatGateway->name)}}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-control-label font-weight-bold">@lang('Fiat Currency')</label>
                                            <input type="text"class="form-control" value="{{__($advertisement->fiat->name)}}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="w-100 font-weight-bold">@lang('Margin')</label>
                                        <div class="input-group has_append">
                                            <input class="form-control" type="text" value="{{__($advertisement->margin)}}" readonly>
                                            <div class="input-group-append">
                                                <div class="input-group-text"><span>%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="w-100 font-weight-bold">@lang('Payment Window')</label>
                                        <div class="input-group has_append">
                                            <input class="form-control" type="text" value="{{__($advertisement->window)}}" readonly>
                                            <div class="input-group-append">
                                                <div class="input-group-text"><span>@lang('Minutes')</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="w-100 font-weight-bold">@lang('Minimum')</label>
                                            <div class="input-group has_append">
                                                <input class="form-control" type="text" value="{{showAmount($advertisement->min)}}" readonly>
                                                <div class="input-group-append">
                                                    <div class="input-group-text"><span>{{__($advertisement->fiat->name)}}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="w-100 font-weight-bold">@lang('Maximum')</label>
                                            <div class="input-group has_append">
                                                <input class="form-control" type="text" value="{{showAmount($advertisement->max)}}" readonly>
                                                <div class="input-group-append">
                                                    <div class="input-group-text"><span>{{__($advertisement->fiat->name)}}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="w-100 font-weight-bold">@lang('Rate')</label>
                                            <div class="input-group has_append">
                                                <input class="form-control" type="text" value="{{rate($advertisement->type,$advertisement->crypto->rate,$advertisement->fiat->rate,$advertisement->margin)}}" readonly>
                                                <div class="input-group-append">
                                                    <div class="input-group-text"><span>{{__($advertisement->fiat->name)}}/{{__($advertisement->crypto->name)}}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-control-label font-weight-bold">@lang('Payment Details')</label>
                                            <textarea class="form-control" rows="8" readonly>{{__($advertisement->details)}}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-control-label font-weight-bold">@lang('Terms of Trade')</label>
                                            <textarea class="form-control" rows="8" readonly>{{__($advertisement->terms)}}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="form-control-label font-weight-bold">@lang('Status')</label>
                                            <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-toggle="toggle" data-on="@lang('Active')" data-off="@lang('Disabled')" name="status" @if($advertisement->status) checked @endif>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn--primary btn-block">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{route('admin.ad.index')}}" class="btn btn-sm btn--primary box--shadow1 text--small"><i class="la la-fw la-backward"></i> @lang('Go Back') </a>
@endpush
