<style>
    
    .tab-pane .row {
        margin-top: 10px;
    }

    .message-item, .call-item {
        border-bottom: 1px solid whitesmoke;
        padding: 5px 0;
    }
    .message-item p, .call-item p {
        margin: 2px;
    }

    .cc-title {
        margin: 0; 
        padding-bottom: 10px; 
        font-size: 20px; 
        border-bottom: 1px solid #dcdbdb;
    }

    .numberSearch {
        padding: 18px 0;
    }

    .number-dialer-div {
        border: 1px solid #d2d6de;
        position: absolute;
        z-index: 1;
    }

    .number-dialer-div:focus-within {
        border-color:#3c8dbc;
    }

    .number-dialer-div * {
        border: none;
    }

    .disable-number-dialer-div {
        background-color: #eeeeee !important;
        cursor: not-allowed;
    }

    .suggestion-numbers {
        display: none;
        max-height: 340px;
        overflow: auto;
        background-color: white;
        padding: 10px;
        box-shadow: rgba(0, 0, 0, 0.07) 0px 1px 1px, rgba(0, 0, 0, 0.07) 0px 2px 2px, rgba(0, 0, 0, 0.07) 0px 4px 4px, rgba(0, 0, 0, 0.07) 0px 8px 8px, rgba(0, 0, 0, 0.07) 0px 16px 16px;
    }

    .suggestion-numbers .item {
        padding: 10px 0 10px 5px;
        position: relative;
    }

    .suggestion-numbers .item:not(:last-child) {
        border-bottom: 1px solid #dcdbdb;
    }

    .suggestion-numbers .item span {
        display: block;
        color: #2e82b2;
        font-weight: bold;
        cursor: pointer;
    }

    .suggestion-numbers .item > button {
        position: absolute;
        right: .5em;
        top: 50%;
        transform: translate(0,-50%);
    }

    .call-controller {
        margin-top: 55px;
    }

    .call-dialer {
        min-height: 80px;
        margin-bottom: 10px;
        text-align: center;
    }

    .statistics {
        position: absolute;
        width: 100%;
        bottom: -30px;
        /* background-color: #ecf0f5; */
        /* height: 120px; */
    }

    .agent-live-stats-box {
        height: 410px;
        overflow: auto;
        margin-top: 10px;
    }

    .agent-live-stats-box p {
        margin: 0;
    }

    .agent-live-stats-box .name {
        font-size: 18px;
    }

    .agent-live-stats-box .agent {
        padding: 12px 0;
        margin: 0;
    }

    .agent-live-stats-box .agent:not(:last-child) {
        border-bottom: 1px solid #dcdbdb;
    }


    .call-history {
        max-height: 320px;
        overflow: auto;
    }



