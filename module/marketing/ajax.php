<?php

/************************** New Person **********************/
if (isset($_GET['page']) and $_GET['page'] == "newPerson") {

    // Include the modal header
    modal_header("New Person", full_website_address() . "/xhr/?module=marketing&page=addNewPerson");

?>
    <div class="box-body">

        <div class="form-group required">
            <label for="personFullName"><?= __("Person Full Name:"); ?></label>
            <input type="text" name="personFullName" id="personFullName" placeholder="Enter person name" class="form-control" required>
        </div>
        <div class="row">
            <div class="form-group col-md-6">
                <label for="personType"><?= __("Person Type"); ?></label>
                <select name="personType" id="personType" class="form-control">
                    <?php
                    $personType = array('Teacher', 'Student', 'Guardian', 'Service Holder', 'Merchant');
                    foreach ($personType as $type) {
                        echo "<option value='$type'>$type</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group col-md-6 personDesignation">
                <label for="personDesignation"><?= __("Designation:"); ?></label>
                <input type="text" name="personDesignation" id="personDesignation" placeholder="Enter person designation" class="form-control">
            </div>
            <div style="display: none;" class="form-group col-md-6 personClass">
                <label for="personClass"><?= __("Person Class"); ?></label>
                <select name="personClass" id="personClass" class="form-control select2">
                    <option value=""><?= __("Select Class"); ?></option>
                    <?php
                        $class = array(
                            "1"   => __("Class One"),
                            "2"   => __("Class Two"),
                            "3"   => __("Class Three"),
                            "4"   => __("Class Four"),
                            "5"   => __("Class Five"),
                            "6"   => __("Class Six"),
                            "7"   => __("Class Seven"),
                            "8"   => __("Class Eight"),
                            "9"   => __("Class Nine"),
                            "10"  => __("Class Ten"),
                            "11"  => __("HSC 1st Year"),
                            "12"  => __("HSC 2nd Year"),
                            "13"  => __("Diploma/Graduation Level")
                        );

                        foreach ($class as $key => $value) {
                            echo "<option value='$key'>$value</option>";
                        }
                    ?>
                </select>
            </div>

        </div>
        <div class="form-group personSubject">
            <label for="personSubject"><?= __("Person Subject"); ?></label>
            <select name="personSubject[]" id="personSubject" class="form-control select2Ajax" closeOnSelect="false" select2-tag="true" multiple select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=subjectList" style="width: 100%;">
                <option value=""><?= __("Select Subjects"); ?>....</option>
            </select>
        </div>
        <div class="form-group">
            <label for="personTags"><?= __("Person Tag"); ?></label>
            <select name="personTags[]" id="personTags" class="form-control select2Ajax" closeOnSelect="false" select2-tag="true" multiple select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=personTagList" style="width: 100%;">
                <option value=""><?= __("Select Tages"); ?>....</option>
            </select>
        </div>
        <div class="row">
            <div class="form-group col-md-6 required">
                <label for="personPhone"><?= __("Phone:"); ?></label>
                <input type="text" name="personPhone" id="personPhone" value="" class="form-control">
            </div>
            <div class="form-group col-md-6">
                <label for="personEmail"><?= __("Email:"); ?></label>
                <input type="email" name="personEmail" id="personEmail" value="" class="form-control">
            </div>
            <div class="form-group col-md-6">
                <label for="personDivision"><?= __("Person Division:"); ?></label>
                <select name="personDivision" id="personDivision" class="form-control" style="width: 100%;">
                    <option value=""><?= __("Select Division"); ?></option>
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="personDistrict"><?= __("Person District:"); ?></label>
                <select name="personDistrict" id="personDistrict" class="form-control" style="width: 100%;">
                    <option value=""><?= __("Select District"); ?></option>
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="personUpazila"><?= __("Person Upazila:"); ?></label>
                <select name="personUpazila" id="personUpazila" class="form-control" style="width: 100%;">
                    <option value=""><?= __("Select Upazila"); ?>...</option>
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="personPostalCode"><?= __("Postal Code:"); ?></label>
                <input type="text" name="personPostalCode" id="personPostalCode" value="" class="form-control">
            </div>
        </div>
        <div class="form-group">
            <label for="personInstitute"><?= __("Organization/ Institute:"); ?></label>
            <select name="personInstitute" id="personInstitute" class="form-control" style="width: 100%;">
                <option value=""><?= __("Select Institute"); ?>...</option>
            </select>
        </div>
        <div class="form-group">
            <label for="personAddress"><?= __("Person Address:"); ?></label>
            <textarea name="personAddress" id="personAddress" rows="3" class="form-control"> </textarea>
        </div>
        <div class="form-group">
            <label for="personWebsite"><?= __("Website:"); ?></label>
            <input type="text" name="personWebsite" id="personWebsite" value="" class="form-control">
        </div>
        <div class="form-group">
            <label for="leadSource"><?= __("Data Source:"); ?></label>
            <select name="leadSource" id="leadSource" class="form-control select2Ajax" select2-tag="true" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=leadsDataSource" style="width: 100%;">
                <option value=""><?= __("Select Data Source"); ?>....</option>
            </select>
        </div>
        <div class="row">
            <div class="form-group col-md-8">
                <label for="dataCollectBy"><?= __("Data Collect By:"); ?></label>
                <select name="dataCollectBy" id="dataCollectBy" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=MRList" style="width: 100%;">
                    <option value=""><?= __("Select employee"); ?>....</option>
                </select>
            </div>
            <div class="form-group col-md-4">
                <label for="collectionDate"><?= __("Collection Date:"); ?></label>
                <input type="text" name="collectionDate" id="collectionDate" value="" class="form-control datePicker">
            </div>
        </div>

    </div>
    <!-- /Box body-->

    <script>
        $(document).on("change", "#personType", function() {

            var personType = $(this).val();

            if (personType == "Teacher") {

                $(".personDesignation, .personSubject").show();
                $(".personClass").hide();

            } else if (personType == "Student") {

                $(".personDesignation, .personSubject").hide();
                $(".personClass").show();

            } else if (personType == "Service Holder" || personType == "Merchant" || personType == "Guardian") {

                $(".personSubject, .personClass").hide();
                $(".personDesignation").show();

            }

        });


        /* If division, district and upazila change then clear the lower fields */
        $(document).on("change", "#personDivision", function() {
            $("#personDistrict").val(null).trigger("change");
            $("#personUpazila").val(null).trigger("change");
            $("#personInstitute").val(null).trigger("change");
        });
        $(document).on("change", "#personDistrict", function() {
            $("#personUpazila").val(null).trigger("change");
            $("#personInstitute").val(null).trigger("change");
        });
        $(document).on("change", "#personUpazila", function() {
            $("#personInstitute").val(null).trigger("change");
        });


        $(document).on('mouseenter focus', '#personDivision, #personDistrict, #personUpazila, #personInstitute', function() {

            var select2AjaxUrl = '<?php echo full_website_address() ?>/info/?module=select2&page=';

            /* Initialize Select Ajax Elements */
            $(this).select2({
                placeholder: $(this).children('option:first').html(),
                /* Get the first option as placeholder */
                ajax: {
                    url: function() {

                        if ($(this)[0]['name'] === "personDivision") {

                            return select2AjaxUrl + "divisionList";

                        } else if ($(this)[0]['name'] === "personDistrict") {

                            if ($("#personDivision").val() === "") {

                                $(this).select2("close");
                                return alert("<?= __("Please select the division"); ?>");
                            }
                            /* Generate the url */
                            return select2AjaxUrl + "districtList&division_id=" + $("#personDivision").val();

                        } else if ($(this)[0]['name'] === "personUpazila") {

                            if ($("#personDistrict").val() === "") {
                                $(this).select2("close");
                                return alert("<?= __("Please select the district"); ?>");
                            }
                            /* Generate the url */
                            return select2AjaxUrl + "upazilaList&district_id=" + $("#personDistrict").val();

                        } else if ($(this)[0]['name'] === "personInstitute") {


                            /* Generate the url */
                            return select2AjaxUrl + "instituteList&upazila_id=" + $("#personUpazila").val();

                        }

                    },
                    dataType: "json",
                    delay: 250,
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    },
                    cache: true
                }
            });

        });
    </script>

<?php

    // Include the modal footer
    modal_footer();
}

