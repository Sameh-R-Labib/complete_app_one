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


site_header("Miscellaneous Topics");

$page_str = <<<EOPAGESTR5

<h2>Common Questions, Misconceptions, and Problems</h2>

<p>The DOM specification is quite lengthy and not always easy to interpret. It
is common to expect a certain result just to find out that the actual result is
not even close to your expectation. This also holds true even with DOM
interaction within PHP. The following sections will explore many of the common
questions, misconceptions, and problems people encounter using the DOM extension
and will provide some insight into methods you can employ to achieve your
desired results.</p>

<h3>DOM Objects and PHP Sessions</h3>

<p>The most frequently encountered “problem” developers have when using the DOM
extension concerns storing DOM objects in session. Let me just say that DOM
objects cannot be natively stored in session. This doesn’t mean it is impossible
to store an XML document in session, just that some additional coding is
required to perform this action.</p>

<p>Storing data in session requires serialization. <em class="highlight">DOM
objects natively cannot be serialized using PHP functions such as serialize() or
the automatic serialization that is performed when storing data to a session
without losing data.</em> This is because of the reliance on the underlying
libxml2 library <em class="highlight">and because the DOM classes do not
implement the magic sleep() and wakeup() methods.</em> Your first reaction to
this might be the question, why aren’t those methods implemented? The answer is
simple. You have two ways to serialize a document: to a string or to a file.
Because of the size of XML documents, in many cases they are stored on the file
system rather than as a string in memory, so these specific methods were never
implemented and left to the user to handle in whatever manner they like.</p>

<div class="remarkbox">
  <div class="rbtitle">note-to-self</div>
  <div class="rbcontent">
  <p>Before I decide how I'll store documents for my sessions I need to
  figure out whether my document trees may be to large when serialized
  to store in session memory. Because if it is then I need to store to
  file insatead of memory (using the sleep/wake magic methods).</p>
  </div>
</div>

<p>Working around this is not all that difficult; in fact, you can deal with
this in a couple of ways. The first method is extremely simple and can be
performed in the same number of lines of code as you would need when storing
or fetching a DOM object from a session:</p>

<pre>
&#36;_SESSION['domobj'] = <b>&#36;dom-&gt;saveXML()</b>;
</pre>

<p>Rather than storing the DOMDocument object, &#36;dom, in session, the tree is
serialized by the<br/><em>saveXML() method</em>, which is then stored in
session. For example:</p>

<pre>
&#36;dom = <b>DOMDocument::loadXML</b>(&#36;_SESSION['domobj']);
</pre>

<p>When the DOM object needs to the restored from session, a new DOMDocument is
created from the serialized tree in session.</p>

<p>Another method you can use is to <em class="highlight">extend the DOMDocument
class and implement the </em><em>__sleep()</em> and <em>__wakeup()
methods</em>:</p>

<pre>
class customDoc extends DOMDocument() {
   private &#36;serializedDoc = NULL;

   function __sleep() {
      &#36;this-&gt;serializedDoc = &#36;this-&gt;saveXML();
      return array("serializedDoc");
   }

