<style>
    table.profitCalculation td:nth-child(even) {
        border-left: 1px solid #ababab;
        border-right: 1px solid #ababab;
    }
</style>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?= __("Income Report"); ?>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-primary">
                    <div class="box-body">

                        <form id="incomeReportGenerator" action="">
                            <div class="row">

                                <div class="col-md-3 form-group">
                                    <label for="incomeReportShop"><?= __("Select Shop"); ?></label>
                                    <select name="incomeReportShop" id="incomeReportShop" class="form-control select2" style="width: 100%;">
                                        <option value=""><?= __("All Shop"); ?>....</option>
                                        <?php 
                                            $shops = easySelectA(array(
                                                "table"     => "shops",
                                                "where"     => array(
                                                    "is_trash = 0"
                                                )
                                            ));

                                            if($shops !== false) {
                                                foreach($shops["data"] as $shop) {
                                                    $selected = (isset($_SESSION['sid']) and $_SESSION['sid'] === $shop['shop_id']) ? "selected" : "";
                                                    echo "<option {$selected} value='{$shop['shop_id']}'>{$shop['shop_name']}</option>";
                                                }
                                            }
                                            
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-3 form-group">
                                    <label for="incomeReportAccounts"><?= __("Select Accounts"); ?></label>
                                    <select name="incomeReportAccounts" id="incomeReportAccounts" class="form-control select2" style="width: 100%;" required>
                                        <option value=""><?= __("All Accounts"); ?>....</option>
                                        <?php
                                            $selectAccounts = easySelect("accounts", "accounts_id, accounts_name", array(), array("is_trash = 0 and accounts_type = 'Local (Cash)' "));
                                            foreach($selectAccounts["data"] as $accounts) {
                                                $selected = (isset($_SESSION['aid']) and $_SESSION['aid'] === $accounts['accounts_id']) ? "selected" : "";
                                                echo "<option {$selected} value='{$accounts['accounts_id']}'>{$accounts['accounts_name']}</option>";
                                            }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-3 form-group">
                                    <label for="incomeReportDate"><?= __("Select Date"); ?></label>
                                    <input placeholder="<?= __("Select Date"); ?>" name="incomeReportDate" id="incomeReportDate" class="form-control" required autocomplete="off">
                                </div>
                                <div style="margin-top: 5px;" class="col-md-2">
                                    <label for=""></label>
                                    <input type="submit" value="<?= __("Submit"); ?>" class="form-control">
                                </div>
                            </div>

                        </form>

                    </div>

                </div>

            </div>

        </div>

        <div class="row">

            <div class="col-md-7">

                <div style="display: block;" id="accountsLedger" class="box">
                    <div class="box-header">
                        <h3 class="box-title">Profit calculation</h3>
                    </div>
                    <!-- Box header -->
                    <div class="box-body">
                        
                        
                        <table class="table table-striped profitCalculation" style="width: 100%;">
                            <tbody>
                                <tr>
                                    <td><strong><?= __("Opening Stock"); ?></strong></td>
                                    <td class="text-right">0.00</td>
                                    <td><strong><?= __("Sales"); ?></strong></td>
                                    <td class="text-right">0.00</td>
                                </tr>
                                <tr>
                                    <td><strong><?= __("Purchase"); ?></strong></td>
                                    <td class="text-right">0.00</td>
                                    <td><strong><?= __("Closing Stock"); ?></strong></td>
                                    <td class="text-right">0.00</td>
                                </tr>
                                <tr>
                                    <td><strong><?= __("Salary"); ?></strong></td>
                                    <td class="text-right">0.00</td>
                                    <td><strong></strong></td>
                                    <td class="text-right">0.00</td>
                                </tr>
                                <tr>
                                    <td><strong><?= __("Expenses"); ?></strong></td>
                                    <td class="text-right">0.00</td>
                                    <td><strong></strong></td>
                                    <td class="text-right">0.00</td>
                                </tr>
                                <tr>
                                    <td><strong><?= __("Rent"); ?></strong></td>
                                    <td class="text-right">0.00</td>
                                    <td><strong></strong></td>
                                    <td class="text-right">0.00</td>
                                </tr>
                                <tr>
                                    <td><strong><?= __("Profit"); ?></strong></td>
                                    <td class="text-right">0.00</td>
                                    <td><strong></strong></td>
                                    <td class="text-right">0.00</td>
                                </tr>
        

                            </tbody>
                        </table>                        
                      
                    </div>
                    <!-- box body-->

                </div>
                <!-- box -->

            </div>

            <div class="col-md-5">

                <div style="display: block;" id="accountsLedger" class="box">
                    <div class="box-header">
                        <h3 class="box-title">Product Wise profit calculation</h3>
                    </div>
                    <!-- Box header -->
                    <div class="box-body">
                        
                        <table id="incomeReportInfo" class="table table-striped" style="width: 100%;">
                            <tbody>
                                <tr>
                                    <td><strong><?= __("Total Sale"); ?></strong></td>
                                    <td class="text-right">0.00</td>
                                </tr>
                                <tr>
                                    <td><strong><?= __("Total Sale Discount"); ?></strong></td>
                                    <td class="text-right">0.00</td>
                                </tr>
                                <tr>
                                    <td><strong><?= __("Purchase Value"); ?></strong></td>
                                    <td class="text-right">0.00</td>
                                </tr>
                                <tr>
                                    <td><strong><?= __("Purchase Value Discount"); ?></strong></td>
                                    <td class="text-right">0.00</td>
                                </tr>
                                <tr class="bg-gray">
                                    <td><strong><?= __("Gross Profit"); ?></strong></td>
                                    <td class="text-right">0.00</td>
                                </tr>
                                <tr>
                                    <td><strong><?= __("Total Expenses"); ?></strong></td>
                                    <td class="text-right">0.00</td>
                                </tr>
                                <tr class="bg-success">
                                    <td><strong><?= __("Net Profit"); ?></strong></td>
                                    <td class="text-right">0.00</td>
                                </tr>

                            </tbody>

                        </table>
                        <br/>
                        <small>This will not count either the payment was made or not. It is just depending on how much product was sold and purchased on selected date range.</small> *
                    
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

<script>
    BMS.FUNCTIONS.dateRangePickerPreDefined({selector: "#incomeReportDate"});

    $(document).on("submit", "#incomeReportGenerator", function(e) {
        
        e.preventDefault();

        var formData = new FormData(this);

        $.ajax({
            url: full_website_address + `/info/?module=data&page=getIncomeReportData`,
            type: "post",
            data: formData,
            contentType: false,
            processData: false,
            success: function (data, status) {
                
                //console.log(data);


            }
        });


    })

</script>