</style>

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
    <script async src="<?php echo full_website_address(); ?>/js/?q=cs&v=2.2.6"></script>

    <!-- Main content -->
    <section class="content container-fluid">

      
        <!-- Left Column -->
        <div style="height: 86vh" class="col-lg-3 col-md-4">

            <div class="number-dialer-div">
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-search"></i>
                    </span>
                    <input type="text" placeholder="Type name or number" class="form-control numberSearch">
                    <span class="input-group-addon">
                        <i class="fa fa-caret-down"></i>
                    </span>
                </div>
                <div class="suggestion-numbers"> </div>

            </div>


            <div class="call-controller">


                <h3 style="margin-top: 10px; margin-bottom: 15px;" id="status"></h3>
                <h3 style="margin: 0 0 15px 0; display: none;" id="timer">00:00:00</h3>
                

                <div class="call-dialer">
                    
                    <button id="callButton" type="button" class="btn btn-success btn-flat"><i class="fa fa-fw fa-phone"></i> Dial</button>
                    <button style="display: none;" id="hangButton" type="button" class="btn btn-danger btn-flat"><i class="fa fa-fw fa-phone"></i> End Call</button>

                </div>

                <div style="display: none; min-height: 80px;" id="callReceiver">
                    
                    <button id="callReceiveButton" type="button" class="btn btn-success btn-flat"><i class="fa fa-fw fa-phone"></i> Accept</button>
                    <button id="IncomingCallHangButton" type="button" class="btn btn-danger btn-flat"><i class="fa fa-fw fa-phone"></i> Reject</button>

                </div>
                
                <div class="row">
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
                </div>

                <div style="margin-top: 15px; margin-left: 10px;" class="form-group">
                    <button id="addNewCase" class="btn btn-primary">Add Case/ Issue</button>
                    <a data-toggle="modal" data-target="#modalDefault" href="<?php echo full_website_address(); ?>/xhr/?module=marketing&page=newPerson" class="btn btn-primary"><?= __("New Lead"); ?></a>
                    <!-- <button class="btn btn-primary">View Specimen Copies</button> -->
                </div>


                <?php
                if ($selectSipCredentials === false) {
                    _e("Sorry! No credentials found. This module will not work.");
                }
                ?>
                <div style="padding: 5px; display: none; margin-top: 10px;" class="dndAlert alert-danger">Do not Disturb mode is enabled. No incoming call will work.</div>

            </div>

            <?php if(current_user_can("customer_support_dashboard.View")) { ?>
                <div style="position: absolute; bottom: -25px; width: 95%;" class="box box-success">

                    <div class="box-header with-border">
                        <h3 class="box-title">Live status</h3>
                    </div>
                    <div class="box-body agent-live-stats-box">

                        <?php 
                            $selectUserExtension = easySelectA(array(
                                "table"     => "sip_credentials as sip",
                                "fields"    => "sip_username as ext, emp_id, emp_firstname, emp_lastname",
                                "join"      => array(
                                    "left join {$table_prefix}users as user on user_id = sip_representative",
                                    "left join {$table_prefix}employees on emp_id = user_emp_id"
                                ),
                                "where" => array(
                                    "user.is_trash = 0 and user_status = 'Active'"
                                )
                            ));

                            if( $selectUserExtension !== false ) {

                                foreach($selectUserExtension["data"] as $key => $agent ) {

                                    echo "<div class='row agent'>
                                            <div class='col-md-3'>
                                                <img width='50px' height='50px' src='". full_website_address() ."/images/?for=employees&id={$agent["emp_id"]}' class='img-circle' />
                                            </div>
                                            <div class='col-md-9'>
                                                <p class='name'>{$agent['emp_firstname']} {$agent['emp_lastname']}</p>
                                                <p class='agentExt{$agent['ext']}'>Available</p>
                                            </div> 
                                        </div>";

                                }

                            }

                        ?>

                        

                    </div>

                </div>
            
            <?php } ?>

            
        
        </div>

        <!-- Right Column -->
        <div style="height: 85vh;" class="col-lg-9 col-md-8">

            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs call-center-tabs">
                    <li class=""> <a href="#tab_script" data-toggle="tab">Script</a> </li>
                    <li class="" > <a href="#tab_leads" data-toggle="tab">Leads</a> </li>
                    <li class="active"> <a href="#tab_messages" data-toggle="tab">Messages <span class="tabMessageCount"></span> </a> </li>
                    <li class="hidden_tab" style="display: none;"> <a href="#tab_products" data-toggle="tab">Products</a> </li>
                    <li class="hidden_tab" style="display: none;" class='feedback_tab'> <a href="#tab_feedback" data-toggle="tab">Feedback</a> </li>
                </ul>
                <div class="tab-content">

                    <div class="tab-pane" id="tab_script">

                        <p>Bangladesh is a land of rivers. Most of her lands are lowings</p>
                        <p>Bangladesh is a land of rivers. Most of her lands are lowings</p>
                        <p>Bangladesh is a land of rivers. Most of her lands are lowings</p>
                        <p>Bangladesh is a land of rivers. Most of her lands are lowings</p>
                        <p>Bangladesh is a land of rivers. Most of her lands are lowings</p>
                        <p>Bangladesh is a land of rivers. Most of her lands are lowings</p>
                        <p>Bangladesh is a land of rivers. Most of her lands are lowings</p>
                        <p>Bangladesh is a land of rivers. Most of her lands are lowings</p>
                        <p>Bangladesh is a land of rivers. Most of her lands are lowings</p>

                    </div> <!-- End Script Tab -->

                    <div class="tab-pane" id="tab_leads">

                        <div class="row">

                            <div class="col-md-8">
                                
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
                                        <td><?php echo __("Action"); ?></td>
                                        <td></td>
                                    </tr>
          
                                </table>

                                <div style="display: none" class="dataToCopy">

                                </div>

                                <br/>
                                <h2 class="cc-title"><?= __("Order/ Service"); ?></h3>
                                <table class="table callerOrderInfo">
                                    <thead>
                                        <tr>
                                            <th><?php echo __("Date"); ?></th>
                                            <th><?php echo __("Reference"); ?></th>
                                            <th><?php echo __("Amount"); ?></th>
                                            <th><?php echo __("Payment"); ?></th>
                                            <th style="width: 220px;" type="select" 
                                                data-options="Order Placed,In Production,Processing,Call not Picked,Confirmed,Hold,Delivered,Cancelled"
                                                where-to-update="<?php echo full_website_address(); ?>/xhr/?module=my-shop&page=changeSaleStatus"
                                                class="sort text-center no-print"><?php echo __("Status"); ?>
                                            </th>
                                            <th><?php echo __("Action"); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr><td class="text-center" colspan="5">Sorry! No order found.</td></tr>
                                    </tbody>
                                </table>

                            </div>
                            <div class="col-md-4">

                                <h2 class="cc-title"><?= __("Call History"); ?> <small style="font-weight: bold" class="call-count"></small> </h3>
                                <div class="call-history">

                                    <p>No call</p>
                                    
                                </div>

                                <br/>
                                <h2 class="cc-title"><?= __("Cases"); ?></h3>
                                <div class="case-history">

                                    <p>No Cases</p>
                                    
                                </div>

                            </div>

                        </div>

                    </div> <!-- End Leads Tab -->

                    <div class="tab-pane active" id="tab_messages">
                        
                        <div class="row">

                            <!-- Send Message Box -->
                            <div class="col-md-6">

                                <br/>
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
                                
                            </div> <!-- End Send Message Box -->
                            

                            <div class="col-md-6">

                                <h2 class="cc-title"><?= __("Notes"); ?></h3>
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
                                
                                <br/>
                                <h2 class="cc-title"><?= __("Message History"); ?></h3>
                                <div class="message-history">

                                    <p>No SMS</p>
                                    
                                </div>

                            </div>


                        </div>

                    </div> <!-- End Message Tab -->

                    <div class="tab-pane" id="tab_products">
                        
                    </div> <!-- End Products Tab -->

                    <div class="tab-pane" id="tab_feedback">

                        <div class="row" style="margin-left: 0;">

                            <div class="col-md-6">

                                <div class="form-group">
                                    <label for="callFeedback"><?= __("Call Feedback"); ?></label>
                                    <textarea name="callFeedback" id="callFeedback" cols="30" rows="5" placeholder="Enter feedback here..." class="form-control"></textarea>
                                </div>
                                
                                
                                <div class="row">

                                    <div class="form-group col-md-6">
                                        <label for="callReason"><?= __("Call Reason"); ?></label>
                                        <select name="callReason[]" id="callReason" class="form-control select2Ajax" select2-tag="true" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=callReasonList" style="width: 100%;">
                                            <option value=""><?= __("Select Reason"); ?>....</option>
                                        </select>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="specimenCopyReceived"><?= __("Specimen Copy Received"); ?></label>
                                        <select name="specimenCopyReceived" id="specimenCopyReceived" class="form-control">
                                            <option value="">Select one...</option>
                                            <option value="No">No</option>
                                            <option value="Yes">Yes</option>
                                            <option value="Partial">Partial</option>
                                            <option value="Not Sure">Not Sure</option>
                                        </select>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="feedbackInformative"><?= __("Informative"); ?></label>
                                        <select name="feedbackInformative" id="feedbackInformative" class="form-control">
                                            <option value="">Select one...</option>
                                            <option value="No">No</option>
                                            <option value="Yes">Yes</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="SaleOurProduct"><?= __("Sale Our Book"); ?></label>
                                        <select name="SaleOurProduct" id="SaleOurProduct" class="form-control">
                                            <option value="">Select one...</option>
                                            <option value="No">No</option>
                                            <option value="Yes">Yes</option>
                                            <option value="Sold Before">Sold Before</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="userOurProduct"><?= __("Use Our Book"); ?></label>
                                        <select name="userOurProduct" id="userOurProduct" class="form-control">
                                            <option value="">Select one...</option>
                                            <option value="No">No</option>
                                            <option value="Yes">Yes</option>
                                            <option value="Used Before">Used Before</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="mrFeedback"><?= __("MR Feedback"); ?></label>
                                        <select name="mrFeedback" id="mrFeedback" class="form-control">
                                            <option value="">Select one...</option>
                                            <option value="No Comment">No Comment</option>
                                            <option value="Positive">Positive</option>
                                            <option value="Negative">Negative</option>                                            
                                        </select>
                                    </div>
                                    
                                </div>
                                
                                <div class="form-group">
                                    <label for="otherInformation"><?php echo __("Other Info"); ?></label>
                                    <textarea name="otherInformation" id="otherInformation" cols="30" rows="2" placeholder="Enter any other information here..." class="form-control"></textarea>
                                </div>

                                <div class="row">
                                    
                                    <div class="form-group col-md-6">
                                        <input type="text" placeholder="Caller" name="feedbackCaller" id="feedbackCaller" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <input id="saveFeedback" type="button" value="Save" class="btn btn-primary">
                                    </div>
                                </div>

                            </div>

                            <div class="col-md-6">
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
                            </div>

                        </div>
                        
                    </div> <!-- End feedback Tab -->


                </div>

            </div>


            <?php if(current_user_can("customer_support_dashboard.View")) { ?>

                <div class="statistics">

                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-green"><i class="fa fa-flag-o"></i></span>

                            <div class="info-box-content">
                            <span class="info-box-text">Agent In Call</span>
                            <span class="info-box-number inCallAgent">0</span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                    </div>
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-green"><i class="fa fa-flag-o"></i></span>

                            <div class="info-box-content">
                            <span class="info-box-text">Busy Agent</span>
                            <span class="info-box-number busyAgentCount">0</span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                    </div>
                    <div class="col-md-3 col-sm-6 col-xs-12 ">
                        <div class="info-box">
                            <span class="info-box-icon bg-green"><i class="fa fa-flag-o"></i></span>

                            <div class="info-box-content">
                            <span class="info-box-text">Free Agent</span>
                            <span class="info-box-number freeAgentCount">0</span>
                            </div> 
                            <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                    </div>
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-green"><i class="fa fa-flag-o"></i></span>

                            <div class="info-box-content">
                            <span class="info-box-text">Agent Unavailable</span>
                            <span class="info-box-number unableAgentCount">0</span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                    </div>

                </div>

            <?php } ?>

        </div>




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


    // Hide the suggestion numbers when escape key presed
    $(document).on("keydown", ".numberSearch", function(e) {
        
        var keyName = e.key;
        if( keyName === "Escape" ) {
            $(".suggestion-numbers").html("").slideUp("fast");
        }

    });


    // Hide the suggestion numbers when click outside of the it
    $(document).mouseup(function(e) {

        var container = $(".number-dialer-div");

        if( !container.is(e.target) && container.has(e.target).length === 0 ) {

            $(".suggestion-numbers").html("").slideUp("fast");

        }

    });


    var numberSearchTimeout;
    $(document).on("input focus", ".numberSearch", function(e) {        

        var searchText = $(this).val();

        if( searchText.length > 1 ) { // If the input length at least three

            clearTimeout( numberSearchTimeout );

            numberSearchTimeout = setTimeout(function() {

                BMS.fn.get("searchContact&s="+ searchText, function(results) {

                    if(results !== "") {

                        var contactSuggestion = "";
                        results.forEach(item => {
                            contactSuggestion += `<div class="item">
                                                    <span onclick="showCallerDetails('${item.number}')" >${item.name} (${item.type})</span>
                                                    <span onclick="showCallerDetails('${item.number}')">${item.number}</span>
                                                    <button onclick="dial('${item.number}')" type="button" class="btn btn-success btn-flat callButton"><i class="fa fa-fw fa-phone"></i> Call</button>
                                                </div>`;
                        });

                        /** Show the suggestion numbers */
                        $(".suggestion-numbers").html(contactSuggestion).slideDown("fast");

                    } else {

                        // Hide the suggestion numbers
                        $(".suggestion-numbers").html("").slideUp("fast");
                        
                    }

                });
                

            }, 400);


        } else {

            // Hide the suggestion numbers
            $(".suggestion-numbers").html("").slideUp("fast");

        }
    
    });


    
    <?php if(current_user_can("customer_support_dashboard.View")) { ?>

        function showAgentsStats() {

            setTimeout(() => {

                $.ajax({
                    url: `${full_website_address}/ami.php`,
                    timeout: 3000,
                    success: function(data) {

                        showAgentsStats();
                        
                        var data = JSON.parse(data);

                        $(".inCallAgent").html(data.agent_stats.inCallAgent);
                        $(".busyAgentCount").html(data.agent_stats.busyAgent);
                        $(".freeAgentCount").html(data.agent_stats.freeAgent);
                        $(".unableAgentCount").html(data.agent_stats.unableAgent);
                        
                        if( Object.keys(data.agent_status).length > 0) {

                            $.each(data.agent_status, function(extens, item) {

                                if(item.state === "") {

                                    $(".agentExt" + extens ).html(`${item.status}`);

                                } else {
                                    
                                    $(".agentExt" + extens ).html(`${item.type} (${item.number}), ${item.state}`);

                                }


                            });

                        }

                    },
                    error: function() {
                        
                        showAgentsStats();

                    }

                });
                
            }, 3000);

        }

        // Initial States
        showAgentsStats();


    <?php } ?>


</script>