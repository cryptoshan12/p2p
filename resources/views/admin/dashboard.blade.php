@extends('admin.layouts.app')

@section('panel')
      @if(@json_decode($general->sys_version)->version > systemDetails()['version'])
        <div class="row">
            <div class="col-md-12">
                <div class="card text-white bg-warning mb-3">
                    <div class="card-header">
                        <h3 class="card-title"> @lang('New Version Available') <button class="btn btn--dark float-right">@lang('Version') {{json_decode($general->sys_version)->version}}</button> </h3>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title text-dark">@lang('What is the Update ?')</h5>
                        <p><pre  class="f-size--24">{{json_decode($general->sys_version)->details}}</pre></p>
                    </div>
                </div>
            </div>
        </div>
        @endif
        @if(@json_decode($general->sys_version)->message)
        <div class="row">
            @foreach(json_decode($general->sys_version)->message as $msg)
              <div class="col-md-12">
                  <div class="alert border border--primary" role="alert">
                      <div class="alert__icon bg--primary"><i class="far fa-bell"></i></div>
                      <p class="alert__message">@php echo $msg; @endphp</p>
                      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
              </div>
            @endforeach
        </div>
        @endif

    <div class="row mb-none-30">
        <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
            <div class="dashboard-w1 bg--primary b-radius--10 box-shadow">
                <div class="icon">
                    <i class="fa fa-users"></i>
                </div>
                <div class="details">
                    <div class="numbers">
                        <span class="amount">{{$widget['total_users']}}</span>
                    </div>
                    <div class="desciption">
                        <span class="text--small">@lang('Total Users')</span>
                    </div>
                    <a href="{{route('admin.users.all')}}" class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('View All')</a>
                </div>
            </div>
        </div><!-- dashboard-w1 end -->
        <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
            <div class="dashboard-w1 bg--cyan b-radius--10 box-shadow">
                <div class="icon">
                    <i class="fa fa-users"></i>
                </div>
                <div class="details">
                    <div class="numbers">
                        <span class="amount">{{$widget['verified_users']}}</span>
                    </div>
                    <div class="desciption">
                        <span class="text--small">@lang('Total Verified Users')</span>
                    </div>
                    <a href="{{route('admin.users.active')}}" class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('View All')</a>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
            <div class="dashboard-w1 bg--orange b-radius--10 box-shadow ">
                <div class="icon">
                    <i class="la la-envelope"></i>
                </div>
                <div class="details">
                    <div class="numbers">
                        <span class="amount">{{$widget['email_unverified_users']}}</span>
                    </div>
                    <div class="desciption">
                        <span class="text--small">@lang('Total Email Unverified Users')</span>
                    </div>

                    <a href="{{route('admin.users.email.unverified')}}" class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('View All')</a>
                </div>
            </div>
        </div><!-- dashboard-w1 end -->
        <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
            <div class="dashboard-w1 bg--pink b-radius--10 box-shadow ">
                <div class="icon">
                    <i class="fa fa-shopping-cart"></i>
                </div>
                <div class="details">
                    <div class="numbers">
                        <span class="amount">{{$widget['sms_unverified_users']}}</span>
                    </div>
                    <div class="desciption">
                        <span class="text--small">@lang('Total SMS Unverified Users')</span>
                    </div>

                    <a href="{{route('admin.users.sms.unverified')}}" class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('View All')</a>
                </div>
            </div>
        </div><!-- dashboard-w1 end -->


    </div><!-- row end-->

    <div class="row mb-none-30 mt-50">
        <div class="col-xl-12 col-md-12 mb-30">
            <h4>@lang('Deposit Summary')</h4>
        </div>

        @foreach ($deposits as $deposit)
            <div class="col-xl-4 col-md-6 mb-30">
                <div class="widget bb--3 border--primary b-radius--10 bg--white p-4 box--shadow2 has--link">
                    <a href="{{route('admin.deposit.successful')}}" class="item--link"></a>
                    <div class="widget__icon b-radius--rounded bg--primary display-4"><img src="{{ getImage(imagePath()['crypto']['path'].'/'. $deposit->image,imagePath()['crypto']['size'])}}" alt="@lang('image')"></div>
                    <div class="widget__content">
                    <p class="text-uppercase text-muted">@lang('Total Deposit')</p>
                    <h3 class="text--primary font-weight-bold">{{showAmount($deposit->deposits_sum_amount,8)}} {{__($deposit->code)}}</h3>
                    <p class="stat-down mt-10">
                        <span>@lang('Total Charge')</span>
                        <i class="fas fa-arrow-right"></i>
                        {{showAmount($deposit->deposits_sum_charge,8)}} {{__($deposit->code)}}
                    </p>
                    </div>
                    <div class="widget__arrow">
                    <i class="fas fa-chevron-right"></i>
                    </div>
                </div><!-- widget end -->
            </div>
        @endforeach
    </div>


    <div class="row mb-none-30 mt-50">
        <div class="col-xl-12 col-md-12 mb-30">
            <h4>@lang('Withdrawal Summary')</h4>
        </div>

        @foreach ($withdrawals as $withdrawal)
            <div class="col-xl-4 col-md-6 mb-30">
                <div class="widget bb--3 border--info b-radius--10 bg--white p-4 box--shadow2 has--link">
                <a href="{{route('admin.withdraw.approved')}}" class="item--link"></a>
                <div class="widget__icon b-radius--rounded bg--info display-4"><img src="{{ getImage(imagePath()['crypto']['path'].'/'. $withdrawal->image,imagePath()['crypto']['size'])}}" alt="@lang('image')"></div>
                <div class="widget__content">
                    <p class="text-uppercase text-muted">@lang('Total Withdrawal')</p>
                    <h3 class="text--info font-weight-bold">{{showAmount($withdrawal->withdrawals_sum_amount,8)}} {{__($withdrawal->code)}}</h3>
                    <p class="stat-down mt-10">
                        <span>@lang('Total Charge')</span>
                        <i class="fas fa-arrow-right"></i>
                        {{showAmount($withdrawal->withdrawals_sum_charge,8)}} {{__($withdrawal->code)}}
                    </p>
                </div>
                <div class="widget__arrow">
                    <i class="fas fa-chevron-right"></i>
                </div>
                </div><!-- widget end -->
            </div>
        @endforeach
    </div>

    <div class="row mt-50 mb-none-30">
        <div class="col-xl-3 col-lg-6 col-sm-6 mb-30">
            <div class="dashboard-w1 bg--19 b-radius--10 box-shadow" >
                <div class="icon">
                    <i class="lab la-adversal"></i>
                </div>
                <div class="details">
                    <div class="numbers">
                        <span class="amount">{{$widget['totalAd']}}</span>
                    </div>
                    <div class="desciption">
                        <span>@lang('Total Adveretisements')</span>
                    </div>
                    <a href="{{route('admin.ad.index')}}" class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('View All')</a>
                </div>
            </div>
        </div>


        <div class="col-xl-3 col-lg-6 col-sm-6 mb-30">
            <div class="dashboard-w1 bg--3 b-radius--10 box-shadow" >
                <div class="icon">
                    <i class="las la-exchange-alt"></i>
                </div>
                <div class="details">
                    <div class="numbers">
                        <span class="amount">{{$widget['totalTrade']}}</span>
                    </div>
                    <div class="desciption">
                        <span>@lang('Total Trade')</span>
                    </div>
                    <a href="{{route('admin.trade.request.index')}}" class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('View All')</a>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-sm-6 mb-30">
            <div class="dashboard-w1 bg--12 b-radius--10 box-shadow" >
                <div class="icon">
                    <i class="lab la-bitcoin"></i>
                </div>
                <div class="details">
                    <div class="numbers">
                        <span class="amount">{{$widget['totalCrypto']}}</span>
                    </div>
                    <div class="desciption">
                        <span>@lang('Total Crypto Currency')</span>
                    </div>

                    <a href="{{route('admin.crypto.index')}}" class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('View All')</a>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-sm-6 mb-30">
            <div class="dashboard-w1 bg--6 b-radius--10 box-shadow">
                <div class="icon">
                    <i class="las la-coins"></i>
                </div>
                <div class="details">
                    <div class="numbers">
                        <span class="amount">{{$widget['totalFiat']}}</span>
                    </div>
                    <div class="desciption">
                        <span>@lang('Total Fiat Currency')</span>
                    </div>

                    <a href="{{route('admin.fiat.currency.index')}}" class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('View All')</a>
                </div>
            </div>
        </div>
    </div>


    <div class="row mb-none-30 mt-5">
        <div class="col-xl-4 col-lg-6 mb-30">
            <div class="card overflow-hidden">
                <div class="card-body">
                    <h5 class="card-title">@lang('Login By Browser')</h5>
                    <canvas id="userBrowserChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-6 mb-30">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">@lang('Login By OS')</h5>
                    <canvas id="userOsChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-6 mb-30">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">@lang('Login By Country')</h5>
                    <canvas id="userCountryChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="cronModal">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Cron Job Setting Instruction')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <p class="cron mb-2 text-justify">@lang('To Automate the process of deactive the expired promotional featured ads, you need to set the cron job. Set The cron time as minimum as possible.')</p>

                    <label class="w-100 font-weight-bold">@lang('Fiat Currency Cron Command')</label>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" id="copyinput-fiat" value="curl -s {{route('cron.fiat.rate')}}" readonly>
                        <div class="input-group-append">
                            <button class="btn btn--primary copy" onclick="fiatFunction()" type="button"><i class="la la-copy"></i></button>
                        </div>
                    </div>

                    <label class="w-100 font-weight-bold">@lang('Crypto Currency Cron Command')</label>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" id="copyinput-crypto" value="curl -s {{route('cron.crypto.rate')}}" readonly>
                        <div class="input-group-append">
                            <button class="btn btn--primary copy" onclick="cryptoFunction()" type="button"><i class="la la-copy"></i></button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--secondary" data-dismiss="modal">@lang('Close')</button>
                </div>
            </div>
        </div>
    </div>

