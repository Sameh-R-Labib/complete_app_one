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


site_header("Navigating the Tree");

$page_str = <<<EOPAGESTR5

<p>Compared to other tree-based parsers (in PHP 5, the SimpleXML extension is
the only other native tree-based extension), one of the DOM extension’s
strengths is its rich navigation support. DocBook is a system for writing
documentation in XML
format. I will use the example document in Listing 6-1 throughout the following
sections to illustrate how to navigate a document tree.</p>

<p>Listing 6-1. Example Document Using DocBook Format</p>

<pre>
&lt;?xml version="1.0" encoding="UTF-8"?&gt;
&lt;!DOCTYPE book PUBLIC "-//OASIS//DTD DocBook XML V4.1.2//EN"
                      "http://www.oasis-open.org/docbook/xml/4.1.2/docbookx.dtd"&gt;
&lt;book lang="en&gt;
   &lt;bookinfo&gt;
      &lt;title&gt;DOM in PHP 5&lt;/title&gt;
      &lt;author&gt;
         &lt;firstname&gt;Rob&lt;/firstname&gt;
         &lt;surname&gt;Richards&lt;/surname&gt;
      &lt;/author&gt;
      &lt;copyright&gt;
         &lt;year&gt;2005&lt;/year&gt;
         &lt;holder&gt;Rob Richards&lt;/holder&gt;
      &lt;/copyright&gt;
   &lt;/bookinfo&gt;
   &lt;preface&gt;
      &lt;title&gt;The DOM Tree&lt;/title&gt;
      &lt;para&gt;An example DOM Tree using DocBook.&lt;/para&gt;
   &lt;/preface&gt;
   &lt;chapter id="navigation"&gt;
      &lt;title&gt;Navigating The Tree&lt;/title&gt;
      &lt;para&gt;The document element is accessed from the
 &lt;emphasis&gt;documentElement&lt;/emphasis&gt; property, which is available from any class
 derived from DOMNode&lt;/para&gt;
      &lt;para&gt;The document node is also accessible using the
 &lt;emphasis&gt;ownerDocument&lt;/emphasis&gt; property, also derived from the DOMNode
 class.&lt;/para&gt;
   &lt;/chapter&gt;
&lt;/book&gt;
</pre>

<p>This first step you need to take is to load the document into a DOMDocument
object. I will show how to load the document in Listing 6-1 from the file
mydocbook.xml. For now, the document will be loaded with the default options.
This means the DTD is not loaded and the id attribute within the document is a
regular attribute and not an ID type. For example:</p>

<pre>
&#36;dom = new DOMDocument();
&#36;dom-&gt;load('mydocbook.xml');
</pre>

<div>
  <img src="domobview.jpg" width="458" height="551" alt="pic of example
  tree" />
</div>

<h3>Understanding the Document Element</h3>

<p>The <em>document element</em>, like the document node, is a focal point in an
XML document. Being the root of the body for the document, it is a node with a
fixed position—the entry point for the body and universally accessible. Objects
derived from the DOMNode class are able to access the <em>documentElement
property</em>, which returns the document element as a DOMElement to also
navigate back to the document element.</p>

<div class="remarkbox">
  <div class="rbtitle">clarification</div>
  <div class="rbcontent">
  <p>There are two things which are unique to a document. One is its
  <em>document object</em>; the other is its <em>document element object</em>.
  It has only one of each!</p>
  </div>
</div>

<p>The document element from Listing 6-1 is the book element. Using the
DOMDocument object, &#36;dom, you can retrieve the book element with the
documentElement property:</p>

<pre>
&#36;root = &#36;dom-&gt;documentElement;
</pre>

<p>This call returns a DOMElement object, which is the book element node, and
sets it to the variable &#36;root. Armed with the document element, you can now
explore the rest of the body.</p>

<h3>Accessing Basic Node Information</h3>

