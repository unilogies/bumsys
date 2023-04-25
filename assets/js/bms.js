"use strict";

// Create the BMS Namespace if it doesn't exists
var BMS = BMS || {};
var updateSDPTimeout;

BMS.FUNCTIONS = {

    getDateTime: function() {
        var date = new Date();
        return date.getFullYear() + "-" + date.getMonth() + "-" + date.getDay() + "-" + date.getHours() + ":" + date.getMinutes() + ":" + date.getSeconds();
    },

    calculateDiscount: function(amount, discount=null) {

        if(discount === "" || discount === null || discount === 'null' || discount === 0) {
        
            return parseFloat(Number(amount));

        } else if( typeof discount === 'string' && discount.indexOf("%") > 0 ) {
        
            // For parcantage discount  
            return parseFloat(Number(amount) - (Number(discount.replace("%",""))/100) * Number(amount)); 

        } else {

            // For Fixed Discount
            return parseFloat(Number(amount) - Number(discount));

        }

    },

    calculateTarifCharges: function(amount, discount = null) {
        
        if(discount === "" || discount === null || discount === 'null' || discount === 0) {

            return 0.00;

        } else if (typeof discount === 'string' && discount.indexOf("%") > 0) {

            // For parcantage discount  
            return ((Number(discount.replace("%", "")) / 100) * Number(amount)).toFixed(2);

        } else {

            // For Fixed Discount
            return (Number(discount)).toFixed(2);

        }

    },

    datePicker: function({selector=".datePicker", format="YYYY-MM-DD", timePicker= false}="") {

        $(selector).daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            showDropdowns: true,
            timePicker: timePicker,
            timePicker24Hour: true,
            autoApply: true,
            parentEl: "div.dynamic-container",
            drops: "auto",
            locale:{
                format: format,
                cancelLabel: 'Clear'
            }
        });

        // Apply the date
        $(selector).on('apply.daterangepicker', function(ev, picker) {

            $(this).val(picker.startDate.format(format));

        });

        // clear the date
        $(selector).on('cancel.daterangepicker', function(ev, picker){
        
            $(this).val('');

        });

    },


    dateRangePicker: function({selector=".dateRangePicker", format="YYYY-MM-DD", timePicker= false}="") {

        $(selector).daterangepicker({
            autoUpdateInput: false,
            showDropdowns: true,
            drops: "auto",
            linkedCalendars: false,
            timePicker: timePicker,
            timePicker24Hour: true,
            parentEl: "div.dynamic-container",
            locale: {
                format: format,
                cancelLabel: 'Clear'
            }
        });
    
        // Apply the date
        $(selector).on('apply.daterangepicker', function(ev, picker) {

            $(this).val(picker.startDate.format(format) + ' - ' + picker.endDate.format(format));
    
        });
    
        // clear the date
        $(selector).on('cancel.daterangepicker', function(ev, picker){
        
            $(this).val('');
    
        });

    },

    dateRangePickerPreDefined: function({selector=".dateRangePickerPreDefined", format="YYYY-MM-DD", timePicker= false, ranges = {}}="") {

        $(selector).daterangepicker({
            autoUpdateInput: false,
            showDropdowns: true,
            drops: "auto",
            alwaysShowCalendars: true,
            linkedCalendars: false,
            timePicker: timePicker,
            timePicker24Hour: true,
            parentEl: "div.dynamic-container",
            locale: {
                format: format,
                cancelLabel: 'Clear'
            },
            ranges  : Object.keys(ranges).length > 0 ? ranges : {
                'Today'       : [moment(), moment()],
                'This Month'  : [moment().startOf('month'), moment().endOf('month')],
                'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                'This Year'  : [moment().startOf('year'), moment().endOf('year')],
                'Last Year'  : [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')],
                'All'  : [moment(0), moment().subtract('year').endOf('day')],
            }
            
        });
    
        // Apply the date
        $(selector).on('apply.daterangepicker', function(ev, picker) {

            $(this).val(picker.startDate.format(format) + ' - ' + picker.endDate.format(format));
    
        });
    
        // clear the date
        $(selector).on('cancel.daterangepicker', function(ev, picker){
        
            $(this).val('');
    
        });  

    },

    multiDatePicker: function({selector=".multiDatePicker", format="yyyy-mm-dd"}="") {

        $(selector).datepicker({
            multidate: true,
            multidateSeparator: ", ",
            format: format,
            todayHighlight: true
        });

    },

    getCookie: function(name) {

        var cookie = decodeURIComponent(`; ${document.cookie}`).split(`; ${name}=`);
        if(cookie.length > 1) return cookie[1].split("; ")[0];
        
    },

    copy: function(selector) {

        var element = $(selector);
        element.select();
        //element.setSelectionRange(0, 99999);

        navigator.clipboard.write([

            new ClipboardItem({
                'text/html': new Blob(
                    [
                        element.html()
                    ],
                    {
                        type: 'text/html'
                    }
                )
            })

        ]);
        
    },

    // javascript number translation function
    _n: function(number) {

        // Javascript language declaration
        let currentLang = BMS.fn.getCookie("lang");

        let langPack = {
            "bn_BD": "bn-BD"
        };
        
        // Check if the language is set
        if(currentLang !== undefined) {
            return new Intl.NumberFormat(langPack[currentLang]).format(number);    
        } else {
            return number;
        }

    },

    //** Create new item function when no item found on select2 search */
    createNewSelect2Item: function(selector) {

        var createNewUrl = $(selector).attr("select2-create-new-url");
        var searchValue = $(".select2-search__field").val();
        var searchValueEncode = encodeURIComponent(searchValue);

        console.log(selector);


        /** Hide the select2 container  */
        $('body').trigger('mousedown');

        /** Open Create New modal by given url */
        $("#modalDefault").modal('show').find('.modal-content').load(createNewUrl+"&val="+ searchValueEncode);
        
        /** Select the select2 option after create new */
        $('#modalDefault').on('hidden.bs.modal', function(e) {

            BMS.fn.select2(selector, "", searchValue);

        });

    },

    play: function(toon, loop=false) {

        var url = `${full_website_address}/assets/sounds/${toon}.mp3`;
        var audio = new window.Audio(url);

        $(document).on("pauseAudio", function() {
            audio.pause();
        });

        audio.loop = loop;
        audio.play();

    },

    pause: function() {
        $(document).trigger("pauseAudio");
    },


    startTimer(container="#timer") {

        var startTime = new Date().getTime();
        return setInterval(function() {

            var distance = new Date().getTime() - startTime;

            var totalSeconds = distance / 1000;

            var seconds = Math.floor( totalSeconds % 60 ).toString().padStart(2,0);

            var minutes = Math.floor( ( totalSeconds % 3600 ) / 60 ).toString().padStart(2,0);

            var hours = Math.floor( totalSeconds / 3600 ).toString().padStart(2,0);
            
            $(container).html(`${hours}:${minutes}:${seconds}`);

        }, 1000);


    },

    stopTimer: function(ele) {
        
        clearInterval(ele)

    },

    /**
     * 
     * @param {string} msg The msg we want to notific
     */
    notify: function(msg) {

        Swal.fire({
            toast: true,
            position: "top-right",
            timer: 5000,
            timerProgressBar: true,
            iconHtml: '<i class="fa fa-bell"></i>',
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
                this.play("beep");
            },
            title: `<span style="padding: 10px 0; font-size: 16px;"> ${msg} </span>`,
            showConfirmButton: false,
            onClose: $("body").css('padding', '0px') // This is because, while close the sweet alert a 15px padding-right is keep.

        });

    },

    desktopNotify: function(title, icon="", body="") {

        if(Notification.permission !== "granted") {
            
            Notification.requestPermission();

        } else {

            var notification = new Notification(title, {
                icon: icon,
                body: body
            });

            notification.onclick = function() {
                window.focus();
                this.close();
            }
        }

    },

    alertError: function(msg) {

        Swal.fire({
            toast: true,
            position: "top-right",
            icon: "error",
            timer: 5000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
                this.play("warning");
            },
            title:  `<span style="font-size: 16px;"> ${msg} </span>`,
            showConfirmButton: false,
            onClose: $("body").css('padding', '0px') // This is because, while close the sweet alert a 15px padding-right is keep.

        });

    },

    alertSuccess: function(msg, playSound=true) {

        Swal.fire({
            toast: true,
            position: "top-right",
            icon: "success",
            timer: 5000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
                playSound && this.play("warning");
            },
            title:  `<span style="font-size: 16px;"> ${msg} </span>`,
            showConfirmButton: false,
            onClose: $("body").css('padding', '0px') // This is because, while close the sweet alert a 15px padding-right is keep.

        });

    },

    dTable: function(selector) {


        // Check if the DataTableAjaxPostUrl is undefined
        var DataTableAjaxDataUrl = "";
        if(typeof $(selector).attr("dt-data-url") !== "undefined") {
            DataTableAjaxDataUrl = $(selector).attr("dt-data-url");
        } else {
            DataTableAjaxDataUrl = DataTableAjaxPostUrl;
        }

        // Disable on type  search
        var disableOnTypeSearch = true;
        if(typeof $(selector).attr("dt-disable-on-type-search") !== "undefined") {
            disableOnTypeSearch = false;
        }


        // Set the default ordering of datatable
        var DtDefaultOrder = ($(".defaultOrder").html() !== undefined) ? [ [ $('th.defaultOrder').index(),  'desc' ] ] : [1, 'desc'];
        var iDisplayLength = (typeof defaultiDisplayLength !== 'undefined') ? defaultiDisplayLength : 15;
        var dtHeight = (typeof $(selector).attr("dt-height") !== 'undefined') ? $(selector).attr("dt-height") : "60vh";

        var getDataTable = $(selector).DataTable({
            "processing": true,
            "serverSide": true,
            "responsive": true,
            "scrollX": true,
            "scrollY": dtHeight,
            "scrollCollapse": true,
            "stateSave": true,
            "search" : {
                return: true,
            },
            initComplete: function(settings, json) {

                if(disableOnTypeSearch) {

                    var api = new $.fn.dataTable.Api( settings );

                    $('.dataTables_filter input').unbind();
                    $('.dataTables_filter input').bind('keyup', function(e){
                        var code = e.keyCode || e.which;
                        if (code == 13) {
                            api.search(this.value).draw();
                        }
                    });

                }

            },
            "stateSaveParams": function(settings, data) {

                var api = this.api();
                
                // Disable search, order and length in stateSave
                data.search.search = "";
                data.length = 15;
                data.order = DtDefaultOrder;

                // Set the select text on filter
                data.columns.forEach( (item, i) => {

                    var footer = api.column(i).footer();

                    var filterObject = $(footer).find("select");

                    if( filterObject.length > 0 ) {
                        
                        // Add option text in the state
                        data.columns[i].search.text = $(filterObject).find(":selected").text();

                    }

                });

            },
            "order": DtDefaultOrder,
            // "searchCols": preFilter,
            "select": {
                "style" : "multi+shift",
                "selector": 'td:first-child'
            },
            language: Object.keys(language).length > 0 ? language : {
                "processing": "<i style='color: #3c8dbc;' class='fa fa-spinner fa-2x fa-spin fa-fw'></i><span style='margin-left: 10px;'>Processing...</span>",
            },
            "aLengthMenu": [  
                [15, 100, 500, 1500, 5000, -1],
                [15, 100, 500, 1500, 5000, "All"]
            ],
            "iDisplayLength": iDisplayLength,
            "ajax": {
                url : DataTableAjaxDataUrl, // The URL is come from the table page.
                type: "post" // Method, By default get.
            },
            "columnDefs": [
                {
                    "targets": 'countTotal',
                    "className": 'text-right',
                    render: $.fn.dataTable.render.number( ',', '.', 2)
                }, 
                {
                    "targets": 'highlightWithCountTotal',
                    "className": 'text-right highlight',
                    render: $.fn.dataTable.render.number( ',', '.', 2)
                }, 
                {
                    "targets": 'text-right',
                    "className": 'text-right'
                },
                {
                    "targets": 'sort',
                    "orderable": true,
                    "searchable": true
                },
                {
                    "targets": 'no-sort',
                    "orderable": false
                }, 
                {
                    "targets": 0,
                    "orderable": false,
                    "checkboxes": {
                        "selectRow": true
                    }
                },
                
                {
                    "targets": "px85",
                    "className": 'px85'
                },
                {
                    "targets": "px120",
                    "className": 'px120'
                },
                {
                    "targets": "px160",
                    "className": 'px160'
                },
                {
                    "targets": "px180",
                    "className": 'px180'
                },
                {
                    "targets": "px200",
                    "className": 'px200'
                },
                {
                    "targets": "px220",
                    "className": 'px220'
                },
                {
                    "targets": "px320",
                    "className": 'px320'
                },
                { 
                    "visible": false, 
                    "targets": 'hideit' 
                },
                {
                    "targets": "dtDescription",
                    "className": "dtDescription"
                },
                {
                    "targets": "highlight",
                    "className": "highlight"
                }

            ],

            // Total Sum by Column
            "footerCallback": function(row, data, start, end, display) {

                var api = this.api();

                api.columns('.countTotal, .highlightWithCountTotal', {
                page: 'current'
                }).every(function() {
                    var sum = this
                        .data()
                        .reduce(function(a, b) {
                        var x = parseFloat(a) || 0;
                        var y = parseFloat(b) || 0;
                        return x + y;
                        }, 0);
                    //console.log(BMS.fn._n(sum)); //alert(sum);
                    $(this.footer()).html(sum.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
                });

            }

        });


        // Get all states
        var loadedState = getDataTable.state.loaded();

        // Check if the state is exists for current page
        loadedState = loadedState !== null ? loadedState.columns : undefined;

        getDataTable.columns().every( function (index) {

            var that = this;

            var filterObject = $( 'select, textarea, input:not(.notThisValueFilter)', this.footer() );

            // Check if there is state and any of input, select or textarea is exist
            if( loadedState !== undefined && filterObject.length > 0 ) {

                var val = loadedState[index].search.search;
                var text = loadedState[index].search.text !== undefined ? loadedState[index].search.text : "";

                // if the element is html select
                if(filterObject[0].nodeName === "SELECT") {

                    /// remove the element if its exist 
                     $(filterObject).find("option[value='"+ val +"']").remove();

                    $(filterObject).append( $(`<option selected="selected" value="${val}">${text}</option>`) );


                } else {

                    $(filterObject).val( val );

                }
   
            }


            // filter on changes
            $( 'input, select, textarea', this.footer() ).on( 'enter change clear apply.daterangepicker cancel.daterangepicker', function () {

                /** For not search value.
                 * 
                 * Suppose we want to search all employee except past.
                 * So the mechanism is here
                 */
                var notSearch = $(this).closest("th").find(".notThisValueFilter");

                var search;
                if( notSearch.length > 0 && $(notSearch).val() === "!=" && this.value !== "") {

                    search = JSON.stringify({
                        "operator": "!=",
                        "search":this.value
                    });

                } else {
                    search = this.value;
                }

                if ( that.search() !== this.value ) {
                    that
                        .search( search )
                        .draw();
                }
                

            });


        });

        var today = new Date();
        var date = `${today.getDate()}-${today.getMonth()+1}-${today.getFullYear()} ${today.getHours()}:${today.getMinutes()}:${today.getSeconds()}`;

        if( $(".printButtonPosition").length > 0 ) {
            //begin export buttons
            new $.fn.dataTable.Buttons( getDataTable, {
                buttons: [
                    {
                        extend:    'print',
                        text:      '<i class="fa fa-print"></i> <?= __("Print"); ?>',
                        titleAttr: 'Print',
                        className: 'btn btn-default btn-sm',
                        messageTop: "<b>Printed On:</b> "+ date,
                        title: '',
                        footer: true,
                        autoPrint: true,
                        exportOptions: {
                            columns: ':visible:not(.no-print):not(.dt-checkboxes-cell)',
                            format : {
                                footer : function (data, column, row) {

                                    // If no print class exists then remove the whol html inside th
                                    return ($(row).prop('outerHTML').indexOf("no-print") > 0) ? 
                                        data.replace(data, "") :
                                        data;
                                }
                            }
                        },
                        messageTop: function() {
                            // If the dt exprot message define then append it
                            if($("#DtExportTopMessage").html() !== undefined) {
                                return $("#DtExportTopMessage").html();
                            } else {
                                return "<h2 class='text-center'>" + document.title + "</h2><br/> <p class='text-center'> <strong>Printed On: </strong> "+ date +" </p> <br/>";
                            }
                        }
                    },
                    {
                    extend:    'copy',
                    text:      '<i class="fa fa-files-o"></i> <?= __("Copy"); ?>',
                    titleAttr: 'Copy',
                    className: 'btn btn-default btn-sm',
                    footer: true,
                    exportOptions: {
                        columns: ':visible:not(.no-print):not(.dt-checkboxes-cell)',
                        format : {
                            footer : function (data, column, row) {

                                // If no print class exists then remove the whol html inside th
                                return ($(row).prop('outerHTML').indexOf("no-print") > 0) ? 
                                    data.replace(data, "") :
                                    data;
                            }
                        }
                        }
                    },

                    {
                        extend:    'excel',
                        text:      '<i class="fa fa-file-excel-o"></i> <?= __("Export to Excel"); ?>',
                        titleAttr: 'Excel',
                        className: 'btn btn-default btn-sm',
                        footer: true,
                        exportOptions: {
                        columns: ':visible:not(.no-print):not(.dt-checkboxes-cell)',
                        format : {
                            footer : function (data, column, row) {

                                // If no print class exists then remove the whol html inside th
                                return ($(row).prop('outerHTML').indexOf("no-print") > 0) ? 
                                    data.replace(data, "") :
                                    data;
                            }
                        }
                        },
                        messageTop: function() {
                        // If the dt exprot message define then append it
                        if($("#DtExportTopMessage").html() !== undefined) {
                            return $("#DtExportTopMessage").html();
                        }
                        }
                    },

                    {
                        extend:    'pdf',
                        text:      '<i class="fa fa-file-pdf-o"></i> <?= __("Export to PDF"); ?>',
                        titleAttr: 'PDF',
                        className: 'btn btn-default btn-sm',
                        exportOptions: {
                        columns: ':visible:not(.no-print):not(.dt-checkboxes-cell)',
                        format : {
                            footer : function (data, column, row) {

                                // If no print class exists then remove the whol html inside th
                                return ($(row).prop('outerHTML').indexOf("no-print") > 0) ? 
                                    data.replace(data, "") :
                                    data;
                            }
                        }
                        },
                        messageTop: function() {
                        // If the dt exprot message define then append it
                        if($("#DtExportTopMessage").html() !== undefined) {
                            return $("#DtExportTopMessage").html();
                        }
                        }
                    },

                    {
                        extend:    'colvis',
                        text:      '<i class="fa fa-columns"></i> <?= __("Hide"); ?>',
                        className: 'btn btn-default btn-sm',
                        collectionLayout: 'fixed'
                    }
                    
                ]

            });

            getDataTable.buttons().container().appendTo('.printButtonPosition');

        }

        function format(data) {
            

            // Get all column visibility states
            var columnVisibleState = getDataTable.columns().visible();
            var childRowHtml = ''; //'<div class="slider">';

            data.forEach((item) => {

                childRowHtml += '<tr class="childRow" style="display: none; background-color: #f2fcff;">';

                item.forEach((itemData, index) => {

                    var headerClassName = getDataTable.columns().header()[index].className;

                    // If the column is visible, then append the child column with row
                    childRowHtml += columnVisibleState[index] ? `<td class='${headerClassName}'>${itemData}</td>` : '';

                })

                childRowHtml += '</tr>';

            });


            return $(childRowHtml).toArray();

        }


        // Collapsable row
        // getDataTable
        $('#dataTableWithAjaxExtend tbody').on("click", ".has-child-row", function() {

            var tr =  $(this).closest("tr");
            var row = getDataTable.row(tr);
            var parentProductId = $(this).attr("data-parent-product-id");
            var wid = $("#productReportWarehouseSelection").select2('data')[0].id;

            var itemName = $(this).text();
            var that = this;

            // If shown, then hide
            if( row.child.isShown() ) {

                row.child.hide();
                tr.removeClass('shown');
                
            } else {

                // Add loading/ spiner with product/item name
                $(that).html(`${itemName}  <i style='margin-left: 5px;' class='fa fa-spinner fa-spin'></i>`);

                BMS.fn.get(`getChildProductData&pid=${parentProductId}&wid=${wid}`, function(data) {
                    
                    
                    if(data == 0) {

                        BMS.fn.alertError("Sorry! no item found.");

                    } else {

                        // Show the child row
                        row.child( format( data ) ).show();
                        tr.addClass('shown');

                        $('tr.childRow').fadeIn('slow');

                    }

                    // Remove the spinner after loading child items/ products
                    $(that).html(`${itemName}`);

                });
                
            }

            
        });

    },

    select2: function(selector, optionVal="", optionText="") {

        // Get the url
        var select2AjaxUrl = $(selector).attr("select2-ajax-url");
        var select2MinimumInputLength = $(selector).attr("select2-minimum-input-length");
        var tag = ($(selector).attr("select2-tag") === "true") ? true : false;
        var closeOnSelect = ($(selector).attr("closeOnSelect") === "false") ? false : true;
        var that = selector;
        
        /**
         * For creating create new button
         */
        var noResults = "";
        if ( $(selector).attr("select2-create-new-url") === undefined  ) {

            noResults = "<?php echo __('Nothing found')?>";

        } else {

            noResults = `<span class="createNewSelect2Item" onClick="BMS.fn.createNewSelect2Item('#${$(selector).attr("id")}')" style="cursor: pointer; display: block;"> <i class="fa fa-plus"></i> <?php echo __("Create new..."); ?></span>`;

        }


        // While enter open the new bath window
        $(document).on("keyup", ".select2-search__field", function(event) {
  
          if(event.key === "Enter") {
            $(".createNewSelect2Item").click();
          }
        
        });
        
        // Initialize Select Ajax Elements
        var select2 = $(selector).select2({
            placeholder: ($(selector).children('option:first').html()) ? $(selector).children('option:first').html() : "Select Options", // Get the first option as placeholder
            allowClear: true,
            closeOnSelect:closeOnSelect,
            tags: tag,
            minimumInputLength: select2MinimumInputLength,
            language: {
                noResults: function() {
                    return noResults;
                }
            },
            escapeMarkup: function(markup) {
                return markup;
            },
            ajax: {
                url: select2AjaxUrl,
                dataType: "json",
                delay: 400,
                processResults: function (data) {
                    return {
                        results: data
                    };
                },
                cache: true
            },
            templateResult: function(state) {
                
                if(!state.id) {
                    return state.text;
                }
            
                
                if (typeof state.text === "object" ) {

                    var results = state.text;
                    var resultsHtml = "";
                    resultsHtml +=` <div style="width: 98%;" class='row'>
                                                <div class='col-md-5'>${results[0]}</div>
                                                <div class='col-md-1'>Cost</div>
                                                <div class='col-md-1'>Price</div>
                                                <div class='col-md-2'>Stock</div>
                                                <div class='col-md-3 adjustFormGroupPaddingRight'>Brand</div>
                                            </div>
                                            `;
                    resultsHtml += ` <div style="width: 98%;" class='row'>
                                            <div class='col-md-5'>${results[1]}</div>
                                            <div class='col-md-1'>${results[2]}</div>
                                            <div class='col-md-1'>${results[3]}</div>
                                            <div class='col-md-2'>${results[4]}</div>
                                            <div class='col-md-3 adjustFormGroupPaddingRight'>${results[5]}</div>
                                        </div>
                                        `;

                    return resultsHtml;


                } else {

                    return state.text;

                }

            }

        })


        /**
         *****************************************
         * Set the default value for select2 ajax
         *****************************************
         * 
         * Check if the optionVal is not empty and
         * the previous selected value is not same with current
         */
        if( optionText !== "" && optionVal === "" ) {

            /**
             * If do not know the id/value of select2 option then,
             * search and select from database
             */
            $.ajax({
                type: 'get',
                url: select2AjaxUrl +"&q=" + optionText
            }).then(function(data) {

                var data = JSON.parse(data)[0];

                if(data !== undefined) {

                    var newOption = new Option(data.text, data.id, true, true);
                    select2.append(newOption).trigger('change');

                }

            });

        } else  if( optionVal !== "" && select2.find(':selected').val() !== optionVal ) {
            
            /**
             * If we know the id/value
             *  Set the default value */
            var newOption = new Option(optionText, optionVal, true, true);
            select2.append(newOption).trigger('change');

        }

    },

    get: function(target, returnData) {

        // Send ajax Request and return the data
        $.ajax({
            url: full_website_address + `/info/?module=data&page=${target}`,
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function (data, status) {
                returnData(data);
            },
            error: function() {
                returnData("");
            }
        });
    
    }

},