//*******************************  Add New person ******************** */
if (isset($_GET['page']) and $_GET['page'] == "addNewPerson") {

    // Error handaling
    if (empty($_POST["personFullName"])) {
        return _e("Please enter name.");
    } else if (empty($_POST["personPhone"])) {
        return _e("Please enter mobile number.");
    }

    // Insert the person into database
    $insertPerson = easyInsert(
        "persons",
        array(
            "person_full_name"    => $_POST["personFullName"],
            "person_type"         => $_POST["personType"],
            "person_address"      => $_POST["personAddress"],
            "person_designation"  => ($_POST["personType"] === "Student" or empty($_POST["personDesignation"])) ? NULL : $_POST["personDesignation"],
            "person_student_class" => ($_POST["personType"] !== 'Student' or empty($_POST["personClass"])) ? NULL : $_POST["personClass"],
            "person_institute"    => empty($_POST["personInstitute"]) ? NULL : $_POST["personInstitute"],
            "person_upazila"      => empty($_POST["personUpazila"]) ? NULL : $_POST["personUpazila"],
            "person_district"     => empty($_POST["personDistrict"]) ? NULL : $_POST["personDistrict"],
            "person_division"     => empty($_POST["personDivision"]) ? NULL : $_POST["personDivision"],
            "person_postal_code"  => $_POST["personPostalCode"],
            "person_phone"        => $_POST["personPhone"],
            "person_email"        => $_POST["personEmail"],
            "person_website"      => $_POST["personWebsite"],
            "leads_source"        => empty($_POST["leadSource"]) ? NULL : $_POST["leadSource"],
            "leads_collect_by"    => empty($_POST["dataCollectBy"]) ? NULL : $_POST["dataCollectBy"],
            "leads_collect_date"  => empty($_POST["collectionDate"]) ? NULL : $_POST["collectionDate"]
        ),
        array(
            "person_phone"             => $_POST["personPhone"]
        ),
        true
    );

    if (isset($insertPerson["status"]) and $insertPerson["status"] === "success") {

        _s("New person successfully added.");

        // Insert the subjects
        if ($_POST["personType"] === "Teacher" and isset($_POST["personSubject"])) {

            foreach ($_POST["personSubject"] as $key => $value) {

                easyInsert(
                    "persons_subject",
                    array(
                        "person_id"     => $insertPerson["last_insert_id"],
                        "subject_name"  => $value
                    )
                );
            }
        }


        // Insert Person Tag
        if (isset($_POST["personTags"])) {

            foreach ($_POST["personTags"] as $key => $value) {

                easyInsert(
                    "persons_tag",
                    array(
                        "person_id"     => $insertPerson["last_insert_id"],
                        "tags"  => $value
                    )
                );
            }
        }


    } else {

        _e($insertPerson);
        
    }
}


/*************************** Person List ***********************/
if (isset($_GET['page']) and $_GET['page'] == "personList") {

    $requestData = $_REQUEST;
    $getData = [];

    // List of all columns name for sorting
    $columns = array(
        "",
        "person.person_id",
        "person_full_name",
        "",
        "person_designation",
        "institute_name",
        "person_phone",
        "last_call_time",
        "person_address"
    );

    // Count Total recrods
    $totalFilteredRecords = $totalRecords = easySelectA(array(
        "table" => "persons",
        "fields" => "count(*) as totalRow",
        "where" => array(
            "is_trash = 0"
        )
    ))["data"][0]["totalRow"];

    if ($requestData['length'] == -1) {
        $requestData['length'] = $totalRecords;
    }

    if (!empty($requestData["search"]["value"]) or 
        !empty($requestData["columns"][1]['search']['value']) or 
        !empty($requestData["columns"][3]['search']['value']) or 
        !empty($requestData["columns"][4]['search']['value']) or 
        !empty($requestData["columns"][5]['search']['value']) or
        !empty($requestData["columns"][8]['search']['value']) or
        !empty($requestData["columns"][10]['search']['value']) or
        !empty($requestData["columns"][11]['search']['value'])
        
        ) {  // get data with search

        $dateFilter = "";
        if(!empty($requestData["columns"][1]['search']['value'])) {
            $dateRange = explode(" - ", safe_input($requestData["columns"][1]['search']['value']));
            $dateFilter = " AND (date(person_add_on)  BETWEEN '{$dateRange[0]}' and '{$dateRange[1]}')";
        }

        $searchFilter = "";
        if( !empty($requestData['search']['value']) ) {
            $searchFilter = " and ( 
                person_full_name LIKE '". safe_input($requestData['search']['value']) ."%'
                OR person_phone LIKE '%". safe_input($requestData['search']['value']) ."%'
                OR person_email LIKE '". safe_input($requestData['search']['value']) ."%'
                OR institute_name LIKE '". safe_input($requestData['search']['value']) ."%'
             )";
        }

        $getData = easySelect(
            "persons as person",
            "person.person_id as person_id, person_type, person_add_on, person_full_name, leads_source, last_call_time,
            person_student_class, person_designation, institute_name, institute_type, tag_list, person_phone, person_address, upazila_name, 
            district_name, division_name",
            array(   
                "left join {$table_prefix}districts on district_id = person_district",
                "left join {$table_prefix}divisions on division_id = person_division",
                "left join {$table_prefix}upazilas on upazila_id = person_upazila",
                "left join {$table_prefix}institute on institute_id = person_institute",
                "left join (select
                        person_id,
                        group_concat(tags SEPARATOR ', ') as tag_list
                    from {$table_prefix}persons_tag
                    group by person_id
                ) as tags on tags.person_id = person.person_id"
            ),
            array(
                "person.is_trash = 0",
                " AND person_type"  => $requestData["columns"][3]['search']['value'],
                " AND person_student_class"  => $requestData["columns"][5]['search']['value'],
                " AND person_district"  => $requestData["columns"][8]['search']['value'],
                " AND institute_type"  => $requestData["columns"][10]['search']['value'],
                " AND leads_source"  => $requestData["columns"][11]['search']['value'],
                " AND tag_list LIKE"  => "%{$requestData["columns"][4]['search']['value']}%" ,
                " {$searchFilter} {$dateFilter} "
            ),
            array(
                $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
            ),
            array(
                "start" => $requestData['start'],
                "length" => $requestData['length']
            )
        );

        $totalFilteredRecords = $getData ? $getData['count'] : 0;
        
    } else { // Get data withouth search

        $getData = easySelect(
            "persons as person",
            "person.person_id as person_id, person_type, person_add_on, person_full_name, leads_source, last_call_time,
            person_student_class, person_designation, institute_name, institute_type, tag_list, person_phone, person_address, upazila_name, district_name, division_name",
            array(
                "left join {$table_prefix}districts on district_id = person_district",
                "left join {$table_prefix}divisions on division_id = person_division",
                "left join {$table_prefix}upazilas on upazila_id = person_upazila",
                "left join {$table_prefix}institute on institute_id = person_institute",
                "left join (select
                        person_id,
                        group_concat(tags SEPARATOR ', ') as tag_list
                    from {$table_prefix}persons_tag
                    group by person_id
                ) as tags on tags.person_id = person.person_id"
            ),
            array("person.is_trash = 0"),
            array(
                $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
            ),
            array(
                "start" => $requestData['start'],
                "length" => $requestData['length']
            )
        );
    }

    $allData = [];
    // Check if there have more then zero data
    if (isset($getData['count']) and $getData['count'] > 0) {

        foreach ($getData['data'] as $key => $value) {

            $class = array(
                "1"   => __("Class One"),
                "2"   => __("Class Two"),
                "3"   => __("Class Three"),
                "4"   => __("Class Four"),
                "5"   => __("Class Five"),
                "6"   => __("Class Six"),
                "7"   => __("Class Seven"),
                "8"   => __("Class Eight"),
                "9"   => __("Class Nine"),
                "10"  => __("Class Ten"),
                "11"  => __("HSC 1st Year"),
                "12"  => __("HSC 2nd Year"),
                "13"  => __("Diploma/Graduation Level")
            );

            $allNestedData = [];
            $allNestedData[] = "";
            $allNestedData[] = $value['person_add_on'];
            $allNestedData[] = "{$value['person_full_name']}, {$value['person_designation']}";
            $allNestedData[] = $value['person_type'];
            $allNestedData[] = $value['tag_list'];
            $allNestedData[] = isset($class[$value['person_student_class']]) ? $class[$value['person_student_class']] : '';
            $allNestedData[] = $value['person_phone'];
            $allNestedData[] = ( $value['last_call_time'] !== null ? time_elapsed_string($value['last_call_time']) : '') . "<br/>{$value['last_call_time']}";
            $allNestedData[] = "{$value['person_address']}, {$value['upazila_name']}, {$value['district_name']}, {$value['division_name']}";
            $allNestedData[] = $value['institute_name'];
            $allNestedData[] = $value['institute_type'];
            $allNestedData[] = $value['leads_source'];
            // The action button
            $allNestedData[] = '<div class="btn-group">
                                  <button type="button" class="btn btn-xs btn-flat btn-primary dropdown-toggle" data-toggle="dropdown">
                                  action
                                  <span class="caret"></span>
                                  <span class="sr-only">Toggle Dropdown</span>
                                  </button>
                                  <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                    <li><a data-toggle="modal" href="' . full_website_address() . '/xhr/?module=marketing&page=viewSpecimenProduct&id=' . $value["person_id"] . '"  data-target="#modalDefault"><i class="fa fa-eye"></i> View Specimen</a></li>
                                    <li><a class="' . (current_user_can("persons.Edit") ? "" : "restricted ") . '" data-toggle="modal" href="' . full_website_address() . '/xhr/?icheck=false&module=marketing&page=editPerson&id=' . $value["person_id"] . '"  data-target="#modalDefault"><i class="fa fa-edit"></i> Edit Person</a></li>
                                    <li><a class="' . (current_user_can("persons.Delete") ? "" : "restricted ") . 'deleteEntry" href="' . full_website_address() . '/xhr/?module=marketing&page=deletePerson" data-to-be-deleted="' . $value["person_id"] . '"><i class="fa fa-minus-circle"></i> Delete</a></li>
                                  </ul>
                              </div>';

            $allData[] = $allNestedData;
        }
    }


    $jsonData = array(
        "draw"              => intval($requestData['draw']),
        "recordsTotal"      => intval($totalRecords),
        "recordsFiltered"   => intval($totalFilteredRecords),
        "data"              => $allData
    );

    // Encode in Json Formate
    echo json_encode($jsonData);
}


