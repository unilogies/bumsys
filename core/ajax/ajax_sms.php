<?php

/************************** Send SMS Box **********************/
if(isset($_GET['page']) and $_GET['page'] == "sendSMS") {

    // Include the modal header
    modal_header("Send SMS to " . $_GET["name"], full_website_address() . "/info/?module=sms&page=sendSMSCommand");
    
    ?>

            <div class="row">

              <div class="col-md-12">

                <?php 
                  if( empty($_GET['number']) ) {
                    echo "<div class='alert alert-danger'>Sorry! No number detected.</div>";
                  }
                ?>

                <div class="form-group">
                  <label for="message">Message:</label>
                  <textarea name="message" id="message" cols="30" rows="3" class="form-control">
                  Dear Sir, Assalamu Alaikum. Please collect your salary from the account section.
- <?php echo get_options("companyName"); ?>
                  </textarea>
                </div>
              </div>
         
            </div> <!-- row -->

            <input type="hidden" name="numbers" value="<?php echo $_GET['number']; ?>">

    <?php

    // Include the modal footer
    modal_footer("Send");

    return;

}

/************************** Send Bulk SMS Box **********************/
if(isset($_GET['page']) and $_GET['page'] == "sendBulkSMS") {

  // Include the modal header
  modal_header("Send Bulk SMS", full_website_address() . "/info/?module=sms&page=sendSMSCommand");

  $msg = "Dear Sir, Assalamu Alaikum. Please collect your salary from the account section.\n- ".get_options("companyName");

  if( isset($_GET["type"]) and $_GET["type"] == "greetings" ) {
    $msg = "Dear Sir, Assalamu Alaikum. A warming greetings from ". get_options("companyName") .". Thank You. ";
  }
  
  ?>

          <div class="row">

            <div class="col-md-12">

            <div class="form-group">
                <label for="numbers">Numbers:</label>
                <textarea name="numbers" id="numbers" cols="30" rows="3" class="form-control">
<?php echo $_GET['numbers']; ?>
                </textarea>
              </div>

              <div class="form-group">
                <label for="message">Message:</label>
                <textarea name="message" id="message" cols="30" rows="3" class="form-control">
<?php echo $msg; ?>
                </textarea>
              </div>


            </div>
       
          </div> <!-- row -->


  <?php

  // Include the modal footer
  modal_footer("Send");

  return;

}

//*******************************   sendSMSCommand ******************** */
if(isset($_GET['page']) and $_GET['page'] == "sendSMSCommand") {

      if(send_sms(
        str_replace(";", ",", $_POST["numbers"]),
        $_POST["message"]
      )) {

        echo "<div class='alert alert-success'>SMS Sent Successfully.</div>";

      }

}




?>