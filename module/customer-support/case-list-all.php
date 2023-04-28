<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?= __("Customer Support"); ?>
            <a data-toggle="modal" data-target="#modalDefaultMdm" href="<?php echo full_website_address(); ?>/xhr/?module=customer-support&page=newCase" class="btn btn-primary"><?= __("New Case"); ?></a>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title"><?= __("Case List"); ?></h3>
                        <div class="printButtonPosition"></div>
                    </div>
                    <!-- Box header -->
                    <div class="box-body">
                        <table id="dataTableWithAjaxExtend" class="table table-bordered table-striped table-hover" width="100%">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th><?php echo __("Date"); ?></th>
                                    <th><?php echo __("Case Title"); ?></th>
                                    <th style="width: 200px;"><?php echo __("Requester"); ?></th>
                                    <th class="defaultOrder no-sort"><?php echo __("Priority"); ?></th>
                                    <th class="no-sort"><?php echo __("Type"); ?></th>
                                    <th class="no-sort"><?php echo __("Status"); ?></th>
                                    <th class="no-sort"><?php echo __("Assigned To"); ?></th>
                                    <th class="no-sort"><?php echo __("Belongs To"); ?></th>
                                    <th class="no-sort"><?php echo __("Last Reply"); ?></th>
                                    <th class="no-sort"><?php echo __("Posted By"); ?></th>
                                    <th class="no-sort"><?php echo __("Action"); ?></th>
                                </tr>
                            </thead>

                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th class="no-print">
                                        <input style="width: 160px;" type="text" placeholder="<?= __("Select Date"); ?>" id="caseAddedDate" class="form-control" autocomplete="off">
                                    </th>
                                    <th><?php echo __("Case Title"); ?></th>
                                    <th class="no-print">
                                        <input style="width: 100%;" type="text" placeholder="<?= __("Search Requester"); ?>" id="searchRequester" class="form-control" autocomplete="off">
                                    </th>
                                    <th class="no-print"> 
                                        <select id="casePriority" class="form-control select2" style="width: 100%">
                                            <option value="">Priority...</option>
                                            <?php
                                                $casePriority = array('Low', 'Medium', 'High', 'Critical');
                                                foreach($casePriority as $priority) {
                                                    echo "<option value='{$priority}'>{$priority}</option>";
                                                }
                                            ?>
                                        </select>
                                    </th>
                                    <th class="no-print"> 
                                        <select id="caseType" class="form-control select2" style="width: 100%">
                                            <option value="">Type...</option>
                                            <?php
                                                $caseType = array('Refund Request', 'Packaging Issues', 'Delivery Issue', 'Technical Issues', 'Query', 'Damaged Item', 'Exchange', 'Others');
                                                foreach($caseType as $type) {
                                                    echo "<option value='{$type}'>{$type}</option>";
                                                }
                                            ?>
                                        </select>
                                    </th>
                                    <th class="no-print"> 
                                        <select id="caseStatus" class="form-control select2" style="width: 100%">
                                            <option value="">Status...</option>
                                            <?php
                                                $caseStatus = array('Pending', 'Open', 'Replied', 'Customer Responded', 'Solved', 'Informed', 'On Hold');
                                                foreach($caseStatus as $status) {
                                                    echo "<option value='{$status}'>{$status}</option>";
                                                }
                                            ?>
                                        </select>
                                    </th>
                                    <th>
                                        <select id="caseAssignTo" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=userList" style="width: 100%;">
                                            <option value=""><?= __("Select Assignee"); ?>....</option>
                                        </select>
                                    </th>
                                    <th> 
                                        <select id="caseBelongsTo" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=employeeList" style="width: 100%;">
                                            <option value=""><?= __("Select Belongs to"); ?>....</option>
                                        </select>
                                    </th>
                                    <th><?php echo __("Last Reply"); ?></th>
                                    <th class="no-sort">
                                        <select id="casePostedBy" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=userList" style="width: 100%;">
                                            <option value=""><?= __("Posted By"); ?>....</option>
                                        </select>
                                    </th>
                                    <th><?php echo __("Action"); ?></th>
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
    var DataTableAjaxPostUrl = "<?php echo full_website_address(); ?>/xhr/?module=customer-support&page=caseList";
    BMS.FUNCTIONS.dateRangePickerPreDefined({selector: "#caseAddedDate"});
</script>