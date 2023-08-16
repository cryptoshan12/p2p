@extends('admin.layouts.app')

@section('panel')

    <div class="row">
        <div class="col-lg-12 col-md-12 mb-30">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th scope="col">@lang('SL')</th>
                                    <th scope="col">@lang('Name')</th>
                                    <th scope="col">@lang('Code')</th>
                                    <th scope="col">@lang('Symbol')</th>
                                    <th scope="col">@lang('Rate')</th>
                                    <th scope="col">@lang('Status')</th>
                                    <th scope="col">@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody class="list">
                                @forelse ($fiats as $item)
                                    <tr>
                                        <td data-label="@lang('SL')">{{ $fiats->firstItem() + $loop->index }}</td>
                                        <td data-label="@lang('Name')">{{ __($item->name) }}</td>
                                        <td data-label="@lang('Code')">{{__($item->code)}}</td>
                                        <td data-label="@lang('Symbol')">{{$item->symbol}}</td>
                                        <td data-label="@lang('Rate')">
                                            <span class="font-weight-bold">{{showAmount($item->rate)}} {{__($item->code)}}</span> / @lang('USD')
                                        </td>
                                        <td data-label="@lang('Status')">
                                            @if ($item->status == 1)
                                                <span class="badge badge--primary">@lang('Active')</span>
                                            @else
                                                <span class="badge badge--danger">@lang('Deactive')</span>
                                            @endif
                                        </td>
                                        <td data-label="@lang('Action')">
                                            <a href="javascript:void(0)" class="icon-btn updateBtn"  data-route="{{ route('admin.fiat.currency.update',$item->id) }}" data-resourse="{{$item}}" data-toggle="modal" data-target="#updateBtn" ><i class="la la-pencil-alt"></i></a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($fiats->hasPages())
                    <div class="card-footer py-4">
                        {{ $fiats->links('admin.partials.paginate') }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Add METHOD MODAL --}}
    <div id="addModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"> @lang('Add New Fiat Currency')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{route('admin.fiat.currency.store')}}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="form-control-label font-weight-bold">@lang('Name') <span class="text-danger">*</span></label>
                            <input type="text" class="form-control check-length" data-length="40" placeholder="@lang('Great Britain Pound')" value="{{ old('name') }}" name="name" required>
                            <span class="remaining float-right"></span>
                        </div>

                        <div class="form-group">
                            <label class="form-control-label font-weight-bold">@lang('Code') <span class="text-danger">*</span></label>
                            <input type="text" class="form-control check-length" data-length="10" placeholder="@lang('GBP')" value="{{ old('code') }}" name="code" required>
                            <span class="remaining float-right"></span>
                        </div>

                        <div class="form-group">
                            <label class="form-control-label font-weight-bold">@lang('Symbol') <span class="text-danger">*</span></label>
                            <input type="text"class="form-control check-length" data-length="5" placeholder="@lang('£')" value="{{ old('symbol') }}" name="symbol" required>
                            <span class="remaining float-right"></span>
                        </div>

                        <div class="form-group">
                            <label class="form-control-label font-weight-bold">@lang('Rate') <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">1 @lang('USD') = </div>
                                </div>
                                <input type="number" step="any" class="form-control" placeholder="0" name="rate" value="{{ old('rate') }}" required/>
                                <div class="input-group-append">
                                    <div class="input-group-text"><span
                                        class="currency-symbol"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn--primary">@lang('Save')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Update METHOD MODAL --}}
    <div id="updateBtn" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"> @lang('Update Fiat Currency')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" class="edit-route" method="POST">
                    @csrf

                    <div class="modal-body">
                        <div class="form-group">
                            <label class="form-control-label font-weight-bold">@lang('Name') <span class="text-danger">*</span></label>
                            <input type="text" class="form-control check-length name" data-length="40" placeholder="@lang('Great Britain Pound')" name="name" required>
                            <span class="remaining float-right"></span>
                        </div>

                        <div class="form-group">
                            <label class="form-control-label font-weight-bold">@lang('Code') <span class="text-danger">*</span></label>
                            <input type="text" class="form-control check-length code" data-length="10" placeholder="@lang('GBP')" name="code" required>
                            <span class="remaining float-right"></span>
                        </div>

                        <div class="form-group">
                            <label class="form-control-label font-weight-bold">@lang('Symbol') <span class="text-danger">*</span></label>
                            <input type="text"class="form-control check-length symbol" data-length="5" placeholder="@lang('£')" value="{{ old('symbol') }}" name="symbol" required>
                            <span class="remaining float-right"></span>
                        </div>

                        <div class="form-group">
                            <label class="form-control-label font-weight-bold">@lang('Rate') <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">1 @lang('USD') = </div>
                                </div>
                                <input type="number" step="any" class="form-control rate" placeholder="0" name="rate" required/>
                                <div class="input-group-append">
                                    <div class="input-group-text"><span
                                        class="currency-symbol"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-control-label font-weight-bold">@lang('Status')</label>
                            <input id="edit-status" type="checkbox" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-toggle="toggle" data-on="@lang('Active')" data-off="@lang('Disabled')" name="status">
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn--primary">@lang('Update')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('breadcrumb-plugins')
        <a href="javascript:void(0)" class="btn btn--primary mr-3 mt-2 addBtn"><i class="fa fa-fw fa-plus"></i>@lang('Add New')</a>
        <form action="{{ route('admin.fiat.currency.search') }}" method="GET" class="form-inline float-sm-right bg--white mt-2">
            <div class="input-group has_append">
                <input type="text" name="search" class="form-control" placeholder="@lang('Name / Code')" value="{{ request()->search??null }}">
                <div class="input-group-append">
                    <button class="btn btn--primary" type="submit"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </form>
    @endpush
@endsection

@push('script')
<script>
    'use strict';

    $('.check-length').on('input', function(){
        let maxLength = $(this).data('length');
        let currentLength = $(this).val().length;

        let remain = maxLength - currentLength;
        let result =  `${remain} @lang('characters remaining')`;
        let remainElement = $(this).parent('.form-group').find('.remaining');

        remainElement.css({
            fontWeight: 'bold',
            fontSize: '14px',
            display: 'block',
            textAlign: 'end',
        });

        if(remain <= 5){
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

    (function ($) {
        $('.addBtn').on('click', function () {
            var modal = $('#addModal');

            modal.find('input[name=code]').on('input', function () {
                modal.find('.currency-symbol').text($(this).val());
            });

            modal.modal('show');
        });

        $('.updateBtn').on('click', function () {
            var modal = $('#updateBtn');

            var resourse = $(this).data('resourse');

            modal.find('.currency-symbol').text(resourse.code);

            modal.find('input[name=code]').on('input', function () {
                modal.find('.currency-symbol').text($(this).val());
            });

            var route = $(this).data('route');
            $('.name').val(resourse.name);
            $('.code').val(resourse.code);
            $('.symbol').val(resourse.symbol);
            $('.rate').val(parseFloat(resourse.rate).toFixed(8));

            if(resourse.status == 1){
                modal.find('input[name=status]').bootstrapToggle('on');
            }else{
                modal.find('input[name=status]').bootstrapToggle('off');
            }

            $('.edit-route').attr('action',route);



        });

    })(jQuery);
</script>
@endpush
