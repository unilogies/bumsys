  // Active the current menu
  var getCurrentURL = window.location.href.split("?");
  $('ul.sidebar-menu a').each(function () {
      if ($(this).attr('href') == getCurrentURL[0]) {
          $(this).parents().css("display", "block");
          $(this).parents().addClass("active");
      }
  });


  // Get language
  var language = BMS.fn.getCookie("lang") === undefined ? {} : JSON.parse(localStorage.getItem("dtLang"));

  var isPosPage = typeof posPageUrl !== 'undefined' && posPageUrl ===  window.location.href;
  var isPurchasePage = typeof purchasePageUrl !== 'undefined' && purchasePageUrl === window.location.href;
  var isTransferStockPage = typeof transferStockPageUrl !== 'undefined' && transferStockPageUrl === window.location.href;
  var isProductReturnPage = typeof productReturnPageUrl !== 'undefined' && productReturnPageUrl === window.location.href;
  var isWastageSalePage = typeof wastageSalePageUrl !== 'undefined' && wastageSalePageUrl === window.location.href;
  var isSpecimenCopyPage = typeof specimenCopyPageUrl !== 'undefined' && specimenCopyPageUrl === window.location.href;
  var isScDistributionPage = typeof scDistributionPageUrl !== 'undefined' && scDistributionPageUrl === window.location.href;
  var isAddProductPage = typeof addProductPageUrl !== 'undefined' && addProductPageUrl === window.location.href;
  var isStockEntryPage = typeof stockEntryPageUrl !== 'undefined' && stockEntryPageUrl === window.location.href;
  var isSaleOrderPage = typeof saleOrderPageUrl !== 'undefined' && saleOrderPageUrl === window.location.href;

  // Select2 Normal

  $(function () {
    //Initialize Select2 Elements
    $('.select2').select2();
  });
  
  // End Select2

  $(function() {

    // Check if the DataTableAjaxPostUrl is defined
    try {
      if(!!DataTableAjaxPostUrl) {
      }
    } catch (error) {
      return;
    }
    
    var getDataTableNormal = $("#dataTableWithAjax").DataTable({
      "processing": true,
      "serverSide": true,
      "ajax": {
        url : DataTableAjaxPostUrl, // The URL is come from the table page.
        type: "post" // Method, By default get.
      },
      language: language,
      "columnDefs": [{
        "targets": 'no-sort', // targets variable come from the table page. 
        "orderable": false,
        "searchable": false
      }],
    });

  });
  // End DataTable with Ajax With Less Functionality

  /***** DataTable with Ajax With More Functionality ****/ 
  $(function() {

    if( $(".dataTableWithAjaxExtend").length > 0 ) {

        $(".dataTableWithAjaxExtend").each(function() {

            BMS.fn.dTable(this);
            
        });

    } else if( $("#dataTableWithAjaxExtend").length > 0 ) {
        
        BMS.fn.dTable("#dataTableWithAjaxExtend");

    }


  });
  // End DataTable with Ajax With More Functionality

