<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?= __("Customer Support"); ?>
            <a data-toggle="modal" data-target="#modalDefaultMdm" href="<?php echo full_website_address(); ?>/xhr/?module=customer-support&page=newCase" class="btn btn-primary"><?= __("New Case"); ?></a>
        </h1>
    </section>

    <?php 

        $selectCase = easySelectA(array(
            "table"     => "cases as cases",
            "fields"    => "case_id, case_datetime, case_title, case_priority, case_type, case_status, last_reply,
                            case_site, case_assigned_to, case_belongs_to,
                            concat(assigned_to.emp_firstname, ' ', assigned_to.emp_lastname) as case_assign_name, 
                            concat(belongs_to.emp_firstname, ' ', belongs_to.emp_lastname) as case_belongs_to_name,
                            case_customer, customer_name, customer_phone, customer_email, customer_address,
                            case_person, person_full_name, person_phone, person_email, person_address,
                            person_type, person_designation, institute_name, institute_type, upazila_name, district_name, division_name
                            ",
            "join"      => array(
                "left join {$table_prefix}users on user_id = case_assigned_to",
                "left join {$table_prefix}employees as assigned_to on assigned_to.emp_id = user_emp_id",
                "left join {$table_prefix}employees as belongs_to on belongs_to.emp_id = case_belongs_to",
                "left join {$table_prefix}persons on person_id = case_person",
                "left join {$table_prefix}districts on district_id = person_district",
                "left join {$table_prefix}divisions on division_id = person_division",
                "left join {$table_prefix}upazilas on upazila_id = person_upazila",
                "left join {$table_prefix}institute on institute_id = person_institute",
                "left join {$table_prefix}customers on customer_id = case_customer",
                

                "left join (select
                        max(reply_datetime) as last_reply,
                        reply_case_id
                    from {$table_prefix}case_replies
                    where is_trash = 0
                    group by reply_case_id
                ) as replies on replies.reply_case_id = case_id"
            ),
            "where"     => array(
                "cases.is_trash = 0 and cases.case_id"  => $_GET["case_id"]
            )
        ));

        if($selectCase === false) {

            echo '<div style="margin-top: 20px" class="col-xs-12"><div class="alert alert-danger">Sorry! No Ticket/ Case found.</div></div>';

        } else {

            $case = $selectCase["data"][0];

    ?>

        <script src="<?php echo full_website_address(); ?>/assets/3rd-party/tinymce_6.1.2/tinymce.min.js"></script>
        <link rel="stylesheet" href="<?php echo full_website_address(); ?>/assets/3rd-party/viewerjs-main/dist/viewer.min.css">
        <script src="<?php echo full_website_address(); ?>/assets/3rd-party/viewerjs-main/dist/viewer.min.js"></script>

        <!-- Main content -->
        <section class="content container-fluid">
            <div class="row">
                <div class="col-xs-8">

                    <div class="case-title">
                        <h1><?php echo $case["case_title"]; ?></h1>
                        <p><b>Posted on:</b> <?php echo date("d F, Y h:m A", strtotime($case["case_datetime"])) . " (" .  time_elapsed_string($case["case_datetime"])  .")"; ?></p>
                    </div>
                    
                    <div class="case-replies">

                        <form id="submitCaseReply">

                            <textarea name='caseReply' id='replyBox'></textarea>
                            <div class="box replyForm">

                                <div class="box-body">
                                    <div class="form-group col-md-6">
                                        <label for="caseReplyAttachment">Attachment</label>
                                        <input type="file" name="caseReplyAttachment[]" id="caseAttachment" multiple accept="image/png, image/jpeg" class="form-control">
                                        <small style='margin-top: 5px; display: block'><b>Note:</b> Max Upload Size: <?php echo $_SETTINGS["MAX_UPLOAD_SIZE"]; ?>MB. Only JPEG and PNG image types are allowed to upload</small>    
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="replyMode"><?= __("Reply Mode:"); ?></label>
                                        <select name="replyMode" id="replyMode" class="form-control" style="width: 100%;">
                                            <?php 
                                                $mode = array('Public', 'Private');
                                                foreach($mode as $mode) {
                                                    echo "<option value='{$mode}'>{$mode}</option>";
                                                }
                                            ?>

                                        </select>
                                    </div>
                                    <input type="hidden" name="case_id" value="<?php echo safe_entities($_GET["case_id"]); ?>">
                                    <input type="hidden" name="caseType" value="<?php echo $case["case_type"]; ?>">
                                    <div class="form-group col-md-2">
                                        <br/>
                                        <button class='btn btn-primary' type='submit'>Submit</button>
                                    </div>

                                </div>
                                    
                                    
                            </div>

                        </form>

                        <?php 

                            $selectReplies = easySelectA(array(
                                "table"     => "case_replies as reply",
                                "fields"    => "reply_id, reply_type, reply_datetime, reply_details, reply_attachment,
                                                if(emp_firstname is null, customer_name, concat(emp_firstname, ' ', emp_lastname)) as reply_by,
                                                reply_by_customer
                                                ",
                                "join"      => array(
                                    "left join {$table_prefix}users on user_id = reply_by_agent",
                                    "left join {$table_prefix}employees on emp_id = user_emp_id",
                                    "left join {$table_prefix}customers on customer_id = reply_by_customer"
                                ),
                                "where"     => array(
                                    "reply.is_trash = 0 and reply_case_id"    => $_GET["case_id"]
                                ),
                                "orderby"   => array(
                                    "reply_id"  => "DESC"
                                )
                            ));

                            if($selectReplies !== false) {

                                foreach($selectReplies["data"] as $reply) {

                                    $time_elaps = time_elapsed_string($reply["reply_datetime"]) . " (" .  date("d F, Y h:m A", strtotime($reply["reply_datetime"]))  .")";

                                    $attachment = "";
                                    if($reply["reply_attachment"] !== null) {

                                        $listAttachment = unserialize( html_entity_decode($reply["reply_attachment"]) );

                                        foreach($listAttachment as $image) {
                                            $attachment .= "<li><img width='80' height='80' src='". full_website_address() ."/assets/upload/{$image}'></li>";
                                        }

                                    }


                                    // Define user and reply type
                                    $userType = "<span><i class='fa fa-user'></i> Staff</span>";
                                    $replyType = "<span><i class='fa fa-globe'></i> Public</span>";
                                    $replyClass = 'reply';
                                    $replyTypeMoveTo = 'Private';
                                    if( $reply["reply_by_customer"] !== null ) {
                                        $userType = "<span><i class='fa fa-user'></i> Customer</span>";
                                    }

                                    if( $reply["reply_type"] === "Private" ) {
                                        $replyType = "<span><i class='fa fa-lock'></i> Private</span>";
                                        $replyClass = 'reply private';
                                        $replyTypeMoveTo = 'Public';
                                    }

                                    // If there have any attachment then show them
                                    if($attachment !== "") {
                                        $attachment = "<div class='attachment'>
                                                            <p>Attachment:</p>
                                                            <ul class='imageAttachment'>
                                                                {$attachment}
                                                            </ul>
                                                        </div>";
                                    }
                                    
                                    
                                    echo "<div class='{$replyClass}'>
                                            
                                            <div class='title'>
                                                <span>{$reply["reply_by"]}</span>
                                                <span>
                                                    {$time_elaps}
                                                
                                                    <div class='btn-group'>
                                                        
                                                        <button type='button' class='btn btn-link' data-toggle='dropdown'>
                                                            <i class='fa fa-ellipsis-v'></i>
                                                        </button>
                                                        <ul class='dropdown-menu dropdown-menu-right' role='menu'>

                                                            <li><a class='deleteEntry' removeParent='.reply' href='". full_website_address() . "/xhr/?module=customer-support&page=deleteCaseReply' data-to-be-deleted={$reply["reply_id"]}'> Delete</a></li>
                                                            <li> <a class='updateEntry' href='". full_website_address() . "/xhr/?module=customer-support&page=updateCaseReplyType' data-to-be-updated='{$reply["reply_id"]}'>Move to {$replyTypeMoveTo} </a> </li>

                                                        </ul>

                                                    </div>
                                                </span>

                                            </div>
                                            <div class='type'>
                                                {$userType}
                                                {$replyType}
                                            </div>

                                            <div class='reply-details'>
                                                ". str_replace('\n', '</br>', html_entity_decode($reply["reply_details"]) ) ."
                                            </div>
                                            $attachment
                                            

                                        </div>";

                                }

                            }

                        ?>

                    </div>
      

                </div>
                <!-- col-xs-8-->

                <div class="col-xs-4">

                    <div class="box box-primary">

                        <div class="box-header with-border">
                            <h3 class="box-title"><?php echo __("Contact Details"); ?></h3>
                        </div> <!-- box box-default -->

                        <div class="box-body box-profile">

                            <?php if( $case["case_person"] !== null ) { ?>
                        
                                <h3 class="profile-username">
                                    <?php echo $case["person_full_name"]; ?>
                                    <small><a data-toggle="modal" href="<?php echo full_website_address(); ?>/xhr/?icheck=false&module=marketing&page=editPerson&id=<?php echo $case["case_person"]; ?>" data-target="#modalDefault"><i class="fa fa-edit"></i></a></small>
                                </h3>
                                <p>
                                    <?php echo $case["person_type"]. ( $case["person_designation"] === null ? "" : " ({$case["person_designation"]})" ) ; ?>,
                                    <?php echo $case["institute_name"]; ?>
                                </p>

                                
                                
                                <strong><i class="fa fa-book margin-r-5"></i> Contact</strong>
                                <p class="text-muted">
                                    <?php echo $case["person_phone"] . ", " . $case["person_email"];  ?>
                                </p>

                                <a data-toggle="modal" href="<?php echo full_website_address(); ?>/xhr/?module=marketing&page=viewSpecimenProduct&id=<?php echo $case["case_person"]; ?>"  data-target="#modalDefault"> View Specimen Copies</a>

                                <hr>

                                <strong><i class="fa fa-map-marker margin-r-5"></i> Address</strong>
                                <p class="text-muted"><?php echo $case["person_address"] . ", " . $case["upazila_name"] . ", " . $case["district_name"] . ", " . $case["division_name"]; ?></p>

                                <hr>

                                <strong><i class="fa fa-support margin-r-5"></i> Recent Case/ Tickets</strong>
                                
                                
                                <?php 
                                    $selectCaseForThisPerson = easySelectA(array(
                                        "table"     => "cases",
                                        "fields"    => "case_id, case_title, case_status",
                                        "where"     => array(
                                            "is_trash = 0 and case_id != '{$_GET["case_id"]}'",
                                            " and case_person" => $case["case_person"]
                                        ),
                                        "orderby"   => array(
                                            "case_id"   => "DESC"
                                        ),
                                        "limit" => array(
                                            "start"     => 0,
                                            "length"    => 5
                                        )
                                    ));

                                    if($selectCaseForThisPerson !== false) {
                                        foreach($selectCaseForThisPerson["data"] as $personCase) {
                                            echo "<p style='margin: 5px;'><a href='". full_website_address() ."/customer-support/case-list/?case_id={$personCase['case_id']}'>{$personCase['case_title']}</a> ({$personCase['case_status']})</p>";
                                            
                                        }
                                    } else {
                                        echo '<p class="text-muted">Sorry! No Ticket/ Cases found.</p>';
                                    }

                                ?>

                                <hr>

                                <strong><i class="fa fa-phone margin-r-5"></i> Call Log</strong>

                                <?php

                                    $selectCalls = easySelectA(array(
                                        "table"     => "calls",
                                        "fields"    => "count(*) as totalCall, max(call_datetime) as lastCallTime",
                                        "where"     => array(
                                            "client_identity LIKE" => explode(",", $case["person_phone"])[0] . "%"
                                        ),
                                        "groupby"   => "client_identity"
                                    ));


                                    if($selectCalls !== false) {

                                        $callStats = $selectCalls["data"][0];
                                        echo '<p class="text-muted">
                                                <span class="margin-r-5"><b>Call Count:</b> '. $callStats["totalCall"] .';</span> 
                                                <span class="margin-r-5"><b>Last Call:</b> '. time_elapsed_string($callStats["lastCallTime"]) .' ('. $callStats["lastCallTime"] .')</span> 
                                            </p>';
                                    } else {
                                        echo '<p class="text-muted">Sorry! No calls found.</p>';
                                    }

                                ?>

                                

          
                            <?php } else if( $case["case_customer"] !== null )  { ?>

                                <h3 class="profile-username">
                                    <?php echo $case["customer_name"]; ?>
                                    <small><a data-toggle="modal" href="<?php echo full_website_address(); ?>/xhr/?icheck=false&module=peoples&page=editCustomer&id=<?php echo $case["case_customer"]; ?>" data-target="#modalDefault"><i class="fa fa-edit"></i></a></small>
                                </h3>
                                
                                
                                <strong><i class="fa fa-book margin-r-5"></i> Contact</strong>
                                <p class="text-muted">
                                    <?php echo $case["customer_phone"] . ", " . $case["customer_email"];  ?>
                                </p>

                                <hr>

                                <strong><i class="fa fa-map-marker margin-r-5"></i> Address</strong>
                                <p class="text-muted"><?php echo $case["customer_address"]; ?></p>

                                <hr>

                                <strong><i class="fa fa-support margin-r-5"></i> Recent Case/ Tickets</strong>
                                
                                
                                <?php 
                                    $selectCaseForThisCustomer = easySelectA(array(
                                        "table"     => "cases",
                                        "fields"    => "case_id, case_title, case_status",
                                        "where"     => array(
                                            "is_trash = 0 and case_id != '{$_GET["case_id"]}'",
                                            " and case_customer" => $case["case_customer"]
                                        ),
                                        "orderby"   => array(
                                            "case_id"   => "DESC"
                                        ),
                                        "limit" => array(
                                            "start"     => 0,
                                            "length"    => 5
                                        )
                                    ));

                                    if($selectCaseForThisCustomer !== false) {
                                        foreach($selectCaseForThisCustomer["data"] as $customerCase) {
                                            echo "<p style='margin: 5px;'><a href='". full_website_address() ."/customer-support/case-list/?case_id={$personCase['case_id']}'>{$personCase['case_title']}</a> ({$personCase['case_status']})</p>";
                                            
                                        }
                                    } else {
                                        echo '<p class="text-muted">Sorry! No Ticket/ Cases found.</p>';
                                    }

                                ?>

                                <hr>

                                <strong><i class="fa fa-phone margin-r-5"></i> Call Log</strong>

                                <?php

                                    $selectCalls = easySelectA(array(
                                        "table"     => "calls",
                                        "fields"    => "count(*) as totalCall, max(call_datetime) as lastCallTime",
                                        "where"     => array(
                                            "client_identity LIKE" => explode(",", $case["customer_phone"])[0] . "%"
                                        ),
                                        "groupby"   => "client_identity"
                                    ));


                                    if($selectCalls !== false) {

                                        $callStats = $selectCalls["data"][0];
                                        echo '<p class="text-muted">
                                                <span class="margin-r-5"><b>Call Count:</b> '. $callStats["totalCall"] .';</span> 
                                                <span class="margin-r-5"><b>Last Call:</b> '. time_elapsed_string($callStats["lastCallTime"]) .' ('. $callStats["lastCallTime"] .')</span> 
                                            </p>';
                                    } else {
                                        echo '<p class="text-muted">Sorry! No calls found.</p>';
                                    }

                                ?>

                            <?php } else {
                                echo '<p class="text-muted">Sorry! No data found.</p>';
                            }
                            ?>

                        </div>

                    </div>

                    <form method="post" role="form" id="inlineForm" action="<?php echo full_website_address(); ?>/xhr/?module=customer-support&page=updateCase" enctype="multipart/form-data">
                        <div class="box">

                            <div class="box-header with-border">
                                <h3 class="box-title"><?php echo __("Case Properties"); ?></h3>
                            </div> <!-- box box-default -->

                            <div class="box-body">

                                <div class="form-group required">
                                    <label for="caseTitle"><?= __("Title:"); ?></label>
                                    <input type="text" name="caseTitle" id="caseTitle" value="<?php echo $case["case_title"]; ?>" class="form-control">
                                </div>

                                <div class="row">

                                    <div class="form-group col-md-6 required">
                                        <label for="casePriority"><?= __("Prority:"); ?></label>
                                        <select name="casePriority" id="casePriority" class="form-control" style="width: 100%;" required>
                                            <option value=""><?= __("Select Prority"); ?>....</option>
                                            <?php 
                                                $priority = array('Low', 'Medium', 'High', 'Critical');
                                                foreach($priority as $priority) {
                                                    $selected = $case["case_priority"] === $priority ? "selected" : "";
                                                    echo "<option {$selected} value='{$priority}'>{$priority}</option>";
                                                }
                                            ?>

                                        </select>
                                    </div>

                                    <div class="form-group col-md-6 required">
                                        <label for="caseType"><?= __("Type:"); ?></label>
                                        <select name="caseType" id="caseType" class="form-control" style="width: 100%;" required>
                                            <option value=""><?= __("Select Type"); ?>....</option>
                                            <?php 
                                                $type = array('Refund Request', 'Packaging Issues', 'Delivery Issue', 'Technical Issues', 'Query', 'Damaged Item', 'Exchange', 'Others');
                                                foreach($type as $type) {
                                                    $selected = $case["case_type"] === $type ? "selected" : "";
                                                    echo "<option {$selected} value='{$type}'>{$type}</option>";
                                                }
                                            ?>

                                        </select>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="caseStatus"><?= __("Status:"); ?></label>
                                        <select name="caseStatus" id="caseStatus" class="form-control" style="width: 100%;">
                                            <option value=""><?= __("Select Status"); ?>....</option>
                                            <?php 
                                                $status = array('Pending', 'Open', 'Replied', 'Customer Responded', 'Solved', 'Informed', 'On Hold');
                                                foreach($status as $status) {
                                                    $selected = $case["case_status"] === $status ? "selected" : "";
                                                    echo "<option {$selected} value='{$status}'>{$status}</option>";
                                                }
                                            ?>

                                        </select>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="caseSite"><?= __("Site:"); ?></label>
                                        <input type="text" name="caseSite" id="caseSite" value="<?php echo $case["case_site"] ?>" class="form-control">
                                    </div>

                                </div>


                                <div class="form-group">
                                    <label for="caseCustomer"><?= __("Customer:"); ?></label>
                                    <select name="caseCustomer" id="caseCustomer" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=customerList" style="width: 100%;">
                                        <option value=""><?= __("Select Customer"); ?>....</option>
                                        <?php 
                                            if($case["case_customer"] !== null) {
                                                echo "<option selected value='{$case["case_customer"]}'>{$case["customer_name"]}</option>";
                                            }
                                        ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="casePerson"><?= __("Person/ Lead:"); ?></label>
                                    <select name="casePerson" id="casePerson" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=personList" style="width: 100%;">
                                        <option value=""><?= __("Select Person/ Lead"); ?>....</option>
                                        <?php 
                                            if($case["case_person"] !== null) {
                                                echo "<option selected value='{$case["case_person"]}'>{$case["person_full_name"]}</option>";
                                            }
                                        ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="caseAssignTo"><?= __("Assign To:"); ?></label>
                                    <select name="caseAssignTo" id="caseAssignTo" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=userList" style="width: 100%;">
                                        <option value=""><?= __("Select user"); ?>....</option>
                                        <?php 
                                            if($case["case_assigned_to"] !== null) {
                                                echo "<option selected value='{$case["case_assigned_to"]}'>{$case["case_assign_name"]}</option>";
                                            }
                                        ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="caseBelongsTo"><?= __("Belongs To:"); ?></label>
                                    <select name="caseBelongsTo" id="caseBelongsTo" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=employeeList" style="width: 100%;">
                                        <option value=""><?= __("Select Employee"); ?>....</option>
                                        <?php 
                                            if($case["case_belongs_to"] !== null) {
                                                echo "<option selected value='{$case["case_belongs_to"]}'>{$case["case_belongs_to_name"]}</option>";
                                            }
                                        ?>
                                    </select>
                                </div>

                                <input type="hidden" name="case_id" value="<?php echo safe_entities($_GET["case_id"]); ?>">

                            </div>

                            <div class="box-footer">
                                <button type="submit" id="jqAjaxButton" class="btn btn-primary"><?php echo __("Update Case"); ?></button>
                            </div>

                        </div>
                
                    </form>
                </div>


            </div>
            <!-- row-->
        </section> <!-- Main content End tag -->

    <?php  } ?>


