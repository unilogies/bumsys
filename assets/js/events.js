/**
 * Events.js
 * 
 * All events have been written here.
 */

// Load Poduct by filtered
$(document).on("change", "#productCategoryFilter, #productBrandFilter, #productEditionFilter, #productGenericFilter, #productAuthorFilter", function () {

    BMS.PRODUCT.showProduct({
        category: $("#productCategoryFilter").val(),
        brand: $("#productBrandFilter").val(),
        edition: $("#productEditionFilter").val(),
        generic: $("#productGenericFilter").val(),
        author: $("#productAuthorFilter").val(),
    });

});


// Set the warehouse id and customer id to a hidden input filed
// On change
$(document).on("change", "#customers", function () {
    var customersId = $("#customers").val();
    $("#customersId").val(customersId);

    // get customer data
    BMS.fn.get(`getCustomerData&cid=${customersId}`, function (data) {

        // Change discount value
        $("#orderDiscountValue").val(data.discount);

        // Change shipping rate
        $("#packetShippingRate").val(data.shipping_rate);

    });

});

$(document).on("change", "#warehouse", function () {

    var warehouseId = $("#warehouse").val();
    $("#warehouseId").val(warehouseId);

    // Change the selecte2 product url for pos
    $("#selectPosProduct").attr("select2-ajax-url", `${full_website_address}/info/?module=select2&page=productListForPos&wid=${warehouseId}`);

    // Reinitiate select2 after changing ajax url
    BMS.fn.select2("#selectPosProduct");

});

$(document).on("change", "#stockTransferFromWarehouse, #stockTransferToWarehouse", function () {
    var stockTransferFromWarehouseId = $("#stockTransferFromWarehouse").val();
    var stockTransferToWarehouseId = $("#stockTransferToWarehouse").val();

    $("#stockTransferFromWarehouseId").val(stockTransferFromWarehouseId);
    $("#stockTransferToWarehouseId").val(stockTransferToWarehouseId);

});

$(document).on("change", "#scTransferWarehouse", function () {
    var scTransferWarehouseId = $("#scTransferWarehouse").val();

    $("#scTransferWarehouseId").val(scTransferWarehouseId);

});


// Add tariff & Charges Row
$(document).on("click", "#addTariffChargesRow", function () {

    BMS.MAIN.addTariffChargesRow("#tariffCharges");

});

// Get the Product Details and add into the list
$(document).on("click", ".productButton", function () {

    if (isPosPage) {

        BMS.POS.addProduct(this.value);

    } else if (isPurchasePage) {

        BMS.PURCHASE.addProduct(this.value);

    } else if (isTransferStockPage) {

        BMS.STOCK_TRANSFER.addProduct(this.value);

    } else if (isProductReturnPage) {

        BMS.RETURN.addProduct(this.value);

    } else if (isSpecimenCopyPage) {

        BMS.SPECIMEN_COPY.addProduct(this.value);

    } else if (isScDistributionPage) {

        BMS.SC_DISTRIBUTION.addProduct(this.value);

    } else if (isAddProductPage) {

        BMS.PRODUCT.addProduct(this.value);

    } else if (isStockEntryPage) {

        BMS.STOCK_ENTRY.addProduct(this.value);

    } else if (isSaleOrderPage) {

        BMS.ORDER.addProduct(this.value);

    }

});


$(document).on("change", "#selectProduct, #selectPosProduct, #selectStockTransferProduct, #selectStockEntryProduct", function () {

    //BMS.POS.addProduct(this.value);
    if (isPosPage) {

        BMS.POS.addProduct(this.value);

    } else if (isPurchasePage) {

        BMS.PURCHASE.addProduct(this.value);

    } else if (isTransferStockPage) {

        BMS.STOCK_TRANSFER.addProduct(this.value);

    } else if (isProductReturnPage) {

        BMS.RETURN.addProduct(this.value);

    } else if (isSpecimenCopyPage) {

        BMS.SPECIMEN_COPY.addProduct(this.value);

    } else if (isScDistributionPage) {

        BMS.SC_DISTRIBUTION.addProduct(this.value);

    } else if (isAddProductPage) {

        BMS.PRODUCT.addProduct(this.value);

    } else if (isStockEntryPage) {

        BMS.STOCK_ENTRY.addProduct(this.value);

    } else if (isSaleOrderPage) {

        BMS.ORDER.addProduct(this.value);

    }

    $(this).html("<option value=''>Search Product....</option>");

});

$(document).on("click", "#addWastageSaleRow", function () {

    BMS.WASTAGE_SALE.addWastageSaleItem();

});

// Remove product from current list
$(document).on("click", ".removeThisProduct, .removeThisItem", function () {

    $(this).closest("tr").css("background-color", "red").hide("fast", function () {
        $(this).closest("tr").remove();

        if (isPosPage) {

            BMS.POS.grandTotal();
            BMS.POS.disableEnableWCSelect();

        } else if (isPurchasePage) {

            BMS.PURCHASE.grandTotal();

        } else if (isTransferStockPage) {

            BMS.STOCK_TRANSFER.grandTotal();
            BMS.STOCK_TRANSFER.disableEnableWarehouseSelect();

        } else if (isProductReturnPage) {

            BMS.RETURN.grandTotal();

        } else if (isWastageSalePage) {

            BMS.WASTAGE_SALE.grandTotal();

        } else if (isSpecimenCopyPage) {

            BMS.SPECIMEN_COPY.disableEnableWarehouseSelect();

        } else if (isStockEntryPage) {

            BMS.STOCK_ENTRY.grandTotal();

        } else if (isSaleOrderPage) {

            BMS.ORDER.grandTotal();
            BMS.POS.disableEnableWCSelect();

        }

    });

});

// Remove Tariff & Charges Row for POS
$(document).on("click", ".removeThisTariffCharges", function () {

    $(this).closest(".row").css("background-color", "whitesmoke").hide("fast", function () {
        $(this).closest(".row").remove();

        if (isPosPage) {

            BMS.POS.grandTotal();

        } else if (isPurchasePage) {

            BMS.PURCHASE.grandTotal();

        } else if (isProductReturnPage) {

            BMS.RETURN.grandTotal();

        } else if (isWastageSalePage) {

            BMS.WASTAGE_SALE.grandTotal();

        } else if (isSaleOrderPage) {

            BMS.ORDER.grandTotal();

        }

    });

});

// Calculate tarif and Charges
$(document).on("change", ".tariffChargesName", function () {

    if (isPosPage) {

        BMS.POS.grandTotal();

    } else if (isPurchasePage) {

        BMS.PURCHASE.grandTotal();

    } else if (isProductReturnPage) {

        BMS.RETURN.grandTotal();

    } else if (isWastageSalePage) {

        BMS.WASTAGE_SALE.grandTotal();

    } else if (isSaleOrderPage) {

        BMS.ORDER.grandTotal();

    }

});


// Calculate product price on unit change on POS Page
/**
 * This is not required. Now commenting. Will delete in near version
 
$(document).on("change", ".productItemUnit", function() {
    
    if(isPosPage) {

        BMS.POS.productUnitCheck(this);

    } else if(isPurchasePage) {
    
        BMS.PURCHASE.productUnitCheck(this);

    } else if (isTransferStockPage) {
    
        BMS.STOCK_TRANSFER.productUnitCheck(this);

    } else if (isProductReturnPage) {
    
        BMS.RETURN.productUnitCheck(this);

    } else if (isSpecimenCopyPage) {
    
        BMS.SPECIMEN_COPY.productUnitCheck(this);

    } else {

        BMS.PRODUCT.productUnitCheck(this);
    
    } 

});
*/

// Calculate details while change product Quantity
$(document).on("keyup blur", ".productQnt", function () {

    if (isPosPage) {

        BMS.POS.productQntCheck(this);

    } else if (isPurchasePage) {

        BMS.PURCHASE.calculateEachProduct(this);
        BMS.PURCHASE.grandTotal();

    } else if (isTransferStockPage) {

        BMS.STOCK_TRANSFER.productQntCheck(this);

    } else if (isProductReturnPage) {

        BMS.RETURN.grandTotal();
        BMS.RETURN.calculateEachProduct(this);

    } else if (isSpecimenCopyPage) {

        BMS.SPECIMEN_COPY.productQntCheck(this);

    } else if (isStockEntryPage) {

        BMS.STOCK_ENTRY.calculateEachProduct(this);
        BMS.STOCK_ENTRY.grandTotal();

    } else if (isSaleOrderPage) {

        BMS.ORDER.productQntCheck(this);

    }

});