// create alias
BMS.fn = BMS.FUNCTIONS,

BMS.MAIN = {

    printPage: function(sURL, event, afterPrintOrCancel= function(){}) {
        
        event.preventDefault();

        // Remove previous iframe if there is any
        $(".dynamic-container > iframe").remove();

        var oHideFrame = document.createElement("iframe");
        oHideFrame.onload = function() {

            //**  this onafterprint event not working in iframe currently */

            // this.contentWindow.onbeforeunload = function() {
            //     $(".dynamic-container > iframe").remove();
            //     afterPrintOrCancel(true);
            // };
            // this.contentWindow.onafterprint = function() {
            //     $(".dynamic-container > iframe").remove();
            //     afterPrintOrCancel(true);
            // };

            this.contentWindow.focus(); // Required for IE
            this.contentWindow.print();

            afterPrintOrCancel(true);

        };
        oHideFrame.style.position = "fixed";
        oHideFrame.style.right = "0";
        oHideFrame.style.bottom = "0";
        oHideFrame.style.width = "0";
        oHideFrame.style.height = "0";
        oHideFrame.style.border = "0";
        oHideFrame.src = sURL;
        $(".dynamic-container").append(oHideFrame);

    },

  addTariffChargesRow: function(container) {

    $(container).append(
        `<div class="row"><br/> 
          <div class="col-md-7"> 
              <select name="tariffChargesName[]" class="form-control select2Ajax tariffChargesName" select2-ajax-url="${full_website_address}/info/?module=select2&page=tariffCharges"> 
              <option value="">Select Tariff/Charges</option> 
              </select> 
          </div> 
          <div class="col-md-4"> 
              <input type="number" name="tariffChargesAmount[]" class="form-control tariffChargesAmount" step="any"> 
          </div> 
          <div class="col-md-1"> 
              <i style="cursor: pointer; padding-top: 9px;" class="fa fa-trash-o removeThisTariffCharges"></i> 
          </div> 
        </div>`
    );

  }


},