   function __wakeup() {
      if (! empty(&#36;this-&gt;serializedDoc)) {
         &#36;this-&gt;loadXML(&#36;this-&gt;serializedDoc);
         &#36;this-&gt;serializedDoc = NULL;
      }
   }
}
</pre>

<p>The customDoc class extends the DOMDocument class and implements the magic
methods. Once instantiated (in this case &#36;doc will be used), the object can
be easily stored and retrieved from session as a normal object:</p>

<pre>
/* Store in session */
&#36;_SESSION['domobj'] = &#36;doc;

/* Retrieve from session */
&#36;doc = &#36;_SESSION['domobj'];
</pre>

<p><em class="highlight">Using an extended class in this case allows for the
object to be serialized and stored as desired. For instance, rather than storing
the document as a string in memory, it could be saved as a file in the sleep()
method and restored during wakeup().</em></p>

<h3>Removing Nodes While Iterating Skips Nodes</h3>

<p>Another issue often arises when iterating through a DOMNodeList or
DOMNamedNodeMap and removing nodes. Nodes are often skipped during such
operation. For example, when trying to remove all children from an element, the
first thing someone may think of is to grab all children, iterate through the
DOMNodeList, and remove the node from the document. For example:</p>

<pre>
&#36;children = &#36;element-&gt;childNodes;
foreach(&#36;children as &#36;node) {
   &#36;element-&gt;removeChild(&#36;node);
}
</pre>

<p>This code does not work as expected, and child nodes are still left within
&#36;element. Both DOMNodeList and DOMNamedNodeMap are live collections.
Additions and subtractions of nodes within a tree can directly affect the nodes
contained with the collections as well as their indexes within the collection.
In the previous code snippet, once a node is removed, all nodes that follow it
within the collection automatically have their index reduced by 1. The results
of this code would end up removing every other node in the collection, starting
with the first node.</p>

<p>You can work around this issue by removing nodes in reverse order or
performing a loop while &#36;element still has children:</p>

<pre>
/* Removal Based on Index */
&#36;length = &#36;children-&gt;length;
for(&#36;x=&#36;length-1; &#36;x &gt;= 0; &#36;x--) {
   &#36;element-&gt;removeChild(&#36;children-&gt;item(&#36;x));
}

/* Removal based on children */
while (&#36;element-&gt;hasChildNodes()) {
   &#36;element-&gt;removeChild(&#36;element-&gt;firstChild);
}
</pre>

<p>You can use many different techniques to do this. The first method
illustrated shows how you can perform the iteration without regard to the type
of node within the collection. It is possible that you have to change the actual
code used for removal, because the code used here is specific to removing child
nodes. The second example performs the same task as the first example, but
instead no collections are used. As long as &#36;element has children, the loop
will be processed and continue to remove the first child of &#36;element.</p>

<h3>The XML Tree Contains Garbled Characters</h3>

<p>No matter how much encodings are stressed, people often forget that data is
internally stored in UTF-8 encoding. Other than during the loading and saving of
an XML document, data that is not compatible with UTF-8 must be encoded or
decoded when accessing or modifying content. Chapter 5 explains this in detail
as well as covers the methods you can employ to handle data correctly when
interacting with the XML-based extensions in PHP 5.</p>

<h3>Extended Class Not Being Returned When Accessing Node</h3>

<p>This has to be the most often encountered issue when using extended classes,
which is why it is mentioned here even though it has already been covered in
this chapter. If you run into this issue, refer to the “Object Lifetime and
Scope Within the Extending Classes” section for an in-depth examination of the
topic.</p>

<h3>Unable to Retrieve Elements by ID</h3>

<p>The <em>method getElementById()</em> will return NULL when an element with
the specific <em>ID</em> is not found. Even though you might think the ID is
valid in the document and that a DOMElement should be returned, a common
misconception may result in NULL being returned.</p>

<p>Attributes for elements are using the name ID but are not recognized as ID
attributes. The name ID is not special in XML. Just because an attribute uses
this name does not automatically turn it into an ID attribute. To create IDs,
you must define attributes in a DTD as IDs. The DTD, if external, must also be
processed while loading the document. Once the DTD has been loaded and the
document processed, elements will then be able to be accessed by their IDs.</p>

<p>In a couple of special cases, this does not hold true. The qualified 
attribute name <em>xml:id</em> is one of these cases. Attributes with this name
are handled as ID attributes and do not require a DTD. Currently, these
attributes are recognized and set up as IDs only when a document is loaded. Work
is taking place within the libxml2 library to support appending attributes with
this name that also automatically result in IDs, but as of libxml2 2.6.20, this
has yet to be implemented.</p>

<p>Another special case is the <em>setIdAttribute() methods</em>.
<em class="highlight">These methods have not yet been implemented at the time of
writing but are on the to-do list for the DOM extension so may or may not be
available by the time you read this.</em> These methods will allow already
existing attributes to be set and unset as ID attributes without needing a DTD
or schema.</p>

<h3>Loading Document Issues Entity Errors</h3>

<p>By definition, an XML document must be well-formed. Entity errors and
warnings are issued when a document uses entity references and the entities are
not defined in a DTD. The most common problem encountered deals with the use of
the &amp; character:</p>

<pre>
&lt;root&gt;this &amp; that&lt;/root&gt;
</pre>

<p>This, contrary to what many believe, is not a legal XML document. Unless
&amp; is contained within a CDATA section, it cannot be used alone for text
content. Within text content, it must be escaped and can be written as this:</p>

<pre>
&lt;root&gt;this &amp;amp; that &lt;/root&gt;
</pre>

<p>When trying to load a document containing a stand-alone &amp; within text
content, you have two options. You can either convert it to the appropriate
entity reference or completely disregard the document. The problem with the
latter is that for some reason, this issue gets reported as a bug because the
document being loaded is coming from a remote source, such as an RSS feed. In a
case like that, your best bet is to contact the owner of the document and let
them know their XML is not legal.</p>

<h3>Added DTD Not Recognized</h3>

<p>A DTD manually added to a document using append and insert operations is not
handled by the document as a regular DTD. DTDs are parsed and set up
appropriately while a document is being parsed. Adding one later, unless
creating the DTD and document using the DOMImplementation methods, requires the
document to be serialized and reloaded in order for the DTD to be read
correctly.</p>

<h3>Unable to Access Elements in Default Namespace Using XPath</h3>

<p>One of the biggest issues encountered when using XPath concerns selecting
elements in the default namespace. No prefixes are associated with the
namespace, and the elements cannot be selected just by using their names.
Although you can hack together an expression to get access to these elements,
the easiest method is to manually register the default namespace with some
prefix. This then allows you to call the elements using the newly associated
prefix and element name. Refer to the “Using XPath and Namespaces” section in
this chapter for additional information.</p>

EOPAGESTR5;
echo $page_str;

site_footer();

?>