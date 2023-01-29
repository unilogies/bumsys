<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?= __("Persons"); ?>
            <small><?= __("Person List"); ?></small>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title"><?= __("Person List"); ?></h3>
                        <div class="printButtonPosition"></div>
                    </div>
                    <!-- Box header -->
                    <div class="box-body">
                        <table id="dataTableWithAjaxExtend" class="table table-bordered table-striped table-hover" width="100%">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th><?= __("Date"); ?></th>
                                    <th><?= __("Person Details"); ?></th>
                                    <th><?= __("Person Type"); ?></th>
                                    <th><?= __("Person Tags"); ?></th>
                                    <th style="width: 120px"><?= __("Class"); ?></th>
                                    <th><?= __("Phone"); ?></th>
                                    <th style="width: 120px"><?= __("Last Call"); ?></th>
                                    <th><?= __("Address"); ?></th>
                                    <th><?= __("Person Institute"); ?></th>
                                    <th><?= __("Institute Type"); ?></th>
                                    <th><?= __("Data Source"); ?></th>
                                    <th class="no-print no-sort" width="100px"><?= __("Action"); ?></th>
                                </tr>
                            </thead>

                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th class="col-md-1 no-print">
                                        <input style="width: 120px" type="text" name="personDateFilter" id="personDateFilter" placeholder="<?= __("Date Filter"); ?>" class="form-control input-sm" autoComplete="Off">
                                    </th>
                                    <th><?= __("Person Details"); ?></th>
                                    <th class="no-print">
                                        <select name="personType" id="personType" class="form-control select2" style="width: 100%">
                                            <option value="">Type...</option>
                                            <?php
                                            $personType = array('Teacher', 'Student', 'Guardian', 'Service Holder', 'Merchant');
                                            foreach ($personType as $type) {
                                                echo "<option value='{$type}'>{$type}</option>";
                                            }
                                            ?>
                                        </select>
                                    </th>
                                    <th class="no-print">
                                        <select name="personTags[]" id="personTags" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=personTagList" style="width: 100%;">
                                            <option value=""><?= __("Select Tages"); ?>....</option>
                                        </select>
                                    </th>
                                    <th>
                                        <select id="personClass" class="form-control select2" class="form-control" style="width: 100%">
                                            <option value=""><?= __("Select Class"); ?></option>
                                            <?php
                                                $class = array(
                                                    "1"   => __("Class One"),
                                                    "2"   => __("Class Two"),
                                                    "3"   => __("Class Three"),
                                                    "4"   => __("Class Four"),
                                                    "5"   => __("Class Five"),
                                                    "6"   => __("Class Six"),
                                                    "7"   => __("Class Seven"),
                                                    "8"   => __("Class Eight"),
                                                    "9"   => __("Class Nine"),
                                                    "10"  => __("Class Ten"),
                                                    "11"  => __("HSC 1st Year"),
                                                    "12"  => __("HSC 2nd Year"),
                                                    "13"  => __("Diploma/Graduation Level")
                                                );

                                                foreach ($class as $key => $value) {
                                                    echo "<option value='$key'>$value</option>";
                                                }
                                            ?>
                                        </select>
                                    </th>
                                    <th><?= __("Phone"); ?></th>
                                    <th><?= __("Last Call"); ?></th>
                                    <th>
                                        <select id="personAddressDistrict" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=districtList" style="width: 100%;" required>
                                            <option value=""><?= __("Select District"); ?>....</option>
                                        </select>
                                    </th>
                                    <th><?= __("Person Institute"); ?></th>
                                    <th class="no-print">
                                        <select name="instituteType" id="instituteType" class="form-control select2" style="width: 100%">
                                            <option value="">Type...</option>
                                            <?php
                                            $instituteType = array('School', 'College', 'University', 'Coaching', 'Library', 'Store');
                                            foreach ($instituteType as $type) {
                                                echo "<option value='{$type}'>{$type}</option>";
                                            }
                                            ?>
                                        </select>
                                    </th>
                                    <th>
                                        <select id="leadsDataSource" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=leadsDataSource" style="width: 100%;" required>
                                            <option value=""><?= __("Select District"); ?>....</option>
                                        </select>
                                    </th>
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
    var DataTableAjaxPostUrl = "<?php echo full_website_address(); ?>/xhr/?module=marketing&page=personList";
    BMS.FUNCTIONS.dateRangePickerPreDefined({selector: "#personDateFilter"});
</script>