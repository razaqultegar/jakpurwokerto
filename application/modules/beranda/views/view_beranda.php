<div class="row">
  <div class="col-xl-3 col-md-6 mb-4">
    <div class="card border-left-warning shadow h-100 py-2">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-gray-800 text-uppercase mb-1">Jumlah Anggota</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_anggota?></div>
          </div>
          <div class="col-auto">
            <i class="fas fa-users fa-2x text-gray-300"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6 mb-4">
    <div class="card border-left-primary shadow h-100 py-2">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-gray-800 text-uppercase mb-1">Jumlah Laki-laki</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_laki?></div>
          </div>
          <div class="col-auto">
            <i class="fas fa-mars fa-2x text-gray-300"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6 mb-4">
    <div class="card border-left-danger shadow h-100 py-2">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-gray-800 text-uppercase mb-1">Jumlah Perempuan</div>
            <div class="row no-gutters align-items-center">
              <div class="col-auto">
                <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800"><?php echo $total_perempuan?></div>
              </div>
            </div>
          </div>
          <div class="col-auto">
            <i class="fas fa-venus fa-2x text-gray-300"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6 mb-4">
    <div class="card border-left-success shadow h-100 py-2">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-uppercase mb-1">Jumlah Kas</div>
            <div class="h5 mb-0 font-weight-bold"><?php echo $this->format_angka($total_kas); ?></div>
          </div>
          <div class="col-auto">
            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-xl-6 col-lg-6">
    <div class="card shadow mb-4">
      <div class="card-body">
        <div id="usia-bar"></div>
      </div>
    </div>
  </div>
  <div class="col-xl-6 col-lg-6">
    <div class="card shadow mb-4">
      <div class="card-body">
        <div id="anggota"></div>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-xl-6 col-lg-6">
    <div class="card shadow mb-4">
      <div class="card-body">
        <div id="pendidikan"></div>
      </div>
    </div>
  </div>
  <div class="col-xl-6 col-lg-6">
    <div class="card shadow mb-4">
      <div class="card-body">
        <div id="pekerjaan"></div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
