// Function to add thousand separator and decimal
// TSD = thousand seprator and Decimal
function tsd(num) {
    return Number(num).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');

}

/**
 * 
 * @param {number} ms The number of milliseconds
 * Usage: await sleep(200);
 * @returns 
 */
function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

/**
 * 
 * @param {string} data The string or number which need to check empty or not
 * @returns True if empty otherwise false
 */
function empty(data) {
    return data === "" || data === null || data === 'null';
}

/**
 * 
 * @param {string} name the index name to retrived from local storage
 * @returns the value of the item from local storage
 */
function get_options(name) {
    return localStorage.getItem(name);
}

/**
 * 
 * @param {string|number} number The input number which need to be formated
 * @param {number} decimalPlaces The decimal place/ How many number need after the decimal
 * @param {string} decimalSeparator The decimal separtor
 * @param {string} thousandSeparator The thousand seperator
 * @param {string} currencySymbol Symbol of the currency
 * @param {string} currencySymbolPosition Currency symbol position. left of right
 * @returns The formated number
 */
function to_money(
    number, 
    decimalPlaces = null,
    decimalSeparator = "",
	thousandSeparator = "",
	currencySymbol = "",
	currencySymbolPosition = ""
    ) {

        var currencySymbol = empty(currencySymbol) ? get_options("currencySymbol") : currencySymbol;
        var currencySymbolPosition = empty(currencySymbolPosition) ? get_options("currencySymbolPosition") : currencySymbolPosition;

        var formatedNumber = format_number(number, decimalPlaces = decimalPlaces, decimalSeparator = decimalSeparator, thousandSeparator = thousandSeparator);

        if( !empty(currencySymbolPosition) && currencySymbolPosition.toLowerCase === "right" ) {
            formatedNumber = formatedNumber + " " +  currencySymbol;
        } else {
            formatedNumber = currencySymbol + " " + formatedNumber;
        }

        return formatedNumber;
}

function format_number(
    number, 
    decimalPlaces = null,
    decimalSeparator = "",
	thousandSeparator = ""
    ) {

        var number = Number(number);
        var decimalPlaces = empty(decimalPlaces) ? get_options("decimalPlaces") : decimalPlaces;
        var decimalSeparator = empty(decimalSeparator) ? get_options("decimalSeparator") : decimalSeparator;
        var thousandSeparator = empty(thousandSeparator) ? get_options("thousandSeparator") : thousandSeparator;

        /** Regex taken from VisioN: https://stackoverflow.com/a/14428340 */
        var re = '\\d(?=(\\d{' + (3) + '})+' + (decimalPlaces > 0 ? '\\D' : '$') + ')',
        num = number.toFixed(Math.max(0, ~~decimalPlaces));
        var formatedNumber = (decimalSeparator ? num.replace('.', decimalSeparator) : num).replace(new RegExp(re, 'g'), '$&' + (thousandSeparator || ','));

        return formatedNumber;

}


function getPurchaseProductList(saleid, saleRef) {

    /** Hide the customer purchase list */
    $("#showPurchaseList").hide();

    /** Show the purchase product list */
    $("#showPurchaseProductList").show();

    BMS.fn.get(`customerPurchaseProductList&saleid=${saleid}`, purchaseList => {
        
        var purchaseListHtml = "";
        purchaseList.forEach(item=> {

            purchaseListHtml += `<li style="cursor: pointer" 
                                    onClick="BMS.POS.addReturnProduct(
                                        '${item.pid}', 
                                        '${item.pn}', 
                                        '${item.pg}', 
                                        '${item.batch}', 
                                        '${item.pu}', 
                                        '${item.hed}',
                                        '${item.stock_item_discount}', 
                                        '${item.stock_item_price}', 
                                        '${item.stock_item_qty}', 
                                        '${item.stock_item_subtotal}'
                                    )" class="item">
                                    <a href="javascript:void(0)" class="product-title">${item.pn}
                                        <span style="font-size: 12px;" class="label label-success pull-right"> <i class="fa fa-undo"></i> </span>
                                        <span class="product-description">
                                            ${item.stock_item_qty}${(item.pu === null) ? "" : item.pu} @ ${to_money(item.stock_item_price)}
                                        </span>
                                    </a>
                                </li>`;

        });

        
        $(".saleReferenceShow").html(`<p style="font-size: 14px; font-weight: bold;">Product list for: ${saleRef}</p>`);
        $("#showPurchaseProductList ul").html(purchaseListHtml);

    });

}


