<?php

/************************** Add new Category **********************/
if(isset($_GET['page']) and $_GET['page'] == "newCategory") {

  // Include the modal header
  modal_header("New Category", full_website_address() . "/xhr/?module=products&page=addNewCategory");

  $categoryName = isset($_GET["val"]) ? $_GET["val"] : "";
  
  ?>
    <div class="box-body">
      
      <div class="form-group">
        <label class="required" for="categoryName"><?= __("Category Name:"); ?></label>
        <input type="text" name="categoryName" value="<?php echo $categoryName; ?>" id="categoryName" class="form-control">
      </div>
      <div class="form-group">
        <label for="shopId"><?= __("Shop:"); ?> </label>
        <i data-toggle="tooltip" data-placement="right" title="<?= __("In which shop the category will appears. If keep empty the category will appears on all shops."); ?>" class="fa fa-question-circle"></i>
        
        <select name="shopId" id="shopId" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=shopList" style="width: 100%;">
          <option value=""><?= __("Select Shop"); ?>....</option>
        </select>
        
      </div>
            
    </div>
    <!-- /Box body-->

  <?php

  // Include the modal footer
  modal_footer();

}


/************************** Add new Category **********************/
if(isset($_GET['page']) and $_GET['page'] == "addNewCategory") {

  if(empty($_POST["categoryName"])) {
    return _e("Please enter category name.");
  }
  
  $returnMsg = easyInsert(
      "product_category", // Table name
      array( // Fileds Name and value
          "category_name"     => $_POST["categoryName"],
          "category_shop_id"  => empty($_POST["shopId"]) ? NULL : $_POST["shopId"]
      ),
      array( // No duplicate allow.
          "category_name"   => $_POST["categoryName"]
      )
  );

  if($returnMsg === true) {
      _s("New category added successfully.");
  } else {
      _e($returnMsg);
  }

}


/*************************** Product Category List ***********************/
if(isset($_GET['page']) and $_GET['page'] == "productCategoryList") {
    
  $requestData = $_REQUEST;
  $getData = [];

  // List of all columns name
  $columns = array(
      "category_name",
      "shop_name"
  );
  
  // Count Total recrods
  $totalFilteredRecords = $totalRecords = easySelectA(array(
        "table" => "product_category",
        "fields" => "count(*) as totalRow",
        "where" => array(
            "is_trash = 0"
        )
    ))["data"][0]["totalRow"];

  if($requestData['length'] == -1) {
    $requestData['length'] = $totalRecords;
  }

  if(!empty($requestData["search"]["value"])) {  // get data with search
      
      $getData = easySelect(
          "product_category",
          "category_id, category_name, shop_name",
          array(
              "left join {$table_prefeix}shops on category_shop_id = shop_id"
          ),
          array (
              "{$table_prefeix}product_category.is_trash = 0 and category_name LIKE" => $requestData['search']['value'] . "%",
              " OR shop_name LIKE" => $requestData['search']['value'] . "%"
          ),
          array (
              $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
          ),
          array (
              "start" => $requestData['start'],
              "length" => $requestData['length']
          )
      );

      $totalFilteredRecords = $getData ? $getData["count"] : 0;

  } else { // Get data withouth search

      $getData = easySelect(
          "product_category",
          "category_id, category_name, shop_name",
          array(
              "left join {$table_prefeix}shops on category_shop_id = shop_id"
          ),
          array("{$table_prefeix}product_category.is_trash = 0"),
          array (
              $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
          ),
          array (
              "start" => $requestData['start'],
              "length" => $requestData['length']
          )
      );

  }

  $allData = [];
  // Check if there have more then zero data
  if(isset($getData['count']) and $getData['count'] > 0) {
      
      foreach($getData['data'] as $key => $value) {
          $allNestedData = [];
          $allNestedData[] = $value["category_name"];
          $allNestedData[] = $value["shop_name"];
          // The action button
          $allNestedData[] = '<div class="btn-group">
                                  <button type="button" class="btn btn-xs btn-flat btn-primary dropdown-toggle" data-toggle="dropdown">
                                  action
                                  <span class="caret"></span>
                                  <span class="sr-only">Toggle Dropdown</span>
                                  </button>
                                  <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                  <li><a data-toggle="modal" href="'. full_website_address() .'/xhr/?icheck=false&module=products&page=editCategory&id='. $value["category_id"] .'"  data-target="#modalDefault"><i class="fa fa-edit"></i> Edit</a></li>
                                  <li><a class="deleteEntry" href="'. full_website_address() . '/xhr/?module=products&page=deleteCategory" data-to-be-deleted="'. $value["category_id"] .'"><i class="fa fa-minus-circle"></i> Delete</a></li>
                                  </ul>
                              </div>';
          
          $allData[] = $allNestedData;
      }
  }
  

  $jsonData = array (
      "draw"              => intval( $requestData['draw'] ),
      "recordsTotal"      => intval( $totalRecords ),
      "recordsFiltered"   => intval( $totalFilteredRecords ),
      "data"              => $allData
  );
  
  // Encode in Json Formate
  echo json_encode($jsonData); 
  
}


/***************** Delete Product Category ****************/
// Delete Group
if(isset($_GET['page']) and $_GET['page'] == "deleteCategory") {

  $deleteData = easyDelete(
      "product_category",
      array(
          "category_id" => $_POST["datatoDelete"]
      )
  );

  if($deleteData === true) {
      echo 1;
  } 

}


/************************** Edit Category **********************/
if(isset($_GET['page']) and $_GET['page'] == "editCategory") {

  $selectProductCategory = easySelect(
    "product_category",
    "category_id, category_name, shop_id, shop_name",
    array(
        "left join {$table_prefeix}shops on category_shop_id = shop_id"
    ),
    array(
        "category_id" => $_GET['id']
    )
  );

  $productCategory = $selectProductCategory["data"][0];

  // Include the modal header
  modal_header("Edit Category", full_website_address() . "/xhr/?module=products&page=updateCategory");
  
  ?>
    <div class="box-body">
      
      <div class="form-group">
        <label class="required" for="categoryName"><?= __("Category Name:"); ?></label>
        <input type="text" name="categoryName" value="<?php echo $productCategory["category_name"]; ?>" id="categoryName" class="form-control">
      </div>
      <div class="form-group">
        <label for="shopId"><?= __("Shop:"); ?> </label>
        <i data-toggle="tooltip" data-placement="right" title="<?= __("In which shop the category will appears. If keep empty the category will appears on all shops."); ?>" class="fa fa-question-circle"></i>
        
        <select name="shopId" id="shopId" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=shopList" style="width: 100%;">
          <option value="<?php echo $productCategory["shop_id"]; ?>"><?php echo $productCategory["shop_name"]; ?></option>  
        </select>
        <input type="hidden" name="category_id" value="<?php echo htmlentities($_GET['id']); ?>">
      </div>
            
    </div>
    <!-- /Box body-->

  <?php

  // Include the modal footer
  modal_footer();

}


//*******************************  Update Product Category ******************** */
if(isset($_GET['page']) and $_GET['page'] == "updateCategory") {

  if(empty($_POST["categoryName"])) {
    return _e("Please enter category name.");
  }
 
  // Update Other Information
  $updateCategory = easyUpdate(
      "product_category",
      array(
          "category_name"     => $_POST["categoryName"],
          "category_shop_id"  => empty($_POST["shopId"]) ? NULL : $_POST["shopId"]
      ),
      array(
          "category_id" => $_POST["category_id"]
      )
  );

  if($updateCategory === true) {
      _s("Category successfully updated.");
  } else {
      _e($updateCategory);
  }

}

