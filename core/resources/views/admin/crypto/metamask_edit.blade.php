@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form method="POST" action="{{route('admin.crypto.metamask.update',$metamaskConf->id)}}" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="form-row">
                            <div class="col-lg-7 col-xl-8">
                                <div class="row">
                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label class="form-control-label font-weight-bold">@lang('Chain Id') <span class="text-danger">*</span></label>
                                            <input type="number"class="form-control check-length" data-length="10" placeholder="@lang('1, 57')" value="{{$metamaskConf->chain_id}}" name="chain_id" required>
                                            <span class="remaining float-right"></span>
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label class="form-control-label font-weight-bold">@lang('Name') <span class="text-danger">*</span></label>
                                            <input type="text"class="form-control check-length" data-length="40" placeholder="@lang('Bitcoin, Lightcoin')" value="{{$metamaskConf->name}}" name="name" required>
                                            <span class="remaining float-right"></span>
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label class="form-control-label font-weight-bold">@lang('Wallet Address') <span class="text-danger">*</span></label>
                                            <label class="form-control-label"><small>Your Metamask wallet address all funds will go here.</small></label>
                                            <input type="text"class="form-control" placeholder="@lang('BTC, LTC')" value="{{$metamaskConf->wallet_address}}" name="wallet_address" required>
                                            <span class="remaining float-right"></span>
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label class="form-control-label font-weight-bold">@lang('Gas Limit') <span class="text-danger">*</span></label>
                                            <label class="form-control-label"><small>This is Gas Limit of how much you will pay fees for transaction.</small></label>
                                            <input type="text"class="form-control" placeholder="21000" value="{{$metamaskConf->gas_limit}}" name="gas_limit" required>
                                            <span class="remaining float-right"></span>
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label class="form-control-label font-weight-bold">@lang('Network Confirmations') <span class="text-danger">*</span></label>
                                            <label class="form-control-label"><small>This is confirmations count from miners to check authenticity of transaction.</small></label>
                                            <input type="text"class="form-control" placeholder="10" value="{{$metamaskConf->network_confirmations}}" name="network_confirmations" required>
                                            <span class="remaining float-right"></span>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="form-control-label font-weight-bold">@lang('Status')</label>
                                            <input id="edit-status" type="checkbox" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-toggle="toggle" data-on="@lang('Active')" data-off="@lang('Disabled')" name="status">
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

            var nameLength = $('input[name=name]').val().length;
            
            @if($metamaskConf->status == 1)
                $('#edit-status').parent('div').removeClass('off');
                $('#edit-status').prop('checked', true);
            @else
                $('#edit-status').parent('div').addClass('off');
                $('#edit-status').prop('checked', false);
            @endif

            $('.check-length').on('input', function(){
                let maxLength = $(this).data('length');
                let currentLength = $(this).val().length;

                let remain = maxLength - currentLength;
                let result =  `${remain} characters remaining`;
                let remainElement = $(this).parent('.form-group').find('.remaining');

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

