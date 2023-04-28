<?php 

$selectSipCredentials = easySelectA(array(
    "table"     => "sip_credentials",
    "fields"    => "sip_username, sip_password, sip_domain, sip_websocket_addr",
    "where"     => array(
        "sip_representative"    => $_SESSION["uid"]
    )
));

if($selectSipCredentials !== false) {
    $sip = $selectSipCredentials["data"][0];
    echo "<script> const sipCredentials = {
        uri: 'sip:{$sip['sip_username']}@{$sip['sip_domain']}',
        socket: '{$sip['sip_websocket_addr']}',
        user: '{$sip['sip_username']}',
        pass: '{$sip['sip_password']}'
    }; </script>";
}

?>

<!-- Content Wrapper. Contains page content -->
<script async src="<?php echo full_website_address(); ?>/js/?q=voiceMessage&v=2.0.3"></script>
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?php echo __("Voice Message Broadcasting System"); ?>
        <a data-toggle="modal" data-target="#modalDefault" href="<?php echo full_website_address(); ?>/xhr/?module=customer-support&page=newVoiceMessageEntry" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> <?= __("Add New"); ?></a>
      </h1>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">

        <div class="row">

            <div class="col-md-8">

                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title"><?= __("Voice Message List"); ?></h3>
                    </div>
                    <!-- Box header -->
                    <div class="box-body">
                        <table dt-height="30vh" dt-data-url="<?php echo full_website_address(); ?>/xhr/?module=customer-support&page=voiceMessageList" class="dataTableWithAjaxExtend addToSMSBox table table-bordered table-striped table-hover" width="100%">
                            <thead>
                            <tr>
                                <th></th>
                                <th><?php echo __("Date"); ?></th>
                                <th><?php echo __("Description"); ?></th>
                                <th><?php echo __("Record"); ?></th>
                                <th><?php echo __("Status"); ?></th>
                                <th><?php echo __("Action"); ?></th>
                            </tr>
                            </thead>
            
                            <tfoot>
                            <tr>
                                <th></th>
                                <th><?php echo __("Date"); ?></th>
                                <th><?php echo __("Description"); ?></th>
                                <th><?php echo __("Record"); ?></th>
                                <th><?php echo __("Status"); ?></th>
                                <th><?php echo __("Action"); ?></th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                    <!-- box body-->
                </div>
                <!-- box -->

            </div>

            <!-- terminal --> 
            <div class="col-md-4">

                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title"><?= __("Terminal"); ?></h3>
                    </div>
                    <!-- Box header -->
                    <div style="height: 340px; background-color: black; color: white; overflow: auto;" class="box-body">
                       <ul class="voiceMessageTerminal" style="list-style: none; padding-left: 10px;">
                           <li>Click Start Sending to initiate the terminal</li>
                       </ul>
                    </div>
                    <!-- box body-->
                </div>
                <!-- box -->
                
            </div>
        
        </div>
        <!-- row-->

    </section> <!-- Main content End tag -->
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