/******************** Add new Products *******************/
if(isset($_GET['page']) and $_GET['page'] == "newProduct") {

    //print_r($_POST);
    //exit();

    if(empty($_POST["productCode"])) {
        return _e("Please enter product code.");
    } elseif(empty($_POST["productName"])) {
        return _e("Please enter product name");
    } elseif(empty($_POST["productType"])) {
        return _e("Please select product type");
    } elseif( $_POST["productType"] == "Variable" and !isset($_POST["product_variation"]) ) {
        return _e("Please select at least one variation for variable product");
    } elseif( ( $_POST["productType"] === "Grouped" or $_POST["productType"] === "Bundle" )  and !isset($_POST["bgProductID"]) or ( isset($_POST["bgProductID"]) and count($_POST["bgProductID"]) < 2 ) ) {
        return _e("Please select at least two products for Grouped or Bundle product.");
    } elseif(empty($_POST["productCategory"])) {
        return _e("Please select product category.");
    } elseif( $_POST["productType"] !== "Variable" and strlen($_POST["productSalePrice"]) < 1) {
        return _e("Please enter sale price.");
    } elseif( $_POST["productType"] !== "Variable" and strlen($_POST["productPurchasePrice"]) < 1) {
        return _e("Please enter purchase price.");
    } elseif( !empty($_POST["productIntitalStock"]) and !isset($_SESSION["wid"]) ) {
        return _e("Sorry! no default warehouse found for this user. Please add a default warehouse");
    }

    // For bundle product it is not allow to add bundle or the product which have sub product in the list
    if( $_POST["productType"] === "Bundle" ) {

        // Check, are there any bundle product or product which have sub product
        $allSubProductId = implode(",", $_POST["bgProductID"]);
        $checkSubProduct = easySelectA(array(
            "table"     => "products",
            "fields"    => "product_name",
            "where"     => array(
                "product_id in($allSubProductId) and (has_sub_product = 1 or product_type = 'Bundle') "
            )
        ));

        // If there are any sub product then return error
        if($checkSubProduct !== false) {

            return _e("Sorry! <b>{$checkSubProduct['data'][0]['product_name']}</b> is a bundle or has sub product. The product, which is bundle or have sub product can not be added in bundle product.");

        }

    }

    // Start the mysql Transaction
    runQuery("START TRANSACTION;");

    // Upload the image and store upload information in $productPhoto variable
    $productPhoto = NULL;
    if($_FILES["productPhoto"]["size"] > 0) {

        $productPhoto = easyUpload($_FILES["productPhoto"], "products/{$_POST["productCode"]}", "main__" . $_POST["productCode"]);

        if(!isset($productPhoto["success"])) {
            return _e($productPhoto);
        } else {
            $productPhoto = $productPhoto["fileName"];
        }
        
    }
  
    $insertProduct = easyInsert(
        "products", // Table name
        array( // Fileds Name and value
            "product_code"                  => $_POST["productCode"],
            "product_name"                  => $_POST["productName"],
            "product_type"                  => $_POST["productType"],
            "product_group"                 => empty($_POST["productGroupSelect"]) ? NULL : $_POST["productGroupSelect"],
            "product_description"           => $_POST["productDescription"],
            "product_edition"               => NULL, //$_POST["productEdition"],
            "product_variations"            => isset($_POST["product_attribute"]) ? serialize($_POST["product_attribute"]) : NULL,
            "product_purchase_price"        => $_POST["productPurchasePrice"],
            "product_sale_price"            => $_POST["productSalePrice"],
            "product_distributor_discount"  => $_POST["productDistributorDiscount"],
            "product_wholesaler_discount"   => $_POST["productWholesalerDiscount"],
            "product_retailer_discount"     => $_POST["productRetailerDiscount"],
            "product_consumer_discount"     => $_POST["productConsumerDiscount"],
            "product_weight"                => $_POST["productWeight"],
            "product_height"                => $_POST["productHeight"],
            "product_width"                 => $_POST["productWidth"],
            "product_photo"                 => $productPhoto,
            "product_category_id"           => $_POST["productCategory"],
            "product_brand_id"              => isset($_POST["productBrandSelect"]) ? $_POST["productBrandSelect"] : NULL,
            "product_generic"               => isset($_POST["productGenericSelect"]) ? $_POST["productGenericSelect"] : NULL,
            "product_published_date"        => isset($_POST["productPublishedDate"]) ? $_POST["productPublishedDate"] : NULL,
            "product_pages"                 => isset($_POST["productTotalPages"]) ? $_POST["productTotalPages"] : NULL,
            "product_isbn"                  => isset($_POST["productISBN"]) ? $_POST["productISBN"] : NULL,
            "product_alert_qnt"             => empty($_POST["alertQuantity"]) ? 0 : $_POST["alertQuantity"],
            "product_packet_qnt"            => empty($_POST["packetQuantity"]) ? 0 : $_POST["packetQuantity"],
            "product_initial_stock"         => empty($_POST["productIntitalStock"]) ? 0 : $_POST["productIntitalStock"],
            "product_add_by"                => $_SESSION["uid"],
            "has_expiry_date"               => $_POST["productHasExpiryDate"],
            "is_disabled"                   => $_POST["productIsDiscontinued"],
            "maintain_stock"                => $_POST["maintainStockInventory"]
        ),
        array( // No duplicate allow.
            "product_code"   => $_POST["productCode"]
        ),
        true
        
    );


    // check if the product has been successfully inserted
    if(isset($insertProduct["status"]) and $insertProduct["status"] === "success") {


        /**
         * If the product is grouped or bundle then insert the grouped/ bundle product
         */
        if( $_POST["productType"] === "Grouped" or $_POST["productType"] === "Bundle" ) {

            // Insert Bundle/ Sub product
            $insertSubProduct = "INSERT INTO {$table_prefeix}bg_product_items(
                bg_product_id,
                bg_item_product_id,
                bg_product_price,
                bg_product_qnt
            ) VALUES";

            foreach($_POST["bgProductID"] as $pkey => $bgProductId) {

                $insertSubProduct .= "(
                    '{$insertProduct["last_insert_id"]}',
                    '{$bgProductId}',
                    '". $_POST["bgProductSalePrice"][$pkey] ."',
                    '". $_POST["bgProductQnt"][$pkey] ."'
                ),";

            }

            runQuery(substr_replace($insertSubProduct, ";", -1, 1));
            
        }

        // Insert intital stock
        $initalStockEntry = "INSERT INTO {$table_prefeix}product_stock (
            stock_type,
            stock_entry_date,
            stock_warehouse_id,
            stock_product_id,
            stock_item_price,
            stock_item_qty,
            stock_item_subtotal,
            stock_created_by,
            stock_item_description
        ) VALUES ";

        // Intital Stock for Main product
        // If there is no variable product then add initial stock in main product
        if( !empty($_POST["productIntitalStock"]) and !isset($_POST["product_variation"]) ) {

            $initalStockEntry .= "(
                'initial',
                '". date("Y-m-d") ."'
                '{$_SESSION["wid"]}',
                '{$insertProduct["last_insert_id"]}',
                '". safe_input($_POST["productPurchasePrice"]) ."',
                '". safe_input($_POST["productIntitalStock"]) ."',
                '". safe_input($_POST["productIntitalStock"]) * safe_input($_POST["productPurchasePrice"]) ."',
                '{$_SESSION['uid']}',
                'Added on product entry'
            ),";
        }

        // Product Variation
        if($_POST["productType"] === "Variable" and isset($_POST["product_variation"]) ) {

            $variation = [];
            // Generate the variation
            foreach($_POST["product_variation"] as $pvname => $pv) {
                foreach($pv as $key => $value) {
                    $variation[$key][$pvname] = $value;
                }
            }

            // Generate Product variation photo
            $variationPhoto = [];
            foreach($_FILES["productVariationPhoto"] as $vpKey => $vp) {
                foreach($vp as $photoKey => $photoVal) {
                    $variationPhoto[$photoKey][$vpKey] = $photoVal;
                }
            }

            // Insert variable product
            $insertVariableProduct = "INSERT INTO {$table_prefeix}products(
                product_code,
                product_name,
                product_group,
                product_parent_id,
                product_type,
                product_description,
                product_edition,
                product_unit,
                product_variations,
                product_purchase_price,
                product_sale_price,
                product_distributor_discount,
                product_wholesaler_discount,
                product_retailer_discount,
                product_consumer_discount,
                product_weight,
                product_height,
                product_width,
                product_photo,
                product_category_id,
                product_brand_id,
                product_initial_stock,
                product_add_by,
                has_expiry_date,
                has_sub_product,
                is_disabled,
                maintain_stock
            ) VALUES ";


            // Product meta
            $productMeta = "INSERT INTO {$table_prefeix}product_meta(
                product_id,
                meta_type,
                meta_key,
                meta_value
            ) VALUES ";


            foreach($variation as $vKey => $pv) {

                // Upload product variation image
                $productVariationPhoto = NULL;
                if($variationPhoto[$vKey]["size"] > 0) {

                    $productPhoto = easyUpload($variationPhoto[$vKey], "products/{$_POST["productCode"]}", join("-", $pv) . "_" . $_POST["productVariationCode"][$vKey] );

                    if(!isset($productPhoto["success"])) {
                        
                        //return _e($productPhoto);
                        // Insert log if there are any error while uploading photo
                        create_log($productPhoto, debug_backtrace()[0] );

                    } else {
                        $productVariationPhoto = $productPhoto["fileName"];
                    }
                    
                }

                // Delete the units variation as of we do not need it in product name
                $pv_without_units = $pv;
                unset($pv_without_units["Units"]);

                $variationPurchasePirce =  empty($_POST["productVariationPurchasePrice"][$vKey]) ? 0 : safe_input($_POST["productVariationPurchasePrice"][$vKey]);
                $variationSalePirce =  empty($_POST["productVariationSalePrice"][$vKey]) ? 0 : safe_input($_POST["productVariationSalePrice"][$vKey]);
                
                $insertVariableProduct .= "
                (
                    '". safe_input($_POST["productVariationCode"][$vKey]) ."',
                    '". safe_input($_POST["productName"]) . ( empty($pv_without_units) ? "" : ' - ' . join(", ", $pv_without_units) ) ."',
                    '". safe_input($_POST["productGroupSelect"]) ."',
                    '{$insertProduct["last_insert_id"]}',
                    'Child',
                    '". safe_input($_POST["productVariationDescription"][$vKey]) ."',
                    ". ( isset($_POST["product_variation"]["Editions"][$vKey]) ?  "'".safe_input($_POST["product_variation"]["Editions"][$vKey])."'" : "NULL") .",
                    ". ( isset($_POST["product_variation"]["Units"][$vKey]) ?  "'".safe_input($_POST["product_variation"]["Units"][$vKey])."'" : "NULL") .",
                    '". safe_input(serialize($pv_without_units)) ."',
                    '{$variationPurchasePirce}',
                    '{$variationSalePirce}',
                    '". safe_input($_POST["productDistributorVariationDiscount"][$vKey]) ."',
                    '". safe_input($_POST["productWholesalerVariationDiscount"][$vKey]) ."',
                    '". safe_input($_POST["productRetailerVariationDiscount"][$vKey]) ."',
                    '". safe_input($_POST["productConsumerVariationDiscount"][$vKey]) ."',
                    '". safe_input($_POST["productVariationWeight"][$vKey]) ."',
                    '". safe_input($_POST["productVariationHeight"][$vKey]) ."',
                    '". safe_input($_POST["productVariationWidth"][$vKey]) ."',
                    '{$productVariationPhoto}',
                    '". safe_input($_POST["productCategory"]) ."',
                    ". ( isset($_POST["productBrandSelect"]) ?  "'".safe_input($_POST["productBrandSelect"])."'" : "NULL") .",
                    '". ( empty($_POST["productVariationIntitalStock"][$vKey]) ? 0 : safe_input($_POST["productVariationIntitalStock"][$vKey]) ) ."',
                    '{$_SESSION["uid"]}',
                    '". safe_input($_POST["productHasExpiryDate"]) ."',
                    '". safe_input($_POST["productVariationHasSubProduct"][$vKey]) ."',
                    '". safe_input($_POST["productIsDiscontinued"]) ."',
                    '". safe_input($_POST["maintainStockInventory"]) ."'
                ),";

                /** Add sub product for variable product
                 * Adding sub/ bundle product for variable product product is not required
                 * 
                 * We plan to add sub product in another interface called Sub Product Attachment.
                 * 
                 * Now commenting, will delete later
                 */
                // if($_POST["productHasSubProduct"] === "1" ) {

                //     foreach($_POST["bgProductID"] as $pkey => $bgProductId) {

                //         // If there have any unit in this product variation
                //         // Then multiply the bgProductQnt with unit base qty
                //         if(isset($_POST["product_variation"]["Units"][$vKey] ) ) {

                //             $bgProductQty = "(select base_qnt * ". $_POST["bgProductQnt"][$pkey] ." from {$table_prefeix}product_units where unit_name = '". $_POST["product_variation"]["Units"][$vKey] ."' )";

                //         } else {
                            
                //             $bgProductQty = "'".$_POST["bgProductQnt"][$pkey]."'";

                //         }

                //         $insertSubProduct .= "(
                //             (select product_id from {$table_prefeix}products where product_code = '". safe_input($_POST["productVariationCode"][$vKey]) ."'),
                //             '{$bgProductId}',
                //             '". $_POST["bgProductSalePrice"][$pkey] ."',
                //             {$bgProductQty}
                //         ),";

                //     }
                    
                // }


                /**
                 * Set Intital Stock for product Varition
                 */
                if( !empty($_POST["productVariationIntitalStock"][$vKey]) ) {

                    $initalStockEntry .= "(
                        'initial',
                        '". date("Y-m-d") ."'
                        '{$_SESSION["wid"]}',
                        (select product_id from {$table_prefeix}products where product_code = '". safe_input($_POST["productVariationCode"][$vKey]) ."'),
                        '{$variationPurchasePirce}',
                        '". safe_input($_POST["productVariationIntitalStock"][$vKey]) ."',
                        '". safe_input($_POST["productVariationIntitalStock"][$vKey]) * $variationPurchasePirce ."',
                        '{$_SESSION['uid']}',
                        'Added on product entry'
                    ),";
                }


                /**
                 * Product meta generation
                 */
                foreach($pv as $attributeName => $variationName) {

                    $productMeta .= "
                    (
                        (select product_id from {$table_prefeix}products where product_code = '". safe_input($_POST["productVariationCode"][$vKey]) ."'),
                        'Variation',
                        '". $attributeName ."',
                        '". $variationName ."'
                    ),";

                    // Default Variation
                    if( isset($_POST["defaultVariation"]) and $_POST["defaultVariation"] === $_POST["productVariationCode"][$vKey] ) {

                        $productMeta .= "
                        (
                            '{$insertProduct["last_insert_id"]}',
                            'Default-Variation',
                            '". $attributeName ."',
                            '". $variationName ."'
                        ),";
                        
                    }

                }

            }


            // Insert variable products
            runQuery(substr_replace($insertVariableProduct, ";", -1, 1));

            // Insert product meta
            runQuery(substr_replace($productMeta, ";", -1, 1));


        }


        // Insert intital Product Stock
        // If there is any initital stock set either variation or main product
        if( !empty($_POST["productIntitalStock"]) or (  isset($_POST["productVariationIntitalStock"]) and  array_sum($_POST["productVariationIntitalStock"]) > 0 ) ) {
            
            runQuery(substr_replace($initalStockEntry, ";", -1, 1));

        }
        

        // Insert Author
        if( isset($_POST["bookAuthor"]) and count($_POST["bookAuthor"]) > 0 ) {

            foreach($_POST["bookAuthor"] as $bakey => $baID) {

                easyInsert(
                    "product_author_relations",
                    array(
                    "product_id"        => $insertProduct["last_insert_id"],
                    "product_author_id" => $baID
                    )
                );

            }
            
        }

        if( !empty($conn->get_all_error)  ) {
    
            _e( $conn->get_all_error[0]. " Please check the error log for more information.");

            // If there have any error then rollback/undo the data
            runQuery("ROLLBACK;");
        
        } else {
            
            // If there have not any error then commit/save the data permanently
            runQuery("COMMIT;");
            _s("New product added successfully.");

        }


    } else {

        _e($insertProduct);

    }

}




/******************** Attach Sub Product *******************/
if(isset($_GET['page']) and $_GET['page'] == "attachSubProduct") {


    $selectProduct = easySelectA(array(
        "table"     => "products",
        "fields"    => "product_id, product_name, has_sub_product, product_unit",
        "where"     => array(
            "has_sub_product = 1 and product_id"    => $_POST["mainProduct"]
        )
    ));

    if($selectProduct === false ) {

        echo _e("Sorry! this product is not eligible to link sub product.");
        
    } else {

        // Check, are there any bundle product or product which have sub product
        $allSubProductId = implode(",", $_POST["bgProductID"]);
        $checkSubProduct = easySelectA(array(
            "table"     => "products",
            "fields"    => "product_name",
            "where"     => array(
                "product_id in($allSubProductId) and (has_sub_product = 1 or product_type = 'Bundle') "
            )
        ));

        // If there are any sub product then return error
        if($checkSubProduct !== false) {

            _e("Sorry! <b>{$checkSubProduct['data'][0]['product_name']}</b> is a bundle or has sub product. The product, which is bundle or have sub product can not be attached or linked.");

        } else {


            // Start the mysql Transaction
            runQuery("START TRANSACTION;");


            $product = $selectProduct["data"][0];

            // Delete Privous bg product
            easyPermDelete(
                "bg_product_items",
                array(
                    "bg_product_id" => $_POST["mainProduct"]
                )
            );


            // Insert Bundle/ Sub product
            $insertSubProduct = "INSERT INTO {$table_prefeix}bg_product_items(
                bg_product_id,
                bg_item_product_id,
                bg_product_price,
                bg_product_qnt
            ) VALUES";

             
            foreach($_POST["bgProductID"] as $pkey => $bgProductId) {

                // If there have any unit in this product
                // Then multiply the bgProductQnt with unit base qty
                if( !empty($product['product_unit']) ) {

                    $bgProductQty = "(select base_qnt * ". $_POST["bgProductQnt"][$pkey] ." from {$table_prefeix}product_units where unit_name = '{$product['product_unit']}' )";

                } else {
                    
                    $bgProductQty = "'{$_POST["bgProductQnt"][$pkey]}'";

                }

                $insertSubProduct .= "(
                    '{$product['product_id']}',
                    '{$bgProductId}',
                    '". $_POST["bgProductSalePrice"][$pkey] ."',
                    {$bgProductQty}
                ),";
                

            }


            // Run query to insert sub products
            runQuery(substr_replace($insertSubProduct, ";", -1, 1));


            // Check if there is any error on inserting data
            if( !empty($conn->get_all_error)  ) {
    
                _e( $conn->get_all_error[0]. " Please check the error log for more information.");
    
                // If there are any error then rollback/undo the data
                runQuery("ROLLBACK;");
            
            } else {
                
                // If there have not any error then commit/save the data permanently
                runQuery("COMMIT;");
                _s("Successfully updatted");
    
            }
            

        }


    }


}