<p>Before going too much further, it is useful to take a brief look at how to
access basic node information. In some cases, the return
value may even be NULL when the called property is not applicable for the
node.</p>

<h4>Node Type</h4>

<div class="remarkbox">
  <div class="rbtitle">note-to-self</div>
  <div class="rbcontent">
  <p>I have a list of the constant <em>names</em> on the "Node Types" page;
  however, I don't have the numeric values for these constants. These
  numeric values should'nt matter since I'll be refering to them by name.</p>
  </div>
</div>

<pre>
&#36;type = &#36;root-&gt;nodeType;
print &#36;type;
</pre>

<p>This code prints the number 1, which corresponds to the XML_ELEMENT_NODE
constant. You can find the complete list of node type constants in Appendix B,
and in a moment you will be introduced to a few more.</p>

<h4>Node Name</h4>

<p>The name of a node is generally applicable to element and attribute
nodes. All nodes have names, but unlike elements and attributes that
actually have specific names, most other nodes have generalized names
corresponding to the type of node. The property used to access the node
name is <em>nodeName</em>:</p>

<pre>
print &#36;dom-&gt;nodeName."&#92;n";
print &#36;root-&gt;nodeName."&#92;n";
</pre>

<p>This code illustrates the difference of the node name for a document
node and an element node. The document node, &#36;dom, returns the value
#document. The element node, &#36;root, on the other hand, returns the
tag name for the element, book. If this returned the node name of a text
node, the value would be #text. As you can see, the node name for the
text node is nondescriptive and offers no additional information that
could have just as easily been obtained from the node type.</p>

<h4>Node Value</h4>

<p>The property <em>nodeValue</em> offers access to the contents of certain
nodes. <em class="highlight">Nodes having values are attributes, CDATA
sections, comments, PIs, and text. This is according to the specification. For
convenience, the DOM implementation in PHP 5 allows you to access this
property by element node as well:</em></p>

<pre>
print &#36;dom-&gt;nodeValue."&#92;n";
print &#36;root-&gt;nodeValue."&#92;n";
</pre>

<p>In the first call, the node value for the document node is accessed. The
property is not valid for document nodes, and NULL is returned with only a
line feed printed. The output is a bit long, but the
abbreviated output looks like the following:</p>

<pre>
                DOM in PHP 5

                        Rob
                        Richards



                        2005
/* Rest of Output Omitted for Brevity */
</pre>

<p>When the document was initially loaded, whitespaces were not removed from
the document. These whitespaces, <em class="highlight">being text nodes</em>,
are also concatenated and included as part of the output, resulting in the
previous formatting.</p>

<h3>Moving Within the Tree</h3>

<p>At this point, you are still situated on the document element with the
&#36;root object. You can navigate to most other node types by accessing
children. <em class="highlight">Attribute nodes are an exception to this.
These are treated as properties of element nodes</em>, which will be covered
in the “Accessing Attributes” section. Movement, however, is not restricted
to descending into the tree. As you will see, accessing siblings, accessing
parents, and even directly accessing the document node are all possible.</p>

<h4>Accessing Children</h4>

<p>Child nodes are those that are direct descendants of the current node.
Simply put, Document nodes can contain comment nodes, PIs, a
document type, and a single element node as children.
<em class="highlight">The type of children possible depends upon the type of
the current node.</em> You can perform a quick check to see whether a node has
child nodes with the <em>hasChildNodes() method</em>, which returns a Boolean
indicating whether child nodes are present on the current node.</p>

<div class="remarkbox">
  <div class="rbtitle">weirdness-alert</div>
  <div class="rbcontent">
  <p>DOMNodeList objects are special. For standard objects you
  access data using methods or properties. Here it's different. You can
  iterate through the object itself as if it was an array of DOMNode
  objects! Otherwise, you can use its DOMNodelist::item method.</p>
  </div>
</div>

