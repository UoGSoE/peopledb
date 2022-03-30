@extends('layouts.app')

@section('content')

<h3 class="title is-3">Stats and Trends</h3>

<div class="columns">
    <div class="column">
        <div id="staffovertime">
        </div>
    </div>
    <div class="column">
        <div id="currentpercents">
        </div>
    </div>
</div>
<div class="columns">
    <div class="column">
        <div id="groupsovertime">
        </div>
    </div>
    <div class="column">
        <div id="groupsbars">
        </div>
    </div>
</div>
<div class="columns">
    <div class="column">
        <div id="groupstreemap">
        </div>
    </div>
</div>
<h5 class="title is-5">Change over the last year</h5>
<div class="columns">
    <div class="column">
        <div id="growthtotal"></div>
    </div>
    <div class="column">
        <div id="growthacademics"></div>
    </div>
    <div class="column">
        <div id="growthphds"></div>
    </div>
    <div class="column">
        <div id="growthmpas"></div>
    </div>
    <div class="column">
        <div id="growthtechnicians"></div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>

function getRandomishNumbers(count, seed = null) {
    if (! seed) {
        seed = Math.floor(Math.random() * (100 - 1) + 1);
    }
    var numbers = [];
    for (var i = 0; i < count; i++) {
        numbers.push(seed + Math.floor(Math.random() * (70 - 1) + 1));
    }
    return numbers;
}

var options = {
          series: [
              {
                name: "Academics",
                data: getRandomishNumbers(9, 250)
              },
              {
                name: "PhDs",
                data: getRandomishNumbers(9, 400)
              },
              {
                name: "MPAs",
                data: getRandomishNumbers(9, 70)
              },
              {
                name: "Technicians",
                data: getRandomishNumbers(9, 60)
              },
        ],
          chart: {
        //   height: 350,
          type: 'line',
          zoom: {
            enabled: false
          }
        },
        dataLabels: {
          enabled: false
        },
        stroke: {
          curve: 'straight'
        },
        title: {
          text: 'Number of people over time',
          align: 'left'
        },
        grid: {
          row: {
            colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
            opacity: 0.5
          },
        },
        xaxis: {
          categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep'],
        }
        };

        var chart = new ApexCharts(document.querySelector("#staffovertime"), options);
        chart.render();

        var options = {
          series: [250, 400, 70, 60],
          chart: {
        //   width: 380,
          type: 'pie',
        },
        labels: ['Academics', 'PhDs', 'MPAs', 'Technicians'],
        responsive: [{
          breakpoint: 480,
          options: {
            chart: {
            //   width: 200
            },
            legend: {
              position: 'bottom'
            }
          }
        }]
        };

        var chart = new ApexCharts(document.querySelector("#currentpercents"), options);
        chart.render();

        var options = {
          series: [
              {
                name: "Civil",
                data: getRandomishNumbers(9, 150)
              },
              {
                name: "Bio",
                data: getRandomishNumbers(9, 200)
              },
              {
                name: "Nano",
                data: getRandomishNumbers(9, 130)
              },
              {
                name: "Aero",
                data: getRandomishNumbers(9, 60)
              },
              {
                name: "Mech",
                data: getRandomishNumbers(9, 100)
              },
        ],
          chart: {
        //   height: 350,
          type: 'line',
          zoom: {
            enabled: false
          }
        },
        dataLabels: {
          enabled: false
        },
        stroke: {
          curve: 'straight'
        },
        title: {
          text: 'Group membership over time',
          align: 'left'
        },
        grid: {
          row: {
            colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
            opacity: 0.5
          },
        },
        xaxis: {
          categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep'],
        }
        };

        var chart = new ApexCharts(document.querySelector("#groupsovertime"), options);
        chart.render();

        var options = {
          series: [{
          data: [400, 200, 350, 540, 170]
        }],
          chart: {
          type: 'bar',
        //   height: 350
        },
        plotOptions: {
          bar: {
            borderRadius: 4,
            horizontal: true,
          }
        },
        dataLabels: {
          enabled: false
        },
        xaxis: {
          categories: ['Civil', 'Bio', 'Nano', 'Aero', 'Mech'],
        }
        };

        var chart = new ApexCharts(document.querySelector("#groupsbars"), options);
        chart.render();

        var options = {
          series: [
          {
            name: 'Civil',
            data: [
              {
                x: 'Academics',
                y: 10
              },
              {
                x: 'PhDs',
                y: 60
              },
              {
                x: 'RAs',
                y: 41
              }
            ]
          },
          {
            name: 'Nano',
            data: [
              {
                x: 'Academics',
                y: 10
              },
              {
                x: 'PhDs',
                y: 20
              },
              {
                x: 'RAs',
                y: 51
              },
            ]
          },
          {
            name: 'Bio',
            data: [
              {
                x: 'Academics',
                y: 20
              },
              {
                x: 'PhDs',
                y: 55
              },
              {
                x: 'RAs',
                y: 32
              },
            ]
          },
          {
            name: 'Aero',
            data: [
              {
                x: 'Academics',
                y: 40
              },
              {
                x: 'PhDs',
                y: 95
              },
              {
                x: 'RAs',
                y: 37
              },
            ]
          },
          {
            name: 'Mech',
            data: [
              {
                x: 'Academics',
                y: 80
              },
              {
                x: 'PhDs',
                y: 120
              },
              {
                x: 'RAs',
                y: 57
              },
            ]
          },
        ],
          legend: {
          show: true
        },
        chart: {
        //   height: 350,
          type: 'treemap'
        },
        title: {
          text: 'Groups Breakdown',
          align: 'center'
        }
        };

        var chart = new ApexCharts(document.querySelector("#groupstreemap"), options);
        chart.render();

        var options = {
          series: [70],
          chart: {
          height: 250,
          type: 'radialBar',
        },

        plotOptions: {
          radialBar: {
            hollow: {
              size: '70%',
            }
          },
        },
        labels: ['Total'],
        };

        var chart = new ApexCharts(document.querySelector("#growthtotal"), options);
        chart.render();

        var options = {
          series: [20],
          chart: {
          height: 250,
          type: 'radialBar',
        },

        plotOptions: {
          radialBar: {
            hollow: {
              size: '70%',
            }
          },
        },
        labels: ['Academics'],
        };

        var chart = new ApexCharts(document.querySelector("#growthacademics"), options);
        chart.render();

        var options = {
          series: [60],
          chart: {
          height: 250,
          type: 'radialBar',
        },

        plotOptions: {
          radialBar: {
            hollow: {
              size: '70%',
            }
          },
        },
        labels: ['PhDs'],
        };

        var chart = new ApexCharts(document.querySelector("#growthphds"), options);
        chart.render();

        var options = {
          series: [10],
          chart: {
          height: 250,
          type: 'radialBar',
        },

        plotOptions: {
          radialBar: {
            hollow: {
              size: '70%',
            }
          },
        },
        labels: ['MPAs'],
        };

        var chart = new ApexCharts(document.querySelector("#growthmpas"), options);
        chart.render();

        var options = {
          series: [8],
          chart: {
          height: 250,
          type: 'radialBar',
        },

        plotOptions: {
          radialBar: {
            hollow: {
              size: '70%',
            }
          },
        },
        labels: ['Technicians'],
        };

        var chart = new ApexCharts(document.querySelector("#growthtechnicians"), options);
        chart.render();

</script>
@endpush
