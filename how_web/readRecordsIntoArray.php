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


site_header('Read Records Into Array');

$page_str = <<<EOPAGESTR

<p>Example:</p>

<pre>
function getValuesForHTML_Table(&#36;fieldNames_in, &#36;tableName_in,
                                &#36;whereClause_in) {
/*
This function gets passed a list of database table field names
&#36;fieldNames_in and a database table name &#36;tableName_in as parameters.
Also, it gets passed an SQL WHERE clause.

Return: an array of arrays. Each top level array element
(numerically indexed) will corresponds to a single record and is an
array (numerically indexed) of the desired field values for that
record. The records which will become part of this array may be limited
by a WHERE clause. This array is intended to hold values from a database
table for further processing by the script which called this function.
This function will return FALSE if there where no records to be
retrieved from the database. However, the script will die if the SQL
query fails.

&#36;fieldNames_in is an array which is numerically indexed
starting with zero. The value of each element will be a
name specifying a field of the database table &#36;tableName_in.

&#36;whereClause_in is a string which contains an SQL WHERE clause
*/

  /*
  Is there at least one field name?
  */
  if (!isset(&#36;fieldNames_in) OR !is_array(&#36;fieldNames_in)
                          OR sizeof(&#36;fieldNames_in) < 1) {
    die('Error 589760. -Programmer.');
  }


  /*
  Make a query string which retrieves the fields specified in
  &#36;fieldNames_in and all the records in the table specified by
  &#36;tableName_in.
  */
  
  /*
  Compose the &#36;fieldNameList string.
  */
  &#36;fieldNameList = reset(&#36;fieldNames_in);
  next(&#36;fieldNames_in);
  while (&#36;array_cell = each(&#36;fieldNames_in)) {
    &#36;fieldNameList .= ", " . &#36;array_cell['value'];
  }

  &#36;query = "SELECT &#36;fieldNameList
            FROM &#36;tableName_in
            &#36;whereClause_in
            LIMIT 0, 300";

  &#36;result = mysql_query(&#36;query);
  if (!&#36;result) {
    die('The db query failed in include file table function. Error 88800881. -Programmer.');
  } elseif (mysql_num_rows(&#36;result) < 1) {
    return FALSE;
  }


  /*
  A loop that reads in the values from the query result into the
  two dimensional array which will be returned.
  */
  &#36;i = 0;
  while (&#36;row = mysql_fetch_row(&#36;result)) {
    &#36;values[&#36;i] = &#36;row;
    &#36;i += 1;
  }

  if (&#36;i == 0) {
    die('Error 41554182. -Programmer. ');
  }


  return &#36;values;
}
</pre>

<p>Better Example:</p>

<pre>
function getAllMatches(&#36;composite_IN) {
/*
Get all kds_associate records which have a composite field value of
&#36;composite. Assign them to &#36;allMatches.
*/
  global &#36;allMatches;
  if (!get_magic_quotes_gpc()) {
    &#36;composite_IN = addslashes(&#36;composite_IN);
  }
  &#36;query = "SELECT id, nextPhrase, isComplete
            FROM kds_associate
            WHERE composite='&#36;composite_IN'";
  &#36;result = mysql_query(&#36;query);
  if (!&#36;result) {
    die('Query failed. Err: 68398994. -Programmer.');
  }
  &#36;allMatches = array();
  while (&#36;row = mysql_fetch_array(&#36;result, MYSQL_ASSOC)) {
    &#36;allMatches[] = &#36;row;
  }
  return;
}
</pre>

EOPAGESTR;
echo $page_str;

site_footer();
?>