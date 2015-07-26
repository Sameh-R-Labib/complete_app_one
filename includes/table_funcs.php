<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

/*
This script is for inclusion in any other script that needs these
table functions.
*/


function getValuesForHTML_Table($fieldNames_in, $tableName_in,
                                $whereClause_in) {
/*
This function gets passed a list of database table field names
$fieldNames_in and a database table name $tableName_in as parameters.
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

$fieldNames_in is an array which is numerically indexed
starting with zero. The value of each element will be a
name specifying a field of the database table $tableName_in.

$whereClause_in is a string which contains an SQL WHERE clause
*/

  /*
  Is there at least one field name?
  */
  if (!isset($fieldNames_in) OR !is_array($fieldNames_in)
                          OR sizeof($fieldNames_in) < 1) {
    die('Error 589760. -Programmer.');
  }


  /*
  Make a query string which retrieves the fields specified in
  $fieldNames_in and all the records in the table specified by
  $tableName_in.
  */
  
  /*
  Compose the $fieldNameList string.
  */
  $fieldNameList = reset($fieldNames_in);
  next($fieldNames_in);
  while ($array_cell = each($fieldNames_in)) {
    $fieldNameList .= ", " . $array_cell['value'];
  }

  $query = "SELECT $fieldNameList
            FROM $tableName_in
            $whereClause_in
            LIMIT 0, 300";

  $result = mysql_query($query);
  if (!$result) {
    die('The db query failed in include file table function. Error 88800881. -Programmer.');
  } elseif (mysql_num_rows($result) < 1) {
    return FALSE;
  }


  /*
  A loop that reads in the values from the query result into the
  two dimensional array which will be returned.
  */
  $i = 0;
  while ($row = mysql_fetch_row($result)) {
    $values[$i] = $row;
    $i += 1;
  }

  if ($i == 0) {
    die('Error 41554182. -Programmer. ');
  }


  return $values;
}



function makeTable($capIn, $nOfColsIn, $tblHeaderIn, $rowIn) {
/*
Return: string HTML table or just table caption string
Input: caption,
       how many columns,
       array of table header values as strings,
       array (two-dimensional) of table values as strings.
         - 1st level: row content
         - 2nd level: column content
         - Note: a two-dimensional array is an array whose
                 elements are arrays also.
Effects: Script dies if $rowIn is not a two dimensional array.
         If there are no rows then there will be no table except
         for the caption.
*/


  /*
  Put opening table tag into the string.
  */
  $tableStr = "<table class=\"events\" width=\"678\">\n";


  /*
  Put table caption into the string.
  */
  $tableStr .= "  <caption>$capIn</caption>\n";

  /*
  We don't want a table if no rows exist. Just the caption.
  */
  if ( count($rowIn) < 1) {
    $tableStr .= "</table>\n";
    return $tableStr;
  }

  /*
  Put table headers into the string.
  */
  $tableStr .= "  <tr>\n";
  reset($tblHeaderIn);
  while ($array_cell = each($tblHeaderIn)) {
    $headerTemp = $array_cell['value'];
    $tableStr .= "    <th>$headerTemp</th>\n";
  }
  $tableStr .= "  </tr>\n";


  /*
  Put all the table rows into the string.
  */
  if (!is_array($rowIn)) { die('Err: 316580044. -Programmer.'); }
  reset($rowIn);
  // Loop through 1st level array.
  while ($array_cell_1 = each($rowIn)) {
    $tableStr .= "  <tr>\n";
    $tempArr = $array_cell_1['value'];
    if (!is_array($tempArr)) { die('Err: 5865443208. -Programmer.'); }
    // Loop through 2nd level array.
    while ($array_cell_2 = each($tempArr)) {
      $tempStr = $array_cell_2['value'];
      $tableStr .= "    <td>$tempStr</td>\n";
    }
    $tableStr .= "  </tr>\n";
  }


  /*
  Put closing table tag into the string.
  */
  $tableStr .= "</table>\n";


  return $tableStr;
}


?>