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


site_header('Select Box Seeded from DB');

$page_str = <<<EOPAGESTR

<p>This function takes an enumerated array of enumerated arrays as represented by
the vehicle_in parameter. It produces a select input form field which displays
two record fields but returns one field value.</p>

<pre>
function selectBox(&#36;vehicle_in) {
/*
This function takes a two dimensional array of vehicle information and
returns a selection box for a form. Each selection will return a vehicle
id value. The corresponding thing which the user will click on will be a
string containing both the id and the knownAs. The structure and content
of &#36;vehicle_in array is described in function selectVehicle() above.

If &#36;vehicle_in array is empty or unavailable the script will die.
*/

  if (!isset(&#36;vehicle_in) OR !is_array(&#36;vehicle_in) OR sizeof(&#36;vehicle_in) &lt; 1) {
    die("Function failed to create select box because no array was passed.");
  }


  /*
  Here is a sample code for a select box on my site.

  &lt;div&gt;
    &lt;label for="user_type"&gt;Which best describes you?&lt;/label&gt;
    &lt;select name="user_type" id="user_type"&gt;
      &lt;option value ="1"&gt;Owner Administarator of this Website&lt;/option&gt;
      &lt;option value ="2"&gt;Driver or Assistant for MD HC Public Schools&lt;/option&gt;
      &lt;option value ="3"&gt;Driver for SAMEH R LABIB, LLC&lt;/option&gt;
    &lt;/select&gt;
  &lt;/div&gt;
  */

  &#36;selectB_str = "&#92;n&lt;div&gt;&#92;n  &lt;label for=\"vehicleId\" class=\"fixedwidth\"&gt;Which one?&lt;/label&gt;&#92;n" .
      "  &lt;select name=\"vehicleId\" id=\"vehicleId\"&gt;&#92;n";

  /*
  Here is the loop that builds the main body of the select box.
  */
  unset(&#36;temp_1);
  unset(&#36;temp_2);
  reset(&#36;vehicle_in);
  while (&#36;array_cell = each(&#36;vehicle_in))
  {
    &#36;temp_1 = &#36;array_cell['value'][0];
    &#36;temp_2 = &#36;array_cell['value'][1];
    &#36;selectB_str .=
    "    &lt;option value=\"&#36;temp_1\"&gt;&#36;temp_1 &#36;temp_2&lt;/option&gt;&#92;n";
  }

  &#36;selectB_str .= "  &lt;/select&gt;&#92;n&lt;/div&gt;&#92;n&#92;n";
  return &#36;selectB_str;
}
</pre>

EOPAGESTR;
echo $page_str;

site_footer();
?>