<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?= __("Lead Statistics"); ?>
        </h1>
    </section>



    <!-- Main content -->
    <section class="content container-fluid">

        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-md-4 col-sm-6 col-xs-12">
                <!-- small box -->
                <div class="small-box bg-green">
                    <div class="inner">
                        <h3>
                            <?php
                                $totalLeads =  easySelectD(" SELECT count(*) as totalLeads from {$table_prefix}persons");

                                $totalLeadsCount = 0;
                                if ($totalLeads !== false) {
                                    $totalLeadsCount = number_format($totalLeads["data"][0]["totalLeads"], 2);
                                }

                                echo __($totalLeadsCount);

                            ?>
                        </h3>

                        <p><?= __("Total Leads"); ?></p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-users"></i>
                    </div>
                    <a href="<?php echo full_website_address(); ?>/marketing/person-list/" class="small-box-footer"><?= __("Person List"); ?> <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-md-4 col-sm-6 col-xs-12">
                <!-- small box -->
                <div class="small-box bg-aqua">
                    <div class="inner">
                        <h3>
                            <?php 
                                
                                $specimenCount = easySelectD(" SELECT sum(scd_product_qnt) as totalSpecimen FROM {$table_prefix}sc_distribution where is_trash=0"); 

                                $specimenCopyGiven = 0;
                                if($specimenCount !== false) {
                                    $specimenCopyGiven = number_format($specimenCount["data"][0]["totalSpecimen"], 2);
                                }
                                
                                echo __($specimenCopyGiven);

                            ?>
                        </h3>

                        <p><?= __("Total Specimen Copy Given"); ?></p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-smile-o"></i>
                    </div>
                    <a href="<?php echo full_website_address(); ?>/marketing/specimen-copy-distributions/" class="small-box-footer"><?= __("Distribution List"); ?> <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-md-4 col-sm-6 col-xs-12">
                <!-- small box -->
                <div class="small-box bg-yellow">
                    <div class="inner">
                        <h3><?php echo __(easySelectD("SELECT count(product_id) as totalProduct FROM {$table_prefix}products")["data"][0]["totalProduct"]); ?></h3>

                        <p><?= __("Total Products"); ?></p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-cube"></i>
                    </div>
                    <a href="<?php echo full_website_address(); ?>/products/product-list/" class="small-box-footer"><?= __("Products List"); ?> <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->
        </div>
        <!-- /.row -->

        <div class="row">

            <!-- Pie Chart of Current year by Month -->
            <div class="col-xs-5">
                <div class="box box-info">
                    <div class="box-header">
                        <h3 class="box-title"><?= __("Lead Chart"); ?></h3>
                    </div>
                    <div class="box-body">
                        <div class="chart">
                            <canvas id="leadChart" style="height: 425px"></canvas>
                        </div>
                    </div>
                </div>
            </div> <!-- /.Col -->

            <div class="col-xs-7">
                <div class="box box-info">
                    <div class="box-header">
                        <h3 class="box-title"><?= __("Student Chart"); ?></h3>
                    </div>
                    <div class="box-body">
                        <div class="chart">
                            <canvas id="studentChart" style="height: 425px"></canvas>
                        </div>
                    </div>
                </div>
            </div> <!-- /.Col -->

        </div> <!-- /.Row -->


    </section> <!-- Main content End tag -->
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?php

