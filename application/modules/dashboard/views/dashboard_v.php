<!-- if admin or HR -->
<?php if ($group == 1 || $group == 2) { ?>

  <input type="hidden" id="group" value="0">
  <?php $groupLog = 0; 

// if participant
} else { 
  // if assistant manager or higher
  if ($position_grade > 3 && $position_grade < 7) { ?>
    
    <input type="hidden" id="group" value="<?= $section ?>">
    <?php $groupLog = $section;
  // if manager and higher
  } elseif ($position_grade > 6) { ?>

    <input type="hidden" id="group" value="<?= $department ?>">
    <?php $groupLog = $department;
    
  } 
} ?>

<section class="content-header">
  <h1>
    Dashboard
    <small>Control panel</small>
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Dashboard</li>
  </ol>
</section>

<!-- Main content -->
<section class="content">

  <div class="row">
    
    <!-- information widget only show if any informations -->
    <?php $informations ? $this->load->view('information_widget') : NULL ?>

    <div class="col-lg-4 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-aqua">
        <div class="inner">
          <h3><?= $participants ?></h3>

          <p>Peserta Assessment</p>
        </div>
        <div class="icon">
          <i class="ion ion-bag"></i>
        </div>
        <a 
          href="<?= base_url('dashboard/ALL_PARTICIPANTS/status/'.$groupLog.'/section') ?>" 
          class="small-box-footer">Detail <i class="fa fa-arrow-circle-right"></i>
        </a>
      </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-4 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-red">
        <div class="inner">
          <small style="font-size: 20px" class="pull-right"><?= number_format($uncompletePercentage, 2) ?>%</small>
          <h3><?= $assessmentThatUncomplete ?></h3>
          <p>Belum Dinilai</p>
        </div>
        <div class="icon">
          <i class="ion ion-person-add"></i>
        </div>
        <a 
          href="<?= base_url('dashboard/UNCOMPLETE/status/'.$groupLog.'/section') ?>" 
          class="small-box-footer">Detail <i class="fa fa-arrow-circle-right"></i>
        </a>
      </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-4 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-green">
        <div class="inner">
          <small style="font-size: 20px" class="pull-right"><?= number_format($completePercentage, 2) ?>%</small>
          <h3><?= $completedAssessment ?></h3>
          <p>Sudah Dinilai </p>
        </div>
        <div class="icon">
          <i class="ion ion-stats-bars"></i>
        </div>
        <a 
          href="<?= base_url('dashboard/COMPLETE/status/'.$groupLog.'/section') ?>" 
          class="small-box-footer">Detail <i class="fa fa-arrow-circle-right"></i>
        </a>
      </div>
    </div>
  </div>
  <!-- /.row -->


  <!-- PieChart -->
  <div class="row">
    <div class="col-md-6">
      <div class="box box-default">
      <div id="piechart1" style="min-width: 310px; height: 400px; max-width: 600px; margin: 0 auto"></div>          
      </div>
      <!-- /.box -->
    </div>
    <div class="col-md-6">
      <div class="box box-default">
      <div id="piechart2" style="min-width: 310px; height: 400px; max-width: 600px; margin: 0 auto"></div>          
      </div>
      <!-- /.box -->
    </div>
  </div>
</section>
<?php
$loginSession = $this->session->userdata('login_session');
?>

<script type="text/javascript">
  var isAdminOrHR = $('#group').val() === '0' ? true : false;
  var section     = $('#group').val();
    
  var url1        = '<?= base_url() ?>dashboard/jobtitle_chart/' + isAdminOrHR + '/' + <?= $loginSession['nik']?>;
  $.get(url1, function (response) {

    var respon = JSON.parse(response)

    var chartContent = respon.map(function(data) {
      return {
        name: data.name,
        y: parseInt(data.y)
      }
    })

    Highcharts.chart('piechart1', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
            text: 'Jumlah Karyawan Per Job Title'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.y:.f} orang</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.y:.f}'
                }
            }
        },
        series: [{
            name: 'Total',
            colorByPoint: true,
            data: chartContent
        }]
    });
  });

  var url2 = '<?= base_url() ?>dashboard/employes_grade/'+isAdminOrHR+'/'+section;
  $.get(url2, function(resp) {
    var response = JSON.parse(resp)

    var chartContent2 = response.map(function(data) {
      return {
        name: data.name,
        y: parseInt(data.y)
      }
    })

    Highcharts.chart('piechart2', {
      chart: {
          plotBackgroundColor: null,
          plotBorderWidth: null,
          plotShadow: false,
          type: 'pie'
      },
      title: {
          text: 'Jumlah Karyawan Per Grade '
      },
      tooltip: {
          pointFormat: '{series.name}: <b>{point.y:.f} orang</b>'
      },
      plotOptions: {
          pie: {
              allowPointSelect: true,
              cursor: 'pointer',
              dataLabels: {
                  enabled: true,
                  format: '<b>{point.name}</b>: {point.y:.f}'
              }
          }
      },
      series: [{
          name: 'Total',
          colorByPoint: true,
          data: chartContent2
      }]
    });  
  })

</script>
