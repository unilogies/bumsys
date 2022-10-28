<?php 

if( isset($_GET["export"]) and !isset($_POST["backupToken"]) or ( isset($_POST["backupToken"]) and $_POST["backupToken"] !== $_SESSION["csrf_token"] ) ) {

    header('HTTP/1.0 403 Forbidden');
    die("<strong>Error:</strong> You have no permission to access this resource.");

}

ini_set('memory_limit', '-1');
// Database Backup
if(isset($_GET["export"]) and isset($_GET["type"]) and $_GET["type"] === "db" ) {

    $filename = "database-backup_".date("Y-m-d_H:i");
    $format = "sql";

    if( !empty($_POST["backupName"]) ) {
        $filename = $_POST["backupName"];
    }

    if( !empty($_POST["backupFormat"]) ) {
        $format = $_POST["backupFormat"];
    }

    $filename = preg_replace("/[^a-z0-9\_\-\.]/i", '', $filename) . '.' . $format;

    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename='.$filename);
    header("Content-Transfer-Encoding: binary");
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');

    //$backupData = "";
    echo "-- \n";
    echo "-- Database Backup";
    echo "\n";
    echo "-- Export Created on ". date("Y-m-d H:i")."\n\n";
    echo "\n";
    echo "-- ************************************************\n";
    echo "-- Start exporting Data\n";
    echo "-- ************************************************\n";

    if(empty($_POST["selectedTable"])) {
        echo "-- No table selected";
        exit();
    }

    echo "\n";
    echo "\n";
    echo 'SET FOREIGN_KEY_CHECKS=0;' . "\n";
    echo "\n";
    echo "\n";
    echo "-- \n";


    // Generate the database/ tables backup
    foreach($_POST["selectedTable"] as $table) {

        echo "-- Data for {$table} table \n";
        echo "-- \n";

        // Select table data
        $tableData = $conn->query("SELECT * FROM {$table}");
        $tableData = $tableData->fetch_all();

        // Get table columns
        $tableColumns = $conn->query("SHOW COLUMNS FROM {$table}");
        $tableColumns = $tableColumns->fetch_all();

        $columns = "";
        foreach($tableColumns as $tKey => $col) {
            $columns .= "`" . $col[0] ."`, ";
        }
        $columns = substr($columns, 0, -2);


        // Create table chunk for more with 1000 for avoiding mysql max packets
        $tableDataChunk = array_chunk($tableData, 1000);

        foreach($tableDataChunk as $chunk) {

            // Insert into command
            echo "INSERT INTO `{$table}` \n({$columns}) VALUES \n";

            $rowData = "";
            foreach($chunk as $row) {

                $fieldData = "";
                foreach($row as $field) {

                    if(is_null($field)) {
                        
                        $fieldData .= " NULL,";

                    } elseif( is_numeric($field) ) {

                        $fieldData .= " ".$field.",";

                    } else {
                        
                        $fieldData .= " '". $conn->real_escape_string($field) ."',";

                    }

                }

                $rowData .= "(";
                $rowData .= substr(trim($fieldData), 0, -1);
                $rowData .= "),\n";

            }

            echo substr($rowData, 0, -2). ";";
            echo "\n";

            
        }


        echo "\n";
        echo "-- \n";

    }

    exit();

}


// Files Backup
if(isset($_GET["export"]) and isset($_GET["type"]) and $_GET["type"] === "files" ) {
    

    $filename = "database-backup_".date("Y-m-d_H:i");
    $format = "zip";

    if( !empty($_POST["backupName"]) ) {
        $filename = $_POST["backupName"];
    }

    if( !empty($_POST["backupFormat"]) ) {
        $format = $_POST["backupFormat"];
    }

    $filename = preg_replace("/[^a-z0-9\_\-\.]/i", '', $filename) . '.' . $format;

    $dir = DIR_UPLOAD;

    // This script taken from https://stackoverflow.com/a/4914807
    
    // Get real path for our folder
    $rootPath = realpath($dir);

    // Initialize archive object
    $zip = new ZipArchive();
    $zip->open($filename, ZipArchive::CREATE | ZipArchive::OVERWRITE);

    // Create recursive directory iterator
    /** @var SplFileInfo[] $files */
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($rootPath),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ($files as $name => $file)
    {
        // Skip directories (they would be added automatically)
        if (!$file->isDir())
        {
            // Get real and relative path for current file
            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen($rootPath) + 1);

            // Add current file to archive
            $zip->addFile($filePath, $relativePath);
        }
    }

    // Zip archive will be created only after closing object
    $zip->close();


    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename='.basename($filename));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filename));
    readfile($filename);

    // Delete the file after download
    unlink($filename);

}


