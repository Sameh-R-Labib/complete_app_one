<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

/*
ADJUSTED BID HISTORY
--------------------
This script will produce and display a table representing the
adjusted bid history for Howard County Public Schools transportation.

Author: Sameh Labib (03/27/2011)
*/

session_start();

require_once('includes/login_funcs.php');
require_once('includes/db_vars.php');
include_once('includes/header_footer.php');
require_once('includes/table_funcs.php');

// User must be logged in and be someone special.
if (!user_isloggedin() || !user_type_cookieIsSet()) {
  form_destroy();
  $host = $_SERVER['HTTP_HOST'];
  header("Location: http://{$host}/web/login.php");
  exit;
}
if ($_COOKIE['user_type'] != 1) {
  form_destroy();
  die('Script aborted #3098. -Programmer.');
}

// Cancel if requested.
if (isset($_POST['cancel'])) {
  form_destroy();
  $host = $_SERVER['HTTP_HOST'];
  $uri = $_SERVER['PHP_SELF'];
  header("Location: http://$host$uri");
  exit;
}

if (isSet($_SESSION['ABH_mode'])) {
  $mode = $_SESSION['ABH_mode'];
} else {
  $mode = 'stageOne';
}


if ($mode == 'stageOne') {
  site_header('Adjusted Bid History');
  explainPurposeOfForm();
  explainYear(); // Explain what to enter for Year value.
  explainFuel(); // Explain what to enter for price of fuel.
  presentYearFuelForm();
  site_footer();
  $_SESSION['ABH_mode'] = 'process the form';
} elseif ($mode == 'process the form') {
  processTheForm();
  getActualBidValues();
  calculateAnnualPay();
  compensateForSize();
  compensateForInflation();
  compensateForFuelSubsidies();
  getAvgBidForEachYear();
  getStandardDeviationsFEY();
  site_header('Adjusted Bid History');
  displayResults();
  site_footer();
  form_destroy();
  exit();
} else {
  form_destroy();
  die('The script died for reason # 294875654. -Programmer.');
}




/* ***********************************************************
FUNCTIONS SECTION:
*/
function form_destroy() {
  $_SESSION['ABH_mode'] = 'stageOne';
  $_SESSION['ABH_submitToken'] = "";
  $_SESSION['ABH_year'] = 0;
  $_SESSION['ABH_fuel'] = 0;
  $_SESSION['ABH_bid'] = array();
  $_SESSION['ABH_deltaFuel'] = array();
  $_SESSION['ABH_statsForYear'] = array();

  return;
}




function explainPurposeOfForm() {
  $page_str = <<<EOPAGESTR
<p>The purpose of this form is to show the trend in bid rates for school
bus routes in Howard County. This form will produce a table which shows bid
result averages over the past years compensated for two things: One is inflation.
The other is the adjustments in pay from the Board for fluctuation in fuel prices.</p>

<p>Note: Bids from the current bid year that are in the database will be ignored.
Only previous year bids are included in the result.</p>

EOPAGESTR;
  echo $page_str;
  return;
}




function explainYear() {
  $page_str = <<<EOPAGESTR
<p>The term "year" on this page refers to the current year for bid sessions. Bidding
occur between November and June (during the school year). The "year" will be the one
associated with the Spring of that time period.</p>

EOPAGESTR;
  echo $page_str;
  return;
}




function explainFuel() {
  $page_str = <<<EOPAGESTR
<p>The term "fuel" on this page refers to the cost of a gallon of diesel
associated with the "year" mentioned above. You will need to determine
the value for "fuel" by guessing. Try to guess what "fuel" will be in June
of the "year"; because, that "fuel" will be the baseline for compensation
according to the Board.</p>

EOPAGESTR;
  echo $page_str;
  return;
}




