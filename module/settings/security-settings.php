<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?= __("Security"); ?>
        </h1>
    </section>


    <!-- Main content -->
    <section class="content container-fluid">
        <div class="row">

            <div class="col-xs-7">

                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">
                            <?= __("Firewall"); ?>
                            <a data-toggle="modal" data-target="#modalDefault" href="<?php echo full_website_address(); ?>/xhr/?module=settings&page=newFirewallRole" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> <?= __("New Role"); ?></a>
                        </h3>
                    </div>
                    <!-- Box header -->
                    <div class="box-body">
                        <table id="dataTableWithAjaxExtend" class="table table-striped table-hover" width="100%">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th><?php echo __("Time"); ?></th>
                                    <th><?php echo __("Status"); ?></th>
                                    <th><?php echo __("Ip Address"); ?></th>
                                    <th><?php echo __("Action"); ?></th>
                                    <th><?php echo __("Comment"); ?></th>
                                    <th><?php echo __("Action"); ?></th>
                                </tr>
                            </thead>
            
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th><?php echo __("Time"); ?></th>
                                    <th><?php echo __("Status"); ?></th>
                                    <th><?php echo __("Ip Address"); ?></th>
                                    <th><?php echo __("Action"); ?></th>
                                    <th><?php echo __("Comment"); ?></th>
                                    <th><?php echo __("Action"); ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <!-- box body-->
                </div>
                <!-- box -->

            </div>

            <div class="col-xs-5">
                <div class="box">
                    
                    <div class="box-header with-border">
                        <h3 class="box-title">Security Settings</h3>
                    </div> <!-- box box-default -->
                    <!-- Form start -->
                    <form method="post" role="form" id="jqFormAdd" action="<?php echo full_website_address(); ?>/xhr/?module=settings&page=saveSystemSettings">
                        <div class="box-body">

                            <div class="form-group">
                                <label for="autoLogoutTime"><?= __("Auto Logout time: (In seconds)"); ?></label>
                                <input type="number" name="autoLogoutTime" id="autoLogoutTime" value="<?php echo get_options("autoLogoutTime"); ?>" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="maxInvalidLoginAttemptToBlockUser"><?= __("Max Invalid Login Attempt to Block the user:"); ?></label>
                                <input type="number" min="-1" placeholder="-1 for Unlimited/ Disable" name="maxInvalidLoginAttemptToBlockUser" id="maxInvalidLoginAttemptToBlockUser" class="form-control" value="<?= get_options("maxInvalidLoginAttemptToBlockUser"); ?>">
                            </div>
                            <div class="form-group">
                                <label for="maxInvalidLoginAttemptToBlockHost"><?= __("Max Invalid Login Attempt to Block the Host/IPs:"); ?></label>
                                <input type="number" min="-1" placeholder="-1 for Unlimited/ Disable" name="maxInvalidLoginAttemptToBlockHost" id="maxInvalidLoginAttemptToBlockHost" class="form-control" value="<?= get_options("maxInvalidLoginAttemptToBlockHost"); ?>">
                            </div>
                            <div class="form-group">
                                <label for="canAccessOnlyPermittedIP"><?= __("Can Access Only Permitted IP:"); ?></label>
                                <select name="canAccessOnlyPermittedIP" id="canAccessOnlyPermittedIP" class="form-control select2">
                                    <option <?php echo get_options("canAccessOnlyPermittedIP") == "1" ? "selected" : ""; ?> value="1">Yes</option>
                                    <option <?php echo get_options("canAccessOnlyPermittedIP") == "0" ? "selected" : ""; ?> value="0">No</option>
                                </select>
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
            <!-- col-xs-5-->

            

        </div>
        <!-- row-->

    </section> <!-- Main content End tag -->
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<script>
    
    var scrollY = "";
    var DataTableAjaxPostUrl = "<?php echo full_website_address(); ?>/xhr/?module=settings&page=firewallList";
  </script>
