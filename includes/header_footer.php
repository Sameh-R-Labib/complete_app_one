<?php
// NAME: header_footer.php
//
// FROM: Keep in /includes. Authored by Sameh Labib.
//
// PURPOSE: This script will be included within .html files or .php scripts that generate
// pages for my site. It will define two functions: site_header() --which takes a string argument
// corresponding to the title of the page--, and site_footer().
//
// NOTE: The script which uses these functions must be in the same directory as the
// javascript and CSS files referred to in the functions. Also, the script which uses
// these functions must be in a directory at the same level as /acct_webroot/web since the
// location of images is specified relatively.


require_once('login_funcs.php');




function site_header($title) {
/*
Generates all the HTML before the main content of the page.
*/

  // Variable definitions for links
  $host = $_SERVER['HTTP_HOST'];
  $beginURL = "http://{$host}/web/";
  $navigate_str = "<li><a href=\"{$beginURL}kds_navigate.php\">Navigate</a></li>";
  $homepage_str = "<li><a href=\"{$beginURL}index.php\">Home</a></li>";
  $signup_str = "<li><a href=\"{$beginURL}register.php\">Sign Up</a></li>";
  $signin_str = "<li><a href=\"{$beginURL}login.php\">Sign In/Out</a></li>";
  $forgot_str = "<li><a href=\"{$beginURL}forgot.php\">Forgot Password</a></li>";
  $changeemail_str = "<li><a href=\"{$beginURL}changeemail.php\">Change Email</a></li>";
  $changepass_str = "<li><a href=\"{$beginURL}changepass.php\">Change Password</a></li>";
  $editUI_str = "<li><a href=\"{$beginURL}edit_userinfo.php\">Edit User Information</a></li>";
  $impersonate_str = "<li><a href=\"{$beginURL}impersonate.php\">Impersonate</a></li>";
  $staticweb_str = "<li><a href=\"http://www.gxsam11.net/braing817.htm\">Static Pages</a></li>";
  $selectYourUserType_str = "<li><a href=\"{$beginURL}selectYourUserType.php\">Select Your user_type</a></li>";
  $createANewUserType_str = "<li><a href=\"{$beginURL}createANewUserType.php\">Create A New user_type</a></li>";

  // Set the Navigation Bar links based on user_type:
  if ( user_type_cookieIsSet() AND  $_COOKIE['user_type'] != 5) {

    $user_type = $_COOKIE['user_type'];

    if ( $user_type == 1 ) {
      // user_type is Owner Administarator of this Website
      $navbar_str = <<<EOADMINNAVSTR
$signin_str
$selectYourUserType_str
$homepage_str
<li><a href="#">KDS</a>
    <ul>
    <li><a href="{$beginURL}editKA.php">Edit Knowledge Article</a></li>
    <li><a href="{$beginURL}addPlusSecureKda.php">Create Plus Secure a KDA</a></li>
    <li><a href="{$beginURL}createA_kda.php">Create A KDA</a></li>
    <li><a href="{$beginURL}usedScriptSESSION_Prepends.php">Used Script SESSION Prepends</a></li>
    <li><a href="{$beginURL}secureA_kds_associateRec.php">Secure A kds_associate Record</a></li>
    <li><a href="{$beginURL}removeKdaNode.php">Remove KDA Node</a></li>
    </ul>
</li>
<li><a href="#">Maintenance</a>
    <ul>
    <li><a href="{$beginURL}shopJobList.php">Shop Job List</a></li>
    <li><a href="{$beginURL}setTimeDesired.php">Set timeDesired</a></li>
    <li><a href="{$beginURL}viewServHistForVeh.php">View Service History for Vehicle</a></li>
    <li><a href="{$beginURL}processAnInvoice.php">Process An Invoice</a></li>
    <li><a href="{$beginURL}updateMaintItem.php">Update Maintenance Item</a></li>
    <li><a href="{$beginURL}addShop.php">Add Shop</a></li>
    <li><a href="{$beginURL}addMaintenanceItem.php">Add Maintenance Item</a></li>
    <li><a href="{$beginURL}addPart.php">Add Part</a></li>
    <li><a href="{$beginURL}assocPIDwithMID.php">Assoc. Part w/ MaintenItem</a></li>
    <li><a href="{$beginURL}viewMItems4Vehicle.php">View Maintenance Items for Vehicle</a></li>
    <li><a href="{$beginURL}viewParts.php">View Parts</a></li>
    <li><a href="{$beginURL}viewShops.php">View Shops</a></li>
    </ul>
</li>
<li><a href="#">Bid</a>
    <ul>
    <li><a href="{$beginURL}adjustedBidHist.php">Adjusted Bid History</a></li>
    </ul>
</li>
$staticweb_str
$navigate_str
EOADMINNAVSTR;
    } elseif ( $user_type == 2 ) {
      // user_type is Driver or Assistant for MD HC Public Schools
      $navbar_str = <<<EOHCDRIVERNAVSTR
$signin_str
$selectYourUserType_str
$navigate_str
$homepage_str
EOHCDRIVERNAVSTR;
    } elseif ( $user_type == 3 ) {
      // user_type is Driver for SAMEH R LABIB, LLC
      $navbar_str = <<<EOSRLDRIVERNAVSTR
$signin_str
$selectYourUserType_str
$navigate_str
$homepage_str
EOSRLDRIVERNAVSTR;
    } elseif ( $user_type == 4 ) {
      // user_type is School Bus Contractor for MD HC Public Schools
      $navbar_str = <<<EOHCSBCOTNAVSTR
$signin_str
$selectYourUserType_str
$navigate_str
$homepage_str
EOHCSBCOTNAVSTR;
    }
  
    unset($user_type);
  
  } else {
    // User has no user_type cookie
    $navbar_str = <<<EONOUTCOOKIE
$signin_str
$navigate_str
$homepage_str
$signup_str
$forgot_str
EONOUTCOOKIE;
  }


  $site_header = <<<EOSITEHEADER
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<meta http-equiv="pragma" CONTENT="no-cache">
<link rel="icon" href="/favicon.gif" type="image/gif">
<title>$title</title>
<link href="{$beginURL}style2.css" rel="stylesheet" type="text/css"/>
<link href="{$beginURL}cssverticalmenu.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="{$beginURL}cssverticalmenu.js">
/***********************************************
* CSS Vertical List Menu- by JavaScript Kit (www.javascriptkit.com)
* Menu interface credits: http://www.dynamicdrive.com/style/csslibrary/item/glossy-vertical-menu/ 
* This notice must stay intact for usage
* Visit JavaScript Kit at http://www.javascriptkit.com/ for this script and 100s more
***********************************************/
</script>
<!-- <script type="text/javascript" src="{$beginURL}jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="{$beginURL}script.js"></script> -->
</head>

<body>



<div id="header">
<div id="sitebranding">
<h1>School Bus Transportation Company</h1>
</div>
</div> <!-- end of header div -->


<div id="navleft">
<ul id="verticalmenu" class="glossymenu">
<!-- left navigation links go here -->
$navbar_str
</ul>
</div> <!-- end of navleft div -->


<div id="bodycontent">
<!-- title goes here -->
<h1 class="pagetitle">$title</h1>
<!-- end title -->
<!-- content goes here -->
EOSITEHEADER;
  echo $site_header;
  unset($site_header);
  unset($navbar_str);
}




function site_footer() {
/*
Generates all the HTML after the main content of the page.
*/
  $site_footer = <<<EOSITEFOOTER

<div id="footer">
<!-- footer goes here -->
<p class="footcopy">Copyright &copy; 2011 SAMEH R LABIB, LLC</p>
</div> <!-- end of footer div -->

<!-- end content -->
</div> <!-- end of bodycontent div -->



<div id="navright">

<!-- bus img goes here -->
<div>
<img src="school-bus-clipart11.gif" class="navrtimg" alt="School Bus gif" />
</div>

</div> <!-- end of navright div -->


</body>

</html>
EOSITEFOOTER;
  echo $site_footer;
  unset($site_footer);
}

?>