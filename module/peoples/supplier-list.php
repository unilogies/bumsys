<?php 

$printButton = true;
$excelExportButton = true;
$otherExportButton = false;

?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Suppliers
        <small>Supplier List</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Suppliers</a></li>
        <li class="active">Supplier List</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Suppliers List</h3>
            </div>
            <!-- Box header -->
            <div class="box-body">
              <table id="dataTableWithAjaxExtend" class="table table-bordered table-striped table-hover" width="100%">
                <thead>
                  <tr>
                    <th>Supplier Name</th>
                    <th>Address</th>
                    <th>Contact</th>
                    <th class="no-print no-sort" width="100px">Action</th>
                  </tr>
                </thead>
   
                <tfoot>
                  <tr>
                    <th>Supplier Name</th>
                    <th>Address</th>
                    <th>Contact</th>
                  <th>Action</th>
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
    var DataTableAjaxPostUrl = "<?php echo full_website_address(); ?>/xhr/?module=peoples&page=supplierList";
  </script>