/***************** Delete Person ****************/
if (isset($_GET['page']) and $_GET['page'] == "deletePerson") {

    $deleteData = easyDelete(
        "persons",
        array(
            "person_id" => $_POST["datatoDelete"]
        )
    );

    if ($deleteData === true) {

        echo '{
          "title": "' . __("The person has been successfully deleted.") . '",
          "icon": "success"
      }';
    } else {

        echo '{
        "title": "Error: ' . __($deleteData) . '",
        "icon": "error"
    }';
    }
}

/************************** New Person **********************/
if (isset($_GET['page']) and $_GET['page'] == "editPerson") {

    // Include the modal header
    modal_header("Edit Person", full_website_address() . "/xhr/?module=marketing&page=UpdatePerson");

    $person = easySelectA(array(
        "table"   => "persons",
        "fields"  => "person_full_name, person_type, leads_source, leads_collect_by, leads_collect_date, person_designation, person_student_class, person_division, division_name, person_district, district_name,
                  person_upazila, upazila_name, person_postal_code, person_address, person_phone, person_email, person_website, person_institute, institute_name,
                  concat(emp_firstname, ' ', emp_lastname) as lead_collector_name",
        "where"   => array(
            "person_id" => $_GET["id"]
        ),
        "join"  => array(
            "left join {$table_prefix}districts on person_district = district_id",
            "left join {$table_prefix}divisions on person_division = division_id",
            "left join {$table_prefix}upazilas on person_upazila = upazila_id",
            "left join {$table_prefix}institute on person_institute = institute_id",
            "left join {$table_prefix}employees on emp_id = leads_collect_by"
        )
    ))["data"][0];

?>
    <div class="box-body">

        <div class="form-group required">
            <label for="personFullName"><?= __("Person Full Name:"); ?></label>
            <input type="text" name="personFullName" id="personFullName" value="<?php echo $person["person_full_name"]; ?>" placeholder="Enter person name" class="form-control" required>
        </div>
        <div class="row">
            <div class="form-group col-md-6">
                <label for="personType"><?= __("Person Type"); ?></label>
                <select name="personType" id="personType" class="form-control">
                    <?php
                    $personType = array('Teacher', 'Student', 'Guardian', 'Service Holder', 'Merchant');
                    foreach ($personType as $type) {
                        $selected = ($person["person_type"] === $type) ? "selected" : "";
                        echo "<option {$selected} value='$type'>$type</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group col-md-6 personDesignation">
                <label for="personDesignation"><?= __("Designation:"); ?></label>
                <input type="text" name="personDesignation" id="personDesignation" value="<?php echo $person["person_designation"]; ?>" placeholder="Enter person designation" class="form-control">
            </div>
            <div style="display: none;" class="form-group col-md-6 personClass">
                <label for="personClass"><?= __("Person Class"); ?></label>
                <select name="personClass" id="personClass" class="form-control select2">
                    <option value=""><?= __("Select Class"); ?></option>
                    <?php
                    $class = array(
                        "1"   => __("Class One"),
                        "2"   => __("Class Two"),
                        "3"   => __("Class Three"),
                        "4"   => __("Class Four"),
                        "5"   => __("Class Five"),
                        "6"   => __("Class Six"),
                        "7"   => __("Class Seven"),
                        "8"   => __("Class Eight"),
                        "9"   => __("Class Nine"),
                        "10"  => __("Class Ten"),
                        "11"  => __("HSC 1st Year"),
                        "12"  => __("HSC 2nd Year"),
                        "13"  => __("Diploma/Graduation Level")
                    );

                    foreach ($class as $key => $value) {
                        $selected = $person["person_student_class"] == $key ? "selected" : "";
                        echo "<option $selected value='$key'>$value</option>";
                    }
                    ?>
                </select>
            </div>

        </div>
        <div class="form-group personSubject">
            <label for="personSubject"><?= __("Person Subject"); ?></label>
            <select name="personSubject[]" id="personSubject" class="form-control select2Ajax" closeOnSelect="false" select2-tag="true" multiple select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=subjectList" style="width: 100%;">
                <option value=""><?= __("Select Subjects"); ?>....</option>
                <?php
                    $selectSubject = easySelectA(array(
                        "table"   => "persons_subject",
                        "fields"  => "subject_name",
                        "where"   => array(
                            "person_id" => $_GET["id"]
                        )
                    ));

                    if (isset($selectSubject["count"]) and $selectSubject["count"] > 0) {

                        foreach ($selectSubject["data"] as $key => $subject) {

                            echo "<option selected value='{$subject['subject_name']}'>{$subject['subject_name']}</option>";
                        }
                    }

                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="personTags"><?= __("Person Tag"); ?></label>
            <select name="personTags[]" id="personTags" class="form-control select2Ajax" closeOnSelect="false" select2-tag="true" multiple select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=personTagList" style="width: 100%;">
                <option value=""><?= __("Select Tages"); ?>....</option>
                <?php
                    $selectTags = easySelectA(array(
                        "table"   => "persons_tag",
                        "fields"  => "tags",
                        "where"   => array(
                            "person_id" => $_GET["id"]
                        )
                    ));

                    if (isset($selectTags["count"]) and $selectTags["count"] > 0) {

                        foreach ($selectTags["data"] as $key => $subject) {

                            echo "<option selected value='{$subject['tags']}'>{$subject['tags']}</option>";
                        }
                    }

                ?>
            </select>
        </div>
        <div class="row">
            <div class="form-group col-md-6 required">
                <label for="personPhone"><?= __("Phone:"); ?></label>
                <input type="text" name="personPhone" id="personPhone" value="<?php echo $person["person_phone"]; ?>" class="form-control">
            </div>
            <div class="form-group col-md-6">
                <label for="personEmail"><?= __("Email:"); ?></label>
                <input type="email" name="personEmail" id="personEmail" value="<?php echo $person["person_email"]; ?>" class="form-control">
            </div>
            <div class="form-group col-md-6">
                <label for="personDivision"><?= __("Person Division:"); ?></label>
                <select name="personDivision" id="personDivision" class="form-control" style="width: 100%;">
                    <option value=""><?= __("Select Division"); ?></option>
                    <option selected value="<?php echo $person["person_division"]; ?>"><?php echo $person["division_name"]; ?></option>
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="personDistrict"><?= __("Person District:"); ?></label>
                <select name="personDistrict" id="personDistrict" class="form-control" style="width: 100%;">
                    <option value=""><?= __("Select District"); ?></option>
                    <option selected value="<?php echo $person["person_district"]; ?>"><?php echo $person["district_name"]; ?></option>
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="personUpazila"><?= __("Person Upazila:"); ?></label>
                <select name="personUpazila" id="personUpazila" class="form-control" style="width: 100%;">
                    <option value=""><?= __("Select Upazila"); ?>...</option>
                    <option selected value="<?php echo $person["person_upazila"]; ?>"><?php echo $person["upazila_name"]; ?></option>
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="personPostalCode"><?= __("Postal Code:"); ?></label>
                <input type="text" name="personPostalCode" id="personPostalCode" value="<?php echo $person["person_postal_code"]; ?>" class="form-control">
            </div>
        </div>
        <div class="form-group">
            <label for="personInstitute"><?= __("Organization/ Institute:"); ?></label>
            <select name="personInstitute" id="personInstitute" class="form-control" style="width: 100%;">
                <option value=""><?php echo __("Select Institute"); ?>...</option>
                <option selected value="<?php echo $person["person_institute"]; ?>"><?php echo $person["institute_name"]; ?></option>
            </select>
        </div>
        <div class="form-group">
            <label for="personAddress"><?= __("Person Address:"); ?></label>
            <textarea name="personAddress" id="personAddress" rows="3" class="form-control"> <?php echo $person["person_address"]; ?> </textarea>
        </div>
        <div class="form-group">
            <label for="personWebsite"><?= __("Website:"); ?></label>
            <input type="text" name="personWebsite" id="personWebsite" value="<?php echo $person["person_website"]; ?>" class="form-control">
        </div>
        <div class="form-group">
            <label for="leadSource"><?= __("Data Source:"); ?></label>
            <select name="leadSource" id="leadSource" class="form-control select2Ajax" select2-tag="true" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=leadsDataSource" style="width: 100%;">
                <option value=""><?= __("Select Data Source"); ?>....</option>
                <option selected value="<?php echo $person["leads_source"]; ?>"><?php echo $person["leads_source"]; ?></option>
            </select>
        </div>
        <div class="row">
            <div class="form-group col-md-8">
                <label for="dataCollectBy"><?= __("Data Collect By:"); ?></label>
                <select name="dataCollectBy" id="dataCollectBy" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=MRList" style="width: 100%;">
                    <option value=""><?= __("Select employee"); ?>....</option>
                    <option selected value="<?php echo $person["leads_collect_by"]; ?>"><?php echo $person["lead_collector_name"]; ?></option>
                </select>
            </div>
            <div class="form-group col-md-4">
                <label for="collectionDate"><?= __("Collection Date:"); ?></label>
                <input type="text" name="collectionDate" id="collectionDate" value="<?php echo $person["leads_collect_date"]; ?>" class="form-control datePicker">
            </div>
        </div>

        <input type="hidden" name="personId" value="<?php echo safe_entities($_GET["id"]); ?>">

    </div>
    <!-- /Box body-->

    <script>
        $(document).ready(function() {
            function taskOnPersonChange(personType) {

                if (personType == "Teacher") {

                    $(".personDesignation, .personSubject").show();
                    $(".personClass").hide();

                } else if (personType == "Student") {

                    $(".personDesignation, .personSubject").hide();
                    $(".personClass").show();

                } else if (personType == "Service Holder" || personType == "Merchant" || personType == "Guardian") {

                    $(".personSubject, .personClass").hide();
                    $(".personDesignation").show();

                }

            }


            taskOnPersonChange("<?php echo $person["person_type"]; ?>");


            $(document).on("change", "#personType", function() {

                taskOnPersonChange($(this).val());

            });

            /* If division, district and upazila change then clear the lower fields */
            $(document).on("change", "#personDivision", function() {
                $("#personDistrict").val(null).trigger("change");
                $("#personUpazila").val(null).trigger("change");
                $("#personInstitute").val(null).trigger("change");
            });
            $(document).on("change", "#personDistrict", function() {
                $("#personUpazila").val(null).trigger("change");
                $("#personInstitute").val(null).trigger("change");
            });
            $(document).on("change", "#personUpazila", function() {
                $("#personInstitute").val(null).trigger("change");
            });


            $(document).on('mouseenter focus', '#personDivision, #personDistrict, #personUpazila, #personInstitute', function() {

                var select2AjaxUrl = '<?php echo full_website_address() ?>/info/?module=select2&page=';

                /* Initialize Select Ajax Elements */
                $(this).select2({
                    placeholder: $(this).children('option:first').html(),
                    /* Get the first option as placeholder */
                    ajax: {
                        url: function() {
                            if ($(this)[0]['name'] === "personDivision") {

                                return select2AjaxUrl + "divisionList";

                            } else if ($(this)[0]['name'] === "personDistrict") {

                                if ($("#personDivision").val() === "") {

                                    $(this).select2("close");
                                    return alert("<?= __("Please select the division"); ?>");
                                }
                                /* Generate the url */
                                return select2AjaxUrl + "districtList&division_id=" + $("#personDivision").val();

                            } else if ($(this)[0]['name'] === "personUpazila") {

                                if ($("#personDistrict").val() === "") {
                                    $(this).select2("close");
                                    return alert("<?= __("Please select the district"); ?>");
                                }
                                /* Generate the url */
                                return select2AjaxUrl + "upazilaList&district_id=" + $("#personDistrict").val();

                            } else if ($(this)[0]['name'] === "personInstitute") {

                                if ($("#personUpazila").val() === "") {
                                    $(this).select2("close");
                                    return alert("<?= __("Please select the upazila"); ?>");
                                }
                                /* Generate the url */
                                return select2AjaxUrl + "instituteList&upazila_id=" + $("#personUpazila").val();

                            }

                        },
                        dataType: "json",
                        delay: 250,
                        processResults: function(data) {
                            return {
                                results: data
                            };
                        },
                        cache: true
                    }
                });

            });
        });
    </script>

<?php

    // Include the modal footer
    modal_footer();
}

//*******************************  Update person ******************** */
if (isset($_GET['page']) and $_GET['page'] == "UpdatePerson") {

    // Error handaling
    if (empty($_POST["personFullName"])) {
        return _e("Please enter name.");
    } else if (empty($_POST["personPhone"])) {
        return _e("Please enter mobile number.");
    }

    // Insert the person into database
    $UpdatePerson = easyUpdate(
        "persons",
        array(
            "person_full_name"    => $_POST["personFullName"],
            "person_type"         => $_POST["personType"],
            "person_address"      => $_POST["personAddress"],
            "person_designation"  => ($_POST["personType"] === "Student" or empty($_POST["personDesignation"])) ? NULL : $_POST["personDesignation"],
            "person_student_class" => ($_POST["personType"] !== 'Student' or empty($_POST["personClass"])) ? NULL : $_POST["personClass"],
            "person_institute"    => empty($_POST["personInstitute"]) ? NULL : $_POST["personInstitute"],
            "person_upazila"      => empty($_POST["personUpazila"]) ? NULL : $_POST["personUpazila"],
            "person_district"     => empty($_POST["personDistrict"]) ? NULL : $_POST["personDistrict"],
            "person_division"     => empty($_POST["personDivision"]) ? NULL : $_POST["personDivision"],
            "person_postal_code"  => $_POST["personPostalCode"],
            "person_phone"        => $_POST["personPhone"],
            "person_email"        => $_POST["personEmail"],
            "person_website"      => $_POST["personWebsite"],
            "leads_source"        => empty($_POST["leadSource"]) ? NULL : $_POST["leadSource"],
            "leads_collect_by"    => empty($_POST["dataCollectBy"]) ? NULL : $_POST["dataCollectBy"],
            "leads_collect_date"  => empty($_POST["collectionDate"]) ? NULL : $_POST["collectionDate"]
        ),
        array(
            "person_id"             => $_POST["personId"]
        ),
        true
    );

    if ($UpdatePerson === true) {

        _s("The person successfully updated.");

        // Delete Previous Subjects
        easyPermDelete(
            "persons_subject",
            array(
                "person_id"   => $_POST["personId"]
            )
        );

        // Insert the subjects
        if ($_POST["personType"] === "Teacher" and isset($_POST["personSubject"])) {

            foreach ($_POST["personSubject"] as $key => $value) {

                easyInsert(
                    "persons_subject",
                    array(
                        "person_id"     => $_POST["personId"],
                        "subject_name"  => $value
                    )
                );
            }
        }

        // Delete Previous Tags
        easyPermDelete(
            "persons_tag",
            array(
                "person_id"   => $_POST["personId"]
            )
        );

        // Insert Person Tag
        if (isset($_POST["personTags"])) {

            foreach ($_POST["personTags"] as $key => $value) {

                easyInsert(
                    "persons_tag",
                    array(
                        "person_id"     => $_POST["personId"],
                        "tags"  => $value
                    )
                );
            }

        }


    } else {
        _e($UpdatePerson);
    }
}


/************************** Invoice Product **********************/
if(isset($_GET['page']) and $_GET['page'] == "viewSpecimenProduct") {
  

        // Select Sales item
        $selectSpecimenItems = easySelectA(array(
            "table"     => "sc_distribution as distribution",
            "fields"    => "product_name, scd_product_qnt, scd_date, concat(emp_firstname, ' ', emp_lastname) as distributor",
            "join"      => array(
                "left join {$table_prefix}products on product_id = scd_product_id",
                "left join {$table_prefix}employees on emp_id = scd_distributor "
            ),
            "where" => array(
                "is_bundle_item = 0 and distribution.is_trash = 0 and scd_person_id" => $_GET["id"]
            )
        ));

    ?>

    <div class="modal-header">
        <h4 class="modal-title">Specimen Copies</h4>
    </div>

    <div class="modal-body">


        <table class="table table-striped table-condensed">
            <tbody>
            <tr>
                <td>Date</td>
                <td>Products</td>
                <td>Qty</td>
                <td>Distributor</td>
            </tr>

            <?php 


                if($selectSpecimenItems !== false) {

                    foreach($selectSpecimenItems["data"] as $key => $specimen) {

                        echo "<tr>";
                        echo " <td>{$specimen['scd_date']}</td>";
                        echo " <td>{$specimen['product_name']}</td>";
                        echo " <td>" . number_format($specimen['scd_product_qnt'],2) . "</td>";
                        echo " <td>{$specimen['distributor']}</td>";
                        echo "</tr>";
    
                    }

                } else {
                    echo "<td colspan='3'>No Specimen Copy found</td>";
                }
                

            ?>     

            </tbody>


        </table>
    
    </div> <!-- /.modal-body -->

    <?php
  
}

/*************************** specimenCopyOverview  ***********************/
if (isset($_GET['page']) and $_GET['page'] == "specimenCopyOverview") {

    $requestData = $_REQUEST;
    $getData = [];

    // List of all columns name for sorting
    $columns = array(
        "",
        "emp_firstname, product_category_id",
        "emp_firstname",
        "product_name",
        "scDispatch",
        "scReturn",
        "totalDistributed",
        "",
        "sc_items_product_unit"

    );

    // Count Total recrods
    $totalFilteredRecords = $totalRecords = easySelectA(array(
        "table" => "product_stock",
        "fields" => " count(DISTINCT stock_product_id, stock_employee_id ) as totalRow",
        "where" => array(
            "is_trash = 0 and stock_type = 'specimen-copy'"
        )
    ))["data"][0]["totalRow"];

    if ($requestData['length'] == -1) {
        $requestData['length'] = $totalRecords;
    }

    if (  !empty($requestData["search"]["value"]) or !empty($requestData["columns"][3]['search']['value']) or !empty($requestData["columns"][4]['search']['value']) ) {  // get data with search

        $productEditionFilter = !empty($requestData["columns"][3]['search']['value']) ? " product_edition = '{$requestData["columns"][3]['search']['value']}' " : " product_type != 'Child' ";

        $getData = easySelectA(array(
            "table"   => "product_stock as scMain",
            "fields"  => "product_unit, stock_product_id, stock_employee_id, category_name, product_edition, product_sale_price,
                  sum(case when stock_type = 'specimen-copy' and stock_item_qty is not null then stock_item_qty else 0 end) as scDispatch, 
                  sum(case when stock_type = 'specimen-copy-return' and stock_item_qty is not null then stock_item_qty else 0 end) as scReturn, 
                  if(totalDistributed is null, 0, totalDistributed) as getTotalDistributed, emp_firstname, emp_lastname, product_name",
            "join"    => array(
                "left join {$table_prefix}employees on stock_employee_id = emp_id",
                "left join {$table_prefix}warehouses on stock_warehouse_id = warehouse_id",
                "left join {$table_prefix}products on stock_product_id = product_id",
                "left join {$table_prefix}product_category on product_category_id = category_id",
                "left join ( select 
                    scd_distributor, 
                    scd_product_id, 
                    sum(scd_product_qnt) as totalDistributed 
                from {$table_prefix}sc_distribution 
                group by scd_distributor, scd_product_id 
            ) as sc_items_distribution on stock_product_id = scd_product_id and stock_employee_id = scd_distributor"
            ),
            "where" => array(
                "scMain.is_trash = 0 and scMain.stock_sc_id is not null and {$productEditionFilter}",
                " AND ( concat(emp_firstname, ' ', emp_lastname) LIKE '%". safe_input($requestData['search']['value']) ."%'",
                " or product_name LIKE" => "%" . $requestData['search']['value'] . "%",
                ")",
                " AND product_category_id" => $requestData["columns"][4]['search']['value'],
            ),
            "groupby" => "stock_product_id, stock_employee_id",
            "orderBy"  => array(
                $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
            ),
            "limit" => array(
                "start" => $requestData['start'],
                "length" => $requestData['length']
            )
        ));

        $totalFilteredRecords = $getData ? $getData["count"] : 0;

    } else { // Get data withouth search

        $getData = easySelectA(array(
            "table"   => "product_stock as scMain",
            "fields"  => "product_unit, stock_product_id, stock_employee_id, category_name, product_edition, product_sale_price,
                  sum(case when stock_type = 'specimen-copy' and stock_item_qty is not null then stock_item_qty else 0 end) as scDispatch, 
                  sum(case when stock_type = 'specimen-copy-return' and stock_item_qty is not null then stock_item_qty else 0 end) as scReturn, 
                  if(totalDistributed is null, 0, totalDistributed) as getTotalDistributed, emp_firstname, emp_lastname, product_name",
            "join"    => array(
                "left join {$table_prefix}employees on stock_employee_id = emp_id",
                "left join {$table_prefix}products on stock_product_id = product_id",
                "left join {$table_prefix}product_category on product_category_id = category_id",
                "left join ( select 
                scd_distributor, 
                scd_product_id, 
                sum(scd_product_qnt) as totalDistributed 
            from {$table_prefix}sc_distribution 
            group by scd_distributor, scd_product_id
        ) as sc_items_distribution on stock_product_id = scd_product_id and stock_employee_id = scd_distributor"
            ),
            "where" =>  array(
                "scMain.is_trash = 0 and scMain.stock_sc_id is not null"
            ),
            "groupby" => "stock_product_id, stock_employee_id",
            "orderBy"  => array(
                $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
            ),
            "limit" => array(
                "start" => $requestData['start'],
                "length" => $requestData['length']
            )
        ));
    }

    $allData = [];
    // Check if there have more then zero data
    if (isset($getData['count']) and $getData['count'] > 0) {

        foreach ($getData['data'] as $key => $value) {

            $inHand = $value['scDispatch'] - ($value['scReturn'] + $value['getTotalDistributed']);

            $allNestedData = [];
            $allNestedData[] = "";
            $allNestedData[] = "{$value['emp_firstname']} {$value['emp_lastname']}";
            $allNestedData[] = $value['product_name'];
            $allNestedData[] = $value['product_edition'];
            $allNestedData[] = $value['category_name'];
            $allNestedData[] = $value['scDispatch'];
            $allNestedData[] = $value['scReturn'];
            $allNestedData[] = $value['getTotalDistributed'];
            $allNestedData[] = $inHand;
            $allNestedData[] = $inHand * $value['product_sale_price'];
            $allNestedData[] = $value['product_unit'];

            $allData[] = $allNestedData;
        }
    }


    $jsonData = array(
        "draw"              => intval($requestData['draw']),
        "recordsTotal"      => intval($totalRecords),
        "recordsFiltered"   => intval($totalFilteredRecords),
        "data"              => $allData
    );

    // Encode in Json Formate
    echo json_encode($jsonData);
}

