<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

/*
This script creates a page titled "View Vehicles". It will have a
paragraph and a table of all the vehicles or equipment owned by the
company. The information in each row will include the following
fields (which are a subset of the actual vehicles database table
fields): id, tag, state, make, knownAs
*/

require_once('includes/login_funcs.php');
require_once('includes/db_vars.php');
include_once('includes/header_footer.php');
require_once('includes/table_funcs.php');

/*
User must be logged in and be someone special.
*/
if (!user_isloggedin() || !user_type_cookieIsSet()) {
  // Redirect to login page.
  $host = $_SERVER['HTTP_HOST'];
  header("Location: http://{$host}/web/login.php");
  exit;
}
if ($_COOKIE['user_type'] != 1) {
  die('Script aborted #3098. -Programmer.');
}


/*
Name of database table we are using as our source.
*/
$tableName = "vehicles";


/*
Ceate an array (numerically indexed) containing the names
of the database table fields which we want to display in
our HTML table.
*/
$fieldNames = array('id', 'tag', 'state', 'make', 'knownAs');


/*
Create an SQL WHERE clause.
*/
$whereClause = '';


/*
Create a two dimensional array called $vehicle which will
hold all the vehicle data to be displayed in the table.
See the header of getValuesForHTML_Table() for a description
of the two dimensional array $vehicle.
*/
$vehicle = getValuesForHTML_Table($fieldNames, $tableName, $whereClause);


if ($vehicle == FALSE) {
  die('Error: no records found. -Programmer.');
}


$cap = 'Vehicles (and Equipment)';

$nOfCols = 5;   // number of columns to be displayed


/*
Create an array (numerically indexed) containing the column
header strings.
*/
$tblHeader = array('vehicle ID', 'tag', 'State', 'make', 'known as');


/*
Create an HTML string for the table.
*/
$htmlTable_1 = makeTable($cap, $nOfCols, $tblHeader, $vehicle);


/*
Construct page:
*/
site_header('View Vehicles');
$page_str = <<<EOPAGESTR

<p>This page is intended to give you basic information about
the vehicles (or equipment) for our company. There are more
data fields per vehicle than shown here. Also, as of the time
of this writing the table shown can only present a maximum of three
hundred (300) records. If this becomes unacceptable one day
have a programmer make modifications.</p>

$htmlTable_1

EOPAGESTR;
echo $page_str;
site_footer();

?>