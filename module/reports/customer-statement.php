<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?= __("Customer Statement"); ?>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-primary">
                    <div class="box-body">

                        <form id="customerStatementReport" action="">
                            <div class="row">
                                <div class="col-md-5 form-group">
                                    <label for="customerSelection"><?= __("Select Customer"); ?></label>
                                    <select name="customerSelection" id="customerSelection" class="form-control select2Ajax" select2-minimum-input-length="1" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=customerList" style="width: 100%;" required>
                                        <option value=""><?= __("Select Customer"); ?>....</option>
                                    </select>
                                </div>
                                <div class="col-md-5 form-group">
                                    <label for="customerStatementDateRange"><?= __("Closings/ Date range"); ?></label>
                                    <input disabled type="text" name="customerStatementDateRange" id="customerStatementDateRange" class="form-control" value="" autoComplete="off" required>
                                </div>
                                <div style="margin-top: 5px;" class="col-md-2">
                                    <label for=""></label>
                                    <input type="submit" value="Submit" class="form-control">
                                </div>
                            </div>
                        </form>

                        <div class="form-group">
                            <div id="DtExportTopMessage">

                                <h2 style="font-weight: bold;" class="text-center"><?= get_options("companyName"); ?></h2>
                                <p class="text-center">38/4-K (Mannan Market), Banglabazar, Dhaka</p>
                                <h3 class="text-center"><?= __("Customer Statement"); ?></h3>
                                <p class="text-center"><strong><?= __("Time:"); ?> <?php echo date("Y-m-d H:i:s");  ?> </strong> </p>
                                <br />
                                <p><strong><?= __("Customer Name:"); ?> </strong> <span id="customerName"></span> </p>
                                <p><strong><?= __("Address:"); ?> </strong> <span id="customerAddress"></span> </p>
                                <p><strong><?= __("Date Range:"); ?> </strong> <span id="customerStatementDates"></span> </p>

                                <table id="paymentInfo" class="text-right table table-bordered" style="width: 100%;">
                                    <tbody>
                                        <tr>
                                            <td><strong><?= __("Purchased"); ?></strong></td>
                                            <td>0.00</td>
                                            <td><strong><?= __("Total Sales Paid"); ?></strong></td>
                                            <td>0.00</td>
                                            <td><strong><?= __("Opening/Previous Balance"); ?></strong></td>
                                            <td>0.00</td>
                                        </tr>
                                        <tr>
                                            <td><strong><?= __("Purchased Discount"); ?></strong></td>
                                            <td>0.00</td>
                                            <td><strong><?= __("Received Payment"); ?></strong></td>
                                            <td>0.00</td>
                                            <td><strong><?= __("Given Bonus"); ?></strong></td>
                                            <td>0.00</td>

                                        </tr>
                                        <tr>
                                            <td><strong><?= __("Total Shipping"); ?></strong></td>
                                            <td>0.00</td>
                                            <td><strong><?= __("Advance Collection"); ?></strong></td>
                                            <td>0.00</td>
                                            <td><strong><?= __("Special Discounts"); ?></strong></td>
                                            <td>0.00</td>
                                        </tr>
                                        <tr>
                                            <td><strong><?= __("Total Product Return"); ?></strong></td>
                                            <td>0.00</td>
                                            <td></td>
                                            <td></td>
                                            <td><strong><?= __("Total Payment Return"); ?></strong></td>
                                            <td>0.00</td>
                                        </tr>
                                        <tr class="bg-gray">
                                            <td><strong><?= __("Net Purchased"); ?></strong></td>
                                            <td>0.00</td>
                                            <td><strong><?= __("Total Paid"); ?></strong></td>
                                            <td>0.00</td>
                                            <td><strong><?= __("Balance/ Due"); ?></strong></td>
                                            <td>0.00</td>
                                        </tr>


                                    </tbody>

                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">

                <div id="customerReport" class="box">
                    <div class="box-header">
                        <h3 class="box-title"></h3>
                        <div class="printButtonPosition"></div>
                    </div>
                    <!-- Box header -->
                    <div class="box-body">
                        <table id="dataTableWithAjaxExtend" class="fixedDateWidthOnPrint table table-striped table-hover" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th class="no-sort"><?= __("Date"); ?></th>
                                    <th class="no-sort"><?= __("Reference"); ?></th>
                                    <th class="no-sort"><?= __("Description"); ?></th>
                                    <th class="countTotal no-sort"><?= __("Total"); ?></th>
                                    <th class="countTotal no-sort"><?= __("Discount"); ?></th>
                                    <th class="countTotal no-sort"><?= __("Shipping"); ?></th>
                                    <th class="countTotal no-sort"><?= __("Debit"); ?></th>
                                    <th class="countTotal no-sort"><?= __("Credit"); ?></th>
                                    <th class="hideit text-right"><?= __("Balance"); ?></th>
                                </tr>
                            </thead>

                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th class="text-right"><?= __("Total:"); ?> </th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>

                    </div>
                    <!-- box body-->
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

<script>
    /* Column Defference target column of data table */
    var DataTableAjaxPostUrl = "<?php echo full_website_address(); ?>/xhr/?module=reports&page=customerStatement";
    var defaultiDisplayLength = -1;

    $(document).on("change", "#customerSelection", function(e){
        
        BMS.fn.get( "getCustomerClosingsDate&cid="+ $(this).val() , function(data) {

            let keys = Object.keys(data);

            var dateRangeObject = {};

            // Javascript loop from last
            for(var x=keys.length-1; x>=0; x--) {
                dateRangeObject[' ' + keys[x]] = data[keys[x]]
            }
            
            BMS.FUNCTIONS.dateRangePickerPreDefined({
                selector: "#customerStatementDateRange", 
                ranges: Object.assign({
                            'This Month': [moment().startOf('month'), moment().endOf('month')],
                            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                            'This Year'  : [moment().startOf('year'), moment().endOf('year')],
                        }, dateRangeObject)
            });

        });

        if( $(this).val() !== "" ) {
            $("#customerStatementDateRange").prop("disabled", false);
        } else {
            $("#customerStatementDateRange").prop("disabled", true);
        }
        
    });


    
</script>