/******************** Add new Products *******************/
if(isset($_GET['page']) and $_GET['page'] == "updateProduct") {

   // print_r($_POST);
    //exit();

    if(empty($_POST["productCode"])) {
        return _e("Please enter product code.");
    } elseif(empty($_POST["productName"])) {
        return _e("Please enter product name");
    } elseif(empty($_POST["productType"])) {
        return _e("Please select product type");
    } elseif( $_POST["productType"] == "Variable" and (!isset($_POST["product_variation"]) and !isset($_POST["edit"]["variation_product_id"]) ) ) {
        return _e("Please select at least one variation for variable product");
    } elseif( ( $_POST["productType"] === "Grouped" or $_POST["productType"] === "Bundle" )  and !isset($_POST["bgProductID"]) or ( isset($_POST["bgProductID"]) and count($_POST["bgProductID"]) < 2 ) ) {
        return _e("Please select at least two products for Sub/Grouped or Bundle product.");
    } elseif(empty($_POST["productCategory"])) {
        return _e("Please select product category.");
    } elseif( $_POST["productType"] !== "Variable" and strlen($_POST["productSalePrice"]) < 1) {
        return _e("Please enter sale price.");
    } elseif( $_POST["productType"] !== "Variable" and strlen($_POST["productPurchasePrice"]) < 1) {
        return _e("Please enter purchase price.");
    } elseif( !empty($_POST["productIntitalStock"]) and $_POST["productIntitalStock"] > 0 and !isset($_SESSION["wid"]) ) {
        return _e("Sorry! no default warehouse found for this user. Please add a default warehouse");
    }


    // For bundle product it is not allow to add bundle or the product which have sub product in the BG list
    if( $_POST["productType"] === "Bundle" ) {

        // Check, are there any bundle product or product which have sub product
        $allSubProductId = implode(",", $_POST["bgProductID"]);
        $checkSubProduct = easySelectA(array(
            "table"     => "products",
            "fields"    => "product_name",
            "where"     => array(
                "product_id in($allSubProductId) and (has_sub_product = 1 or product_type = 'Bundle') "
            )
        ));

        // If there are any sub product then return error
        if($checkSubProduct !== false) {

            return _e("Sorry! <b>{$checkSubProduct['data'][0]['product_name']}</b> is a bundle or has sub product. The product, which is bundle or have sub product can not be added in bundle product.");

        }

    }

    

    // Start the mysql Transaction
    runQuery("START TRANSACTION;");

    // update the product photo
    if($_FILES["productPhoto"]["size"] > 0) {

        $productPhoto = easyUpload($_FILES["productPhoto"], "products/{$_POST["productCode"]}", "main__" . $_POST["productCode"]);

        if(!isset($productPhoto["success"])) {
            return _e($productPhoto);
        } else {
            $productPhoto = $productPhoto["fileName"];
        }

        // update product photo
        $updateProduct = easyUpdate(
            "products", // Table name
            array( // Fileds Name and value
                "product_photo"           => $productPhoto
            ),
            array( 
                "product_id"   => $_POST["product_id"]
            )
        );
        
    }


    $updateProduct = easyUpdate(
        "products", // Table name
        array( // Fileds Name and value
            "product_code"                  => $_POST["productCode"],
            "product_name"                  => $_POST["productName"],
            "product_type"                  => $_POST["productType"],
            "product_group"                 => empty($_POST["productGroupSelect"]) ? NULL : $_POST["productGroupSelect"],
            "product_description"           => $_POST["productDescription"],
            // "product_edition"               => $_POST["productEdition"], // Product edition no need to update
            "product_variations"            => isset($_POST["product_attribute"]) ? serialize($_POST["product_attribute"]) : NULL,
            "product_purchase_price"        => $_POST["productPurchasePrice"],
            "product_sale_price"            => $_POST["productSalePrice"],
            "product_distributor_discount"  => $_POST["productDistributorDiscount"],
            "product_wholesaler_discount"   => $_POST["productWholesalerDiscount"],
            "product_retailer_discount"     => $_POST["productRetailerDiscount"],
            "product_consumer_discount"     => $_POST["productConsumerDiscount"],
            "product_weight"                => $_POST["productWeight"],
            "product_height"                => $_POST["productHeight"],
            "product_width"                 => $_POST["productWidth"],
            "product_category_id"           => $_POST["productCategory"],
            "product_brand_id"              => isset($_POST["productBrandSelect"]) ? $_POST["productBrandSelect"] : NULL,
            "product_generic"               => isset($_POST["productGenericSelect"]) ? $_POST["productGenericSelect"] : NULL,
            "product_published_date"        => isset($_POST["productPublishedDate"]) ? $_POST["productPublishedDate"] : NULL,
            "product_pages"                 => isset($_POST["productTotalPages"]) ? $_POST["productTotalPages"] : NULL,
            "product_isbn"                  => isset($_POST["productISBN"]) ? $_POST["productISBN"] : NULL,
            "product_alert_qnt"             => empty($_POST["alertQuantity"]) ? 0 : $_POST["alertQuantity"],
            "product_packet_qnt"            => empty($_POST["packetQuantity"]) ? 0 : $_POST["packetQuantity"],
            "product_initial_stock"         => empty($_POST["productIntitalStock"]) ? 0 : $_POST["productIntitalStock"],
            "product_update_by"             => $_SESSION["uid"],
            "has_expiry_date"               => $_POST["productHasExpiryDate"],
            "is_disabled"                   => $_POST["productIsDiscontinued"],
            "maintain_stock"                => $_POST["maintainStockInventory"]
        ),
        array( 
            "product_id"   => $_POST["product_id"]
        )
    );

    if($updateProduct === true) {

        
        /**
         * If the product is grouped or bundle then insert grouped or bundle item
         */
        if( $_POST["productType"] === "Grouped" or $_POST["productType"] === "Bundle" ) {

            // Delete Privous bg product
            easyPermDelete(
                "bg_product_items",
                array(
                    "bg_product_id" => $_POST["product_id"]
                )
            );


            // Insert Bundle/ Grouped product
            $insertSubProduct = "INSERT INTO {$table_prefeix}bg_product_items(
                bg_product_id,
                bg_item_product_id,
                bg_product_price,
                bg_product_qnt
            ) VALUES";

            foreach($_POST["bgProductID"] as $pkey => $bgProductId) {

                $insertSubProduct .= "(
                    '{$_POST["product_id"]}',
                    '{$bgProductId}',
                    '". $_POST["bgProductSalePrice"][$pkey] ."',
                    '". $_POST["bgProductQnt"][$pkey] ."'
                ),";

            }

            // Run query to insert BG product
            runQuery(substr_replace($insertSubProduct, ";", -1, 1));
            
        }

        // Check if there any product variation to edit
        if( isset($_POST["edit"]["variation_product_id"]) and count($_POST["edit"]["variation_product_id"]) > 0 ) {


            // Rebuild the array for product variation photo
            $updateVariationPhoto = [];
            foreach($_FILES["editProductVariationPhoto"] as $vpKey => $vp) {
                foreach($vp as $photoKey => $photoVal) {
                    $updateVariationPhoto[$photoKey][$vpKey] = $photoVal;
                }
            }

            // update product variation
            foreach($_POST["edit"]["variation_product_id"] as $vKey => $productId) {

                /**
                 * Generate variation for this product
                 * We need to regenerate the variation, because of if the product name is changed, 
                 * then we can not show the product name correctly
                 */
                $selectProductMeta = easySelectA(array(
                    "table" => "product_meta",
                    "fields" => "meta_value",
                    "where" => array(
                        "meta_key != 'Units' and product_id" => $productId
                    )
                ));
                
                $variations = [];
                if( $selectProductMeta !== false ) {
                    foreach($selectProductMeta["data"] as $item) {
                        array_push($variations, $item["meta_value"]);
                    }
                }


                // Update image for variation product
                if($updateVariationPhoto[$vKey]["size"] > 0) {

                    $uploadProductVariationPhoto = easyUpload($updateVariationPhoto[$vKey], "products/{$_POST["edit"]["productVariationCode"][$vKey]}", "main__" . $_POST["edit"]["productVariationCode"][$vKey]);

                    if(isset($uploadProductVariationPhoto["success"])) {

                        // update product photo
                        easyUpdate(
                            "products", // Table name
                            array( // Fileds Name and value
                                "product_photo"   => $uploadProductVariationPhoto["fileName"]
                            ),
                            array( 
                                "product_id"   => $productId
                            )
                        );

                    } else {

                        create_log($uploadProductVariationPhoto,  debug_backtrace()[0] );

                    }
                    
                }


                easyUpdate(
                    "products",
                    array(
                        "product_code"                  => $_POST["edit"]["productVariationCode"][$vKey],
                        "product_name"                  => $_POST["productName"] . ( empty($variations) ? "" : " - " . join(", ", $variations) ),
                        "product_group"                 => empty($_POST["productGroupSelect"]) ? NULL : $_POST["productGroupSelect"],
                        "product_variations"            => serialize($variations),
                        "product_description"           => $_POST["edit"]["productVariationDescription"][$vKey],
                        "product_purchase_price"        => $_POST["edit"]["productVariationPurchasePrice"][$vKey],
                        "product_sale_price"            => $_POST["edit"]["productVariationSalePrice"][$vKey],
                        "product_distributor_discount"  => $_POST["edit"]["productDistributorVariationDiscount"][$vKey],
                        "product_wholesaler_discount"   => $_POST["edit"]["productWholesalerVariationDiscount"][$vKey],
                        "product_retailer_discount"     => $_POST["edit"]["productRetailerVariationDiscount"][$vKey],
                        "product_consumer_discount"     => $_POST["edit"]["productConsumerVariationDiscount"][$vKey],
                        "product_weight"                => $_POST["edit"]["productVariationWeight"][$vKey],
                        "product_height"                => $_POST["edit"]["productVariationHeight"][$vKey],
                        "product_width"                 => $_POST["edit"]["productVariationWidth"][$vKey],
                        "product_category_id"           => $_POST["productCategory"],
                        "product_brand_id"              => isset($_POST["productBrandSelect"]) ? $_POST["productBrandSelect"] : NULL,
                        "product_initial_stock"         => empty($_POST["edit"]["productVariationIntitalStock"][$vKey]) ? 0 : $_POST["edit"]["productVariationIntitalStock"][$vKey] ,
                        "has_sub_product"               => $_POST["edit"]["productVariationHasSubProduct"][$vKey],
                        "has_expiry_date"               => $_POST["productHasExpiryDate"],
                        "maintain_stock"                => $_POST["maintainStockInventory"],
                        "is_disabled"                   => $_POST["productIsDiscontinued"],
                        "product_update_by"             => $_SESSION["uid"]
                    ),
                    array(
                        "product_id"    => $productId
                    )
                );

            }

        }

        // Insert new Product Variation on edit/update
        if($_POST["productType"] === "Variable" and isset($_POST["product_variation"]) ) {

            $variation = [];
            // Generate the variation
            foreach($_POST["product_variation"] as $pvname => $pv) {
                foreach($pv as $key => $value) {
                    $variation[$key][$pvname] = $value;
                }
            }

            // Format the array for product variation photo
            $variationPhoto = [];
            foreach($_FILES["productVariationPhoto"] as $vpKey => $vp) {
                foreach($vp as $photoKey => $photoVal) {
                    $variationPhoto[$photoKey][$vpKey] = $photoVal;
                }
            }

            // Insert variable product
            $insertVariableProduct = "INSERT INTO {$table_prefeix}products(
                product_code,
                product_name,
                product_group,
                product_parent_id,
                product_type,
                product_description,
                product_edition,
                product_unit,
                product_variations,
                product_purchase_price,
                product_sale_price,
                product_distributor_discount,
                product_wholesaler_discount,
                product_retailer_discount,
                product_consumer_discount,
                product_weight,
                product_height,
                product_width,
                product_photo,
                product_category_id,
                product_brand_id,
                product_initial_stock,
                product_add_by,
                has_expiry_date,
                has_sub_product,
                is_disabled,
                maintain_stock
            ) VALUES ";

            // Product meta
            $productMeta = "INSERT INTO {$table_prefeix}product_meta(
                product_id,
                meta_type,
                meta_key,
                meta_value
            ) VALUES ";


            // Insert intital stock
            $initalStockEntry = "INSERT INTO {$table_prefeix}product_stock (
                stock_type,
                stock_entry_date,
                stock_warehouse_id,
                stock_product_id,
                stock_item_price,
                stock_item_qty,
                stock_item_subtotal,
                stock_created_by,
                stock_item_description
            ) VALUES ";


            // Loop through variation
            foreach($variation as $vKey => $pv) {

                // Upload product variation image
                $productVariationPhoto = NULL;
                if($variationPhoto[$vKey]["size"] > 0) {

                    $productPhoto = easyUpload($variationPhoto[$vKey], "products/{$_POST["productCode"]}", join("-", $pv) . "_" . $_POST["productVariationCode"][$vKey] );

                    if(!isset($productPhoto["success"])) {
                        
                        //return _e($productPhoto);
                        create_log($productPhoto, debug_backtrace()[0] );

                    } else {
                        $productVariationPhoto = $productPhoto["fileName"];
                    }
                    
                }

                // Delete the units variation as of we do not need it in product name
                $pv_without_units = $pv;
                unset($pv_without_units["Units"]);

                $variationPurchasePirce =  empty($_POST["productVariationPurchasePrice"][$vKey]) ? 0 : safe_input($_POST["productVariationPurchasePrice"][$vKey]);
                $variationSalePirce =  empty($_POST["productVariationSalePrice"][$vKey]) ? 0 : safe_input($_POST["productVariationSalePrice"][$vKey]);
                
                $insertVariableProduct .= "
                (
                    '". safe_input($_POST["productVariationCode"][$vKey]) ."',
                    '". safe_input($_POST["productName"]) . ( empty($pv_without_units) ? "" : ' - ' . join(", ", $pv_without_units) ) ."',
                    '". safe_input($_POST["productGroupSelect"]) ."',
                    '{$_POST["product_id"]}',
                    'Child',
                    '". safe_input($_POST["productVariationDescription"][$vKey]) ."',
                    ". ( isset($_POST["product_variation"]["Editions"][$vKey]) ?  "'".safe_input($_POST["product_variation"]["Editions"][$vKey])."'" : "NULL") .",
                    ". ( isset($_POST["product_variation"]["Units"][$vKey]) ?  "'".safe_input($_POST["product_variation"]["Units"][$vKey])."'" : "NULL") .",
                    '". safe_input(serialize($pv_without_units)) ."',
                    '{$variationPurchasePirce}',
                    '{$variationSalePirce}',
                    '". safe_input($_POST["productDistributorVariationDiscount"][$vKey]) ."',
                    '". safe_input($_POST["productWholesalerVariationDiscount"][$vKey]) ."',
                    '". safe_input($_POST["productRetailerVariationDiscount"][$vKey]) ."',
                    '". safe_input($_POST["productConsumerVariationDiscount"][$vKey]) ."',
                    '". safe_input($_POST["productVariationWeight"][$vKey]) ."',
                    '". safe_input($_POST["productVariationHeight"][$vKey]) ."',
                    '". safe_input($_POST["productVariationWidth"][$vKey]) ."',
                    '{$productVariationPhoto}',
                    '". safe_input($_POST["productCategory"]) ."',
                    ". ( isset($_POST["productBrandSelect"]) ?  "'".safe_input($_POST["productBrandSelect"])."'" : "NULL") .",
                    '". ( empty($_POST["productVariationIntitalStock"][$vKey]) ? 0 : safe_input($_POST["productVariationIntitalStock"][$vKey]) ) ."',
                    '{$_SESSION["uid"]}',
                    '". safe_input($_POST["productHasExpiryDate"]) ."',
                    '". safe_input($_POST["productVariationHasSubProduct"][$vKey]) ."',
                    '". safe_input($_POST["productIsDiscontinued"]) ."',
                    '". safe_input($_POST["maintainStockInventory"]) ."'
                ),";


                /**
                 * Set Intital Stock for product Varition
                 */
                if( !empty($_POST["productVariationIntitalStock"][$vKey]) ) {

                    $initalStockEntry .= "(
                        'initial',
                        '". date("Y-m-d") ."',
                        '{$_SESSION["wid"]}',
                        (select product_id from {$table_prefeix}products where product_code = '". safe_input($_POST["productVariationCode"][$vKey]) ."'),
                        '{$variationPurchasePirce}',
                        '". safe_input($_POST["productVariationIntitalStock"][$vKey]) ."',
                        '". safe_input($_POST["productVariationIntitalStock"][$vKey]) * $variationPurchasePirce ."',
                        '{$_SESSION['uid']}',
                        'Added on product entry'
                    ),";
                }


                /**
                 * Product meta generation
                 */
                foreach($pv as $attributeName => $variationName) {

                    $productMeta .= "
                    (
                        (select product_id from {$table_prefeix}products where product_code = '". safe_input($_POST["productVariationCode"][$vKey]) ."'),
                        'Variation',
                        '". $attributeName ."',
                        '". $variationName ."'
                    ),";

                    // Default Variation
                    if( isset($_POST["defaultVariation"]) and $_POST["defaultVariation"] === $_POST["productVariationCode"][$vKey] ) {

                        $productMeta .= "
                        (
                            '{$insertProduct["last_insert_id"]}',
                            'Default-Variation',
                            '". $attributeName ."',
                            '". $variationName ."'
                        ),";
                        
                    }

                }

            }


            // Insert variable products
            runQuery(substr_replace($insertVariableProduct, ";", -1, 1));

            // Insert product meta
            runQuery(substr_replace($productMeta, ";", -1, 1));

        }


        // Insert intital Product Stock
        // If there is any initital stock on new variation
        // We can not insert any data for update initial stock
        if( isset($_POST["productVariationIntitalStock"]) and  array_sum($_POST["productVariationIntitalStock"]) > 0  ) {
            
            runQuery(substr_replace($initalStockEntry, ";", -1, 1));

        }


        // Update Author
        if( isset($_POST["bookAuthor"]) and count($_POST["bookAuthor"]) > 0 ) {

            // Delete privous author for this product
            easyPermDelete(
                "product_author_relations",
                array(
                    "product_id"  => $_POST["product_id"],
                )
            );

            foreach($_POST["bookAuthor"] as $bakey => $baID) {

                easyInsert(
                    "product_author_relations",
                    array(
                    "product_id"        => $_POST["product_id"],
                    "product_author_id" => $baID
                    )
                );

            }
            
        }



        if( !empty($conn->get_all_error)  ) {
    
            _e( $conn->get_all_error[0]. " Please check the error log for more information.");

            // If there have any error then rollback/undo the data
            runQuery("ROLLBACK;");
        
        } else {
            
            // If there have not any error then commit/save the data permanently
            runQuery("COMMIT;");
            _s("The product has been updated successfully.");

        }


    } else {

        _e($updateProduct);

    }

}