function presentYearFuelForm() {
  $submitToken = time();
  $_SESSION['ABH_submitToken'] = $submitToken;
  $php_self = $_SERVER['PHP_SELF'];

  $page_str = <<<EOPAGESTR
<form action="$php_self" method="post" class="loginform">
  <fieldset>
  <legend>Your input:</legend>
  <div>
    <label for="year" class="fixedwidth">Year (CCYY)</label>
    <input type="text" name="year" id="year" value="" size="4" maxlength="4"/>
  </div>
  <div>
    <label for="fuel" class="fixedwidth">Fuel (#.###)</label>
    <input type="text" name="fuel" id=fuel"" value="" size="6" maxlength="7"/>
  </div>
  <div>
    <input type="hidden" name="submitToken" value="$submitToken">
  </div>
  <div class="buttonarea">
    <input type="submit" name="cancel" value="Cancel"/>
    <input type="submit" name="submit" value="Submit"/>
  </div>
  </fieldset>
</form>

EOPAGESTR;
  echo $page_str;
  return;
}




function processTheForm() {
/*
The purpose of this function is to read, validate and store the values
for year and fuel.
*/
  if (isset($_POST['submitToken'])) {
    $submitToken = $_POST['submitToken'];
  } else {
    $submitToken = "";
  }
  if ($submitToken != $_SESSION['ABH_submitToken']) {
    form_destroy();
    $host = $_SERVER['HTTP_HOST'];
    $uri = $_SERVER['PHP_SELF'];
    header("Location: http://$host$uri");
    exit;
  }

  if (isset($_POST['year'])) {
    $year = $_POST['year'];
  } else {
    form_destroy();
    die('You probably bailed last time. Start over. Err: 581649. -Programmer.');
  }

  if (isset($_POST['fuel'])) {
    $fuel = $_POST['fuel'];
  } else {
    form_destroy();
    die('You probably bailed last time. Start over. Err: 671649. -Programmer.');
  }

  /*
  Validate.
  */
  // trim
  $year = trim($year);
  $fuel = trim($fuel);
  // check length
  if ( strlen($year) > 4 OR strlen($fuel) > 7 ) {
    form_destroy();
    die('String too long. Err: 544649. -Programmer.');
  }
  if ( strlen($year) < 4 OR strlen($fuel) < 1 ) {
    form_destroy();
    die('String too short. Err: 584449. -Programmer.');
  }

  /*
  If either year or fuel is non-numeric abort.
  */
  if ( !is_numeric($year) OR !is_numeric($fuel)) {
    form_destroy();
    die('Not numeric. Err: 124412. -Programmer.');
  }

  if (!really_is_int($year)) {
    form_destroy();
    die('year is not integer Error 2438179. -Programmer.');
  }

  if ( $year < 1987 OR $year > 2300) {
    form_destroy();
    die('year is out of range Error 3638149. -Programmer.');
  }

  $_SESSION['ABH_year'] = $year;
  $_SESSION['ABH_fuel'] = $fuel;

  return;
}




function really_is_int($val)
{
  if(func_num_args() !== 1)
      exit(__FUNCTION__.'(): not passed 1 arg');

  $weirdPart = ((string)abs((int) $val));
  if ($weirdPart === "0") {
    return TRUE;
  }

  return ($val !== true) && ((string)abs((int) $val)) === ((string) ltrim($val, '-0'));
}




function getActualBidValues() {
/*
This function will:
  - Read raw data from bid_history database table.
      (miles, hours, base_mile_r, ext_mile_r, base_hr_r, ext_hr_r, year)
  - Put it into session variable.
"bid" will be the variable name for the array.
*/
  $query = "SELECT miles, hours, base_mile_r, ext_mile_r, base_hr_r, ext_hr_r, year
            FROM bid_history";
  $result = mysql_query($query);
  if (!$result) {
    die('Query failed. Err: 77398224. -Programmer.');
  }
  $bid = array();
  while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
    if ($row['year'] != $_SESSION['ABH_year']) {
      $bid[] = $row;
    }
  }
  $_SESSION['ABH_bid'] = $bid;
  return;
}