$leadsCount = easySelectD("
    SELECT 
        count(CASE WHEN person_type = 'Teacher' then person_type end) as totalTeacher,
        count(CASE WHEN person_type = 'Student' then person_type end) as totalStudent,
        count(CASE WHEN person_type = 'Guardian' then person_type end) as totalGuardian,
        count(CASE WHEN person_type = 'Service Holder' then person_type end) as totalServiceHolder,
        count(CASE WHEN person_type = 'Merchant' then person_type end) as totalMerchant
    FROM {$table_prefix}persons
")["data"][0];


$studentsCount = easySelectD("
    SELECT 
        count(CASE WHEN person_student_class = 1 then person_student_class end) as classOne,
        count(CASE WHEN person_student_class = 2 then person_student_class end) as classTwo,
        count(CASE WHEN person_student_class = 3 then person_student_class end) as classThree,
        count(CASE WHEN person_student_class = 4 then person_student_class end) as classFour,
        count(CASE WHEN person_student_class = 5 then person_student_class end) as classFive,
        count(CASE WHEN person_student_class = 6 then person_student_class end) as classSix,
        count(CASE WHEN person_student_class = 7 then person_student_class end) as classSeven,
        count(CASE WHEN person_student_class = 8 then person_student_class end) as classEight,
        count(CASE WHEN person_student_class = 9 then person_student_class end) as classNine,
        count(CASE WHEN person_student_class = 10 then person_student_class end) as classTen,
        count(CASE WHEN person_student_class = 11 then person_student_class end) as classEleven,
        count(CASE WHEN person_student_class = 12 then person_student_class end) as classTwelve,
        count(CASE WHEN person_student_class = 13 then person_student_class end) as classGraduation
    FROM {$table_prefix}persons
    where person_type = 'Student'
")["data"][0];

//print_r($studentsCount);

?>


<script src="<?php echo full_website_address(); ?>/assets/3rd-party/chart.js/Chart.min.js"></script>

<script>
    var ctx = document.getElementById('leadChart');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [
                "<?= __("Teacher"); ?>",
                "<?= __("Student"); ?>",
                "<?= __("Guardian"); ?>",
                "<?= __("Service Holder"); ?>",
                "<?= __("Merchant"); ?>"
            ],
            datasets: [{
                label: "",
                minBarLength: 2,
                data: [
                    "<?php echo $leadsCount['totalTeacher']; ?>",
                    "<?php echo $leadsCount['totalStudent']; ?>",
                    "<?php echo $leadsCount['totalGuardian']; ?>",
                    "<?php echo $leadsCount['totalServiceHolder']; ?>",
                    "<?php echo $leadsCount['totalMerchant']; ?>"
                ],
                backgroundColor: [
                    "#00c0ef",
                    "red",
                    "green",
                    "blue",
                    "orange"
                ],
            }],

        },
        options: {
            responsive: true,
            legend: {
                display: false,
                position: "right"
            },
            scales: {
                y: {
                    beginAtZero: true,
                }
            }
        }
    });


    var studentChart = document.getElementById('studentChart');
    new Chart(studentChart, {
        type: 'bar',
        data: {
            labels: [
                "<?= __("Class One"); ?>",
                "<?= __("Class Two"); ?>",
                "<?= __("Class Three"); ?>",
                "<?= __("Class Four"); ?>",
                "<?= __("Class Five"); ?>",
                "<?= __("Class Six"); ?>",
                "<?= __("Class Seven"); ?>",
                "<?= __("Class Eight"); ?>",
                "<?= __("Class Nine"); ?>",
                "<?= __("Class Ten"); ?>",
                "<?= __("HSC 1st"); ?>",
                "<?= __("HSC 2nd"); ?>",
                "<?= __("Diploma/ Graduation"); ?>",
            ],
            datasets: [{
                label: "",
                minBarLength: 10,
                data: [
                    "<?php echo $studentsCount['classOne']; ?>",
                    "<?php echo $studentsCount['classTwo']; ?>",
                    "<?php echo $studentsCount['classThree']; ?>",
                    "<?php echo $studentsCount['classFour']; ?>",
                    "<?php echo $studentsCount['classFive']; ?>",
                    "<?php echo $studentsCount['classSix']; ?>",
                    "<?php echo $studentsCount['classSeven']; ?>",
                    "<?php echo $studentsCount['classEight']; ?>",
                    "<?php echo $studentsCount['classNine']; ?>",
                    "<?php echo $studentsCount['classTen']; ?>",
                    "<?php echo $studentsCount['classEleven']; ?>",
                    "<?php echo $studentsCount['classTwelve']; ?>",
                    "<?php echo $studentsCount['classGraduation']; ?>"
                ],
                backgroundColor: [
                    "#00c0ef",
                    "red",
                    "green",
                    "blue",
                    "orange",
                    "maroon",
                    "purple",
                    "fuchsia",
                    "navy",
                    "teal",
                    "lime",
                    "olive",
                    "aqua"
                ],
            }],

        },
        options: {
            responsive: true,
            legend: {
                display: false,
                position: "right",
            },
            scales: {
                y: {
                    beginAtZero: true,
                }
            },
        }
    });
</script>