/*************************** Product List ***********************/
if(isset($_GET['page']) and $_GET['page'] == "productList") {
    
  $requestData = $_REQUEST;

  $getData = [];

  // List of all columns name
  $columns = array(
      "",
      "product_id",
      "product_name",
      "product_group",
      "product_generic",
      "product_edition",
      "category_name",
      "product_weight",
      "product_sale_price"
  );
  
  // Count Total recrods
  $totalFilteredRecords = $totalRecords = easySelectA(array(
        "table" => "products",
        "fields" => "count(*) as totalRow",
        "where" => array(
            "is_trash = 0 and product_type != 'Child'"
        )
    ))["data"][0]["totalRow"];

  if($requestData['length'] == -1) {
    $requestData['length'] = $totalRecords;
  }

  if(!empty($requestData["search"]["value"]) or !empty($requestData["columns"][5]['search']['value']) or !empty($requestData["columns"][6]['search']['value'])) {  // get data with search

        // If there are any edition to filter, we do not need product_type != 'Child' filter
        $productEditionFilter = !empty($requestData["columns"][5]['search']['value']) ? " product_edition = '{$requestData["columns"][5]['search']['value']}' " : " product_type != 'Child' ";
      
        $getData = easySelect(
            "products as product",
            "product_id, product_code, product_name, product_type, product_group, product_generic, product_description, round(product_purchase_price, 2) as product_purchase_price, 
            round(product_sale_price, 2) as product_sale_price, category_name, product_edition, has_sub_product",
            array (
            "left join {$table_prefeix}product_category on product_category_id = category_id"
            ),
            array (
                "product.is_trash = 0 and {$productEditionFilter} and (product_code LIKE '". safe_input($requestData['search']['value']) ."%' ",
                " OR product_name LIKE" => $requestData['search']['value'] . "%",
                " OR category_name LIKE" => $requestData['search']['value'] . "%",
                ")",
                " AND product_category_id" => $requestData["columns"][6]['search']['value'],
            ),
            array (
                $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
            ),
            array (
                "start" => $requestData['start'],
                "length" => $requestData['length']
            )
        );

      $totalFilteredRecords = $getData ? $getData["count"] : 0;

  } else { // Get data withouth search

        $getData = easySelect(
            "products as product",
            "product_id, product_code, product_name, product_group, product_generic, product_type, product_description, round(product_purchase_price, 2) as product_purchase_price, 
            round(product_sale_price, 2) as product_sale_price, category_name, product_edition, has_sub_product",
            array (
                "left join {$table_prefeix}product_category on product_category_id = category_id"
            ),
            array("product.is_trash = 0 and product_type != 'Child'"),
            array (
                $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
            ),
            array (
                "start" => $requestData['start'],
                "length" => $requestData['length']
            )
        );

  }

  $allData = [];
  // Check if there have more then zero data
  if(isset($getData['count']) and $getData['count'] > 0) {
      
      foreach($getData['data'] as $key => $value) {

            $subProductAttachment = "";
            if( $value["has_sub_product"] == 1 ) {
                $subProductAttachment = '<li><a href="'. full_website_address() .'/products/attach-sub-product/?pid='. $value["product_id"] .'"><i class="fa fa-link"></i> Link SubProduct</a></li>';
            }

          $allNestedData = [];
          $allNestedData[] = "";
          $allNestedData[] = $value["product_code"];
          $allNestedData[] = $value["product_name"];
          $allNestedData[] = $value["product_group"];
          $allNestedData[] = $value["product_generic"];
          $allNestedData[] = $value["product_edition"];
          $allNestedData[] = $value["category_name"];
          $allNestedData[] = "";
          $allNestedData[] = $value["product_purchase_price"] . " / " . $value["product_sale_price"];
          // The action button
          $allNestedData[] = '<div class="btn-group">
                                  <button type="button" class="btn btn-xs btn-flat btn-primary dropdown-toggle" data-toggle="dropdown">
                                  action
                                  <span class="caret"></span>
                                  <span class="sr-only">Toggle Dropdown</span>
                                  </button>
                                  <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                    <li><a href="'. full_website_address() .'/products/edit-product/?pid='. $value["product_id"] .'"><i class="fa fa-edit"></i> Edit</a></li>
                                    '. $subProductAttachment .'
                                    <li><a class="deleteEntry" href="'. full_website_address() . '/xhr/?module=products&page=deleteProduct" data-to-be-deleted="'. $value["product_id"] .'"><i class="fa fa-minus-circle"></i> Delete</a></li>
                                  </ul>
                              </div>';
          
          $allData[] = $allNestedData;
      }
  }
  

  $jsonData = array (
      "draw"              => intval( $requestData['draw'] ),
      "recordsTotal"      => intval( $totalRecords ),
      "recordsFiltered"   => intval( $totalFilteredRecords ),
      "data"              => $allData
  );
  
  // Encode in Json Formate
  echo json_encode($jsonData); 

}


/***************** Delete Product ****************/
if(isset($_GET['page']) and $_GET['page'] == "deleteProduct") {

    $deleteData = easyDelete(
        "products",
        array(
            "product_id" => $_POST["datatoDelete"]
        )
    );
    

    if($deleteData === true) {

        // Delete all childs/variation of this product
        easyDelete(
            "products",
            array(
                "product_parent_id" => $_POST["datatoDelete"]
            )
        );

        echo 1;

    } 

}

/***************** Delete Variation Product ****************/
if(isset($_GET['page']) and $_GET['page'] == "deleteVariationProduct") {

    $deleteVariationProduct = easyDelete(
        "products",
        array(
            "product_id" => $_POST["product_id"]
        )
    );
  
}


/************************** Add new Unit **********************/
if(isset($_GET['page']) and $_GET['page'] == "newUnit") {

  // Include the modal header
  modal_header("New Unit", full_website_address() . "/xhr/?module=products&page=addNewUnit");
  
  ?>
    <div class="box-body">
      
      <div class="form-group required">
        <label for="unitName"><?= __("Unit Name:"); ?></label>
        <i data-toggle="tooltip" data-placement="right" title="E.G: Dozen, Litter" class="fa fa-question-circle"></i>
        <input type="text" name="unitName" id="unitName" placeholder="E.G: Kilograms" class="form-control" required>
      </div>
      <div class="form-group required">
        <label for="unitShortname"><?= __("Unit Shortname:"); ?></label>
        <i data-toggle="tooltip" data-placement="right" title="E.G: kg for kilograms" class="fa fa-question-circle"></i>
        <input type="text" name="unitShortname" placeholder="E.G: kg"  id="unitShortname" class="form-control" required>
      </div>
      <div class="form-group">
          <label for="equal_unit_qnt"><?= __("Equal To:"); ?> </label>
          <div class="row">
              <div class="col-md-4">
                  <input type="number" name="equalUnitQnt" id="equal_unit_qnt" class="form-control">
              </div>
              <div class="col-md-8">
                  <select name="equalUnit" id="equalUnit" class=" form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=itemUnitList" style="width: 100%;">
                      <option value=""><?= __("Select unit"); ?></option>
                  </select>
              </div>
          </div>
          
      </div>
      <div class="form-group">
        <label for="unitDescription"><?= __("Unit Description"); ?></label>
        <textarea name="unitDescription" id="unitDescription" rows="3" class="form-control"></textarea>
      </div>
            
    </div>
    <!-- /Box body-->

  <?php

  // Include the modal footer
  modal_footer();

}