function calculateAnnualPay() {
/*

This function calculates and stores into $_SESSION['ABH_bid'] array the annual pay
which correlates to each of the actual bids.

day_pay = (55 * base_mile_r) + (3 * base_hr_r)
                 + (x_miles * ext_mile_r) + (x_hrs * ext_hr_r)
                 
annual_pay = 180 * day_pay

*/

  $bid = $_SESSION['ABH_bid'];

  foreach ($bid as $bidArrKey => $bidArrElem) {
    $baseComp = (55 * $bidArrElem['base_mile_r']) + (3.0 * $bidArrElem['base_hr_r']);
    /*
    x_milesDayComp
    */
    $miles = $bidArrElem['miles'];
    if ($miles > 55) {
      $x_miles = $miles - 55;
      $x_mileDayComp = $x_miles * $bidArrElem['ext_mile_r'];
    } else {
      $x_mileDayComp = 0;
    }
    /*
    x_hrsDayComp
    */
    $hours = $bidArrElem['hours'];
    if ($hours > 3.0) {
      $x_hrs = $hours - 3.0;
      $x_hrsDayComp = $x_hrs * $bidArrElem['ext_hr_r'];
    } else {
      $x_hrsDayComp = 0;
    }
    $day_pay = $baseComp + $x_mileDayComp + $x_hrsDayComp;
    $annual_pay = 180 * $day_pay;
 
    $bid[$bidArrKey]['annual_pay'] = $annual_pay;
  }
  
  $_SESSION['ABH_bid'] = $bid;
  return;
}




function compensateForSize() {
/*
This means adjust the annual_pay for each bid array element to reflect
what the contractor would have bid had the route been of standard size.

Standrd Size Route:
58 miles
4.38 hours

Export into $_SESSION['ABH_bid'].

Compensation factors:
  - Small Route (58/3:30)      criteria: time between less than 3:36
        1.033                            (if mileage is greater than 69 don't use this bid)
  - Medium Route (58/3:55)     criteria: time between 3:37 and 4:08
        1.016                            (if mileage is greater than 74 don't use this bid)
  - Standard Route (58/4:23)   criteria: time between 4:09 and 4:32
        1.0                              (if mileage is greater than 82 don't use this bid)
  - Large Route (58/4:40)      criteria: time between 4:33 and 4:48
        0.986                            (if mileage is greater than 87 don't use this bid)
  - X-Large Route (62/5:00)    criteria: time between 4:49 and 5:12
        0.97                             (if mileage is greater than 90 don't use this bid)
  - XX-Large Route (75/5:23)   criteria: time between 5:13 and 5:40
        0.957                            (if mileage is greater than 96 don't use this bid)
*/
  $bid = $_SESSION['ABH_bid'];

  foreach ($bid as $bidArrKey => $bidArrElem) {
    $hours = $bidArrElem['hours'];
    $miles = $bidArrElem['miles'];
    $year = $bidArrElem['year'];
    $annual_pay = $bidArrElem['annual_pay'];

    // if hours <= 3.6
    if ( $hours <= 3.6) {
      if ( $miles <= 69 ) {
        $bid[$bidArrKey]['annual_pay'] = $annual_pay * 1.033;
      } else {
        echo "Data Point ID: year = $year, hours = $hours, miles = $miles.<br/>\n";
        form_destroy();
        die('The script encountered an unusual data point. Do something about it.');
      }

    // if hours > 3.6 and <= 4.13333
    } elseif ( $hours > 3.6 AND $hours <= 4.13333 ) {
      if ( $miles <= 77 ) {
        $bid[$bidArrKey]['annual_pay'] = $annual_pay * 1.016;
      } else {
        if (  $miles > 77 AND $miles <= 110 ) {
          $bid[$bidArrKey]['annual_pay'] = $annual_pay * 0.986;
        } else {
          echo "Data Point ID: year = $year, hours = $hours, miles = $miles.<br/>\n";
          form_destroy();
          die('The script encountered an unusual data point. Do something about it.');
        }
      }

    // if hours > 4.13333 and <= 4.53333
    } elseif ( $hours > 4.13333 AND $hours <= 4.53333 ) {
      if ( $miles > 82 AND $miles <= 115 ) {
        $bid[$bidArrKey]['annual_pay'] = $annual_pay * 0.986;
      } elseif ( $miles > 115 ) {
        echo "Data Point ID: year = $year, hours = $hours, miles = $miles.<br/>\n";
        form_destroy();
        die('The script encountered an unusual data point. Do something about it.');
      }

    // if hours > 4.53333 and <= 4.8
    } elseif ( $hours > 4.53333 AND $hours <= 4.8 ) {
      if ( $miles <= 87 ) {
        $bid[$bidArrKey]['annual_pay'] = $annual_pay * 0.986;
      } else {
        echo "Data Point ID: year = $year, hours = $hours, miles = $miles.<br/>\n";
        form_destroy();
        die('The script encountered an unusual data point. Do something about it.');
      }

    // if hours > 4.8 and <= 5.2
    } elseif ( $hours > 4.8 AND $hours <= 5.2 ) {
      if ( $miles <= 90 ) {
        $bid[$bidArrKey]['annual_pay'] = $annual_pay * 0.97;
      } else {
        echo "Data Point ID: year = $year, hours = $hours, miles = $miles.<br/>\n";
        form_destroy();
        die('The script encountered an unusual data point. Do something about it.');
      }
    
    // if hours > 5.2 and <= 5.6667
    } elseif ( $hours > 5.2 AND $hours <= 5.6667 ) {
      if ( $miles <= 105 ) {
        $bid[$bidArrKey]['annual_pay'] = $annual_pay * 0.957;
      } else {
        echo "Data Point ID: year = $year, hours = $hours, miles = $miles.<br/>\n";
        form_destroy();
        die('The script encountered an unusual data point. Do something about it.');
      }
    
    // Bad data point.
    } else {
      echo "Data Point ID: year = $year, hours = $hours, miles = $miles.<br/>\n";
      form_destroy();
      die('The script encountered an unusual data point. Do something about it.');
    }
  }
  
  $_SESSION['ABH_bid'] = $bid;
  return;
}