?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?php echo __("Database and File Backup"); ?>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Settings</a></li>
            <li class="active">Department Settings</li>
            <li class="active">Department List</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">

        <ul class="nav nav-tabs">
            <li class="active"> <a href="#database_backup" data-toggle="tab">Database</a> </li>
            <li> <a href="#file_backup" data-toggle="tab">Files & Photos</a> </li>
            <li> <a href="#full_backup" data-toggle="tab">Full Backup</a> </li>
        </ul>
        <div style="background-color: white; border: 1px solid #ddd; border-top-color: transparent;" class="tab-content">

            <div class="tab-pane active box-body" id="database_backup">

                <form method="post" id="" action="?export&type=db&contentOnly=true">
                    
                    <div class="col-md-4">
                        <h3>Select Table</h3>
                        <div style="height: 520px; overflow: auto; padding-top: 15px; border-top: 1px solid #ddd" class="select-table">
                            <label style='cursor: pointer;'>
                                <input type="checkbox" id="selectAllTable" checked> <span style='margin-left: 10px;'>Select/ Unselect All table</span>
                            </label>
                            <div style="border-bottom: 1px solid #ddd; margin: 5px 0 10px 0;"></div>

                            <?php 

                                $getTable = $conn->query("SHOW TABLES");
                                while($table = $getTable->fetch_row() ) {
                                    echo "<label style='cursor: pointer;'> <input type='checkbox' name='selectedTable[]' value='{$table[0]}' checked> <span style='margin-left: 10px;'>{$table[0]}</span> </label><br/>";
                                }

                            ?>

                        </div>
                        <br/>
                    </div>
                    <div class="col-md-4">

                        <h3 style="border-bottom: 1px solid #ddd; padding-bottom: 10px;">Generate Backup</h3>
                        
                        <div class="form-group">
                            <label for="backupName">Backup Name:</label>
                            <input type="text" name="backupName" id="backupName" value="database-backup_<?php echo date("Y-m-d_H:i"); ?>" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="backupFormat">Format:</label>
                            <select name="backupFormat" id="backupFormat" class="form-control">
                                <option value="sql">SQL</option>
                            </select>
                        </div>
                        <input type="hidden" name="backupToken" value="<?php echo $_SESSION["csrf_token"]; ?>">
                        <button type="submit" class="btn btn-primary">Generate & Download Backup</button>

                    </div>

                </form>


            </div>

            <div class="tab-pane box-body" id="file_backup">
                
                <form id="filesBackup" method="post" action="?export&type=files&contentOnly=true">
                    <div class="col-md-6">
                        <h3 style="border-bottom: 1px solid #ddd; padding-bottom: 10px;">Generate Backup</h3>
                            
                        <div class="form-group">
                            <label for="backupName">Backup Name:</label>
                            <input type="text" name="backupName" id="backupName" value="files-backup_<?php echo date("Y-m-d_H:i"); ?>" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="backupFormat">Format:</label>
                            <select name="backupFormat" id="backupFormat" class="form-control">
                                <option value="zip">zip</option>
                            </select>
                        </div>
                        <input type="hidden" name="backupToken" value="<?php echo $_SESSION["csrf_token"]; ?>">
                        <button type="submit" class="btn btn-primary">Generate & Download Backup</button>
                        <br/><br/>
                    </div>
                </form>
                
            </div>

            <div class="tab-pane box-body" id="full_backup">
                <p>Full Backup</p>
            </div>

        </div>



    </section> <!-- Main content End tag -->
    <!-- /.content -->

</div>
<!-- /.content-wrapper -->

<script>

    var DataTableAjaxPostUrl = "<?php echo full_website_address(); ?>/xhr/?module=settings&page=departmentList";

    /** Toggle select table */
    $('#selectAllTable').on('change', function(event){

        var checkBoxes = $("input[name=selectedTable\\[\\]]");
        checkBoxes.prop("checked", !checkBoxes.prop("checked"));

    });

    /** Form submit for database backup */
    $(document).on("submit", "#databaseBackup", function(e) {
        
        e.preventDefault();

        if( $("input[name=selectedTable\\[\\]]:checked").length < 1 ) {
            
            return BMS.fn.alertError("Please select at least one table.");

        }

        var databaseBackupData = new FormData(this);
        var submitter = e.originalEvent.submitter;

        /** Add Loading Icon */
        $(submitter).html("Generate & Download Backup &nbsp;&nbsp; <i class='fa fa-spin fa-refresh'></i>");

        /** Send ajax data */
        $.ajax({
            url: full_website_address + "/xhr/?module=settings&page=generateDatabaseBackup",
            type: "post",
            data: databaseBackupData,
            cache: false,
            contentType: false,
            processData: false,
            success: function(data, status) {
                if(status == "success") {

                    /** Remove the loading Icon */
                    $(submitter).html("Generate & Download Backup &nbsp;&nbsp");

                    /** Open the download link */
                    window.location = full_website_address + '/assets/backup/'+data;

                }
            }
        });


    });



</script>