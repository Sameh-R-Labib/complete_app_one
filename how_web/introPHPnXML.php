<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

$docRoot = $_SERVER["DOCUMENT_ROOT"];

require_once("$docRoot/web/includes/login_funcs.php");
require_once("$docRoot/web/includes/db_vars.php");
require_once("$docRoot/web/includes/header_footer.php");

// User must be logged in and be someone special.
if (!user_isloggedin() || !user_type_cookieIsSet()) {
  // Redirect to login page.
  $host = $_SERVER['HTTP_HOST'];
  header("Location: http://{$host}/web/login.php");
  exit;
}
if ($_COOKIE['user_type'] != 1) {
  die('Script aborted #3098. -Programmer.');
}


site_header("Intro: PHP & XML");

$page_str = <<<EOPAGESTR5

<p>XML is one of the hottest buzzwords in the software business today; but what
does it mean for Joe or Jane Average PHP Developer? Well, it could very well be
the necessary precondition for a better Internet — one that is faster to
develop, more interactive, less junky, and more accessible to a larger
audience. With PHP, you’re already in an excellent position to smoothly
integrate XML into your Web development arsenal as the technology matures.</p>

EOPAGESTR5;
echo $page_str;

site_footer();

?>