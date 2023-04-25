  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?= __("Bills"); ?>
        <a data-toggle="modal" data-target="#modalDefaultMdm" href="<?php echo full_website_address(); ?>/xhr/?select2=true&module=expenses&page=newBill" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> <?= __("Add Bill"); ?></a>
        <a data-toggle="modal" data-target="#modalDefault" href="<?php echo full_website_address(); ?>/xhr/?select2=true&module=expenses&page=dueBillPay" class="btn btn-sm btn-primary"><i class="fa fa-money"></i> <?= __("Pay Due Bill"); ?></a>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Bills</a></li>
        <li class="active">Bill List</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
          
            <!-- Box header -->
            <div class="box-header">
              <h3 class="box-title"><?= __("Bills"); ?></h3>
              <div class="printButtonPosition"></div>
            </div>

            <div class="box-body">
              <table id="dataTableWithAjaxExtend" class="table table-striped table-hover" width="100%">
                <thead>
                  <tr>
                    <th></th>
                    <th class="px120"><?= __("Entry Date"); ?></th>
                    <th class="px160"><?= __("Bill Date"); ?></th>
                    <th><?= __("Company"); ?></th>
                    <th><?= __("Reference"); ?></th>
                    <th class="countTotal"><?= __("Amount"); ?></th>
                    <th><?= __("Description"); ?></th>
                    <th><?= __("Attachment"); ?></th>
                    <th class="no-sort"><?= __("Action"); ?></th>
                  </tr>
                </thead>

                <tfoot>
                  <tr>
                    <th></th>
                    <th><?= __("Entry Date"); ?></th>
                    <th>
                        <input style="width: 175px;" type="text" placeholder="<?= __("Select Date"); ?>" id="billDateFilter" class="form-control" autocomplete="off">
                    </th>
                    <th>
                        <select style="width: 320px; margin-left: 30px;" id="billCompanyFilter" class="form-control select2Ajax" select2-minimum-input-length="1" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=CompanyList" required>
                            <option value=""><?= __("Select Company"); ?>....</option>
                        </select>
                    </th>
                    <th><?= __("Reference"); ?></th>
                    <th><?= __("Amount"); ?></th>
                    <th><?= __("Description"); ?></th>
                    <th><?= __("Attachment"); ?></th>
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
    BMS.FUNCTIONS.dateRangePickerPreDefined({selector: "#billDateFilter"});
    var DataTableAjaxPostUrl = "<?php echo full_website_address(); ?>/xhr/?module=expenses&page=billsList";
  </script>
