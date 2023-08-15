@extends($activeTemplate.'layouts.frontend')

@section('content')
@include($activeTemplate.'partials.breadcrumb')


    <section class="pt-120 pb-120">
        <div class="container">
            <div class="row mb-none-30">
                <div class="col-xl-12 col-lg-12 col-sm-12">
                    <div class="btn-group justify-content-end">
                        <a href="{{route('ticket.open')}}" class="cmn-btn2 btn-md">@lang('New Ticket')</a>
                    </div>
                </div>
            </div>
            <div class="row mt-50">
                <div class="col-xl-12">
                    <div class="custom--card">
                        <div class="card-body p-0">
                            <div class="table-responsive--md">
                                <table class="table custom--table">
                                    <thead>
                                        <tr>
                                            <th>@lang('Subject')</th>
                                            <th>@lang('Status')</th>
                                            <th>@lang('Priority')</th>
                                            <th>@lang('Last Reply')</th>
                                            <th>@lang('Action')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($supports as $key => $support)
                                            <tr>
                                                <td data-label="@lang('Subject')"> <a href="{{ route('ticket.view', $support->ticket) }}" class="font-weight-bold"> [@lang('Ticket')#{{ $support->ticket }}] {{ __($support->subject) }} </a></td>
                                                <td data-label="@lang('Status')">
                                                    @if($support->status == 0)
                                                        <span class="badge text-white badge--completed">@lang('Open')</span>
                                                    @elseif($support->status == 1)
                                                        <span class="badge text-white badge--released">@lang('Answered')</span>
                                                    @elseif($support->status == 2)
                                                        <span class="badge text-white badge--pending">@lang('Customer Reply')</span>
                                                    @elseif($support->status == 3)
                                                        <span class="badge text-white badge--cancel">@lang('Closed')</span>
                                                    @endif
                                                </td>
                                                <td data-label="@lang('Priority')">
                                                    @if($support->priority == 1)
                                                        <span class="badge text-white badge--released">@lang('Low')</span>
                                                    @elseif($support->priority == 2)
                                                        <span class="badge text-white badge--paid">@lang('Medium')</span>
                                                    @elseif($support->priority == 3)
                                                        <span class="badge text-white badge--completed">@lang('High')</span>
                                                    @endif
                                                </td>
                                                <td data-label="@lang('Last Reply')">{{ \Carbon\Carbon::parse($support->last_reply)->diffForHumans() }} </td>

                                                <td data-label="@lang('Action')">
                                                    <a href="{{ route('ticket.view', $support->ticket) }}" class="cmn-btn btn-sm">
                                                        <i class="las la-desktop"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="100%" class="text-center">@lang('No data found')</td>
                                            </tr>
                                        @endforelse

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    {{$supports->links()}}
                </div>
            </div>
        </div>
    </section>
@endsection
