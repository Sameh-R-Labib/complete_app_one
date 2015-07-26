<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require_once('../includes/login_funcs.php');
require_once('../includes/db_vars.php');
require_once('../includes/header_footer.php');

// User must be logged in and be someone special.
if (!user_isloggedin() || !user_type_cookieIsSet()) {
  $host = $_SERVER['HTTP_HOST'];
  header("Location: http://{$host}/web/login.php");
  exit;
}
if ($_COOKIE['user_type'] != 1) {
  die('Script aborted #3098. -Programmer.');
}


site_header('Update a Record');

$page_str = <<<EOPAGESTR

<p>The sample code updates an existing record which matches
particular field values.</p>

<pre>
// Send data to db
&#36;query = "UPDATE vehicles
          SET vin = '&#36;vin',
              title = '&#36;title',
              cost = '&#36;cost',
              year = '&#36;year',
              make = '&#36;make',
              model = '&#36;model',
              dateOfPurchase = STR_TO_DATE('&#36;dateOfPurchase','%m/%d/%Y'),
              bodyNumber = '&#36;bodyNumber',
              transmissionSN = '&#36;transmissionSN',
              engineSN = '&#36;engineSN',
              knownAs = '&#36;knownAs',
              dateOfRemoval = STR_TO_DATE('&#36;dateOfRemoval','%m/%d/%Y'),
              isInFleet = '&#36;isInFleet'
          WHERE tag = '&#36;tag' AND state = '&#36;state'";
&#36;result = mysql_query(&#36;query);
if (!&#36;result || mysql_affected_rows() < 1) {
  &#36;status_message = 'Problem with user data entry';
} else {
  &#36;status_message = 'Successfully edited user data';
}
</pre>

EOPAGESTR;
echo $page_str;

site_footer();
?>