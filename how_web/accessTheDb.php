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


site_header('Access the Database');

$page_str = <<<EOPAGESTR

<p>Get one thing straight before you use this page: All sensitive information
has been altered. So, don't use as-is.</p>

<p>There is a ping-pong variable to set:</p>

<pre>
&#36;pingPong = "string for it!";
</pre>

<p>NOTE: The definition of the ping-pong variable can be inside an included script
(as long as this script gets included before the inclusion of the database
access script).</p>

<p>Include database access script:</p>

<pre>
require_once('includes/accessMyDb.php');
</pre>

<p>Example of Database Access Script:</p>

<pre>
&lt;?php
// This script will connect to the database using the parameters
// specified in the variables below. If connecting or selecting
// fail an error message will be printed.

if (!isSet(&#36;pingPong)) {
  &#36;pingPong = "";
}

if (&#36;pingPong != "the ping-pong string.#*^3") {
  exit('&lt;h2>You can not access this file!&lt;/h2>');
}

&#36;hostname = "mysql.domain-name.com";
&#36;user = "mainRoot_alex";
&#36;password = "onlyiknowit";
&#36;database = "mainRoot_DB_NAME";

if (!(&#36;link=mysql_connect(&#36;hostname, &#36;user, &#36;password))) {
  echo "Your PHP script says: Error connecting to database on the host.";
}
if (!mysql_select_db(&#36;database, &#36;link)) {
  echo "Your PHP script says: Error selecting database on the host.";
}

?&gt;
</pre>

EOPAGESTR;
echo $page_str;

site_footer();
?>