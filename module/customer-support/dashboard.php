	<!-- Content Wrapper. Contains page content -->
	<div class="content-wrapper">
		<!-- Content Header (Page header) -->
		<section class="content-header">
			<h1>
				<?=__("Customer Support Dashboard");?>
				<small></small>
			</h1>
		</section>

		<!-- Main content -->
		<section class="content">
		
			<!-- Chart -->
			<div class="row">
				<div class="col-lg-12">
					<div class="box box-primary">
						<div class="box-header with-border">
							<h3 class="box-title"><?php echo __("Call History Overview"); ?></h3>
						</div>
						<div class="box-body">
							<div class="chart">
								<canvas id="overviewChart" style="height: 380px"></canvas>
							</div>
						</div>
						
					</div>
				</div>
			</div>

			<div class="row">
				<!-- Top Customer of this product -->
				<div class="col-sm-12 col-lg-12">
                    <div class="box">
                        <div class="box-header">
                            <h3 class="box-title"><?= __("Agent Wise Statistics"); ?></h3>
                        </div>
                        <!-- Box header -->
                        <div class="box-body">
                            <table dt-height="30vh" dt-data-url="<?php echo full_website_address(); ?>/xhr/?module=customer-support&page=agentWiseCallStatistics" class="dataTableWithAjaxExtend addToSMSBox table table-bordered table-striped table-hover" width="100%">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th><?php echo __("Agent Name"); ?></th>
                                        <th><?php echo __("Talk Time"); ?></th>
                                        <th><?php echo __("ATT"); ?></th>
                                        <th><?php echo __("Answered"); ?></th>
                                        <th><?php echo __("Not Answred"); ?></th>
                                        <th><?php echo __("Busy"); ?></th>
                                        <th><?php echo __("Missed"); ?></th>
                                        <th><?php echo __("Unreachable"); ?></th>
                                        <th><?php echo __("Total"); ?></th>
                                    </tr>
                                </thead>
                
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th class="no-print">
                                            <input style="width: 160px;" type="text" placeholder="<?= __("Select Date"); ?>" name="callDateRange" id="callDateRange" class="form-control" autocomplete="off">
                                        </th>
                                        <th><?php echo __("Talk Time"); ?></th>
                                        <th><?php echo __("ATT"); ?></th>
                                        <th><?php echo __("Answered"); ?></th>
                                        <th><?php echo __("Not Answred"); ?></th>
                                        <th><?php echo __("Busy"); ?></th>
                                        <th><?php echo __("Missed"); ?></th>
                                        <th><?php echo __("Unreachable"); ?></th>
                                        <th><?php echo __("Total"); ?></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <!-- box body-->
                    </div>
                    <!-- box -->
				</div> <!-- /.Col -->

        

				

			</div> <!-- /.Row -->


		</section> <!-- Main content End tag -->
		<!-- /.content -->
	</div>
	<!-- /.content-wrapper -->

	<?php 

		$callData = easySelectD("
            SELECT db_date, call_status, if(totalCall is null, 0, totalCall) as totalCall FROM `time_dimension`
            left join ( SELECT
                    date(call_datetime) as call_date,
                    count(*) as totalCall,
                    call_status
                from {$table_prefix}calls
                group by date(call_datetime), call_status
            ) as calls on call_date = db_date
            where db_date BETWEEN DATE_SUB(CURRENT_DATE(), INTERVAL 30 DAY) and CURRENT_DATE()
		");
        

        $dates = array();
        $unreachableCall = array();
        $busyCall = array();
        $notAnsweredCall = array();
        $answeredCall = array();


        if($callData !== false) {

            foreach($callData["data"] as $key => $call){

                if( !in_array($call["db_date"], $dates) ) {
                    array_push($dates, $call["db_date"]);
                }

                // If call status empty then set zero in all call type
                if( empty($call["call_status"]) ) {
                    array_push($unreachableCall, 0);
                    array_push($busyCall, 0);
                    array_push($notAnsweredCall, 0);
                    array_push($answeredCall, 0);
                }

                if( $call["call_status"] === "Unreachable" ) {

                    array_push($unreachableCall, $call["totalCall"]);

                } else if( $call["call_status"] === "Busy" ) {

                    array_push($busyCall, $call["totalCall"]);

                } else if( $call["call_status"] === "Not Answered" ) {

                    array_push($notAnsweredCall, $call["totalCall"]);

                } else if( $call["call_status"] === "Answered" ) {

                    array_push($answeredCall, $call["totalCall"]);
                    
                }
                
            }

        }

		$callDates = __(json_encode($dates));
		$unreachableCall = json_encode($unreachableCall);
        $busyCall = json_encode($busyCall);
        $notAnsweredCall = json_encode($notAnsweredCall);
        $answeredCall = json_encode($answeredCall);

	?>

	<script src="<?php echo full_website_address(); ?>/assets/3rd-party/chart.js/Chart.min.js"></script>

<script>

var ctx = document.getElementById('overviewChart');
var myLineChart = new Chart(ctx, {
	type: 'line',
	data: {
		labels: <?php echo $callDates; ?>,
		datasets: [
			{
				label: "<?= __("Answered"); ?>",
				borderColor: "green",
				borderWidth: 2,
				data: <?php echo $answeredCall; ?>
			},
            {
				label: "<?= __("Not Answered"); ?>",
				borderColor: "red",
				borderWidth: 2,
				data: <?php echo $notAnsweredCall; ?>
			},
            {
				label: "<?= __("Busy"); ?>",
				borderColor: "blue",
				borderWidth: 2,
				data: <?php echo $busyCall; ?>
			},
            {
				label: "<?= __("Unreachable"); ?>",
				borderColor: "gray",
				borderWidth: 2,
				data: <?php echo $unreachableCall; ?>
			}
		]
	},
	options: {
		responsive: true,
		tooltips: {
			mode: 'index',
			intersect: false
		}
	}
});

BMS.FUNCTIONS.dateRangePickerPreDefined({selector: "#callDateRange"});

</script>
