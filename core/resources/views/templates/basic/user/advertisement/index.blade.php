@extends($activeTemplate.'layouts.frontend')
@section('content')
    @include($activeTemplate.'partials.breadcrumb')

    <section class="pt-120 pb-120">
        <div class="container">
          <div class="row">
            <div class="col-xl-12 col-lg-12 col-sm-12">
                <div class="btn-group justify-content-end">
                    <a href="{{route('user.advertisement.new')}}" class="cmn-btn2 btn-md">@lang('New Advertisement')</a>
                </div>
            </div>
          </div>

          <div class="row mt-4">
            <div class="col-xl-12">
              <div class="custom--card">
                <div class="card-body p-0">
                  <div class="table-responsive--md">
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
                          @forelse ($advertisements as $item)
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
                                        <span class="badge text-white badge--released">@lang('Active')</span>
                                    @else
                                        <span class="badge text-white badge--cancel">@lang('Deactive')</span>
                                    @endif
                                </td>
                                <td data-label="@lang('Action')">
                                    @if ($item->status == 1)
                                        <button class="cmn-btn btn-sm bg-danger statusBtn" data-id="{{ $item->id }}" data-status="{{ $item->status }}"><i class="las la-low-vision"></i></button>
                                    @else
                                        <button class="cmn-btn btn-sm bg-success statusBtn" data-id="{{ $item->id }}" data-status="{{ $item->status }}"><i class="las la-eye"></i></button>
                                    @endif
                                    <a href="{{route('user.advertisement.edit',$item->id)}}" class="cmn-btn btn-sm"><i class="las la-edit"></i></a>
                                </td>
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

              {{$advertisements->links()}}
            </div>
          </div>
        </div>
    </section>

    <div class="modal fade" id="statusModal" tabindex="-1" role="dialog" aria-labelledby="existModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="existModalLongTitle">@lang('Confirmation')!</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form class="status-route" method="POST">
                @csrf
                <input type="hidden" name="id">
                <div class="modal-body">
                    <p class="status-msz"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('Close')</button>
                    <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-check"></i> @lang("Yes")</button>
                </div>
            </form>
        </div>
        </div>
    </div>
@endsection



@push('script')

    <script>
        (function($) {
            "use strict";
            var statusModal = $('#statusModal');

            $('.statusBtn').on('click', function() {
                var id = $(this).data('id');
                var status = $(this).data('status');
                var statusMessage;

                if (status == 1) {
                    statusMessage =`@lang('By proceeding with this operation, the advertisement will be') <span class="text-danger fw-bold">@lang('deactivated').</span>`;
                } else {
                    statusMessage =`@lang('By proceeding with this operation, the advertisement will be') <span class="text-success fw-bold">@lang('activated').</span>`;
                }

                statusModal.find('.status-msz').html(statusMessage);
                statusModal.find('input[name="id"]').val(id);
                statusModal.find('.status-route').attr('action', `{{ route('user.advertisement.status') }}`);
                statusModal.modal('show');
            });
        })(jQuery);
    </script>

@endpush
