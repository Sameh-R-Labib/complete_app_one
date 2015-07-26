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


site_header("Working with XML (in PHP)");

$page_str = <<<EOPAGESTR5

<p>By now you may or may not think XML is the greatest thing since cinnamon
toast, but in either case you’re probably asking yourself, “OK, but what can I
actually do with it?” This is actually not such an easy question to answer. In
theory, you can do three main things with XML: manipulate and store data;
pass data around between software applications or between organizations; and
display XML pages in a browser or other application using style sheets to apply
display directives.</p>

<p>In practice, almost no one actually uses XML as a primary data store when
SQL is so ubiquitous. It’s possible, although still difficult, to manipulate
data using XML — for instance, to edit documents by creating and manipulating
XML nodes rather than straight text — but again many users don’t see a
tremendous amount of extra value to this practice. A great deal of progress has
been made in displaying XML in the browser, generally in the form of XHTML, in
the last couple of years, but there are still significant issues with this
practice. For more information about displaying XML, see the sidebar “The
Promises and Pitfalls of Displaying XML.”</p>

<p>This leaves one main job for XML right now: exchanging data between
applications and organizations. This happens to be the area in which PHP can
have the most immediate impact. For instance, a C program might perform some
operations on data from a data store and then output the results in XML, which
PHP could transform into HTML for display in a browser or other
application.</p>

<div class="remarkbox">
  <div class="rbtitle">The Promises and Pitfalls of Displaying XML</div>
  <div class="rbcontent">
  <p>XML attempts to do something that HTML has only very imperfectly
  accomplished: enforce real separation between content and display. XML tags
  contain no display-oriented meaning whatsoever — so an element called
  &lt;header&gt;&lt;/header&gt; in XML does not imply anything about large bold
  text, and, we hope, never will. All display information will be applied
  through style sheets. These can be either Cascading Style Sheets, which are
  already familiar to many HTML developers, or XSL (eXtensible Style Language),
  which is the next-generation style sheet.</p>
  <p>A single XML document will, in theory, be displayable in any number of
  ways simply by applying a different style sheet. The promise is that you will
  be able to take an XML document and, by simply swapping in various XSL
  templates, be able to create a version of the page for very large screens, a
  version for cellular phones, a version for the visually handicapped, a
  version with certain lines highlighted in red, and so forth.</p>
  <p>The reality of the situation right now is not that rosy. The XSL standard
  is still notoriously shaky, and it seems to be resisting wide adoption.
  Cascading Style Sheets have been around since 1997 and browser support for
  them remains so problematic that most major Web sites still use font
  tags—indicating that XSL has quite a way to go before it gains wide
  acceptance. It’s a perfect example of the truism that “worse is better” —
  people have been complaining about HTML’s limitations almost since it was
  invented; but a technology which is better yet harder to implement, like XML,
  might not have so quickly acquired such a large user base.</p>
  <p>In the meantime, XML must be transformed into HTML on the server side. It
  is possible to do this using XSL itself, but so far relatively few sites have
  chosen this option. Among other discouraging factors, XSL transformations can
  only result in HTML that still meets the requirements for XML
  well-formedness, also known as XHTML. It’s far more common at this point to
  use some other program, such as PHP, to translate the XML into HTML.</p>
  </div>
</div>

<p>This data flow actually makes sense if substantial amounts of computation
need to happen behind the scenes, because you do not want to have a big program
both performing complex operations and outputting HTML if you can possibly help
it.</p>

<p>PHP can also read in data from a data store and write XML documents itself.
This can be helpful when transferring content from one Web site to another,
as in syndicating news stories. You can also use this functionality to help
non-technical users produce well-formed XML documents with a Web-form front
end. At the moment, writing XML might well be the most common category of
XML-related PHP task.</p>

<p>Finally, data is beginning to be manipulated and exchanged across human and
nonhuman endpoints via the Internet itself. This technology is called Web
services, and it is the subject of Chapter 41.</p>

EOPAGESTR5;
echo $page_str;

site_footer();

?>