<div class="remarkbox">
  <div class="rbtitle">extra-info</div>
  <div class="rbcontent">
  <p>DOMNodelist::item() is a method. Use it as such!</p>
  <p>DOMNode DOMNodelist::item ( int &#36;index )</p>
  </div>
</div>

<p>All child nodes can be returned as a <em>DOMNodeList</em> using the
<em>childNodes property</em>. <em class="highlight">An object of the
DOMNodeList class is an iterable object;</em> you can access the child nodes
using the <em>DOMNodeList::item method</em> to retrieve a specific node from
the list or even the iterator functions in PHP, such as foreach:</p>

<pre>
if (&#36;root-&gt;hasChildNodes()) {
   &#36;children = &#36;root-&gt;childNodes;
   foreach(&#36;children as &#36;node) {
      print &#36;node-&gt;nodeName."&#92;n";
   }
}
</pre>

<p>This code retrieves the children of the document element, iterating through
the resulting DOMNodeList object using foreach, and prints the name of each
node. The output from this is as follows:</p>

<pre>
foreach(&#36;children as &#36;node) {
   if (&#36;node-&gt;nodeType != XML_TEXT_NODE) {
      print &#36;node-&gt;nodeName."&#92;n";
   }
}
</pre>

<p>Here the text nodes have been skipped, resulting in the following
output:</p>

<pre>
bookinfo
preface
chapter
</pre>

<p>You can also access a subtree directly using the <em>firstChild</em> and
<em>lastChild</em> properties. Rather than having to retrieve the entire
collection of children, these properties are quick ways to access the start or
end of the subtree:</p>

<pre>
&#36;first = &#36;root-&gt;firstChild;
&#36;last = &#36;root-&gt;lastChild;
</pre>

<p>The variable &#36;first contains the DOMText object that is the first child
beneath the book element and prior to the bookinfo element. which you will
learn about now.</p>

<h4>Accessing Siblings</h4>

<p>Sibling nodes are those residing on the same level as the current node. For
example, all nodes within the &#36;children DOMNodeList object are siblings of
each other. They all live on the same level and have the same parent. You move
laterally within a subtree using the <em>nextSibling</em> and
<em>previousSibling</em> properties.</p>

<p>Using the &#36;first object created in the previous section, you can access
the sibling nodes using the nextSibling property:</p>

<pre>
&#36;node = &#36;first;
while(&#36;node) {
   if (&#36;node-&gt;nodeType == XML_ELEMENT_NODE) {
      print &#36;node-&gt;nodeName."&#92;n";
   }
   &#36;node = &#36;node-&gt;nextSibling;
}
</pre>

<p>This gives you the same results as when iterating &#36;children and
printing only element tag names:</p>

<pre>
bookinfo
preface
chapter
</pre>

<p>The previousSibling property allows navigation to be performed in
reverse:</p>

<pre>
&#36;node = &#36;last;
while(&#36;node) {
   if (&#36;node-&gt;nodeType == XML_ELEMENT_NODE) {
      print &#36;node-&gt;nodeName."&#92;n";
   } &#36;node = &#36;node-&gt;previousSibling;
}
</pre>

<p>The output this time is as follows:</p>

<pre>
chapter
preface
bookinfo
</pre>

<h4>Accessing Specific Elements</h4>

<p>You can also access specific elements by tag names. When you need to access
specific elements <em class="highlight">within the scope of the current
node</em>, you can use the methods <em>getElementsByTagName()</em> and
<em>getElementsByTagNameNS()</em>. Element nodes can be
contained only within document nodes and element nodes; thus, these methods are
available only when the current node is based on a DOMDocument or DOMElement
class. For example, from the document node, &#36;dom, you can retrieve
<em class="highlight">all title elements within the document</em> using the
getElementsByTagName() method:</p>

<pre>
&#36;elements = &#36;dom-&gt;getElementsByTagName("title");
&#36;length = &#36;elements-&gt;length;
for (&#36;x=0;&#36;x &lt; &#36;length;&#36;x++) {
   print "Element Value: ".&#36;elements-&gt;item(&#36;x)-&gt;nodeValue."&#92;n";
}
</pre>

<p>This code retrieves a DOMNodeList object, &#36;elements, containing all title
elements <em class="highlight">within the scope of the document node</em>,
&#36;dom. Being the document node, this returns all elements named title within
the entire document. <em>length</em> is
the total number of elements, and the collection uses a zero-based index, so no
(available in PHP 5), the element at the current index, &#36;x, is retrieved,
The output from this operation is as follows:</p>

<pre>
Element Value: DOM in PHP 5
Element Value: The DOM Tree
Element Value: Navigating The Tree
</pre>

<p><em class="highlight">You can pass the special value * for the tag name
argument.</em> This is a wildcard used to match any element name. For
example:</p>

<pre>
&#36;preface = &#36;root-&gt;getElementsByTagName("preface");
&#36;elements = &#36;preface-&gt;item(0)-&gt;getElementsByTagName("*");
&#36;length = &#36;elements-&gt;length;
for (&#36;x=0;&#36;x &lt; &#36;length;&#36;x++) {
   print "Element Name: ".&#36;elements-&gt;item(&#36;x)-&gt;nodeName."&#92;n";
   print "Element Value: ".&#36;elements-&gt;item(&#36;x)-&gt;nodeValue."&#92;n";
}
</pre>

<p>From the document element, &#36;root, all preface elements within its scope
are retrieved as a DOMNodeList object, &#36;preface. No length test is
performed, because you already know that an element exists in the document.
Again, dereferencing is used; the first element in the DOMNodeList is retrieved,
and immediately in the same line of code, <em>getElementsByTageName("*")</em>
is called on the node. All elements within the scope of the preface element are
returned and set to the &#36;elements variable. You can access this collection
the same way as before: The resulting output is as follows:</p>

<pre>
Element Name: title
Element Value: The DOM Tree
Element Name: para
Element Value: An example DOM Tree using DocBook.
</pre>

<p>When working with namespaced documents, the <em>getElementsByTagNameNS()
method</em>
allows elements in specified namespaces to be returned. The example document in
this chapter does not contain namespaces, so I cannot give a specific example at
this time. Just like the name parameter, the namespace URI parameter also
accepts the * wildcard. For example:</p>

<pre>
&#36;result = &#36;dom-&gt;getElementsByTagNameNS("*", "*");
</pre>

<p>The resulting DOMNodeList, &#36;result, will contain every element in the
document that is within any namespace.</p>

<h4>Accessing Attributes</h4>

<p>Attributes inherit the same methods and properties from the DOMNode class as
other node types, but they are not accessed in the same manner as other nodes in
a document. As you have seen so far, nodes are traversed through children of
nodes.</p>

<h5>Individual Attributes</h5>

<p>Attributes do not have to be accessed through a DOMNamedNodeMap. The
DOMElement class offers attribute-specific methods that you can use to access
specific attributes. The method used depends upon whether just the value of the
attribute or the entire attribute node needs to be returned. It also depends
upon whether namespaces are in use. You can access attributes using the
<em>getAttribute()</em>, <em>getAttributeNode()</em>, <em>getAttributeNS()</em>,
and <em>getAttributeNodeNS()</em> methods. For example:</p>

<pre>
/* Access lang attribute value directly */
print "Attribute Value: ".&#36;root-&gt;getAttribute("lang")."&#92;n";

/* Return the lang attribute node and access the returned attribute node */
&#36;attr = &#36;root-&gt;getAttributeNode("lang");
print "Attribute Value: ".&#36;attr-&gt;nodeValue."&#92;n";
</pre>

<p>If the attributes were associated
with a namespace, the results from the methods would be empty unless the
appropriate namespace URI were passed as the first parameter.</p>

EOPAGESTR5;
echo $page_str;

site_footer();

?>