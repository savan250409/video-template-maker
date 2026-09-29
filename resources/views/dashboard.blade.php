@extends('layout.master')

@push('plugin-styles')
    <link href="{{ asset('assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet" />
@endpush

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Welcome to Dashboard, {{ Auth()->user()->name }}</h4>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-xl-12 stretch-card">
            <div class="row flex-grow-1">
                @can('animated-template-list')
                    <div class="col-md-4 grid-margin stretch-card">
                        <div class="card" style="border-radius:10px;">
                            <a href="{{ Route('animated-template.index') }}" style="color:unset;">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between">
                                        <div class="my-auto">
                                            <div class="align-items-baseline mb-2">
                                                <h6 class="card-title mb-0" style="font-size:smaller;">Video Template</h6>
                                            </div>
                                            <div class="">
                                                <h3 class="mb-2" style="font-weight:700;">{{ $totalAnimatedTemplates }}</h3>
                                            </div>
                                        </div>
                                        <div class="my-auto">
                                            <img src="{{ asset('icons/video templates.svg') }}" />
                                        </div>

                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                @endcan
                @can('template-category-list')
                    <div class="col-md-4 grid-margin stretch-card">
                        <div class="card" style="border-radius:10px;">
                            <a href="{{ Route('template-category.index') }}" style="color:unset;">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between">
                                        <div class="my-auto">
                                            <div class="align-items-baseline mb-2">
                                                <h6 class="card-title mb-0" style="font-size:smaller;">Video Template Category
                                                </h6>
                                            </div>
                                            <div class="">
                                                <h3 class="mb-2" style="font-weight:700;">
                                                    {{ $totalAimatedTemplateCategories }}</h3>
                                            </div>
                                        </div>
                                        <div class="my-auto">
                                            <img src="{{ asset('icons/video cat.svg') }}" />
                                        </div>

                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                @endcan
                @can('musics-list')
                    <div class="col-md-4 grid-margin stretch-card">
                        <div class="card" style="border-radius:10px;">
                            <a href="{{ Route('musics.index') }}" style="color:unset;">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between">
                                        <div class="my-auto">
                                            <div class="align-items-baseline mb-2">
                                                <h6 class="card-title mb-0" style="font-size:smaller;">Music</h6>
                                            </div>
                                            <div class="">
                                                <h3 class="mb-2" style="font-weight:700;">{{ $totalMusics }}</h3>
                                            </div>
                                        </div>
                                        <div class="my-auto">
                                            <img src="{{ asset('icons/All Music.svg') }}" />
                                        </div>

                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                @endcan
                @can('music-category-list')
                    <div class="col-md-4 grid-margin stretch-card">
                        <div class="card" style="border-radius:10px;">
                            <a href="{{ Route('music-category.index') }}" style="color:unset;">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between">
                                        <div class="my-auto">
                                            <div class="align-items-baseline mb-2">
                                                <h6 class="card-title mb-0" style="font-size:smaller;">Music Category</h6>
                                            </div>
                                            <div class="">
                                                <h3 class="mb-2" style="font-weight:700;">{{ $totalMusicCategories }}</h3>
                                            </div>
                                        </div>
                                        <div class="my-auto">
                                            <img src="{{ asset('icons/Music cat.svg') }}" />
                                        </div>

                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                @endcan
                @can('banners-list')
                    <div class="col-md-4 grid-margin stretch-card">
                        <div class="card" style="border-radius:10px;">
                            <a href="{{ Route('banners.index') }}" style="color:unset;">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between">
                                        <div class="my-auto">
                                            <div class="align-items-baseline mb-2">
                                                <h6 class="card-title mb-0" style="font-size:smaller;">Banner</h6>
                                            </div>
                                            <div class="">
                                                <h3 class="mb-2" style="font-weight:700;">{{ $totalBanners }}</h3>
                                            </div>
                                        </div>
                                        <div class="my-auto">
                                            <img src="{{ asset('icons/banner.svg') }}" />
                                        </div>

                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                @endcan
                @can('report-list')
                    <div class="col-md-4 grid-margin stretch-card">
                        <div class="card" style="border-radius:10px;">
                            <a href="{{ url('admin/reported') }}" style="color:unset;">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between">
                                        <div class="my-auto">
                                            <div class="align-items-baseline mb-2">
                                                <h6 class="card-title mb-0" style="font-size:smaller;">Report</h6>
                                            </div>
                                            <div class="">
                                                <h3 class="mb-2" style="font-weight:700;">{{ $totalReports }}</h3>
                                            </div>
                                        </div>
                                        <div class="my-auto">
                                            <img src="{{ asset('icons/Report.svg') }}" />
                                        </div>

                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                @endcan


            </div>
        </div>
    </div> <!-- row -->

    <div class="row">
        <div class="col-12 col-xl-12 grid-margin stretch-card">
            <div class="card overflow-hidden">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-baseline mb-4 mb-md-3">
                        <h6 class="card-title mb-0">User Analytics</h6>
                        <div class="dropdown">
                            <button class="btn p-0" type="button" id="dropdownMenuButton3" data-bs-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                            </button>
                        </div>
                    </div>
                    <div class="row align-items-start mb-2">
                        <div class="col-md-7">
                        </div>
                        <div class="col-md-5 d-flex justify-content-md-end">
                            <div class="btn-group mb-3 mb-md-0" role="group" aria-label="Basic example">
                                <button type="button" class="btn btn-outline-primary"
                                    onclick="graphFilter('today', this)" id="today-btn">Days</button>
                                <!--<button type="button" class="btn btn-outline-primary d-none d-md-block" onclick="graphFilter('week', this)" id="week-btn">Week</button>-->
                                <button type="button" class="btn btn-primary" onclick="graphFilter('month', this)"
                                    id="month-btn">Month</button>
                                <button type="button" class="btn btn-outline-primary"
                                    onclick="graphFilter('year', this)" id="year-btn">Year</button>
                            </div>
                        </div>
                    </div>
                    <div id="user-current-year-analytic" class="user-year-graph-model"></div>
                    <div id="user-current-month-analytic" class="user-month-graph-model"></div>
                    <div id="user-current-week-analytic" class="user-week-graph-model"></div>
                    <div id="user-current-day-analytic" class="user-day-graph-model"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('plugin-scripts')
    <script src="{{ asset('assets/plugins/chartjs/chart.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/jquery.flot/jquery.flot.js') }}"></script>
    <script src="{{ asset('assets/plugins/jquery.flot/jquery.flot.resize.js') }}"></script>
    <script src="{{ asset('assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/progressbar-js/progressbar.min.js') }}"></script>
