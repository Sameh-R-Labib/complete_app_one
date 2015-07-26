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


site_header('Checkbox Multi-Select');

$page_str = <<<EOPAGESTR5

<p>The code which follows comes from processAnInvoice_funcs.php. First,
look at the flowchart.</p>

<div>
  <img src="checkboxmultiselect.jpg" width="520" height="1180" alt="checkbox multi-select
  flowchart" />
</div>

<p>Extracted from function selectFromExistingMIDs. This code produces the form:</p>

<pre>
  /*
  First we need to gather the information which will be presented in the
  form. This includes the id and label of each maintenance item for this
  vehicle. We will gather this information in a two-dimensional array. The first
  dimension, or top level if you want to call it that, corresponds to
  the maintenance items. The second dimension corresponds to the two field values
  of the particular maintenance item.
  This array will be called: mntItem.
  For example, the first maintenance item is going to be mntItem[0].
  mntItem[0][0] will contain the id value of mntItem[0].
  mntItem[0][1] will contain the label value of mntItem[0]
  */

  /*
  To do this first I have to get these values from the database.
  I'll use the same functions I used before to acquire data to
  populate an HTML table.
  */
  &#36;tableName = "maintenItems";
  &#36;fieldNames = array('id', 'label');
  &#36;whereClause = "WHERE vehicleId = '&#36;vclID'";
  &#36;mntItem = getValuesForHTML_Table(&#36;fieldNames, &#36;tableName, &#36;whereClause);
  if (&#36;mntItem == FALSE) {
    form_destroy();
    die('Error: no records found (0994128). -Programmer.');
  }
  
  /*
  The array of maintenance items we have here will be useful later. So,
  I'll store it in a session variable.
  */
  &#36;_SESSION['originalChoices'] = array();
  &#36;_SESSION['originalChoices'] = &#36;mntItem;
  
  /*
  Now, I'll make the check boxes.
  */
  &#36;chkBoxes = chkBoxes(&#36;mntItem);

  /*
  Manage protection from aborted form since code uses sessions.
  In other words the code which validates the values received from a
  submitted form needs to know that it is not executing after the user
  has come back after a previously abandoned instance of the script.
  */
  &#36;submitToken = time();
  &#36;_SESSION['PAI_submitToken'] = &#36;submitToken;

  /*
  Construct page:
  */
  site_header('Process An Invoice');
  
  // Superglobals don't work with heredoc
  &#36;php_self = &#36;_SERVER['PHP_SELF'];
  
  &#36;page_str = &lt;&lt;&lt;EOPAGESTR

&lt;h2>Step Two&lt;/h2>

&lt;p>Below you will find all the maintenance items in the system for this vehicle.
Please, select all the ones which apply to this invoice. If you do not find
all the maintenace items which belong on this invoice do not worry. You will
be given the opportunity to add those later.&lt;/p>


&lt;form action="&#36;php_self" method="post" class="loginform">
  &lt;fieldset>
  &lt;legend>Specify all maintenance items:&lt;/legend>
&#36;chkBoxes
  &lt;div>
    &lt;input type="hidden" name="submitToken" value="&#36;submitToken">
  &lt;/div>
  &lt;div class="buttonarea">
    &lt;input type="submit" name="cancel" value="Cancel"/>
    &lt;input type="submit" name="submit" value="Submit existing MIDs!"/>
  &lt;/div>
  &lt;/fieldset>
&lt;/form>

EOPAGESTR;
  echo &#36;page_str;
  site_footer();
  &#36;_SESSION['ProcAnInv_mode'] = 'stageThree';
  return;
</pre>

<p>This is the function which produces the checkboxes in the form:</p>