function backToPurchaseList() {
    
    /** show the customer purchase list */
    $("#showPurchaseList").show();

    /** Hide the purchase product list */
    $("#showPurchaseProductList").hide();

}

 // Function check is input json or not
function isJson(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}

function sumInputs(input) {
    var sum = 0;

    $(input).each(function(){
        sum +=  $(this).val() === "" ? 0 : parseFloat( $(this).val() );
    });

    return sum;
}


function disableInput(selector, status=true) {

    // Add a class called disableInput in which are not disable by default
    $(selector).find("input, select, button, textarea").not('[disabled]').addClass("disableInput");

    // Then desable/ Enable input which are not disable by default
    $(".disableInput").prop("disabled", status);
    
}

function ajaxFormSubmit(selector, clearForm = false) {

    // Change the mouse icon to wait
    $("body").toggleClass("wait");

    var submitButton = $(selector).find(":submit");  // Select the submit button
    var submitButtonText = $(submitButton).html();

    // Add loading icon in the button after click.
    $(submitButton).html(submitButtonText + " &nbsp;&nbsp; <i class='fa fa-spin fa-refresh'></i>");

    var actionUrl = $(selector).attr("action");   // Get the form action url and store into postUrl
    var formMethod = $(selector).attr("method");  // Get the form method and store in formMethod
    var formData = new FormData(selector);       // Get all form data and store into formData 

    /**
     * Disable all input field until ajax request complete. 
     */
    disableInput(selector);

    var that = selector;

    $.ajax({
        url: actionUrl,
        type: formMethod,
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        success: function (data, status) {

            if (status == "success") {

                /**
                 * Display the sucess or error massage.  
                 * Check If the return data is JSON
                 */
                if (isJson(data)) {

                    // Parse json store in parsedJson
                    var parsedJson = JSON.parse(data);

                    //console.log(parsedJson);

                    for (var key in parsedJson) {

                        sweetAlert[key] = parsedJson[key];

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

                    /**
                     * Hide the modal if we alert through sweet alert
                     */
                    $(".modal.in").modal("hide");

                } else if (clearForm === true && data.indexOf("danger") < 1) { /** If the clearForm true then alert the success/error msg */

                    // alert the msg
                    BMS.fn.notify($(data).text());

                    // Clear the form by loading the current page
                    getContent("./");


                } else if (undefined === $("#ajaxSubmitMsg").html()) {

                    $('<div style="margin-top: 20px; text-align: left;"' + data + '</div>').insertAfter(submitButton).delay(5000).fadeOut(function () {
                        $(this).remove();
                    });

                } else {

                    //$(data).insertAfter('#ajaxSubmitMsg');

                    // Comment out above and wirte below because it is not disappearing the last msg in bill pay in expense
                    $("#ajaxSubmitMsg").html(data);

                }


                /**
                 * Disable all input field until ajax request complete. 
                 */
                disableInput(that, false);

                /**
                 * Remove the loading icon from button
                 */
                $(submitButton).html(submitButtonText);

                /**
                 * Change the mouse icon to auto
                 */
                $("body").toggleClass("wait");

                /**
                 * Reload the dataTable after Insert, Update or delete records
                 */
                if ($('#dataTableWithAjax').length > 0) {
                    $('#dataTableWithAjax').DataTable().ajax.reload(null, false);
                }

                if ($('#dataTableWithAjaxExtend').length > 0) {
                    $('#dataTableWithAjaxExtend').DataTable().ajax.reload(null, false);
                }

            }

        }

    });

}


function sendBulkSMS(url) {

    var table = $('#dataTableWithAjaxExtend').DataTable();

    //var data = table.columns( 0 ).data();
    var rows = table.rows({ selected: true }).indexes();
    var data = table.cells(rows, 0).data();
    var number = [];

    $.each(data, function (key, num) {

        if (num != "" || num != null || num != 'NULL' || num != 0 || num != '0') {
            number.push(num);
        }

    });


    $("#modalDefault").modal('show').find('.modal-content').load(url + encodeURI(number.join()));

}

// Function to load dynamic page
function getContent(href, event = false) {

    if (typeof session !== 'undefined') {
        // terminate the call session when change the page
        session.terminate();
    }

    // If Ctrl+Click fired then open in new tab
    if (window.event && window.event.ctrlKey) {
        return;
    } else if (event) {
        event.preventDefault();
    }

    // Remove the modal backdrop
    $(".modal-backdrop.fade.in").remove();

    $.get(href + "?contentOnly=true", function (data, status) {

        // clear the value
        defaultiDisplayLength = '';

        // remove the all classes that are curently active
        $("li").removeClass("active");

        window.history.pushState(null, '', href);

        // Generate the page slug
        var pageSlug = href;

        // Generate the page title
        var pageTitle = "Not Found!";
        if (getPageTitle[pageSlug] !== undefined) {
            pageTitle = getPageTitle[pageSlug];
        }

        // Set the page title
        window.document.title = pageTitle;

        $(".dynamic-container").html(data);

        jQuery.get(full_website_address + '/assets/js/initiator.min.js');

    }).fail(function (data) {

        if (data.statusText === "Not Found") {

            $(".dynamic-container").html(`<div class="content-wrapper">
                                        <section class="content-header">
                                            <h1>404 Error Page</h1>
                                        </section>
                                        <section class="content">
                                            <div class="error-page">
                                                <h2 style="margin-top:0" class="headline text-yellow"> 404</h2>
                                                <div class="error-content">
                                                    <h3><i class="fa fa-warning text-yellow"></i> Oops! Page not found.</h3>
                                                        <p>We could not find the page you were looking for.</p>
                                                    </div>
                                            </div>
                                        </section>
                                    </div>`);

        } else {

            $(".dynamic-container").html(`<div class="content-wrapper">
                                        <section class="content-header">
                                            <h1>No Internet</h1>
                                        </section>
                                        <section class="content">
                                            <div class="error-page">
                                                <div class="error-content">
                                                    <h3><i class="fa fa-warning text-red"></i> No Internet</h3>
                                                        <p>Try:</p>
                                                        <ul>
                                                            <li>Checking the network cables, modem, and router</li>
                                                            <li>Reconnecting to Wi-Fi</li>
                                                            <li>Running Network Diagnostics</li>
                                                        </ul>
                                                    </div>
                                            </div>
                                        </section>
                                    </div>`);
        }



    });

}



/**
    Main author: https://stackoverflow.com/a/32839413
    Modified by: Khurshid Alam
*/
function combosVariations(options, optionIndex=0, results=[], current={}) {
    
    var allKeys = Object.keys(options);
    var optionKey = allKeys[optionIndex];

    var list = options[optionKey];

    list.forEach( item => {

        current[optionKey] = item;

        if (optionIndex + 1 < allKeys.length) {
            combosVariations(options, optionIndex + 1, results, current);
        } else {
            // The easiest way to clone an object.
            var res = JSON.parse(JSON.stringify(current));
            results.push(res);
        }

    });

    return results;

}

function generateVariation() {

    var att = [];

    var units = $(".units:checked");
    if(units.length > 0) {
        
        att["Units"]  = [];

        $.each(units, function(ukey, uval) {
            
            att["Units"].push($(uval).val())

        });

    }


    var editions = $(".editions:checked");
    if(editions.length > 0) {
        
        att["Editions"]  = [];

        $.each(editions, function(ekey, eVal) {
            
            att["Editions"].push($(eVal).val())

        });

    }


    var attribute = $(".attribute");
    $.each(attribute, function(key, val) {
        
        var attributeName = $(val).find(".attributeName").val();
        var attributeValue = $(val).find("attributeValue, input:checked");

        if(attributeValue.length > 0) {
            att[attributeName] = [];
        }

        $.each(attributeValue, function(vKey, variation) {

            att[attributeName].push($(variation).val());

        });

    });

    var attributeCount = Object.keys(att).length;

    if(attributeCount === 0) {

        alert("Please select at least one Attribute.");

    } else {

        // If there already have variation then confirm to add again.
        if($(".product-variation-list li").length > 0 && !confirm("There already have variations. Do you wish to generate again?") ) {
            return;
        }

        var variation = combosVariations(att);

        console.log(variation);

        var variationHTML = "";
        var timestamp = new Date().getTime();
        var productPurchasePrice = $("#productPurchasePrice").val();
        var productSalePrice = $("#productSalePrice").val();
        var productDistributorDiscount = $("#productDistributorDiscount").val();
        var productWholesalerDiscount = $("#productWholesalerDiscount").val();
        var productRetailerDiscount = $("#productRetailerDiscount").val();
        var productConsumerDiscount = $("#productConsumerDiscount").val();
        var disableInitialStock = $("#productHasExpiryDate").val() === "1" ? "disabled" : "";

        variation.forEach(items => {

            var variationList = "";
            var variationName = [];

            var productCode = timestamp;
            timestamp++;
            

            $.each(items, function(variation, value) {

                variationList += `<input type="hidden" name="product_variation[${variation}][]" value="${value}">`;
                
                variationName.push(value);

            });


            var variationId = variationName.join("");
            variationHTML += `<li style="overflow: auto;">
                                    <div data-toggle="collapse" data-parent=".product-variation-list" href="#variation_${variationId}" style="cursor: pointer;">
                                        <input type="radio" name="defaultVariation" value="${productCode}" class="defaultVariation stopPropagation" data-html="true" data-toggle="tooltip" data-placement="top" data-original-title="Mark this as <br/>default variation">
                                        <span class="handle">
                                            <i class="fa fa-ellipsis-v"></i>
                                            <i class="fa fa-ellipsis-v"></i>
                                        </span>
                                        <span class="text">${variationName.join(", ")}</span>
                                        <div class="tools">
                                            <i class="fa fa-edit"></i>
                                            <i class="fa fa-trash-o removeThisVariation"></i>
                                        </div>
                                    </div>
                                    <div id="variation_${variationId}" class="panel-collapse collapse">
                                        ${variationList}
                                        <div style="border-top: 1px solid #eee; margin-top: 15px; padding-bottom: 0px;" class="box-body">
                                            <div class="form-group required col-md-2">
                                                <label><?php echo __("Code:"); ?></label>
                                                <input type="text" name="productVariationCode[]" value="${productCode}" onclick="select()" class="productVariationCode form-control" required>
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label><?php echo __("Purchase Price"); ?></label>
                                                <input type="number" name="productVariationPurchasePrice[]" value="${productPurchasePrice}" class="form-control productVariationPurchasePrice" step="any">
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label><?php echo __("Sale Price"); ?></label>
                                                <input type="number" name="productVariationSalePrice[]" value="${productSalePrice}" class="form-control productVariationSalePrice" step="any">
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label><?php echo __("Distributor Discount:"); ?></label>
                                                <i data-toggle="tooltip" data-placement="bottom" title="<?php echo __("Percentage or Fixed amount."); ?>" class="fa fa-question-circle"></i>
                                                <input type="text" name="productDistributorVariationDiscount[]" value="${productDistributorDiscount}" placeholder="Eg: 10 or 20%" class="form-control">
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label><?php echo __("Wholesaler Discount:"); ?></label>
                                                <i data-toggle="tooltip" data-placement="bottom" title="<?php echo __("Percentage or Fixed amount."); ?>" class="fa fa-question-circle"></i>
                                                <input type="text" name="productWholesalerVariationDiscount[]" value="${productWholesalerDiscount}" placeholder="Eg: 10 or 20%"  class="form-control">
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label><?php echo __("Retailer Discount:"); ?></label>
                                                <i data-toggle="tooltip" data-placement="bottom" title="<?php echo __("Percentage or Fixed amount."); ?>" class="fa fa-question-circle"></i>
                                                <input type="text" name="productRetailerVariationDiscount[]" value="${productRetailerDiscount}" placeholder="Eg: 10 or 20%"  class="form-control">
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label><?php echo __("Consumer Discount:"); ?></label>
                                                <i data-toggle="tooltip" data-placement="bottom" title="<?php echo __("Percentage or Fixed amount."); ?>" class="fa fa-question-circle"></i>
                                                <input type="text" name="productConsumerVariationDiscount[]" value="${productConsumerDiscount}" placeholder="Eg: 10 or 20%"  class="form-control">
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label><?php echo __("Initial Stock"); ?></label>
                                                <i data-toggle="tooltip" data-placement="right" title="Opening or Initial stock of this variation" class="fa fa-question-circle"></i>
                                                <input ${disableInitialStock} type="number" name="productVariationIntitalStock[]" class="form-control productIntitalStock" step="any">
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label for="productVariationHasSubProduct"><?php echo __("Has sub product?"); ?></label>
                                                <i data-toggle="tooltip" data-placement="left" title="Select Yes if the variation has sub product." class="fa fa-question-circle"></i>
                                                <select name="productVariationHasSubProduct[]" id="productVariationHasSubProduct" class="form-control" required>
                                                    <option value="0">No</option>
                                                    <option value="1">Yes</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label><?php echo __("Weight:"); ?></label>
                                                <input type="text" name="productVariationWeight[]" class="form-control">
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label><?php echo __("Width:"); ?></label>
                                                <input type="text" name="productVariationWidth[]" class="form-control">
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label><?php echo __("Height:"); ?></label>
                                                <input type="text" name="productVariationHeight[]" class="form-control">
                                            </div>
                                            
                                            <div class="form-group col-md-6">
                                                <label><?php echo __("Description:"); ?></label>
                                                <textarea name="productVariationDescription[]" rows="3" class="form-control"></textarea>
                                            </div>
                                            <div class="imageContainer">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for=""><?php echo __("Variation Photo:"); ?></label>
                                                        <div class="input-group">
                                                            <span class="input-group-btn">
                                                                <span class="btn btn-default btn-file">
                                                                    <?php echo __("Select photo"); ?> <input type="file" name="productVariationPhoto[]" class="imageToUpload">
                                                                </span>
                                                            </span>
                                                            <input type="text" class="form-control imageNameShow" readonly>
                                                        </div>
                                                        <div style="margin-top: 8px;" class="photoErrorMessage"></div>
                                                    </div>
                                                </div>
                                                <div style="margin-bottom: 5px;" class="form-group col-md-3">
                                                    <div style="height: 120px; text-align: center;" class="image_preview">
                                                        <img style="margin: auto;" class="previewing" width="100%" height="auto" src="" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>`;

        });

        $(".product-variation-list").append(variationHTML);

    
    }

}


function addVariation() {

    var att = [];

    var units = $(".units:checked");
    if(units.length > 0) {
        
        att["Units"]  = [];

        $.each(units, function(ukey, uval) {
            
            att["Units"].push($(uval).val())

        });

    }

    var editions = $(".editions:checked");
    if(editions.length > 0) {
        
        att["Editions"]  = [];

        $.each(editions, function(ekey, eVal) {
            
            att["Editions"].push($(eVal).val())

        });

    }

    var attribute = $(".attribute");
    $.each(attribute, function(key, val) {
        
        var attributeName = $(val).find(".attributeName").val();
        var attributeValue = $(val).find("attributeValue, input:checked");

        if(attributeValue.length > 0) {
            att[attributeName] = [];
        }

        $.each(attributeValue, function(vKey, variation) {

            att[attributeName].push($(variation).val());

        });

    });

    var attributeCount = Object.keys(att).length;

    if(attributeCount === 0) {

        alert("Please select at least one Attribute or Unit.");

    } else {

        var variationSelect = "";
        for(let attribute in att) {
        
            variationSelect += `<div class="col-md-2 stopPropagation adjustFormGroupPadding"> 
                                    <select style="" name="product_variation[${attribute}][]" class="form-control" required>`;
                
                variationSelect += `<option value="">Select ${attribute}...</option>`;
                att[attribute].forEach(function(item, index) {
                    variationSelect += `<option value='${item}'>${item}</option>`;
                });

            variationSelect += `</select> </div>`;

        }

        var productCode = new Date().getTime();        
        var productPurchasePrice = $("#productPurchasePrice").val();
        var productSalePrice = $("#productSalePrice").val();
        var productDistributorDiscount = $("#productDistributorDiscount").val();
        var productWholesalerDiscount = $("#productWholesalerDiscount").val();
        var productRetailerDiscount = $("#productRetailerDiscount").val();
        var productConsumerDiscount = $("#productConsumerDiscount").val();
        var disableInitialStock = $("#productHasExpiryDate").val() === "1" ? "disabled" : "";

        var variationHTML = `<li>
                                    <div data-toggle="collapse" data-parent=".product-variation-list" href="#variation_${productCode}" style="cursor: pointer;">
                                        <input type="radio" name="defaultVariation" value="${productCode}" class="defaultVariation stopPropagation" data-html="true" data-toggle="tooltip" data-placement="top" data-original-title="Mark this as <br/>default variation">
                                        <span class="handle">
                                            <i class="fa fa-ellipsis-v"></i>
                                            <i class="fa fa-ellipsis-v"></i>
                                        </span>
                                        <span style="width: 90%; vertical-align: middle; margin-left: 20px;" class="text"> <div class="row">${variationSelect}</div> </span>
                                        <div class="tools">
                                            <i class="fa fa-edit"></i>
                                            <i class="fa fa-trash-o removeThisVariation"></i>
                                        </div>
                                    </div>
                                    <div id="variation_${productCode}" class="panel-collapse collapse in">
                                        <div style="border-top: 1px solid #eee; margin-top: 15px; padding-bottom: 0px;" class="box-body">
                                            <div class="form-group required col-md-2">
                                                <label><?php echo __("Code:"); ?></label>
                                                <input type="text" name="productVariationCode[]" value="${productCode}" onclick="select()" class="productVariationCode form-control" required>
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label><?php echo __("Purchase Price"); ?></label>
                                                <input type="number" name="productVariationPurchasePrice[]" value="${productPurchasePrice}" class="form-control productVariationPurchasePrice" step="any">
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label><?php echo __("Sale Price"); ?></label>
                                                <input type="number" name="productVariationSalePrice[]" value="${productSalePrice}" class="form-control productVariationSalePrice" step="any">
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label><?php echo __("Distributor Discount:"); ?></label>
                                                <i data-toggle="tooltip" data-placement="bottom" title="<?php echo __("Percentage or Fixed amount."); ?>" class="fa fa-question-circle"></i>
                                                <input type="text" name="productDistributorVariationDiscount[]" value="${productDistributorDiscount}" placeholder="Eg: 10 or 20%" class="form-control">
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label><?php echo __("Wholesaler Discount:"); ?></label>
                                                <i data-toggle="tooltip" data-placement="bottom" title="<?php echo __("Percentage or Fixed amount."); ?>" class="fa fa-question-circle"></i>
                                                <input type="text" name="productWholesalerVariationDiscount[]" value="${productWholesalerDiscount}" placeholder="Eg: 10 or 20%"  class="form-control">
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label><?php echo __("Retailer Discount:"); ?></label>
                                                <i data-toggle="tooltip" data-placement="bottom" title="<?php echo __("Percentage or Fixed amount."); ?>" class="fa fa-question-circle"></i>
                                                <input type="text" name="productRetailerVariationDiscount[]" value="${productRetailerDiscount}" placeholder="Eg: 10 or 20%"  class="form-control">
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label><?php echo __("Consumer Discount:"); ?></label>
                                                <i data-toggle="tooltip" data-placement="bottom" title="<?php echo __("Percentage or Fixed amount."); ?>" class="fa fa-question-circle"></i>
                                                <input type="text" name="productConsumerVariationDiscount[]" value="${productConsumerDiscount}" placeholder="Eg: 10 or 20%"  class="form-control">
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label><?php echo __("Initial Stock"); ?></label>
                                                <i data-toggle="tooltip" data-placement="right" title="Opening or Initial stock of this variation" class="fa fa-question-circle"></i>
                                                <input ${disableInitialStock} type="number" name="productVariationIntitalStock[]" class="form-control productIntitalStock" step="any">
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label for="productVariationHasSubProduct"><?php echo __("Has sub product?"); ?></label>
                                                <i data-toggle="tooltip" data-placement="left" title="Select Yes if the variation has sub product." class="fa fa-question-circle"></i>
                                                <select name="productVariationHasSubProduct[]" id="productVariationHasSubProduct" class="form-control" required>
                                                    <option value="0">No</option>
                                                    <option value="1">Yes</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label><?php echo __("Weight:"); ?></label>
                                                <input type="text" name="productVariationWeight[]" class="form-control">
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label><?php echo __("Width:"); ?></label>
                                                <input type="text" name="productVariationWidth[]" class="form-control">
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label><?php echo __("Height:"); ?></label>
                                                <input type="text" name="productVariationHeight[]" class="form-control">
                                            </div>
                                            
                                            <div class="form-group col-md-6">
                                                <label><?php echo __("Description:"); ?></label>
                                                <textarea name="productVariationDescription[]" rows="3" class="form-control"></textarea>
                                            </div>
                                            <div class="imageContainer">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for=""><?php echo __("Product Photo:"); ?></label>
                                                        <div class="input-group">
                                                            <span class="input-group-btn">
                                                                <span class="btn btn-default btn-file">
                                                                    <?php echo __("Select photo"); ?> <input type="file" name="productVariationPhoto[]" class="imageToUpload">
                                                                </span>
                                                            </span>
                                                            <input type="text" class="form-control imageNameShow" readonly>
                                                        </div>
                                                        <div style="margin-top: 8px;" class="photoErrorMessage"></div>
                                                    </div>
                                                </div>
                                                <div style="margin-bottom: 5px;" class="form-group col-md-3">
                                                    <div style="height: 120px; text-align: center;" class="image_preview">
                                                        <img style="margin: auto;" class="previewing" width="100%" height="auto" src="" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>`;

        $(".product-variation-list").append(variationHTML);
        

    }

}


function setVariationPrice() {

    var purchasePrice = $("#productVariationPurchasePriceSetter").val();
    var salePrice = $("#productVariationSelePriceSetter").val();
    var discount = $("#productVariationDiscountSetter").val();

    // Set prces for specific units
    var getWhereUnit = $("#SetPricesWhereUnit").val();
    if( getWhereUnit !== "" ) {
        
        $.each($(`input[name='product_variation[Units][]'][value='${getWhereUnit}'], select[name='product_variation[Units][]'] option:selected[value='${getWhereUnit}']`), function(key, val) {
            
            /** Set purchase price */
            if(purchasePrice !== "") {
                $(this).closest("li").find(".productVariationPurchasePrice").val(purchasePrice)
            }

            /** Set sale Price */
            if(salePrice !== "") {
                $(this).closest("li").find(".productVariationSalePrice").val(salePrice)
            }

            /** Set discount */
            if(discount !== "") {
                $(this).closest("li").find(".productVariationDiscount").val(discount)
            }

        });

    } else {

        /** Set purchase price */
        if(purchasePrice !== "") {
            $(".productVariationPurchasePrice").val(purchasePrice)
        }

        /** Set sale Price */
        if(salePrice !== "") {
            $(".productVariationSalePrice").val(salePrice)
        }

        /** Set discount */
        if(discount !== "") {
            $(".productVariationDiscount").val(discount)
        }

    }

}

/**
 * Function for show visual product
 * @param {*} param
 */
const showVisualProduct = ({
    category = "",
    brand = "",
    edition = "",
    generic = "",
    author = "",
    terms = "",
    sort = ""
} = {}) => {

    $.ajax({
        url: full_website_address + `/info/?module=data&page=productVisualList&catId=${category}&brand=${brand}&edition=${edition}&generic=${generic}&author=${author}&terms=${terms}&sort=${sort}`,
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        success: function(data, status) {


            if (data !== null) {

                var visualReport = "";
                data.forEach(product => {

                    var productPhoto = full_website_address;
                    if (product.v && product.v > 0) {
                        productPhoto += "/images/?for=products&id=" + product.id + "&q=YTozOntzOjI6Iml3IjtpOjIwMDtzOjI6ImloIjtpOjIyMDtzOjI6ImlxIjtpOjcwO30=" + "&v=" + product.v;
                    } else {
                        productPhoto += "/assets/images/noimage.png";
                    }

                    var bgColor = "bg-green";
                    if (parseInt(product.stock) <= parseInt(product.alert)) {
                        bgColor = "bg-red"
                    } else if (parseInt(product.stock) <= 3000) {
                        bgColor = "bg-orange"
                    }

                    visualReport += `<div class="col-md-4 col-sm-6 col-xs-12">
                                            <div class="small-box ${bgColor}">
                                                <div class="inner">
                                                    <p style="font-size: 20px; font-weight: 600">
                                                        ${product.name}
                                                    </p>
                                                    <p style="font-size: 24px; font-weight: 600; margin: 0;">
                                                        ${format_number(product.sold_qty)} ${ product.sold_qty !== product.total_sold_qty ? '('+ format_number(product.total_sold_qty) +')' : '' } Sold <br/>
                                                       ${product.stock} Stock
                                                    </p>
                                                </div>
                                                <div class="icon">
                                                    <img width='120' src='${productPhoto}' >
                                                </div>
                                                <a target="_blank" href="${full_website_address}/reports/product-report/?pid=${product.id}" class="small-box-footer"><?= __("View Report "); ?> <i class="fa fa-arrow-circle-right"></i></a>
                                            </div>
                                        </div>`;
                });

                $("#productVisualListContainer").html(visualReport);

            } else {

                $("#productVisualListContainer").html("<div class='alert alert-danger'>Sorry! no product found in this criteria.</div>");

            }

        }

    });

};
