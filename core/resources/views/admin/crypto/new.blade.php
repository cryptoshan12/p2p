@extends('admin.layouts.app')
@section('panel')

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form method="POST" action="{{route('admin.crypto.store')}}" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="form-row">
                            <div class="col-lg-5 col-xl-4">
                                <div class="form-group">
                                    <h5>@lang('Image')</h5>
                                    <div class="image-upload mt-2">
                                        <div class="thumb">
                                            <div class="avatar-preview">
                                                <div class="profilePicPreview" style="background-image: url({{ getImage('',imagePath()['crypto']['size'])}})">
                                                    <button type="button" class="remove-image"><i class="fa fa-times"></i></button>
                                                </div>
                                            </div>
                                            <div class="avatar-edit">
                                                <input type="file" class="profilePicUpload" name="image" id="profilePicUpload1" accept=".png, .jpg, .jpeg" required>
                                                <label for="profilePicUpload1" class="bg--success"> @lang('Image')</label>

                                                <small class="mt-2 text-facebook">@lang('Supported files'): <b>@lang('jpeg, jpg, png')</b>.
                                                @lang('Image Will be resized to'): <b>{{imagePath()['crypto']['size']}}</b> @lang('px')

                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-7 col-xl-8">
                                <div class="row">
                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label class="form-control-label font-weight-bold">@lang('Name') <span class="text-danger">*</span></label>
                                            <input type="text"class="form-control check-length" data-length="40" placeholder="@lang('e.g. Bitcoin, Lightcoin')" value="{{ old('name') }}" name="name" required>
                                            <span class="remaining float-right"></span>
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label class="form-control-label font-weight-bold">@lang('Code') <span class="text-danger">*</span></label>
                                            <input type="text"class="form-control check-length" data-length="10" placeholder="@lang('e..g. BTC, LTC')" value="{{ old('code') }}" name="code" required>
                                            <span class="remaining float-right"></span>
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label class="form-control-label font-weight-bold">@lang('Symbol') <span class="text-danger">*</span></label>
                                            <input type="text"class="form-control check-length" data-length="5" placeholder="@lang('e.g. ₿, Ł')" value="{{ old('symbol') }}" name="symbol" required>
                                            <span class="remaining float-right"></span>
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <label class="w-100 font-weight-bold">@lang('Rate') <span class="text-danger">*</span></label>
                                        <div class="input-group has_append">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text"> <span>1&nbsp;</span> <span class="currency-symbol"></span> &nbsp; = </div>
                                            </div>
                                            <input class="form-control" type="number" step="any" name="rate" placeholder="0" value="{{ old('rate') }}" required>
                                            <div class="input-group-append">
                                                <div class="input-group-text"><span>@lang('USD')</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mt-2">
                                        <h5 class="text-center my-3">@lang('Deposit Charges')</h5>

                                        <div class="row">
                                            <div class="input-group mb-3 col-xl-6">
                                                <label class="form-control-label font-weight-bold">@lang('Fixed Charge') <span class="text-danger">*</span></label>
                                                <div class="input-group has_append">
                                                    <input class="form-control" type="number" step="any" name="dc_fixed" placeholder="0" required>
                                                    <div class="input-group-append">
                                                        <div class="input-group-text"><span class="currency-symbol"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="input-group mb-3 col-xl-6">
                                                <label class="form-control-label font-weight-bold">@lang('Percentage Charge') <span class="text-danger">*</span></label>

                                                <div class="input-group has_append">
                                                    <input class="form-control" type="number" step="0.01" max="100" name="dc_percent" placeholder="0" required>
                                                    <div class="input-group-append">
                                                        <div class="input-group-text"><span>%</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mt-2">
                                        <h5 class="text-center mb-3">@lang('Withdrawal Charges')</h5>
                                        <div class="row">
                                            <div class="input-group mb-3 col-xl-6">
                                                <label class="form-control-label font-weight-bold">@lang('Fixed Charge') <span class="text-danger">*</span></label>
                                                <div class="input-group has_append">
                                                    <input class="form-control" type="number" step="any" name="wc_fixed" placeholder="0" required>
                                                    <div class="input-group-append">
                                                        <div class="input-group-text"><span class="currency-symbol"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="input-group mb-3 col-xl-6">
                                                <label class="form-control-label font-weight-bold">@lang('Percentage Charge') <span class="text-danger">*</span></label>

                                                <div class="input-group has_append">
                                                    <input class="form-control" type="number" step="0.01" max="100" name="wc_percent" placeholder="0" required>
                                                    <div class="input-group-append">
                                                        <div class="input-group-text"><span>%</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-md-12 mt-2">
                                        <h5 class="text-center mb-3">@lang('Currency Option')</h5>
                                        
                                        <div class="row">
                                            <div class="input-group mb-3 col-xl-6">
                                                <label class="form-control-label font-weight-bold">@lang('Is Currency from MetaMask ?') <span class="text-danger"></span></label>
                                                <div class="input-group has_append">
                                                    <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-toggle="toggle" data-on="@lang('Enable')" data-off="@lang('Disable')" name="isMetaMask" >
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="input-group mb-3 col-xl-6">
                                                <label class="form-control-label font-weight-bold">@lang('BSC Address') <span class="text-danger"></span></label>
                                                <div class="input-group has_append">
                                                    <input type="text"class="form-control " placeholder="BSC Address" value="" name="bsc_address">
                                                </div>
                                            </div>
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
    <a href="{{route('admin.crypto.index')}}" class="btn btn-sm btn--primary box--shadow1 text--small"><i class="la la-fw la-backward"></i> @lang('Go Back') </a>
@endpush

@push('script')
    <script>
        (function ($) {
            "use strict";

            $('input[name=code]').on('input', function () {
                $('.currency-symbol').text($(this).val());
            });

            $('.check-length').on('input', function(){
                let maxLength       = $(this).data('length');
                let currentLength   = $(this).val().length;
                let remain          = maxLength - currentLength;
                let result          = `${remain} @lang('characters remaining')`;
                let remainElement   = $(this).parent('.form-group').find('.remaining');

                remainElement.css({
                    fontWeight: 'bold',
                    fontSize: '14px',
                    display: 'block',
                    textAlign: 'end',
                });

                if(remain <= 4){
                    remainElement.css('color', 'red');
                }else if(remain <= 20){
                    remainElement.css('color', 'green');
                }else{
                    remainElement.css('color', 'black');
                }

                remainElement.html(`${remain} @lang('characters remaining')`);
            });

            $('.check-length').on('keypress', function(){
                let maxLength = $(this).data('length');
                let currentLength = $(this).val().length;

                if(currentLength >= maxLength){
                    return false;
                }
            });

        })(jQuery);
    </script>

@endpush