// Open the search product option when enter pressed
$(document).on("keydown", ".productQnt", function (event) {

    if (event.key === "Enter") {
        if (!$('#selectProduct, #selectPosProduct, #selectStockTransferProduct, #selectStockEntryProduct').hasClass("select2-hidden-accessible")) {
            // inititalize the select2 if not initilized
            BMS.fn.select2("#selectProduct, #selectPosProduct, #selectStockTransferProduct, #selectStockEntryProduct");

        }

        $("#selectProduct, #selectPosProduct, #selectStockTransferProduct, #selectStockEntryProduct").select2("open");

    }

});

// Prevent form submit while press enter button in purchase discount
// and hide the purchase discount modal and show the Finalize Purchase modal
$(document).on("keydown", "#purchaseDiscountValue", function (event) {

    if (event.key === "Enter") {

        event.preventDefault();
        $("#purchaseDiscount").modal("hide");
        $("#finalizePurchase").modal("show");

    }

});

// Focus adjust amount on press enter on shipping in Finalize Sale screen
$(document).on("keydown", "#shippingCharge", function (event) {

    if (event.key === "Enter") {

        event.preventDefault();
        $("#adjustAmount").select();

    }

});

// Focus paid amount on press enter on adjust amount in Finalize Sale screen
$(document).on("keydown", "#adjustAmount", function (event) {

    // Ad class for adjust amount auto change
    $(this).addClass("disableAdjustAmountAutoChange");

    if (event.key === "Enter") {

        event.preventDefault();
        $(".posSalePaymentAmount").first().select();
        $(".posSalePaymentAmount").first().addClass("disablePaymentAmountAutoChange");

    }

});


/**
 * For multiple payment method
 * interact payment input with enter
 */
$(document).on("keydown focus", ".posSalePaymentAmount", function (event) {

    var nextPaymentBox = $(this).closest(".row").next(".row").find("input.posSalePaymentAmount");
    $(this).addClass("disablePaymentAmountAutoChange");

    if (nextPaymentBox.length === 1 && event.key === "Enter") {

        event.preventDefault();

        nextPaymentBox.addClass("disablePaymentAmountAutoChange");

        // select next payment input
        nextPaymentBox.select();

    }

});


$(document).on("keydown", ".select2-search__field", function (e) {

    // Check if we are in the search product input and
    // check the input is not empty and and
    // if there at least on product added
    // check if Enter key is pressed then
    // Open the discount modal

    if (isPosPage
        && $(this).attr("aria-controls") === "select2-selectPosProduct-results"
        && this.value === ""
        && $(".productQnt").length > 0
        && e.key === "Enter"
    ) {

        // close the select2 search product box
        $("#selectPosProduct").select2('close');

        // Open Discount
        $("#orderDiscount").modal("show");

    }


    // For purchase page
    if (isPurchasePage
        && $(this).attr("aria-controls") === "select2-selectProduct-results"
        && this.value === ""
        && $(".productQnt").length > 0
        && e.key === "Enter"
    ) {

        // close the select2 search product box
        $("#selectProduct").select2('close');

        // Open Discount
        $("#purchaseDiscount").modal("show");

    }


});


// Open the finalize purchase modal when discount modal is closed
$(document).on("hide.bs.modal", "#orderDiscount", function (e) {

    var orderDiscount = $("#orderDiscountValue");
    var orderDiscountValue = $(orderDiscount).val();

    if( BMS.POS.isGivenDiscountPermitted(orderDiscount) ) {

        // Open payment modal
        $("#payment").modal("show");

        // Calculate order discount
        BMS.POS.grandTotal();


    } else {

        // Display the error message
        Swal.fire({
            title: "Error!",
            text: `You do not have permission to give ${orderDiscountValue} discount.`,
            icon: "error"
        });
        $(orderDiscount).val(0);

        e.preventDefault();

    }

});


$(document).on("blur keyup", ".wastageSaleItemQnt, .wastageSaleItemPrice", function () {

    var itemQnt = $(this).closest("tr").find(".wastageSaleItemQnt").val();
    var itemPrice = $(this).closest("tr").find(".wastageSaleItemPrice").val();

    /* Display the subtotal */
    $(this).closest("tr").find(".wastageSaleItemSubtotal").html(parseFloat(itemQnt * itemPrice).toFixed(2));

    /* Count all totals */
    BMS.WASTAGE_SALE.grandTotal();

});

/* Calculate details while change product Quantity, product Discount and wastageSale price */
$(document).on("blur keyup", "#wastageSaleDiscount, #wastageSalePaidAmount", function () {

    BMS.WASTAGE_SALE.grandTotal();

});

/* Calculate details while change product Quantity, product Discount and purchase price */
$(document).on("blur keyup", ".productPurchaseDiscount, .productPurchasePrice", function () {

    if (isPurchasePage) {

        BMS.PURCHASE.calculateEachProduct(this);
        BMS.PURCHASE.grandTotal();

    } else if (isTransferStockPage) {

        BMS.STOCK_TRANSFER.calculateEachProduct($(this));
        BMS.STOCK_TRANSFER.grandTotal();

    }

});

/* Calculate details while change product Quantity, product Discount and return price */
$(document).on("blur keyup", ".productReturnDiscount, .productReturnPrice", function () {

    BMS.RETURN.calculateEachProduct(this);
    BMS.RETURN.grandTotal();

});

/* Calculate the purchase Discount */
$(document).on("keyup blur", "#purchaseDiscount, #purchasePaidAmount, #purchaseShipping", function () {

    BMS.PURCHASE.grandTotal();

});

/* Calculate the return Discount */
$(document).on("keyup blur", "#returnDiscount, #returnShipping, #returnSurcharge, #returnPaidAmount", function () {

    BMS.RETURN.grandTotal();

});

// Calculate details while change packet
$(document).on("keyup blur", "#productSaleItemPacket", function () {

    // Will Be done later

    //BMS.POS.productPacketCheck();

});

// Calculate details while change product Discount
$(document).on("blur keydown", "#productSaleItemDiscount", function (event) {

    BMS.POS.productDiscountCheck(event);

});

// Calculate the order discount
$(document).on("keydown", "#orderDiscountValue", function (e) {

    var keyCode = e.keyCode || e.which;

    if(keyCode === 13) {
        
        BMS.POS.orderDiscountCheck(this, e);

    }

});

// Calculate Paid amount, change and due
$(document).on("keyup", ".posSalePaymentAmount, #shippingCharge, #totalPackets, #packetShippingRate, #adjustAmount", function (event) {

    BMS.POS.grandTotal(event);

});


