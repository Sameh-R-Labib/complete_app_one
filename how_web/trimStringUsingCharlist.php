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


site_header('Trim String Using Character List');

$page_str = <<<EOPAGESTR

<p>Besides being able to trim whitespace trim (ltrim and rtrim) can also
use a character list to specify which characters get trimmed. Here is an
example where ltrim is being asked to trim all characters belonging to
the set of characters {'-', '0'}.</p>

<pre>
function really_is_int(&#36;val)
{
  if(func_num_args() !== 1)
      exit(__FUNCTION__.'(): not passed 1 arg');

  return (&#36;val !== true) && ((string)abs((int) &#36;val)) === ((string) ltrim(&#36;val, '-0'));
}
</pre>

EOPAGESTR;
echo $page_str;

site_footer();
?>