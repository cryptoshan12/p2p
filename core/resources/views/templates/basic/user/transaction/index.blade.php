@extends($activeTemplate.'layouts.frontend')
@section('content')

    @include($activeTemplate.'partials.breadcrumb')

    <section class="pt-120 pb-120 section--bg">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                  <div class="custom--card">
                    <div class="card-header border-bottom-0 text-center">
                        <ul class="btn-list justify-content-center">
                          <li>
                            <a href="{{route('user.transaction.index')}}" class="btn btn-sm btn-outline--base  @if(!request()->id) active @endif">@lang('All')</a>
                          </li>
                          @foreach ($cryptos as $crypto)
                            <li>
                              <a href="{{route('user.transaction.single',[$crypto->id,$crypto->code])}}" class="btn btn-sm btn-outline--base @if(request()->id == $crypto->id) active @endif">{{$crypto->code}}</a>
                            </li>
                          @endforeach
                        </ul>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive table-responsive--md">
                          <table class="table custom--table mb-0">
                            <thead>
                              <tr>
                                <th>@lang('Crypto Currency')</th>
                                <th>@lang('Transaction Code')</th>
                                <th>@lang('Amount')</th>
                                <th>@lang('Charge')</th>
                                <th>@lang('Post balance')</th>
                                <th>@lang('Details')</th>
                              </tr>
                            </thead>
                            <tbody>
                                  @forelse ($transactions as $transaction)
                                    <tr>
                                        <td data-label="@lang('Crypto Currency')"><span class="text--base">{{__($transaction->crypto->code)}}</span></td>
                                        <td data-label="@lang('Transaction Code')">{{$transaction->trx}}</td>
                                        <td data-label="@lang('Amount')">
                                            <span class="fw-bold @if($transaction->trx_type == '+') text-success @else text-danger @endif">
                                                {{showAmount($transaction->amount,8)}}
                                            </span>
                                        </td>
                                        <td data-label="@lang('Charge')">{{showAmount($transaction->charge,8)}}</td>
                                        <td data-label="@lang('Post balance')">{{showAmount($transaction->post_balance,8)}}</td>
                                        <td data-label="@lang('Details')">{{__($transaction->details)}}</td>
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
                </div>
            </div>
            <div class="row">
                {{ $transactions->links() }}
            </div>
        </div>
    </section>
@endsection