// Add new user group page
if(isset($_GET['page']) and $_GET['page'] == "addNewUnit") {

    if(empty($_POST["unitName"])) {
        return _e("Please enter unit name.");
    } else if(empty($_POST["unitShortname"])) {
        return _e("Please enter unit short name.");
    }

    // select the equal unit
    $base_qnt = 1;
    if( !empty($_POST["equalUnit"]) ) {

        $base_qnt = easySelectA(array(
            "table"     => "product_units",
            "fields"    => "base_qnt",
            "where"     => array(
                "unit_id"   => $_POST["equalUnit"]
            )
        ))["data"][0]["base_qnt"];

    }
  
    $returnMsg = easyInsert(
        "product_units", // Table name
        array( // Fileds Name and value
            "unit_name"         => $_POST["unitName"],
            "short_name"        => $_POST["unitShortname"],
            "equal_unit_id"     => empty($_POST["equalUnit"]) ? NULL : $_POST["equalUnit"],
            "equal_unit_qnt"    => ( empty($_POST["equalUnitQnt"]) or $_POST["equalUnitQnt"] == 0 ) ? 0 : $_POST["equalUnitQnt"],
            "base_qnt"          => ( empty($_POST["equalUnitQnt"]) or $_POST["equalUnitQnt"] == 0 ) ? 1 : $base_qnt * $_POST["equalUnitQnt"],
            "unit_description"  => $_POST["unitDescription"]
        ),
        array( // No duplicate allow.
            "unit_name"   => $_POST["unitName"]
        )
    );

    if($returnMsg === true) {
        _s("New unit added successfully.");
    } else {
        _e($returnMsg);
    }

}



