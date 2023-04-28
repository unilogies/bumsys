<div class="content-wrapper">
    <!-- Content Header (Page header) -->

    <?php

    $selectSipCredentials = easySelectA(array(
        "table"     => "sip_credentials",
        "fields"    => "sip_username, sip_password, sip_domain, sip_websocket_addr",
        "where"     => array(
            "sip_representative"    => $_SESSION["uid"]
        )
    ));

    if ($selectSipCredentials !== false) {
        $sip = $selectSipCredentials["data"][0];
        echo "<script> const sipCredentials = {
                uri: 'sip:{$sip['sip_username']}@{$sip['sip_domain']}',
                socket: '{$sip['sip_websocket_addr']}',
                user: '{$sip['sip_username']}',
                pass: '{$sip['sip_password']}'
            }; </script>";
    }

    $getExtentionNumber = easySelectA(array(
        "table"     => "sip_credentials as sip_credential",
        "fields"    => "sip_username as extention, concat(emp_firstname, ' ', emp_lastname) as name",
        "join"      => array(
            "left join {$table_prefix}users on sip_representative = user_id",
            "left join {$table_prefix}employees on user_emp_id = emp_id",
        ),
        "where" => array(
            "sip_credential.is_trash = 0"
        )
    ));

    if ($getExtentionNumber !== false) {

        echo "<script> const extentionNumbers = " . json_encode($getExtentionNumber["data"]) . "; </script>";
    }

    ?>
    <!-- Content Wrapper. Contains page content -->
    <script async src="<?php echo full_website_address(); ?>/js/?q=cs&v=2.1.8"></script>

    <!-- Main content -->
    <section class="content container-fluid">

        <div class="row" id="customerSupport" style="position: fixed; top: 50px; z-index: 5; background-color: #ecf0f5; width: 88%; padding-top: 15px">

            <section style="padding: 0px 15px 15px 15px;" class="content-header">
                <h1>
                    <?php echo __("Customer Support"); ?>
                </h1>
            </section>

            <div class="col-md-3">

                <div id="callDailer" class="input-group">
                    <input id="callNumber" placeholder="Please enter number here.." type="text" value="" class="form-control">
                    <span class="input-group-btn">
                        <button id="callButton" type="button" class="btn btn-success btn-flat"><i class="fa fa-fw fa-phone"></i> Call</button>
                        <button style="display: none;" id="hangButton" type="button" class="btn btn-danger btn-flat"><i class="fa fa-fw fa-phone"></i> End</button>
                    </span>
                </div>

                <div style="min-height: 80px; margin-left: 10px;">

                    <h3 style="margin-top: 15px;" id="status">Dail a number!</h3>
                    <?php
                    if ($selectSipCredentials === false) {
                        _e("Sorry! No credentials found. This module will not work.");
                    }
                    ?>
                    <div style="padding: 5px; display: none;" class="dndAlert alert-danger">Do not Disturb mode is enabled. No incoming call will work.</div>

                    <h3 style="margin: 0; display: none" id="timer">00:00:00</h3>
                    <div style="display: none; height: 70px; margin-top: 12px;" id="callReceiver">

                        <button id="callReceiveButton" type="button" class="btn btn-success btn-flat"><i class="fa fa-fw fa-phone"></i> Accept</button>
                        <button id="IncomingCallHangButton" type="button" class="btn btn-danger btn-flat"><i class="fa fa-fw fa-phone"></i> Reject</button>

                    </div>

                </div>


                <!-- Action Buttons -->
                <a id="muteCall" class="btn btn-app">
                    <i class="fa fa-microphone"></i> Mute
                </a>
                <a id="unmuteCall" style="display: none;" class="btn btn-app">
                    <i class="fa fa-microphone-slash"></i> Unmute
                </a>
                <a id="holdCall" class="btn btn-app">
                    <i class="fa fa-pause"></i> Hold
                </a>
                <a id="unholdCall" style="display: none;" class="btn btn-app">
                    <i class="fa fa-play"></i> Unhold
                </a>
                <a id="transferCall" class="btn btn-app">
                    <i class="fa fa-arrow-up"></i> Transfer
                </a>
                <a id="enableDND" class="btn btn-app">
                    <i class="fa fa-bell-slash"></i> Enable DND
                </a>
                <a id="disableDND" style="display: none;" class="btn btn-app">
                    <i class="fa fa-bell"></i> Disable DND
                </a>

                <div style="margin-top: 15px; margin-left: 10px;" class="form-group">
                    <button id="addNewCase" class="btn btn-primary">Add Case/ Issue</button>
                    <a data-toggle="modal" data-target="#modalDefault" href="<?php echo full_website_address(); ?>/xhr/?module=marketing&page=newPerson" class="btn btn-primary"><?= __("New Lead"); ?></a>
                    <!-- <button class="btn btn-primary">View Specimen Copies</button> -->
                </div>

            </div>

            <div class="col-md-3">

                <div class="box box-info">
                    <div class="box-header">
                        <h3 class="box-title"><?= __("Caller Info"); ?></h3>
                    </div>
                    <!-- Box header -->
                    <div class="box-body">
                        <table class="table callerInfo">
                            <tr>
                                <td><?php echo __("Name:"); ?></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td><?php echo __("Type:"); ?></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td><?php echo __("Desig."); ?></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td><?php echo __("Address:"); ?></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td><?php echo __("Last Call:"); ?></td>
                                <td></td>
                            </tr>
                        </table>

                    </div>
                    <!-- box body-->
                </div>
                <!-- box -->

            </div>

            <div class="col-md-3">

                <div class="box box-danger">
                    <!-- Box header -->
                    <div class="box-header with-border">
                        <h3 class="box-title"><?= __("FeedBack"); ?></h3>
                    </div>

                    <div class="box-body">

                        <div class="form-group">
                            <textarea name="feedbackArea" id="feedbackArea" cols="30" rows="6" placeholder="Enter feedback here..." class="form-control"></textarea>
                        </div>
                        <!-- <div style="margin-bottom: 5px;" class="form-group">
                              <select name="feedbackReviewer" id="feedbackReviewer" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=userList" style="width: 100%;">
                                  <option value=""><?= __("Select feedback reviewer"); ?>....</option>
                              </select>
                          </div> -->
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <input type="text" placeholder="Caller" name="feedbackCaller" id="feedbackCaller" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <input id="saveFeedback" type="button" value="Save" class="btn btn-primary">
                            </div>
                        </div>

                    </div>
                    <!-- box body-->
                </div>
                <!-- box -->

            </div>

            <div class="col-md-3">

                <div class="box box-primary">

                    <!-- Box header -->
                    <!-- Box header -->
                    <div class="box-header with-border">
                        <h3 class="box-title"><?= __("Message"); ?></h3>
                    </div>

                    <div class="box-body">

                        <div class="form-group">

                            <textarea name="csSmsText" id="csSmsText" cols="30" rows="6" placeholder="Enter text here..." class="form-control"></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <input type="text" placeholder="Enter recipient number" name="csSmsSendTo" id="csSmsSendTo" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <input id="csSendSMS" type="button" value="Send SMS" class="btn btn-primary">
                            </div>
                        </div>

                    </div>
                    <!-- box body-->
                </div>
                <!-- box -->

            </div>

        </div>

        <div style="height: 310px;"></div>
        <br />

        <div id="test" class="row">

            <div class="col-md-6">

                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title"><?= __("Person List"); ?></h3>
                    </div>
                    <!-- Box header -->
                    <div class="box-body personList">
                        <table dt-height="30vh" dt-data-url="<?php echo full_website_address(); ?>/xhr/?module=customer-support&page=personListForCallCenter" dt-disable-on-type-search class="dataTableWithAjaxExtend table table-bordered table-striped table-hover" width="100%">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th><?= __("Person Details"); ?></th>
                                    <th><?= __("Phone"); ?></th>
                                    <th><?= __("Address"); ?></th>
                                    <th><?= __("Specimen Products"); ?></th>
                                    <th class="no-print no-sort" width="100px"><?= __("Action"); ?></th>
                                </tr>
                            </thead>

                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th><?= __("Person Details"); ?></th>
                                    <th><?= __("Phone"); ?></th>
                                    <th><?= __("Address"); ?></th>
                                    <th><?= __("Specimen Products"); ?></th>
                                    <th><?= __("Action"); ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <!-- box body-->
                </div>
                <!-- box -->

                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title"><?= __("Marketing Representative List"); ?></h3>
                    </div>
                    <!-- Box header -->
                    <div class="box-body">
                        <table dt-height="30vh" dt-data-url="<?php echo full_website_address(); ?>/xhr/?module=customer-support&page=representativeList" dt-disable-on-type-search class="dataTableWithAjaxExtend addToSMSBox table table-bordered table-striped table-hover" width="100%">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th width="30px"><?= __("ID"); ?></th>
                                    <th class="no-sort" width="80px"><?= __("Photo"); ?></th>
                                    <th><?php echo __("Name"); ?></th>
                                    <th><?php echo __("Working Area"); ?></th>
                                    <th><?php echo __("Contact Number"); ?></th>
                                </tr>
                            </thead>

                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th><?php echo __("Name"); ?></th>
                                    <th><?php echo __("Working Area"); ?></th>
                                    <th><?php echo __("Contact Number"); ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <!-- box body-->
                </div>
                <!-- box -->

                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title"><?= __("Library List"); ?></h3>
                    </div>
                    <!-- Box header -->
                    <div class="box-body">
                        <table dt-height="30vh" dt-data-url="<?php echo full_website_address(); ?>/xhr/?module=peoples&page=customerList" dt-disable-on-type-search class="dataTableWithAjaxExtend table table-bordered table-striped table-hover" width="100%">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th class="col-md-3"><?= __("Name"); ?></th>
                                    <th><?= __("District"); ?></th>
                                    <th><?= __("Division"); ?></th>
                                    <th><?= __("Address"); ?></th>
                                    <th class="col-md-1"><?= __("Contact"); ?></th>
                                    <th class="hideit no-print no-sort" width="100px"><?= __("Action"); ?></th>
                                </tr>
                            </thead>

                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th><?= __("Customer Name"); ?></th>
                                    <th><?= __("District"); ?></th>
                                    <th><?= __("Division"); ?></th>
                                    <th><?= __("Address"); ?></th>
                                    <th><?= __("Contact"); ?></th>
                                    <th><?= __("Action"); ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <!-- box body-->
                </div>
                <!-- box -->




            </div>
            <!-- col-md-6-->

            <div class="col-md-6">

                <div class="row">

                    <div class="col-md-6">

                        <div class="box">
                            <div class="box-header with-border">
                                <h3 class="box-title"><?= __("Quick Feedback"); ?></h3>
                            </div>
                            <!-- Box header -->
                            <div style="max-height: 220px; overflow: auto;" class="box-body quickFeedback">

                                <ul class="todo-list">
                                    <?php
                                    $selectFeedback = easySelectA(array(
                                        "table"     => "notes",
                                        "fields"    => "note_text",
                                        "where"     => array(
                                            "is_trash = 0 and note_type = 'feedback' and note_created_by"   => $_SESSION["uid"]
                                        )
                                    ));

                                    if ($selectFeedback !== false) {

                                        foreach ($selectFeedback["data"] as $fKey => $fValue) {
                                            echo '<li style="cursor:pointer;">' . $fValue["note_text"] . '</li>';
                                        }
                                    }

                                    ?>

                                    <li style="cursor:pointer; text-align: center;"><i class="fa fa-plus-circle"></i> Add New</li>

                                </ul>

                            </div>
                            <!-- box body-->
                        </div>
                        <!-- box -->

                    </div>

                    <div class="col-md-6">

                        <div class="box">
                            <div class="box-header with-border">
                                <h3 class="box-title"><?= __("Note"); ?></h3>
                            </div>
                            <!-- Box header -->
                            <div style="max-height: 220px; overflow: auto;" class="box-body userNote">
                                <ul class="todo-list">
                                    <?php
                                    $selectFeedback = easySelectA(array(
                                        "table"     => "notes",
                                        "fields"    => "note_text",
                                        "where"     => array(
                                            "is_trash = 0 and note_type = 'note' and note_created_by"   => $_SESSION["uid"]
                                        )
                                    ));

                                    if ($selectFeedback !== false) {

                                        foreach ($selectFeedback["data"] as $fKey => $fValue) {
                                            echo '<li style="cursor:pointer;">' . $fValue["note_text"] . '</li>';
                                        }
                                    }

                                    ?>

                                    <li style="cursor:pointer; text-align: center;"><i class="fa fa-plus-circle"></i> Add New</li>

                                </ul>

                            </div>
                            <!-- box body-->
                        </div>
                        <!-- box -->

                    </div>

                </div>

                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title"><?= __("Case List"); ?></h3>
                    </div>
                    <!-- Box header -->
                    <div class="box-body caseList">
                        <table dt-height="40vh" dt-data-url="<?php echo full_website_address(); ?>/xhr/?module=customer-support&page=caseList" dt-disable-on-type-search class="dataTableWithAjaxExtend table table-bordered table-striped table-hover" width="100%">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th><?php echo __("Date"); ?></th>
                                    <th><?php echo __("Requester"); ?></th>
                                    <th><?php echo __("Title"); ?></th>
                                    <th><?php echo __("Priority"); ?></th>
                                    <th><?php echo __("Type"); ?></th>
                                    <th><?php echo __("Status"); ?></th>
                                </tr>
                            </thead>

                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th><?php echo __("Date"); ?></th>
                                    <th><?php echo __("Requester"); ?></th>
                                    <th><?php echo __("Title"); ?></th>
                                    <th><?php echo __("Priority"); ?></th>
                                    <th><?php echo __("Type"); ?></th>
                                    <th><?php echo __("Status"); ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <!-- box body-->
                </div>
                <!-- box -->

                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title"><?= __("Product List"); ?></h3>
                    </div>
                    <!-- Box header -->
                    <div class="box-body">
                        <table dt-height="40vh" dt-data-url="<?php echo full_website_address(); ?>/xhr/?module=customer-support&page=productList" dt-disable-on-type-search class="dataTableWithAjaxExtend table table-bordered table-striped table-hover" width="100%">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th style="width: 150px;"><?= __("Product Name"); ?></th>
                                    <th style="width: 100px;"><?= __("Edition"); ?></th>
                                    <th style="width: 120px;"><?= __("Category"); ?></th>
                                    <th style="width: 300px;"><?= __("Description"); ?></th>
                                </tr>
                            </thead>

                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th><?= __("Product Name"); ?></th>
                                    <th>
                                        <select name="productEdition" id="productEdition" class="form-control select2">
                                            <option value=""><?= __("All Ed."); ?></option>
                                            <?php

                                            $selectProductYear = easySelectA(array(
                                                "table"   => "products",
                                                "fields"  => "product_edition",
                                                "groupby" => "product_edition"
                                            ));

                                            if ($selectProductYear) {
                                                foreach ($selectProductYear["data"] as $key => $value) {
                                                    echo "<option value='{$value['product_edition']}'>{$value['product_edition']}</option>";
                                                }
                                            }

                                            ?>
                                        </select>
                                    </th>
                                    <th>
                                        <select name="productCategory" id="productCategory" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address(); ?>/info/?module=select2&page=productCategoryList">
                                            <option value=""><?= __("Category"); ?>...</option>
                                        </select>
                                    </th>
                                    <th><?= __("Description"); ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <!-- box body-->
                </div>
                <!-- box -->




            </div>
            <!-- col-md-6-->

        </div>
        <!-- row-->

    </section> <!-- Main content End tag -->
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
<script>
    // request permission on page load
    document.addEventListener('DOMContentLoaded', function() {

        if (!Notification) {
            alert('Desktop notifications not available in your browser.');
            return;
        }

        if (Notification.permission !== 'granted') {
            Notification.requestPermission();
        }

        // Enable / Disable DND when page is load
        if( get_options("dnd") === "enable" ) {

            $("#enableDND").hide();
            $("#disableDND").show();
            $(".dndAlert").show();

        } else {
            
            $("#disableDND").hide();
            $("#enableDND").show();

        }

    });
</script>