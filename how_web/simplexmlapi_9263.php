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


site_header("PHP's SimpleXML API");

$page_str = <<<EOPAGESTR5

<p>The SimpleXML API is new in PHP5. Characterized as an object-mapping API,
SimpleXML dispenses with Web standards and absolute flexibility in favor of
simplicity and modest memory usage. If you just need to read some data from an
XML document and write some other data back in, the SimpleXML likely will
require the fewest lines of code of all possible approaches to the problem.</p>

<p>Here’s the idea behind SimpleXML: As in the DOM approach, SimpleXML parses
an XML document and holds the whole thing in memory. However, rather than hold
the document as a DOM object (which you must further manipulate before you can
use its contents), its elements are stored as native PHP variables and so are
immediately useable. Because many DOM tasks do not actually require you to
traverse all the children and parents of a document, but rather perform
repetitive tasks on well-defined nodes, SimpleXML ultimately constitutes a
PHP-specific compromise between the SAX and DOM approaches.</p>

<h2>Using SimpleXML</h2>

<p>When using SimpleXML, you read a passage of XML text — either a string or a
file — into a variable with the function simplexml_load_string() or
simplexml_load_file(). You then have a local object you can refer to directly.
Listing 40-8 shows how the SimpleXML API can be used to get variable values out
of an XML file with just a few lines of code.</p>

<p>Listing 40-8 demonstrates a typical use of SimpleXML.</p>

<p>Listing 40-8: SimpleXML sample (simplexml.php)</p>

<pre>
&lt;?php

&#36;recipe = simplexml_load_file("recipe.xml");

&#36;ingredients = &#36;recipe-&gt;ingredients;
&#36;directions  = &#36;recipe-&gt;directions;
&#36;servings    = &#36;recipe-&gt;servings;

foreach (&#36;ingredients as &#36;ingredient)
{
  print "&lt;P&gt;Ingredient: &#36;ingredient";
}

print "&lt;P&gt;Directions: &#36;directions";
print "&lt;P&gt;Serves &#36;servings";

?&gt;
</pre>

<h2>SimpleXML functions</h2>

<p>Table 40-7 lists the most important SimpleXML functions, with descriptions
of what they do.</p>

<table class="events" width="678">
  <caption>Table 40-7: SimpleXML Function Summary</caption>
  <tr>
    <td>simplexml_load_file(file)</td>
    <td>Import and parse a file.</td>
  </tr>
  <tr>
    <td>simplexml_load_string(string)</td>
    <td>Import and parse a string.</td>
  </tr>
  <tr>
    <td>simplexml_import_dom(DomDocument)</td>
    <td>This function allows you to convert a DomDocument object into a
    SimpleXML object, and then treated just like an imported XML file or
    string.</td>
  </tr>
</table>

EOPAGESTR5;
echo $page_str;

site_footer();

?>