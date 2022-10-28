<?php

if(is_login() !== true) {
  $rdr_to = full_website_address()."/login/";
  header("location: {$rdr_to}");
  exit();
}

if( !isset($_SESSION["sid"]) and !isset($_SESSION["aid"]) ) {
  require ERROR_PAGE . "500.php";
  exit();
}

require DIR_THEME . "header.php"; 

?>
  <script>
    // Collapse the sidebar on POS screen
    $("body").addClass("sidebar-collapse");
    
  </script>

  <style>
    .btn-product {
      border: 1px solid #eee;
      cursor: pointer;
      height: 115px;
      width: 11.8%;
      margin: 0 0 3px 2px;
      padding: 2px;
      min-width: 100px;
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

    table {
      margin-bottom: 0px !important;
    }

    table th {
      padding: 8px 4px !important;
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
    #posCalculationTable td {
      padding: 5px 10px;
    }

    .productDescription {
      cursor: pointer;
      padding: 0 5px;
      margin-right: 5px;
      display: inline-block;
    }

    .tableBodyScroll tbody {
        display: block;
        overflow: auto;
        height: 54vh;
    }
    .tableBodyScroll thead, .tableBodyScroll tbody tr {
        display: table;
        width: 100%;
        table-layout: fixed;
    }

    .tableBodyScroll thead {
        width: calc( 100% - 3px );
    }

  </style>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    
  <section style="display: none" class="content-header">
      <h1>
        Return
      </h1>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">

      <div class="row">

        <!-- Left Column -->
        <div class="col-md-6">
          <form action="#">
            <div class="box box-primary">

              <div class="box-body">
              
                <div title="Select Customer" data-toggle="tooltip" class="form-group">
                  <select name="customers" id="customers" class="form-control select2Ajax" select2-minimum-input-length="1" select2-ajax-url="<?php echo full_website_address(); ?>/info/?module=select2&page=customerList">
                    <option value="1">Walk-in Customer</option>
                  </select>
                  <input type="hidden" name="customersId" id="customersId" value="1">
                </div>
                <div title="Search Product" data-toggle="tooltip" class="form-group">
                  <div class="input-group">

                    <select name="selectProduct" id="selectProduct" class="form-control pull-left select2Ajax" select2-minimum-input-length="1" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=productList" style="width: 100%;">
                      <option value="">Search Product....</option>
                    </select>

                    <div style="cursor: pointer;" class="input-group-addon btn-primary btn-hover" id ="addProductButton">
                      <i class="fa fa-plus-circle "></i>
                    </div>
                  
                  </div>
                </div>
              </div>
                
              <!--  Product List-->
              <div class="product-list">

                <table id="productTable" class="tableBodyScroll table table-bordered table-striped table-hover">
                  <thead>
                    <tr class="bg-primary">
                      <th class="col-md-4 text-center">Product</th>
                      <th class="text-center">Price</th>
                      <th class="text-center" >Bought Qnt</th>
                      <th class="text-center" >Returned Qnt</th>
                      <th class="text-center">Return Qnt</th>
                      <th class="text-center">Subtotal</th>
                      <th class="text-center" style="width: 25px !important;">
                        <i class="fa fa-trash-o" style="opacity:0.5; filter:alpha(opacity=50);"></i>
                      </th>
                    </tr>
                  </thead>
                  
                  <tbody>
                    
                  </tbody>

                </table>

              </div>

              <div class="box-body">

                <table style="width: 100%; background: #ecf0f5;" id ="posCalculationTable">
                  <tbody>
                    <tr>
                      <td class="col-md-3">Items</td>
                      <td class="text-right col-md-3 totalItemAndQnt">0(0)</td>
                      <td class="col-md-3">Total</td>
                      <td class="text-right col-md-3 totalAmount">0.00</td>
                    </tr>
                    <tr>
                      <td></td>
                      <td></td>
                      <td>Discount <a data-toggle="modal" data-target="#orderDiscount" href="#"> <i class="fa fa-edit"></i> </a> </td>
                      <td class="text-right totalOrderDiscountAmount">(-) 0.00</td>
                    </tr>
                    <tr style="font-weight: bold; background: #333; color: #fff;">
                      <td></td>
                      <td class="text-right"></td>
                      <td>Net Total</td>
                      <td class="text-right netTotalAmount">0.00</td>
                    </tr>

                  </tbody>
                </table>
                <div class="post-action">
                  
                  <div style="float: left;" class="btn-group">
                    <a href="<?php echo full_website_address(); ?>/return/" class="btn btn-danger btn-block btn-flat">Cancel</a>
                  </div>
                  
                  <div style="float: right;" class="btn-group">
                    <button data-toggle="modal" data-target="#productReturn" type="button" class="btn btn-success btn-block btn-flat"><i class="fa fa-undo"></i> Return</button>
                  </div>
                  
                </div>
              </div>
            </div>

            <div class="modal fade" id="productReturnDetails">
              <div class="modal-dialog ">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Product Description</h4> <!-- Product name will display here -->
                  </div>
                  <div class="modal-body">

                      <div class="form-group">
                        <label for="productReturnItemQnt">Quantity</label>
                        <input type="text" id="productReturnItemQnt" class="form-control" value="" onclick="select()">  
                      </div>
                      <div class="form-group">
                        <label for="productReturnItemDiscount">Discount</label>
                        <input type="text" id="productReturnItemDiscount" class="form-control" value="" nclick="select()"> 
                      </div>
                      <div class="form-group">
                        <label for="productReturnItemDetails">Details</label>
                        <textarea id="productReturnItemDetails" rows="3" class="form-control" placeholder="Eg: Product EMI or serial"></textarea>
                      </div>
                      <input type="hidden" class="product_id" value="">
                    
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Update</button>
                  </div>
                </div>
                <!-- /.modal-content -->
              </div>
              <!-- /.modal-dialog -->
            </div>
            <!-- /.modal -->

            <div class="modal fade" id="orderDiscount">
              <div class="modal-dialog ">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Order Discount</h4>
                  </div>
                  <div class="modal-body">
                    <div class="form-group">
                      <label for="orderDiscountValue">Order Discount</label>
                      <input type="text" name="orderDiscountValue" id="orderDiscountValue" class="form-control">
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Update</button>
                  </div>
                </div>
                <!-- /.modal-content -->
              </div>
              <!-- /.modal-dialog -->
            </div>
            <!-- /.modal -->

            <div class="modal fade" id="productReturn">
              <div class="modal-dialog ">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Finalize Return</h4>
                  </div>
                  <div class="modal-body">

                    <div class="form-group required">
                      <label for="returnDate">Return Date</label>
                      <input type="text" name="returnDate" id="returnDate" value="<?php echo date("Y-m-d"); ?>" class="form-control" required>
                    </div>
                    <div class="form-group required">
                      <label for="returnWarehouse">Return Warehouse</label>
                      <select name="warehouse" id="warehouse" class="form-control select2 required" style="width: 100%;">
                        <?php
                          $selectWarehouse = easySelect("warehouses");
                          foreach($selectWarehouse["data"] as $warehouse) {
                            echo "<option value='{$warehouse['warehouse_id']}'>{$warehouse['warehouse_name']}</option>";
                          }
                        ?>
                      </select>
                    </div>
                    <div class="form-group">
                      <label for="returnSurcharge">Surcharge</label>
                      <input type="number" name="returnSurcharge" id="returnSurcharge" class="form-control">
                    </div>

                    <div class="form-group">
                      <label for="returnNote">Return note:</label>
                      <textarea name="returnNote" id="returnNote" rows="4" class="form-control"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="ReturnMoney">
                          <input type="checkbox" name="ReturnMoney" id="ReturnMoney" value="true" class=""> Return money
                        </label>
                    </div>

                    <div class="returnAmountAccountsDiv" style="display: none;" class="form-group">
                      <label for="returnAmountAccounts">Accounts</label>
                      <select name="returnAmountAccounts" id="returnAmountAccounts" class="form-control select2" style="width: 100%;">
                        <?php
                          $selectAccounts = easySelect("accounts", "accounts_id, accounts_name", array(), array("is_trash" => 0));
                          foreach($selectAccounts["data"] as $accounts) {
                            echo "<option value=''>Select accounts</option>";
                            echo "<option value='{$accounts['accounts_id']}'>{$accounts['accounts_name']}</option>";
                          }
                        ?>
                      </select>
                      <small>Which account the money will return from.</small>
                    </div>

                  </div>

                  <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    <button id="returnSubmit" type="submit" class="btn btn-primary">Submit</button>
                  </div>
                </div>
                <!-- /.modal-content -->
              </div>
              <!-- /.modal-dialog -->
            </div>
            <!-- /.modal -->
          </form>
        </div>
        <!-- /Left Column -->

        <!-- Right Column -->

        <div class="col-md-6">

          <!-- Product Filter -->
          <div class="row">

            <div class="col-md-4">
              <div class="form-group">
                <select name="productCategory" id="productCategory" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address(); ?>/info/?module=select2&page=productCategoryList">
                  <option value="">All Category</option>
                </select>
              </div>
            </div>

            <div class="col-md-4">
              <div class="form-group">
                <select name="productYear" id="productYear" class="form-control select2">
                  <option value="2020">2020</option>
                  <option value="2019">2019</option>
                  <option value="2018">2018</option>
                </select>
              </div>
            </div>

          </div>
          <!-- /Product Filter -->
          

          <div style="height: 85vh; overflow: auto;" class="box box-success">

            <div id="productListContainer" class="box-body">
              <!-- Here the products will be showen -->
                                                                                                                      
            </div>

          </div>

        </div>
        <!-- /Right Column -->

      </div>

    </section> <!-- Main content End tag -->
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

<script>

  //Date picker
  $('#returnDate').datepicker({
      format: 'yyyy-mm-dd',
      autoclose: true
  });

  // Load Product
  $( function() {
    $.ajax({
      url: "<?php echo full_website_address(); ?>/info/?module=data&page=productList",
      success: function (data, status) {
        // Now all the products display in the products container
        $("#productListContainer").html(data);

      }
    });
  });

  // Load Poduct by filtered
  $(document).on("change", "#productCategory, #productYear", function() {
    var productCategory = $("#productCategory").val();
    var productYear = $("#productYear").val();

    $.ajax({
      url: "<?php echo full_website_address(); ?>/info/?module=data&page=productList&product_category_id="+productCategory+"&product_year="+productYear,
      success: function (data, status) {

        // Now all the products display in the products container
        $("#productListContainer").html(data);

      }
    });
  });

  // Set the customer id to a hidden input filed
  // On change
  $(document).on("change", "#customers", function() {
    var customersId = $("#customers").val();

    $("#customersId").val(customersId);

  });


  // Get the Product Details and add into the list
  $(document).on("click", "#addProductButton, .productButton", function() {
    
    // If product select from product list box then remove/ empty the product selection input.
    if($(this).val() > 0) {
      $("#selectProduct").html("<option value=''>Search Product....</option>");
    }

    var numOfProduct = $("#productTable > tbody > tr").length;
    var productId = ($('#selectProduct').val() > 0) ? $('#selectProduct').val() : $(this).val();
    var productQnt = $(".productID[value='"+productId+"']").closest("tr").find(".productQnt").val();
    var pqnt = (productQnt === undefined) ? 1 : Number(productQnt) + 1; // pqnt = product qunantity 
   
    
    if(productId === "") {
      alert("Please select a product");
      return;
    }
    
    
    $.ajax({
      url: "<?php echo full_website_address(); ?>/info/?module=data&page=productDetailsForReturn&product_id="+productId+"&customer_id="+ $("#customersId").val(),
      success: function (data, status) {
        if(status == "success") {
 
          var product = JSON.parse(data);

          // Check if the product already in the list
          var ProductIsExists = false;
          $(".productID").each( function() {
            
            if($(this).val() === productId) {

              ProductIsExists = true; 
              var quantitySelector = $(this).closest("tr").find(".productQnt");
              var totalQuantity = Number(quantitySelector.val()) + 1;

              // If the product already in the list then increase the quantity of that product
              quantitySelector.val(totalQuantity);

            }

            var netSalesPrice = $(this).closest("tr").find("td:nth-child(3)").html();
            var Quantity = $(this).closest("tr").find(".productQnt").val();
            var Discount = $(this).closest("tr").find(".productDiscount").val();
            var SubtotalRow = $(this).closest("tr").find("td:nth-child(8)");

            var netSalesPrice = $(this).closest("tr").find(".netSalesPrice").val();
            var Quantity = $(this).closest("tr").find(".productQnt").val();
            var Discount = $(this).closest("tr").find(".productDiscount").val();
            var SubtotalRow = $(this).closest("tr").find("td.subtotalCol");

            // Display Subtotal for each product
            $(SubtotalRow).html((calculateDiscount(netSalesPrice,Discount) * Quantity).toFixed(2));

            // Select Quantity input field
            $("tr#pid"+productId).find(".productQnt").select();
            
          });

          if(ProductIsExists === true) {
            // Count the Total Quantity
            grandTotal();
            return;
          }

          var productDiscount = (product.product_discount === null) ? "0%" : product.product_discount;

          var html = '<tr id="pid'+ productId +'"> \
                  <td class="col-md-4"> \
                    <span data-toggle="modal" onclick="editProductItemDetails('+ productId +', \''+ product.product_name +'\')" data-target="#productReturnDetails"> <i class="fa fa-info-circle productDescription"></i> </span> \
                    <a data-toggle="modal" data-target="#modalDefault" href="<?php echo full_website_address(); ?>/xhr/?module=reports&page=totalPurchasedQuantityOfThisCustomer&cid='+ $("#customersId").val() +'&pid='+productId+'">'+product.product_name+'</a> \
                  </td> \
                  <td class="text-right">'+ parseFloat(product.product_sale_price).toFixed(2) +'</td> \
                  <td class="text-center">'+ product.bought_qnt +'</td> \
                  <td class="text-center">'+ product.returned_qnt +'</td> \
                  <td><input onclick = "this.select()" type="text" name="productQnt[]" value="1" class="productQnt form-control text-center" autoComplete="off"></td> \
                  <td class="subtotalCol text-right">'+ calculateDiscount(product.product_sale_price, product.product_discount) +'</td> \
                  <td style="width: 25px; !important"> \
                    <i style="cursor: pointer;" class="fa fa-trash-o removeThisProduct"></i> \
                  </td> \
                  <input type="hidden" name="productID[]" class="productID" value="'+ productId +'"> \
                  <input type="hidden" class="netSalesPrice" name="productSalePirce[]" value="'+ product.product_sale_price +'"> \
                  <input type="hidden" name="productDiscount[]" value="'+ productDiscount +'" class="productDiscount" autoComplete="off"> \
                  <input type="hidden" name="productItemDetails[]" class="productItemDetails" value=""> \
                </tr>';

          $("#productTable > tbody").append(html);

          // Select Quantity input field
          $("input.productID[value="+productId+"]").closest("tr").find(".productQnt").select();
          
          // Count the Total Quantity
          grandTotal();
          
          // Disable the customer Selection and Warehouse selection if there have any product in the list
          disableEnableWCSelect();

        }
      }
    });
    
  });

  
  // Calculate data
  
  // Calculate details while change product Quantity
  $(document).on("keyup blur", ".productQnt", function() {

    // Count Sub total for each product. 
    var netSalesPrice = $(this).closest("tr").find(".netSalesPrice").val();
    var Quantity = $(this).val();
    var Discount = $(this).closest("tr").find(".productDiscount").val();
    var SubtotalRow = $(this).closest("tr").find("td.subtotalCol");

    // If the product quantity less then 1 then throw an error
    if(Quantity < 1) {
      
      // Display the error message
      Swal.fire({
        title: "Error!",
        text: "Product qunatity must be at least one",
        icon: "error",
        onClose: $("body").css('padding', '0px') // This is because, while close the sweet alert a 15px padding-right is keep.
      });

      // Set product quantity 1
      $(this).val(1)
      Quantity = 1;

    }

    // Display Subtotal for each product
    $(SubtotalRow).html((calculateDiscount(netSalesPrice,Discount) * Quantity).toFixed(2));
    // Call the grand total
    grandTotal();

  });


  // Calculate details while change product Discount
  $(document).on("blur", ".productDiscount", function() {

    // Count Sub total for each product. 
    var subTotal = 0;
    var netSalesPrice = $(this).closest("tr").find(".netSalesPrice").val();
    var Quantity = $(this).closest("tr").find(".productQnt").val();
    var Discount = $(this).val();
    var SubtotalRow = $(this).closest("tr").find("td.subtotalCol");
    
    if(Discount.indexOf("%") > 1 && Discount.replace("%","") >= 100) {
      
      // Display the error message
      Swal.fire({
        title: "Error!",
        text: "Discount Must be below of 100%",
        icon: "error",
        onClose: $("body").css('padding', '0px') // This is because, while close the sweet alert a 15px padding-right is keep.
      });
      $(this).val("");
      return;
      
    } else if (Number(Discount) >= Number(netSalesPrice) && Discount.indexOf("%") < 1) {
      $(this).val("");

      // Display the error message
      Swal.fire({
        title: "Error!",
        text: "Discount Must be below of product purchase price",
        icon: "error",
        onClose: $("body").css('padding', '0px') // This is because, while close the sweet alert a 15px padding-right is keep.
      });
      return;
    }

    // Display Subtotal for each product
    $(SubtotalRow).html((calculateDiscount(netSalesPrice, Discount) * Quantity).toFixed(2));
    
    grandTotal();

  });

  // Remove product from current list
  $(document).on("click", ".removeThisProduct", function() {

    $(this).closest("tr").css("background-color", "red").hide("fast", function() {
      $(this).closest("tr").remove();

      // Count all totals
      grandTotal();

      // Disable the customer Selection and Warehouse selection if there have any product in the list
      disableEnableWCSelect()

    });
    
  });

  // Calculate the order discount
  $(document).on("blur", "#orderDiscountValue", function() {

    var totalAmount = $(".totalAmount").html();
    var orderDiscount = $(this).val();
    
    if(orderDiscount.indexOf("%") > 1 && orderDiscount.replace("%","") >= 100) {
      
      // Display the error message
      Swal.fire({
        title: "Error!",
        text: "Discount Must be below of 100%",
        icon: "error",
        onClose: $("body").css('padding', '0px') // This is because, while close the sweet alert a 15px padding-right is keep.
      });
      $(this).val("");
      return;
      
    } else if (Number(orderDiscount) >= Number(totalAmount) && orderDiscount.indexOf("%") < 1) {
      
      // Display the error message
      Swal.fire({
        title: "Error!",
        text: "Discount Must be below of product purchase price",
        icon: "error",
        onClose: $("body").css('padding', '0px') // This is because, while close the sweet alert a 15px padding-right is keep.
      });
      $(this).val("");
      return;
    }
    
    // Grand total
    grandTotal();

  });


  // Function to count total quantity and total (Grant) amount and display both of these.
  function grandTotal() {
    var productQuantity = 0;
    $(".productQnt").each(function() {
      productQuantity += Number($(this).val());
    })

    var productItem = 0;
    $("#productTable > tbody > tr").each(function() {
      productItem += 1;
    })

    // Display total product quantity and items
    $(".totalItemAndQnt").html(productItem + ' (' + productQuantity + ')');
    
    // Count Total Amount
    var totalAmount = 0;
    $(".subtotalCol").each( function() {
      totalAmount +=  Number($(this).html());
    })

    // Display Total amount 
    $(".totalAmount").html(totalAmount.toFixed(2));

    
    // Calculate the Discount
    var orderDiscount = $("#orderDiscountValue").val();
    var amountAfterDiscount = calculateDiscount(totalAmount, orderDiscount);

    // Display the discount amount in the order discount field
    $(".totalOrderDiscountAmount").html("(-) " + (totalAmount - amountAfterDiscount).toFixed(2));
    

    // Display the Net total
    var calculateNetTotal = amountAfterDiscount;
    $(".netTotalAmount").html(calculateNetTotal);

  }


  // Function to Calculate discount
  function calculateDiscount(amount, discount=null) {

    if(discount === null || discount === 'null' || discount === 0) {
      
      return (Number(amount)).toFixed(2);

    } else if(discount.indexOf("%") > 0 ) {
    
      // For parcantage discount  
      return (Number(amount) - (Number(discount.replace("%",""))/100) * Number(amount)).toFixed(2); 

    } else {

      // For Fixed Discount
      return (Number(amount) - Number(discount)).toFixed(2);

    }
    
  }


    // Submit the sale
    $(document).on('submit', 'form', function(event){ 
      event.preventDefault();

      // Check if There is at least one product in the purchase list
      var num = 0;
      $(".productQnt").each(function() {
        num += Number($(this).val());
      })

      if(num < 1) {
        // Display the error message
        Swal.fire({
          title: "Error!",
          text: "Please add at least one product.",
          icon: "error",
          onClose: $("body").css('padding', '0px') // This is because, while close the sweet alert a 15px padding-right is keep.
        });
        return; 
      }
      
      // Add loading icon in the button after click.
      $("#returnSubmit").html("Submit &nbsp;&nbsp; <i class='fa fa-spin fa-refresh'></i>");

      var formData = new FormData(this);       // Get all form data and store into formData 

      $.ajax({
        url: "<?php echo full_website_address(); ?>/info/?module=productReturn",
        type: "post",
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        success: function(data, status) 
          {
            if(status == "success") {
              //Remove the loading icon from button
              $("#returnSubmit").html("Submit");

              var returnStatus = JSON.parse(data);

              // Redirect to sale invoice print page
              if(returnStatus["returnStatus"] === "success") {
                window.location.href= "<?php echo full_website_address(); ?>/invoice-print/?autoPrint=true&invoiceType=produtReturn&msg=Return successfully completed&id="+ returnStatus["returnId"];
              }

            }
          }
      });

    });
  
  function disableEnableWCSelect() {
    // WC = warehouse and Customer
    var productCountInList = 0;
      $(".productQnt").each(function() {
        productCountInList += Number($(this).val());
      })

      if(productCountInList > 0) {
        $("#customers").prop("disabled", true);
      } else {
        $("#customers").prop("disabled", false);
      }
  }


  // Keyboard Shortcut
  $(document).keydown(function(event) {

    if(event.which == 114) { //F3
      $("#orderDiscount").modal("show");
      return false;
    }

    if(event.which == 115) { //F4
      $("#payment").modal("show");
      return false;
    }
    
});

  // Prevent Enter key for default activity
  $('form').on('keyup keypress', function(e) {
  var keyCode = e.keyCode || e.which;
  if (keyCode === 13) { 

    // Check if the payment modal is open then the enter button should work

    if($("#payment").hasClass("in") === false) {

      e.preventDefault();
      grandTotal();

      if( $("#orderDiscount").hasClass("in") ) { // If orderDiscount is opened the hide it

        $('#orderDiscount').modal('hide');

      } else if( $("#productReturnDetails").hasClass("in") ) { // If orderDiscount is opened the hide it

        $('#productReturnDetails').modal('hide');

      }
      

    }

  }
  
});

// Focuse input field after
$('.modal').on('shown.bs.modal', function () {
  $('.modal input:not("#returnDate")').focus();
})

$(document).ready(function() {
  $("#success-alert").delay(5000).hide(500);
});

function editProductItemDetails(product_id, product_name) {

  // Display the product name on modal
  $("#productReturnDetails .modal-title").html(product_name);
  $("#productReturnDetails .product_id").val(product_id);

  // select product details row
  var product_row = $("tr#pid"+product_id);

  $("#productReturnDetails #productReturnItemQnt").val( product_row.find(".productQnt").val() );
  $("#productReturnDetails #productReturnItemDiscount").val( product_row.find(".productDiscount").val() );
  $("#productReturnDetails #productReturnItemDetails").val( product_row.find(".productItemDetails").val() );

}


// update product item details while close the modal
$('#productReturnDetails').on('hide.bs.modal', function () {

  // Selector
  var product_id = $("#productReturnDetails .product_id").val();
  var product_qnt = $("#productReturnDetails #productReturnItemQnt").val();
  var Discount = $("#productReturnDetails #productReturnItemDiscount").val();

  var product_row = $("tr#pid"+product_id);
  var packetSelector = product_row.find(".productPacket");
  var SubtotalRow = product_row.find("td.subtotalCol");
  var netSalesPrice = product_row.find(".netSalesPrice").val();

  // If the product quantity less then 1 then throw an error
  if(product_qnt < 1) {

    // Display the error message
    Swal.fire({
      title: "Error!",
      text: "Product qunatity must be at least one",
      icon: "error",
      onClose: $("body").css('padding', '0px') // This is because, while close the sweet alert a 15px padding-right is keep.
    });

    // Set product quantity 1
    product_qnt = 1;

  }

  // Display Product Details
  product_row.find(".productQnt").val( product_qnt );
  product_row.find(".productDiscount").val( Discount );
  product_row.find(".productItemDetails").val( $("#productSaleDetails #productSaleItemDetails").val() );

  // Display the subtotal
  $(SubtotalRow).html((calculateDiscount(netSalesPrice,Discount) * product_qnt).toFixed(2));

  // Calculation Again
  grandTotal();

})

$(document).on("change", "#ReturnMoney", function() {

  if( $("#ReturnMoney").is(':checked') ) {
    $(".returnAmountAccountsDiv").show();
  } else {
    $(".returnAmountAccountsDiv").hide();
  }

});

// Scroll to the current product selectiion
$(document).on('focus', '.productQnt', function() {

$(this)[0].scrollIntoView({behavior: "smooth", block: "end", inline: "nearest"});

})

</script>

<?php require DIR_THEME . "footer.php"; ?>