@extends('backend.layouts.master')
<title>{{ Helper::getSetting()->brand_name }} - Dashboard</title>
@section('main-content')
<div class="container-fluid">
    @include('backend.layouts.notification')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
      <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
    </div>

    <!-- Content Row -->
    <div class="row">

      <!-- Category -->
      <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col mr-2">
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Category</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{\App\Models\Category::countActiveCategory()}}</div>
              </div>
              <div class="col-auto">
                <i class="fas fa-sitemap fa-2x text-gray-300"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Products -->
      <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col mr-2">
                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Products</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{\App\Models\Product::countActiveProduct()}}</div>
              </div>
              <div class="col-auto">
                <i class="fas fa-cubes fa-2x text-gray-300"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Order -->
      <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col mr-2">
                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Order</div>
                <div class="row no-gutters align-items-center">
                  <div class="col-auto">
                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">{{\App\Models\Order::countActiveOrder()}}</div>
                  </div>

                </div>
              </div>
              <div class="col-auto">
                <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!--Posts-->
      <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col mr-2">
                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Post</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{\App\Models\Post::countActivePost()}}</div>
              </div>
              <div class="col-auto">
                <i class="fas fa-folder fa-2x text-gray-300"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row">

      <!-- Area Chart -->
      <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
          <!-- Card Header - Dropdown -->
          <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Earnings Overview</h6>

          </div>
          <!-- Card Body -->
          <div class="card-body">
            <div class="chart-area">
              <canvas id="myAreaChart"></canvas>
            </div>
          </div>
        </div>
      </div>

      <!-- Pie Chart -->
      <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
          <!-- Card Header - Dropdown -->
          <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Users</h6>
          </div>
          <!-- Card Body -->
          <div class="card-body" style="overflow:hidden">
            <div id="pie_chart" style="width:350px; height:320px;">
          </div>
        </div>
      </div>
    </div>
    <!-- Content Row -->

  </div>

    <div class="row">
        {{-- RECENT ORDERS START --}}
        <div class="col-12 col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Orders</h6>
                    <a href="{{ route('order.index') }}">View Orders</a>
                </div>
                <div class="card-body p-0">
                    <table class="table m-0 table-striped">
                        <tbody>
                        @foreach($recentOrders as $order)
                            <tr>
                                <td class="d-flex justify-content-between px-4">
                                    <div>
                                        <p class="mb-0">{{ $order->first_name }} {{ $order->last_name }}</p>
                                        <p>
                                            <span class="font-weight-bold">{{ $order->order_number }}</span> | {{ $order->created_at }}
                                        </p>
                                    </div>
                                    <span>Rp{{ number_format($order->total_amount) }}</span>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        {{-- RECENT ORDERS END --}}
        {{-- TOP PRODUCTS START --}}
        <div class="col-12 col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Top Products</h6>
                    <a href="{{ route('product.index') }}">View Report</a>
                </div>
                <div class="card-body p-0">
                    <table class="table m-0 table-striped">
                        <tbody>
                        @foreach($topProducts as $product)
                            <tr>
                                <td class="d-flex justify-content-between px-4">
                                    <div>
                                        <p class="mb-0">{{ $product->title }}</p>
                                        <p>
                                            <span class="font-weight-bold">{{ $product->carts_count }}</span> Sold
                                        </p>
                                    </div>
                                    <span>Rp{{ number_format( $product->price) }}</span>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        {{-- TOP PRODUCTS END --}}
        {{-- RECENT SIGN UPS START --}}
        <div class="col-12 col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Sign Ups</h6>
                    <a href="{{ route('users.index') }}">View Users</a>
                </div>
                <div class="card-body p-0">
                    <table class="table m-0 table-striped">
                        <tbody>
                        @foreach($recentSignUps as $user)
                            <tr>
                                <td class="d-flex justify-content-between px-4">
                                    <div>
                                        <p class="mb-0">{{ $user->name }}</p>
                                    </div>
                                    <span>{{ $user->created_at?->diffForHumans() }}</span>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        {{-- RECENT SIGN UPS END --}}
        {{-- TOP CUSTOMERS START --}}
        <div class="col-12 col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Top Customers</h6>
                    <a href="{{ route('users.index') }}">View Report</a>
                </div>
                <div class="card-body p-0">
                    <table class="table m-0 table-striped">
                        <tbody>
                        @foreach($topCustomers as $user)
                            <tr>
                                <td class="d-flex justify-content-between px-4">
                                    <div>
                                        <p class="mb-0">{{ $user->name }}</p>
                                        <p>
                                            <span class="font-weight-bold">{{ $user->orders_count }}</span> Orders
                                        </p>
                                    </div>
                                    <span>Rp{{ number_format($user->orders_sum_total_amount ?? 0) }}</span>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        {{-- TOP CUSTOMERS END --}}
    </div>

    <div class="row">
        {{-- LATEST FINALISED ORDER START --}}
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-body p-0">
                    <h6 class="m-3 font-weight-bold text-dark">Latest Finalised Order</h6>
                    <table class="table m-0">
                        <tbody>
                        <tr>
                            <td>{{ now() }}</td>
                            <td>Dummy Text</td>
                            <td>YES - NO</td>
                            <td>Rp. 50,000</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        {{-- LATEST FINALISED ORDER END --}}
        {{-- DAILY ORDER HEALTH CHECK START --}}
{{--        <div class="col-12">--}}
{{--            <div class="card shadow mb-4">--}}
{{--                <div class="card-body p-0">--}}
{{--                    <h6 class="m-3 font-weight-bold text-dark">Daily Order Health Check for {{ now()->format('d M Y') }}</h6>--}}
{{--                    <div id="dailyOrderHealthCheck"></div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
        {{-- DAILY ORDER HEALTH CHECK END --}}
    </div>

{{--    <div class="row">--}}
{{--        --}}{{-- LATEST WEBSITE SYNC START --}}
{{--        <div class="col-12 col-md-6">--}}
{{--            <div class="card shadow mb-4">--}}
{{--                <div class="card-body">--}}
{{--                    <h6 class=" font-weight-bold text-dark">Latest Website Sync</h6>--}}
{{--                    <p>-</p>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--        --}}{{-- LATEST WEBSITE SYNC END --}}
{{--        --}}{{-- LATEST ELASTICSEARCH SYNC START --}}
{{--        <div class="col-12 col-md-6">--}}
{{--            <div class="card shadow mb-4">--}}
{{--                <div class="card-body">--}}
{{--                    <h6 class=" font-weight-bold text-dark">Latest Elasticsearch Sync</h6>--}}
{{--                    <p>-</p>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--        --}}{{-- LATEST ELASTICSEARCH SYNC END --}}
{{--        --}}{{-- LATEST SCHEDULER START --}}
{{--        <div class="col-12 col-md-6">--}}
{{--            <div class="card shadow mb-4">--}}
{{--                <div class="card-body">--}}
{{--                    <h6 class=" font-weight-bold text-dark">Scheduler</h6>--}}
{{--                    <p>-</p>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--        --}}{{-- LATEST SCHEDULER END --}}
{{--        --}}{{-- LATEST QUEUE PROCESS START --}}
{{--        <div class="col-12 col-md-6">--}}
{{--            <div class="card shadow mb-4">--}}
{{--                <div class="card-body">--}}
{{--                    <h6 class=" font-weight-bold text-dark">Queue Process</h6>--}}
{{--                    <p>-</p>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--        --}}{{-- LATEST QUEUE PROCESS END --}}
{{--        --}}{{-- LATEST CURRENT NUMBER OF PEOPLE ON SITE START --}}
{{--        <div class="col-12 col-md-6">--}}
{{--            <div class="card shadow mb-4">--}}
{{--                <div class="card-body">--}}
{{--                    <h6 class=" font-weight-bold text-dark">Current Number of People on Site</h6>--}}
{{--                    <p>-</p>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--        --}}{{-- LATEST CURRENT NUMBER OF PEOPLE ON SITE END --}}
{{--        --}}{{-- LATEST LAST IMPORT ERROR START --}}
{{--        <div class="col-12 col-md-6">--}}
{{--            <div class="card shadow mb-4">--}}
{{--                <div class="card-body">--}}
{{--                    <h6 class=" font-weight-bold text-dark">Last Import Error</h6>--}}
{{--                    <p>-</p>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--        --}}{{-- LATEST LAST IMPORT ERROR END --}}
{{--    </div>--}}

    <div class="row">
        {{-- CURRENT DEALS START --}}
{{--        <div class="col-12 col-md-12">--}}
{{--            <div class="card shadow mb-4">--}}
{{--                <div class="card-header py-3">--}}
{{--                    <h6 class="m-0 font-weight-bold text-dark">Current Deals</h6>--}}
{{--                </div>--}}
{{--                <div class="card-body p-0">--}}
{{--                    <table class="table m-0">--}}
{{--                        <thead>--}}
{{--                            <tr>--}}
{{--                                <th>&nbsp;</th>--}}
{{--                                <th>START DATE</th>--}}
{{--                                <th>END DATE</th>--}}
{{--                                <th>STATUS</th>--}}
{{--                            </tr>--}}
{{--                        </thead>--}}
{{--                        <tbody>--}}
{{--                            <tr>--}}
{{--                                <td>Dummy Text</td>--}}
{{--                                <td>{{ now() }}</td>--}}
{{--                                <td>{{ now()->addDay() }}</td>--}}
{{--                                <td>Successful</td>--}}
{{--                            </tr>--}}
{{--                        </tbody>--}}
{{--                    </table>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
        {{-- CURRENT DEALS END --}}
        {{-- CURRENT COUPONS START --}}
        <div class="col-12 col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Current Coupons</h6>
                    <a href="{{ route('coupon.index') }}">View Coupons</a>
                </div>
                <div class="card-body p-0">
                    <table class="table m-0">
                        <thead>
                        <tr>
                            <th>COUPON NAME</th>
                            <th>USAGE</th>
                            <th>START DATE</th>
                            <th>END DATE</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($currentCoupons as $coupon)
                        <tr>
                            <td>{{ $coupon->coupons_name }}</td>
                            <td>{{ $coupon->orders_count }}</td>
                            <td>{{ $coupon->start_date }}</td>
                            <td>{{ $coupon->end_date }}</td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        {{-- CURRENT COUPONS END --}}
    </div>
@endsection

@push('scripts')
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
{{-- pie chart --}}
<script type="text/javascript">
  var analytics = <?php echo $users; ?>

  google.charts.load('current', {'packages':['corechart']});
  google.charts.setOnLoadCallback(drawChart);

  function drawChart()
  {
      var data = google.visualization.arrayToDataTable(analytics);
      var options = {
          title : 'Last 7 Days registered user'
      };
      var chart = new google.visualization.PieChart(document.getElementById('pie_chart'));
      chart.draw(data, options);
  }
</script>
  {{-- line chart --}}
  <script type="text/javascript">
    const url = "{{route('product.order.income')}}";
    // Set new default font family and font color to mimic Bootstrap's default styling
    Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
    Chart.defaults.global.defaultFontColor = '#858796';

    function number_format(number, decimals, dec_point, thousands_sep) {
      // *     example: number_format(1234.56, 2, ',', ' ');
      // *     return: '1 234,56'
      number = (number + '').replace(',', '').replace(' ', '');
      var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function(n, prec) {
          var k = Math.pow(10, prec);
          return '' + Math.round(n * k) / k;
        };
      // Fix for IE parseFloat(0.55).toFixed(0) = 0;
      s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
      if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
      }
      if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
      }
      return s.join(dec);
    }

      // Area Chart Example
      var ctx = document.getElementById("myAreaChart");

        axios.get(url)
              .then(function (response) {
                const data_keys = Object.keys(response.data);
                const data_values = Object.values(response.data);
                var myLineChart = new Chart(ctx, {
                  type: 'line',
                  data: {
                    labels: data_keys, // ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
                    datasets: [{
                      label: "Earnings",
                      lineTension: 0.3,
                      backgroundColor: "rgba(78, 115, 223, 0.05)",
                      borderColor: "rgba(78, 115, 223, 1)",
                      pointRadius: 3,
                      pointBackgroundColor: "rgba(78, 115, 223, 1)",
                      pointBorderColor: "rgba(78, 115, 223, 1)",
                      pointHoverRadius: 3,
                      pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                      pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                      pointHitRadius: 10,
                      pointBorderWidth: 2,
                      data:data_values,// [0, 10000, 5000, 15000, 10000, 20000, 15000, 25000, 20000, 30000, 25000, 40000],
                    }],
                  },
                  options: {
                    maintainAspectRatio: false,
                    layout: {
                      padding: {
                        left: 10,
                        right: 25,
                        top: 25,
                        bottom: 0
                      }
                    },
                    scales: {
                      xAxes: [{
                        time: {
                          unit: 'date'
                        },
                        gridLines: {
                          display: false,
                          drawBorder: false
                        },
                        ticks: {
                          maxTicksLimit: 7
                        }
                      }],
                      yAxes: [{
                        ticks: {
                          maxTicksLimit: 5,
                          padding: 10,
                          // Include a dollar sign in the ticks
                          callback: function(value, index, values) {
                            return 'Rp. ' + number_format(value);
                          }
                        },
                        gridLines: {
                          color: "rgb(234, 236, 244)",
                          zeroLineColor: "rgb(234, 236, 244)",
                          drawBorder: false,
                          borderDash: [2],
                          zeroLineBorderDash: [2]
                        }
                      }],
                    },
                    legend: {
                      display: false
                    },
                    tooltips: {
                      backgroundColor: "rgb(255,255,255)",
                      bodyFontColor: "#858796",
                      titleMarginBottom: 10,
                      titleFontColor: '#6e707e',
                      titleFontSize: 14,
                      borderColor: '#dddfeb',
                      borderWidth: 1,
                      xPadding: 15,
                      yPadding: 15,
                      displayColors: false,
                      intersect: false,
                      mode: 'index',
                      caretPadding: 10,
                      callbacks: {
                        label: function(tooltipItem, chart) {
                          var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                          return datasetLabel + ': Rp. ' + number_format(tooltipItem.yLabel);
                        }
                      }
                    }
                  }
                });
              })
              .catch(function (error) {
              //   vm.answer = 'Error! Could not reach the API. ' + error
              console.log(error)
              });

  </script>
@endpush