BMS.PRODUCT = {

    /**
     * 
     * @param {int} product_id      The product to check
     * @param {string} whereToCheck Optional! The container where to check the product is exists  
     */
    isExists: function(product_id, whereToCheck=".productID") {

        return $(whereToCheck).filter(function() { return this.value === product_id; }).length > 0;

    },

    /**
     * 
     * @param {object} product The parent product details object
     * @param {object} location In which target the addProduct function will actiavted. Eg: BMS.POS.addProduct() or BMS.PURCHASE.addProduct()
     * @param {object} returnData The child product details object
     * @returns 
     */
    validationCheck: function(product, location, returnData) {

        /* Check if the product already in the list         
        if (this.isExists(product.pid) && !confirm(`The product (${product.pn}) is already in the list. Do you want to add it again?`)) {
            return;
        } */

        if (this.isExists(product.pid)) {
            return alert(`The product (${product.pn}) is already in the list.`)
        } 

        /** check if there has product variation */
        if( product.pv !== undefined ) {

            /** Generate product variation selection html */
            var variationsHtml = "";
            var variationList = {};
            var defaultVariation = []
            var productids = {};

            /**
             * mk = meta key
             * mv = meta value
             */
            $.each(product.pv, function(key, variation) {
                    
                if ( !Array.isArray(variationList[variation.mk]) ) {

                    variationList[variation.mk] = [variation.mv];

                } else if( !variationList[variation.mk].includes(variation.mv) ) {

                    variationList[variation.mk].push(variation.mv);

                }

                if( variation.t === "DV" ) {
                    
                    defaultVariation.push(variation.mv);

                } else {
                    
                    if ( !Array.isArray( productids[variation.id]) ) {

                        productids[variation.id] = [variation.mv];
    
                    } else {

                        productids[variation.id].push(variation.mv);
    
                    }

                }

            });


            $.each(variationList, function(attribute, variations) {

                variationsHtml += `<div id="variationSelection" class="form-group row text-left">
                                    <label class="col-sm-4">${attribute}:</label>
                                    <div class="col-sm-8">
                                        <select class="form-control">
                                            <option value="">Select ${attribute}...</option>`;
                                        
                                            $.each(variations, function(key, vName) {    
                                                variationsHtml += `<option `+ (defaultVariation.includes(vName) ? "selected" : "") +` value="${vName}">${vName}</option>`;
                                            });

                variationsHtml += `         </select>
                                    </div>
                                </div>`;

            });

            Swal.fire({
                title: '<strong>Select Variation</strong>',
                html: 'for '+ product.pn +' <br/><br/>'+variationsHtml,
                showCloseButton: true,
                showCancelButton: true,
                focusConfirm: false,
                confirmButtonText: 'Ok'
            }).then((result) => {

                // if confirmed then add the product
                if(result.isConfirmed) {
                    
                    // Get the select variation
                    var getAttribution = $("#variationSelection select");
                    var selectedVariation = [];
                    $.each(getAttribution, function(){

                        /**
                         * Check if the option is selected
                         * or if the selected value is not empty
                         */
                        if( this.value !== "" ) {

                            selectedVariation.push(this.value);

                        }

                    });


                    // Find the product id based on selected variation
                    var getPid = "";
                    $.each(productids, function(key, val) {

                        
                        if(selectedVariation.every( ai => val.includes(ai) ) ) {

                            getPid = key;
                            return false;
                            
                        }


                    });
                    
                    /** Check if the product id is not empty or selected variation is not empty */
                    if(getPid === "" || selectedVariation.length === 0 ) {

                        Swal.fire("Sorry! no product found");

                    } else {
                        
                        location.addProduct(getPid);

                    }
                    
                }

            });

        } else {

            // Return the product
            return returnData(product); 
            
        }

    },

    parseProductList: function( {category='', brand='', edition='', generic='', author=''}, returnData ) {

        // Parse product list and return
        $.ajax({
            url: full_website_address + `/info/?module=data&page=productList&catId=${category}&brand=${brand}&edition=${edition}&generic=${generic}&author=${author}`,
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function (data, status) {
                returnData(data);
            }
        });

    },

    showProduct: function({container='#productListContainer', category='', brand='', edition='', generic='', author=''}='' ) {

        this.parseProductList({
            category:category, 
            brand:brand, 
            edition:edition,
            generic:generic,
            author:author
        }, productData => {
        
            // if no product found then display the error msg
            if(productData == null) {
                $(container).html("<div class='alert alert-danger'>Sorry! No products found in this criteria.</div>");
                return;
            }

            var productHtml = "";
            productData.forEach(product => {
                
                var productPhoto = full_website_address;
                if( product.v && product.v > 0 ) {
                    productPhoto += "/images/?for=products&id="+ product.id +"&q=YTozOntzOjI6Iml3IjtpOjIwMDtzOjI6ImloIjtpOjIyMDtzOjI6ImlxIjtpOjcwO30="+"&v="+ product.v;
                } else {
                    productPhoto += "/assets/images/noimage.png";
                }

                productHtml += "<button type='button' value='"+ product.id +"' title='"+ product.name +"' data-toggle='tooltip' class='productButton btn-product btn-default'> \
                                <img src='"+ productPhoto +"' style='width:60px; height:60px;' alt='"+ product.name +"'> \
                                <span> "+ product.name +" </span> \
                                </button> \
                                ";
            });

            // Now all the products display in the products container
            $(container).html(productHtml);

        });

    },

    getDetails: function(product_id, returnData) {

        // Parse product list and return
        $.ajax({ 
            url: full_website_address + `/info/?module=data&page=productDetails&product_id=${product_id}`,
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function (data, status) {
                returnData(data);
            }
        });

    },

    addProduct: function(product_id="", isScanner=false) {
      
        var productId =  (product_id === "") ? $('#selectProduct').val() : product_id;
      
        if (productId === "") {
            alert("Please select a product");
            return;
        }

        // Get product details
        this.getDetails(productId, products => {

            products.forEach(eachProduct=> {

                BMS.PRODUCT.validationCheck(eachProduct, this, product => { 

                    /** Ignore batched/expiry product to adding in bundle/sub product */
                    if(product.hed === "1") {
                        BMS.fn.alertError("Sorry! The product which have expiry date or batch number, can not be added in sub/bundle product.");
                        return;
                    }

                    var itemQnt = product.iq ? parseFloat(product.iq).toFixed(0) : 1;

                    var html = `<tr>
                                <input type="hidden" name="bgProductID[]" class="productID" value="${product.pid}">
                                <td class="col-md-5">${product.pn}</td>
                                <td class="col-md-3"><input onclick = "this.select()" type="text" name="bgProductQnt[]" value="${itemQnt}" class="productQnt form-control text-center"></td>
                                <td class="col-md-2">${product.pu}</td>
                                <td class="text-right col-md-2 gbProductPrice"><input onclick = "this.select()" type="text" name="bgProductSalePrice[]" value="${product.sp}" class="productSalePrice form-control text-center" step="any"></td>
                                <td style="width: 28px !important;">
                                    <i style="cursor: pointer;" class="fa fa-trash-o removeThisProduct"></i>
                                </td>
                            </tr>`;

                    $("#productTable > tbody").append(html);

                });
                
                

                // This will not required. Becase we are removing the group product
                /*if( $("#productType").val() === "Grouped") {

                    // Hide price on grouped product
                    $(".gbProductPrice").hide();

                }*/

                /* remove the value from selectProduct select box */
                $('#selectProduct').empty();

            });

            // Select Quantity input field if not barcose scanner
            if(isScanner === false) {
                $(`#productTable > tbody tr:last`).find(".productQnt").select();
            }

        });

    },

    getListByGeneric: function(genericName) {

        /** 
         * Get Product list by
         * initializing the select2 with default option 
         * 
         * Arguments: Selector, option value, Option text
         * 
         */
        BMS.fn.select2("#productGenericFilter", genericName, genericName);

    },

    productUnitCheck: function(selector) {

        this.getDetails({
            product_id: $(selector).closest("tr").find(".productID").val(),
            unit: $(selector).val()
        }, products => {

            // Change the sale price on unit change
            $(selector).closest("tr").find(".productSalePrice").val(products[0]["sp"]);

        });

    }

};

