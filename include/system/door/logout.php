<?php

if(session_destroy() == true) {

  // unset all cookies
  if (isset($_SERVER['HTTP_COOKIE'])) {
    $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
    foreach($cookies as $cookie) {
        $parts = explode('=', $cookie);
        $name = trim($parts[0]);
        setcookie($name, '', time()-1000);
        setcookie($name, '', time()-1000, '/');
    }
  }
  
  $rdr_to = full_website_address()."/login/";
  header("location: {$rdr_to}");

}

	
?>
