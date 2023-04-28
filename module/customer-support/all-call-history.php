<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?= __("Customer Support"); ?>
        <small><?= __("Call List"); ?></small>
      </h1>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title"><?= __("Call List"); ?></h3>
              <div class="printButtonPosition"></div>
            </div>
            <!-- Box header -->
            <div class="box-body">
              <table id="dataTableWithAjaxExtend" class="table table-bordered table-striped table-hover" width="100%">
                <thead>
                  <tr>
                    <th></th>
                    <th class="px120"><?php echo __("Date"); ?></th>
                    <th class="px85"><?php echo __("Direction"); ?></th>
                    <th class="px120"><?php echo __("Status"); ?></th>
                    <th class="px85"><?php echo __("Reason"); ?></th>
                    <th class="px160"><?php echo __("Client"); ?></th>
                    <th class="px85"><?php echo __("Client Type"); ?></th>
                    <th class="px220"><?php echo __("Specimen Products"); ?></th>
                    <th class="px85"><?php echo __("Duration"); ?></th>
                    <th class="px220"><?php echo __("Feedback"); ?></th>
                    <th class="px85"><?php echo __("Specimen Received"); ?></th>
                    <th class="px85"><?php echo __("Informative"); ?></th>
                    <th class="px85"><?php echo __("Sale Our Book"); ?></th>
                    <th class="px85"><?php echo __("Use Our Book"); ?></th>
                    <th class="px85"><?php echo __("MR Feedback"); ?></th>
                    <th class="px85"><?php echo __("Other Info"); ?></th>
                    <th class="px85"><?php echo __("Perform By"); ?></th>
                  </tr>
                </thead>
   
                <tfoot>
                  <tr>
                    <th></th>
                    <th class="no-print">
                        <input type="text" placeholder="<?= __("Select Date"); ?>" name="callDate" id="callDate" class="form-control" autocomplete="off">
                    </th>
                    <th> 
                        <select name="callDirection" id="callDirection" class="form-control select2" style="width: 100%">
                            <option value="">Direction...</option>
                            <?php
                                $callDirection = array('Outgoing', 'Incoming');
                                foreach($callDirection as $Direction) {
                                    echo "<option value='{$Direction}'>{$Direction}</option>";
                                }
                            ?>
                      </select>
                    </th>
                    <th class="no-print"> 
                        <select name="callStatus" id="callStatus" class="form-control select2" style="width: 100%">
                            <option value="">Status...</option>
                            <?php
                                $callStatus = array('Answered', 'Missed', 'Rejected', 'Not Answered', 'Busy', 'Unreachable', 'Pending');
                                foreach($callStatus as $status) {
                                    echo "<option value='{$status}'>{$status}</option>";
                                }
                            ?>
                      </select>
                    </th>
                    <th>
                        <select id="callReasonFilter" class="form-control select2Ajax" select2-tag="true" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=callReasonList" style="width: 100%;">
                            <option value=""><?= __("All Reason"); ?>...</option>
                        </select>
                    </th>
                    <th><?php echo __("Client"); ?></th>
                    <th class="no-print"> 
                        <select id="clientType" class="form-control select2" style="width: 100%">
                            <option value="">Type...</option>
                            <?php
                                $personType = array('Teacher', 'Student', 'Guardian', 'Service Holder', 'Merchant');
                                foreach($personType as $type) {
                                    echo "<option value='{$type}'>{$type}</option>";
                                }
                            ?>
                      </select>
                    </th>
                    <th><?php echo __("Specimen Products"); ?></th>
                    <th><?php echo __("Duration"); ?></th>
                    <th><?php echo __("Feedback"); ?></th>
                    <th>
                        <select id="specimenReceivedFilter" class="form-control select2" style="width: 100%">
                            <option value="">All...</option>
                            <?php
                                $sr = array('Yes', 'No', 'Partial', 'Not Sure');
                                foreach($sr as $sr) {
                                    echo "<option value='{$sr}'>{$sr}</option>";
                                }
                            ?>
                      </select>
                    </th>
                    <th><?php echo __("Informative"); ?></th>
                    <th><?php echo __("Sale Our Book"); ?></th>
                    <th><?php echo __("Use Our Book"); ?></th>
                    <th><?php echo __("MR Feedback"); ?></th>
                    <th><?php echo __("Other Info"); ?></th>
                    <th class="no-print">
                        <select name="callPerformBy" id="callPerformBy" class="form-control select2" style="width: 100%">
                                <option value="">All...</option>
                                <?php
                                    $representative = easySelectA(array(
                                        "table"     => "sip_credentials",
                                        "fields"    => "sip_representative, concat(emp_firstname, ' ', emp_lastname) as representative_name",
                                        "join"      => array(
                                            "left join {$table_prefix}users on sip_representative = user_id",
                                            "left join {$table_prefix}employees on user_emp_id = emp_id"
                                        )
                                    ));
                                    foreach($representative["data"] as $representative) {
                                        echo "<option value='{$representative["sip_representative"]}'>{$representative["representative_name"]}</option>";
                                    }
                                ?>
                        </select>
                    </th>
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
    /*Column Defference target column of data table */
    var DataTableAjaxPostUrl = "<?php echo full_website_address(); ?>/xhr/?module=customer-support&page=allCallList";
    BMS.FUNCTIONS.dateRangePickerPreDefined({selector: "#callDate"});
  </script>
