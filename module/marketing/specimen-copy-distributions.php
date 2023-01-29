  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">

     <section class="content-header">

      <?php 

        if(isset($_GET["action"]) and $_GET["action"] == "distribute") {
          // Show a success msg
          echo _s("Specimen copies distribution have been successfully added.");
        }

      ?>
      
    </section>
  
    <section class="content-header">
      <h1>
        <?= __("Specimen Copy Distributions"); ?>
        <a href="<?php echo full_website_address(); ?>/marketing/new-sc-distribution/" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> Add New</a>

      </h1>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
    
      <div class="row">
        <div class="col-xs-12">
          <div class="box">

          <div class="box-header">
              <h3 class="box-title"><?= __("Specimen Copy Distribution List"); ?></h3>
              <div class="printButtonPosition"></div>
            </div>
            <!-- Box header -->
            <div class="box-body">
              <table id="dataTableWithAjaxExtend" class="table table-bordered table-striped table-hover" width="100%">
                <thead>
                  <tr>
                    <th></th>
                    <th><?= __("Date"); ?></th>
                    <th><?= __("Distributor"); ?></th>
                    <th><?= __("Recipient"); ?></th>
                    <th><?= __("Phone"); ?></th>
                    <th><?= __("Product"); ?></th>
                    <th><?= __("Quantity"); ?></th>
                    <th><?= __("Unit"); ?></th>
                    <th class="no-sort"><?= __("Action"); ?></th>
                  </tr>
                </thead>
   
                <tfoot>
                  <tr>
                    <th></th>
                    <th class="no-print">
                        <input style="width: 130px;" type="text" placeholder="<?= __("Select Date"); ?>" id="scDistributionDate" class="form-control" autocomplete="off">
                    </th>
                    <th>
                        <select style="width: 230px;" id="specimenCopyDistribution" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=employeeListAll" style="width: 100%;">
                            <option value=""><?= __("Select Distributor"); ?>....</option>
                        </select>    
                    </th>
                    <th>
                        <select style="width: 230px;" name="scRecipient" id="scRecipient" select2-minimum-input-length="1" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=personList" style="width: 100%;" required>
                            <option value=""><?= __("Select Recepient"); ?>....</option>
                        </select>
                    </th>
                    <th><?= __("Phone"); ?></th>
                    <th><?= __("Product"); ?></th>
                    <th><?= __("Quantity"); ?></th>
                    <th><?= __("Unit"); ?></th>
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
    var DataTableAjaxPostUrl = "<?php echo full_website_address(); ?>/xhr/?module=marketing&page=specimenCopyDistributionList";
    BMS.FUNCTIONS.dateRangePickerPreDefined({selector: "#scDistributionDate"});
  </script>