/*************************** Unit List ***********************/
if(isset($_GET['page']) and $_GET['page'] == "itemUnitList") {
    
  $requestData = $_REQUEST;
  $getData = [];

  // List of all columns name
  $columns = array(
      "unit_name",
      "unit_description"
  );
  
  // Count Total recrods
  $totalFilteredRecords = $totalRecords = easySelectA(array(
        "table" => "product_units",
        "fields" => "count(*) as totalRow",
        "where" => array(
            "is_trash = 0"
        )
    ))["data"][0]["totalRow"];

  if($requestData['length'] == -1) {
    $requestData['length'] = $totalRecords;
  }

  $search = $requestData["search"]["value"];
  $getData = easySelectD("
      SELECT unit_id, unit_name, short_name, unit_description, equal_unit_qnt, equal_unit_name FROM `{$table_prefeix}product_units` unit1
          left join (
              select 
                  unit_id as id,
                  unit_name as equal_unit_name
              from {$table_prefeix}product_units
          ) unit2 on unit1.equal_unit_id = unit2.id
      where is_trash=0 and unit_name like '{$search}%'
      order by {$columns[$requestData['order'][0]['column']]} {$requestData['order'][0]['dir']}
  ");

  $totalFilteredRecords = $getData ? $getData["count"] : 0;

  $allData = [];
  // Check if there have more then zero data
  if(isset($getData['count']) and $getData['count'] > 0) {
      
      foreach($getData['data'] as $key => $value) {
          $allNestedData = [];
          $equalTo = !empty($value['equal_unit_name']) ? " ({$value['equal_unit_qnt']} {$value['equal_unit_name']}) " : "";
          $allNestedData[] = $value["unit_name"] . $equalTo;
          $allNestedData[] = $value["short_name"];
          $allNestedData[] = $value["unit_description"];
          // The action button
          $allNestedData[] = '<div class="btn-group">
                                  <button type="button" class="btn btn-xs btn-flat btn-primary dropdown-toggle" data-toggle="dropdown">
                                  action
                                  <span class="caret"></span>
                                  <span class="sr-only">Toggle Dropdown</span>
                                  </button>
                                  <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                  <li><a data-toggle="modal" href="'. full_website_address() .'/xhr/?icheck=false&module=products&page=editUnit&id='. $value["unit_id"] .'"  data-target="#modalDefault"><i class="fa fa-edit"></i> Edit</a></li>
                                  <li><a class="deleteEntry" href="'. full_website_address() . '/xhr/?module=products&page=deleteUnit" data-to-be-deleted="'. $value["unit_id"] .'"><i class="fa fa-minus-circle"></i> Delete</a></li>
                                  </ul>
                              </div>';
          
          $allData[] = $allNestedData;
      }
  }
  

  $jsonData = array (
      "draw"              => intval( $requestData['draw'] ),
      "recordsTotal"      => intval( $totalRecords ),
      "recordsFiltered"   => intval( $totalFilteredRecords ),
      "data"              => $allData
  );
  
  // Encode in Json Formate
  echo json_encode($jsonData); 

}


/***************** Delete Item Unit ****************/
// Delete Group
if(isset($_GET['page']) and $_GET['page'] == "deleteUnit") {

  $deleteData = easyDelete(
      "product_units",
      array(
          "unit_id" => $_POST["datatoDelete"]
      )
  );

  if($deleteData === true) {
      echo 1;
  } 

}


/************************** Edit Item Unit **********************/
if(isset($_GET['page']) and $_GET['page'] == "editUnit") {

  $unit_id = safe_input($_GET['id']);

  $selectItemUnit = easySelectD("
      SELECT unit_id, equal_unit_id, unit_name, short_name, unit_description, equal_unit_qnt, equal_unit_name FROM `{$table_prefeix}product_units` unit1
          left join (
              select 
                  unit_id as id,
                  unit_name as equal_unit_name
              from {$table_prefeix}product_units
          ) unit2 on unit1.equal_unit_id = unit2.id
      where is_trash=0 and unit_id = '{$unit_id}'
  ");

  $itemUnit = $selectItemUnit["data"][0];

  // Include the modal header
  modal_header("Edit Unit", full_website_address() . "/xhr/?module=products&page=updateUnit");
  
  ?>
    <div class="box-body">
      
      <div class="form-group required">
        <label for="unitName"><?= __("Unit Name:"); ?></label>
        <i data-toggle="tooltip" data-placement="right" title="E.G: Dozen, Litter" class="fa fa-question-circle"></i>
        <input type="text" name="unitName" id="unitName" value="<?php echo $itemUnit["unit_name"]; ?>" class="form-control">
      </div>
      <div class="form-group required">
        <label for="unitShortname"><?= __("Unit Shortname:"); ?></label>
        <input type="text" name="unitShortname" placeholder="E.G: kg" value="<?php echo $itemUnit["short_name"]; ?>"  id="unitShortname" class="form-control" required>
      </div>
      <div class="form-group">
          <label for="equal_unit_qnt"><?= __("Equal To:"); ?> </label>
          <div class="row">
              <div class="col-md-4">
                  <input type="number" onclick="this.select();" name="equalUnitQnt" id="equal_unit_qnt" value="<?php echo $itemUnit["equal_unit_qnt"]; ?>" class="form-control">
              </div>
              <div class="col-md-8">
                  <select name="equalUnit" id="equalUnit" class=" form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=itemUnitList" style="width: 100%;">
                      <option value="<?php echo $itemUnit["equal_unit_id"]; ?>"><?php echo $itemUnit["equal_unit_name"]; ?></option>
                  </select>
              </div>
          </div>
          
      </div>
      <div class="form-group">
        <label for="unitDescription"><?= __("Unit Description"); ?></label>
        <textarea name="unitDescription" id="unitDescription" rows="3" class="form-control"><?php echo $itemUnit["unit_description"]; ?></textarea>
      </div>
      <input type="hidden" name="unit_id" value="<?php echo htmlentities($_GET['id']); ?>">
    </div>
    <!-- /Box body-->

  <?php

  // Include the modal footer
  modal_footer();

}


//*******************************  Update Item Unit ******************** */
if(isset($_GET['page']) and $_GET['page'] == "updateUnit") {

    if(empty($_POST["unitName"])) {
        return _e("Please enter unit name.");
    } else if(empty($_POST["unitShortname"])) {
        return _e("Please enter unit short name.");
    }


    // select the equal unit
    $base_qnt = 1;
    if( !empty($_POST["equalUnit"]) ) {

        $base_qnt = easySelectA(array(
            "table"     => "product_units",
            "fields"    => "base_qnt",
            "where"     => array(
                "unit_id"   => $_POST["equalUnit"]
            )
        ))["data"][0]["base_qnt"];

    }

  // Update Other Information
  $updateUnit = easyUpdate(
      "product_units",
      array(
          "unit_name"         => $_POST["unitName"],
          "short_name"        => $_POST["unitShortname"],
          "equal_unit_id"     => empty($_POST["equalUnit"]) ? NULL : $_POST["equalUnit"],
          "equal_unit_qnt"    => $_POST["equalUnitQnt"],
          "base_qnt"          => ( empty($_POST["equalUnitQnt"]) or $_POST["equalUnitQnt"] == 0 ) ? 1 : $base_qnt * $_POST["equalUnitQnt"],
          "unit_description"  => $_POST["unitDescription"]
      ),
      array(
          "unit_id" => $_POST["unit_id"]
      )
  );

  if($updateUnit === true) {
      _s("Unit successfully updated.");
  } else {
      _e($updateUnit);
  }
  
}


/************************** Add new product Edition **********************/
if(isset($_GET['page']) and $_GET['page'] == "newProductEdition") {

    // Include the modal header
    modal_header("New Product Edition", full_website_address() . "/xhr/?module=products&page=addNewProductEdition");
    
    $edition_name = isset( $_GET["val"] ) ? $_GET["val"] : "";
    
    ?>
      <div class="box-body">
        
        <div class="form-group required">
          <label for="editionName"><?= __("Edition Name:"); ?></label>
          <input type="text" name="editionName" id="EditionName" class="form-control" value="<?php echo $edition_name; ?>" required>
        </div>
        <div class="form-group">
          <label for="editionDescription"><?= __("Description:"); ?></label>
          <textarea name="editionDescription" id="editionDescription" rows="3" class="form-control"></textarea>
        </div>
              
      </div>
      <!-- /Box body-->
  
    <?php
  
    // Include the modal footer
    modal_footer();
  
}

  
// Add Edition
if(isset($_GET['page']) and $_GET['page'] == "addNewProductEdition") {
  
    if(empty($_POST["editionName"])) {
      return _e("Please enter edition name.");
    }
    
    $returnMsg = easyInsert(
        "product_editions", // Table name
        array( // Fileds Name and value
            "edition_name"         => $_POST["editionName"],
            "edition_description"  => $_POST["editionDescription"]
        ),
        array( // No duplicate allow.
            "edition_name"   => $_POST["editionName"]
        )
    );
  
    if($returnMsg === true) {
        _s("The edition has been added successfully.");
    } else {
        _e($returnMsg);
    }
  
}
  
  
/*************************** Edition List ***********************/
if(isset($_GET['page']) and $_GET['page'] == "productEditionList") {
      
    $requestData = $_REQUEST;
    $getData = [];
  
    // List of all columns name
    $columns = array(
        "edition_name",
        "edition_description"
    );
    
    // Count Total recrods
    $totalFilteredRecords = $totalRecords = easySelectA(array(
        "table" => "product_editions",
        "fields" => "count(*) as totalRow",
        "where" => array(
            "is_trash = 0"
        )
    ))["data"][0]["totalRow"];
  
    if($requestData['length'] == -1) {
      $requestData['length'] = $totalRecords;
    }
  
    $getData = easySelect(
        "product_editions",
        "*",
        array(),
        array (
            "is_trash=0 and edition_name LIKE" => $requestData['search']['value'] . "%",
        ),
        array (
            $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
        ),
        array (
            "start" => $requestData['start'],
            "length" => $requestData['length']
        )
    );
    $totalFilteredRecords = $getData ? $getData["count"] : 0;
  
  
    $allData = [];
    // Check if there have more then zero data
    if(isset($getData['count']) and $getData['count'] > 0) {
        
        foreach($getData['data'] as $key => $value) {
            $allNestedData = [];
            $allNestedData[] = $value["edition_name"];
            $allNestedData[] = $value["edition_description"];
            // The action button
            $allNestedData[] = '<div class="btn-group">
                                    <button type="button" class="btn btn-xs btn-flat btn-primary dropdown-toggle" data-toggle="dropdown">
                                    action
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                    <li><a data-toggle="modal" href="'. full_website_address() .'/xhr/?icheck=false&module=products&page=editProductEdition&id='. $value["edition_id"] .'"  data-target="#modalDefault"><i class="fa fa-edit"></i> Edit</a></li>
                                    <li><a class="deleteEntry" href="'. full_website_address() . '/xhr/?module=products&page=deleteProductEdition" data-to-be-deleted="'. $value["edition_id"] .'"><i class="fa fa-minus-circle"></i> Delete</a></li>
                                    </ul>
                                </div>';
            
            $allData[] = $allNestedData;
        }
    }
    
  
    $jsonData = array (
        "draw"              => intval( $requestData['draw'] ),
        "recordsTotal"      => intval( $totalRecords ),
        "recordsFiltered"   => intval( $totalFilteredRecords ),
        "data"              => $allData
    );
    
    // Encode in Json Format
    echo json_encode($jsonData); 
  
}

  
/***************** Delete Product Edition ****************/
if(isset($_GET['page']) and $_GET['page'] == "deleteProductEdition") {
  
    $deleteData = easyDelete(
        "product_editions",
        array(
            "edition_id" => $_POST["datatoDelete"]
        )
    );
  
    if($deleteData === true) {
        echo 1;
    } else {
        echo $deleteData;
    }
  
}
  
/************************** Edition Product edition **********************/
if(isset($_GET['page']) and $_GET['page'] == "editProductEdition") {
  
    $productEdition = easySelect(
        "product_editions",
        "*",
        array(),
        array(
            "edition_id" => $_GET['id']
        )
    )["data"][0];
  
    // Include the modal header
    modal_header("Edit Product Edition", full_website_address() . "/xhr/?module=products&page=updateProductEdition");
    
    ?>
      <div class="box-body">
        
        <div class="required" class="form-group">
          <label for="editionName"><?= __("Edition Name:"); ?></label>
          <input type="text" name="editionName" id="editionName" class="form-control" value="<?php echo $productEdition["edition_name"]; ?>" required>
        </div>
        <div class="form-group">
          <label for="editionDescription"><?= __("Description:"); ?></label>
          <textarea name="editionDescription" id="editionDescription" rows="3" class="form-control"><?php echo $productEdition["edition_description"]; ?></textarea>
        </div>
        <input type="hidden" name="edition_id" value="<?php echo htmlentities($_GET['id']); ?>">
              
      </div>
      <!-- /Box body-->
  
    <?php
  
    // Include the modal footer
    modal_footer();
  
}
  
// Update Product edition
if(isset($_GET['page']) and $_GET['page'] == "updateProductEdition") {
  
    if(empty($_POST["editionName"])) {
      return _e("Please enter edition name.");
    }
    
    $returnMsg = easyUpdate(
        "product_editions",
        array(
            "edition_name"         => $_POST["editionName"],
            "edition_description"  => $_POST["editionDescription"]
        ),
        array(
            "edition_id"   => $_POST["edition_id"]
        )
    );
  
    if($returnMsg === true) {
        _s("The edition successfully updated.");
    } else {
        _e($returnMsg);
    }
  
}



/************************** Add new product Brand **********************/
if(isset($_GET['page']) and $_GET['page'] == "newProductBrand") {

  // Include the modal header
  modal_header("New Product Brand", full_website_address() . "/xhr/?module=products&page=addNewProductBrand");
  
  $brand_name = isset( $_GET["val"] ) ? $_GET["val"] : "";
  
  ?>
    <div class="box-body">
      
      <div class="form-group required">
        <label for="brandName"><?= __("Brand Name:"); ?></label>
        <input type="text" name="brandName" id="brandName" class="form-control" value="<?php echo $brand_name; ?>" required>
      </div>
      <div class="form-group">
        <label for="brandDescription"><?= __("Brand Description:"); ?></label>
        <textarea name="brandDescription" id="brandDescription" rows="3" class="form-control"></textarea>
      </div>
            
    </div>
    <!-- /Box body-->

  <?php

  // Include the modal footer
  modal_footer();

}

// Add new user group page
if(isset($_GET['page']) and $_GET['page'] == "addNewProductBrand") {

  if(empty($_POST["brandName"])) {
    return _e("Please enter brand name.");
  }
  
  $returnMsg = easyInsert(
      "product_brands", // Table name
      array( // Fileds Name and value
          "brand_name"         => $_POST["brandName"],
          "brand_description"  => $_POST["brandDescription"]
      ),
      array( // No duplicate allow.
          "brand_name"   => $_POST["brandName"]
      )
  );

  if($returnMsg === true) {
      _s("New brand added successfully.");
  } else {
      _e($returnMsg);
  }

}


/*************************** Unit List ***********************/
if(isset($_GET['page']) and $_GET['page'] == "productBrandList") {
    
  $requestData = $_REQUEST;
  $getData = [];

  // List of all columns name
  $columns = array(
      "brand_name",
      "brand_description"
  );
  
  // Count Total recrods
  $totalFilteredRecords = $totalRecords = easySelectA(array(
      "table" => "product_brands",
      "fields" => "count(*) as totalRow",
      "where" => array(
          "is_trash = 0"
      )
  ))["data"][0]["totalRow"];

  if($requestData['length'] == -1) {
    $requestData['length'] = $totalRecords;
  }

  $getData = easySelect(
      "product_brands",
      "*",
      array(),
      array (
          "is_trash=0 and brand_name LIKE" => $requestData['search']['value'] . "%",
      ),
      array (
          $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
      ),
      array (
          "start" => $requestData['start'],
          "length" => $requestData['length']
      )
  );
  $totalFilteredRecords = $getData ? $getData["count"] : 0;


  $allData = [];
  // Check if there have more then zero data
  if(isset($getData['count']) and $getData['count'] > 0) {
      
      foreach($getData['data'] as $key => $value) {
          $allNestedData = [];
          $allNestedData[] = $value["brand_name"];
          $allNestedData[] = $value["brand_description"];
          // The action button
          $allNestedData[] = '<div class="btn-group">
                                  <button type="button" class="btn btn-xs btn-flat btn-primary dropdown-toggle" data-toggle="dropdown">
                                  action
                                  <span class="caret"></span>
                                  <span class="sr-only">Toggle Dropdown</span>
                                  </button>
                                  <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                  <li><a data-toggle="modal" href="'. full_website_address() .'/xhr/?icheck=false&module=products&page=editProductBrand&id='. $value["brand_id"] .'"  data-target="#modalDefault"><i class="fa fa-edit"></i> Edit</a></li>
                                  <li><a class="deleteEntry" href="'. full_website_address() . '/xhr/?module=products&page=deleteProductBrand" data-to-be-deleted="'. $value["brand_id"] .'"><i class="fa fa-minus-circle"></i> Delete</a></li>
                                  </ul>
                              </div>';
          
          $allData[] = $allNestedData;
      }
  }
  

  $jsonData = array (
      "draw"              => intval( $requestData['draw'] ),
      "recordsTotal"      => intval( $totalRecords ),
      "recordsFiltered"   => intval( $totalFilteredRecords ),
      "data"              => $allData
  );
  
  // Encode in Json Format
  echo json_encode($jsonData); 

}

/***************** Delete Product Brand ****************/
if(isset($_GET['page']) and $_GET['page'] == "deleteProductBrand") {

  $deleteData = easyDelete(
      "product_brands",
      array(
          "brand_id" => $_POST["datatoDelete"]
      )
  );

  if($deleteData === true) {
      echo 1;
  } else {
      echo $deleteData;
  }

}

/************************** Add new product Brand **********************/
if(isset($_GET['page']) and $_GET['page'] == "editProductBrand") {

  $ProductBrand = easySelect(
      "product_brands",
      "*",
      array(),
      array(
          "brand_id" => $_GET['id']
      )
  )["data"][0];

  // Include the modal header
  modal_header("New Product Brand", full_website_address() . "/xhr/?module=products&page=updateProductBrand");
  
  ?>
    <div class="box-body">
      
      <div class="required" class="form-group">
        <label for="brandName"><?= __("Brand Name:"); ?></label>
        <input type="text" name="brandName" id="brandName" class="form-control" value="<?php echo $ProductBrand["brand_name"]; ?>" required>
      </div>
      <div class="form-group">
        <label for="brandDescription"><?= __("Brand Description:"); ?></label>
        <textarea name="brandDescription" id="brandDescription" rows="3" class="form-control"><?php echo $ProductBrand["brand_description"]; ?></textarea>
      </div>
      <input type="hidden" name="brand_id" value="<?php echo htmlentities($_GET['id']); ?>">
            
    </div>
    <!-- /Box body-->

  <?php

  // Include the modal footer
  modal_footer();

}

// Add new user group page
if(isset($_GET['page']) and $_GET['page'] == "updateProductBrand") {

  if(empty($_POST["brandName"])) {
    return _e("Please enter brand name.");
  }
  
  $returnMsg = easyUpdate(
      "product_brands",
      array(
          "brand_name"         => $_POST["brandName"],
          "brand_description"  => $_POST["brandDescription"]
      ),
      array(
          "brand_id"   => $_POST["brand_id"]
      )
  );

  if($returnMsg === true) {
      _s("The brand successfully updated.");
  } else {
      _e($returnMsg);
  }

}


/************************** Add new Author **********************/
if(isset($_GET['page']) and $_GET['page'] == "newAuthor") {

  // Include the modal header
  modal_header("New Author", full_website_address() . "/xhr/?module=products&page=addNewAuthor");
  
  ?>
    <div class="box-body">
      
      <div class="form-group required">
        <label for="authoryName"><?= __("Author Name:"); ?></label>
        <input type="text" name="authoryName" placeholder="Enter author name" id="authoryName" class="form-control" required>
      </div>
      <div class="form-group">
        <label for="authoryDoB"><?= __("Author Birth Date:"); ?></label>
        <input type="text" name="authoryDoB" id="authoryDoB" class="form-control datePicker" autoComplete="off">
      </div>
      <div class="form-group">
        <label for="authoryDoD"><?= __("Author Death Date:"); ?></label>
        <input type="text" name="authoryDoD" id="authoryDoD" class="form-control datePicker" autoComplete="off">
      </div>
      <div class="form-group">
        <label for="authorMobile"><?= __("Author Mobile:"); ?></label>
        <input type="text" name="authorMobile" id="authorMobile" class="form-control">
      </div>
      <div class="form-group">
        <label for="authorAdress"><?= __("Address:"); ?></label>
        <input type="text" name="authorAdress" id="authorAdress" class="form-control">
      </div>
      <div class="form-group">
        <label for="authorCountry"><?= __("Country:"); ?></label>
        <input type="text" name="authorCountry" id="authorCountry" class="form-control">
      </div>
      <div class="form-group">
        <label for="authorDescription"><?= __("Description:"); ?></label>
        <textarea class="form-control" name="authorDescription" id="authorDescription" cols="30" rows="3"></textarea>
      </div>
      <div class="form-group">
        <label for="authorWebsite"><?= __("Website:"); ?></label>
        <input type="text" name="authorWebsite" id="authorWebsite" class="form-control">
      </div>
      
    </div>
    <!-- /Box body-->

  <?php

  // Include the modal footer
  modal_footer();

}

/************************** Add new Author **********************/
if(isset($_GET['page']) and $_GET['page'] == "addNewAuthor") {

  if(empty($_POST["authoryName"])) {
    return _e("Please enter author name.");
  }
  
  $returnMsg = easyInsert(
      "product_authors", 
      array( 
          "author_name"       => $_POST["authoryName"],
          "author_birth_date" => empty($_POST["authoryDoB"]) ? NULL : $_POST["authoryDoB"],
          "author_death_date" => empty($_POST["authoryDoD"]) ? NULL : $_POST["authoryDoD"],
          "author_mobile"     => empty($_POST["authorMobile"]) ? NULL : $_POST["authorMobile"],
          "author_address"    => $_POST["authorAdress"],
          "author_country"    => $_POST["authorCountry"],
          "author_description" => $_POST["authorDescription"],
          "author_website"    => $_POST["authorWebsite"]
      ),
      array( // No duplicate allow.
          "author_mobile" => $_POST["authorMobile"],
          "author_name"  => $_POST["authoryName"]
      )
  );

  if($returnMsg === true) {
      _s("New author added successfully.");
  } else {
      _e($returnMsg);
  }

}


/*************************** Unit List ***********************/
if(isset($_GET['page']) and $_GET['page'] == "authorList") {
    
  $requestData = $_REQUEST;
  $getData = [];

  // List of all columns name
  $columns = array(
      "author_name",
      "author_birth_date",
      "author_death_date",
      "author_mobile",
      "author_address",
      "author_description"
  );
  
  // Count Total recrods
  $totalFilteredRecords = $totalRecords = easySelectA(array(
      "table" => "product_authors",
      "fields" => "count(*) as totalRow",
      "where" => array(
          "is_trash = 0"
      )
  ))["data"][0]["totalRow"];

  if($requestData['length'] == -1) {
    $requestData['length'] = $totalRecords;
  }

  $getData = easySelectA(array(
    "table" => "product_authors",
    "where" => array(
      "is_trash = 0 and author_name LIKE" => $requestData['search']['value'] . "%",
      " and author_mobile" => $requestData['search']['value'],
    ),
    "orderby" => array(
      $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
    ),
    "limit" => array(
      "start" => $requestData['start'],
      "length" => $requestData['length']
    )
  ));


  $totalFilteredRecords = $getData ? $getData["count"] : 0;


  $allData = [];
  // Check if there have more then zero data
  if(isset($getData['count']) and $getData['count'] > 0) {
      
      foreach($getData['data'] as $key => $value) {
          $allNestedData = [];
          $allNestedData[] = $value["author_name"];
          $allNestedData[] = $value["author_birth_date"];
          $allNestedData[] = $value["author_death_date"];
          $allNestedData[] = $value["author_mobile"];
          $allNestedData[] = $value["author_address"] . ', ' . $value["author_country"];
          $allNestedData[] = $value["author_description"];
          // The action button
          $allNestedData[] = '<div class="btn-group">
                                  <button type="button" class="btn btn-xs btn-flat btn-primary dropdown-toggle" data-toggle="dropdown">
                                  action
                                  <span class="caret"></span>
                                  <span class="sr-only">Toggle Dropdown</span>
                                  </button>
                                  <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                  <li><a data-toggle="modal" href="'. full_website_address() .'/xhr/?icheck=false&module=products&page=editAuthor&id='. $value["author_id"] .'"  data-target="#modalDefault"><i class="fa fa-edit"></i> Edit</a></li>
                                  <li><a class="deleteEntry" href="'. full_website_address() . '/xhr/?module=products&page=deleteAuthor" data-to-be-deleted="'. $value["author_id"] .'"><i class="fa fa-minus-circle"></i> Delete</a></li>
                                  </ul>
                              </div>';
          
          $allData[] = $allNestedData;
      }
  }
  

  $jsonData = array (
      "draw"              => intval( $requestData['draw'] ),
      "recordsTotal"      => intval( $totalRecords ),
      "recordsFiltered"   => intval( $totalFilteredRecords ),
      "data"              => $allData
  );
  
  // Encode in Json Format
  echo json_encode($jsonData); 

}

/***************** Delete Product Authors ****************/
if(isset($_GET['page']) and $_GET['page'] == "deleteAuthor") {

  $deleteData = easyDelete(
      "product_authors",
      array(
          "author_id" => $_POST["datatoDelete"]
      )
  );

  if($deleteData === true) {
      echo 1;
  } else {
      echo $deleteData;
  }

}



/************************** Edit Author **********************/
if(isset($_GET['page']) and $_GET['page'] == "editAuthor") {

  // Include the modal header
  modal_header("Edit Author", full_website_address() . "/xhr/?module=products&page=updateAuthor");

  $author = easySelectA(array(
    "table" => "product_authors",
    "where" => array(
      "author_id" => $_GET["id"]
    )
  ))["data"][0];
  
  ?>
    <div class="box-body">
      
      <div class="form-group required">
        <label for="authoryName"><?= __("Author Name:"); ?></label>
        <input type="text" name="authoryName" placeholder="Enter author name" value="<?= $author["author_name"]; ?>" id="authoryName" class="form-control" required>
      </div>
      <div class="form-group">
        <label for="authoryDoB"><?= __("Author Birth Date:"); ?></label>
        <input type="text" name="authoryDoB" id="authoryDoB" value="<?= $author["author_birth_date"]; ?>" class="form-control datePicker" autoComplete="off">
      </div>
      <div class="form-group">
        <label for="authoryDoD"><?= __("Author Death Date:"); ?></label>
        <input type="text" name="authoryDoD" id="authoryDoD" value="<?= $author["author_death_date"]; ?>" class="form-control datePicker" autoComplete="off">
      </div>
      <div class="form-group">
        <label for="authorMobile"><?= __("Author Mobile:"); ?></label>
        <input type="text" name="authorMobile" id="authorMobile" value="<?= $author["author_mobile"]; ?>" class="form-control">
      </div>
      <div class="form-group">
        <label for="authorAdress"><?= __("Address:"); ?></label>
        <input type="text" name="authorAdress" id="authorAdress" value="<?= $author["author_address"]; ?>" class="form-control">
      </div>
      <div class="form-group">
        <label for="authorCountry"><?= __("Country:"); ?></label>
        <input type="text" name="authorCountry" id="authorCountry" value="<?= $author["author_country"]; ?>" class="form-control">
      </div>
      <div class="form-group">
        <label for="authorDescription"><?= __("Description:"); ?></label>
        <textarea class="form-control" name="authorDescription" id="authorDescription" cols="30" rows="3"><?= $author["author_description"]; ?></textarea>
      </div>
      <div class="form-group">
        <label for="authorWebsite"><?= __("Website:"); ?></label>
        <input type="text" name="authorWebsite" id="authorWebsite" value="<?= $author["author_website"]; ?>" class="form-control">
      </div>
      <input type="hidden" name="authorId" value="<?= $_GET["id"]; ?>">
      
    </div>
    <!-- /Box body-->

  <?php

  // Include the modal footer
  modal_footer();

}

/************************** Add new Author **********************/
if(isset($_GET['page']) and $_GET['page'] == "updateAuthor") {

  if(empty($_POST["authoryName"])) {
    return _e("Please enter author name.");
  }
  
  $returnMsg = easyUpdate(
      "product_authors", 
      array( 
          "author_name"       => $_POST["authoryName"],
          "author_birth_date" => empty($_POST["authoryDoB"]) ? NULL : $_POST["authoryDoB"],
          "author_death_date" => empty($_POST["authoryDoD"]) ? NULL : $_POST["authoryDoD"],
          "author_mobile"     => empty($_POST["authorMobile"]) ? NULL : $_POST["authorMobile"],
          "author_address"    => $_POST["authorAdress"],
          "author_country"    => $_POST["authorCountry"],
          "author_description" => $_POST["authorDescription"],
          "author_website"    => $_POST["authorWebsite"]
      ),
      array( 
          "author_id" => $_POST["authorId"]
      )
  );

  if($returnMsg === true) {
      _s("The author has been successfully updated.");
  } else {
      _e($returnMsg);
  }

}


/************************** Add new Attribute **********************/
if(isset($_GET['page']) and $_GET['page'] == "newProductAttribute") {

    // Include the modal header
    modal_header("New Attribute", full_website_address() . "/xhr/?module=products&page=addNewProductAttribute");
    
    ?>
      <div class="box-body">
        
        <div class="form-group required">
          <label for="attributeName"><?= __("Attribute Name:"); ?></label>
          <input type="text" name="attributeName" placeholder="Enter attribute name" id="attributeName" class="form-control" required>
        </div>
        <div class="form-group required">
          <label for="attributeType"><?= __("Attribute Type:"); ?></label>
          <select name="attributeType" id="attributeType" class="form-control">
              <option value="Select">Select</option>
              <option value="Color">Color</option>
              <option value="Radio">Radio</option>
          </select>
        </div>
        <div class="form-group">
          <label for="attributeDescription"><?= __("Attribute Description:"); ?></label>
          <textarea name="attributeDescription" id="attributeDescription" cols="30" rows="3" class="form-control"></textarea>
        </div>
        
        
      </div>
      <!-- /Box body-->
  
    <?php
  
    // Include the modal footer
    modal_footer();
  
  }
  
  /************************** Add new Author **********************/
  if(isset($_GET['page']) and $_GET['page'] == "addNewProductAttribute") {
  
    if(empty($_POST["attributeName"])) {
        return _e("Please enter attribute name.");
    } else if(empty($_POST["attributeType"])) {
        return _e("Please select attribute type.");
    }
    
    $returnMsg = easyInsert(
        "product_attributes", 
        array( 
            "pa_name"           => $_POST["attributeName"],
            "pa_type"           => $_POST["attributeType"],
            "pa_description"    => empty($_POST["attributeDescription"]) ? NULL : $_POST["attributeDescription"],
        ),
        array( // No duplicate allow.
            "pa_name" => $_POST["attributeName"]
        )
    );
  
    if($returnMsg === true) {
        _s("New attribute added successfully.");
    } else {
        _e($returnMsg);
    }
  
  }
  

/*************************** Attribute List ***********************/
if(isset($_GET['page']) and $_GET['page'] == "productAttributeList") {
    
    $requestData = $_REQUEST;
    $getData = [];
  
    // List of all columns name
    $columns = array(
        "attributes.pa_name",
        "pa_type"
    );
    
    // Count Total recrods
    $totalFilteredRecords = $totalRecords = easySelectA(array(
        "table" => "product_attributes",
        "fields" => "count(*) as totalRow",
        "where" => array(
            "is_trash = 0"
        )
    ))["data"][0]["totalRow"];
  
    if($requestData['length'] == -1) {
      $requestData['length'] = $totalRecords;
    }
  
    $getData = easySelectA(array(
      "table"   => "product_attributes as attributes",
      "fields"  => "pa_id, attributes.pa_name as attribute_name, pa_type, pa_description, group_concat(pv_name SEPARATOR ', ') as variations_list",
      "where"   => array(
        "attributes.is_trash = 0 and attributes.pa_name LIKE" => $requestData['search']['value'] . "%"
      ),
      "join"    => array(
          "left join {$table_prefeix}product_variations as variations on variations.pa_name = attributes.pa_name"
      ),
      "groupby" => "attributes.pa_name",
      "orderby" => array(
        $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
      ),
      "limit" => array(
        "start" => $requestData['start'],
        "length" => $requestData['length']
      )
    ));
  
  
    $totalFilteredRecords = $getData ? $getData["count"] : 0;
  
  
    $allData = [];
    // Check if there have more then zero data
    if(isset($getData['count']) and $getData['count'] > 0) {
        
        foreach($getData['data'] as $key => $value) {
            $allNestedData = [];
            $allNestedData[] = $value["attribute_name"];
            $allNestedData[] = $value["pa_type"];
            $allNestedData[] = $value["variations_list"];
            $allNestedData[] = $value["pa_description"];
            // The action button
            $allNestedData[] = '<div class="btn-group">
                                    <button type="button" class="btn btn-xs btn-flat btn-primary dropdown-toggle" data-toggle="dropdown">
                                    action
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                    <li><a data-toggle="modal" href="'. full_website_address() .'/xhr/?icheck=false&module=products&page=editProductAttribute&id='. $value["pa_id"] .'"  data-target="#modalDefault"><i class="fa fa-edit"></i> Edit</a></li>
                                    <li><a class="deleteEntry" href="'. full_website_address() . '/xhr/?module=products&page=deleteProductAttribute" data-to-be-deleted="'. $value["pa_id"] .'"><i class="fa fa-minus-circle"></i> Delete</a></li>
                                    </ul>
                                </div>';
            
            $allData[] = $allNestedData;
        }
    }
    
  
    $jsonData = array (
        "draw"              => intval( $requestData['draw'] ),
        "recordsTotal"      => intval( $totalRecords ),
        "recordsFiltered"   => intval( $totalFilteredRecords ),
        "data"              => $allData
    );
    
    // Encode in Json Format
    echo json_encode($jsonData); 
  
}


/***************** Delete Product Authors ****************/
if(isset($_GET['page']) and $_GET['page'] == "deleteProductAttribute") {

  $deleteData = easyDelete(
      "product_attributes",
      array(
          "pa_id" => $_POST["datatoDelete"]
      )
  );

  if($deleteData === true) {
      echo 1;
  } else {
      echo $deleteData;
  }

}

/************************** Edit Product Attribute **********************/
if(isset($_GET['page']) and $_GET['page'] == "editProductAttribute") {

    // Include the modal header
    modal_header("Edit Attribute", full_website_address() . "/xhr/?module=products&page=updateProductAttribute");

    $pa = easySelectA(array(
        "table"     => "product_attributes",
        "where"     => array(
            "pa_id"     => $_GET["id"]
        )
    ))["data"][0];

    
    ?>
      <div class="box-body">
        
        <div class="form-group required">
          <label for="attributeName"><?= __("Attribute Name:"); ?></label>
          <input type="text" name="attributeName" placeholder="Enter attribute name" id="attributeName" value="<?php echo $pa["pa_name"] ; ?>" class="form-control" required>
        </div>
        <div class="form-group required">
          <label for="attributeType"><?= __("Attribute Type:"); ?></label>
          <select name="attributeType" id="attributeType" class="form-control">
              <option <?php echo ( $pa["pa_type"] === "Select" ) ? "selected" : ""; ?> value="Select">Select</option>
              <option <?php echo ( $pa["pa_type"] === "Color" ) ? "selected" : ""; ?> value="Color">Color</option>
              <option <?php echo ( $pa["pa_type"] === "Radio" ) ? "selected" : ""; ?> value="Radio">Radio</option>
          </select>
        </div>
        <div class="form-group">
          <label for="attributeDescription"><?= __("Attribute Description:"); ?></label>
          <textarea name="attributeDescription" id="attributeDescription" cols="30" rows="3" class="form-control"><?php echo $pa["pa_description"] ; ?></textarea>
        </div>
        <input type="hidden" name="pa_id" value="<?php echo htmlentities($_GET["id"]); ?>">
        
        
      </div>
      <!-- /Box body-->
  
    <?php
  
    // Include the modal footer
    modal_footer();
  
}
  
/************************** Add new Author **********************/
if(isset($_GET['page']) and $_GET['page'] == "updateProductAttribute") {
  
    if(empty($_POST["attributeName"])) {
        return _e("Please enter attribute name.");
    } else if(empty($_POST["attributeType"])) {
        return _e("Please select attribute type.");
    }
    
    $returnMsg = easyUpdate(
        "product_attributes", 
        array( 
            "pa_name"           => $_POST["attributeName"],
            "pa_type"           => $_POST["attributeType"],
            "pa_description"    => empty($_POST["attributeDescription"]) ? NULL : $_POST["attributeDescription"],
        ),
        array( // No duplicate allow.
            "pa_id" => $_POST["pa_id"]
        )
    );
  
    if($returnMsg === true) {
        _s("Attribute has been successfully updated.");
    } else {
        _e($returnMsg);
    }
  
}
  

/************************** Add new Attribute **********************/
if(isset($_GET['page']) and $_GET['page'] == "newProductVariation") {

    // Include the modal header
    modal_header("New Variation", full_website_address() . "/xhr/?module=products&page=addNewProductVariation");
    
    ?>
      <div class="box-body">
        
        <div class="form-group required">
          <label for="variationName"><?= __("Variation Name:"); ?></label>
          <input type="text" name="variationName" placeholder="Enter variation name" id="variationName" class="form-control" required>
        </div>
        <div class="form-group required">
          <label for="variationAttribute"><?= __("Attribute:"); ?></label>
          <select name="variationAttribute" id="variationAttribute" class="form-control">
              <option value="">Select Attribute....</option>
              <?php 
                $pa = easySelectA(array(
                    "table" => "product_attributes",
                    "where" => array(
                        "is_trash = 0"
                    )
                ))["data"];

                foreach($pa as $paVal) {
                    echo "<option value='{$paVal['pa_name']}'>{$paVal['pa_name']}</option>";
                }

              ?>
              
          </select>
        </div>
        <div class="form-group">
          <label for="variationDescription"><?= __("Description:"); ?></label>
          <textarea name="variationDescription" id="variationDescription" cols="30" rows="3" class="form-control"></textarea>
        </div>
        
        
      </div>
      <!-- /Box body-->
  
    <?php
  
    // Include the modal footer
    modal_footer();
  
  }
  
  /************************** Add new Author **********************/
  if(isset($_GET['page']) and $_GET['page'] == "addNewProductVariation") {
  
    if(empty($_POST["variationName"])) {
        return _e("Please enter variation name.");
    } else if(empty($_POST["variationAttribute"])) {
        return _e("Please select attribute.");
    }
    
    $returnMsg = easyInsert(
        "product_variations", 
        array( 
            "pa_name"           => $_POST["variationAttribute"],
            "pv_name"           => $_POST["variationName"],
            "pv_description"    => empty($_POST["variationDescription"]) ? NULL : $_POST["variationDescription"],
        ),
        array( // No duplicate allow.
            "pv_name" => $_POST["variationName"]
        )
    );
  
    if($returnMsg === true) {
        _s("New variation added successfully.");
    } else {
        _e($returnMsg);
    }
  
  }
  

/*************************** Attribute List ***********************/
if(isset($_GET['page']) and $_GET['page'] == "productVariationList") {
    
    $requestData = $_REQUEST;
    $getData = [];
  
    // List of all columns name
    $columns = array(
        "pv_name",
        "pa_name",
        "pv_description"
    );
    
    // Count Total recrods
    $totalFilteredRecords = $totalRecords = easySelectA(array(
        "table" => "product_variations",
        "fields" => "count(*) as totalRow",
        "where" => array(
            "is_trash = 0"
        )
    ))["data"][0]["totalRow"];
  
    if($requestData['length'] == -1) {
      $requestData['length'] = $totalRecords;
    }
  
    $getData = easySelectA(array(
      "table"   => "product_variations",
      "where"   => array(
        "is_trash = 0 and ( pv_name LIKE '". safe_input($requestData['search']['value']) ."%'",
        " or pa_name LIKE" => $requestData['search']['value'] . "%",
        ")"
      ),
      "orderby" => array(
        $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
      ),
      "limit" => array(
        "start" => $requestData['start'],
        "length" => $requestData['length']
      )
    ));
  
  
    $totalFilteredRecords = $getData ? $getData["count"] : 0;
  
  
    $allData = [];
    // Check if there have more then zero data
    if(isset($getData['count']) and $getData['count'] > 0) {
        
        foreach($getData['data'] as $key => $value) {
            $allNestedData = [];
            $allNestedData[] = $value["pv_name"];
            $allNestedData[] = $value["pa_name"];
            $allNestedData[] = $value["pv_description"];
            // The action button
            $allNestedData[] = '<div class="btn-group">
                                    <button type="button" class="btn btn-xs btn-flat btn-primary dropdown-toggle" data-toggle="dropdown">
                                    action
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                    <li><a data-toggle="modal" href="'. full_website_address() .'/xhr/?icheck=false&module=products&page=editProductVariation&id='. $value["pv_id"] .'"  data-target="#modalDefault"><i class="fa fa-edit"></i> Edit</a></li>
                                    <li><a class="deleteEntry" href="'. full_website_address() . '/xhr/?module=products&page=deleteProductVariation" data-to-be-deleted="'. $value["pv_id"] .'"><i class="fa fa-minus-circle"></i> Delete</a></li>
                                    </ul>
                                </div>';
            
            $allData[] = $allNestedData;
        }
    }
    
  
    $jsonData = array (
        "draw"              => intval( $requestData['draw'] ),
        "recordsTotal"      => intval( $totalRecords ),
        "recordsFiltered"   => intval( $totalFilteredRecords ),
        "data"              => $allData
    );
    
    // Encode in Json Format
    echo json_encode($jsonData); 
  
}


/***************** Delete Product Authors ****************/
if(isset($_GET['page']) and $_GET['page'] == "deleteProductVariation") {

  $deleteData = easyDelete(
      "product_variations",
      array(
          "pv_id" => $_POST["datatoDelete"]
      )
  );

  if($deleteData === true) {
      echo 1;
  } else {
      echo $deleteData;
  }

}


/************************** Edit Product Variation **********************/
if(isset($_GET['page']) and $_GET['page'] == "editProductVariation") {

    // Include the modal header
    modal_header("New Variation", full_website_address() . "/xhr/?module=products&page=updateProductVariation");

    $pv = easySelectA(array(
        "table"     => "product_variations",
        "where"     => array(
            "pv_id" => $_GET["id"]
        )
    ))["data"][0];
    
    ?>
      <div class="box-body">
        
        <div class="form-group required">
          <label for="variationName"><?= __("Variation Name:"); ?></label>
          <input type="text" name="variationName" placeholder="Enter variation name" id="variationName" value="<?php echo $pv["pv_name"]; ?>" class="form-control" required>
        </div>
        <div class="form-group required">
          <label for="variationAttribute"><?= __("Attribute:"); ?></label>
          <select name="variationAttribute" id="variationAttribute" class="form-control">
              <option value="">Select Attribute....</option>
              <?php 
                $pa = easySelectA(array(
                    "table" => "product_attributes",
                    "where" => array(
                        "is_trash = 0"
                    )
                ))["data"];

                foreach($pa as $paVal) {

                    $selected = ( $pv["pa_name"] === $paVal['pa_name'] ) ? "selected" : "";
                    echo "<option {$selected} value='{$paVal['pa_name']}'>{$paVal['pa_name']}</option>";
                }

              ?>
              
          </select>
        </div>
        <div class="form-group">
          <label for="variationDescription"><?= __("Description:"); ?></label>
          <textarea name="variationDescription" id="variationDescription" cols="30" rows="3" class="form-control"><?php echo $pv["pv_description"]; ?></textarea>
        </div>
        <input type="hidden" name="pv_id" value="<?php echo htmlentities($_GET["id"]); ?>">
        
        
      </div>
      <!-- /Box body-->
  
    <?php
  
    // Include the modal footer
    modal_footer();
  
  }
  
  /************************** Add new Author **********************/
  if(isset($_GET['page']) and $_GET['page'] == "updateProductVariation") {
  
    if(empty($_POST["variationName"])) {
        return _e("Please enter variation name.");
    } else if(empty($_POST["variationAttribute"])) {
        return _e("Please select attribute.");
    }

    // Select previous variationName
    $oldVariationName = easySelectA(array(
        "table"     => "product_variations",
        "fields"    => "pv_name",
        "where"     => array(
            "pv_id" => $_POST["pv_id"]
        )
    ))["data"][0]["pv_name"];
    
    
    // Update variation name
    $returnMsg = easyUpdate(
        "product_variations", 
        array( 
            "pa_name"           => $_POST["variationAttribute"],
            "pv_name"           => $_POST["variationName"],
            "pv_description"    => empty($_POST["variationDescription"]) ? NULL : $_POST["variationDescription"],
        ),
        array( // No duplicate allow.
            "pv_id" => $_POST["pv_id"]
        )
    );    

  
    if($returnMsg === true) {

        // Update variation on product meta
        easyUpdate(
            "product_meta",
            array(
                "meta_value"    => $_POST["variationName"]
            ),
            array(
                "meta_value"    => $oldVariationName
            )
        );

        _s("The variation updateded successfully.");


    } else {
        _e($returnMsg);
    }
  
}


/************************** Add new Generic **********************/
if(isset($_GET['page']) and $_GET['page'] == "newProductGeneric") {

    // Include the modal header
    modal_header("New Generic", full_website_address() . "/xhr/?module=products&page=addNewProductGeneric");

    $generic_name = isset( $_GET["val"] ) ? $_GET["val"] : "";
    
    ?>
      <div class="box-body">
        
        <div class="form-group required">
          <label for="genericName"><?= __("Generic Name:"); ?></label>
          <input type="text" name="genericName" placeholder="Enter generic name" id="genericName" value="<?php echo $generic_name; ?>" class="form-control" required>
        </div>
        <div class="form-group">
          <label for="genericDescription"><?= __("Description:"); ?></label>
          <textarea name="genericDescription" id="genericDescription" cols="30" rows="3" class="form-control"></textarea>
        </div>
        
      </div>
      <!-- /Box body-->
  
    <?php
  
    // Include the modal footer
    modal_footer();
  
}
  
/************************** Add new Author **********************/
if(isset($_GET['page']) and $_GET['page'] == "addNewProductGeneric") {
  
    if(empty($_POST["genericName"])) {
        return _e("Please enter generic name.");
    }
    
    $returnMsg = easyInsert(
        "product_generic", 
        array( 
            "generic_name"           => $_POST["genericName"],
            "generic_description"    => empty($_POST["genericDescription"]) ? NULL : $_POST["genericDescription"],
        ),
        array( // No duplicate allow.
            "generic_name" => $_POST["genericName"]
        )
    );
  
    if($returnMsg === true) {
        _s("New generic added successfully.");
    } else {
        _e($returnMsg);
    }
  
}


/*************************** Attribute List ***********************/
if(isset($_GET['page']) and $_GET['page'] == "productGenericList") {
    
    $requestData = $_REQUEST;
    $getData = [];
  
    // List of all columns name
    $columns = array(
        "generic_name",
        "generic_description"
    );
    
    // Count Total recrods
    $totalFilteredRecords = $totalRecords = easySelectA(array(
        "table" => "product_generic",
        "fields" => "count(*) as totalRow",
        "where" => array(
            "is_trash = 0"
        )
    ))["data"][0]["totalRow"];
  
    if($requestData['length'] == -1) {
      $requestData['length'] = $totalRecords;
    }
  
    $getData = easySelectA(array(
      "table"   => "product_generic",
      "where"   => array(
        "is_trash = 0 and generic_name LIKE" => $requestData['search']['value'] . "%"
      ),
      "orderby" => array(
        $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
      ),
      "limit" => array(
        "start" => $requestData['start'],
        "length" => $requestData['length']
      )
    ));
  
  
    if( !empty($requestData['search']['value']) ) {
        $totalFilteredRecords = $getData ? $getData["count"] : 0;
    }
  
  
    $allData = [];
    // Check if there have more then zero data
    if(isset($getData['count']) and $getData['count'] > 0) {
        
        foreach($getData['data'] as $key => $value) {
            $allNestedData = [];
            $allNestedData[] = $value["generic_name"];
            $allNestedData[] = $value["generic_description"];
            // The action button
            $allNestedData[] = '<div class="btn-group">
                                    <button type="button" class="btn btn-xs btn-flat btn-primary dropdown-toggle" data-toggle="dropdown">
                                    action
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                    <li><a data-toggle="modal" href="'. full_website_address() .'/xhr/?icheck=false&module=products&page=editProductGeneric&id='. $value["generic_id"] .'"  data-target="#modalDefault"><i class="fa fa-edit"></i> Edit</a></li>
                                    <li><a class="deleteEntry" href="'. full_website_address() . '/xhr/?module=products&page=deleteProductGeneric" data-to-be-deleted="'. $value["generic_id"] .'"><i class="fa fa-minus-circle"></i> Delete</a></li>
                                    </ul>
                                </div>';
            
            $allData[] = $allNestedData;
        }
    }
    
  
    $jsonData = array (
        "draw"              => intval( $requestData['draw'] ),
        "recordsTotal"      => intval( $totalRecords ),
        "recordsFiltered"   => intval( $totalFilteredRecords ),
        "data"              => $allData
    );
    
    // Encode in Json Format
    echo json_encode($jsonData); 
  
}


/***************** Delete Product Authors ****************/
if(isset($_GET['page']) and $_GET['page'] == "deleteProductGeneric") {

  $deleteData = easyDelete(
      "product_generic",
      array(
          "generic_id" => $_POST["datatoDelete"]
      )
  );

  if($deleteData === true) {
      echo 1;
  } else {
      echo $deleteData;
  }

}


/************************** Edit product Generic **********************/
if(isset($_GET['page']) and $_GET['page'] == "editProductGeneric") {

    // Include the modal header
    modal_header("Edit Generic", full_website_address() . "/xhr/?module=products&page=updateeProductGeneric");

    $pg = easySelectA(array(
        "table" => "product_generic",
        "where" => array(
            "generic_id"    => $_GET["id"]
        )
    ))["data"][0];
    
    ?>
      <div class="box-body">
        
        <div class="form-group required">
          <label for="genericName"><?= __("Generic Name:"); ?></label>
          <input type="text" name="genericName" placeholder="Enter generic name" value="<?php echo $pg["generic_name"]; ?>" id="genericName" class="form-control" required>
        </div>
        <div class="form-group">
          <label for="genericDescription"><?= __("Description:"); ?></label>
          <textarea name="genericDescription" id="genericDescription" cols="30" rows="3" class="form-control"><?php echo $pg["generic_description"]; ?></textarea>
        </div>
        <input type="hidden" name="generic_id" value="<?php echo htmlentities($_GET["id"]); ?>">
        
      </div>
      <!-- /Box body-->
  
    <?php
  
    // Include the modal footer
    modal_footer();
  
}
  
/************************** Update generic **********************/
if(isset($_GET['page']) and $_GET['page'] == "updateeProductGeneric") {
  
    if(empty($_POST["genericName"])) {
        return _e("Please enter generic name.");
    }
    
    $returnMsg = easyUpdate(
        "product_generic", 
        array( 
            "generic_name"           => $_POST["genericName"],
            "generic_description"    => empty($_POST["genericDescription"]) ? NULL : $_POST["genericDescription"],
        ),
        array( // No duplicate allow.
            "generic_id" => $_POST["generic_id"]
        )
    );
  
    if($returnMsg === true) {
        _s("The generic has been updated successfully.");
    } else {
        _e($returnMsg);
    }
  
}

?>