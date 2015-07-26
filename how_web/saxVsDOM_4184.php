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


site_header("SAX versus DOM");

$page_str = <<<EOPAGESTR5

<div class="remarkbox">
  <div class="rbtitle">note-to-self</div>
  <div class="rbcontent">
    <p>I plan to mostly use DOM for editing HTML in my KDS.</p>
  </div>
</div>

<p>There are two common APIs for handling XML and XML documents: the Document
Object Model (DOM) and the Simple API for XML (SAX). PHP5 has one module for
each API. PHP5 also includes a new feature, the SimpleXML API. It allows you to
quickly convert XML elements into PHP variables, albeit with some limitations.
All three modules are now included in all PHP distributions.</p>

<p>You can use the DOM, SAX, or SimpleXML API to parse and change an XML
document. To create or extend an XML document entirely through the PHP
interface (in other words, without writing any of it by hand), you must use the
DOM. Each API has advantages and disadvantages:</p>

<ul>
  <li>
    <p>SAX: SAX is much more lightweight and easier to learn, but it basically
    treats XML as flowthrough string data. So if, for instance, you want to
    parse a recipe, you could whip up a SAX parser in PHP, which might enable
    you to add boldface to the ingredient list. Adding a completely new element
    or attribute would be very difficult, however; and even changing the value
    of one particular ingredient would be laborious.</p>
    <p>SAX is very good for repetitive tasks that can be applied to all
    elements of a certain type — for instance, replacing a particular element
    tag with HTML tags as a step toward transforming XML into HTML for display.
    The SAX parser passes through a document once from top to bottom — so it
    cannot “go back” and do things based on inputs later in the document.</p>
  </li>
  <li>DOM: PHP’s DOM extension reads in an XML file and creates a walkable
  object tree in memory. Starting with a document or an element of a document
  (called nodes in the DOM) you can get or set the children, parents, and text
  content of each part of the tree. You can save DOM objects to containers as
  well as write them out as text. DOM XML works best if you have a complete XML
  document available. If your XML is streaming in very slowly or you want to
  treat many different XML snippets as sections of the same document, you want
  to use SAX. Because the DOM extension builds a tree in memory, it can be
  quite the resource hog with large documents.</li>
  <li>SimpleXML: The SimpleXML API makes it easy to quickly open an XML file,
  convert some of the elements found there into native PHP types (variables,
  objects, and so on) and then operate on those native types as you would
  normally. The SimpleXML API saves you the hassle of making a lot of the extra
  calls that the SAX and DOM APIs require, uses far less memory than DOM XML,
  and often is the simplest way of accessing XML data quickly. There are
  limitations, though, including some quirky behavior related to attributes and
  deeply nested elements.</li>
</ul>

EOPAGESTR5;
echo $page_str;

site_footer();

?>