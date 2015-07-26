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


site_header('Validate Value is Integer');

$page_str = <<<EOPAGESTR

<p>Sample function call:</p>

<pre>
if (!really_is_int(&#36;id)) {
  form_destroy();
  die('id is not integer Error 2438179. -Programmer.');
}
</pre>

<p>WARNING: The original version of function really_is_int fails to
return the appropriate
value when passed a zero (0). Zero is an integer as are all negative whole numbers.</p>

<p>Sample user defined function really_is_int:</p>

<pre>
function really_is_int(&#36;val)
{
  if(func_num_args() !== 1)
      exit(__FUNCTION__.'(): not passed 1 arg');

  &#36;weirdPart = ((string)abs((int) &#36;val));
  if (&#36;weirdPart === "0") {
    return TRUE;
  }

  return (&#36;val !== true) && ((string)abs((int) &#36;val)) === ((string) ltrim(&#36;val, '-0'));
}
</pre>

<p>I used the original version of this function in createA_kda_funcs.php.</p>

EOPAGESTR;
echo $page_str;

site_footer();
?>