/*************************** specimenCopyList  ***********************/
if (isset($_GET['page']) and $_GET['page'] == "specimenCopyList") {

    $requestData = $_REQUEST;
    $getData = [];

    // List of all columns name for sorting
    $columns = array(
        "",
        "sc_add_on",
        "sc_id",
        "sc_type",
        "emp_firstname"
    );

    // Count Total recrods
    $totalFilteredRecords = $totalRecords = easySelectA(array(
        "table" => "specimen_copies",
        "fields" => "count(*) as totalRow",
        "where" => array(
            "is_trash = 0"
        )
    ))["data"][0]["totalRow"];

    if ($requestData['length'] == -1) {
        $requestData['length'] = $totalRecords;
    }

    if (!empty($requestData["search"]["value"])) {  // get data with search

        $getData = easySelectA(array(
            "table"   => "specimen_copies as specimen_copy",
            "fields"  => "sc_id, sc_date, sc_type, emp_firstname, emp_lastname, warehouse_name",
            "join"    => array(
                "left join {$table_prefix}employees on sc_employee_id = emp_id",
                "left join {$table_prefix}warehouses on sc_warehouse_id = warehouse_id"
            ),
            "where" => array(
                "specimen_copy.is_trash = 0 and (emp_firstname LIKE" => "%" . $requestData['search']['value'] . "%",
                " or emp_lastname LIKE" => "%" . $requestData['search']['value'] . "%",
                " or warehouse_name LIKE" => $requestData['search']['value'] . "%",
                ")"
            ),
            "orderBy"  => array(
                $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
            ),
            "limit" => array(
                "start" => $requestData['start'],
                "length" => $requestData['length']
            )
        ));

        $totalFilteredRecords = $getData ? $getData["count"] : 0;
    } else { // Get data withouth search

        $getData = easySelectA(array(
            "table"   => "specimen_copies as specimen_copy",
            "fields"  => "sc_id, sc_date, sc_type, emp_firstname, emp_lastname, warehouse_name",
            "join"    => array(
                "left join {$table_prefix}employees on sc_employee_id = emp_id",
                "left join {$table_prefix}warehouses on sc_warehouse_id = warehouse_id"
            ),
            "where" => array(
                "specimen_copy.is_trash = 0"
            ),
            "orderBy"  => array(
                $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
            ),
            "limit" => array(
                "start" => $requestData['start'],
                "length" => $requestData['length']
            )
        ));
    }

    $allData = [];
    // Check if there have more then zero data
    if (isset($getData['count']) and $getData['count'] > 0) {

        foreach ($getData['data'] as $key => $value) {
            $allNestedData = [];
            $allNestedData[] = "";
            $allNestedData[] = $value['sc_date'];
            $allNestedData[] = "SC-" . $value['sc_id'];
            $allNestedData[] = $value['sc_type'];
            $allNestedData[] = $value['warehouse_name'];
            $allNestedData[] = "{$value['emp_firstname']} {$value['emp_lastname']}";
            // The action button
            $allNestedData[] = '<div class="btn-group">
                                  <button type="button" class="btn btn-xs btn-flat btn-primary dropdown-toggle" data-toggle="dropdown">
                                  action
                                  <span class="caret"></span>
                                  <span class="sr-only">Toggle Dropdown</span>
                                  </button>
                                  <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                    <li><a class="' . (current_user_can("specimen_copy.Delete") ? "" : "restricted ") . 'deleteEntry" href="' . full_website_address() . '/xhr/?module=marketing&page=deleteSpecimenCopy" data-to-be-deleted="' . $value["sc_id"] . '"><i class="fa fa-minus-circle"></i> Delete</a></li>
                                    <li><a href="' . full_website_address() . '/invoice-print/?autoPrint=false&invoiceType=scpecimenCopy&id=' . $value["sc_id"] . '"><i class="fa fa-print"></i> View Items</a></li>
                                    <li><a href="'. full_website_address() .'/marketing/edit-specimen-copy/?edit='. $value["sc_id"] .'"><i class="fa fa-edit"></i> Edit</a></li>
                                  </ul>
                              </div>';

            $allData[] = $allNestedData;
        }
    }


    $jsonData = array(
        "draw"              => intval($requestData['draw']),
        "recordsTotal"      => intval($totalRecords),
        "recordsFiltered"   => intval($totalFilteredRecords),
        "data"              => $allData
    );

    // Encode in Json Formate
    echo json_encode($jsonData);
}


