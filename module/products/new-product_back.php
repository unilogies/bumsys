<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?= __("Products"); ?>
        <small><?= __("New Product"); ?></small>
      </h1>
    </section>
    
    <style>
        .btn-product {
            border: 1px solid #eee;
            cursor: pointer;
            height: 115px;
            width: 11.8%;
            margin: 0 0 3px 2px;
            padding: 2px;
            min-width: 98px;
            overflow: hidden;
            display: inline-block;
            font-size: 13px;
            background-color: white;
        }

        .btn-product span {
            display: table-cell;
            height: 45px;
            line-height: 15px;
            vertical-align: middle;
            text-transform: uppercase;
            width: 11.5%;
            min-width: 94px;
            overflow: hidden;
        }

        #productListContainer {
            max-height: 680px;
            overflow: auto;
        }

        .tableBodyScroll tbody td {
            padding: 6px 4px !important;
            vertical-align: middle !important;
        }

        .tableBodyScroll tbody {
            display: block;
            overflow: auto;
            height: 20vh;
        }

        .tableBodyScroll thead,
        .tableBodyScroll tbody tr {
            display: table;
            width: 100%;
            table-layout: fixed;
        }

        .tableBodyScroll thead {
            width: calc(100% - 3px);
        }

        .tableBodyScroll tbody::-webkit-scrollbar {
            width: 4px;
        }

        .tableBodyScroll tbody::-webkit-scrollbar-track {
            -webkit-box-shadow: inset 0 0 6px #337ab7;
            border-radius: 10px;
        }

        .tableBodyScroll tbody::-webkit-scrollbar-thumb {
            border-radius: 10px;
            -webkit-box-shadow: inset 0 0 6px #337ab7;
        }
    </style>
    

    <!-- Main content -->
    <section class="content container-fluid">

      <!-- Form start -->
      <form method="post" role="form" id="jqFormAdd" class="newProductAdd" action="<?php echo full_website_address(); ?>/xhr/?module=products&page=newProduct" enctype="multipart/form-data">

        <div class="box box-default">
        
          <div class="box-header with-border">
            <h3 class="box-title"><?= __("New Product"); ?></h3>
          </div> <!-- box box-default -->

          <div class="box-body">

            <div class="row">

                <div class="col-md-6">
                    <div class="form-group required">
                        <label for="productName"><?= __("Product Name:"); ?></label>
                        <input type="text" name="productName" id="productName" class="form-control" required>
                    </div>

                    <div class="row">

                        <div class="form-group col-md-6 required">
                            <label for="productType"><?= __("Product Type:"); ?></label>
                            <select name="productType" id="productType" class="form-control" required>
                                <option value="Normal">Normal</option>
                                <option value="Bundle">Bundle</option>
                                <option value="Grouped">Grouped</option>
                                <option value="Variable">Variable</option>
                            </select>
                        </div>
                        <div class="form-group required col-md-6">
                            <label for="productCode"><?= __("Product Code:"); ?></label>
                            <input type="text" name="productCode" id="productCode" value="<?php echo rand(0,9).date("mdyHi"); ?>" onclick="select()" class="form-control" required>
                        </div>

                    </div>

                    <div class="form-group required">
                        <label for="productCategoryForAdd"><?= __("Product Category:"); ?></label>
                        <select name="productCategoryForAdd" id="productCategoryForAdd" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address(); ?>/info/?module=select2&page=productCategoryList" required>
                            <option value=""><?= __("Select Category"); ?>....</option>
                        </select>
                    </div>

                    <?php if( get_options("productSettingsCanAddBrands") ): ?>
                    <div class="form-group">
                        <label for="productBrandSelect"><?= __("Brand/Publisher:"); ?></label>
                        <select name="productBrandSelect" id="productBrandSelect" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address(); ?>/info/?module=select2&page=productBrandList" required>
                            <option value=""><?= __("Select Brand/Publisher"); ?>....</option>
                        </select>
                    </div>
                    <?php endif; ?>

                    <?php if( get_options("productSettingsCanAddGeneric") ): ?>
                    <div class="form-group">
                        <label for="productGenericSelect"><?= __("Generic:"); ?></label>
                        <select name="productGenericSelect" id="productGenericSelect" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address(); ?>/info/?module=select2&page=productGenericList" required>
                            <option value=""><?= __("Select Generic"); ?>....</option>
                        </select>
                    </div>
                    <?php endif; ?>

                    <div class="row">
                        <?php if( get_options("productSettingsCanAddBookInfo") ): ?>
                        <div class="form-group col-md-12 bookAuthor">
                            <label for="bookAuthor"><?= __("Author:"); ?></label>
                            <select style="height: 32px;" multiple name="bookAuthor[]" id="bookAuthor" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address(); ?>/info/?module=select2&page=authorList">
                                <option value=""><?= __("Select Author"); ?>....</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="productTotalPages"><?= __("Total Page:"); ?></label>
                            <input type="text" name="productTotalPages" placeholder="Book's Total Pages" id="productTotalPages" class="form-control">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="productISBN"><?= __("ISBN:"); ?></label>
                            <input type="text" name="productISBN" placeholder="Book's ISBN" id="productTotalPages" class="form-control">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="productPublishedDate"><?= __("Published Date:"); ?></label>
                            <input type="text" name="productPublishedDate" placeholder="Published Date" id="productPublishedDate" class="form-control datePicker">
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="productDescription"><?= __("Product Description:"); ?></label>
                        <textarea name="productDescription" id="productDescription" rows="3" class="form-control"></textarea>
                    </div>

                </div>

                <div class="col-md-6">
                    <div class="form-group col-md-6">
                        <label for="productEdition"><?= __("Edition:"); ?></label>
                        <input type="text" name="productEdition" placeholder="Enter edition" id="productEdition" class="form-control">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="productDiscount"><?= __("Discount:"); ?></label>
                        <i data-toggle="tooltip" data-placement="bottom" title="<?= __("Percentage or Fixed amount. Eg: %d or %s. If your sales price is %d and discount is %s then the product actual sale price will be %d.", 120, "5%", 100, "10%", 90); ?>" class="fa fa-question-circle"></i>
                        <input type="text" name="productDiscount" id="productDiscount" class="form-control">
                    </div>

                    <div class="form-group col-md-6">
                        <label for="productWeight"><?= __("Weight:"); ?></label>
                        <input type="text" name="productWeight" id="productWeight" class="form-control">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="productWidth"><?= __("Width:"); ?></label>
                        <input type="text" name="productWidth" id="productWidth" class="form-control">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="productHeight"><?= __("Height:"); ?></label>
                        <input type="text" name="productHeight" id="productHeight" class="form-control">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="alertQuantity"><?= __("Alert Quantity"); ?></label>
                        <input type="text" name="alertQuantity" id="alertQuantity" class="form-control">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="packetQuantity"><?= __("Packet Quantity"); ?></label>
                        <i data-toggle="tooltip" data-placement="right" title="Quantity for per packet" class="fa fa-question-circle"></i>
                        <input type="text" name="packetQuantity" id="packetQuantity" class="form-control">
                    </div>

                    <div class="form-group col-md-6">
                        <label for=""><?= __("Product Photo:"); ?></label>
                        <div class="input-group">
                            <span class="input-group-btn">
                                <span class="btn btn-default btn-file">
                                    <?= __("Select photo"); ?> <input type="file" name="productPhoto" id="imgInp">
                                </span>
                            </span>
                            <input type="text" class="form-control" id="imageNameShow" readonly>
                        </div>

                        <div style="margin-top: 8px;" id="message"></div>
                
                    </div>

                    <div style="margin-bottom: 5px;" class="form-group col-md-6">
                        <div style="height: 120px; text-align: center;" id="image_preview">
                        <img style="margin: auto;" id="previewing" width="100%" height="auto" src="" />
                        </div>
                    </div>

                </div>

            </div>
          
          </div> <!-- ./box-body -->
        
        </div> <!--  Box End -->

        <div style="display: none;" class="box box-default bundleProductsContainer">
          <div class="box-header">
            <h3 class="box-title"><?= __("Bundle Products"); ?></h3>
          </div>
          <div class="box-body">

            <div class="row">

              <div style="margin-top: 2px;" class="col-md-4">

                  <div class="form-group">
                      <label for=""><?= __("Search Products"); ?></label>
                      <div class="input-group">
                          <select name="selectProduct" id="selectProduct" class="form-control pull-left select2Ajax" select2-minimum-input-length="1" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=productList" style="width: 100%;">
                              <option value=""><?= __("Select Product"); ?>....</option>
                          </select>

                          <div style="cursor: pointer;" class="input-group-addon btn-primary btn-hover" id="addProductButton">
                              <i class="fa fa-plus-circle "></i>
                          </div>

                      </div>
                  </div>
                  <p class="text-center">-- <?= __("OR"); ?> --</p>
                  <div class="text-center">
                      <button data-toggle="modal" data-target="#browseProduct" type="button" class="btn btn-info"><i class="fa fa-folder-open"></i> <?= __("browse Product"); ?></button>
                  </div>
                  <br />

                  <!-- Browse Product Modal -->
                  <div class="modal fade" id="browseProduct">
                      <div class="modal-dialog modal-mdm">
                          <div class="modal-content">
                              <div class="modal-header">
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                      <span aria-hidden="true">&times;</span></button>
                                  <h4 class="modal-title"><?= __("Browse Product"); ?> <small><?= __("Click on the product to add"); ?></small> </h4>
                              </div>
                              <div class="modal-body">

                                  <!-- Product Filter -->
                                  <div class="row">

                                      <?php load_product_filters(); ?>

                                  </div>
                                  <!-- /Product Filter -->

                                  <div class="row box-body" id="productListContainer">
                                      <!-- Here the products will be shown -->
                                  </div>

                              </div>

                              <div class="modal-footer">
                                  <button type="button" class="btn btn-default" data-dismiss="modal"><?= __("Close"); ?></button>
                              </div>
                          </div>
                          <!-- /. Browse Product modal-content -->
                      </div>
                      <!-- /. Browse Product modal-dialog -->
                  </div>
                  <!-- /.Browse Product modal -->
              </div>

              <div class="col-sm-8">
                  <table id="productTable" class="tableBodyScroll table table-bordered table-striped table-hover">
                      <thead>
                          <tr class="bg-primary">
                              <th class="col-md-5 text-center"><?= __("Product Name"); ?></th>
                              <th class="col-md-3 text-center"><?= __("Quantity"); ?></th>
                              <th class="col-md-2 text-center"><?= __("Unit"); ?></th>
                              <th class="col-md-2 text-center gbProductPrice"><?= __("Price"); ?></th>
                              <th style="width: 28px !important;">
                                  <i class="fa fa-trash-o" style="opacity:0.5; filter:alpha(opacity=50);"></i>
                              </th>
                          </tr>
                      </thead>

                      <tbody>
                      </tbody>

                      <tfoot>
                      </tfoot>
                  </table>
              </div>

            </div> <!-- row -->
            
          </div> <!-- ./box-body -->

        </div> <!--  Box End -->


        <div class="box box-default">
          <div class="box-header">
            <h3 class="box-title"><?= __("Product Unit Variant"); ?> <small class="alert-danger" style="display: none;" id='gbUnitNote'><?= __("In Bundle or Grouped product type, these unit prices will not be used and you can not ad more then one unit"); ?></small> </h3>
          </div>
          <div class="box-body">
            
            <div class="unitVariantItem">
              <div class="row">
                <div class="form-group col-md-5">
                  <label for="productUnit"><?= __("Unit:"); ?></label>
                  <div class="input-group">
                    <select name="productUnit[]" id="productUnit" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address(); ?>/info/?module=select2&page=itemUnitListByName" required>
                      <option value=""><?= __("Select Unit"); ?>....</option>
                    </select>
                    <span data-toggle="tooltip" data-placement="top" title="<?= __("Use this unit as default."); ?>" class="input-group-addon">
                      <input class="defaultUnit" type="radio" name="defaultUnit" value="0">
                    </span>
                  </div>
                </div>
                <div class="form-group col-md-3">
                  <label for="productPurchasePrice"><?= __("Purchase Price Or Costing:"); ?></label>
                  <i data-toggle="tooltip" data-placement="right" title="Product purchase price or costing" class="fa fa-question-circle"></i>
                  <input type="number" name="productPurchasePrice[]" id="productPurchasePrice" step="any" value="0" class="form-control" required autocomplete="off" onclick="this.select();">
                </div>
                <div class="form-group col-md-3">
                  <label for="productSalePrice"><?= __("Sales Price:"); ?></label>
                  <input type="number" name="productSalePrice[]" id="productSalePrice" value="0" step="any" class="form-control" required autocomplete="off" onclick="this.select();">
                </div>
              </div> <!-- row -->
            </div> <!-- unitVariantItem --> 

            <div style="text-align: center;">
              <button type="button" id="newUnitVariant" class="btn btn-warning"><i class="fa fa-plus-circle"></i> <?= __("Another Unit"); ?></button>
            </div>
            
          </div> <!-- ./box-body -->

          <div class="box-footer">
              <button type="submit" id="jqAjaxButton" class="btn btn-primary"><?= __("Add Product"); ?></button>
          </div>

        </div>




      </form> <!-- Form End --> 

    </section> <!-- Main content End tag content container-fluid -->
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

