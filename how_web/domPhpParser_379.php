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


site_header("DOM PHP Parser");

$page_str = <<<EOPAGESTR5

<div class="remarkbox">
  <div class="rbtitle">Depricated</div>
  <div class="rbcontent">
  <p>Don't learn the functions from this page. These are functions that
  were used pre PHP 5.0 when DOM was called domxml. Instead read the pages
  from the XML book which cover the DOM extension.</p>
  </div>
</div>

<p>The Document Object Model is a complete API for creating, editing, and
parsing XML documents. The DOM is a recommendation of the World Wide Web
Consortium. You can read all about it in the W3’s inimitable prose at
www.w3.org/DOM/.</p>

<p>Basically the idea is that every XML document can be viewed as a hierarchy
of nodes resembling leaves on a tree. Starting with the root element, of which
all other elements can be expressed as children, any program should be able to
build a representation of the structure of a document. Attributes and character
data can also be attached to elements. This tree can be read into memory from
an XML file, manipulated by PHP, and written out to another XML file or stored
in a container.</p>

<p>The parser behind the scenes in PHP’s DOM extension is gnome-libxml2 (aka
Gnome libxml2), which is supposedly less memory-intensive than others. This is
available at www.xmlsoft.org.</p>

<p>DOM XML is the only entirely object-oriented API in PHP, so some familiarity
with objectoriented programming helps when using it. However, there are a
limited number of objects and methods, so you do not need any particularly deep
knowledge of object-oriented programming to use DOM XML.</p>

<h2>Using DOM XML</h2>

<p>How you use the DOM will depend on your goals, but these steps are
common:</p>

<ol>
  <li>Open a new DOM XML document, or read one into memory.</li>
  <li>Manipulate the document by nodes.</li>
  <li>Write out the resulting XML into a string or file. This also frees the
  memory used by the parser.</li>
</ol>

<p>Listing 40-6: A simple DOM XML example (dom_example.php)</p>

<pre>
&lt;?php
&#36;doc = new DomDocument("1.0");
&#36;root = &#36;doc-&gt;createElement("HTML");
&#36;root = &#36;doc-&gt;appendChild(&#36;root);
&#36;body = &#36;doc-&gt;createElement("BODY");
&#36;body = &#36;root-&gt;appendChild(&#36;body);
&#36;body-&gt;setAttribute("bgcolor", "#87CEEB");
&#36;graff = &#36;doc-&gt;createElement("P");
&#36;graff = &#36;body-&gt;appendChild(&#36;graff);
&#36;text = &#36;doc-&gt;createTextNode("This is some text.");
&#36;text = &#36;graff-&gt;appendChild(&#36;text);
&#36;doc-&gt;save("test_dom.xml");
?&gt;
</pre>

<h2>DOM functions</h2>

<p>Table 40-1 lists the most common DOM functions. You must call one of these
functions before you can use any of the other DOM XML functions!</p>

<table class="events" width="678">
  <caption>Table 40-1: DOM XML Top-Level Function Summary</caption>
  <tr>
    <th>Function</th>
    <th>Behavior</th>
  </tr>
  <tr>
    <td>domxml_open_mem(string)</td>
    <td>Takes a string containing an XML document as an argument. This function
    parses the document and creates a Document object.</td>
  </tr>
  <tr>
    <td>domxml_open_file(filename)</td>
    <td>Takes a string containing an XML file as an argument. This function
    parses the file and creates a Document object.</td>
  </tr>
  <tr>
    <td>domxml_xmltree(string)</td>
    <td>Takes a string containing an XML document as an argument. Creates a
    tree of PHP objects and returns a DOM object. Note: The object tree
    returned by this function is read-only.</td>
  </tr>
  <tr>
    <td>domxml_new_doc(version)</td>
    <td>Creates a new, empty XML document in memory. Returns a
    Document object.</td>
  </tr>
</table>

<p>Table 40-2 lists the most important classes of the DOM API.</p>

<table class="events" width="678">
  <caption>Table 40-2: XML DOM Class Summary</caption>
  <tr>
    <th>Class</th>
    <th>Behavior</th>
  </tr>
  <tr>
    <td>DomDocument</td>
    <td>This class encapsulates an XML document. It contains the root element
    and a DTD if any.</td>
  </tr>
  <tr>
    <td>DomNode</td>
    <td>Encapsulates a node, aka an element. A node can be the root element or
    any element within it. Nodes can contain other nodes, character data, and
    attributes.</td>
  </tr>
  <tr>
    <td>DomAttr</td>
    <td>This class encapsulates a node attribute. An attribute is a
    user-defined quality of the node.</td>
  </tr>
</table>

<p>Table 40-3 lists the most important methods of the DomDocument class.</p>

<table class="events" width="678">
  <caption>Table 40-3: DomDocument Class Summary</caption>
  <tr>
    <th>Method</th>
    <th>Behavior</th>
  </tr>
  <tr>
    <td>createElement(name)</td>
    <td>Creates a new element whose tag is the passed string. You must append
    this element to another element using DomNode-&gt;appendChild().</td>
  </tr>
  <tr>
    <td>createTextNode(character_data)</td>
    <td>Creates a new text node (DomText object). You must append this node to
    another node using DomNode-&gt;appendChild().</td>
  </tr>
  <tr>
    <td>save(filename)</td>
    <td>Dumps XML from memory to a designated file.</td>
  </tr>
  <tr>
    <td>saveXML([node])</td>
    <td>Dumps XML from memory to a string. Optional parameter is
    a DomNode object.</td>
  </tr>
</table>

<p>Table 40-4 lists the most important methods of the DomNode class.</p>

<table class="events" width="678">
  <caption>Table 40-4: DomNode Class Summary</caption>
  <tr>
    <th>Method</th>
    <th>Behavior</th>
  </tr>
  <tr>
    <td>appendChild(newnode)</td>
    <td>Attaches a node to another node.</td>
  </tr>
  <tr>
    <td>removeChild(child)</td>
    <td>Removes the child node.</td>
  </tr>
</table>

<p>Table 40-5 lists the most important methods of the DomAttr class.</p>

<table class="events" width="678">
  <caption>Table 40-5: DomAttr Class Summary</caption>
  <tr>
    <th>Method</th>
    <th>Behavior</th>
  </tr>
  <tr>
    <td>name()</td>
    <td>Returns an attribute name.</td>
  </tr>
  <tr>
    <td>value()</td>
    <td>Returns the value of an attribute.</td>
  </tr>
</table>

EOPAGESTR5;
echo $page_str;

site_footer();

?>