BMS.POS = {

  clearScreen: function() {

    $(".dynamic-container").slideUp(350, function() {

        // Set today date if it change
        var date = new Date();
        $("#salesDate").val( date.toISOString().split('T')[0] );

        // Remove tariff and Charges except first one
        $("#tariffCharges .row").not('div:first').remove();

        // Remove select2 tarrif and charges value
        $(".tariffChargesName").val('').change();

        // Remove Discount
        //$("#orderDiscountValue").val("0");
        
        // Remove all payment options and add new empty one
        var paymentItemRow = `<div style="margin: 0;" class="row">
            <div class="form-group pull-right col-md-3 adjustFormGroupPadding">
                <label>Amount</label>
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

    $(".paymentMethodBox").html(paymentItemRow);

        // remove order discount, paid amount and sales note value
        $("#orderDiscountValue, #shippingCharge, .posSalePaymentAmount, #adjustAmount, #salesNote").val("");

        // Remove shippingChargeEdited from #shippingCharge
        $("#shippingCharge").removeClass("shippingChargeEdited");

        // Remove disablePaymentAmountAutoChange class From posSalePaymentAmount 
        $(".posSalePaymentAmount").removeClass("disablePaymentAmountAutoChange");

        // Remove all product items
        $("#productTable > tbody").html("");

        // If the sale is edit then remove the salesId hidden input field and
        // Add Hold & Confirm button in finalize sale modal
        $("#editSalesId").remove();
        $("#payment .modal-footer").html(`
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?= __("Close"); ?></button>
            <button name="posAction" value="sale_is_confirmed" style="visibility: hidden;" type="submit"></button>
            <button name="posAction" value="sale_is_hold" type="submit" class="posSubmit btn btn-warning"><i class="fa fa-pause-circle"></i> <?= __("Hold"); ?></button>
            <button name="posAction" value="sale_is_confirmed" type="submit" class="posSubmit btn btn-success"><i class="fa fa-check-circle"></i> <?= __("Confirm"); ?></button>
        `);

        // Uncheck checked elements
        $("input:radio, input:checkbox").prop("checked", false);

        // Initialize select2 set walk in customer and open the select2
        BMS.fn.select2("#customers", 1, "Walk-in Customer");
        // This features will use later
        //$("#customers").select2('open');
    

        // Count the Total Quantity
        BMS.POS.grandTotal();
                    
        // Disable the customer Selection and Warehouse selection if there have any product in the list
        BMS.POS.disableEnableWCSelect();

    }).slideDown(500);

  },

  getProductDetails: function( {product_id, warehouse_id, customer_id="", qnt='', batch='', packet=''}, returnData ) {


    // Parse product list and return
    $.ajax({ 
        url: full_website_address + `/info/?module=data&page=productDetailsForPos&product_id=${product_id}&warehouse_id=${warehouse_id}&cid=${customer_id}&pqnt=${qnt}&batch=${batch}&packet=${packet}`,
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        success: function (data, status) {
            returnData(data);
        }
    });

  },

  disableEnableWCSelect: function() {
    // WC = warehouse and Customer
    if( $(".productQnt").length > 0) {
      $("#customers, #warehouse").prop("disabled", true);
    } else {
      $("#customers, #warehouse").prop("disabled", false);
    }
    
  },

  grandTotal: function(event="") {
    
    var productQuantity = 0;
    var productItem = 0;
    $(".productQnt").each(function() {
      productQuantity += Number($(this).val());
      productItem += 1;
    });

    // Display total product quantity and items
    $(".totalItemAndQnt").html(productItem + ' (' + productQuantity + ')');
    
    // Count Total Amount
    var totalAmount = 0;
    $(".subtotalCol").each( function() {
      totalAmount +=  Number($(this).html());
    });

    // Display Total amount 
    $(".totalAmount").html(totalAmount.toFixed(2));

    
    // Calculate the Discount
    var orderDiscount = $("#orderDiscountValue").val();

    var amountAfterDiscount = BMS.FUNCTIONS.calculateDiscount(totalAmount, orderDiscount);

    // Display the discount amount in the order discount field
    $(".totalOrderDiscountAmount").html("(-) " + (totalAmount - amountAfterDiscount).toFixed(2));

    // Calculate tarif and Charges
    var totaltarifChargesAmount = 0;
    $("#tariffCharges > .row").each(function() {

        var tarifChargesValue = $(this).closest(".row").find(".tariffChargesName").val().split(": ")[1];
        var tarifChargesAmount = BMS.FUNCTIONS.calculateTarifCharges(amountAfterDiscount, tarifChargesValue);

        $(this).closest(".row").find(".tariffChargesAmount").val(tarifChargesAmount);
        totaltarifChargesAmount += Number(tarifChargesAmount);

    });

    // Display the Tariff and Charges amount
    $(".totalTariffChargesAmount").html("(+) " + (totaltarifChargesAmount).toFixed(2));
    
    // check if the Finalize Sale window is open
    if($("#payment").hasClass("in") === true) {
      
      // Calculate Shipping and display Shipping Charge if shipping input box is not focused
      // and totalPackets or packetShipingRate is focused.
      if( $("#shippingCharge").is(":focus") === false && ( $("#totalPackets").is(":focus") || $("#packetShippingRate").is(":focus")  ) ) {

        var calculatShipping = $("#totalPackets").val() * $("#packetShippingRate").val();
        $("#shippingCharge").val(calculatShipping);

      }

    } else {

      // Count Total Packet
      var totalPacket = 0;
      $(".productPacket").each( function() {
        totalPacket +=  Number($(this).val());
      })

      // Display Packet 
      $(".displayTotalPackets").html( (totalPacket).toFixed(2) );
      $("#totalPackets").val( Math.round(totalPacket) );

      // Calculate Shipping and display
      var calculatShipping = $("#totalPackets").val() * $("#packetShippingRate").val();
      
      // Display Shipping Charge
      $("#shippingCharge").val(calculatShipping);

    }

    // Display the Net total
    var calculateNetTotal = (Number(totaltarifChargesAmount) + Number(amountAfterDiscount)).toFixed(2);
    $(".netTotalAmount").html(calculateNetTotal);
    $("#finalizeSale > tbody > tr:nth-child(1) > td:nth-child(3)").html( calculateNetTotal );

    // Get shipping
    var shippingCharge = Number($("#shippingCharge").val());

    // Calculate the grand total
    var grandTotal = Number(calculateNetTotal) + shippingCharge;

    // Automatic adjust amount
    if( config.posSaleAutoAdjustAmount === "1" ) {

        $("#adjustAmount:not(.disableAdjustAmountAutoChange)").val( parseFloat( parseFloat(grandTotal).toFixed() - grandTotal ).toFixed(2) );
        
    }

    var adjustAmount = Number($("#adjustAmount").val());

    // Calculate the grand total by minusing/suming adjust amount
    var grandTotal = parseFloat( Number(calculateNetTotal) + shippingCharge + Number(adjustAmount) ).toFixed(2);

    // Add payable amount in paid amount box if the option posSaleAutoMarkAsPaid is enabled
    if( config.posSaleAutoMarkAsPaid === "1" ) {

        // Calculate entered payment amount
        var enteredPayment = sumInputs(".disablePaymentAmountAutoChange");

        // calculating payable amount
        var payableAmount = 0;
        if( grandTotal > 0 && grandTotal > enteredPayment ) {

            // For positive grand total
            payableAmount = grandTotal - enteredPayment;

        } else if( grandTotal < 0 && grandTotal < enteredPayment ) {

            // For negative grand total/return purpose
            payableAmount = grandTotal - enteredPayment;

        }

        $(".posSalePaymentAmount:not(.disablePaymentAmountAutoChange)").first().val( parseFloat(payableAmount).toFixed(2) );

    }

    
    var paidAmount = sumInputs(".posSalePaymentAmount");
    var amountChange = (grandTotal < paidAmount) ? (paidAmount - grandTotal) : 0;
    var amountDue = (grandTotal > paidAmount) ? (grandTotal - paidAmount) : 0;
  
    // Display the Grand Total, Due amount and change amount
    $("#finalizeSale > tbody > tr:nth-child(4) > td:nth-child(2)").html( grandTotal );
    $("#finalizeSale > tbody > tr:nth-child(6) > td:nth-child(2)").html( amountChange );
    $("#finalizeSale > tbody > tr:nth-child(7) > td:nth-child(2)").html( amountDue );
    
    // Display Quick Cash
    $("#quickPayableAmount").html( grandTotal );

    //console.log(grandTotal);
    //console.log(paidAmount);

    // If paid amount is more then grand total and payment modal is opened
    if( $("#payment").hasClass("in") === true && ( (grandTotal > 0 && grandTotal < paidAmount) || (grandTotal < 0 && grandTotal > paidAmount) ) ) {

        Swal.fire({
            title: "Paid amount can not be more then Grand total.",
            icon: "error"
        });

        // Display the payable amount
        $(".posSalePaymentAmount").last().val(0);

    }


  },



  editProductItemDetails: function(rowId, product_name) {

    // Display the product name on modal
    $("#productSaleDetails .modal-title").html(product_name);
    $("#productSaleDetails .rowId").val(rowId);

    // select product details row
    var product_row = $(`#${rowId}`);

    $("#productSaleDetails #productSaleItemPrice").val( product_row.find(".netSalesPrice").val() );
    $("#productSaleDetails #productSaleItemDiscount").val( product_row.find(".productDiscount").val() );
    $("#productSaleDetails #productSaleItemPacket").val( product_row.find(".productPacket").val() );
    $("#productSaleDetails #productSaleItemDetails").val( product_row.find(".productItemDetails").val() );

  },


  addProduct: function(product_id="", isScanner=false) {

    var productId =  (product_id === "") ? $('#selectProduct').val() : product_id;

    if (productId === "") {
        alert("Please select a product");
        return;
    }

    // Remove/ empty the product selection input.
    if($('#selectProduct').val() > 0) {
      $("#selectProduct").html("<option value=''>Search Product....</option>");
    }

    // Get product details
    this.getProductDetails({
      product_id: productId,
      warehouse_id: $("#warehouseId").val(),
      customer_id: $("#customersId").val()
    }, products => {

      if(products["error"] !== undefined && products["error"] === true) {
        
        // Display the error message
        Swal.fire({
          title: "Error!",
          text: products["msg"],
          icon: "error"
        });

        return; 
      }

        // Loop throw Products
        products.forEach(eachProduct => {

            BMS.PRODUCT.validationCheck(eachProduct, this, product => {

                var packet = 0;
                var itemQnt = product.iq ? parseFloat(product.iq).toFixed(0) : 1;
                if(product.pq > 0) {
                    packet = (itemQnt / Number(product.pq)).toFixed(2);
                }

                // Check Discount
                var productDiscount = !product.pd ? "0" : product.pd;

                // Generate Discount
                var amountAfterDiscount = BMS.FUNCTIONS.calculateDiscount(product.sp, productDiscount);
                var displayProductPrice = "";
                if( Number(amountAfterDiscount) === Number(product.sp) ) {
                    displayProductPrice = parseFloat(product.sp).toFixed(2);
                } else {
                    displayProductPrice = "<span>" + parseFloat(amountAfterDiscount).toFixed(2) + "</span><span><del><small>"+ parseFloat(product.sp).toFixed(2) +"</small></del></span>";
                }                      

                var rowId = Date.now()+product.pid;
                var generic = product.gn === null ? "" : `<small style="cursor: zoom-in;" onClick="BMS.PRODUCT.getListByGeneric('${product.gn}')"><i>${product.gn}</i></small>`;

                var html = `<tr ${ product.so ? 'style="background-color: pink;"' : '' } id=${rowId}> 
                                <td class="col-md-7">
                                    <span data-toggle="modal" data-target="#modalDefault" href="${full_website_address}/xhr/?module=reports&page=totalPurchasedQuantityOfThisCustomer&cid=${$("#customersId").val()}&pid=${productId}"> <i class="fa fa-info-circle productDescription"></i> </span> 
                                    <a href="#" data-toggle="modal" onclick="BMS.POS.editProductItemDetails('${rowId}', '${product.pn}')" data-target="#productSaleDetails">${product.pn}</a>
                                    ${generic}
                                </td> 
                                <td class="col-md-2 displayProductPrice text-right">${displayProductPrice}</td> 
                                <td class="col-md-2"><input onclick = "this.select()" type="text" name="productQnt[]" value="${itemQnt}" class="productQnt form-control text-center" autoComplete="off"></td>
                                <td class="col-md-2">${product.pu}</td> 
                                <td class="col-md-3 subtotalCol text-right">${ parseFloat(Number(amountAfterDiscount)* Number(itemQnt)).toFixed(2) }</td> 
                                <td style="width: 25px !important;"> 
                                    <i style="cursor: pointer;" class="fa fa-trash-o removeThisProduct"></i> 
                                </td> 
                                <input type="hidden" name="productID[]" class="productID" value="${product.pid}"> 
                                <input type="hidden" class="netSalesPrice" name="productSalePirce[]" value="${product.sp}"> 
                                <input type="hidden" class="productMainSalePirce" name="productMainSalePirce[]" value="${product.sp}"> 
                                <input type="hidden" class="productSO" value="${product.so}"> 
                                <input type="hidden" name="productDiscount[]" value="${productDiscount}" class="productDiscount" autoComplete="off"> 
                                <input type="hidden" name="productBatch[]" value="">
                                <input type="hidden" name="productHasExpiryDate[]" value="${product.hed}"> 
                                <input type="hidden" name="productPacket[]" value="${packet}" class="productPacket"> 
                                <input type="hidden" name="productItemDetails[]" class="productItemDetails" value=""> 
                            </tr>`;

                $("#productTable > tbody").append(html);

            });

        });


        // Select Quantity input field if not barcose scanner
        if(isScanner === false) {
            $(`#productTable > tbody tr:last`).find(".productQnt").select();
        }
        
        // Count the Total Quantity
        this.grandTotal();
        
        // Disable the customer Selection and Warehouse selection if there have any product in the list
        this.disableEnableWCSelect();

    });

  },

  addReturnProduct: function (
      pid, 
      product_name,
      product_generic,
      batch_id,
      product_unit,
      hed,
      discount,
      item_price,
      item_qty,
      item_subtotal
    ) {

    /* Check if the product already in the list */        
    if (BMS.PRODUCT.isExists(pid) && !confirm(`The product (${product_name}) is already in the list. Do you want to add it again?`)) {
        return;
    }

    var rowId = Date.now()+pid;
    var generic = product_generic === null ? "" : `<small style="cursor: zoom-in;" onClick="BMS.PRODUCT.getListByGeneric('${product_generic}')"><i>${product_generic}</i></small>`;

    var html = `<tr id=${rowId}> 
                    <td class="col-md-7">
                        <span> <i class="fa fa-undo productDescription"></i> </span> 
                        <a href="#" data-toggle="modal" onclick="BMS.POS.editProductItemDetails(${rowId}, '${product_name}')" data-target="#productSaleDetails">${product_name}</a>
                        ${generic}
                    </td> 
                    <td class="col-md-2 displayProductPrice text-right">${item_price}</td> 
                    <td class="col-md-2"><input onclick = "this.select()" type="text" name="productQnt[]" value="-${item_qty}" class="productQnt form-control text-center" autoComplete="off"></td>
                    <td class="col-md-2">${product_unit}</td> 
                    <td class="col-md-3 subtotalCol text-right">-${ item_subtotal }</td> 
                    <td style="width: 25px; !important"> 
                        <i style="cursor: pointer;" class="fa fa-trash-o removeThisProduct"></i> 
                    </td> 
                    <input type="hidden" name="productID[]" class="productID" value="${pid}"> 
                    <input type="hidden" class="netSalesPrice" name="productSalePirce[]" value="${item_price}"> 
                    <input type="hidden" class="productMainSalePirce" name="productMainSalePirce[]" value="${item_price}"> 
                    <input type="hidden" name="productDiscount[]" value="${discount}" class="productDiscount" autoComplete="off"> 
                    <input type="hidden" name="productBatch[]" value="${batch_id}">
                    <input type="hidden" name="productHasExpiryDate[]" value="${hed}"> 
                    <input type="hidden" name="productPacket[]" value="0" class="productPacket"> 
                    <input type="hidden" name="productItemDetails[]" class="productItemDetails" value=""> 
                </tr>`;

    $("#productTable > tbody").append(html);

    // Count the Total Quantity
    this.grandTotal();

  },
          
  /**
    * This is not required. Now commenting. Will delete in near version
    * 
  productUnitCheck: function(selector) {

    var Quantity = $(selector).closest("tr").find(".productQnt").val();
    var Discount = $(selector).closest("tr").find(".productDiscount").val();
    var SubtotalRow = $(selector).closest("tr").find("td.subtotalCol");

    var that = selector;

    this.getProductDetails({
      product_id: $(selector).closest("tr").find(".productID").val(),
      warehouse_id: $("#warehouseId").val(),
      qnt: Quantity,
      unit: $(selector).val()
    }, product => {


      if(product["error"] !== undefined && product["error"] === true) {

        $(that).closest("tr").find(".productQnt").val(product.stq); // Set the product quantity with Stock Quantity
        Quantity = parseFloat(product.stq).toFixed(0);

        // Display the error message
        Swal.fire({
          title: "Error!",
          text: product["msg"],
          icon: "error"
        });

      }

      // Check if product[0] isset, if not the use direct product
      // Because If there is any error then the product details come with the error
      var product = product[0] ? product[0] : product;

      // Change the product Price
      var amountAfterDiscount = BMS.FUNCTIONS.calculateDiscount(product.sp, Discount);
      var displayProductPrice = "";
      if( Number(amountAfterDiscount) === Number(product.sp) ) {
        var displayProductPrice = parseFloat(product.sp).toFixed(2);
      } else {
        var displayProductPrice = "<span>" + amountAfterDiscount + "</span><span><del><small>"+ parseFloat(product.sp).toFixed(2) +"</small></del></span>";
      }
      
      $(that).closest("tr").find(".netSalesPrice").val(product.sp);
      $(that).closest("tr").find(".productMainSalePirce").val(product.sp);
      $(that).closest("tr").find(".displayProductPrice").html(displayProductPrice);
      $(SubtotalRow).html( (Quantity * amountAfterDiscount).toFixed(2) );

      // Calculate all data
      this.grandTotal();

    })
   
  },

  */

  productQntCheck: function(selector, event) {

    // Count Sub total for each product. 
    var netSalesPrice = $(selector).closest("tr").find(".netSalesPrice").val();
    var Quantity = $(selector).val();
    var Discount = $(selector).closest("tr").find(".productDiscount").val();
    var SubtotalRow = $(selector).closest("tr").find("td.subtotalCol");
    var packetSelector = $(selector).closest("tr").find(".productPacket");
    var that = selector;

    // If the product quantity less then 1 then throw an error
    if(Quantity == 0) {

      // Display the error message
      Swal.fire({
        title: "Error!",
        text: "Product qunatity can not be zero (0)",
        icon: "error"
      });

      // Set product quantity 1
      $(selector).val(1);
      Quantity = 1;

    }

    this.getProductDetails({
      product_id: $(selector).closest("tr").find(".productID").val(),
      warehouse_id: $("#warehouseId").val(),
      qnt: Quantity,
      unit: $(selector).closest("tr").find(".productItemUnit").val()
    }, product => {

      if(product["error"] !== undefined && product["error"] === true) {

        $(that).val(product.stq); // Set the product quantity with Stock Quantity
        Quantity = product.stq;

        // Display the error message
        Swal.fire({
            title: "Error!",
            text: product["msg"],
            icon: "error"
        });

      }

      // Check if product[0] isset, if not the use direct product
      // Because If there is any error then the product details come with the error
      var product = product[0] ? product[0] : product;

      // change product background color while changing qty and when out of stock
      if(product.so) {

        $(selector).closest("tr").css("background-color", "pink");

      } else {

        $(selector).closest("tr").css("background-color", "white");

      }

      // Display Subtotal for each product
      $(SubtotalRow).html((BMS.FUNCTIONS.calculateDiscount(netSalesPrice,Discount) * Quantity).toFixed(2));

      // Update the packet
      var packet = 0;
      if(Number(product.pq) > 0) {
        packet = (Quantity / Number(product.pq)).toFixed(2);
      }
      packetSelector.val( packet );

      // Call the grand total
      this.grandTotal();
    
    });

  },

  productPacketCheck: function() {

    // Count Sub total for each product. 
    var netSalesPrice = $(selector).closest("tr").find(".netSalesPrice").val();
    var Packet = $(selector).val();
    var Discount = $(selector).closest("tr").find(".productDiscount").val();
    var SubtotalRow = $(selector).closest("tr").find("td.subtotalCol");
    var quantitySelector = $(selector).closest("tr").find(".productQnt");
    var packetSelector = $(selector).closest("tr").find(".productPacket");

    this.getProductDetails({
      product_id: $(selector).closest("tr").find(".productID").val(),
      warehouse_id: $("#warehouseId").val(),
      packet: Packet
    }, product => {

      if(product["error"] !== undefined && product["error"] === true) {

        $(quantitySelector).val(product["having_item_quantity"]); // Set the product quantity with max

        // Display Subtotal for each product
        $(SubtotalRow).html((BMS.FUNCTIONS.calculateDiscount(netSalesPrice,Discount) * product["having_item_quantity"] ).toFixed(2));

        // Update the packet
        var packet = 0;
        if(Number(product.product_packet_quantity) > 0) {
          packet = ( product["having_item_quantity"] / Number(product.product_packet_quantity)).toFixed(2);
        }
        packetSelector.val( packet );

        // Display the error message
        Swal.fire({
          title: "Error!",
          text: product["msg"],
          icon: "error"
        });

      } else {
        
        // Calculate total product based on packet
        var TotalProduct = Math.round(Number(product["product_packet_quantity"]) * Packet);

        // Display total quantity againts of total packet
        if(TotalProduct > 0) {
          $(quantitySelector).val( TotalProduct );
          // Display Subtotal for each product
          $(SubtotalRow).html((BMS.FUNCTIONS.calculateDiscount(netSalesPrice,Discount) * TotalProduct ).toFixed(2));
        }

      }

      // Call the grand total
      this.grandTotal();

    });

  },


  productDiscountCheck: function(event) {

    // Count Sub total for each product. 
    var netSalesPrice = $("#productSaleItemPrice").val();
    var Discount = $("#productSaleItemDiscount").val();
    
    if( event.key === "Enter" && Discount.indexOf("%") > 1 && Discount.replace("%","") >= 100) {

      // Display the error message
      Swal.fire({
        title: "Error!",
        text: "Discount Must be below of 100%",
        icon: "error"
      });

      $("#productSaleItemDiscount").val("");
      $("#productSaleItemDiscount").select();

      event.preventDefault();
      
    } else if ( event.key === "Enter" && Number(Discount) >= Number(netSalesPrice) && Discount.indexOf("%") < 1) {
      
      $("#productSaleItemDiscount").val("");
      $("#productSaleItemDiscount").select();

        // Prevent closing modal if presed enter when have discount problem
        if(event.key === "Enter") {
            event.preventDefault();
        }
  
      // Display the error message
      Swal.fire({
        title: "Error!",
        text: "Discount Must be below of product sale price",
        icon: "error"
      });

      event.preventDefault();
      
    }

  },


    orderDiscountCheck: function(selector, e) {

        var totalAmount = $(".totalAmount").text();
        var orderDiscount = $(selector).val();

        if(orderDiscount.indexOf("%") > 1 && orderDiscount.replace("%","") >= 100) {
        
            // Display the error message
            Swal.fire({
                title: "Error!",
                text: "Discount Must be below of 100%",
                icon: "error"
            });
            $(selector).val(0);

            e.preventDefault();
    
        
        } else if( Number(totalAmount) < 0 &&  Number(orderDiscount) > 0 ) {

            // Display the error message
            Swal.fire({
                title: "Error!",
                text: "Discount must be a negative amount in case of negative total amount.",
                icon: "error"
            });
            
            $(selector).val(-Number(orderDiscount));
            
            e.preventDefault();

        } else if( Number(totalAmount) < 0 && ( Number(orderDiscount) > 0 || Math.abs(Number(orderDiscount)) >= Math.abs(Number(totalAmount)) ) ) {

            // Display the error message
            Swal.fire({
                title: "Error!",
                text: "Discount Must be below of total amount.",
                icon: "error"
            }); 
            
            $(selector).val(0);
            
            e.preventDefault();

        } else if ( Number(totalAmount) > 0 && Number(orderDiscount) >= Number(totalAmount) && orderDiscount.indexOf("%") < 1) {

        // Display the error message
        Swal.fire({
            title: "Error!",
            text: "Discount Must be below of total amount.",
            icon: "error"
        });
        
        $(selector).val(0);
        
        e.preventDefault();
    

        } else if( this.isGivenDiscountPermitted(selector) !== true ) { // If the current biller has permission to give such discount

            // Display the error message
            Swal.fire({
                title: "Error!",
                text: `You do not have permission to give ${orderDiscount} discount.`,
                icon: "error"
            });
            $(selector).val(0);

            e.preventDefault();

        }

        // Grand total
        this.grandTotal();
        

    },


    isGivenDiscountPermitted: function(selector) {

        var totalAmount = $(".totalAmount").text();
        var orderDiscount = $(selector).val();

        if( get_options("maxDiscount") != "" ) { // If the current biller set a max discount

            var maxDiscountAmount = totalAmount - BMS.FUNCTIONS.calculateDiscount(totalAmount, get_options("maxDiscount"));
            var givenDiscountAmount = totalAmount - BMS.FUNCTIONS.calculateDiscount(totalAmount, orderDiscount);

            // In case of return/ negative value
            // -2 is greater then -4
            // So we are converting all negative value to abs
            return Math.abs(maxDiscountAmount) >= Math.abs(givenDiscountAmount);

        } else {
            return true;
        }

    }


};


BMS.PURCHASE = {

    getProductDetails: BMS.PRODUCT.getDetails,

    grandTotal: function() {

        var totalProductQnt = 0;
        $(".productQnt").each(function() {
            totalProductQnt += Number($(this).val());
        });

        /* Display total product quantity */
        $("#totalItems").html( $(".productQnt").length + "(" + totalProductQnt + ")");

        /* Count Total Amount */
        var totalAmount = 0;
        $(".subTotal").each(function() {
            totalAmount += Number($(this).html());
        });

        /* Display Total amount (Sub Total) */
        $(".totalPurchasePrice").html(totalAmount.toFixed(2));

        var amountAfterDiscount = BMS.FUNCTIONS.calculateDiscount(totalAmount, $("#purchaseDiscountValue").val());

        /* Display Discount amount */
        $(".totalPurchaseDiscount").html("(-) " + (totalAmount - amountAfterDiscount).toFixed(2));

        /* Calculate tarif and Charges */
        var totaltarifChargesAmount = 0;
        $("#tariffCharges > .row").each(function() {

            var tarifChargesValue = $(this).closest(".row").find(".tariffChargesName").val().split(": ")[1];
            var tarifChargesAmount = BMS.FUNCTIONS.calculateTarifCharges(amountAfterDiscount, tarifChargesValue);

            $(this).closest(".row").find(".tariffChargesAmount").val(tarifChargesAmount);
            totaltarifChargesAmount += Number(tarifChargesAmount);

        });

        /* Display Total tarif and Charges Amount */
        $(".totalTariffCharges").html("(+) " + totaltarifChargesAmount.toFixed(2));


        /* Display net total */
        var calculateNetTotal = (Number(totaltarifChargesAmount) + Number(amountAfterDiscount)).toFixed(2);
        $(".netTotal").html(calculateNetTotal);
        $("#purchaseNetTotal").val(calculateNetTotal);

        /* Display Grand Total by adding shipping */
        var calculateGrandTotal = Number(calculateNetTotal) + Number($("#purchaseShipping").val());
        $("#purchaseGrandTotal").val(calculateGrandTotal);

        /* Calculate Due Amount */
        var paidAmount = Number($("#purchasePaidAmount").val());

        var dueAmount = (calculateGrandTotal <= paidAmount) ? 0 : calculateGrandTotal - paidAmount;
        var changeAmount = (calculateGrandTotal >= paidAmount) ? 0 : paidAmount - calculateGrandTotal;

        /* Display Change Amount */
        $("#purchaseChangeAmount").val(changeAmount);

        /* Display Due Amount */
        $("#purchaseDueAmount").val(dueAmount);

    },


    calculateEachProduct: function(selector) {

        /* Count Sub total for each product.  */
        var NetPurchasePriceOrCosting = $(selector).closest("tr").find(".productPurchasePrice").val();
        var Quantity = $(selector).closest("tr").find(".productQnt").val();
        var Discount = $(selector).closest("tr").find(".productPurchaseDiscount").val();
        var SubtotalRow = $(selector).closest("tr").find(".subTotal");

        /** Check if the quantity is a numeric value */
        if( isNaN(Quantity) ) {
            alert("Product quantity must be a valid number.");
            $(selector).closest("tr").find(".productQnt").val(1);
            return;
        }

        /* Check if product quantity below of 1 */
        if (Quantity < 1 && $("#purchaseStatus").val() !== "Ordered") {
            alert("Product quantity must be minimum of 1.");
            $(selector).closest("tr").find(".productQnt").val(1);
            return;
        }

        /* Check product purchase Price */
        if (NetPurchasePriceOrCosting === "" || NetPurchasePriceOrCosting === null || NetPurchasePriceOrCosting === 'null') {
            NetPurchasePriceOrCosting = 0;
            $(selector).closest("tr").find(".productPurchasePrice").val(0.00);
        }

        /* Check Discount */
        if (Discount.indexOf("%") > 1 && Discount.replace("%", "") >= 100) {
        
            // Set Discount to Zero
            $(selector).closest("tr").find(".productPurchaseDiscount").val(0);
            alert("Discount Must be below of 100%");
            return;

        } else if (Number(Discount) > Number(NetPurchasePriceOrCosting) && Discount.indexOf("%") < 1) {

            // Set Discount to Zero
            $(selector).closest("tr").find(".productPurchaseDiscount").val(0);
            alert("Discount Must be below of product purchase price");
            return;
        }
        /* Display Subtotal for each product */
        $(SubtotalRow).html(( BMS.FUNCTIONS.calculateDiscount(NetPurchasePriceOrCosting, Discount) * Quantity).toFixed(2) );

    },


    addProduct: function(product_id="", isScanner=false) {

        var productId =  (product_id === "") ? $('#selectProduct').val() : product_id;

        if (productId === "") {
            alert("Please select a product");
            return;
        }

        this.getProductDetails(productId, products => {
        
            products.forEach(eachProduct => {

                BMS.PRODUCT.validationCheck(eachProduct, this, product => {

                    var productBatchHtml = "<input type='hidden' name='productBatch[]' value=''>";
                    /** check if the product has expiry date */
                    if( product.hed !== undefined && product.hed === "1" && $("#purchaseStatus").val() !== "Ordered" ) {

                        productBatchHtml = `<select name="productBatch[]" id="productBatchFor${product.pid}" class="form-control select2Ajax" select2-create-new-url="<?php echo full_website_address(); ?>/xhr/?tooltip=true&module=stock-management&page=newBatchForSelectedProduct&pid=${product.pid}" select2-ajax-url="<?php echo full_website_address(); ?>/info/?module=select2&page=batchList&pid=${product.pid}" required>
                                                <option value=""><?= __("Select Batch"); ?>....</option>
                                            </select>`;
                    }

                    var itemQnt = product.iq ? parseFloat(product.iq).toFixed(0) : 1;

                    var html = `<tr>
                        <input type="hidden" name="productID[]" class="productID" value="${product.pid}">
                        <td class="col-md-4">${product.pn}</td>
                        <td class="col-md-2">${productBatchHtml}</td>
                        <td class="stock_col" style="width: 155px;"><span>${product.alertq}</span> <span>${product.soldq}</span> <span>${product.stockq}</span></td>
                        <td><input onclick = "this.select()" type="text" name="productQnt[]" value="${itemQnt}" class="productQnt form-control text-center"></td>
                        <td>${product.pu}</td>
                        <td class="text-right"><input onclick = "this.select()" type="text" name="productPurchasePrice[]" value="${product.pp}" class="productPurchasePrice form-control text-center" step="any"></td>
                        <input type="hidden" name="productMainPurchasePrice[]" class="productMainPurchasePrice" value="${product.pp}" step="any">
                        <td class="text-right"><input onclick = "this.select()" type="text" name="productPurchaseDiscount[]" value="0" placeholder="10% or 10" class="productPurchaseDiscount form-control text-center"></td>
                        <td class="text-right subTotal">${product.pp}</td>
                        <td style="width: 30px; !important">
                            <i style="cursor: pointer;" class="fa fa-trash-o removeThisProduct"></i>
                        </td>
                        </tr>`;

                    $("#productTable > tbody").append(html);

                });

            });

            // Select Quantity input field if not barcose scanner
            if(isScanner === false) {
                $(`#productTable > tbody tr:last`).find(".productQnt").select();
            }

        
        /* Count the Total Quantity */
        this.grandTotal();

        /* remove the value from selectProduct select box */
        $('#selectProduct').empty();

        });

    },


    productUnitCheck: function(selector) {

        var productId = $(selector).closest("tr").find(".productID").val();
        var self = this;
        $.ajax({
            url: full_website_address + "/info/?module=data&page=productUnitDetails&product_id=" + productId + "&unit=" + $(selector).val(),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(productUnit, status) {
                if (status == "success") {

                    /* Change the product Price */
                    $(selector).closest("tr").find(".productPurchasePrice").val(productUnit.pp);
                    $(selector).closest("tr").find(".productMainPurchasePrice").val(productUnit.pp);

                    /* Calculate all data */
                    self.calculateEachProduct(selector);
                    self.grandTotal();

                }
            }
        });

    }

};


// Assing all functionality from POS
BMS.ORDER = Object.assign({}, BMS.POS);

// Rewrite addProduct Functions
BMS.ORDER.addProduct = function(product_id="") {
          
    var productId =  (product_id === "") ? $('#selectProduct').val() : product_id;

    if (productId === "") {
        alert("Please select a product");
        return;
    }

    // Remove/ empty the product selection input.
    if($('#selectProduct').val() > 0) {
        $("#selectProduct").html("<option value=''>Search Product....</option>");
    }

    // Get product details
    BMS.PRODUCT.getDetails(productId, products => {

        // Loop throw Products
        products.forEach(eachProduct => {

            BMS.PRODUCT.validationCheck(eachProduct, this, product => {

                var packet = 0;
                var itemQnt = product.iq ? parseFloat(product.iq).toFixed(0) : 1;
                if(product.pq > 0) {
                    packet = (itemQnt / Number(product.pq)).toFixed(2);
                }

                // Check Discount
                var productDiscount = !product.pd ? "0" : product.pd;

                // Generate Discount
                var amountAfterDiscount = BMS.FUNCTIONS.calculateDiscount(product.sp, productDiscount);
                var displayProductPrice = "";
                if( Number(amountAfterDiscount) === Number(product.sp) ) {
                    displayProductPrice = parseFloat(product.sp).toFixed(2);
                } else {
                    displayProductPrice = "<span>" + amountAfterDiscount + "</span><span><del><small>"+ parseFloat(product.sp).toFixed(2) +"</small></del></span>";
                }                      

                var rowId = Date.now()+product.pid;

                var html = `<tr ${ product.so ? 'style="background-color: pink;"' : '' } id=${rowId}> 
                                <td class="col-md-7">
                                    <span data-toggle="modal" data-target="#modalDefault" href="${full_website_address}/xhr/?module=reports&page=totalPurchasedQuantityOfThisCustomer&cid=${$("#customersId").val()}&pid=${productId}"> <i class="fa fa-info-circle productDescription"></i> </span> 
                                    <a href="#" data-toggle="modal" onclick="BMS.POS.editProductItemDetails('${rowId}', '${product.pn}')" data-target="#productSaleDetails">${product.pn}</a>
                                </td> 
                                <td class="col-md-2 displayProductPrice text-right">${displayProductPrice}</td> 
                                <td class="col-md-2"><input onclick = "this.select()" type="text" name="productQnt[]" value="${itemQnt}" class="productQnt form-control text-center" autoComplete="off"></td>
                                <td class="col-md-2">${product.pu}</td> 
                                <td class="col-md-3 subtotalCol text-right">${ parseFloat(Number(amountAfterDiscount)* Number(itemQnt)).toFixed(2) }</td> 
                                <td style="width: 25px !important;"> 
                                    <i style="cursor: pointer;" class="fa fa-trash-o removeThisProduct"></i> 
                                </td> 
                                <input type="hidden" name="productID[]" class="productID" value="${product.pid}"> 
                                <input type="hidden" class="netSalesPrice" name="productSalePirce[]" value="${product.sp}"> 
                                <input type="hidden" class="productMainSalePirce" name="productMainSalePirce[]" value="${product.sp}"> 
                                <input type="hidden" class="productSO" value="${product.so}"> 
                                <input type="hidden" name="productDiscount[]" value="${productDiscount}" class="productDiscount" autoComplete="off"> 
                                <input type="hidden" name="productBatch[]" value="">
                                <input type="hidden" name="productHasExpiryDate[]" value="${product.hed}"> 
                                <input type="hidden" name="productPacket[]" value="${packet}" class="productPacket"> 
                                <input type="hidden" name="productItemDetails[]" class="productItemDetails" value=""> 
                            </tr>`;

                $("#productTable > tbody").append(html);

            });

        });

        // Select Quantity input field
        $(`#productTable > tbody tr:last`).find(".productQnt").select();
        
        // Count the Total Quantity
        this.grandTotal();
        
        // Disable the customer Selection and Warehouse selection if there have any product in the list
        this.disableEnableWCSelect();

    });
};

// Rewrite productQntCheck function
BMS.ORDER.productQntCheck = function(selector, event) {

    // Count Sub total for each product. 
    var netSalesPrice = $(selector).closest("tr").find(".netSalesPrice").val();
    var Quantity = $(selector).val();
    var Discount = $(selector).closest("tr").find(".productDiscount").val();
    var SubtotalRow = $(selector).closest("tr").find("td.subtotalCol");

    // If the product quantity less then 1 then throw an error
    if(Quantity == 0) {

        // Display the error message
        Swal.fire({
            title: "Error!",
            text: "Product qunatity can not be zero (0)",
            icon: "error"
        });

        // Set product quantity 1
        $(selector).val(1);
        Quantity = 1;

    }

    // Display Subtotal for each product
    $(SubtotalRow).html((BMS.FUNCTIONS.calculateDiscount(netSalesPrice,Discount) * Quantity).toFixed(2));

    // Call the grand total
    this.grandTotal();
    
};


BMS.STOCK_TRANSFER = {

  getProductDetails: BMS.POS.getProductDetails,

  grandTotal: function() {

    var totalProductQnt = 0;
    $(".productQnt").each(function() {
        totalProductQnt += Number($(this).val());
    });

    /* Display total product quantity */
    $("#totalItems").html( $(".productQnt").length + "(" + totalProductQnt + ")");

    /* Count Total Amount */
    var totalAmount = 0;
    $(".subTotal").each(function() {
        totalAmount += Number($(this).html());
    });

    /* Display Total amount (Sub Total) */
    $(".totalPurchasePrice").html(totalAmount.toFixed(2));

  },

  disableEnableWarehouseSelect: function() {

     var productCountInList = $(".productQnt").length;

     if(productCountInList > 0) {
     $("#stockTransferFromWarehouse, #stockTransferToWarehouse").prop("disabled", true);
     } else {
     $("#stockTransferFromWarehouse, #stockTransferToWarehouse").prop("disabled", false);
     }

  },

  calculateEachProduct: function(selector) {

    /* Count Sub total for each product.  */
    var subTotal = 0;
    var NetPurchasePriceOrCosting = $(selector).closest("tr").find(".productPurchasePrice").val();
    var Quantity = $(selector).closest("tr").find(".productQnt").val();
    var Discount = $(selector).closest("tr").find(".productPurchaseDiscount").val();
    var SubtotalRow = $(selector).closest("tr").find(".subTotal");

    /* Check if product quantity below of 1 */
    if (Quantity < 1) {
        alert("Product quantity must be minimum of 1.");
        $(selector).closest("tr").find(".productQnt").val(1);
        return;
    }

    /* Check product purchase Price */
    if (NetPurchasePriceOrCosting === "" || NetPurchasePriceOrCosting === null || NetPurchasePriceOrCosting === 'null') {
        NetPurchasePriceOrCosting = 0;
        $(selector).closest("tr").find(".productPurchasePrice").val(0.00);
    }

    /* Check Discount */
    if (Discount.indexOf("%") > 1 && Discount.replace("%", "") >= 100) {

        // Set Discount to Zero
        $(selector).closest("tr").find(".productPurchaseDiscount").val(0);
        alert("Discount Must be below of 100%");
        return;

    } else if (Number(Discount) > Number(NetPurchasePriceOrCosting) && Discount.indexOf("%") < 1) {

        // Set Discount to Zero
        $(selector).closest("tr").find(".productPurchaseDiscount").val(0);
        alert("Discount Must be below of product purchase price");
        return;
    }
    /* Display Subtotal for each product */
    $(SubtotalRow).html((BMS.FUNCTIONS.calculateDiscount(NetPurchasePriceOrCosting, Discount) * Quantity).toFixed(2));

  },

  addProduct: function(product_id="", isScanner=false) {
  
    var productId =  (product_id === "") ? $('#selectProduct').val() : product_id;
    var fromWarehouse = $("#stockTransferFromWarehouseId").val();
    var pqnt = 1;

    if( fromWarehouse === "" ) {
        alert("Please select an warehouse to add product");
        return;
    }

    if (productId === "") {
        alert("Please select a product");
        return;
    }

    
    this.getProductDetails({
      product_id: productId,
      warehouse_id: fromWarehouse,
      qnt: pqnt
    }, products => {
      
        if(products["error"] !== undefined && products["error"] === true) {
            
            /* Display the error message */
            Swal.fire({
                title: "Error!",
                text: products["msg"],
                icon: "error"
            });

            return; 
        }


        // Loop throw Products
        products.forEach(eachProduct => {

            BMS.PRODUCT.validationCheck(eachProduct, this, product => { 

                var productBatchHtml = "<input type='hidden' name='productBatch[]' value=''>";
                /** check if the product has expiry date */
                if( product.hed !== undefined && product.hed === "1" ) {

                    productBatchHtml = `<select name="productBatch[]" id="productBatch" class="form-control select2Ajax" select2-create-new-url="<?php echo full_website_address(); ?>/xhr/?tooltip=true&module=stock-management&page=newBatchForSelectedProduct&pid=${product.pid}" select2-ajax-url="<?php echo full_website_address(); ?>/info/?module=select2&page=batchList&pid=${product.pid}" required>
                                            <option value=""><?= __("Select Batch"); ?>....</option>
                                        </select>`;
                }

                var itemQnt = product.iq ? parseFloat(product.iq).toFixed(0) : 1;
                 var html = `<tr>
                                <input type="hidden" name="productID[]" class="productID" value="${product.pid}">
                                <td class="col-md-4">${product.pn}</td>
                                <td class="col-md-2">${productBatchHtml}</td>
                                <td class="col-md-1"><input onclick = "this.select()" type="text" name="productQnt[]" value="${itemQnt}" class="productQnt form-control text-center"></td>
                                <td class="col-md-1"> ${product.pu}</td>
                                <td class="text-right col-md-1"><input onclick = "this.select()" type="text" name="productPurchasePrice[]" value="${product.pp}" class="productPurchasePrice form-control text-center" step="any"></td>
                                <input type="hidden" name="productMainPurchasePrice[]" value="${product.pp}" step="any">
                                <td class="text-right col-md-1"><input onclick = "this.select()" type="text" name="productPurchaseDiscount[]" value="" placeholder="10% or 10" class="productPurchaseDiscount form-control text-center"></td>
                                <td class="text-right subTotal">${parseFloat(product.pp * itemQnt).toFixed(2) }</td>
                                <td style="width: 30px; !important">
                                    <i style="cursor: pointer;" class="fa fa-trash-o removeThisProduct"></i>
                                </td>
                            </tr>`;

                $("#productTable > tbody").append(html);

            });

        });

      
      /* Count the Total Quantity */
      this.grandTotal();
      this.disableEnableWarehouseSelect();

      /* remove the value from selectProduct select box */
      $('#selectProduct').empty();


    });

  },

  productUnitCheck: function(selector) {

    var that = selector;

    this.getProductDetails({
      product_id: $(selector).closest("tr").find(".productID").val(),
      warehouse_id: $("#stockTransferFromWarehouseId").val(),
      qnt: $(selector).closest("tr").find(".productQnt").val(),
      unit: $(selector).val()
    }, product => {

      if(product["error"] !== undefined && product["error"] === true) {

        console.log(product.stq);

        $(that).closest("tr").find(".productQnt").val(product.stq); /* Set the product quantity with max */

        /* Display the error message */
        Swal.fire({
          title: "Error!",
          text: product["msg"],
          icon: "error"
        });

      }

      /* Change the product Price */
      $(that).closest("tr").find(".productPurchasePrice").val(parseFloat(product.pp).toFixed(2));

      /* Calculate all data */
      this.calculateEachProduct(that);
      this.grandTotal();

    });

  },


  productQntCheck: function(selector) {

    var Quantity = $(selector).val();
    var that = $(selector);

    /** Check if the quantity is a numeric value */
    if( isNaN(Quantity) ) {
        alert("Product quantity must be a valid number.");
        /* Set product quantity 1 */
        $(selector).val(1);
        Quantity = 1;

    }

    /* If the product quantity less then 1 then throw an error */
    if(Quantity < 1) {

        /* Display the error message */
        Swal.fire({
            title: "Error!",
            text: "Product qunatity must be at least one",
            icon: "error"
        });

        /* Set product quantity 1 */
        $(selector).val(1);
        Quantity = 1;

    }

    this.getProductDetails({
      product_id: $(selector).closest("tr").find(".productID").val(),
      warehouse_id: $("#stockTransferFromWarehouseId").val(),
      qnt: Quantity,
      unit: $(selector).closest("tr").find(".productItemUnit").val()
    }, product => {

      if(product["error"] !== undefined && product["error"] === true) {

        $(that).val(product["stq"]); /* Set the product quantity with max */

        /* Display the error message */
        Swal.fire({
        title: "Error!",
        text: product["msg"],
        icon: "error"
        });

      }

      /* Call the grand total */
      this.calculateEachProduct(that);
      this.grandTotal();

    });

  }

}


BMS.STOCK_ENTRY = {

    getProductDetails: BMS.PRODUCT.getDetails,
  
    grandTotal: function() {
  
      var totalProductQnt = 0;
      $(".productQnt").each(function() {
          totalProductQnt += Number($(this).val());
      });
  
      /* Display total product quantity */
      $("#totalItems").html( $(".productQnt").length + "(" + totalProductQnt + ")");
  
      /* Count Total Amount */
      var totalAmount = 0;
      $(".subTotal").each(function() {
          totalAmount += Number($(this).html());
      });
  
      /* Display Total amount (Sub Total) */
      $(".totalPurchasePrice").html(totalAmount.toFixed(2));
  
    },
  
  
    calculateEachProduct: function(selector) {
  
      /* Count Sub total for each product.  */
      var subTotal = 0;
      var NetPurchasePriceOrCosting = $(selector).closest("tr").find(".productPurchasePrice").val();
      var Quantity = $(selector).closest("tr").find(".productQnt").val();
      var Discount = $(selector).closest("tr").find(".productPurchaseDiscount").val();
      var SubtotalRow = $(selector).closest("tr").find(".subTotal");
  
      /* Check if product quantity below of 1 */
      if (Quantity < 1 && $("#stockEntryType").val() === "Production" ) {
          alert("Negative quantity is not valid for Production of stock.");
          $(selector).closest("tr").find(".productQnt").val(1);
          return;
      }
  
      /* Check product purchase Price */
      if (NetPurchasePriceOrCosting === "" || NetPurchasePriceOrCosting === null || NetPurchasePriceOrCosting === 'null') {
          NetPurchasePriceOrCosting = 0;
          $(selector).closest("tr").find(".productPurchasePrice").val(0.00);
      }
  
      /* Check Discount */
      if (Discount.indexOf("%") > 1 && Discount.replace("%", "") >= 100) {
  
          // Set Discount to Zero
          $(selector).closest("tr").find(".productPurchaseDiscount").val(0);
          alert("Discount Must be below of 100%");
          return;
  
      } else if (Number(Discount) > Number(NetPurchasePriceOrCosting) && Discount.indexOf("%") < 1) {
  
          // Set Discount to Zero
          $(selector).closest("tr").find(".productPurchaseDiscount").val(0);
          alert("Discount Must be below of product purchase price");
          return;
      }
      /* Display Subtotal for each product */
      $(SubtotalRow).html((BMS.FUNCTIONS.calculateDiscount(NetPurchasePriceOrCosting, Discount) * Quantity).toFixed(2));
  
    },
  
    addProduct: function(product_id="") {
  
      var productId =  (product_id === "") ? $('#selectProduct').val() : product_id;
      var fromWarehouse = $("#stockTransferFromWarehouseId").val();
      var pqnt = 1;
  
      if( fromWarehouse === "" ) {
          alert("Please select an warehouse to add product");
          return;
      }
  
      if (productId === "") {
          alert("Please select a product");
          return;
      }
  
      
      this.getProductDetails(productId, products => {
        
          if(products["error"] !== undefined && products["error"] === true) {
              
              /* Display the error message */
              Swal.fire({
                  title: "Error!",
                  text: products["msg"],
                  icon: "error"
              });
  
              return; 
          }
  
  
          // Loop throw Products
          products.forEach(eachProduct => {
  
              BMS.PRODUCT.validationCheck(eachProduct, this, product => { 
  
                  var productBatchHtml = "<input type='hidden' name='productBatch[]' value=''>";
                  /** check if the product has expiry date */
                  if( product.hed !== undefined && product.hed === "1" ) {
  
                      productBatchHtml = `<select name="productBatch[]" id="productBatch" class="form-control select2Ajax" select2-create-new-url="<?php echo full_website_address(); ?>/xhr/?tooltip=true&module=stock-management&page=newBatchForSelectedProduct&pid=${product.pid}" select2-ajax-url="<?php echo full_website_address(); ?>/info/?module=select2&page=batchList&pid=${product.pid}" required>
                                              <option value=""><?= __("Select Batch"); ?>....</option>
                                          </select>`;
                  }
  
                  var itemQnt = product.iq ? parseFloat(product.iq).toFixed(0) : 1;
                   var html = `<tr>
                                  <input type="hidden" name="productID[]" class="productID" value="${product.pid}">
                                  <td class="col-md-4">${product.pn}</td>
                                  <td class="col-md-2">${productBatchHtml}</td>
                                  <td class="col-md-1"><input onclick = "this.select()" type="text" name="productQnt[]" value="${itemQnt}" class="productQnt form-control text-center"></td>
                                  <td class="col-md-1"> ${product.pu}</td>
                                  <td class="text-right col-md-1"><input onclick = "this.select()" type="text" name="productPurchasePrice[]" value="${product.pp}" class="productPurchasePrice form-control text-center" step="any"></td>
                                  <input type="hidden" name="productMainPurchasePrice[]" value="${product.pp}" step="any">
                                  <td class="text-right col-md-1"><input onclick = "this.select()" type="text" name="productPurchaseDiscount[]" value="" placeholder="10% or 10" class="productPurchaseDiscount form-control text-center"></td>
                                  <td class="text-right subTotal">${parseFloat(product.pp * itemQnt).toFixed(2) }</td>
                                  <td style="width: 30px; !important">
                                      <i style="cursor: pointer;" class="fa fa-trash-o removeThisProduct"></i>
                                  </td>
                              </tr>`;
  
                  $("#productTable > tbody").append(html);
  
              });
  
          });
  
        
        /* Count the Total Quantity */
        this.grandTotal();
  
        /* remove the value from selectProduct select box */
        $('#selectProduct').empty();

      });
  
    }
  
}


BMS.RETURN = {

  getProductReturnDetails: function({product_id, customer_id}, returnData) {

    $.ajax({ 
      url: full_website_address + "/info/?module=data&page=productDetailsForReturn&product_id=" + product_id+"&customer_id="+customer_id,
      contentType: "application/json; charset=utf-8",
      dataType: "json",
      success: function (data, status) {
        returnData(data);
      }
    });

  },

  grandTotal: function() {

    var totalProductQnt = 0;
    $(".productQnt").each(function() {
        totalProductQnt += Number($(this).val());
    });

    /* Display total product quantity */
    $("#totalItems").html( $(".productQnt").length + "(" + totalProductQnt + ")");

    /* Count Total Amount */
    var totalAmount = 0;
    $(".subTotal").each(function() {
        totalAmount += Number($(this).html());
    });

    /* Display Total amount (Sub Total) */
    $(".totalReturnPrice").html(totalAmount.toFixed(2));

    var amountAfterDiscount = BMS.FUNCTIONS.calculateDiscount(totalAmount, $("#returnDiscountValue").val());

    /* Display Discount amount */
    $(".totalReturnDiscount").html("(-) " + (totalAmount - amountAfterDiscount).toFixed(2));

    /* Calculate tarif and Charges */
    var totaltarifChargesAmount = 0;
    $("#tariffCharges > .row").each(function() {

        var tarifChargesValue = $(this).closest(".row").find(".tariffChargesName").val().split(": ")[1];
        var tarifChargesAmount = BMS.FUNCTIONS.calculateTarifCharges(amountAfterDiscount, tarifChargesValue);

        $(this).closest(".row").find(".tariffChargesAmount").val(tarifChargesAmount);
        totaltarifChargesAmount += Number(tarifChargesAmount);

    });

    /* Display Total tarif and Charges Amount */
    $(".totalTariffCharges").html("(+) " + totaltarifChargesAmount.toFixed(2));


    /* Display net total */
    var calculateNetTotal = (Number(totaltarifChargesAmount) + Number(amountAfterDiscount)).toFixed(2);
    $(".netTotal").html(calculateNetTotal);
    $("#returnNetTotal").val(calculateNetTotal);

    // get shipping
    var returnShipping = $("#returnShipping").val();

    /* Display Grand Total by subtracting surcharge */
    var calculateGrandTotal = ( Number(calculateNetTotal) + Number(returnShipping) ) - Number($("#returnSurcharge").val());
    $("#returnGrandTotal").val(calculateGrandTotal);

    /* Calculate Due Amount */
    var paidAmount = Number($("#returnPaidAmount").val());

    var dueAmount = (calculateGrandTotal <= paidAmount) ? 0 : calculateGrandTotal - paidAmount;
    var changeAmount = (calculateGrandTotal >= paidAmount) ? 0 : paidAmount - calculateGrandTotal;

    /* Display Change Amount */
    $("#returnChangeAmount").val(changeAmount);

    /* Display Due Amount */
    $("#returnDueAmount").val(dueAmount);

  },

  calculateEachProduct: function(selector) {

    /* Count Sub total for each product.  */
    var subTotal = 0;
    var NetReturnPriceOrCosting = $(selector).closest("tr").find(".productReturnPrice").val();
    var Quantity = $(selector).closest("tr").find(".productQnt").val();
    var Discount = $(selector).closest("tr").find(".productReturnDiscount").val();
    var SubtotalRow = $(selector).closest("tr").find(".subTotal");

    /* Check if product quantity below of 1 */
    if (Quantity < 1) {
        alert("Product quantity must be minimum of 1.");
        $(selector).closest("tr").find(".productQnt").val(1);
        return;
    }

    /* Check product return Price */
    if (NetReturnPriceOrCosting === "" || NetReturnPriceOrCosting === null || NetReturnPriceOrCosting === 'null') {
        NetReturnPriceOrCosting = 0;
        $(selector).closest("tr").find(".productReturnPrice").val(0.00);
    }

    /* Check Discount */
    if (Discount.indexOf("%") > 1 && Discount.replace("%", "") >= 100) {
        alert("Discount Must be below of 100%");
        return;
    } else if (Number(Discount) > Number(NetReturnPriceOrCosting) && Discount.indexOf("%") < 1) {
        alert("Discount Must be below of product return price");
        return;
    }
    /* Display Subtotal for each product */
    $(SubtotalRow).html((BMS.FUNCTIONS.calculateDiscount(NetReturnPriceOrCosting, Discount) * Quantity).toFixed(2));

  },


  addProduct: function(product_id="") {

    var productId =  (product_id === "") ? $('#selectProduct').val() : product_id;
    var customer_id = $("#returnCustomer").val();

    if(customer_id === "") {
        alert("Please select the customer");
        return; 
    }

    if (productId === "") {
        alert("Please select a product");
        return;
    }

  
    this.getProductReturnDetails({
      product_id: productId,
      customer_id: customer_id
    }, products => {


      // Loop throw Products
      products.forEach(eachProduct => {

        BMS.PRODUCT.validationCheck(eachProduct, this, product => { 

            var productDiscount = !product.pd ? "0" : product.pd;
            var amountAfterDiscount = parseFloat(BMS.FUNCTIONS.calculateDiscount(product.sp, productDiscount)).toFixed(2);

            var productBatchHtml = "<input type='hidden' name='productBatch[]' value=''>";
            /** check if the product has expiry date */
            if( product.hed !== undefined && product.hed === "1" ) {

                productBatchHtml = `<select name="productBatch[]" id="productBatch" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address(); ?>/info/?module=select2&page=batchList&pid=${product.pid}" required>
                                        <option value=""><?= __("Select Batch"); ?>....</option>
                                    </select>`;
            }

            var html = `<tr>
                    <input type="hidden" name="productID[]" class="productID" value="${product.pid}">
                    <td class="col-md-4 productDetails">${product.pn}<br/>(<i>Purchased: ${product.prq} Returned: ${product.rtq}</i>)</td>
                    <td class="col-md-2">${productBatchHtml}</td>
                    <td class="col-md-1"><input onclick = "this.select()" type="text" name="productQnt[]" value="1" class="productQnt form-control text-center"></td>
                    <td class="col-md-1">${product.pu}</td>
                    <td class="text-right col-md-1"><input onclick = "this.select()" type="text" name="productReturnPrice[]" value="${product.sp}" class="productReturnPrice form-control text-center" step="any"></td>
                    <input type="hidden" name="productReturnMainPrice[]" value="${product.sp}" step="any">
                    <td class="text-right col-md-1"><input onclick = "this.select()" type="text" name="productReturnDiscount[]" value="${productDiscount}" placeholder="10% or 10" class="productReturnDiscount form-control text-center"></td>
                    <td class="text-right subTotal">${amountAfterDiscount}</td>
                    <td style="width: 30px; !important">
                        <i style="cursor: pointer;" class="fa fa-trash-o removeThisProduct"></i>
                    </td>
                </tr>`;

            $("#productTable > tbody").append(html);

        });

      });


      /* Count the Total Quantity */
      this.grandTotal();

      /* remove the value from selectProduct select box */
      $('#selectProduct').empty();

    });

  },


  productUnitCheck: function(selector) {

    var that = selector;

    this.getProductReturnDetails({
      product_id: $(selector).closest("tr").find(".productID").val(),
      customer_id: $("#returnCustomer").val(),
      unit: $(selector).val()
    }, products => {

      var product = products[0];

      /* Change the product Price */
      $(that).closest("tr").find(".productReturnPrice").val( parseFloat(product.sp).toFixed(2) );

      var productDetails = product.pn + '<br/>(<i>Purchased: '+ parseFloat(product.prq).toFixed(2) +', Returned: '+ parseFloat(product.rtq).toFixed(2) + ')';
      $(that).closest("tr").find(".productDetails").html( productDetails );

      /* Calculate all data */
      this.calculateEachProduct(that);
      this.grandTotal();
    
    });

  }

}


BMS.WASTAGE_SALE = {

  grandTotal: function() {

    var totalProductQnt = 0;
    $(".wastageSaleItemQnt").each(function() {
        totalProductQnt += Number($(this).val());
    });

    /* Display total product quantity */
    $("#totalItems").html( $(".wastageSaleItemQnt").length + "(" + totalProductQnt + ")");

    /* Count Total Amount */
    var totalAmount = 0;
    $(".wastageSaleItemSubtotal").each(function() {
        totalAmount += Number($(this).text());
    });

    /* Display Total amount (Sub Total) */
    $(".totalWastageSalePrice").html(totalAmount.toFixed(2));

    var amountAfterDiscount = BMS.FUNCTIONS.calculateDiscount(totalAmount, $("#wastageSaleDiscountValue").val());

    /* Display Discount amount */
    $(".totalWastageSaleDiscount").html("(-) " + (totalAmount - amountAfterDiscount).toFixed(2));

    /* Calculate tarif and Charges */
    var totaltarifChargesAmount = 0;
    $("#tariffCharges > .row").each(function() {

        var tarifChargesValue = $(this).closest(".row").find(".tariffChargesName").val().split(": ")[1];
        var tarifChargesAmount = BMS.FUNCTIONS.calculateTarifCharges(amountAfterDiscount, tarifChargesValue);

        $(this).closest(".row").find(".tariffChargesAmount").val(tarifChargesAmount);
        totaltarifChargesAmount += Number(tarifChargesAmount);

    });

    /* Display Total tarif and Charges Amount */
    $(".totalTariffCharges").html("(+) " + totaltarifChargesAmount.toFixed(2));


    /* Display net total */
    var calculateNetTotal = (Number(totaltarifChargesAmount) + Number(amountAfterDiscount)).toFixed(2);
    $(".netTotal").html(calculateNetTotal);
    $("#wastageSaleNetTotal").val(calculateNetTotal);

    /** Get Paid Amount */
    var paidAmount = $("#wastageSalePaidAmount").val();

    /** Caculate Due amount and display */
    var dueAmount = ( paidAmount < calculateNetTotal ) ? (calculateNetTotal - paidAmount) : 0;
    $("#wastageSaleDue").val( (dueAmount).toFixed(2) );

  },


  addWastageSaleItem: function() {

    var html = '<tr>\
                <td class="col-md-5"> <input type="text" name="wastageSaleItem[]" placeholder="Enter Item name and details" class="wastageSaleItem form-control" required> </td>\
                <td class="col-md-2"> <input type="number" step="any" name="wastageSaleItemQnt[]" class="wastageSaleItemQnt form-control" required> </td>\
                <td class="col-md-2"> <input type="number" step="any" name="wastageSaleItemPrice[]" class="wastageSaleItemPrice form-control" required> </td>\
                <td class="text-right wastageSaleItemSubtotal">0.00</td>\
                <td style="width: 30px; !important"> \
                    <i style="cursor: pointer;" class="fa fa-trash-o removeThisItem"></i> \
                </td> \
            </tr>';

    $("#productTable > tbody").append(html);

    /* Focus the item name input field */
    $(".wastageSaleItem").focus();

    /* Count all totals */
    this.grandTotal();

  }

}


BMS.SPECIMEN_COPY = {
  
  getProductDetails: BMS.POS.getProductDetails,

  disableEnableWarehouseSelect: function() {

    if ( $(".productQnt").length > 0) {
        $("#scTransferWarehouse").prop("disabled", true);
    } else {
        $("#scTransferWarehouse").prop("disabled", false);
    }

  },

  addProduct: function(product_id="") {

    var productId =  (product_id === "") ? $('#selectProduct').val() : product_id;
    var fromWarehouse = $("#scTransferWarehouse").val();

    if (fromWarehouse === "") {
        alert("Please select an warehouse to add product");
        return;
    }

    if (productId === "") {
        alert("Please select a product");
        return;
    }


        // Get product details
    this.getProductDetails({
        product_id: productId,
        warehouse_id: fromWarehouse,
    }, products => {

        if (products["error"] !== undefined && products["error"] === true) {

            /* Display the error message */
            Swal.fire({
                title: "Error!",
                text: products["msg"],
                icon: "error"
            });

            return;
        }

        // Loop throw Products
        products.forEach(eachProduct => {
            
            BMS.PRODUCT.validationCheck(eachProduct, this, product => {

                var itemQnt = product.iq ? parseFloat(product.iq).toFixed(0) : 1;

                var html = `<tr> \
                        <input type="hidden" name="productID[]" class="productID" value="${product.pid}">
                        <td class="col-md-6">${product.pn}</td>
                        <td class="col-md-3"><input onclick = "this.select()" type="text" name="productQnt[]" value="${itemQnt}" class="productQnt form-control text-center"></td>
                        <td class="col-md-3">${product.pu}</td>
                        <td style="width: 28px !important;">
                            <i style="cursor: pointer;" class="fa fa-trash-o removeThisProduct"></i>
                        </td>
                        </tr>`;

                $("#productTable > tbody").append(html);

            });


        });


      /* Count the Total Quantity */
      this.disableEnableWarehouseSelect();

      /* remove the value from selectProduct select box */
      $('#selectProduct').empty();

    });


  },

  productUnitCheck: function(selector) {

    this.getProductDetails({
      product_id: $(selector).closest("tr").find(".productID").val(),
      warehouse_id: $("#scTransferWarehouse").val(),
      qnt: $(selector).closest("tr").find(".productQnt").val(),
      unit: $(selector).val()
    }, product => {

      if (product["error"] !== undefined && product["error"] === true) {

        $(selector).closest("tr").find(".productQnt").val(product.stq); /* Set the product quantity with max */

        /* Display the error message */
        Swal.fire({
            title: "Error!",
            text: product["msg"],
            icon: "error"
        });

      }
    
    });

  },

  productQntCheck: function(selector) {

    var Quantity = $(selector).val();

    /** Check if the quantity is a numeric value */
    if( isNaN(Quantity) ) {
        alert("Product quantity must be a valid number.");
        /* Set product quantity 1 */
        $(selector).val(1);
        Quantity = 1;
    }

    /* If the product quantity less then 1 then throw an error */
    if (Quantity < 1) {

        /* Display the error message */
        Swal.fire({
            title: "Error!",
            text: "Product qunatity must be at least one",
            icon: "error"
        });

        /* Set product quantity 1 */
        $(selector).val(1);
        Quantity = 1;

    }

    this.getProductDetails({
      product_id: $(selector).closest("tr").find(".productID").val(),
      warehouse_id: $("#scTransferWarehouse").val(),
      qnt: Quantity,
      unit: $(selector).closest("tr").find(".productItemUnit").val()
    }, product => {

      if (product["error"] !== undefined && product["error"] === true) {

        $(selector).val(product.stq); /* Set the product quantity with max */

        /* Display the error message */
        Swal.fire({
            title: "Error!",
            text: product["msg"],
            icon: "error"
        });

      }

    });

  }

}


BMS.SC_DISTRIBUTION = {

  getProductDetails: BMS.PRODUCT.getDetails,

  addProduct: function(product_id="") {

    var productId =  (product_id === "") ? $('#selectProduct').val() : product_id;

    if (productId === "") {
        alert("Please select a product");
        return;
    }


    // Get product details
    this.getProductDetails(productId, products => {

      if (products["error"] !== undefined && products["error"] === true) {

        /* Display the error message */
        Swal.fire({
            title: "Error!",
            text: products["msg"],
            icon: "error"
        });

        return;

      }

        // Loop throw Products
        products.forEach(eachProduct => {

            BMS.PRODUCT.validationCheck(eachProduct, this, product => {
                
                var itemQnt = product.iq ? parseFloat(product.iq).toFixed(0) : 1;

                var html = `<tr>
                    <input type="hidden" name="productID[]" class="productID" value="${product.pid}">
                    <td class="col-md-6">${product.pn}</td>
                    <td class="col-md-3"><input onclick = "this.select()" type="text" name="productQnt[]" value="${itemQnt}" class="productQnt form-control text-center"></td>
                    <td class="col-md-3">${product.pu}</td>
                    <td style="width: 28px !important;">
                        <i style="cursor: pointer;" class="fa fa-trash-o removeThisProduct"></i>
                    </td>
                </tr>`;

                $("#productTable > tbody").append(html);

            });


        });

        /* remove the value from selectProduct select box */
        $('#selectProduct').empty();

    });

  }

};


BMS.CHAT = {


    showChatBox: function(event, userId) {

        if(event !== "") {
            event.preventDefault();
        }

        // Get SDP Data for selected user
        $.ajax({
            url: full_website_address + "/info/?module=chat&page=getChatUserData",
            type: "post",
            data: {
                userId: userId
            },
            success: function(data, status) {

                var data = JSON.parse(data) ;

                console.log( data );

                var latestMsg = "";
                if( data.latest_msg !== "" ) {

                    data.latest_msg.forEach(item => {
                        
                        if( item.from_user != userId ) {
                            // Local user conversation
                            latestMsg += `<div class="direct-chat-msg">
                                                <div class="direct-chat-info clearfix">
                                                    <span class="direct-chat-name pull-left">${item.from_username}</span>
                                                    <span class="direct-chat-timestamp pull-right">${item.datetime}</span>
                                                </div>
                                                <img class="direct-chat-img" src="${full_website_address}/images/?for=employees&id=${item.from_user}">
                                                <div class="direct-chat-text">
                                                    ${item.msg_text}
                                                </div>
                                            </div>`;

                        } else {

                            // Remote user conversation
                            latestMsg += `<div class="direct-chat-msg right">
                                                <div class="direct-chat-info clearfix">
                                                    <span class="direct-chat-name pull-right">${item.from_username}</span>
                                                    <span class="direct-chat-timestamp pull-left">${item.datetime}</span>
                                                </div>
                                                <img class="direct-chat-img" src="${full_website_address}/images/?for=employees&id=${item.from_user}">
                                                <div class="direct-chat-text">
                                                    ${item.msg_text}
                                                </div>
                                            </div>`;
                                            

                        }

                    });

                }

                // Show the chat box
                var chatBox = `<div class="chatBoxItem chatBoxForUser${userId}">
                            <input type="hidden" value=${userId} class="userId">
                            <div class="header">
                                <img width='40px' height='40px' src='${full_website_address}/images/?for=employees&id=${userId}' class='img-circle' />
                                <div class="card" >
                                    <p class="name">${data.name}</p>
                                    <p>${data.position}</p>
                                </div>
                                <div class="action">
                                    <span class="close-chat">
                                        <i class="fa fa-close"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="direct-chat direct-chat-warning">
                            
                                <div class="direct-chat-messages">
                                    ${latestMsg}
                                </div>
                            </div>
                            <div class="composer">
                                <label for="chatAttachment${userId}">
                                    <span title="Attach file" class="attachment">
                                        <i class="fa fa-plus"></i>
                                    </span>
                                </label>
                                <input style="display: none;" type="file" class="chat-attachment" id="chatAttachment${userId}">
                                <input type="text" placeholder="Type message and press enter to send ..." class="form-control message-composer">
                            </div>
                        </div>`;

                $(".chatBox").prepend(chatBox).ready( function() {

                    var container = $(`.chatBoxForUser${userId}`).find(".direct-chat-messages");

                    var chatHeight = $(container).get(0).scrollHeight;
                    $(container).animate({ scrollTop: chatHeight }, 0);
                    

                    // Focus the composer
                    $(container).closest(".chatBox").find(".message-composer").focus();

                });

            }

        });

    },


    send: function(msg, toUserId) {

        wss.send(JSON.stringify({
            "type"   : "message",
            "toUser" : toUserId,
            "msg"    : msg
        }));

    }

}