// Submit the sale
$(document).on('submit', '#posSale', function (event) {
    
    event.preventDefault();

    // Check if There is at least one product in the purchase list
    var isNoProductAdded = $(".productQnt").length === 0;
    var isStockOutProductAdded = $('.productSO').filter(function () { return this.value == 1 }).length > 0;

    // Check if the customer is set
    if ($("#customersId").val() === "") {
        return BMS.fn.alertError("Please select the customer");
    }

    // Get the submiter button
    var submitter = event.originalEvent.submitter;

    // Confirm if it is hold
    if (submitter.value === 'sale_is_hold' && !confirm("Are you sure to Hold this?")) {
        return;
    }

    if (isNoProductAdded || isStockOutProductAdded) {
        //alert("Please add at least one product.");
        Swal.fire({
            title: "Error!",
            html: isNoProductAdded ? "Please add a least one product" : "Sorry! there have at least one out of stock product in selected warehouse, which are marked with <span style='Background-color:pink;'>Pink background</span>. Please remove them first.",
            icon: "error",
            onClose: $("body").css('padding', '0px') // This is because, while close the sweet alert a 15px padding-right is keep.
        });
        return;
    }

    // Add loading icon in the button after click.
    $(submitter).html("Submit &nbsp;&nbsp; <i class='fa fa-spin fa-refresh'></i>");

    var formData = new FormData(this);       // Get all form data and store into formData 

    // Append the button value with form data
    formData.append(submitter.name, submitter.value);

    // disable submit button untile ajax request complete
    $("#payment").find("button").prop("disabled", true);

    $.ajax({
        url: full_website_address + "/info/?module=pos",
        type: "post",
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        success: function (data, status) {
            if (status == "success") {
                //Remove the loading icon from button
                $(submitter).html("Printing...");

                var saleStatus = JSON.parse(data);

                // Redirect to sale invoice print page
                if (saleStatus["saleStatus"] === "success") {

                    BMS.MAIN.printPage(full_website_address + "/invoice-print/?invoiceType=posSale&id=" + saleStatus["salesId"], event, function () {

                        // Hide the payment modal
                        $('#payment').modal('hide');

                        // Clear POS Screen
                        BMS.POS.clearScreen();

                        // enable submit button after ajax request complete
                        $("#payment").find("button").prop("disabled", false);

                    });

                } else {

                    $("#posErrorMsg").html('<div class="alert alert-danger">' + saleStatus["msg"] + '</div>');

                    // enable submit button after ajax request complete
                    $("#payment").find("button").prop("disabled", false);

                }

            }
        }
    });

});


/* Check if There is at least one product in the purchase list */
$(document).on('submit', '#productPurchase, #productStockTransfer, #productReturn, #specimenCopyForm, #scDistributionForm', function (event) {

    if ($(".productQnt").length < 1) {
        alert("Please add at least one product.");
        event.preventDefault();
        return;
    }

});

/* Check if There is at least one product in the wastageSale list */
$(document).on('submit', '#wastageSaleForm', function (event) {

    var num = $(".wastageSaleItemQnt").length;

    if (num < 1) {
        alert("Please add at least one item.");
        event.preventDefault();
        return;
    } else if (Number($("#wastageSalePaidAmount").val()) > 0 && $("#wastageSaleAccounts").val() === "") {
        alert("Please select the accounts.");
        event.preventDefault();
        return;
    }

});


// Prevent Enter key for default activity
// $(document).on("keyup keypress", "#posSale, #productPurchase, #productReturn, #productStockTransfer, #wastageSaleForm", function (e) {

$(document).on("keypress", "#posSale, #productPurchase, #productReturn, #productStockTransfer, #wastageSaleForm", function (e) {

    var keyCode = e.keyCode || e.which;

    if (keyCode === 13) {

        if ($("#payment, #finalizePurchase, #finalizeReturn, #finalizeWastageSale").hasClass("in") === false) {

            e.preventDefault();

        }


        if (isPosPage) {

            // Check if the payment modal is open then the enter button should work
            // Otherwise others modals should close
            BMS.POS.grandTotal();

            if ($("#orderDiscount").hasClass("in")) { // If orderDiscount is opened the hide it

                $('#orderDiscount').modal('hide');

            } else if ($("#salesTariffCharges").hasClass("in")) { // If orderTax is opened the hide it

                $('#salesTariffCharges').modal('hide');

            } else if ($("#productSaleDetails").hasClass("in")) { // If productSaleDetails is opened the hide it

                $('#productSaleDetails').modal('hide');

            }


        } else if (isPurchasePage) {

            BMS.PURCHASE.grandTotal();

            if ($("#purchaseTariffCharges").hasClass("in")) { /* If purchaseTariffCharges is opened the hide it */

                $('#purchaseTariffCharges').modal('hide');

            } else if ($("#purchaseDiscount").hasClass("in")) { /* If purchaseDiscount is opened the hide it */

                $('#purchaseDiscount').modal('hide');

            }


        } else if (isProductReturnPage) {


            BMS.RETURN.grandTotal();

            if ($("#returnTariffCharges").hasClass("in")) { /* If returnTariffCharges is opened the hide it */

                $('#returnTariffCharges').modal('hide');

            } else if ($("#returnDiscount").hasClass("in")) { /* If returnDiscount is opened the hide it */

                $('#returnDiscount').modal('hide');

            }

        } else if (isWastageSalePage) {

            BMS.WASTAGE_SALE.grandTotal();

            if ($("#wastageSaleTariffCharges").hasClass("in")) { /* If purchaseTariffCharges is opened the hide it */

                $('#wastageSaleTariffCharges').modal('hide');

            } else if ($("#wastageSaleDiscount").hasClass("in")) { /* If purchaseDiscount is opened the hide it */

                $('#wastageSaleDiscount').modal('hide');

            }

        }

    }


});

/* Select input filed on open modal */
$(document).on('shown.bs.modal', '#orderDiscount, #purchaseDiscount, #returnDiscount, #wastageSaleDiscount, #payment', function () {

    $('#purchaseDiscountValue, #purchasePaidAmount, #orderDiscountValue, #shippingCharge, #returnDiscountValue, #returnPaidAmount, #wastageSaleDiscountValue, #shippingCharge').select();

});

/** Load purchase list on opne Purchase list modal */
$(document).on("show.bs.modal", "#customerPurchaseList", function () {

    BMS.fn.get(`customerPurchaseList&cid=${$("#customersId").val()}`, purchaseList => {

        if (purchaseList == "") {

            $("#showPurchaseList ul").html('<li class="alert alert-danger">Sorry! No data found.</li>');
            return
        }

        var purchaseListHtml = "";
        purchaseList.forEach(item => {


            if (item.sales_status === "Hold") {

                purchaseListHtml += `<li class="item">
                                    <a href="${full_website_address}/sales/pos/?edit=${item.id}" class="product-title">
                                        ${item.date} (${item.ref})
                                        <span style="font-size: 12px;" class="label label-success pull-right">${to_money(item.total)}</span>
                                        <span class="product-description">
                                            <span style="font-size: 12px;" class="label label-warning">Hold</span>
                                            From: ${item.shop}; Status: ${item.pay_status}
                                        </span>
                                    </a>
                                </li>`;

            } else {

                purchaseListHtml += `<li onClick="getPurchaseProductList(${item.id}, '${item.ref}');" class="item">
                                    <a href="javascript:void(0)" class="product-title">
                                        ${item.date} (${item.ref})
                                        <span style="font-size: 12px;" class="label label-success pull-right">${to_money(item.total)}</span>
                                        <span class="product-description">
                                            From: ${item.shop}; Status: ${item.pay_status}
                                        </span>
                                    </a>
                                </li>`;

            }

        });

        $("#showPurchaseList ul").html(purchaseListHtml);

    });

});

/** Load purchase list on search */
$(document).on("keydown", ".searchInvoiceInPos", function (event) {


    // When Pressed enter show the search result
    if (event.key === "Enter") {

        // Change the mouse icon to wait
        $("body").toggleClass("wait");

        var search = encodeURIComponent($(this).val());

        BMS.fn.get(`customerPurchaseList&cid=${$("#customersId").val()}&s=${search}`, purchaseList => {

            if (purchaseList == "") {
                /**
                 * Change the mouse icon to auto
                 */
                $("body").toggleClass("wait");

                $("#showPurchaseList ul").html('<li class="alert alert-danger">Sorry! No data found.</li>');

                return
            }

            var purchaseListHtml = "";
            purchaseList.forEach(item => {


                if (item.sales_status === "Hold") {

                    purchaseListHtml += `<li class="item">
                                            <a href="${full_website_address}/sales/pos/?edit=${item.id}" class="product-title">
                                                ${item.date} (${item.ref})
                                                <span style="font-size: 12px;" class="label label-success pull-right">${to_money(item.total)}</span>
                                                <span class="product-description">
                                                    <span style="font-size: 12px;" class="label label-warning">Hold</span>
                                                    From: ${item.shop}; Status: ${item.pay_status}
                                                </span>
                                            </a>
                                        </li>`;

                } else {

                    purchaseListHtml += `<li onClick="getPurchaseProductList(${item.id}, '${item.ref}');" class="item">
                                            <a href="javascript:void(0)" class="product-title">
                                                ${item.date} (${item.ref})
                                                <span style="font-size: 12px;" class="label label-success pull-right">${to_money(item.total)}</span>
                                                <span class="product-description">
                                                    From: ${item.shop}; Status: ${item.pay_status}
                                                </span>
                                            </a>
                                        </li>`;

                }

            });

            $("#showPurchaseList ul").html(purchaseListHtml);

            /**
             * Change the mouse icon to auto
             */
            $("body").toggleClass("wait");

        });

    }

});

