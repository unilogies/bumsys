  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      
  </section>

  
    <section class="content-header">
      <h1>
        <?= __("Specimen Copies"); ?>
        <a href="<?php echo full_website_address(); ?>/marketing/add-specimen-copy/" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> <?= __("Add New"); ?></a>
      </h1>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
    
      <div class="row">
        <div class="col-xs-12">
          <div class="box">

          <div class="box-header">
              <h3 class="box-title"><?= __("Specimen Copy List"); ?></h3>
              <div class="printButtonPosition"></div>
            </div>
            <!-- Box header -->
            <div class="box-body">
              <table id="dataTableWithAjaxExtend" class="table table-bordered table-striped table-hover" width="100%">
                <thead>
                  <tr>
                    <th></th>
                    <th><?= __("Representative"); ?></th>
                    <th><?= __("Product"); ?></th>
                    <th><?= __("Edition"); ?></th>
                    <th><?= __("Product Category"); ?></th>
                    <th class="countTotal"><?= __("Dispatch"); ?></th>
                    <th class="countTotal"><?= __("Return"); ?></th>
                    <th class="countTotal"><?= __("Distributed"); ?></th>
                    <th class="no-sort countTotal"><?= __("In Hand"); ?></th>
                    <th class="no-sort countTotal"><?= __("Total Value"); ?></th>
                    <th><?= __("Unit"); ?></th>
                  </tr>
                </thead>
   
                <tfoot>
                  <tr>
                    <th></th>
                    <th><?= __("Representative"); ?></th>
                    <th><?= __("Product"); ?></th>
                    <th>
                      <select name="productEdition" id="productEdition" class="form-control select2">
                        <option value=""><?= __("All Editions..."); ?></option>
                        <?php 

                          $selectProductYear = easySelectA(array(
                            "table"   => "products",
                            "fields"  => "product_edition",
                            "groupby" => "product_edition"
                          ));
                          
                          if($selectProductYear) {
                            foreach($selectProductYear["data"] as $key => $value) {
                              echo "<option value='{$value['product_edition']}'>{$value['product_edition']}</option>";
                            }
                          }
                          
                        ?>
                      </select>
                    </th>
                    <th>
                      <select style="width: 180px;" name="productCategory" id="productCategory" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address(); ?>/info/?module=select2&page=productCategoryList">
                        <option value=""><?= __("Category"); ?>...</option>
                      </select>
                    </th>
                    <th><?= __("Dispatch"); ?></th>
                    <th><?= __("Return"); ?></th>
                    <th><?= __("Distributed"); ?></th>
                    <th><?= __("In Hand"); ?></th>
                    <th class="no-sort countTotal"><?= __("Total Value"); ?></th>
                    <th><?= __("Unit"); ?></th>
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
    var DataTableAjaxPostUrl = "<?php echo full_website_address(); ?>/xhr/?module=marketing&page=specimenCopyOverview";
  </script>