@endsection


@push('breadcrumb-plugins')
    <h6 class="text--success">@lang('Last cron run :') {{diffForHumans($general->last_cron)}}</h6>
@endpush


@push('script')

    <script src="{{asset('assets/admin/js/vendor/chart.js.2.8.0.js')}}"></script>

    <script>
        "use strict";

        @if(\Carbon\Carbon::parse($general->last_cron)->diffInSeconds()>=900)
        (function($){
                $("#cronModal").modal('show');
            })(jQuery)
        @endif

        function fiatFunction() {
            var copyText = document.getElementById("copyinput-fiat");
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            /*For mobile devices*/
            document.execCommand("copy");
            iziToast.success({message: "Copied: " + copyText.value, position: "topRight"});
        }

        function cryptoFunction() {
            var copyText = document.getElementById("copyinput-crypto");
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            /*For mobile devices*/
            document.execCommand("copy");
            iziToast.success({message: "Copied: " + copyText.value, position: "topRight"});
        }

        var ctx = document.getElementById('userBrowserChart');
        var myChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: @json($chart['user_browser_counter']->keys()),
                datasets: [{
                    data: {{ $chart['user_browser_counter']->flatten() }},
                    backgroundColor: [
                        '#ff7675',
                        '#6c5ce7',
                        '#ffa62b',
                        '#ffeaa7',
                        '#D980FA',
                        '#fccbcb',
                        '#45aaf2',
                        '#05dfd7',
                        '#FF00F6',
                        '#1e90ff',
                        '#2ed573',
                        '#eccc68',
                        '#ff5200',
                        '#cd84f1',
                        '#7efff5',
                        '#7158e2',
                        '#fff200',
                        '#ff9ff3',
                        '#08ffc8',
                        '#3742fa',
                        '#1089ff',
                        '#70FF61',
                        '#bf9fee',
                        '#574b90'
                    ],
                    borderColor: [
                        'rgba(231, 80, 90, 0.75)'
                    ],
                    borderWidth: 0,

                }]
            },
            options: {
                aspectRatio: 1,
                responsive: true,
                maintainAspectRatio: true,
                elements: {
                    line: {
                        tension: 0 // disables bezier curves
                    }
                },
                scales: {
                    xAxes: [{
                        display: false
                    }],
                    yAxes: [{
                        display: false
                    }]
                },
                legend: {
                    display: false,
                }
            }
        });



        var ctx = document.getElementById('userOsChart');
        var myChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: @json($chart['user_os_counter']->keys()),
                datasets: [{
                    data: {{ $chart['user_os_counter']->flatten() }},
                    backgroundColor: [
                        '#ff7675',
                        '#6c5ce7',
                        '#ffa62b',
                        '#ffeaa7',
                        '#D980FA',
                        '#fccbcb',
                        '#45aaf2',
                        '#05dfd7',
                        '#FF00F6',
                        '#1e90ff',
                        '#2ed573',
                        '#eccc68',
                        '#ff5200',
                        '#cd84f1',
                        '#7efff5',
                        '#7158e2',
                        '#fff200',
                        '#ff9ff3',
                        '#08ffc8',
                        '#3742fa',
                        '#1089ff',
                        '#70FF61',
                        '#bf9fee',
                        '#574b90'
                    ],
                    borderColor: [
                        'rgba(0, 0, 0, 0.05)'
                    ],
                    borderWidth: 0,

                }]
            },
            options: {
                aspectRatio: 1,
                responsive: true,
                elements: {
                    line: {
                        tension: 0 // disables bezier curves
                    }
                },
                scales: {
                    xAxes: [{
                        display: false
                    }],
                    yAxes: [{
                        display: false
                    }]
                },
                legend: {
                    display: false,
                }
            },
        });


        // Donut chart
        var ctx = document.getElementById('userCountryChart');
        var myChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: @json($chart['user_country_counter']->keys()),
                datasets: [{
                    data: {{ $chart['user_country_counter']->flatten() }},
                    backgroundColor: [
                        '#ff7675',
                        '#6c5ce7',
                        '#ffa62b',
                        '#ffeaa7',
                        '#D980FA',
                        '#fccbcb',
                        '#45aaf2',
                        '#05dfd7',
                        '#FF00F6',
                        '#1e90ff',
                        '#2ed573',
                        '#eccc68',
                        '#ff5200',
                        '#cd84f1',
                        '#7efff5',
                        '#7158e2',
                        '#fff200',
                        '#ff9ff3',
                        '#08ffc8',
                        '#3742fa',
                        '#1089ff',
                        '#70FF61',
                        '#bf9fee',
                        '#574b90'
                    ],
                    borderColor: [
                        'rgba(231, 80, 90, 0.75)'
                    ],
                    borderWidth: 0,

                }]
            },
            options: {
                aspectRatio: 1,
                responsive: true,
                elements: {
                    line: {
                        tension: 0 // disables bezier curves
                    }
                },
                scales: {
                    xAxes: [{
                        display: false
                    }],
                    yAxes: [{
                        display: false
                    }]
                },
                legend: {
                    display: false,
                }
            }
        });

    </script>
@endpush
