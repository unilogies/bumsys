  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">

      <?php 

        if(isset($_GET["action"]) and $_GET["action"] == "addSpecimenCopy") {
          // Show a success msg
          echo _s("Specimen copies have been successfully added.");
        }

      ?>
      
  </section>

  
    <section class="content-header">
      <h1>
        <?= __("Specimen Copies"); ?>
        <a href="<?php echo full_website_address(); ?>/marketing/add-specimen-copy/" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> <?= __("Add New"); ?></a>
      </h1>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
    
      <div class="row">
        <div class="col-xs-12">
          <div class="box">

          <div class="box-header">
              <h3 class="box-title"><?= __("Specimen Copy List"); ?></h3>
              <div class="printButtonPosition"></div>
            </div>
            <!-- Box header -->
            <div class="box-body">
              <table id="dataTableWithAjaxExtend" class="table table-bordered table-striped table-hover" width="100%">
                <thead>
                  <tr>
                    <th></th>
                    <th><?= __("Date"); ?></th>
                    <th><?= __("Reference"); ?></th>
                    <th><?= __("Type"); ?></th>
                    <th><?= __("Warehouse"); ?></th>
                    <th><?= __("Representative"); ?></th>
                    <th><?= __("Action"); ?></th>
                  </tr>
                </thead>
   
                <tfoot>
                  <tr>
                    <th></th>
                    <th><?= __("Date"); ?></th>
                    <th><?= __("Reference"); ?></th>
                    <th><?= __("Type"); ?></th>
                    <th><?= __("Warehouse"); ?></th>
                    <th><?= __("Representative"); ?></th>
                    <th><?= __("Action"); ?></th>
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
    var DataTableAjaxPostUrl = "<?php echo full_website_address(); ?>/xhr/?module=marketing&page=specimenCopyList";
  </script>