<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    <?= __("Reports"); ?>
  </h1>
</section>

<!-- Main content -->
<section class="content container-fluid">

  <div style="display: none;" id="DtExportTopMessage">
    <h3 style="font-weight: bold;" class="text-center"><?php echo __("%s Payments Reports", $_GET["paymentType"]); ?>  (<?=  isset($_GET["dateRange"]) ? $_GET["dateRange"] : "" ?>) </h3>
    <p class="text-center"><strong><?= __("Printed On:"); ?> <?php echo date("Y-m-d H:i:s");  ?> </strong> </p>
  </div>

  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-header">
          <h3 class="box-title"><?php echo __("%s Payments Reports", $_GET["paymentType"]); ?> (<?= isset($_GET["dateRange"]) ? $_GET["dateRange"] : "" ?>) </h3>
          <div class="printButtonPosition"></div>
        </div>
        <!-- Box header -->
        <div class="box-body">
          <table id="dataTableWithAjaxExtend" class="table table-striped table-hover" style="width: 100%;">
            <thead>
              <tr>
                <th></th>
                <th><?= __("Months"); ?></th>
                <th><?= __("Paid To"); ?></th>
                <th class="no-sort countTotal"><?= __("Amount"); ?></th>
                <th class="no-sort"><?= __("Description"); ?></th>
              </tr>
            </thead>

            <tfoot>
              <tr> 
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
              </tr>
            </tfoot>
          </table>
          
        </div>
        <!-- box body-->
      </div>
      <!-- box -->
    </div>
    <!-- col-xs-12-->
  </div>
  <!-- row-->

</section> <!-- Main content End tag -->
<!-- /.content -->
</div>
<!-- /.content-wrapper -->

<script>
  var DataTableAjaxPostUrl = "<?php echo full_website_address(); ?>/xhr/?module=reports&page=expenseReportsNonCat&paymentType=<?= $_GET["paymentType"]; ?>&dateRange=<?= $_GET["dateRange"]; ?>";
  var defaultiDisplayLength = -1;
</script>