<script>

  var addProductPageUrl = window.location.href;

  /* Browse Product */
  $("#browseProduct").on("show.bs.modal", function(e) {

      BMS.PRODUCT.showProduct();

  });

  $(document).ready(function (e) {
  /* Function to preview image */
    $(function() {
      $("#imgInp").change(function() {

        /* Show the filename in choose option */
        var imageNameShow = $("#imgInp").val().replace(/\\/g, '/').replace(/.*\//, '');
        $("#imageNameShow").val(imageNameShow);

        $("#message").empty();         /* To remove the previous error message */
        var file = this.files[0];
        var imagefile = file.type;
        var imagesize = file.size;
        var match= ["image/jpeg","image/png","image/jpg"];	
        if(!((imagefile==match[0]) || (imagefile==match[1]) || (imagefile==match[2])))
        {
        $("#message").html("<div style='margin-bottom: 0;' class='alert alert-danger'><?= __("Please Select A valid Image File"); ?></div>");
        return false;
        }
        else if (imagesize > 300000)
        {
        $("#message").html("<div class='alert alert-danger'><?= __("Max image size %d kb", 300); ?></div>");
        return false;
        }
        else 
        {
          var reader = new FileReader();	
          reader.onload = imageIsLoaded;
          reader.readAsDataURL(this.files[0]);
        }		
      });
    });
    function imageIsLoaded(e) { 
      $("#imgInp").css("color","green");
      $('#image_preview').css("display", "block");
      $('#previewing').attr('src', e.target.result);
      $('#previewing').attr('width', 'auto');
      $('#previewing').attr('height', '100%');
    };
  });

  $("#newUnitVariant").click( function(){

    if( $("#productType").val() === "Bundle" || $("#productType").val() === "Grouped" ) {
      alert("<?= __("You can not add more then one unit in Bundle or Grouped product type."); ?>");
      return;
    }

    var defaultUnitNo = $(".defaultUnit").length;
    var unitVariant = ' <div class="row unitItem"> \
                <div class="form-group col-md-5"> \
                  <div class="input-group"> \
                    <select name="productUnit[]" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address(); ?>/info/?module=select2&page=itemUnitListByName"> \
                      <option value=""><?= __("Select Unit"); ?>....</option> \
                    </select> \
                    <span data-toggle="tooltip" data-placement="top" title="<?= __("Use this unit as default."); ?>" class="input-group-addon"> \
                      <input class="defaultUnit" type="radio" name="defaultUnit" value="'+ defaultUnitNo +'"> \
                    </span> \
                  </div> \
                </div> \
                <div class="form-group col-md-3"> \
                  <input type="number" name="productPurchasePrice[]" value="0" step="any" class="form-control" autocomplete="off" onclick="this.select();"> \
                </div> \
                <div class="form-group col-md-3"> \
                  <input type="number" name="productSalePrice[]" value="0" step="any" class="form-control" autocomplete="off" onclick="this.select();"> \
                </div> \
                <div style="cursor: pointer;" class="form-group row col-md-1"> \
                  <i data-toggle="tooltip" data-placement="top" title="Remove this Item" style="cursor: pointer; margin-top: 9px;" class="fa fa-trash-o removeThisUnit"></i> \
                </div> \
              </div> \
              ';

        $(".unitVariantItem").append(unitVariant);
  });

  /* Remove product unit */
  $(document).on("click", ".removeThisUnit", function() {

    $(this).closest(".unitItem").css("background-color", "whitesmoke").hide("fast", function() {
      $(this).closest(".unitItem").remove();

    });
    
  });

  $(document).on("change", "#productType", function() {

    if( $(this).val() === "Bundle" || $(this).val() === "Grouped" ) {

      var boxTitle = "Bundle Product";
      if( $(this).val() === "Grouped") {

        // Hide price on grouped product
        $(".gbProductPrice").hide();

        boxTitle = "Grouped Product";

      } else {
        
        $(".gbProductPrice").show();

      }

      // show the gb note
      $("#gbUnitNote").show();

      // disable new unit varient button
      $('#newUnitVariant').prop("disabled", true);

      // Remove unit item varient if there is more then one unit varient
      $(".unitItem").remove();

      $(".bundleProductsContainer").show();
      $(".bundleProductsContainer .box-title").html(boxTitle);

    
    } else {

      $(".bundleProductsContainer").hide();

      // hide the gb note
      $("#gbUnitNote").hide("disabled", false);

      // Enable new unit varient button
      $('#newUnitVariant').prop();

    } 

  });


</script>

