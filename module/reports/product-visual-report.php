<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?php echo __("Product Visual Report"); ?>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
        <div class="row">

            <div class="col-md-7">
                <?php load_product_filters("Visual"); ?>
            </div>
            <div class="col-md-5 row">

                <div class="col-md-6">
                    <input type="text" name="searchVisualProduct" id="searchVisualProduct" placeholder="Product Name...." class="form-control">
                </div>

                <div class="col-md-6 row">
                    <select name="sortByVisualProduct" id="sortByVisualProduct" class="form-control">
                        <option value="">Sorty By...</option>
                        <option value="1">Best Selling</option>
                        <option value="2">Low Stock</option>
                        <option value="3">High Stock</option>
                    </select>
                </div>

            </div>
            <!-- col-md-12-->
        </div>
        <!-- row-->

        <div class="row">

            <div class="col-md-12">

                <div id="productVisualList" style="margin-top: 10px" class="box box-success">

                    <div id="productVisualListContainer" style="margin-top: 10px" class="box-body">
                        <!-- Here the products will be showen -->



                    </div>

                </div>

            </div>

        </div>

    </section> <!-- Main content End tag -->
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<script>
    

    $(function() {

        showVisualProduct({
            category: $("#productCategoryFilterVisual").val(),
            brand: $("#productBrandFilterVisual").val(),
            edition: $("#productEditionFilterVisual").val(),
            generic: $("#productGenericFilterVisual").val(),
            author: $("#productAuthorFilterVisual").val(),
            terms: $("#searchVisualProduct").val(),
            sort: $("#sortByVisualProduct").val()
        });

    });


    $(document).on("change", "#productCategoryFilterVisual, #productBrandFilterVisual, #productEditionFilterVisual, #productGenericFilterVisual, #productAuthorFilterVisual, #searchVisualProduct, #sortByVisualProduct", function() {

        showVisualProduct({
            category: $("#productCategoryFilterVisual").val(),
            brand: $("#productBrandFilterVisual").val(),
            edition: $("#productEditionFilterVisual").val(),
            generic: $("#productGenericFilterVisual").val(),
            author: $("#productAuthorFilterVisual").val(),
            terms: $("#searchVisualProduct").val(),
            sort: $("#sortByVisualProduct").val()
        });

    });
    
</script>