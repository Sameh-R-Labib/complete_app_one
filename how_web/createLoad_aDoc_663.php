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


site_header("Create/load a Document");

$page_str = <<<EOPAGESTR5

<p>The initial step when dealing with the DOM extension is to create or load a
document. The document is the core for XML because it serves as the root of the
tree for the DOM extension.</p>

<p>The <em>DOMDocument class</em> is the starting point for all applications
using the DOM extension. This class not only serves to create, load, and save
XML documents but also contains the factory methods for creating other node type
objects. The constructor for this object takes the following form:</p>

<pre>
__construct([string version], [string encoding])
</pre>

<p>Both the version and encoding parameters are optional and serve to indicate
the version of the XML specification used for the document and to indicate the
encoding used for the document itself. You can instantiate an empty document
using the new keyword:</p>

<pre>
&#36;dom = new DOMDocument('1.0');
</pre>

<p>This creates an empty DOMDocument object, &#36;dom, using the XML 1.0
specification and no specified encoding. This is equivalent to the following XML
declaration:</p>

<pre>
&lt;?xml version="1.0"?&gt;
</pre>

<p>The version parameter, unlike the encoding parameter, has a default value of
1.0, so this parameter could realistically have been omitted from the object
instantiation call. Likewise, an encoding value may also be passed as an
argument, such as ISO-8859-1. When using the encoding parameter, the use of the
version parameter is required. The code &#36;dom = new DOMDocument('1.0',
'ISO-8859-1'); would result in an XML declaration of &lt;?xml version="1.0"
encoding="ISO-8859-1"?&gt;.</p>

<p>In both cases, the result is the same. The object &#36;dom has been
instantiated from the DOMDocument class as an empty document. Using this object,
a tree can either be manually created using the DOM API or be loaded from an XML
document. You can load a document from a string containing the XML or from a
remote resource.</p>

<p>Using the instantiated object, &#36;dom, you can build the tree using
loadXML() to load from a string and using load() to load from a resource.
Depending upon which method you use, you need either a string containing the XML
document or a URI pointing to the resource for the first parameter. When using
PHP 5.1 and higher, both methods also accept a second optional parameter
containing any parser options (covered in Chapter 5) that provide instructions
to the parser about how the tree should be built. For example:</p>

<pre>
&#36;xmldata = '&lt;?xml version="1.0"?&gt;
&lt;root&gt;
   &lt;child&gt;contents&lt;/child&gt;
&lt;/root&gt;';

&#36;dom-&gt;loadXML(&#36;xmldata, LIBXML_NOBLANKS);
</pre>

<p>Given an already instantiated DOMDocument and the string &#36;xmldata
containing the XML document to load, the loadXML() method populates the tree
while also removing all blanks, which are the insignificant whitespaces. This
would have been the equivalent of setting &#36;xmldata to the string &lt;?xml
version="1.0"?&gt;&lt;root&gt;&lt;child&gt;contents&lt;/child&gt;&lt;/root&gt;
and loading the string without any parser options. The differences between the
two strings are the line feeds, tabs, and spaces, which are removed in the first
case because of the use of the parser option LIBXML_NOBLANKS, and their
positions within the document.</p>

<p>The load() method works in the same way as the loadXML() method, except a
URI is passed as the first parameter. As you probably recall from Chapter 5, you
use PHP streams when loading URIs, allowing for more than the typical file and
http protocols to be used. If the contents of the &#36;xmldata string from the
previous example were contained within the file xmldata.xml, you could build the
tree in the following ways depending upon where the file was located:</p>

<pre>
/* File located in current script directory */
&#36;dom-&gt;load('xmldata.xml', LIBXML_NOBLANKS);

/* File loaded using absolute path */
&#36;dom-&gt;load('file:///tmp/xmldata.xml', LIBXML_NOBLANKS);

/* File loaded from http://www.example.com/xmldata.xml */
&#36;dom-&gt;load('http://www.example.com/xmldata.xml', LIBXML_NOBLANKS);
</pre>

<p>A DOMDocument object does not always need to be instantiated to load a tree.
These methods may also be called statically, which will load data into a tree
and return the newly created DOMDocument object at the same time. The following
examples illustrate how to use the methods statically, which results in the same
tree structure for the &#36;dom objects as previously shown. (I’ve removed the
XML declaration for brevity.)</p>

<pre>
/* Load from string */
&#36;dom = DOMDocument::loadXML('&lt;root&gt;&lt;child&gt;contents&lt;/child&gt;&lt;/root&gt;');

/* Load from URI */
&#36;dom = DOMDocument::load('xmldata.xml', LIBXML_NOBLANKS);
</pre>

<p>You may be wondering why you wouldn’t always use the static methods, because
instantiating the object first requires an additional step just to load data.
The primary reason for this is when using the DOM extension under PHP 5.0, the
parser options are not available to be passed as a second argument to these
functions. A small subset of the parser options, however, is also available as
properties of a DOMDocument object. When you use these properties, you must set
them prior to calling the load functions, which require an already instantiated
object. For example, the equivalent to the LIBXML_NOBLANKS option is the
preserveWhiteSpace property:</p>

<pre>
/* Removing blanks under PHP 5.0 */
&#36;dom = new DOMDocument();
&#36;dom-&gt;preserveWhiteSpace = FALSE;
&#36;dom-&gt;load('xmldata.xml');
</pre>

<p>When you use both properties and parser options, the parser options take
precedence over the properties. This means in any instance where a property is
set and a parser option conflicting with a set property is passed, the parser
will follow the instructions from the parser option.</p>

<p>The DOMDocument class is not limited to loading just XML data. Unless you are
writing Web pages using XHTML (HTML typically does not conform to the XML
constraints), errors will result if trying to load one of these documents using
the XML load methods. Two corresponding load functions do exist, however, that
allow HTML documents to be loaded into a tree, which can then be manipulated the
same way as an XML-based tree. The methods are loadHTML() and loadHTMLFile().
Each of these methods takes exactly one parameter, either the string containing
the HTML or a URI used to locate and load the HTML. Unlike their XML
equivalents, these methods do not accept parser options as a second parameter.
For example:</p>

<pre>
/* Load the file http://www.example.com/index.html */
&#36;dom = new DOMDocument();
&#36;dom-&gt;loadHTMLFile('http://www.example.com/index.html');

/* Loading statically */
&#36;dom = DOMDocument::loadHTMLFile('http://www.example.com/index.html');
</pre>

EOPAGESTR5;
echo $page_str;

site_footer();

?>