<pre>
function chkBoxes(&#36;mntItem_in) {
/*
Input: &#36;mntItem_in is a two-dimensional array. The first
  dimension corresponds to the maintenance items. The second dimension
  corresponds to the two field values of the particular maintenance item.
  For example, the first maintenance item is going to be mntItem[0].
  mntItem_in[0][0] will contain the id value of mntItem[0].
  mntItem_in[0][1] will contain the label value of mntItem[0]
Action: The function chkBoxes() takes this input array and produces a
  string containing the HTML for all the checkbox form fields corresponding
  to the first dimension of the array.
Return: If &#36;mntItem_in array is empty or unavailable the script will die.
  Otherwise, the string will be returned.
*/

  if (!isset(&#36;mntItem_in) OR !is_array(&#36;mntItem_in) OR sizeof(&#36;mntItem_in) &lt; 1) {
    form_destroy();
    die("Function failed to create check boxes because no array was passed.");
  }

  /*
  Here is sample code for a check boxes div on my site.

  &lt;div>
     &lt;p>&lt;b>MVA CDL Status&lt;/b>&lt;/p>
     &lt;div>
        &lt;input type="checkbox" name="cdlAB" id="cdlAB" value="1" checked="checked"/>
        &lt;label for="cdlAB">CDL B (or better)&lt;/label>
     &lt;/div>
     &lt;div>
        &lt;input type="checkbox" name="cdlP" id="cdlP" value="1" checked="checked"/>
        &lt;label for="cdlP">P&lt;/label>
     &lt;/div>
     &lt;div>
        &lt;input type="checkbox" name="cdlS" id="cdlS" value="1" checked="checked"/>
        &lt;label for="cdlS">S&lt;/label>
     &lt;/div>
     &lt;div>
        &lt;input type="checkbox" name="aBrakes" id="aBrakes" value="1" checked="checked"/>
        &lt;label for="aBrakes">Air Brakes&lt;/label>
     &lt;/div>
  &lt;/div>
  */
  
  /*
  We make sure we are starting with a fresh slate.
  */
  unset(&#36;_POST['mId']);

  &#36;chkB_str = "&#92;n&lt;div>&#92;n   &lt;p>&lt;b>Which maintenance items?&lt;/b>&lt;/p>&#92;n";

  /*
  Here is the loop that builds the main body of the check boxes.
  */
  unset(&#36;temp_1);
  unset(&#36;temp_2);
  reset(&#36;mntItem_in);
  &#36;i = 0;
  while (&#36;array_cell = each(&#36;mntItem_in))
  {
    &#36;temp_1 = &#36;array_cell['value'][0];
    &#36;temp_2 = &#36;array_cell['value'][1];
    &#36;chkB_str .=
    "   &lt;div>&#92;n" .
    "      &lt;input type=&#92;"checkbox&#92;" name=&#92;"mId[&#36;i][0]&#92;" id=&#92;"&#36;i&#92;" value=&#92;"&#36;temp_1&#92;"/>&#92;n" .
    "      &lt;label for=&#92;"&#36;i&#92;">&#36;temp_2&lt;/label>&#92;n" .
    "   &lt;/div>&#92;n" .
    "   &lt;div>&#92;n" .
    "      &lt;input type=&#92;"hidden&#92;" name=&#92;"mId[&#36;i][1]&#92;" value=&#92;"&#36;temp_2&#92;">" .
    "   &lt;/div>&#92;n";
    &#36;i += 1;
  }

  &#36;chkB_str .= "&lt;/div>&#92;n&#92;n";
  return &#36;chkB_str;
}
</pre>

<p>Excerpt from function askHowManyNewMIDs. This code retrieves information
produced by submitting the form:</p>

<pre>
  /*
  Store the maintenance items which the user has indicated belong on
  the invoice. The name of the variable will be: &#36;_SESSION['PAI_mIds'].
  At this point in time they can be found in the array &#36;_POST['mId'].
  If there are no
  values in this &#36;_POST['mId'] array then assign a message string
  to the variable which would later be used to show the maintenance
  items added so far.
  */
  if (!isset(&#36;_POST['mId']) OR !is_array(&#36;_POST['mId']) OR sizeof(&#36;_POST['mId']) &lt; 1) {
    &#36;infoMsg = "&lt;p>So far no maintenance items have been added to this invoice.&lt;/p>";
  } else {
    /*
    The list of maintenance items needs to be sanitized because of the way that
    the form created it. In the state which this array is in now there is a first
    dimension element for all the original elements before the formation of the
    check box array since the hidden portions were included no matter what.
    */
    &#36;_SESSION['PAI_mIds'] = array();
    if (isset(&#36;_POST['mId'])) {
      &#36;_SESSION['PAI_mIds'] = sanitize(&#36;_POST['mId']);
    }
</pre>

<p>This code helps the previous code which retrieved info from submitted form</p>

<pre>
function sanitize(&#36;array_in) {
/*
This is a helper function for the function askHowManyNewMIDs(). It takes the
post array submitted in the prior stage and strips away elements of the submitted
maintenance item array which are an aberation of the process of using check boxes
with a multidimensional array. Basically what we want to accomplish is as follows.
We want to unset all first dimension elements which do not contain a second
dimensional element which contains a valid value for its index zero element.
*/

  if (!isset(&#36;array_in) OR !is_array(&#36;array_in) OR sizeof(&#36;array_in) &lt; 1) {
    form_destroy();
    die("Script died at sanitize (990877999). -Programmer.");
  } else {
    /*
    Here is a loop that does the job.
    I can't use the while/each iteration because it does not allow me to
    have proper access to the array I'm iterating through in order to be
    able to unset the current array element.
    NEW APPROACH: I'll just copy the elements I want into a new array.
    Then return the new array.
    */
    unset(&#36;temp_1);
    unset(&#36;temp_2);
    reset(&#36;array_in);
    &#36;new_array = array();
    &#36;count = 0;
    while (&#36;array_cell = each(&#36;array_in))
    {
      if (isset(&#36;array_cell['value'][0])) {
        &#36;temp_1 = &#36;array_cell['value'][0];
        &#36;temp_2 = &#36;array_cell['value'][1];
        &#36;new_array[&#36;count][0] = &#36;temp_1;
        &#36;new_array[&#36;count][1] = &#36;temp_2;
        &#36;count++;
      }
    }
  }

  return &#36;new_array;
}
</pre>

EOPAGESTR5;
echo $page_str;

site_footer();
?>