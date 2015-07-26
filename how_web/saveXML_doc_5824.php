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


site_header("Save a Document");

$page_str = <<<EOPAGESTR5

<div class="remarkbox">
  <div class="rbtitle">note-to-self</div>
  <div class="rbcontent">
  <p>Here, when the author says "a document containing a tree", he means
  "a DOMDocument object which contains your XML data."</p>
  </div>
</div>

<p>Now that you have a document containing a tree, you will see how to output
the contents of the tree. The output may be as a string or to a URI, such as a
file. The methods are similar to those used to load the data. To output as XML,
you’ll use the function saveXML() to output the contents to a string and the
function save() to output to a URI.</p>

<p>The saveXML() method accepts one optional node parameter. The node parameter
must be an object derived from the DOMNode class and must be from the same
document as the DOMDocument object from which the method is being called. When
this parameter is not present, the entire document is serialized to a string.
Using the &#36;dom object created when loading a document with the
LIBXML_NOBLANKS option, you can serialize the document. For example:</p>

<pre>
&#36;output = &#36;dom-&gt;saveXML();
</pre>

<p>This would set &#36;output to a string containing
&lt;root&gt;&lt;child&gt;contents&lt;/child&gt;&lt;/root&gt;. If a DOMElement
object existed called &#36;child that represented the element child in the
document, this object could be passed as a parameter to the method to output
just the element. For example:</p>

<pre>
&#36;output = &#36;dom-&gt;saveXML(&#36;child);
</pre>

<p>This would result in the string &lt;child&gt;contents&lt;/child&gt;.</p>

<p>The save() method also accepts a single parameter. This parameter sets the
URI to which the document is to be serialized. The return value for this method
is the number of bytes written to the URI. Unlike the saveXML method, a single
node cannot be serialized to a URI:</p>

<pre>
&#36;bytes = &#36;dom-&gt;save('output.xml);
</pre>

<p>This snippet of code saves the document to the file output.xml and returns
the number of bytes written to the variable &#36;bytes. Running this code, you
might be surprised to see &#36;bytes equal to 58. Whether a document was loaded
with an XML declaration or the version and encoding parameters were passed when
creating a document, an XML declaration is present when serializing the document
with at least the version parameter, defaulting to 1.0, set.</p>

<p><em class="highlight">Documents manually created or loaded with the
LIBXML_NOBLANKS option typically do not contain text nodes containing
whitespace. When serialized, the output generated is not easily human readable
because the output is all strung together. You can use the</em>
<em>formatProperty</em> <em class="highlight">on the DOMDocument class to
“prettify” the output.</em> Setting this property to TRUE prior to serialization
causes the parser to add line feeds and indentations where appropriate. For
example:</p>

<pre>
&#36;dom-&gt;formatOutput = TRUE;
print &#36;dom-&gt;saveXML();
</pre>

<p>This code results in the following output:</p>

<pre>
&lt;?xml version="1.0"?&gt;
&lt;root&gt;
  &lt;child&gt;content&lt;/child&gt;
&lt;/root&gt;
</pre>

<p>Just as with the load functionality, you can also save a document in HTML
format. The methods saveHTML() and saveHTMLFile() perform this operation. The
method saveHTML() takes no parameters and returns the output as a string. The
saveHTMLFile() method takes a single parameter, the URI, and returns the number
of bytes written. The output is normally not XML-compliant because it is true
HTML and not XHTML. Assuming the object &#36;htmldoc contains a tree to be
serialized into HTML, the following examples illustrate how to use the methods
to serialize HTML:</p>

<pre>
/* Serialize document to a string in HTML format */
&#36;html = &#36;htmldoc-&gt;saveHTML();

/* Serialize document to file index.html in HTML format */
&#36;bytes = &#36;htmldoc-&gt;saveHTMLFile('index.html');
</pre>

<p>You have spent much time examining the simple operations of instantiating,
loading, and saving DOMDocument objects. Understanding the basic operations of
the DOMDocument class is important because this class serves as the foundation
for all operations within the DOM extension. Nearly everything in the DOM
extension is derived from and associated with a document, as you will further
examine when exploring the other aspects of the DOM extension throughout this
chapter. With these basic concepts of the DOMDocument behind you, you can learn
about navigating an existing tree.</p>

EOPAGESTR5;
echo $page_str;

site_footer();

?>