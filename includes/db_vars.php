<?php
// This script will connect to the database using the parameters
// specified in the variables below. If connecting or selecting
// fail an error message will be printed.

if (!isSet($jeffWiggleLabib)) {
  $jeffWiggleLabib = "";
}

if ($jeffWiggleLabib != "Wake up Jeff 59990!") {
  exit('<h2>You can not access this file!</h2>');
}

$hostname = "localhost";
$user = "samehrlabib";
$password = "12bentsnoop";
$database = "samehrlabib_aTestMyLAMP";

if (!($link=mysql_connect($hostname, $user, $password))) {
  echo "Your PHP script says: Error connecting to database on the host.";
}
if (!mysql_select_db($database, $link)) {
  echo "Your PHP script says: Error selecting database on the host.";
}

?>