// Quick Cash
$(document).on("click", ".quick-cash", function () {
    var quickCash = Number($(this).html());

    //Insert amount into amount field
    $(".posSalePaymentAmount").val(parseFloat(quickCash).toFixed(2));
    // Calculation Again
    BMS.POS.grandTotal();

});

// update product item details while close the modal
$(document).on("hide.bs.modal", "#productSaleDetails", function () {

    // Selector
    var rowId = $("#productSaleDetails .rowId").val();
    var productPrice = $("#productSaleDetails #productSaleItemPrice").val();
    var productDiscount = $("#productSaleDetails #productSaleItemDiscount").val();
    var productPacket = $("#productSaleDetails #productSaleItemPacket").val();
    var productDetails = $("#productSaleDetails #productSaleItemDetails").val();

    var product_row = $("tr#" + rowId);

    // Display Product Details
    product_row.find(".netSalesPrice").val(productPrice);

    var amountAfterDiscount = BMS.FUNCTIONS.calculateDiscount(productPrice, productDiscount);
    var displayProductPrice = "";
    if (Number(amountAfterDiscount) === Number(productPrice)) {
        var displayProductPrice = parseFloat(productPrice).toFixed(2);
    } else {
        var displayProductPrice = "<span>" + amountAfterDiscount + "</span><span><del><small>" + parseFloat(productPrice).toFixed(2) + "</small></del></span>";
    }


    product_row.find(".displayProductPrice").html(displayProductPrice);

    product_row.find(".productDiscount").val(productDiscount);
    product_row.find(".productItemDetails").val(productDetails);
    product_row.find(".productPacket").val(productPacket);

    // Display the subtotal
    product_row.find(".subtotalCol").html(amountAfterDiscount * product_row.find(".productQnt").val());

    // Calculation Again
    BMS.POS.grandTotal();

});

// Scroll to the current product selectiion
$(document).on('focus', '.productQnt', function () {

    //$(this)[0].scrollIntoView({behavior: "smooth", block: "end", inline: "nearest"});

    $(this)[0].scrollIntoView({ behavior: "smooth", block: "nearest", inline: "start" });

});

/* Display the account if return money is checked. */
$(document).on("change", "#ReturnMoney", function () {

    if ($("#ReturnMoney").is(':checked')) {
        $(".returnAmountAccountsDiv").show();
    } else {
        $(".returnAmountAccountsDiv").hide();
    }

});

/**
 * Ledgers Form submit
 */
$(document).on("submit", "#accountsLedgerForm", function (event) {
    event.preventDefault();

    /* Column Defference target column of data table */
    var DataTableAjaxPostUrl = full_website_address + "/xhr/?module=ledgers&page=accountsLedger&account_id=" + $("#accountSelection").val() + "&dateRange=" + $("#accountsLedgerDateRange").val();

    /* Call tatable */
    var getDTable = $('#dataTableWithAjaxExtend').DataTable();

    /* Adjust the column size. This function might be not required. Will check latter */
    //getDTable.columns.adjust().draw();

    /* Refresh the employee data */
    getDTable.ajax.url(DataTableAjaxPostUrl).load();

    /* Accounts info */
    $.post(
        full_website_address + "/info/?module=data&page=getAccountsInfo",
        {
            accountsId: $("#accountSelection").val()
        },

        function (data, status) {

            /* Parse Json Data */
            var accounts = JSON.parse(data);

            $("#accountsName").html(accounts.name);
            $("#accountsBalance").html(tsd(accounts.balance));
            $("#accountsLedgerDates").html($("#accountsLedgerDateRange").val());

        }
    );

});

$(document).on("submit", "#employeeLedgerForm", function (event) {
    event.preventDefault();

    /* Column Defference target column of data table */
    var DataTableAjaxPostUrl = full_website_address + "/xhr/?module=ledgers&page=employeeLedger&emp_id=" + $("#employeeSelection").val() + "&dateRange=" + $("#employeeLedgerDateRange").val();

    /* Call tatable */
    var getDTable = $('#dataTableWithAjaxExtend').DataTable();

    /* Adjust the column size. This function might be not required. Will check latter */
    //getDTable.columns.adjust().draw();

    /* Refresh the employee data */
    getDTable.ajax.url(DataTableAjaxPostUrl).load();

    /* Accounts info */
    $.post(
        full_website_address + "/info/?module=data&page=getEmpSalaryData",
        {
            empId: $("#employeeSelection").val()
        },

        function (data, status) {

            /* Parse Json Data */
            var salaryData = JSON.parse(data);

            /* Calculate total */
            var total = Number(salaryData["emp_payable_salary"]) + Number(salaryData["emp_payable_overtime"]) + Number(salaryData["emp_payable_bonus"]);

            /* Display Details in table */
            $("#salaryInfo > tbody > tr > td:nth-child(1)").html(tsd(salaryData["emp_payable_salary"]));
            $("#salaryInfo > tbody > tr > td:nth-child(2)").html(tsd(salaryData["emp_payable_overtime"]));
            $("#salaryInfo > tbody > tr > td:nth-child(3)").html(tsd(salaryData["emp_payable_bonus"]));
            $("#salaryInfo > tbody > tr > td:nth-child(4)").html(tsd(total));

            $("#employeeName").html(salaryData.emp_firstname + ' ' + salaryData.emp_lastname);
            $("#accountsLedgerDates").html($("#employeeLedgerDateRange").val());

        }
    );

});

$(document).on("submit", "#journalLedgerForm", function (event) {
    event.preventDefault();

    /* Column Defference target column of data table */
    var DataTableAjaxPostUrl = full_website_address + "/xhr/?module=ledgers&page=journalLedger&journal_id=" + $("#journalSelection").val() + "&dateRange=" + $("#journalLedgerDateRange").val();

    /* Call tatable */
    var getDTable = $('#dataTableWithAjaxExtend').DataTable();

    /* Adjust the column size. This function might be not required. Will check latter */
    //getDTable.columns.adjust().draw();

    /* Refresh the journal data */
    getDTable.ajax.url(DataTableAjaxPostUrl).load();

});

$(document).on("submit", "#customerLedgerForm", function (event) {
    event.preventDefault();

    /* Column Defference target column of data table */
    var DataTableAjaxPostUrl = full_website_address + "/xhr/?module=ledgers&page=customerLedger&customer_id=" + $("#customerSelection").val() + "&dateRange=" + $("#customerLedgerDateRange").val();

    /* Call tatable */
    var getDTable = $('#dataTableWithAjaxExtend').DataTable();

    /* Adjust the column size. This function might be not required. Will check latter */
    //getDTable.columns.adjust().draw();

    /* Refresh the customer data */
    getDTable.ajax.url(DataTableAjaxPostUrl).load();

});

$(document).on("submit", "#companyLedgerForm", function (event) {
    event.preventDefault();

    /* Column Defference target column of data table */
    var DataTableAjaxPostUrl = full_website_address + "/xhr/?module=ledgers&page=companyLedger&company_id=" + $("#companySelection").val() + "&dateRange=" + $("#companyLedgerDateRange").val();

    /* Call tatable */
    var getDTable = $('#dataTableWithAjaxExtend').DataTable();

    /* Adjust the column size. This function might be not required. Will check latter */
    //getDTable.columns.adjust().draw();

    /* Refresh the company data */
    getDTable.ajax.url(DataTableAjaxPostUrl).load();

});

