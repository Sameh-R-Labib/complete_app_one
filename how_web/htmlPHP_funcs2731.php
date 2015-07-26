<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require_once('../includes/login_funcs.php');
require_once('../includes/db_vars.php');
require_once('../includes/header_footer.php');

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


site_header('HTML Functions');

$page_str = <<<EOPAGESTR5

<table class="events" width="678">
  <caption>HTML-Specific String Functions</caption>
  <tr>
    <th>Function</th>
    <th>Behavior</th>
  </tr>
  <tr>
    <td>htmlspecialchars()</td>
    <td>Takes a string as argument and returns the string with replacements for four
    characters that have special meaning in HTML. Each of these characters is replaced with
    the corresponding HTML entity, so that it will look like the original when rendered by
    a browser. The &amp; character is replaced by &amp;amp; "" (the double-quote character) is
    replaced by &amp;quot;; &lt; is replaced by &amp;lt;; &gt; is replaced by &amp;gt;.</td>
  </tr>
  <tr>
    <td>htmlentities()</td>
    <td>Goes further than htmlspecialchars(), in that it replaces all characters that have
    a corresponding HTML entity with that HTML entity.</td>
  </tr>
  <tr>
    <td>get_html_translation_table()</td>
    <td>Takes one of two special constants (HTML_SPECIAL_CHARS and HTML_ENTITIES), and
    returns the translation table used by htmlspecialchars() and htmlentities(),
    respectively. The translation table is an array where keys are the character strings
    and the corresponding values are their replacements.</td>
  </tr>
  <tr>
    <td>nl2br()</td>
    <td>Takes a string as argument and returns that string with &lt;BR&gt; inserted before all
    new lines (&#92;n). This is helpful, for example, in maintaining the apparent line length
    of text paragraphs when they are displayed in a browser.</td>
  </tr>
  <tr>
    <td>strip_tags()</td>
    <td>Takes a string as argument and does its best to return that string stripped of all
    HTML tags and all PHP tags.</td>
  </tr>
</table>

EOPAGESTR5;
echo $page_str;

site_footer();
?>