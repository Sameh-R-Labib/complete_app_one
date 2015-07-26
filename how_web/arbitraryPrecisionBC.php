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


site_header('Arbitrary Precision (BC)');

$page_str = <<<EOPAGESTR5

<p>The integer and double types are fine for most of the mathematical tasks that arise in
Web scripting, but each instance of these types is stored in a fixed amount of computer
memory, and so the size and precision of the numbers these types can represent is inherently
limited. Although the exact range of these types may depend on the architecture of your
server machine, integers typically range from –2 to the power 31 – 1 to 2 to the power 31
– 1, and doubles can represent
about 13 to 14 decimal digits of precision. For tasks that require greater range or precision,
PHP offers the arbitrary-precision math functions (also known as BC functions, from the name
of the Unix-based, arbitrary-precision calculating utility).</p>

<h2>note</h2>

<hr />

<p>I can read about this somewhere else if I ever need to.</p>

<hr />

EOPAGESTR5;
echo $page_str;

site_footer();
?>