</div>
<!-- /.content-wrapper -->


<style>

    .case-title {
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
        padding: 10px;
        padding-left: 20px;
        position: sticky;
        top: 0;
        background-color: #fff;
        box-shadow: 0 2px 2px -2px rgb(34 47 62 / 10%), 0 8px 8px -4px rgb(34 47 62 / 7%);
        z-index: 1;
    }

    .case-title h1 {
        font-size: 24px;
        font-weight: bold;
        margin: 0;
        padding: 0;
        color: #000
    }
    .case-title p {
        padding-top: 5px;
        font-size: 14px;
        color: #767676;
    }

    .case-replies {
        height: 720px;
        overflow: auto;
    }

    .case-replies::-webkit-scrollbar {
        width: 4px;
    }

    .case-replies::-webkit-scrollbar-track {
        -webkit-box-shadow: inset 0 0 6px #598ecd; 
        border-radius: 10px;
    }

    .case-replies::-webkit-scrollbar-thumb {
        border-radius: 10px;
        -webkit-box-shadow: inset 0 0 6px #598ecd; 
    }

    .case-replies .reply {
        padding: 20px;
        padding-bottom: 30px;
        margin-top: 10px;
        margin-bottom: 10px;
        border-radius: 5px;
        background-color: #fff;
        border: 1px solid #ececec;
    }

    .case-replies .private {
        background-color: #f9ecec;
        border: 1px solid #ececec;
    }

    .reply .title span:first-child {
        font-size: 15px;
        font-weight: 600;
        color: #598ecd;
    }
    .reply .title span:last-child {
        float: right;
        color: #767676;
    }

    .reply .type {
        color: #767676;
        font-size: 13px;
        margin-top: 5px;
    }

    .reply .type span:first-child {
        margin-right: 10px;
    }

    .reply .type span i {
        margin-right: 5px;
    }

    .reply .reply-details {
        font-size: 14px;
        color: #000;
        margin-top: 20px;
        /* white-space: pre-line; */
    }

    .reply .attachment {
        margin-top: 30px;
        padding-top: 5px;
        border-top: 1px solid #ece8e8;
    }

    .reply .attachment p {
        font-weight: bold;
    }

    .imageAttachment {
        list-style-type: none;
    }

    .imageAttachment li {
        display: inline;
        margin: 5px;
        cursor: pointer;
    }

    .tox-tinymce {
        border-bottom-right-radius: 0;
        border-bottom-left-radius: 0;
    }

    .replyForm {
        border-bottom-left-radius: 10px;
        border-bottom-right-radius: 10px;
        box-shadow: none;
        border-top: none;
        margin-top: 5px;
        padding-top: 10px;
    }

    hr {
        margin: 15px 0;
    }


