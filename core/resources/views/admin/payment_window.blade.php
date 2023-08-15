@extends('admin.layouts.app')

@section('panel')

    <div class="row">
        @forelse ($paymentWindows as $item)

            <div class="col-md-3 mb-3">
                <div class="card">
                    <div class="card-body">
                        {{ __($item->minute) }} @lang('Minutes')
                        <a href="javascript:void(0)" class="icon-btn btn--danger removeBtn float-right" data-id="{{ $item->id }}"><i class="la la-trash"></i></a>
                        <a href="javascript:void(0)" class="icon-btn float-right mr-2 updateBtn" data-route="{{ route('admin.payment.window.update',$item->id) }}" data-resourse="{{$item}}" data-toggle="modal" data-target="#updateBtn"><i class="la la-pencil-alt"></i></a>
                    </div>
                </div>
            </div>
        @empty

            <div class="col-md-12">
                <div class="card">
                    <div class="card-body text-center">
                        {{__($emptyMessage) }}
                    </div>
                </div>
            </div>
        @endforelse

    </div>

    {{-- Add METHOD MODAL --}}
    <div id="addModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"> @lang('Add New Payment Window')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{route('admin.payment.window.store')}}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="form-control-label font-weight-bold">@lang('Minutes') <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" placeholder="@lang('5')" value="{{ old('minute') }}" name="minute" required>
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
                    <h5 class="modal-title"> @lang('Update Payment Window')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" class="edit-route" method="POST">
                    @csrf

                    <div class="modal-body">
                        <div class="form-group">
                            <label class="form-control-label font-weight-bold">@lang('Minutes') <span class="text-danger">*</span></label>
                            <input type="number" class="form-control minute" placeholder="@lang('5')" value="{{ old('minute') }}" name="minute" required>
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

    {{-- REMOVE METHOD MODAL --}}
    <div id="removeModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Confirmation')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.payment.window.remove') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="modal-body">
                        <p class="font-weight-bold">@lang('Are you sure to delete this item? Once deleted can not be undone.')</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn--danger">@lang('Remove')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('breadcrumb-plugins')
        <a href="javascript:void(0)" class="btn btn--primary mr-3 mt-2 addBtn"><i class="fa fa-fw fa-plus"></i>@lang('Add New')</a>
    @endpush
@endsection

@push('script')
<script>
    'use strict';

    (function ($) {
        $('.addBtn').on('click', function () {
            var modal = $('#addModal');
            modal.modal('show');
        });

        $('.updateBtn').on('click', function () {
            var modal = $('#updateBtn');

            var resourse = $(this).data('resourse');

            var route = $(this).data('route');
            modal.find('.minute').val(resourse.minute);

            $('.edit-route').attr('action',route);
        });

        $('.removeBtn').on('click', function () {
            var modal = $('#removeModal');
            modal.find('input[name=id]').val($(this).data('id'))
            modal.modal('show');
        });

    })(jQuery);
</script>
@endpush
