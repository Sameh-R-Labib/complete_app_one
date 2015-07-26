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


site_header("Performing Validation");

$page_str = <<<EOPAGESTR5

<p>Chapter 3 covered three methods of validating XML documents. You can use each
of these methods with the DOM extension to perform validation. As shown in the
previous chapter, you can invoke and perform validation using DTDs during
parsing by using the <em>LIBXML_DTDVALID constant</em> with either of the load
options. It is not always the case that a document would need to be validated at
the time of being parsed, and the bigger issue is that only DTDs can currently
be used, leaving XML Schemas and RELAX NG unaccounted for. The DOMDocument class
implements the accessor methods to perform validation after an XML document has
been loaded.</p>

<h2>Validating with DTDs</h2>

<p>You must load DTDs prior to trying to validate against them within the DOM
extension. Loading a document with the LIBXML_DTDLOAD parser option will load an
external DTD but not perform validation at parse time. With a DOMDocument object
instantiated and containing a loaded DTD, validation is as simple as calling the
<em>validate() method</em>.</p>

<p>This method returns TRUE or FALSE, indicating the validity state of the
document. Errors and warnings from libxml can be issued from this method call
and should be handled appropriately, either by using a user error handler,
allowing the printing of the errors; by using error suppression; or by using the
new error handling available in PHP 5.1.</p>

<pre>
&#36;dom = DOMDocument::loadXML('&lt;?xml version="1.0"?&gt;
&lt;!DOCTYPE courses [
   &lt;!ELEMENT courses (course+)&gt;
   &lt;!ELEMENT course (title)&gt;
   &lt;!ELEMENT title (#PCDATA)&gt;
]&gt;
&lt;courses&gt;
   &lt;course&gt;
      &lt;title&gt;Algebra&lt;/title&gt;
   &lt;/course&gt;
&lt;/courses&gt;');
</pre>

<p>The variable &#36;dom, after running this code, is a DOMDocument object
containing an internal subset. <em class="highlight">Internal subsets do not
require any parameters instructing a DTD to be loaded because they are
internal.</em> It has not been validated, because the parser was not instructed
to validate it. At this point, you may want to find out whether the document is
valid, and you can easily do this with the validate() method:</p>

<pre>
&#36;isvalid = &#36;dom-&gt;validate();
var_dump(&#36;isvalid);
</pre>

<p>The result of this is bool(true), which indicates the document is valid.</p>

<p>It becomes more difficult when building a document manually containing a DTD
and performing validation. Internal subsets cannot be created with the DOM
extension manually. <em class="highlight">You can create external subsets using
methods from the DOMImplementation class, but these still are not loaded into
memory.</em> In these instances, a document should be serialized, reloaded, and
then validated in order for validation to work properly.</p>

<div class="remarkbox">
  <div class="rbtitle">note-to-self</div>
  <div class="rbcontent">
  <p>The chapter also has sections for "Validating with XML Schemas" and
  "Validating with RELAX NG"; however, I'm not including them here at this
  time.</p>
  </div>
</div>

EOPAGESTR5;
echo $page_str;

site_footer();

?>