/***************** deleteSpecimenCopy ****************/
if (isset($_GET['page']) and $_GET['page'] == "deleteSpecimenCopy") {

    $deleteData = easyDelete(
        "specimen_copies",
        array(
            "sc_id" => $_POST["datatoDelete"]
        )
    );

    if ($deleteData === true) {

        echo '{
          "title": "' . __("Successfully deleted.") . '",
          "icon": "success"
      }';
    } else {

        echo '{
        "title": "Error: ' . $deleteData . '",
        "icon": "error"
    }';
    }
}


/************************** New Distribution **********************/
if (isset($_GET['page']) and $_GET['page'] == "newScDistribution") {

    // Include the modal header
    modal_header("Specimen Copy Distribution", full_website_address() . "/xhr/?module=marketing&page=addNewScDistribution");

?>
    <div class="box-body">

        <div class="form-group required">
            <label for="scDistributor"><?= __("Distributor:"); ?></label>
            <select name="scDistributor" id="scDistributor" select2-minimum-input-length="1" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=employeeListAll" style="width: 100%;" required>
                <option value=""><?= __("Select distributor"); ?>....</option>
            </select>
        </div>
        <div class="form-group required">
            <label for="scRecipient"><?= __("Recipient/Teacher:"); ?></label>
            <div class="input-group">
                <select name="scRecipient" id="scRecipient" select2-minimum-input-length="1" class="form-control select2Ajax" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=personList" style="width: 100%;" required>
                    <option value=""><?= __("Select Recepient"); ?>....</option>
                </select>
                <div class="input-group-addon">
                    <a data-toggle="modal" data-target="#modalDefaultMdm" href="<?php echo full_website_address(); ?>/xhr/?module=marketing&page=newPerson"><i class="fa fa-plus"></i></a>
                </div>
            </div>
        </div>
        <div class="form-group required">
            <label for="scProduct"><?= __("Product:"); ?></label>
            <select name="scProduct" id="scProduct" class="form-control select2Ajax" select2-minimum-input-length="1" select2-ajax-url="<?php echo full_website_address() ?>/info/?module=select2&page=productList" style="width: 100%;" required>
                <option value=""><?= __("Select Product"); ?>....</option>
            </select>
        </div>

        <div class="form-group col-md-8 row required">
            <label for="scProductQnt"><?= __("Quantity"); ?></label>
            <input type="number" name="scProductQnt" id="scProductQnt" class="form-control" required>
        </div>

        <div class="form-group col-md-5 row required">
            <label for="scProductUnit"><?= __("Unit"); ?></label>
            <select name="scProductUnit" id="scProductUnit" class="form-control" required>
                <option value=""><?= __("Select Product"); ?>...</option>
            </select>
        </div>


    </div>
    <!-- /Box body-->

    <script>
        /* Get the Product Details */
        $(document).on("change", "#scProduct", function() {

            var productId = $(this).val();

            $.ajax({
                url: "<?php echo full_website_address(); ?>/info/?module=data&page=productDetails&product_id=" + productId,
                success: function(data, status) {
                    if (status == "success") {
                        var product = JSON.parse(data);

                        var unitViriant = "";

                        product.unitVariant.forEach(unit => {

                            /* set default unit */
                            if (unit.puv_default == 1) {
                                selectedUnit = "selected";
                            } else {
                                selectedUnit = "";
                            }

                            unitViriant += "<option " + selectedUnit + " value='" + unit.puv_name + "'>" + unit.puv_name + "</option>";

                        });

                        $("#scProductUnit").html(unitViriant);

                    }
                }
            });

        });
    </script>

<?php

    // Include the modal footer
    modal_footer();
}



