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


site_header('SQL Date to String Date');

$page_str = <<<EOPAGESTR

<p>One SQL function which can be used is:</p>

<pre>
DATE_FORMAT
</pre>

<p>Code sample below uses STR_TO_DATE:</p>

<pre>
&#36;query = "SELECT vin, title, cost, year, make, model, DATE_FORMAT(dateOfPurchase, '%m/%d/%Y'),
bodyNumber, transmissionSN, engineSN, knownAs, DATE_FORMAT(dateOfRemoval, '%m/%d/%Y'), isInFleet
            FROM vehicles
            WHERE tag = '&#36;tag' AND state = '&#36;state'";
&#36;result = mysql_query(&#36;query);
if (!&#36;result || mysql_num_rows(&#36;result) < 1) {
  die('Error 56801. -Programmer.');
} else {
  &#36;user_array = mysql_fetch_array(&#36;result);
  
  &#36;vin = &#36;user_array['vin'];
  &#36;title = &#36;user_array['title'];
  &#36;cost = &#36;user_array['cost'];
  &#36;year = &#36;user_array['year'];
  &#36;make = &#36;user_array['make'];
  &#36;model = &#36;user_array['model'];
  &#36;dateOfPurchase = &#36;user_array['DATE_FORMAT(dateOfPurchase, \'%m/%d/%Y\')'];
  &#36;bodyNumber = &#36;user_array['bodyNumber'];
  &#36;transmissionSN = &#36;user_array['transmissionSN'];
  &#36;engineSN = &#36;user_array['engineSN'];
  &#36;knownAs = &#36;user_array['knownAs'];
  &#36;dateOfRemoval = &#36;user_array['DATE_FORMAT(dateOfRemoval, \'%m/%d/%Y\')'];
  &#36;isInFleet = &#36;user_array['isInFleet'];
</pre>

EOPAGESTR;
echo $page_str;

site_footer();
?>