$(document).on("submit", "#advancePaymentLedgerForm", function (event) {
    event.preventDefault();

    /* Column Defference target column of data table */
    var DataTableAjaxPostUrl = full_website_address + "/xhr/?module=ledgers&page=advancePaymentLedger&emp_id=" + $("#employeeSelection").val() + "&dateRange=" + $("#advancePaymentLedgerDateRange").val();

    /* Call tatable */
    var getDTable = $('#dataTableWithAjaxExtend').DataTable();

    /* Adjust the column size. This function might be not required. Will check latter */
    //getDTable.columns.adjust().draw();

    /* Refresh the employee data */
    getDTable.ajax.url(DataTableAjaxPostUrl).load();

});



/**
 * Ledgers Form submit for product ledgher
 */
$(document).on("submit", "#productLedgerForm", function (event) {
    event.preventDefault();

    /* Column Defference target column of data table */
    var DataTableAjaxPostUrl = full_website_address + "/xhr/?module=reports&page=productLedger&pid=" + $("#productSelection").val() + "&wid=" + $("#warehouseSelection").val();

    /* Call tatable */
    var getDTable = $('#dataTableWithAjaxExtend').DataTable();

    /* Adjust the column size. This function might be not required. Will check latter */
    //getDTable.columns.adjust().draw();

    /* Refresh the Product data */
    getDTable.ajax.url(DataTableAjaxPostUrl).load();


});



// Customer Statement
$(document).on("submit", "#customerStatementReport", function (event) {
    event.preventDefault();

    /* Column Defference target column of data table */
    var DataTableAjaxPostUrl = full_website_address + "/xhr/?module=reports&page=customerStatement&cid=" + $("#customerSelection").val() + "&dateRange=" + $("#customerStatementDateRange").val();

    /* Call datable */
    var getDTable = $('#dataTableWithAjaxExtend').DataTable();

    /* Adjust the column size. This function might be not required. Will check latter */
    //getDTable.columns.adjust().draw();

    /* Refresh the customer data */
    getDTable.ajax.url(DataTableAjaxPostUrl).load();

    $.post(
        full_website_address + "/info/?module=data&page=getCustomerStatementInfo",
        {
            customerId: $("#customerSelection").val(),
            dateRange: $("#customerStatementDateRange").val(),
        },

        function (data, status) {

            /* Parse Json Data */
            var paymentsData = JSON.parse(data);

            var netPurchased = Number(paymentsData.net_purchased) - (Number(paymentsData.total_product_returns) + Number(paymentsData.total_purchased_discount));
            var totalPaid = Number(paymentsData.advance_payments_amount) + Number(paymentsData.received_payments_amount) + Number(paymentsData.sales_payments_amount);

            var balanceDue = (totalPaid + Number(paymentsData.previous_balance) + Number(paymentsData.total_given_bonus) + Number(paymentsData.special_discounts_amount)) -
                (netPurchased + Number(paymentsData.total_shipping) + Number(paymentsData.payments_return_amount));


            /* Display the payment and sale data */
            $("#paymentInfo > tbody > tr:nth-child(1) > td:nth-child(2)").html(tsd(paymentsData.net_purchased));
            $("#paymentInfo > tbody > tr:nth-child(2) > td:nth-child(2)").html(tsd(paymentsData.total_purchased_discount));
            $("#paymentInfo > tbody > tr:nth-child(3) > td:nth-child(2)").html(tsd(paymentsData.total_shipping));
            $("#paymentInfo > tbody > tr:nth-child(4) > td:nth-child(2)").html(tsd(paymentsData.total_product_returns));
            $("#paymentInfo > tbody > tr:nth-child(5) > td:nth-child(2)").html(tsd(netPurchased));

            $("#paymentInfo > tbody > tr:nth-child(1) > td:nth-child(4)").html(tsd(paymentsData.sales_payments_amount));
            $("#paymentInfo > tbody > tr:nth-child(2) > td:nth-child(4)").html(tsd(paymentsData.received_payments_amount));
            $("#paymentInfo > tbody > tr:nth-child(3) > td:nth-child(4)").html(tsd(paymentsData.advance_payments_amount));
            $("#paymentInfo > tbody > tr:nth-child(5) > td:nth-child(4)").html(tsd(totalPaid));

            $("#paymentInfo > tbody > tr:nth-child(1) > td:nth-child(6)").html(tsd(paymentsData.previous_balance));
            $("#paymentInfo > tbody > tr:nth-child(2) > td:nth-child(6)").html(tsd(paymentsData.total_given_bonus));
            $("#paymentInfo > tbody > tr:nth-child(3) > td:nth-child(6)").html(tsd(paymentsData.special_discounts_amount));
            $("#paymentInfo > tbody > tr:nth-child(4) > td:nth-child(6)").html(tsd(paymentsData.payments_return_amount));
            $("#paymentInfo > tbody > tr:nth-child(5) > td:nth-child(6)").html(tsd(balanceDue));


            $("#customerName").html(paymentsData.customer_name);
            $("#customerAddress").html(paymentsData.customer_address + ', ' + paymentsData.district_name);
            $("#customerStatementDates").html($("#customerStatementDateRange").val());
        }
    );

});



// Customer Statement
$(document).on("submit", "#locationWiseSaleReort", function (event) {
    event.preventDefault();

    /* Column Defference target column of data table */
    var DataTableAjaxPostUrl = full_website_address + "/xhr/?module=reports&page=locationWiseSalesReport&pid=" + $("#selectProductForLocationReport").val() + "&location=" + $("#selectLocation").val();

    /* Call datable */
    var getDTable = $('#dataTableWithAjaxExtend').DataTable();

    /* Adjust the column size. This function might be not required. Will check latter */
    //getDTable.columns.adjust().draw();

    /* Refresh the customer data */
    getDTable.ajax.url(DataTableAjaxPostUrl).load();


});


// New Salary Payment
$(document).on("change", "#paymentEmployee, #dontAdujctAdvance, .paymentsType", function () {

    var that = $(this);

    $.post(
        "<?php echo full_website_address(); ?>/info/?module=data&page=getEmpSalaryData",
        {
            empId: $("#paymentEmployee").val()
        },

        function (data, status) {

            /* if dont need to adjust advance in this month */
            var isDontAdjust = $("#dontAdjustAdvance").is(":checked");

            /* Parse Json Data */
            var salaryData = JSON.parse(data);

            /* Calculate total */
            var total = Number(salaryData["emp_payable_salary"]) + Number(salaryData["emp_payable_overtime"]) + Number(salaryData["emp_payable_bonus"]);

            var loanDetails = "Last taken loan: " + tsd(salaryData['loan_amount']) + "; Paid: " + tsd(salaryData['loan_installment_paid_amount']) + "; Monthly:" + tsd(salaryData['loan_installment_amount']);

            /* Display Details in table */
            $("#salaryInfo > tbody > tr > td:nth-child(1)").html(tsd(salaryData["emp_payable_salary"]));
            $("#salaryInfo > tbody > tr > td:nth-child(2)").html(tsd(salaryData["emp_payable_overtime"]));
            $("#salaryInfo > tbody > tr > td:nth-child(3)").html(tsd(salaryData["emp_payable_bonus"]));
            $("#salaryInfo > tbody > tr > td:nth-child(4)").html(tsd(total));
            $("#salaryInfo > tfoot > tr:nth-child(1) > th").html(loanDetails);


            /* Change Amount by payment type */
            var paymentTypes = $(that).val();

            if (paymentTypes == "Salary") {
                $(that).closest(".row").find(".paymentAmount").val(Number(salaryData["emp_payable_salary"]).toFixed(0))
            } else if (paymentTypes == "Overtime") {
                $(that).closest(".row").find(".paymentAmount").val(Number(salaryData["emp_payable_overtime"]).toFixed(0))
            } else if (paymentTypes == "Bonus") {
                $(that).closest(".row").find(".paymentAmount").val(Number(salaryData["emp_payable_bonus"]).toFixed(0))
            } else if (paymentTypes == "Advance Salary") {
                $(that).closest(".row").find(".paymentAmount").val(0)
            } else {
                $(".paymentAmount").val(Number(salaryData["emp_payable_salary"]).toFixed(0));
            }

        }

    );
});

$(document).on("change", "#paymentMethods", function () {
    if ($("#paymentMethods").val() == "Cheque") {
        $("#hiddenItem").css("display", "block");
    } else {
        $("#hiddenItem").css("display", "none");
    }
});

