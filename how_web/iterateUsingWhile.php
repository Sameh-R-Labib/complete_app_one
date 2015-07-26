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


site_header('Iterate Array Using While');

$page_str = <<<EOPAGESTR

<p>each(), is somewhat similar to next() but has the virtue of returning false
only after it has run out of array to traverse. each() returns an array itself,
which holds both keys and values for the key/value pair it is pointing at.
The array that each() returns has the following four key/value pairs:</p>

<ul>
  <li>Key: 0<br/>Value: current-key</li>
  <li>Key: 1<br/>Value: current-value</li>
  <li>Key: 'key'<br/>Value: current-key</li>
  <li>Key: 'value'<br/>Value: current-value</li>
</ul>

<p>Example:</p>

<pre>
function print_keys_and_values_each(&#36;city_array) {
/*
reliably prints everything in array
*/
  reset(&#36;city_array);
  while (&#36;array_cell = each(&#36;city_array)) {
    &#36;current_value = &#36;array_cell['value'];
    &#36;current_key = &#36;array_cell['key'];
    print("Key: &#36;current_key; Value: &#36;current_value<BR>");
  }
}

print_keys_and_values_each(&#36;major_city_info);
</pre>

<p>Output:</p>

<pre>
Key: 0; Value: Caracas
Key: Caracas; Value: Venezuela
Key: 1; Value: Paris
Key: Paris; Value: France
Key: 2; Value: Tokyo
Key: Tokyo; Value: Japan
</pre>

EOPAGESTR;
echo $page_str;

site_footer();
?>