</style>

<script>
    /*Column Defference target column of data table */
    var DataTableAjaxPostUrl = "<?php echo full_website_address(); ?>/xhr/?module=customer-support&page=caseList";
    BMS.FUNCTIONS.dateRangePickerPreDefined({selector: "#caseAddedDate"});

    // Initialize the editor
    tinymce.init({
        selector: '#replyBox',
        menubar: false,
        plugins: 'link',
        toolbar: 'undo redo | styles | bold italic | alignleft aligncenter alignright alignjustify | link | outdent indent',
        statusbar: false,
        height : 350,
        default_link_target: "_blank",
        branding: false,
        invalid_elements : 'em,input,textarea,button',
    });

    // Initialz all attachment to viewer
    $('.imageAttachment').each((index, item) => {
        //console.log(item);
        new Viewer( item );
    });

    /** Add reply to cases */
    $(document).on("submit", "#submitCaseReply", function(e) {

        e.preventDefault();

        if( $("#replyBox").val() === "" ) {
            return alert("Please enter reply");
        }

        var formData = new FormData(this);       // Get all form data and store into formData 
        /**
         * Disable all input field until ajax request complete. 
         */
        $(this).find("input, select, button, textarea").prop("disabled", true);

        var that = this;

        $.ajax({
            url: full_website_address + "/xhr/?module=customer-support&page=addCaseReply",
            type: 'POST',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function (data, status) {

                if (status == "success") {


                    if (data.indexOf("danger") > 1) {
                        BMS.fn.alertError($(data).text());
                    } else {
                        
                        // clear content
                        tinymce.get("replyBox").setContent('');

                        // Insert the reply
                        $(data).insertAfter("#submitCaseReply").hide().show('fast');

                    }

                    $(that).find("input, select, button, textarea").prop("disabled", false);

                }

            }

        });

    });


</script>