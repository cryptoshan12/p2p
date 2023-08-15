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
                                    <th scope="col">@lang('Slug')</th>
                                    <th scope="col">@lang('Supported Currencies')</th>
                                    <th scope="col">@lang('Status')</th>
                                    <th scope="col">@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody class="list">
                                @forelse ($fiatGateways as $item)
                                    <tr>
                                        <td data-label="@lang('SL')">{{ $fiatGateways->firstItem() + $loop->index }}</td>
                                        <td data-label="@lang('Name')">{{ __($item->name) }}</td>
                                        <td data-label="@lang('Slug')">{{ __($item->slug) }}</td>
                                        <td data-label="@lang('Supported Currencies')">{{count($item->code)}}</td>
                                        <td data-label="@lang('Status')">
                                            @if ($item->status == 1)
                                                <span class="badge badge--primary">@lang('Active')</span>
                                            @else
                                                <span class="badge badge--danger">@lang('Deactive')</span>
                                            @endif
                                        </td>
                                        <td data-label="@lang('Action')">
                                            <a href="javascript:void(0)" class="icon-btn updateBtn"  data-route="{{ route('admin.fiat.gateway.update',$item->id) }}" data-resourse="{{$item}}" data-toggle="modal" data-target="#updateBtn" ><i class="la la-pencil-alt"></i></a>
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
                @if($fiatGateways->hasPages())
                    <div class="card-footer py-4">
                        {{ $fiatGateways->links('admin.partials.paginate') }}
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
                    <h5 class="modal-title"> @lang('Add New Fiat Gateway')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{route('admin.fiat.gateway.store')}}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="form-control-label font-weight-bold">@lang('Name') <span class="text-danger">*</span></label>
                            <input type="text" class="form-control check-length" data-length="40" placeholder="@lang('Paypal')" value="{{ old('name') }}" name="name" required>
                            <span class="remaining float-right"></span>
                        </div>

                        <div class="form-group">
                            <label class="form-control-label font-weight-bold">@lang('Slug') <span class="text-danger">*</span></label>
                            <input type="text" class="form-control check-length" data-length="100" placeholder="@lang('paypal')" value="{{ old('slug') }}" name="slug" required>
                            <span class="remaining float-right"></span>
                        </div>

                        <div class="form-group">
                            <label class="form-control-label font-weight-bold">@lang('Supported Currencies') <span class="text-danger">*</span></label>
                            <select class="select2-multi-select" name="code[]" multiple="multiple" required>
                                @foreach ($fiatCodes as $item)
                                    <option value="{{$item->id}}">{{__($item->code)}}</option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary btn-block">@lang('Save')</button>
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
                    <h5 class="modal-title"> @lang('Update Fiat Gateway')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" class="edit-route" method="POST">
                    @csrf

                    <div class="modal-body">
                        <div class="form-group">
                            <label class="form-control-label font-weight-bold">@lang('Name') <span class="text-danger">*</span></label>
                            <input type="text" class="form-control check-length name" data-length="40" placeholder="@lang('Paypal, Bkash')" name="name" required>
                            <span class="remaining float-right"></span>
                        </div>

                        <div class="form-group">
                            <label class="form-control-label font-weight-bold">@lang('Slug') <span class="text-danger">*</span></label>
                            <input type="text"class="form-control check-length slug" data-length="100" placeholder="@lang('paypal')" name="slug" required>
                            <span class="remaining float-right"></span>
                        </div>

                        <div class="form-group">
                            <label class="form-control-label font-weight-bold">@lang('Supported Currencies') <span class="text-danger">*</span></label>
                            <select class="select2-multi-select code" name="code[]" multiple="multiple" required>
                                @foreach ($fiatCodes as $item)
                                    <option value="{{$item->id}}">{{__($item->code)}}</option>
                                @endforeach
                            </select>
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
        <form action="{{ route('admin.fiat.gateway.search') }}" method="GET" class="form-inline float-sm-right bg--white mt-2">
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
        let maxLength       = $(this).data('length');
        let currentLength   = $(this).val().length;
        let remain          = maxLength - currentLength;
        let result          = `${remain} characters remaining`;
        let remainElement   = $(this).parent('.form-group').find('.remaining');

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

        remainElement.html(`${remain} characters remaining`);
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
            modal.modal('show');
        });

        $('.updateBtn').on('click', function () {
            var modal = $('#updateBtn');

            var resourse = $(this).data('resourse');

            var route = $(this).data('route');
            $('.name').val(resourse.name);
            $('.slug').val(resourse.slug);

            modal.find('.code').val(resourse.code).select2();

            if(resourse.status == 1){
                $('#edit-status').parent('div').removeClass('off');
                $('#edit-status').prop('checked', true);
            }else{
                $('#edit-status').parent('div').addClass('off');
                $('#edit-status').prop('checked', false);
            }

            $('.edit-route').attr('action',route);

            var nameLength = modal.find('.name').val().length;
            modal.find('.name').parent('.form-group').find('.remaining').text(40 - nameLength + 'characters remaining');
            var nameLength = modal.find('.slug').val().length;
            modal.find('.slug').parent('.form-group').find('.remaining').text(40 - nameLength + 'characters remaining');

        });

        $('input[name=slug]').on('keyup', function(e) {
            var keyCode = e.keyCode || e.which;
            var isValid = regex.test(String.fromCharCode(keyCode));
            if(e.keyCode==32){
                $(this).val($(this).val().replace(/\s+/g, '-'));
            }
        });


    })(jQuery);
</script>
@endpush