/* Add Payment salary row
 The first is used to remove envent listener. */
$(document).off("click", "#addSalaryPaymentRow");
$(document).on("click", "#addSalaryPaymentRow", function () {

    var html = '<div class="row"> \
        <div class="col-md-4"> \
            <div class="form-group required"> \
                <select name="paymentsType[]" class="paymentsType form-control" style="width: 100%;" required> \
                    <option value="Salary">Salary</option> \
                    <option value="Overtime">Overtime</option> \
                    <option value="Bonus">Bonus</option> \
                </select> \
            </div> \
        </div> \
        <div class="col-md-3"> \
            <div class="form-group required"> \
                <input type="number" name="paymentAmount[]" class="paymentAmount form-control" required> \
            </div> \
        </div> \
        <div class="col-md-4"> \
            <div class="form-group"> \
                <input type="text" name="salaryPaymentNote[]" class="salaryPaymentNote form-control"> \
            </div> \
        </div> \
        <div class="col-xs-1"> \
            <i style="cursor: pointer; padding: 10px 5px 0 0;" class="fa fa-trash-o removeSalaryPaymentRow"></i> \
        </div> \
    </div>';

    $("#salaryPaymentRow").append(html);

});

/* Remove Salary payments row */
$(document).on("click", ".removeSalaryPaymentRow", function () {
    $(this).closest(".row").css("background-color", "whitesmoke").hide("slow", function () {
        $(this).closest(".row").remove();
    });
});

// Date time and range picker classess
$(document).on("focusin", ".datePicker", function () {

    BMS.FUNCTIONS.datePicker();

});

$(document).on("focusin", ".dateTimePicker", function () {

    BMS.FUNCTIONS.datePicker({
        selector: this,
        format: "YYYY-MM-DD HH:mm",
        timePicker: true
    });

});

$(document).on("focusin", ".dateRangePicker", function () {

    BMS.FUNCTIONS.dateRangePicker();

});

$(document).on("focusin", ".dateTimeRangePicker", function () {

    BMS.FUNCTIONS.dateRangePicker({
        selector: this,
        format: "YYYY-MM-DD HH:mm",
        timePicker: true
    });

});


$(document).on("focusin", ".dateRangePickerPreDefined", function () {

    BMS.FUNCTIONS.dateRangePickerPreDefined();

});


$(document).on("focusin", ".dateTimeRangePickerPreDefined", function () {

    BMS.FUNCTIONS.dateRangePickerPreDefined({
        selector: this,
        format: "YYYY-MM-DD HH:mm",
        timePicker: true
    });

});


$(document).on("focusin", ".multiDatePicker", function () {

    BMS.FUNCTIONS.multiDatePicker();

});