/*************************** specimenCopyDistributionList  ***********************/
if (isset($_GET['page']) and $_GET['page'] == "specimenCopyDistributionList") {

    $requestData = $_REQUEST;
    $getData = [];

    // List of all columns name for sorting
    $columns = array(
        "",
        "scd_id",
        "emp_firstname",
        "person_full_name",
        "product_name",
        "scd_product_qnt",
        "product_unit"

    );

    // Count Total recrods
    $totalFilteredRecords = $totalRecords = easySelectA(array(
        "table" => "sc_distribution",
        "fields" => "count(*) as totalRow",
        "where" => array(
            "is_trash = 0"
        )
    ))["data"][0]["totalRow"];

    if ($requestData['length'] == -1) {
        $requestData['length'] = $totalRecords;
    }

    if (!empty($requestData["search"]["value"]) or 
        !empty($requestData["columns"][1]['search']['value']) or
        !empty($requestData["columns"][2]['search']['value']) or
        !empty($requestData["columns"][3]['search']['value'])
    ) {  // get data with search

        $dateRange[0] = "";
        $dateRange[1] = "";
        if(!empty($requestData["columns"][1]['search']['value'])) {
            $dateRange = explode(" - ", safe_input($requestData["columns"][1]['search']['value']));
        }

        $getData = easySelectA(array(
            "table"   => "sc_distribution as sc_distribution",
            "fields"  => "scd_id, scd_date, person_phone, emp_firstname, emp_lastname, person_full_name, product_name, scd_product_qnt as scd_product_qnt, product_unit, institute_name",
            "join"    => array(
                "left join {$table_prefix}employees on emp_id = scd_distributor",
                "left join {$table_prefix}products on product_id = scd_product_id",
                "left join {$table_prefix}persons on person_id = scd_person_id",
                "left join {$table_prefix}institute on institute_id = person_institute"
            ),
            "where" => array(
                "sc_distribution.is_trash = 0",
                " AND product_name LIKE" => "%" . $requestData['search']['value'] . "%",
                " AND scd_distributor" => $requestData["columns"][2]['search']['value'],
                " AND scd_person_id" => $requestData["columns"][3]['search']['value'],
                " AND scd_date between '{$dateRange[0]}' and '{$dateRange[1]}'"
            ),
            "orderBy"  => array(
                $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
            ),
            "limit" => array(
                "start" => $requestData['start'],
                "length" => $requestData['length']
            )
        ));

        $totalFilteredRecords = $getData ? $getData["count"] : 0;
        
    } else { // Get data withouth search

        $getData = easySelectA(array(
            "table"   => "sc_distribution as sc_distribution",
            "fields"  => "scd_id, scd_date, person_phone, emp_firstname, emp_lastname, person_full_name, product_name, scd_product_qnt as scd_product_qnt, product_unit, institute_name",
            "join"    => array(
                "left join {$table_prefix}employees on emp_id = scd_distributor",
                "left join {$table_prefix}products on product_id = scd_product_id",
                "left join {$table_prefix}persons on person_id = scd_person_id",
                "left join {$table_prefix}institute on institute_id = person_institute"
            ),
            "where" => array(
                "sc_distribution.is_trash = 0"
            ),
            "orderBy"  => array(
                $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
            ),
            "limit" => array(
                "start" => $requestData['start'],
                "length" => $requestData['length']
            )
        ));
    }


    $allData = [];
    // Check if there have more then zero data
    if (isset($getData['count']) and $getData['count'] > 0) {

        foreach ($getData['data'] as $key => $value) {
            $allNestedData = [];
            $allNestedData[] = "";
            $allNestedData[] = $value['scd_date'];
            $allNestedData[] = "{$value['emp_firstname']} {$value['emp_lastname']}";
            $allNestedData[] = $value['person_full_name'] . ", " . $value['institute_name'];
            $allNestedData[] = $value['person_phone'];
            $allNestedData[] = $value['product_name'];
            $allNestedData[] = $value['scd_product_qnt'];
            $allNestedData[] = $value['product_unit'];
            $allNestedData[] = '<div class="btn-group">
                                    <button type="button" class="btn btn-xs btn-flat btn-primary dropdown-toggle" data-toggle="dropdown">
                                    action
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                    <li><a class="' . (current_user_can("specimen_copy_distribution.Delete") ? "" : "restricted ") . 'deleteEntry" href="' . full_website_address() . '/xhr/?module=marketing&page=deleteScDistribution" data-to-be-deleted="' . $value["scd_id"] . '"><i class="fa fa-minus-circle"></i> Delete</a></li>
                                    </ul>
                                </div>';

            $allData[] = $allNestedData;
        }
    }


    $jsonData = array(
        "draw"              => intval($requestData['draw']),
        "recordsTotal"      => intval($totalRecords),
        "recordsFiltered"   => intval($totalFilteredRecords),
        "data"              => $allData
    );

    // Encode in Json Format
    echo json_encode($jsonData);
}