function compensateForInflation() {
/*
This function adjusts the value of annual_pay for each bid in our history.
It compensates for inflation. After compensation all annual_pay values
become relevant to the current state of the econony with respect to inflation
except possibly for fuel prices. There is a different function which will
compensate for fuel subsidies and another for one time raises.

The rate of inflation will be represented by a constant variable:
define(ABH_INFLATION_RATE, .04);
4% annually

The algorith which figures out the compensated value for each annual_pay
will be of the type which compounds the effect of inflation over the years.
*/
  define('ABH_INFLATION_RATE', .0322);
  $bid = $_SESSION['ABH_bid'];
  $curr_year = $_SESSION['ABH_year'];
  foreach ($bid as $bidArrKey => $bidArrElem) {
    $year = $bidArrElem['year'];
    $annual_pay = $bidArrElem['annual_pay'];
    if ($curr_year < $year) {
      form_destroy();
      die('Error 1586988. -Programmer.');
    }
    /*
    Loop which figures out compensated annual_pay.
    */
    for ($i = $year; $i < $curr_year; ++$i) {
      $annual_pay += ABH_INFLATION_RATE * $annual_pay;
    }
    $bid[$bidArrKey]['annual_pay'] = $annual_pay;
  }
  $_SESSION['ABH_bid'] = $bid;
  return;
}




function compensateForFuelSubsidies() {
/*
As far back as I can remember there has been a fuel subsidy to
compensate for the fluctuation in the price of a gallon of diesel.
The amount of the subsidy is a penny per mile for every 5 cent
change in the price of a gallon of diesel for the month from
the month before.

This function will adjust the value of annual_pay for each bid.
After compensation all annual_pay values will become relevant to
the current price of a gallon of diesel. In other words this is
what will happen to the annual_pay of each bid:
  - All monthly changes (from time of bid to the
    current time) will be added up to get a net
    change in compensation for a mile traveled.
  - Calculate a value for how much change this
    would make in a single school day (58 miles).
    Call this delta_day.
  - delta_year = 180 * delta_day.
  - annual_pay = annual_pay + delta_year.
*/
  $bid = $_SESSION['ABH_bid'];
  $curr_year = $_SESSION['ABH_year'];
  // price in June of $curr_year
  $curr_priceOfGal = $_SESSION['ABH_fuel'];
  foreach ($bid as $bidArrKey => $bidArrElem) {
    $year = $bidArrElem['year'];
    $annual_pay = $bidArrElem['annual_pay'];

    // DOLLAR per mile change.
    $delta_mileRate = calculateDeltaMileRate($year);

    $delta_annualRate = 10440 * $delta_mileRate;
    $bid[$bidArrKey]['annual_pay'] = $annual_pay + $delta_annualRate;
  }
  $_SESSION['ABH_bid'] = $bid;
  return;
}