@endpush

@push('custom-scripts')
    <!--<script src="{{ asset('assets/js/dashboard.js') }}"></script>-->
    <script src="{{ asset('assets/js/datepicker.js') }}"></script>
    <script>
        var fontFamily = "'Roboto', Helvetica, sans-serif"

        if ($('#user-current-month-analytic').length) {
            var options = {
                chart: {
                    type: 'bar',
                    height: '320',
                    parentHeightOffset: 0,
                    foreColor: "#000",
                    background: "#fff",
                    toolbar: {
                        show: false
                    },
                },
                theme: {
                    mode: 'light'
                },
                tooltip: {
                    theme: 'light'
                },
                colors: ["#6571ff"],
                grid: {
                    padding: {
                        bottom: -4
                    },
                    borderColor: "rgba(77, 138, 240, .15)",
                    xaxis: {
                        lines: {
                            show: true
                        }
                    }
                },
                series: [{
                    name: 'sales',
                    data: [
                        @foreach ($userCurrentMonthAnalytic as $month)
                            {{ $month['counts'] }},
                        @endforeach
                    ]
                }],
                xaxis: {
                    type: 'category',
                    categories: [
                        @foreach ($userCurrentMonthAnalytic as $monthDate)
                            "{{ $monthDate['dates'] }}",
                        @endforeach
                    ],
                    axisBorder: {
                        color: "rgba(77, 138, 240, .15)",
                    },
                    axisTicks: {
                        color: "rgba(77, 138, 240, .15)",
                    },
                },
                legend: {
                    show: true,
                    position: "top",
                    horizontalAlign: 'center',
                    fontFamily: fontFamily,
                    itemMargin: {
                        horizontal: 8,
                        vertical: 0
                    },
                },
                stroke: {
                    width: 0
                },
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        columnWidth: '10%',
                    }
                }
            }
            var apexBarChart = new ApexCharts(document.querySelector("#user-current-month-analytic"), options);
            apexBarChart.render();
        }

        let todayBtn = $('#today-btn');
        let weekBtn = $('#week-btn');
        let monthBtn = $('#month-btn');
        let yearBtn = $('#year-btn');

        let graphModelYear = $('#user-current-year-analytic');
        let graphModelMonth = $('#user-current-month-analytic');
        let graphModelWeek = $('#user-current-week-analytic');
        let graphModelDay = $('#user-current-day-analytic');

        function graphFilter(type, element) {
            todayBtn.removeClass('btn-primary');
            weekBtn.removeClass('btn-primary');
            monthBtn.removeClass('btn-primary');
            yearBtn.removeClass('btn-primary');

            todayBtn.addClass('btn-outline-primary');
            weekBtn.addClass('btn-outline-primary');
            monthBtn.addClass('btn-outline-primary');
            yearBtn.addClass('btn-outline-primary');

            element.classList.add('btn-primary');
            element.classList.remove('btn-outline-primary');


            graphModelYear.addClass('d-none');
            graphModelMonth.addClass('d-none');
            graphModelWeek.addClass('d-none');
            graphModelDay.addClass('d-none');

            if (type == 'today') {
                graphModelDay.empty();
                graphModelDay.removeClass('d-none');
                initializeTodayChart();
            } else if (type == 'week') {
                graphModelWeek.empty();
                graphModelWeek.removeClass('d-none');
                initializeWeekChart();
            } else if (type == 'month') {
                graphModelMonth.empty();
                graphModelMonth.removeClass('d-none');
                initializeMonthChart();
            } else if (type == 'year') {
                graphModelYear.empty();
                graphModelYear.removeClass('d-none');
                initializeYearChart();
            }

        }




        function initializeMonthChart() {
            var options = {
                chart: {
                    type: 'bar',
                    height: '320',
                    parentHeightOffset: 0,
                    foreColor: "#000",
                    background: "#fff",
                    toolbar: {
                        show: false
                    },
                },
                theme: {
                    mode: 'light'
                },
                tooltip: {
                    theme: 'light'
                },
                colors: ["#6571ff"],
                grid: {
                    padding: {
                        bottom: -4
                    },
                    borderColor: "rgba(77, 138, 240, .15)",
                    xaxis: {
                        lines: {
                            show: true
                        }
                    }
                },
                series: [{
                    name: 'sales',
                    data: [
                        @foreach ($userCurrentMonthAnalytic as $month)
                            {{ $month['counts'] }},
                        @endforeach
                    ]
                }],
                xaxis: {
                    type: 'category',
                    categories: [
                        @foreach ($userCurrentMonthAnalytic as $monthDate)
                            "{{ $monthDate['dates'] }}",
                        @endforeach
                    ],
                    axisBorder: {
                        color: "rgba(77, 138, 240, .15)",
                    },
                    axisTicks: {
                        color: "rgba(77, 138, 240, .15)",
                    },
                },
                legend: {
                    show: true,
                    position: "top",
                    horizontalAlign: 'center',
                    fontFamily: fontFamily,
                    itemMargin: {
                        horizontal: 8,
                        vertical: 0
                    },
                },
                stroke: {
                    width: 0
                },
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        columnWidth: '30%',
                    }
                }
            }
            var apexBarChart = new ApexCharts(document.querySelector("#user-current-month-analytic"), options);
            apexBarChart.render();
        }

        function initializeYearChart() {
            var options = {
                chart: {
                    type: 'bar',
                    height: '320',
                    parentHeightOffset: 0,
                    foreColor: "#000",
                    background: "#fff",
                    toolbar: {
                        show: false
                    },
                },
                theme: {
                    mode: 'light'
                },
                tooltip: {
                    theme: 'light'
                },
                colors: ["#6571ff"],
                grid: {
                    padding: {
                        bottom: -4
                    },
                    borderColor: "rgba(77, 138, 240, .15)",
                    xaxis: {
                        lines: {
                            show: true
                        }
                    }
                },
                series: [{
                    name: 'sales',
                    data: [
                        @foreach ($userAnalytic as $key => $analytic)
                            {{ $analytic['counts'] }},
                        @endforeach
                    ]
                }],
                xaxis: {
                    type: 'category',
                    categories: [
                        @foreach ($userAnalytic as $key => $analytic)
                            '{{ $analytic['dates'] }}',
                        @endforeach
                    ],
                    axisBorder: {
                        color: "rgba(77, 138, 240, .15)",
                    },
                    axisTicks: {
                        color: "rgba(77, 138, 240, .15)",
                    },
                },
                legend: {
                    show: true,
                    position: "top",
                    horizontalAlign: 'center',
                    fontFamily: fontFamily,
                    itemMargin: {
                        horizontal: 8,
                        vertical: 0
                    },
                },
                stroke: {
                    width: 0
                },
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        columnWidth: '10%',
                    }
                }
            }
            var apexBarChart = new ApexCharts(document.querySelector("#user-current-year-analytic"), options);
            apexBarChart.render();
        }

        function initializeWeekChart() {
            var lineChartOptions = {
                chart: {
                    type: "bar",
                    height: '400',
                    parentHeightOffset: 0,
                    foreColor: "#000",
                    background: "#fff",
                    toolbar: {
                        show: false
                    },
                },
                theme: {
                    mode: 'light'
                },
                tooltip: {
                    theme: 'light'
                },
                colors: ["#6571ff", "#ff3366", "#fbbc06"],
                grid: {
                    padding: {
                        bottom: -4,
                    },
                    borderColor: "rgba(77, 138, 240, .15)",
                    xaxis: {
                        lines: {
                            show: true
                        }
                    }
                },
                series: [{
                    name: "users",
                    data: [
                        @foreach ($userCurrentWeekAnalytic as $week)
                            {{ $week['counts'] }},
                        @endforeach
                    ]
                }, ],
                xaxis: {
                    type: "category",
                    categories: [
                        @foreach ($userCurrentWeekAnalytic as $weekDate)
                            "{{ $weekDate['dates'] }}",
                        @endforeach
                    ],
                    lines: {
                        show: true
                    },
                    axisBorder: {
                        color: "rgba(77, 138, 240, .15)",
                    },
                    axisTicks: {
                        color: "rgba(77, 138, 240, .15)",
                    },
                    crosshairs: {
                        stroke: {
                            color: "#7987a1",
                        },
                    },
                },
                yaxis: {
                    title: {
                        text: 'Total Users',
                        style: {
                            size: 9,
                            color: "#7987a1"
                        }
                    },
                    tickAmount: 4,
                    tooltip: {
                        enabled: true
                    },
                    crosshairs: {
                        stroke: {
                            color: "#7987a1",
                        },
                    },
                },
                markers: {
                    size: 0,
                },
                stroke: {
                    width: 2,
                    curve: "straight",
                },
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        columnWidth: '10%',
                    }
                },
            };
            var apexLineChart = new ApexCharts(document.querySelector("#user-current-week-analytic"), lineChartOptions);
            apexLineChart.render();
        }

        function initializeTodayChart() {
            var lineChartOptions = {
                chart: {
                    type: "bar",
                    height: '400',
                    parentHeightOffset: 0,
                    foreColor: "#000",
                    background: "#fff",
                    toolbar: {
                        show: false
                    },
                },
                theme: {
                    mode: 'light'
                },
                tooltip: {
                    theme: 'light'
                },
                colors: ["#6571ff", "#ff3366", "#fbbc06"],
                grid: {
                    padding: {
                        bottom: -4,
                    },
                    borderColor: "rgba(77, 138, 240, .15)",
                    xaxis: {
                        lines: {
                            show: true
                        }
                    }
                },
                series: [{
                    name: "users",
                    data: [
                        @foreach ($userLast30DaysAnalytic as $thirtyDay)
                            {{ $thirtyDay['counts'] }},
                        @endforeach
                    ]
                }, ],
                xaxis: {
                    type: "category",
                    categories: [
                        @foreach ($userLast30DaysAnalytic as $thirtyDay)
                            "{{ $thirtyDay['dates'] }}",
                        @endforeach
                    ],
                    lines: {
                        show: true
                    },
                    axisBorder: {
                        color: "rgba(77, 138, 240, .15)",
                    },
                    axisTicks: {
                        color: "rgba(77, 138, 240, .15)",
                    },
                    crosshairs: {
                        stroke: {
                            color: "#7987a1",
                        },
                    },
                },
                yaxis: {
                    title: {
                        text: 'Total Users',
                        style: {
                            size: 9,
                            color: "#7987a1"
                        }
                    },
                    tickAmount: 4,
                    tooltip: {
                        enabled: true
                    },
                    crosshairs: {
                        stroke: {
                            color: "#7987a1",
                        },
                    },
                },
                markers: {
                    size: 0,
                },
                stroke: {
                    width: 2,
                    curve: "straight",
                },
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        columnWidth: '10%',
                    }
                },
            };
            var apexLineChart = new ApexCharts(document.querySelector("#user-current-day-analytic"), lineChartOptions);
            apexLineChart.render();
        }
    </script>
@endpush