/***************** deleteScDistribution ****************/
if (isset($_GET['page']) and $_GET['page'] == "deleteScDistribution") {

    $deleteData = easyDelete(
        "sc_distribution",
        array(
            "scd_id" => $_POST["datatoDelete"]
        )
    );

    if ($deleteData === true) {

        echo '{
          "title": "' . __("Successfully deleted.") . '",
          "icon": "success"
      }';
    } else {

        echo '{
        "title": "Error: ' . $deleteData . '",
        "icon": "error"
    }';
    }
}


/************************** New Person **********************/
if (isset($_GET['page']) and $_GET['page'] == "newInstitute") {

    // Include the modal header
    modal_header("New Institute", full_website_address() . "/xhr/?module=marketing&page=addNewInstitute");

?>
    <div class="box-body">

        <div class="form-group required">
            <label for="instituteName"><?= __("Institute Name:"); ?></label>
            <input type="text" name="instituteName" id="instituteName" placeholder="Enter institute name" autoComplete="off" class="form-control" required>
        </div>
        <div class="form-group required">
            <label for="instituteType"><?= __("Institute Type:"); ?></label>
            <select name="instituteType" id="instituteType" class="form-control" style="width: 100%;" required>
                <option value=""><?= __("Select type"); ?>...</option>
                <?php
                $instituteType = array('School', 'College', 'University', 'Coaching', 'Library', 'Store');
                foreach ($instituteType as $type) {
                    echo "<option value='$type'>$type</option>";
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label><?= __("Existing Institute:"); ?></label>
            <div style="border: 1px dotted; padding: 6px; height: 90px; overflow: auto;" id="showInstitute">
                <span style="color: #8a8a8a;"><?= __("Please type institute name above."); ?></span>
            </div>
        </div>
        <div class="form-group">
            <label for="instituteEIIN"><?= __("EIIN:"); ?></label>
            <input type="number" name="instituteEIIN" id="instituteEIIN" placeholder="Enter EIIN" class="form-control">
        </div>
        <div class="form-group required">
            <label for="instituteDivision"><?= __("Institute Division:"); ?></label>
            <select name="instituteDivision" id="instituteDivision" class="form-control" style="width: 100%;" required>
                <option value=""><?= __("Select Division"); ?>...</option>
            </select>
        </div>
        <div class="form-group required">
            <label for="instituteDistrict"><?= __("Institute District:"); ?></label>
            <select name="instituteDistrict" id="instituteDistrict" class="form-control" style="width: 100%;" required>
                <option value=""><?= __("Select District"); ?>...</option>
            </select>
        </div>
        <div class="form-group required">
            <label for="instituteUpazila"><?= __("Institute Upazila:"); ?></label>
            <select name="instituteUpazila" id="instituteUpazila" class="form-control" style="width: 100%;" required>
                <option value=""><?= __("Select Upazila"); ?>...</option>
            </select>
        </div>
        <div class="form-group">
            <label for="instituteAddress"><?= __("Address:"); ?></label>
            <textarea name="instituteAddress" id="instituteAddress" rows="3" class="form-control"> </textarea>
        </div>
        <div class="form-group">
            <label for="instituteWebsite"><?= __("Website:"); ?></label>
            <input type="text" name="instituteWebsite" id="instituteWebsite" value="" class="form-control">
        </div>

    </div>
    <!-- /Box body-->

    <script>
        /* If division and district change then clear the lower fields */
        $(document).on("change", "#instituteDivision, #instituteDistrict", function() {

            if ($("#instituteDistrict").val() !== "" && $(this)[0]['name'] !== "instituteDistrict") {
                $("#instituteDistrict").val(null).trigger("change");
            }

            if ($("#instituteUpazila").val() !== "") {
                $("#instituteUpazila").val(null).trigger("change");
            }

        });

        $(document).on('mouseenter focus', '#instituteDivision, #instituteDistrict, #instituteUpazila', function() {

            var select2AjaxUrl = '<?php echo full_website_address() ?>/info/?module=select2&page=';

            /* Initialize Select Ajax Elements */
            $(this).select2({
                placeholder: $(this).children('option:first').html(),
                /* Get the first option as placeholder */
                ajax: {
                    url: function() {
                        if ($(this)[0]['name'] === "instituteDivision") {

                            return select2AjaxUrl + "divisionList";

                        } else if ($(this)[0]['name'] === "instituteDistrict") {

                            if ($("#instituteDivision").val() === "") {

                                $(this).select2("close");
                                return alert("<?= __("Please select the division"); ?>");
                            }
                            /* Generate the url */
                            return select2AjaxUrl + "districtList&division_id=" + $("#instituteDivision").val();

                        } else if ($(this)[0]['name'] === "instituteUpazila") {

                            if ($("#instituteDistrict").val() === "") {
                                $(this).select2("close");
                                return alert("<?= __("Please select the district"); ?>");
                            }
                            /* Generate the url */
                            return select2AjaxUrl + "upazilaList&district_id=" + $("#instituteDistrict").val();

                        }
                    },
                    dataType: "json",
                    delay: 250,
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    },
                    cache: true
                }
            });

        });

        $(document).on('keyup', "#instituteName", function() {

            $.ajax({
                url: '<?php echo full_website_address(); ?>/info/?module=select2&page=instituteList&q=' + $("#instituteName").val(),

                success: function(data, status) {

                    var institute = JSON.parse(data);

                    var allInstitute = "";
                    $.each(institute, function(key, value) {

                        allInstitute += "<p style='line-height: 1; margin: 2px; font-size: 14px; background: whitesmoke; padding: 5px;'>" + value["text"] + "</p>";

                    });

                    $("#showInstitute").html(allInstitute);

                }


            });


        });
    </script>

<?php

    // Include the modal footer
    modal_footer();
}

//*******************************  Add New institute ******************** */
if (isset($_GET['page']) and $_GET['page'] == "addNewInstitute") {

    // Error handaling
    if (empty($_POST["instituteName"])) {
        return _e("Please enter name.");
    } else if (empty($_POST["instituteUpazila"])) {
        return _e("Please select upazila.");
    }

    // Insert the institute into database
    $insertInstitute = easyInsert(
        "institute",
        array(
            "institute_eiin"    => empty($_POST["instituteEIIN"]) ? NULL : $_POST["instituteEIIN"],
            "institute_upazila"  => $_POST["instituteUpazila"],
            "institute_name"     => $_POST["instituteName"],
            "institute_type"     => $_POST["instituteType"],
            "institute_location" => $_POST["instituteAddress"],
            "institute_website"  => $_POST["instituteWebsite"]
        ),
        array(
            "institute_name"     => $_POST["instituteName"],
            " and institute_upazila"  => $_POST["instituteUpazila"]
        )
    );

    if ($insertInstitute === true) {
        _s("New institute successfully added.");
    } else {
        _e($insertInstitute);
    }
}


/*************************** Institute List ***********************/
if (isset($_GET['page']) and $_GET['page'] == "instituteList") {

    $requestData = $_REQUEST;
    $getData = [];

    // List of all columns name for sorting
    $columns = array(
        "",
        "institute_id",
        "institute_name",
        "institute_type",
        "institute_eiin",
        "location",
        "institute_website"
    );

    // Count Total recrods
    $totalFilteredRecords = $totalRecords = easySelectA(array(
        "table" => "institute",
        "fields" => "count(*) as totalRow",
        "where" => array(
            "is_trash = 0"
        )
    ))["data"][0]["totalRow"];

    if ($requestData['length'] == -1) {
        $requestData['length'] = $totalRecords;
    }

    if (!empty($requestData["search"]["value"]) or !empty($requestData["columns"][3]['search']['value'])) {  // get data with search


        $getData = easySelect(
            "institute as institute",
            "institute_id, institute_name, institute_type, institute_eiin, upazila_name, district_name, institute_website",
            array(
                "left join {$table_prefix}upazilas on institute_upazila = upazila_id",
                "left join {$table_prefix}districts on upazila_district_id = district_id"
            ),
            array(
                "institute.is_trash = 0 and (institute_name like '". safe_input($requestData['search']['value']) ."%' ",
                " or institute_eiin like" => $requestData['search']['value'] . "%",
                " or upazila_name like" => $requestData['search']['value'] . "%",
                " or district_name like" => $requestData['search']['value'] . "%",
                ")",
                " AND institute_type"  => $requestData["columns"][3]['search']['value']
            ),
            array(
                $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
            ),
            array(
                "start" => $requestData['start'],
                "length" => $requestData['length']
            )
        );

        $totalFilteredRecords = $getData ? $getData["count"] : 0;
    } else { // Get data withouth search

        $getData = easySelect(
            "institute as institute",
            "institute_id, institute_name, institute_type, institute_eiin, upazila_name, district_name, institute_website",
            array(
                "left join {$table_prefix}upazilas on institute_upazila = upazila_id",
                "left join {$table_prefix}districts on upazila_district_id = district_id"
            ),
            array("institute.is_trash = 0"),
            array(
                $columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']
            ),
            array(
                "start" => $requestData['start'],
                "length" => $requestData['length']
            )
        );
    }

    $allData = [];
    // Check if there have more then zero data
    if (isset($getData['count']) and $getData['count'] > 0) {

        foreach ($getData['data'] as $key => $value) {
            $allNestedData = [];
            $allNestedData[] = "";
            $allNestedData[] = $value['institute_id'];
            $allNestedData[] = $value['institute_name'];
            $allNestedData[] = $value['institute_type'];
            $allNestedData[] = $value['institute_eiin'];
            $allNestedData[] = "{$value['upazila_name']}, {$value['district_name']}";
            $allNestedData[] = "{$value['institute_website']}";
            // The action button
            $allNestedData[] = '<div class="btn-group">
                                  <button type="button" class="btn btn-xs btn-flat btn-primary dropdown-toggle" data-toggle="dropdown">
                                  action
                                  <span class="caret"></span>
                                  <span class="sr-only">Toggle Dropdown</span>
                                  </button>
                                  <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                  <li><a class="' . (current_user_can("institutes.Delete") ? "" : "restricted ") . 'deleteEntry" href="' . full_website_address() . '/xhr/?module=marketing&page=deleteInstitute" data-to-be-deleted="' . $value["institute_id"] . '"><i class="fa fa-minus-circle"></i> Delete</a></li>
                                  <li><a class="' . (current_user_can("institutes.Edit") ? "" : "restricted ") . '" data-toggle="modal" href="' . full_website_address() . '/xhr/?icheck=false&module=marketing&page=editInstitute&id=' . $value["institute_id"] . '"  data-target="#modalDefault"><i class="fa fa-edit"></i> Edit Institute</a></li>
                                  </ul>
                              </div>';

            $allData[] = $allNestedData;
        }
    }


    $jsonData = array(
        "draw"              => intval($requestData['draw']),
        "recordsTotal"      => intval($totalRecords),
        "recordsFiltered"   => intval($totalFilteredRecords),
        "data"              => $allData
    );

    // Encode in Json Formate
    echo json_encode($jsonData);
}


