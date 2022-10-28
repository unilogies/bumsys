<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?= __("System Settings"); ?>
        </h1>
    </section>

    <style>
        .radiousPosition {
            margin-left: 10px;
            cursor: pointer;
        }
    </style>

    <!-- Main content -->
    <section class="content container-fluid">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <!-- Form start -->
                    <form method="post" role="form" id="jqFormAdd" action="<?php echo full_website_address(); ?>/xhr/?module=settings&page=saveSystemSettings">
                        <div class="box-body">

                            <div class="form-group row">
                                <label class="col-sm-3" for="companyName"><?= __("Company Name:"); ?></label>
                                <div class="col-sm-6">
                                    <input type="text" name="companyName" id="companyName" value="<?php echo get_options("companyName"); ?>" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3" for="companyAddress"><?= __("Company Address:"); ?></label>
                                <div class="col-sm-6">
                                    <input type="text" name="companyAddress" id="companyAddress" value="<?php echo get_options("companyAddress"); ?>" class="form-control">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3" for="timeZone"><?= __("Time Zone:"); ?></label>
                                <div class="col-sm-6">
                                    <select name="timeZone" id="timeZone" class="select2 form-control">
                                        <option value=""><?= __("Select your timezone"); ?></option>
                                        <?php

                                        require LOAD_LIB . "timeZone/timeZone.php";

                                        // Select the current timezone
                                        $currentTimezone = get_options("timeZone");

                                        foreach ($timeZone as $gmt => $timeZone) {
                                            $selected = ($currentTimezone === $timeZone) ? "selected" : "";
                                            echo "<option {$selected} value='{$timeZone}'>{$gmt}</option>";
                                        }

                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3" for="dateFormat">Date Format:</label>
                                <div class="col-sm-6">
                                    <?php

                                    $dateFormat = array(
                                        "F j, Y"  => "%M %d, %Y",
                                        "j F, Y"  => "%d %M, %Y",
                                        "Y-m-d"   => "%Y-%m-%d",
                                        "d-m-Y"   => "%d-%m-%Y",
                                        "Y/m/d"   => "%Y/%m/%d",
                                        "d/m/Y"   => "%d/%m/%Y"
                                    );

                                    // Select the current date format
                                    $currentDateFormat = get_options("dateFormat");

                                    foreach ($dateFormat as $phpDateFormat => $mySQLDateFormat) {
                                        $checked = ($currentDateFormat === $phpDateFormat) ? "checked" : "";
                                        echo '<label>
                            <input ' . $checked . ' type="radio" name="dateFormat" class="square" value="' . $phpDateFormat . '"> <span class="radiousPosition"> ' . date($phpDateFormat) . ' </span>
                          </label><br/>';
                                    }
                                    ?>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3" for="timeFormat"><?= __("Time Format:"); ?></label>
                                <div class="col-sm-6">
                                    <?php
                                    $timeFormat = array(
                                        "g:i a"   => "%h:%i %p",
                                        "g:i A"   => "%h:%i %p",
                                        "h:i a"   => "%I:%i %p",
                                        "h:i A"   => "%I:%i %p",
                                        "H:i"     => "%H:%i"
                                    );

                                    // Select the current date format
                                    $currentTimeFormat = get_options("timeFormat");

                                    foreach ($timeFormat as $phpTimeFormat => $mysqlTimeFormat) {
                                        $checked = ($currentTimeFormat === $phpTimeFormat) ? "checked" : "";

                                        echo '<label>
                            <input ' . $checked . ' type="radio" name="timeFormat" class="square" value="' . $phpTimeFormat . '"> <span class="radiousPosition"> ' . date($phpTimeFormat) . ' </span>
                          </label><br/>';
                                    }
                                    ?>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3" for="currencySymbol"><?= __("Currency:"); ?></label>
                                <div class="col-sm-6">
                                    <select name="currencySymbol" id="currencySymbol" class="select2 form-control">
                                        <option value=""><?= __("Select your currenry"); ?></option>
                                        <?php

                                        require LOAD_LIB . "currencySymbol/currencySymbols.php";

                                        // Select the current timezone
                                        $currentCurrency = trim(get_options("currencySymbol"));

                                        foreach ($currencySymbols as $currencyNname => $symbols) {
                                            $selected = ($currentCurrency === html_entity_decode($symbols)) ? "selected" : "";
                                            echo "<option {$selected} value='{$symbols}'>{$currencyNname}</option>";
                                        }

                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3" for="currencySymbolPosition"><?= __("Symbol Position:"); ?></label>
                                <div class="col-sm-6">
                                    <select name="currencySymbolPosition" id="currencySymbolPosition" class="select2 form-control">
                                        <option <?= get_options("currencySymbolPosition") !== "left" ?: "selected"; ?> value="left">Left</option>
                                        <option <?= get_options("currencySymbolPosition") !== "right" ?: "selected"; ?> value="right">Right</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3" for="thousandSeparator"><?= __("Thousand Separator:"); ?></label>
                                <div class="col-sm-6">
                                    <input type="text" name="thousandSeparator" id="thousandSeparator" class="form-control" value="<?= get_options("thousandSeparator"); ?>">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3" for="decimalSeparator"><?= __("Decimal Separator:"); ?></label>
                                <div class="col-sm-6">
                                    <input type="text" name="decimalSeparator" id="decimalSeparator" class="form-control" value="<?= get_options("decimalSeparator"); ?>">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3" for="decimalPlaces"><?= __("Decimal Places:"); ?></label>
                                <div class="col-sm-6">
                                    <input type="number" name="decimalPlaces" id="decimalPlaces" class="form-control" value="<?= get_options("decimalPlaces"); ?>">
                                </div>
                            </div>

                        </div>
                        <!-- box body-->
                        <div class="box-footer">
                            <button type="submit" id="jqAjaxButton" class="btn btn-primary"><?= __("Save Change"); ?></button>
                        </div>
                    </form>
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