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
                                    <th scope="col">@lang('Completed Trade')</th>
                                    <th scope="col">@lang('Advertise Limit')</th>
                                    <th scope="col">@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($limits as $item)
                                    <tr>
                                        <td data-label="@lang('SL')">{{ $limits->firstItem() + $loop->index }}</td>
                                        <td data-label="@lang('Completed Trade')">{{$item->completed_trade}}</td>
                                        <td data-label="@lang('Advertise Limit')">{{$item->ad_limit}}</td>

                                        <td data-label="@lang('Action')">
                                            <a href="javascript:void(0)" class="icon-btn updateBtn"  data-route="{{ route('admin.ad.limit.update',$item->id) }}" data-resourse="{{$item}}" data-toggle="modal" data-target="#updateBtn" ><i class="la la-pencil-alt"></i></a>
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

                @if($limits->hasPages())
                <div class="card-footer py-4">
                    {{ $limits->links('admin.partials.paginate') }}
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
                    <h5 class="modal-title"> @lang('New Advertise Limit')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{route('admin.ad.limit.store')}}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="form-control-label font-weight-bold">@lang('Completed Trade') <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" placeholder="0" name="completed_trade" required>
                        </div>

                        <div class="form-group">
                            <label class="form-control-label font-weight-bold">@lang('Advertisement Limit') <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" placeholder="0" name="ad_limit" required>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-block btn--primary">@lang('Save')</button>
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
                    <h5 class="modal-title"> @lang('Update Advertise Limit')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" class="edit-route" method="POST">
                    @csrf

                    <div class="modal-body">
                        <div class="form-group">
                            <label class="form-control-label font-weight-bold">@lang('Completed Trade') <span class="text-danger">*</span></label>
                            <input type="number" class="form-control completed-trade" placeholder="0" name="completed_trade" required>
                        </div>

                        <div class="form-group">
                            <label class="form-control-label font-weight-bold">@lang('Advertisement Limit') <span class="text-danger">*</span></label>
                            <input type="number" class="form-control ad-limit" placeholder="0" name="ad_limit" required>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary">@lang('Update')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('breadcrumb-plugins')
        <a href="javascript:void(0)" class="btn btn-sm btn--primary addBtn"><i class="fa fa-fw fa-plus"></i>@lang('Add New')</a>
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
                modal.find('.completed-trade').val(resourse.completed_trade);
                modal.find('.ad-limit').val(resourse.ad_limit);
                $('.edit-route').attr('action',route);
            });
        })(jQuery);
    </script>
@endpush
