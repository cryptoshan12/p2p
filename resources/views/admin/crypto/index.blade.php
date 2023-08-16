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
                                    <th>@lang('Name')</th>
                                    <th>@lang('Image')</th>
                                    <th>@lang('Code')</th>
                                    <th>@lang('Symbol')</th>
                                    <th>@lang('Rate')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody class="list">
                                @forelse ($cryptos as $item)
                                    <tr>
                                        <td data-label="@lang('Name')">{{__($item->name)}}</td>
                                        <td data-label="@lang('Image')">
                                            <div class="user justify-content-center">
                                                <div class="thumb"><img src="{{ getImage(imagePath()['crypto']['path'].'/'. $item->image,imagePath()['crypto']['size'])}}" alt="@lang('image')"></div>
                                            </div>
                                        </td>
                                        <td data-label="@lang('Code')">{{__($item->code)}}</td>
                                        <td data-label="@lang('Symbol')">{{__($item->symbol)}}</td>
                                        <td data-label="@lang('Rate')"><span class="font-weight-bold">{{showAmount($item->rate)}} @lang('USD')</span> / {{__($item->code)}}</td>

                                        <td data-label="@lang('Status')">
                                            @if ($item->status == 1)
                                                <span class="badge badge--success">@lang('Active')</span>
                                            @else
                                                <span class="badge badge--danger">@lang('Deactive')</span>
                                            @endif
                                        </td>
                                        <td data-label="@lang('Action')">
                                            <a href="{{route('admin.crypto.edit',$item->id)}}" class="icon-btn" ><i class="la la-pencil-alt"></i></a>
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
                @if($cryptos->hasPages())
                <div class="card-footer py-4">
                    {{ $cryptos->links('admin.partials.paginate') }}
                </div>
                @endif
            </div>
        </div>
    </div>

    @push('breadcrumb-plugins')
        <a href="{{route('admin.crypto.metamask')}}" class="btn btn--primary mr-3 mt-2"><i class="fa fa-fw fa-cog"></i>@lang('MetaMask')</a>
        <a href="{{route('admin.crypto.new')}}" class="btn btn--primary mr-3 mt-2"><i class="fa fa-fw fa-plus"></i>@lang('Add New')</a>

        <form action="{{ route('admin.crypto.search') }}" method="GET" class="form-inline float-sm-right bg--white mt-2">
            <div class="input-group has_append">
                <input type="text" name="search" class="form-control" placeholder="@lang('Name / Code')" value="{{ request()->search??null }}">
                <div class="input-group-append">
                    <button class="btn btn--primary" type="submit"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </form>

    @endpush
@endsection