/* Function to preview image */
$(document).on("change", ".imageToUpload", function () {

    /** Select the message container */
    var msgContainer = $(this).closest(".imageContainer").find(".photoErrorMessage");

    /** Clear previous msg */
    $(msgContainer).empty();

    /** Get the selecte files */
    var file = this.files[0];
    var fileType = file.type;
    var imagesize = file.size;

    /** Declare the valid image extension */
    var validImage = ["image/jpeg", "image/png", "image/jpg"];
    var maxUploadSize = "<?php echo $_SETTINGS['MAX_UPLOAD_SIZE'] * 1024 * 1024; ?>";

    /** If an invalid file upload the show an error msg  */
    if (validImage.includes(fileType) === false) {

        /**
         * If there is no msg container then alert with pop up msg
         */
        if ($(msgContainer).length < 1) {

            BMS.fn.alertError("<?php echo __('Please select a valid image file'); ?>");

            /** 
             * Remove the selected input
             */
            this.value = this.defaultValue;

        } else {

            $(msgContainer).html("<div style='margin-bottom: 0;' class='alert alert-danger'><?php echo __('Please select a valid image file'); ?></div>");

        }

        return false;



    } if (maxUploadSize < imagesize) {

        /**
         * If there is no msg container then alert with pop up msg
         */
        if ($(msgContainer).length < 1) {

            BMS.fn.alertError("<?php echo __('Max file size %d MB', $_SETTINGS['MAX_UPLOAD_SIZE']); ?>");

            /** 
             * Remove the selected input
             */
            this.value = this.defaultValue;

        } else {

            $(msgContainer).html("<div class='alert alert-danger'><?php echo __('Max file size %d MB', $_SETTINGS['MAX_UPLOAD_SIZE']); ?></div>");

        }

        return false;


    } else {

        var imageName = $(this).val().replace(/\\/g, '/').replace(/.*\//, '');

        $(this).closest(".imageContainer").find(".imageNameShow").val(imageName);

        var that = $(this);

        var reader = new FileReader();
        reader.onload = function (e) {
            $(that).css("color", "green");
            $(that).closest(".imageContainer").find('.image_preview').css("display", "block");
            $(that).closest(".imageContainer").find('.previewing').attr('src', e.target.result);
            //$(that).closest(".imageContainer").find('.previewing').attr('width', 'auto');
            //$(that).closest(".imageContainer").find('.previewing').attr('height', '100%');
        };
        reader.readAsDataURL(this.files[0]);

    }

});



/** Multiple Payment Rows */
$(document).on("click", "#addPosSalePaymentRow", function () {

    var paymentItemRow = `<div style="margin: 0;" class="row">
        <div class="form-group pull-right col-md-3 adjustFormGroupPadding">
            <label>Amount</label>
            <span style="cursor:pointer; padding: 0 4px;" class="pull-right removePosPaymentItem" ><i class="fa fa-times"></i></span>
            <input type="number" onclick="this.select();" name="posSalePaymentAmount[]" class="form-control posSalePaymentAmount" step="any">
        </div>
        <div class="form-group pull-right col-md-3 adjustFormGroupPadding">
            <label>Reference/Info</label>
            <input type="text" name="posSalePaymentReference[]" class="form-control">
        </div>
        <div style="display: none;" class="posSalePaymentBankAccount form-group pull-right col-md-3 adjustFormGroupPadding">
            <label>Bank Account</label>
            <select name="posSalePaymentBankAccount[]" class="form-control pull-left select2Ajax" 
                select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=accountList" style="width: 100%;">
                <option value=""><?php echo __("Select Account"); ?>....</option>
            </select>
        </div>
        <div class="form-group pull-right col-md-3 adjustFormGroupPadding">
            <label>Payment Method</label>
            <select name="posSalePaymentMethod[]" class="posSalePaymentMethod form-control">
                <option value="Cash"><?php echo __("Cash"); ?></option>
                <option value="Bank Transfer"><?php echo __("Bank Transfer"); ?></option>
                <option value="Cheque"><?php echo __("Cheque"); ?></option>
                <option value="Card"><?php echo __("Card"); ?></option>
                <option value="Others"><?php echo __("Others"); ?></option>
            </select>
        </div>
    </div>`;

    $(".paymentMethodBox").append(paymentItemRow);

});

/** Remove POS payment items */
$(document).on("click", ".removePosPaymentItem", function () {

    $(this).closest(".row").remove();

    // Calculate again after remove payment item
    BMS.POS.grandTotal();

});

$(document).on("change", ".posSalePaymentMethod", function () {

    if ($(this).val() === "Cash") {

        $(this).closest(".row").find(".posSalePaymentBankAccount").hide();

    } else {

        $(this).closest(".row").find(".posSalePaymentBankAccount").show();

    }

});

/**
 * Automatic form submition by jquery ajax
 */
$(document).on("submit", "#modalForm, #popUpForm, #jqFormAdd, #jqFormUpdate", function (event) {

    event.preventDefault();   // Prevent Loading

    ajaxFormSubmit(this);

});

// for inline form only
$(document).on("submit", "#inlineForm", function (event) {

    event.preventDefault();   // Prevent Loading

    // clear the form by loading the current page again
    ajaxFormSubmit(this, true);

});

// Delete Entry
$(document).on('click', '.deleteEntry', function (event) {

    // Prevent default submit
    event.preventDefault();

    var deletePostUrl = $(this).attr("href");
    var datatoDelete = $(this).attr("data-to-be-deleted");
    var removeParent = $(this).attr("removeParent");

    var that = this;

    Swal.fire({

        title: 'Are you sure to delete this entry?',
        icon: 'warning',
        didOpen: function () {
            BMS.fn.play("warning");
        },
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',

    }).then((result) => {

        if (result.value) {

            $.post(deletePostUrl,
                {
                    datatoDelete: datatoDelete
                },

                function (data, status) {

                    // Check If the return data is JSON
                    if (isJson(data)) {

                        // Parse json store in parsedJson
                        var parsedJson = JSON.parse(data);

                        for (var key in parsedJson) {

                            sweetAlert[key] = parsedJson[key];

                        }

                    }

                    // Show the success or error msg.
                    Swal.fire({
                        toast: sweetAlert.toast,
                        position: sweetAlert.position,
                        timer: sweetAlert.timer,
                        showConfirmButton: sweetAlert.showConfirmButton,
                        showCloseButton: sweetAlert.showCloseButton,
                        icon: sweetAlert.icon,
                        title: sweetAlert.title,
                        text: sweetAlert.text,
                        onClose: $("body").css('padding', '0px') // This is because, while close the sweet alert a 15px padding-right is keep.
                    });


                    // Remove the deleted elements
                    

                    if(removeParent !== undefined) {

                        $(that).closest(removeParent).hide('fast');
                    }


                    // Reloade the Datatable
                    $('#dataTableWithAjax').DataTable().ajax.reload(null, false);
                    $('#dataTableWithAjaxExtend').DataTable().ajax.reload(null, false);

                }
            );

        }

    })

});
// End Delete Entry

// Update Entry
$(document).on('click', '.updateEntry', function (event) {

    // Prevent default submit
    event.preventDefault();

    var updatePostUrl = $(this).attr("href");
    var datatoUpdate = $(this).attr("data-to-be-updated");
    var that = this;

    $(this).css("color", "black");
    $(this).find("i.fa").addClass("fa-spin");

    $.post(updatePostUrl,
        {
            datatoUpdate: datatoUpdate
        },

        function (data, status) {

            // Check If the return data is JSON
            if (isJson(data)) {

                // Parse json store in parsedJson
                var parsedJson = JSON.parse(data);

                for (var key in parsedJson) {

                    sweetAlert[key] = parsedJson[key];

                }

            }

            // Show the success or error msg.
            Swal.fire({
                toast: sweetAlert.toast,
                position: sweetAlert.position,
                timer: sweetAlert.timer,
                showConfirmButton: sweetAlert.showConfirmButton,
                showCloseButton: sweetAlert.showCloseButton,
                icon: sweetAlert.icon,
                title: sweetAlert.title,
                text: sweetAlert.text,
                onClose: $("body").css('padding', '0px') // This is because, while close the sweet alert a 15px padding-right is keep.
            })


            // Reloade the Datatable
            $('#dataTableWithAjax').DataTable().ajax.reload(null, false);
            $('#dataTableWithAjaxExtend').DataTable().ajax.reload(null, false);

        }
    );

});
// End Update Entry

// Inline Edit
$(document).on('dblclick', 'iledit', function (event) {

    // Prevent default submit
    event.preventDefault();

    var $this = $(this);
    var $thistd = $this.closest("td");
    var $header = $this.closest('table').find('th').eq($thistd.index());
    var pKey = $this.closest('tr').find('pkey').text(); // pkey = primary key where to update

    var type = $header.attr("type");
    var dataSource = $header.attr("data-source");
    var dataOptions = $header.attr("data-options");
    var whereToUpdate = $header.attr("where-to-update");

    var dataVal = $this.attr("data-val");
    var dataText = $this.text();
    var elemWidth = $thistd.width();

    // Show the editor
    if (type === "select2") {
        $thistd.html(`
            <select class="form-control select2InlineEdit" select2-ajax-url="${dataSource}" style="width: ${elemWidth}px;" required>
                <option value=""><?php echo __("Select Entry"); ?>....</option>
            </select>
        `);

        // Initialize select2 and open it
        var $select2InlineEdit = $thistd.find(".select2InlineEdit");
        BMS.fn.select2($select2InlineEdit, dataVal, dataText);
        $select2InlineEdit.select2("open");

    } else if (type === "select") {

        dataOptions = dataOptions.split(",");
        dataOptions.forEach(function (item) {

            var selected = (dataText == item) ? "selected" : "";
            dataOptions += `<option ${selected} value='${item}'>${item}</option>`;

        });

        $thistd.html(`
            <select class="form-control" style="width: ${elemWidth}px; padding: 6px 6px;" required>
                ${dataOptions}
            </select>
        `);

        // Initialize select2 and open it
        var $select2InlineEdit = $thistd.find("select");
        $select2InlineEdit.select2()
        $select2InlineEdit.select2("open");

    } else {

        $thistd.html(`
            <input type="text" value="${dataText}" style="width: ${elemWidth}px;" class="form-control">
        `);

        // If date picker type then initital datepicker
        if (type === "datePicker") {
            $thistd.find("input").addClass("datePicker");
            $thistd.find(".datePicker").focus();
        }

    }

    

    // Change and save the value
    $thistd.find('input, select, textarea').on('enter blur clear apply.daterangepicker, select2:close', function () {


        var oldData = ['select2', 'select'].includes(type) ? dataVal : dataText;
        var newData = $(this).val();
        var that = $(this);

        // If old data and new data is not same
        if (newData !== oldData) {

            $.post(whereToUpdate,
                {
                    pkey: pKey,
                    newData: newData
                },

                function (data, status) {

                    var data = JSON.parse(data);

                    if (data["error"] !== undefined && data["error"] === "true") {

                        BMS.fn.alertError(data["msg"]);

                    } else {

                        // Show the success message
                        BMS.fn.alertSuccess("The data has been successfully updated.");

                        // Display changed items
                        if (type === 'select2' || type === 'select') {

                            $thistd.html(`<iledit data-val='` + $(that).val() + `'>` + $("option:selected", that).text() + `</iledit>`);

                        } else {

                            $thistd.html(`<iledit>` + $(that).val() + `</iledit>`);

                        }

                    }

                }
            );

        } else {


            // Display data items if not changed
            if (type === 'select2' || type === 'select') {

                $thistd.html(`<iledit data-val='` + $(that).val() + `'>` + $("option:selected", that).text() + `</iledit>`);

            } else {

                $thistd.html(`<iledit>` + $(that).val() + `</iledit>`);

            }

        }

    });

});
// Inline Edit


// Remove jquery modal hidden data
$(document).on('hidden.bs.modal', function (e) {
    var target = $(e.target);
    target.removeData('bs.modal')
        .find(".modal-ajax").html('');
});
// End Remove jquery modal hidden data



// Select2 Ajax
$(document).ready(function () {

    // focus is used while users press TAB button to come this field
    $(document).on('mouseenter focus', '.select2Ajax', function () {

        BMS.fn.select2(this);

    });

});
// End Select2 Ajax

// ToolTip
$(document).ready(function () {
    $('body').tooltip({
        selector: '[data-toggle="tooltip"]'
    });
});
// End ToolTip

/********************************************************************
* fix menu overflow under the responsive table 
* hide menu on click... (This is a must because when we open a menu )
*
 *******************************************************************/
$(document).click(function (event) {
    //hide all our dropdowns
    $('.dropdown-menu[data-parent]').hide();

});
$(document).on('click', '.col-sm-12 [data-toggle="dropdown"]', function () {
    // if the button is inside a modal
    if ($('body').hasClass('modal-open')) {
        throw new Error("This solution is not working inside a responsive table inside a modal, you need to find out a way to calculate the modal Z-index and add it to the element")
        return true;
    }

    $buttonGroup = $(this).parent();
    if (!$buttonGroup.attr('data-attachedUl')) {
        var ts = +new Date;
        $ul = $(this).siblings('ul');
        $ul.attr('data-parent', ts);
        $buttonGroup.attr('data-attachedUl', ts);
        $(window).resize(function () {
            $ul.css('display', 'none').data('top');
        });
    } else {
        $ul = $('[data-parent=' + $buttonGroup.attr('data-attachedUl') + ']');
    }
    if (!$buttonGroup.hasClass('open')) {
        $ul.css('display', 'none');
        return;
    }
    dropDownFixPosition($(this).parent(), $ul);
    function dropDownFixPosition(button, dropdown) {
        var dropDownTop = button.offset().top + button.outerHeight();
        dropdown.css('top', dropDownTop + "px");
        dropdown.css('left', button.offset().left - 100 + "px");
        dropdown.css('position', "absolute");

        dropdown.css('width', dropdown.width());
        dropdown.css('heigt', dropdown.height());
        dropdown.css('display', 'block');
        dropdown.appendTo('.dynamic-container');
    }
});


// If there a restricted class exists then prevent the default behaviour
// ******************** Must Be awared **************
// ********* The following two snippet must be bellow on other javascript
$(document).on('click', '.restricted', function () {

    Swal.fire({
        title: 'Sorry!',
        text: 'You have no permission to perform this action',
        icon: 'error'
    });

})

$('.modal').on('show.bs.modal', function (e) {

    // If restricted class exists then disable to open the modal
    if ($.inArray("restricted", e.target.classList) !== -1) {
        return e.preventDefault();
    }

});

/** Eneable auto focus in bs modal */
$('.modal').on('shown.bs.modal', function () {
    $(this).find('[autofocus]').focus();
});
// *********************
//Disable mouse While event in number input
// disable mousewheel on a input number field when in focus
// (to prevent Chromium browsers change the value when scrolling)
$('form').on('focus', 'input[type=number]', function (e) {
    $(this).on('wheel.disableScroll', function (e) {
        e.preventDefault()
    })
});
$('form').on('blur', 'input[type=number]', function (e) {
    $(this).off('wheel.disableScroll')
});

/* Sweet Alert Default configuration */
var sweetAlert = [];
sweetAlert.title = 'The command has been executed successfully.';
sweetAlert.text = '';
sweetAlert.icon = 'success';
sweetAlert.toast = true;
sweetAlert.position = 'top';
sweetAlert.timer = 5000;
sweetAlert.showConfirmButton = false;
sweetAlert.showCloseButton = false;

/* iCheck for checkbox and radio inputs */
$('input[type="checkbox"].square, input[type="radio"].square').iCheck({
    checkboxClass: 'icheckbox_square-blue',
    radioClass: 'iradio_square-blue'
});

/* Global Crsf */
$.ajaxSetup({
    beforeSend: function (xhr) {
        xhr.setRequestHeader("X-CSRF-TOKEN", xCsrfToken);
    }
});

// get page title json 
var getPageTitle = JSON.parse(`<?php echo json_encode($_SETTINGS["PAGE_TITLE"]); ?>`);

// This will work on browser back and forward button
$(window).bind("popstate", function (event) {

    if (typeof session !== 'undefined') {
        // terminate the call session when change the page
        session.terminate();
        
        // Stop the Phone
        phone.stop();
    }

    event.preventDefault();

    $.get(location.href + "?contentOnly=true", function (data, status) {

        // clear the value
        defaultiDisplayLength = '';

        // remove the all classes that are curently active
        $("li").removeClass("active");

        // Generate the page slug
        var pageSlug = location.href;

        // Generate the page title
        var pageTitle = "Not Found!";
        if (getPageTitle[pageSlug] !== undefined) {
            pageTitle = getPageTitle[pageSlug];
        }

        // Set the page title
        window.document.title = pageTitle;

        $(".dynamic-container").html(data);
        jQuery.get(full_website_address + '/assets/js/initiator.min.js');

    });

});


/** Creating custom shortcuts
 * F2 = Open select2 product search
 */
$(document).on("keydown", function (event) {

    if (event.key === "F2") {
        if (!$('#selectProduct, #selectPosProduct, #selectStockTransferProduct, #selectStockEntryProduct').hasClass("select2-hidden-accessible")) {
            // inititalize the select2 if not initilized
            BMS.fn.select2("#selectProduct, #selectPosProduct, #selectStockTransferProduct, #selectStockEntryProduct");

        }

        $("#selectProduct, #selectPosProduct, #selectStockTransferProduct, #selectStockEntryProduct").select2("open");

    }

    if (event.key === "F3") {

        event.preventDefault();

        $("#orderDiscount").modal("show");

    }

});

/** Toogle not filter button */
$(document).on("click", ".toggleNotButton", function () {

    var notThisValue = $(this).closest("span").find(".notThisValueFilter");

    if ($(notThisValue).val() === "=") {

        $(notThisValue).val("!=");
        $(this).text("|");

    } else {

        $(this).text("");
        $(notThisValue).val("=");

    }

});


$(document).on("click", ".stopPropagation", function (e) {

    e.stopPropagation();

});


/**
 * Toggle the product vairation based on product type
 * 
 * Effected Pages:
 * 1. module/products/new-product
 * 2. module/products/edit-product
 */
$(document).on("change", "#productType", function () {

    // Hide all container
    $("#productVariationSection").hide();
    $("#bundleProductsContainer").hide();

    // Hide title
    $(".bundleProduct").hide();
    $(".groupedProduct").hide();

    var boxTitle = "Bundle Product";
    // Then show according to selection
    if (this.value === "Variable") {

        $("#productVariationSection").show();

    } else if ( this.value === "Bundle") {

        $(".bundleProduct").show();
        $("#bundleProductsContainer").show();

    } else if( this.value === "Grouped" ) {

        $(".groupedProduct").show();
        $("#bundleProductsContainer").show();

    }


});


/**
 * Delete the product variation 
 * 
 * Effected Pages:
 * 1. module/products/edit-product
 */
$(document).on("click", ".removeThisVariation", function (e) {

    e.stopPropagation();

    if (confirm("Are sure to remove this variation?")) {

        var product_id = $(this).closest("li").find(".variation_product_id").val();

        $(this).closest("li").hide("fast", function () {
            $(this).remove();
        });

        /** If there have any product id, then delete it from database */
        if(product_id !== undefined) {

            $.ajax({
                url: full_website_address + `/xhr/?module=products&page=deleteVariationProduct`,
                type: "post",
                data: {
                    product_id: product_id
                },
                success: function (data, status) {
                   //
                }
            });


        }

    }

});


$(document).on("click", "#setVariationPrice", function () {

    setVariationPrice();

});


$(window).keydown(function (event) {

    if (event.keyCode == 13 && $("#productVariationPrice").hasClass("in")) {
        event.preventDefault();
        setVariationPrice();
        $("#productVariationPrice").modal("hide");
        return false;
    }

});

/* Browse Product */
$("#browseProduct").on("show.bs.modal", function (e) {

    BMS.PRODUCT.showProduct();

});

/** Disable intital stock while the product expire date yes */
$(document).on("change", "#productHasExpiryDate", function () {

    if (this.value === "1") {
        $(".productIntitalStock").prop("disabled", true);
    } else {
        $(".productIntitalStock").prop("disabled", false);
    }

});

let barCcode = "";
$(document).on('keypress', (e) => {

    // bar code scanner hit enter after complete the scane
    if (e.key === "Enter" ) {

        var isNotInput = e.target.tagName.toLowerCase() !== 'input';
        
        if(barCcode !== "") {

            if(isPosPage && isNotInput) {

                BMS.POS.addProduct(barCcode, true);
    
            } else if(isPurchasePage && isNotInput) {
    
                BMS.PURCHASE.addProduct(barCcode, true);
    
            } else if(isTransferStockPage && isNotInput) {

                BMS.STOCK_TRANSFER.addProduct(barCcode, true);

            } else if(isProductReturnPage && isNotInput) {

                BMS.RETURN.addProduct(barCcode, true);

            } else if (isStockEntryPage && isNotInput) { 

                BMS.STOCK_ENTRY.addProduct(barCcode, true);
                
            } else if(isAddProductPage) {
                // update the product code in product add page
                $("#productCode").val(barCcode);
            }

        }

        // Reset the code
        barCcode = "";


    } else {
        
        // Apend to barcode
        barCcode += e.key; 

    }

});


// Copy by Class

$(document).on('click', ".copyThis", function(e){

    navigator.clipboard.writeText( $(this).text() );

});