function calculateDeltaMileRate($yearIn) {
/*
Return the dollar value for the change in mile pay rate from June of
"the original bid year" ($yearIn) to June of "the current bid year."

Keep in mind that when I say "the original bid year" or "the current
bid year" I am referring to the same definition for year which was
stated earlier when I explained to the user how to specify the year
in which the current bid is taking place.

There will be an array:
    $_SESSION['ABH_deltaFuel']
      key   - year (of bid)
      value - delta (the dollar per mile change which occured from year of
              bid to current bid year)

Since this array which holds the historical values of dollar change
in compensation per mile will not differ during the entire execution
of this script we should load it only once from the database.
*/
  /*
  load it if neccessary
  */
  if (!isset($_SESSION['ABH_deltaFuel']) OR empty($_SESSION['ABH_deltaFuel'])) {
    get_deltaFuelArr(); // Abort if no elements found.
  }

  /*
  Find out the (in dollars) change in mile pay rate corresponding to
  the original bid year $yearIn.
  */
  if (!isset($_SESSION['ABH_deltaFuel'][$yearIn])) {
    form_destroy();
    die('You may have to add a price for diesel in June of that year to database.');
  } else {
    $delta_mileRate = $_SESSION['ABH_deltaFuel'][$yearIn];
  }

  return $delta_mileRate;
}




function get_deltaFuelArr() {
/*
This function creates the array $_SESSION['ABH_deltaFuel'].
If we can't find at least one element to put into this array
then the function must abort.

$_SESSION['ABH_deltaFuel'] is described in function calculateDeltaMileRate.
Here it is again:

  Each element of this array will have:
    key   - year (of bid)
    value - delta (the dollar per mile change which occured from year of
            bid to current bid year)

To get the delta for each year we need to get the price of a gallon
at the original bid year and at the current bid year (which is our guestimate).
After we get those two values multiply each by 100.
Subtract the original bid year price from the current bid year price.
Convert the result to int by rounding to the closest int. Do int division
dividing the result of the subtraction by five. Divide by 100 using float
division to get a dollar amount; This will be the delta.
*/
  $priceCurr = $_SESSION['ABH_fuel'];

  /*
  Get all the year/price pairs from table bid_fuelSurvey which
  have a month value of six (6). Put them into an array called
  yearData.
  */
  $query = "SELECT year, price
            FROM bid_fuelSurvey
            WHERE month=6";
  $result = mysql_query($query);
  if (!$result) {
    form_destroy();
    die('Query failed. Err: 683774294. -Programmer.');
  }
  $yearData = array();
  while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
    $yearData[] = $row;
  }
  if (empty($yearData)) {
    form_destroy();
    die('Err: 883574154. -Programmer.');
  }

  // generate $deltaFuel array
  $deltaFuel = array();
  $priceCurr = $priceCurr * 100; // we don't want this inside the loop
  foreach($yearData as $record) {
    $year = $record['year'];
    $price = $record['price'];
    
    $price = $price * 100;
    
    $delta = $priceCurr - $price;
    $delta = round($delta);
    $delta = $delta / 5;
    $delta = floor($delta);
    $delta = $delta / 100;
    
    $deltaFuel[$year] = $delta;
  }

  if (empty($deltaFuel)) {
    form_destroy();
    die('Err: 993574181. -Programmer.');
  }

  $_SESSION['ABH_deltaFuel'] = $deltaFuel;
  return;
}




