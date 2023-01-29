<?php


if(is_login() === true) {

    $rdr_to = function() {
            
        $userHomepage = easySelectA(array(
            "table"     => "users",
            "fields"    => "user_homepage",
            "where"     => array(
                "user_id"   => $_SESSION["uid"]
            )
        ))["data"][0]["user_homepage"];


        // If user homepage is set
        if( !empty($userHomepage) ) {

            return html_entity_decode($userHomepage);

        } else {

            return full_website_address()."/home/";
        }

    };

    header("location: " . $rdr_to());
    exit();
    
}


$errorMsgOnLogin = "";

if(isset($_POST["user-email"]) or isset($_POST["user-password"])) {

    // Select the user
    $selectUser = easySelectA(array(
        "table"   => "users as user",
        "fields"  => "user_id, user_emp_id, user_pass, user_language, user_email, user_name, user_status, user_homepage, user_locked_reason, biller_shop_id, biller_accounts_id, biller_warehouse_id",
        "join"    => array(
            "left join {$table_prefeix}billers on biller_user_id = user_id"
        ),
        "where"   => array(
            "user.is_trash = 0 and ( user_email" => $_POST["user-email"],
            " OR user_name" => $_POST["user-email"],
        ")"
        )
    ));

    // Validating the user and password
    if(empty($_POST["user-email"]) or empty($_POST["user-password"])) {

        $errorMsgOnLogin = "<div class='alert alert-danger'>". __("Please enter your email and password to login") ."</div>";

    } else if( isset($selectUser["data"][0]["user_status"]) and $selectUser["data"][0]["user_status"] === "Lock" ) {
        
        $errorMsgOnLogin = "<div class='alert alert-danger'>". __("This account has been locked due to following reasons: %s. Please contact with administrator.", __($selectUser["data"][0]["user_locked_reason"])) ."</div>";

    } else if( isset($selectUser["data"][0]["user_status"]) and $selectUser["data"][0]["user_status"] === "Ban" ) {
        
        $errorMsgOnLogin = "<div class='alert alert-danger'>". __("This account has been banned. Please contact with administrator.") ."</div>";

    } else if(!$selectUser or !password_verify($_POST["user-password"], $selectUser["data"][0]["user_pass"])) {

        $errorMsgOnLogin = "<div class='alert alert-danger'>". __("Invalid email or password.") ."</div>";

        $get_user_ip = safe_input(get_ipaddr());

        // Insert Invalid Login Attempt
        easyInsert(
            "login_attempts",
            array(
                "attempt_ipaddr"    => $get_user_ip,
                "attempt_user_id"   => $selectUser !== false ? $selectUser["data"][0]["user_id"] : NULL
            )
        );

        // on invalied login attemt insert the recrods
        // Check if the user exists
        if($selectUser) {

            // Select invalied login attempt for last five minutes for Users
            $failedAttemptInLastFiveMinuteForUser = easySelectD("
                SELECT 
                    COUNT(*) AS totalAttempt 
                FROM {$table_prefeix}login_attempts
                WHERE attempt_user_id = '{$selectUser["data"][0]["user_id"]}' and attempt_time >= NOW() - INTERVAL 5 MINUTE
            ")["data"][0]["totalAttempt"];

            // if there are more then zero and defined attempt in last five minutes then block the user
            if( get_options("maxInvalidLoginAttemptToBlockUser") > 0 and $failedAttemptInLastFiveMinuteForUser > get_options("maxInvalidLoginAttemptToBlockUser") ) {

                easyUpdate(
                    "users",
                    array(
                        "user_status"           => "Lock",
                        "user_locked_reason"    => "Too many invalied login attempts"
                    ),
                    array(
                        "user_id"   => $selectUser["data"][0]["user_id"]
                    )
                );

            }

        }


         // Select invalied login attempt for last five minutes for host/IP
         $failedAttemptInLastFiveMinuteForHost = easySelectD("
            SELECT 
                COUNT(*) AS totalAttempt 
            FROM {$table_prefeix}login_attempts
            WHERE attempt_ipaddr = '{$get_user_ip}' and attempt_time >= NOW() - INTERVAL 5 MINUTE
        ")["data"][0]["totalAttempt"];

        // if there are more then zero and defined attempt in last five minutes then block the Host/Ip
        if( get_options("maxInvalidLoginAttemptToBlockHost") > 0 and $failedAttemptInLastFiveMinuteForHost > get_options("maxInvalidLoginAttemptToBlockHost") ) {

            easyInsert(
                "firewall",
                array(
                    "fw_status"     => 'Active',
                    "fw_ip_address" => $get_user_ip,
                    "fw_action"     => 'Blocked',
                    "fw_comment"    => 'Too many invalied login attempts'
                ),
                array( // No duplicate allow.
                    "fw_ip_address" => $get_user_ip
                )
            );

        }


    } else {

        $users = $selectUser["data"][0];

        // Now set the employee id cookie. The cookie will be httponly
        // The coockie destroy within 30 days. 
        setcookie("eid", $users["user_emp_id"], strtotime( '+30 days'), "/", "", "", true); // eid = employee id

        // Set language cookie
        setcookie("lang", empty($users["user_language"]) ? "" : $users["user_language"], 0, "/");

        // set currencies options local storage
        set_local_storage("currencySymbol", get_options("currencySymbol"));
        set_local_storage("currencySymbolPosition", get_options("currencySymbolPosition"));
        set_local_storage("decimalSeparator", get_options("decimalSeparator"));
        set_local_storage("decimalPlaces", get_options("decimalPlaces"));

        // set currency cookie
        setcookie("currencySymbol", get_options("currencySymbol"), 0, "/");
        setcookie("currencySymbol", get_options("currencySymbol"), 0, "/");

        // If keepAlive is set then add a keepAlive coockie. 
        // This coockie can be retrived by javascript and destroy after browser is closed
        if(isset($_POST["keepAlive"])) {
            setcookie("keepAlive", true, -1, "/");
        } else {
            setcookie("keepAlive", false, -1, "/");
        }

    
        // Set the Network Changes Session
        if(isset($_POST["keepAliveOnNetworkChanges"])) {

            $_SESSION["keepAliveOnNetworkChanges"] = 1;
            // Generate the session access key by sha1 algorithm. 
            $session_access_key = sha1($users["user_email"].$_SERVER["HTTP_USER_AGENT"]);

        } else {
        
            // Generate the session access key if disable network changes by sha1 algorithm. 
            $session_access_key = sha1($users["user_email"].$_SERVER["HTTP_USER_AGENT"].$_SERVER["REMOTE_ADDR"]);

        }


        session_regenerate_id();

        // Store the $session_access_key into the database
        easyUpdate(
            "users",
            array(
                "user_pass_aaccesskey" => $session_access_key
            ),
            array(
                "user_id" => $users["user_id"]
            )
        );

        // Set the session
        $_SESSION["sak"] = $session_access_key;
        $_SESSION["uid"] = $users["user_id"];
        if($users["biller_shop_id"] > 0) {
            $_SESSION["sid"] = $users["biller_shop_id"];
            $_SESSION["aid"] = $users["biller_accounts_id"];
            $_SESSION["wid"] = $users["biller_warehouse_id"];
        }
    
        // Last activity
        $_SESSION["LAST_ACTIVITY"] = time();

        // Generate csrf access token
        $_SESSION["csrf_token"] = sha1($_SESSION["sak"].$_SESSION["uid"].mt_rand());

        // Add Login information
        add_login_info($users["user_id"]);

        // Disable full group by
        $conn->query("SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
        // $conn->query("SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'STRICT_TRANS_TABLES',''));");
        
        // Enanle full strict mode and full group by mode
        //$conn->query("SET sql_mode=(SELECT CONCAT(@@sql_mode, ',STRICT_TRANS_TABLES'));");
        //$conn->query("SET sql_mode=(SELECT CONCAT(@@sql_mode, ',ONLY_FULL_GROUP_BY'));");
        
        // after setting cooke and session redirect the the dashboard. 
        
        $rdr_to = function() {
            
            global $users;
            // If http refererer is set
            if( !empty($users["user_homepage"]) ) {

                return html_entity_decode($users["user_homepage"]);

            } elseif(isset($_SERVER["HTTP_REFERER"]) and rtrim($_SERVER["HTTP_REFERER"], "/") !== full_website_address()) {
                
                return $_SERVER["HTTP_REFERER"];

            } else {
                return full_website_address()."/home/";
            }

        };

        // Javascript redirect is required for set local storage 
        redirect($rdr_to());
        
        //header("location: " . $rdr_to());

    }

}


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo get_options("companyName"); ?></title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  
  <!-- Include all CSS -->
  <link rel="stylesheet" href="<?php echo full_website_address(); ?>/css/">
  
  <!-- Include all Header JS -->
  <script src="<?php echo full_website_address(); ?>/js/?q=head"></script>

</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <a href="#"><b>BUM</b>Sys</a>
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
    <p class="login-box-msg">Enter your email and password to login</p>
    
    <form action="<?php echo full_website_address(); ?>/login/" method="post">
      <div class="form-group has-feedback">
        <input id="userEmail" type="text" name="user-email" class="form-control" value="<?php echo isset($_POST["user-email"]) ? $_POST["user-email"] : ""; ?>" placeholder="Username or Email" required>
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input id="userPassword" type="password" name="user-password" class="form-control" placeholder="Password" required>
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <div class="row">
        <div class="col-xs-8">
          <div class="checkbox icheck">
            <p style="font-weight: bold;">Do not signout on</p>
            <label>
              <input name="keepAlive" type="checkbox" checked> Inactivity
            </label>
            <label>
              <input name="keepAliveOnNetworkChanges" type="checkbox"> Network Changes
            </label>
          </div>
        </div>
        <!-- /.col -->
        <div class="col-xs-4">
          <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
        </div>
        <!-- /.col -->
      </div>
    </form>

    <a id="forgots" href="#forgot">I forgot my password</a><br>
    <br/>
    
    <!-- Show the error message while login --> 
    <?php echo $errorMsgOnLogin; ?>

    <div style="display: none" class="forgotPass">
      <p>Please contact with administrator to retrieve your account</p>
    </div>

  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->

  <!-- Include all footer JS -->
  <script src="<?php echo full_website_address(); ?>/js/?q=foot"></script>
<script>
    
  $(function () {

    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%' /* optional */
    });

    $("#userEmail").select();

  });

  $(document).on("click", "#forgots", function() {
    $(".forgotPass").show();
  });

  $(document).on("keydown", "#userEmail", function(event) {

    /** If press enter on input email then select password fields */
    if( event.key === "Enter" ) {
        
        event.preventDefault();
        $("#userPassword").select();

    }

  });

  
</script>
</body>
</html>
