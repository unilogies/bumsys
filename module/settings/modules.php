  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?php echo __("Modules"); ?>
        <a data-toggle="modal" data-target="#modalDefault" href="<?php echo full_website_address(); ?>/xhr/?tooltip=true&module=settings&page=newDepartment" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> <?= __("Add New"); ?></a>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Settings</a></li>
        <li class="active">Department Settings</li>
        <li class="active">Department List</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
      <div class="row">

            <?php 

                //print_r($activeModule);

                // Active the module
                if( isset($_GET["active"]) and basename($_GET["active"]) === "introductory.php" AND !in_array($_GET["active"], $activeModule) ) {
                        
                    $activeModule[] = $_GET["active"];
                    set_options("activeModule", serialize($activeModule));

                }

                // Deactive the module 
                if( isset($_GET["deactive"]) ) {

                    // Generate the new module array by removing the targeted deactive module
                    $activeModule = array_diff($activeModule, array($_GET["deactive"]));
                    set_options("activeModule", serialize($activeModule));

                }


                $searchModule = glob("module/*/introductory.php");


                foreach($searchModule as $module) {

                    $moduleDetails = getFirstComment($module);
                    $module_address = substr($module, 0, strrpos($module, "/"));
                    $developer = "<a target='_blank' href='". find_modules("Developer URI", $moduleDetails) ."'>". find_modules("Developer", $moduleDetails) ."</a>";
                    $moduleImage = file_exists($module_address .'/screenshot.png') ?  '<img width="100%" src="'. full_website_address() . "/". $module_address .'/screenshot.png"></img>' : '';

                    $activeDeactiveButton = '<a class="btn btn-sm btn-success" href="?active='. urlencode($module) .'">Active</a>';

                    $boxClass = "box-default";
                    if( in_array($module, $activeModule) ) {
                        $activeDeactiveButton = '<a class="btn btn-sm btn-primary" href="?deactive='. urlencode($module) .'">Deactive</a>';
                        $boxClass = "box-success";
                    }
                    

                    echo '<div class="col-md-6">
                        <div class="box '. $boxClass .'">
                            <div class="box-header with-border">
                                <h3 class="box-title">'. __( find_modules("Module Name", $moduleDetails) ) .'</h3>
                            </div>
                            <div class="box-body">
                                '. $moduleImage .'
                                <p style="font-size: 16px; margin-top: 10px;">'. __( find_modules("Description", $moduleDetails) ) .' </p>
                                <p style="border-top: 1px solid gray; border-bottom: 1px solid gray; padding: 5px 0 5px 0;"> 
                                    <span style="margin-right: 10px;"><strong>Version: </strong>'. __( find_modules("Version", $moduleDetails) ) .'</span>
                                    <span style="margin-right: 10px;"><strong>Developer: </strong>'.  $developer .'</span>
                                </p>
                                '. $activeDeactiveButton .'
                                <a class="btn btn-sm btn-danger" href="#">Delete</a>
                            </div>
                        </div>
                    </div>';
                    

                }

            ?>
          

      </div>
      <!-- row-->

    </section> <!-- Main content End tag -->
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <script>
    var DataTableAjaxPostUrl = "<?php echo full_website_address(); ?>/xhr/?module=settings&page=departmentList";
  </script>