function getAvgBidForEachYear() {
/*
Start building an array called statsForYear by assigning a value for
year and average to each element. Store array in session.
*/

  /*
  How am I going to do this?
  We already have:
    $_SESSION['ABH_bid']
  which holds values:
    miles, hours, base_mile_r, ext_mile_r, base_hr_r, ext_hr_r, year, annual_pay
  for each bid.

  Now, to hold stats for each year:
  I'll build array:
    $_SESSION['ABH_statsForYear'].
  The key is the year:
    THE YEAR IS THE KEY
  This year corresponds to a unique year in $_SESSION['ABH_bid'].
  Each element is array.
  Each element has key:
    o average
    o numOfBids
    o (more keys will be added by other functions)
  */
  $bid = $_SESSION['ABH_bid'];
  $statsForYear = array();

  /*
  Go through all the bids adding up pay for each year and
  counting the number of bids for each year.
  */
  foreach ($bid as $bid_element) {
    $year = $bid_element['year'];
    $pay =  $bid_element['annual_pay'];
    if (!isset($statsForYear[$year])) {
      $statsForYear[$year]['average'] = $pay;
      $statsForYear[$year]['numOfBids'] = 1;
    } else {
      $statsForYear[$year]['average'] += $pay;
      $statsForYear[$year]['numOfBids'] += 1;
    }
  }

  /*
  Abort if no years.
  */
  if (empty($statsForYear)) {
    form_destroy();
    die('Err: 718007791. -Programmer.');
  }

  /*
  Iterate though $statsForYear assigning average its true value.
  */
  foreach ($statsForYear as $key => $value) {
    $total = $value['average'];
    $numOfBids = $value['numOfBids'];
    $true_average = $total / $numOfBids;
    $statsForYear[$key]['average'] = $true_average;
  }

  $_SESSION['ABH_statsForYear'] = $statsForYear;
  return;
}




function getStandardDeviationsFEY() {
/*
For each element of $_SESSION['ABH_statsForYear'] assign its stdDev
the value for standard deviation.

Here is how standard deviation is calculated:
  SD = square root of ([the sum of all the squares of the deviations
       of these bids from the average for that same year] divided
       by the number of bids for that same year)
*/
  /*
  Gives each bid value.
  */
  $bid = $_SESSION['ABH_bid'];
  
  /*
  Gives average bid.
  Gives number of bids.
  Provides storage for the sum of all the squares of the deviations
    from the average.
  Provedes storage for standard deviation.
  */
  $statsForYear = $_SESSION['ABH_statsForYear'];

  /*
  For each year get the value for:
      The sum of all the squares of the deviations from the average.
  */
  foreach ($statsForYear as $year => $itsStats) {
    $avg = $itsStats['average'];
    $sum = 0;
    foreach ($bid as $bid_element) {
      $bid_year = $bid_element['year'];
      $bid_amount = $bid_element['annual_pay'];
      if ($bid_year == $year) {
        $deviation = $bid_amount - $avg;
        $sum += pow($deviation, 2);
      }
    }
    $statsForYear[$year]['sum_sqr_dev'] = $sum;
  }

  /*
  For each year get the value for:
      The standard deviation.
  */
  foreach ($statsForYear as $year => $itsStats) {
    $sum_sqr_dev = $itsStats['sum_sqr_dev'];
    $numOfBids = $itsStats['numOfBids'];
    $temp = $sum_sqr_dev / $numOfBids;
    $statsForYear[$year]['std_dev'] = sqrt($temp);
  }

  $_SESSION['ABH_statsForYear'] = $statsForYear;
  return;
}




function displayResults() {
/*
This function displays the table of statistics.
Each row is for a bid year found in our bid records.
There will be the columns:
  o Year
  o Avg. Bid
  o Std. Deviation
  o Qty. Awarded
*/
  /*
  Create array to be displayed as a table.
  $results will be its name.

  Description of array: an array whose elements are arrays.
  All indexing is numeric and sequential. Each element will
  corresponds to a single row and is an array of the column
  values for that row.
  */
  $results = array();
  $statsForYear = $_SESSION['ABH_statsForYear'];
  foreach ($statsForYear as $year => $itsStats) {
    $average = $itsStats['average'];
    $average = number_format($average);
    $std_dev = $itsStats['std_dev'];
    $std_dev = number_format($std_dev);
    $numOfBids = $itsStats['numOfBids'];
    $results[] = array($year, $average, $std_dev, $numOfBids);
  }

  /*
  Create table string.
  */
  if (empty($results)) {
    form_destroy();
    die('Err: 718007791. -Programmer.');
  }
  $cap = 'Results of Bid History Analysis';
  $nOfCols = 4;
  $tblHeader = array('Year', 'Avg. Bid', 'Std. Deviation', 'Qty. Awarded');
  $htmlTable_1 = makeTable($cap, $nOfCols, $tblHeader, $results);

  /*
  Display table.
  */
  echo $htmlTable_1;

  return;
}


?>