var date = new Date();
var yearNow = date.getFullYear();
var total_anggota = '<?php echo $total_anggota?>';
graph= $(function () {
  $.ajax({
    type: "GET",
    url: '<?php echo $json_agt?>',
    dataType: "json",
    contentType: "application/json",
    crossDomain: true,
    success: function (data) {
      var d = data;
      var name = Array();
      var data = Array();
      var dataArrayFinal = Array();
      for(i=0;i<d.length;i++) { 
        name[i] = d[i].name; 
        data[i] = d[i].data;  
      }
      for(j=0;j<name.length;j++) { 
        var temp = new Array(name[j],data[j]); 
        dataArrayFinal[j] = temp;     
      }
      var chart = new Highcharts.Chart({
        chart: {
          renderTo: 'anggota',
          type: 'column',
        },
        colors: ['#4e73df', '#e74a3b'],
        title: {
          text: 'Pertambahan Anggota'
        },
        subtitle: {
          text: 'Total Anggota : '+ total_anggota
        },
        plotOptions: {
          column: {
            depth: 25,
          }
        },
        xAxis: {
          categories: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des']
        },
        yAxis: {
          title: {
            text: 'Jumlah'
          }
        },
        credits: {
          enabled: false
        },
        series: d
      });
      function showValues() {
        $('#alpha-value').html(chart.options.chart.options3d.alpha);
        $('#beta-value').html(chart.options.chart.options3d.beta);
        $('#depth-value').html(chart.options.chart.options3d.depth);
      }
      $('#sliders input').on('input change', function () {
        chart.options.chart.options3d[this.id] = this.value;
        showValues();
        chart.redraw(false);
      });
      showValues();
    }
  });
});
$(function () {
  var categories = ['0-5', '5-17', '17-30', '30-60', '60+'];
  $.ajax({
    type: "GET",
    url: '<?php echo $json_agt_usia?>',
    dataType: "json",
    contentType: "application/json",
    crossDomain: true,
    success: function (data) {
      var d = data;
      var name = Array();
      var data = Array();
      var dataArrayFinal = Array();
      for(i=0;i<d.length;i++) { 
        name[i] = d[i].name; 
        data[i] = d[i].data;  
      }
      for(j=0;j<name.length;j++) { 
        var temp = new Array(name[j],data[j]); 
        dataArrayFinal[j] = temp;     
      }
      var chart = new Highcharts.Chart({
        chart: {
          renderTo: 'usia-bar',
          type: 'bar',
        },
        colors: ['#4e73df', '#e74a3b'],
        title: {
          text: 'Anggota Berdasarkan Usia'
        },
        subtitle: {
          text: 'Total Anggota : '+ total_anggota
        },
        plotOptions: {
          column: {
            depth: 25,
          }
        },
        xAxis: [{
          categories: categories,
          reversed: false,
          labels: {
            step: 1
          }
        }, {
          opposite: true,
          reversed: false,
          categories: categories,
          linkedTo: 0,
          labels: {
            step: 1
          }
        }],
        yAxis: {
          title: {
            text: null
          },
        },
        credits: {
          enabled: false
        },
        plotOptions: {
          series: {
            stacking: 'normal'
          }
        },
        tooltip: {
          formatter: function () {
            return '<b>' + this.series.name + ', Umur ' + this.point.category + '</b><br/>' + 'Jumlah: ' + Highcharts.numberFormat(Math.abs(this.point.y), 0) + ' ('+Highcharts.numberFormat(Math.abs((this.point.y / total_anggota)*100),1)+'%)';
          }
        },
        series: d
      });
    }
  });
});
total_data_pendidikan = '<?php echo $total_pendidikan?>';
$(function () {
  $.ajax({
    type: "GET",
    url: '<?php echo $json_pendidikan?>',
    dataType: "json",
    contentType: "application/json",
    crossDomain: true,
    success: function (data) {
      var d = data
      var name = Array();
      var data = Array();
      var dataArrayFinal = Array();
      for(i=0;i<d.length;i++) { 
        name[i] = d[i].name; 
        data[i] = d[i].data;  
      }
      for(j=0;j<name.length;j++) { 
        var temp = new Array(name[j],data[j]); 
        dataArrayFinal[j] = temp;     
      }
      var chart = new Highcharts.Chart({
        chart: {
          renderTo: 'pendidikan',
          plotBackgroundColor: null,
          plotBorderWidth: null,
          plotShadow: false,
          type: 'pie',
        },
        colors: ['#90ed7d', '#80699B','#8085e9','#f7a35c','#434348','#ff8a80 ','#b2dfdb','#e6ee9c','#cfd8dc','#26a69a','#536dfe','#ff80ab','#880e4f'],
        title: {
          text: 'Pendidikan'
        },
        subtitle: {
          text: 'Total Data : '+ total_data_pendidikan
        },
        tooltip: {
          pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b><br>{point.y} Orang'
        },
        plotOptions: {
          pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            depth: 35,
            dataLabels: {
              enabled: true,
              format: '<b>{point.name}</b>: {point.percentage:.1f}%</b><br>{point.y} Orang',
              style: {
                color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
              }
            }
          }
        },
        credits: {
          enabled: false
        },
        series: [{
          name: 'Jumlah',
          colorByPoint: true,
          data: dataArrayFinal
        }]
      });
    }
  });    
});
$(function () {
  $.ajax({
    type: "GET",
    url: '<?php echo $json_pekerjaan?>',
    dataType: "json",
    contentType: "application/json",
    crossDomain: true,
    success: function (data) {
      var d = data
      var name = Array();
      var data = Array();
      var dataArrayFinal = Array();
      for(i=0;i<d.length;i++) { 
        name[i] = d[i].name; 
        data[i] = d[i].data;  
      }
      for(j=0;j<name.length;j++) { 
        var temp = new Array(name[j],data[j]); 
          dataArrayFinal[j] = temp;     
        }
        var chart = new Highcharts.Chart({
        chart: {
          type: 'bar',
          renderTo: 'pekerjaan',
          marginLeft: 150,
        },
        title: {
          text: 'Statistik Pekerjaan'
        },
        subtitle: {
          text: 'Jumlah Total Anggota : '+ total_anggota
        },
        tooltip: {
          formatter: function() {
            var pcnt = (this.point.y / total_anggota) * 100;
            return '<span style="font-size: 10px"> ' + this.point.name + '</span><br>Jumlah : <b>'+ Highcharts.numberFormat(pcnt,1) + '%</b>' + '<br>' + Highcharts.numberFormat(this.point.y, 0) + ' Orang';
          }
        },
        xAxis: {
          categories:name,
          title: {
            text: null
          },
          min: 0,
          max: 12,
          scrollbar: {
            enabled: true
          },
          tickLength: 0,
        },
        yAxis: {
          min: 0,
          endOnTick: true,
          maxPadding: 0.2,
          title: {
            text: 'Jumlah',
            align: 'high'
          }
        },
        plotOptions: {
          bar: {
            dataLabels: {
              enabled: true,
              formatter: function () {
                return Highcharts.numberFormat(Math.abs(this.y), 0) + ' ('+Highcharts.numberFormat(Math.abs((this.y / total_anggota)*100),1)+'%)';
              },
              style: {
                color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
              }
            }
          }
        },
        legend: {
          enabled: false
        },
        credits: {
          enabled: false
        },
        series: [{
          name: 'Jumlah',
          data: dataArrayFinal,
          colorByPoint: true,
          cursor: 'pointer'
        }]
      });
    }
  });  
});
</script>