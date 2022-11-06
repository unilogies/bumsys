<?php

/************************** New Call **********************/
if(isset($_GET['page']) and $_GET['page'] == "callToPerson") {

  $person = easySelectA(array(
    "table"   => "persons",
    "where"   => array(
      "person_id" => $_GET["id"]
    )
  ))["data"][0];

  // Include the modal header
  modal_header("Call is being transfer to " . $person['person_full_name'], full_website_address() . "/info/?module=call&page=addNewPerson");
  
  ?>
    <div class="box-body">

      <div class="form-group required">
        <label for="isCallReceived">Is phone call receieed?</label>
        <select name="isCallReceived" id="isCallReceived" class="form-control" required>
          <option value="1">Yes</option>
          <option value="0">No</option>
        </select>
      </div>

      <div class="form-group required">
        <label for="personFeedback">Feeback:</label>
        <textarea name="personFeedback" id="personFeedback" cols="30" rows="3" class="form-control" placeholder="Please enter feedback here"></textarea>
      </div>
      <input type="hidden" name="personId" value="<?php echo safe_entities($_GET["id"]); ?>">
      
    </div>
    <!-- /Box body-->

    <script>

      window.location.href = 'sip:<?php echo str_replace("+88","", $person["person_phone"]); ?>';
    
    </script>

  <?php

  // Include the modal footer
  modal_footer();

}


?>