/***************** deleteInstitute ****************/
if (isset($_GET['page']) and $_GET['page'] == "deleteInstitute") {

    $deleteData = easyDelete(
        "institute",
        array(
            "institute_id" => $_POST["datatoDelete"]
        )
    );

    if ($deleteData === true) {

        echo '{
          "title": "' . __("Successfully deleted.") . '",
          "icon": "success"
      }';
    } else {

        echo '{
        "title": "Error: ' . $deleteData . '",
        "icon": "error"
    }';
    }
}


/************************** Edit Person **********************/
if (isset($_GET['page']) and $_GET['page'] == "editInstitute") {

    // Include the modal header
    modal_header("Edit Institute", full_website_address() . "/xhr/?module=marketing&page=updateInstitute");

    $institute = easySelectA(array(
        "table"   => "institute",
        "fields"  => "division_name, institute_type, district_name, upazila_name, institute_eiin, institute_name, institute_website, institute_location, institute_upazila, upazila_district_id, district_division_id",
        "join"  => array(
            "left join {$table_prefix}upazilas on institute_upazila = upazila_id",
            "left join {$table_prefix}districts on upazila_district_id = district_id",
            "left join {$table_prefix}divisions on district_division_id = division_id"
        ),
        "where" => array(
            "institute_id"  => $_GET["id"]
        )
    ))["data"][0];


?>
    <div class="box-body">

        <div class="form-group required">
            <label for="instituteName"><?= __("Institute Name:"); ?></label>
            <input type="text" name="instituteName" id="instituteName" value="<?= $institute["institute_name"]; ?>" placeholder="<?= __("Enter institute name"); ?>" class="form-control" required>
        </div>
        <div class="form-group required">
            <label for="instituteType"><?= __("Institute Type:"); ?></label>
            <select name="instituteType" id="instituteType" class="form-control" style="width: 100%;" required>
                <option value=""><?= __("Select type"); ?>...</option>
                <?php
                $instituteType = array('School', 'College', 'University', 'Coaching', 'Library', 'Store');
                foreach ($instituteType as $type) {
                    $selected = $institute["institute_type"] === $type ? "selected" : "";
                    echo "<option {$selected} value='$type'>$type</option>";
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="instituteEIIN"><?= __("EIIN:"); ?></label>
            <input type="number" name="instituteEIIN" id="instituteEIIN" value="<?= $institute["institute_eiin"]; ?>" placeholder="<?= __("Enter eiin"); ?>" class="form-control">
        </div>
        <div class="form-group required">
            <label for="instituteDivision"><?= __("Institute Division:"); ?></label>
            <select name="instituteDivision" id="instituteDivision" class="form-control" style="width: 100%;" required>
                <option value="<?= $institute["district_division_id"]; ?>"><?= $institute["division_name"]; ?></option>
            </select>
        </div>
        <div class="form-group required">
            <label for="instituteDistrict"><?= __("Institute District:"); ?></label>
            <select name="instituteDistrict" id="instituteDistrict" class="form-control" style="width: 100%;" required>
                <option value="<?= $institute["upazila_district_id"]; ?>"><?= $institute["district_name"]; ?></option>
            </select>
        </div>
        <div class="form-group required">
            <label for="instituteUpazila"><?= __("Institute Upazila:"); ?></label>
            <select name="instituteUpazila" id="instituteUpazila" class="form-control" style="width: 100%;" required>
                <option value="<?= $institute["institute_upazila"]; ?>"><?= $institute["upazila_name"]; ?></option>
            </select>
        </div>
        <div class="form-group">
            <label for="instituteAddress"><?= __("Address:"); ?></label>
            <textarea name="instituteAddress" id="instituteAddress" rows="3" class="form-control"> <?= $institute["institute_location"]; ?> </textarea>
        </div>
        <div class="form-group">
            <label for="instituteWebsite"><?= __("Website:"); ?></label>
            <input type="text" name="instituteWebsite" id="instituteWebsite" value="<?= $institute["institute_website"]; ?>" class="form-control">
        </div>
        <input type="hidden" name="instituteId" value="<?php echo safe_entities($_GET["id"]); ?>">

    </div>
    <!-- /Box body-->

    <script>
        /* If division and district change then clear the lower fields */
        $(document).on("change", "#instituteDivision, #instituteDistrict", function() {

            if ($("#instituteDistrict").val() !== "" && $(this)[0]['name'] !== "instituteDistrict") {
                $("#instituteDistrict").val(null).trigger("change");
            }

            if ($("#instituteUpazila").val() !== "") {
                $("#instituteUpazila").val(null).trigger("change");
            }

        });

        $(document).on('mouseenter focus', '#instituteDivision, #instituteDistrict, #instituteUpazila', function() {

            var select2AjaxUrl = '<?php echo full_website_address() ?>/info/?module=select2&page=';

            /* Initialize Select Ajax Elements */
            $(this).select2({
                placeholder: $(this).children('option:first').html(),
                /* Get the first option as placeholder */
                ajax: {
                    url: function() {
                        if ($(this)[0]['name'] === "instituteDivision") {

                            return select2AjaxUrl + "divisionList";

                        } else if ($(this)[0]['name'] === "instituteDistrict") {

                            if ($("#instituteDivision").val() === "") {

                                $(this).select2("close");
                                return alert("<?= __("Please select the division"); ?>");
                            }
                            /* Generate the url */
                            return select2AjaxUrl + "districtList&division_id=" + $("#instituteDivision").val();

                        } else if ($(this)[0]['name'] === "instituteUpazila") {

                            if ($("#instituteDistrict").val() === "") {
                                $(this).select2("close");
                                return alert("<?= __("Please select the district"); ?>");
                            }
                            /* Generate the url */
                            return select2AjaxUrl + "upazilaList&district_id=" + $("#instituteDistrict").val();

                        }
                    },
                    dataType: "json",
                    delay: 250,
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    },
                    cache: true
                }
            });

        });
    </script>

<?php

    // Include the modal footer
    modal_footer();
}

//*******************************  Add New institute ******************** */
if (isset($_GET['page']) and $_GET['page'] == "updateInstitute") {

    // Error handaling
    if (empty($_POST["instituteName"])) {
        return _e("Please enter name.");
    } else if (empty($_POST["instituteUpazila"])) {
        return _e("Please select upazila.");
    }

    // Update the institute into database
    $updateInstitute = easyUpdate(
        "institute",
        array(
            "institute_eiin"    => empty($_POST["instituteEIIN"]) ? NULL : $_POST["instituteEIIN"],
            "institute_upazila"  => $_POST["instituteUpazila"],
            "institute_name"     => $_POST["instituteName"],
            "institute_type"     => $_POST["instituteType"],
            "institute_location" => $_POST["instituteAddress"],
            "institute_website"  => $_POST["instituteWebsite"]
        ),
        array(
            "institute_id"  => $_POST["instituteId"]
        )
    );

    if ($updateInstitute === true) {
        _s("Institute successfully updated.");
    } else {
        _e($updateInstitute);
    }
}

?>