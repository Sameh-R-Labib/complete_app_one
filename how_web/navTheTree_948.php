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
strengths is its rich navigation support. This strength can also be a weakness
because this rich support results in a large number of methods and properties;
this leads to a large API to learn and understand. The document in Listing
6-1 is in DocBook format. DocBook is a system for writing documentation in XML
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

<p>Navigation all begins with a <em>DOMDocument object</em>. These objects have
no attributes; they have only child nodes. At a minimum, all XML documents must
have a document element, but as mentioned in previous chapters, a document can
also have a DTD and any number of comment and PI nodes. You can access these
nodes using any of the many child properties and methods available from the base
DOMNode class. The body of the document is the most commonly accessed and
modified portion of the tree. Before examining how to access child nodes, which
will be covered later in the “Moving Within the Tree” section, you will first
see how to easily access the body.</p>

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
access basic node information. Three of the most basic pieces of information
often used within the DOM extension are the type of node, the name of the node,
and the value of the node. Knowing the structure of a document is not a
requirement when using the DOM extension, so many times you will need these
pieces of information when writing applications in PHP. The properties within
the next sections are all from the base DOMNode class. Although all classes
derived from the DOMNode class may call these properties, not all properties
return useful information for all types of nodes. In some cases, the return
value may even be NULL when the called property is not applicable for the
node.</p>

<h4>Node Type</h4>

<p>In many cases when using the DOM extension, a node will be returned but you
won’t know what type of node it is. In these instances, you can check the type
of node using the <em>nodeType property</em>. <em class="highlight">This
property returns an integer corresponding to one of the built-in constants for
node types</em>:</p>

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
could have just as easily been obtained from the node type. A few
additional node types exist that do have specific names, such as
entities, entity references, notations, document type definitions, and
PIs. Although a few of these may be useful to you, elements and
attributes are still the most commonly used nodes with this property.</p>

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
line feed printed. In the second call, the nodeValue of the document element
is printed. As mentioned, this property is not valid according to the DOM
specifications. To make things a little easier, the DOM extension in PHP 5
does allow this property for an element and returns a concatenation of all
text nodes within the scope of the element. The output is a bit long, but the
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

<h4>Using the Properties Together</h4>

<p>The nodeType, nodeName, and nodeValue properties are often useful and used
together when writing code where logic is conditional based on the specifics
of the node being tested. Consider the following code, which can be used as a
function. A node, referenced by &#36;node, is tested; based on criteria of
these properties, certain actions are taken.</p>

<pre>
switch (&#36;node-&gt;nodeType) {
   case XML_ELEMENT_NODE:
      print "Element Tag Name: ".&#36;node-&gt;nodeName;
      if (&#36;node-&gt;nodeName == "book") {
         /* We may want the lang attribute */
      }
      break;
   case XML_ATTRIBUTE_NODE:
      print "Attribute Name: ".&#36;node-&gt;nodeName."&#92;n";
      print "Attribute Value: ".&#36;node-&gt;nodeValue."&#92;n";
      if (&#36;node-&gt;nodeName == "lang") {
         /* Do something with the language */
      }
      break;
   case XML_TEXT_NODE:
   case XML_CDATA_SECTION_NODE:
      print "Content: ".&#36;node-&gt;nodeValue."&#92;n";
      break;
   default:
      print "Other Node Names: ".&#36;node-&gt;nodeName."&#92;n";
}
</pre>

<p>This code uses a <em>switch statement</em> to perform certain actions based
on the node type of the node passed in. Depending upon the type, actions then
take place based on the name and possible value of the node. This is a
simplified case but should give you an idea of how these properties can be
useful. As you become more familiar with other aspects of tree navigation, you
will revisit and modify this code.</p>

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
Simply put, all nodes living exactly one level beneath the current node are
children. For example, an element node may have mixed content consisting of,
but not limited to, a comment, a text node, and some additional element nodes.
An attribute node contains a single child node, which is a text node holding
the value for the attribute. Document nodes can contain comment nodes, PIs, a
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
#text
bookinfo
#text
preface
#text
chapter
#text
</pre>

<p>The book element contains three child elements but also is interspersed
with whitespace. This whitespace was not removed when the document was loaded,
resulting in the previous text nodes being created in the tree. Using this
property, you can see why the nodeType property can come in handy. Unless you
need to take some specific action with the whitespace, more often than not you
will ignore it when navigating the tree. For example:</p>

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
beneath the book element and prior to the bookinfo element. The variable
&#36;last contains the DOMText object that is the last child of book and that
contains the line feed after the closing chapter tag. Currently being
whitespace, these nodes can be ignored for now. So where does this get you? you
may ask. You can also move laterally by accessing node siblings, which you will
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

<h4>Accessing Parents and Using ownerDocument</h4>

<p>Nodes can also perform ascending movement within a tree. Every node within a
document has a parent with the exception of the document node. A parent is the
direct ancestor of the current node; hence, a document node cannot have a parent
node because it is the root node for the entire document. You can access the
parent using the <em>parentNode property</em>:</p>

<div class="remarkbox">
  <div class="rbtitle">note-to-self</div>
  <div class="rbcontent">
  <p>The author's example looks like an infinite loop. But, who am I
  to say anything!</p>
  </div>
</div>

<pre>
do {
   &#36;node = &#36;first;
   while(&#36;node) {
      if (! &#36;node-&gt;parentNode-&gt;isSameNode(&#36;root)) {
         print "ERROR: Parent Node Test FAILED";
         break 2;
      }
      &#36;node = &#36;node-&gt;nextSibling;
   }
   print "All parent node tests PASSED";
} while(0);
</pre>

<p>Using the code from the nextSibling example, the parentNode for each of the
nodes, including the text nodes, is returned and tested against the document
element, &#36;root, using the isSameNode() method. <em class="highlight">This
example uses object dereferencing features from PHP 5</em> and is equivalent to
writing the following:</p>

<pre>
&#36;parent = &#36;node-&gt;parentNode;
if (! &#36;parent-&gt;isSameNode(&#36;root)) {
...
</pre>

<p>The <em>isSameNode() method</em> tests the current node against the node
passed as an argument to determine whether they are the same node. By “same
node,” I mean the nodes must be the same node within the document. This is not
the same as saying
the nodes are equivalent; equivalent nodes must just have the same names and
content but do not have to be the same node with the same position in the
document. As you can see from the resulting All parent node tests PASSED
message, the parent node for these is the document element, &#36;root.</p>

<p>Nodes have <em class="highlight">direct access to</em> their associated
document through the
<em>ownerDocument property</em>. Although the body is accessible using the
<em>documentElement property</em>, the document node is still an important node
even when not needing or using a DTD. Later in this chapter, in the “Document
Nodes” section, you will learn how to use the document node object for factory
methods. This node provides much of the functionality used when creating and
editing documents and is accessed frequently in applications. For example:</p>

<pre>
&#36;node = &#36;root-&gt;ownerDocument;
print &#36;node-&gt;nodeName."&#92;n";
</pre>

<p>The code prints the value #document, because the document node is returned
from the property. To verify this, you can execute the following code using the
isSameNode() method:</p>

<pre>
if (&#36;dom-&gt;isSameNode(&#36;node))
   print "TRUE";
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
the entire document. The collection is iterated using a for loop based on
length, indicating the number of nodes within the collection. <em>length</em> is
the total number of elements, and the collection uses a zero-based index, so no
items are at an index equal to or greater than the length. Using dereferencing
(available in PHP 5), the element at the current index, &#36;x, is retrieved,
and the nodeValue for the node is printed. The output from this operation is as
follows:</p>

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
performed, because you already know that an element exists in the document
(although it is a good habit to test the return values prior to using them).
Again, dereferencing is used; the first element in the DOMNodeList is retrieved,
and immediately in the same line of code, <em>getElementsByTageName("*")</em>
is called on the node. All elements within the scope of the preface element are
returned and set to the &#36;elements variable. You can access this collection
the same way as before: by using a for loop. This time the node name is also
printed with its value, because you have no way to know exactly what elements
are returned when using the wildcard. The resulting output is as follows:</p>

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
this time. The method differs from the non-namespaced method in that it takes
two arguments. The first is the namespace URI, and the second is the local name
of the element, which is the same as the tag name for the previous method. Just
like the name parameter, the namespace URI parameter also accepts the *
wildcard. Using the wildcard results in retrieving all elements in any
namespace, but they must be in a namespace with the name determined from the
second parameter, which can also be a wildcard. For example:</p>

<pre>
&#36;result = &#36;dom-&gt;getElementsByTagNameNS("*", "*");
</pre>

<p>The resulting DOMNodeList, &#36;result, will contain every element in the
document that is within any namespace.</p>

<h4>Accessing Attributes</h4>

<p>Attributes inherit the same methods and properties from the DOMNode class as
other node types, but they are not accessed in the same manner as other nodes in
a document. As you have seen so far, nodes are traversed through children of
nodes. Attributes are different because they are not children of elements, which
is the only node type from which attributes may reside; rather, attributes,
conceptually, are properties of elements. You access them through their own set
of properties and methods.</p>

<h5>Collections of Attributes</h5>

<p>Just like you can check and access children, you can check attributes with
the <em>hasAttributes() method</em> and access them with the <em>attributes
property</em>. Both of these are defined on the DOMNode class and are safe to
use with all node types, although <em class="highlight">an object of DOMElement
will be the only class type that can return useful data:</em></p>

<pre>
if (&#36;root-&gt;hasAttributes()) {
   &#36;attributes = &#36;root-&gt;attributes;
   foreach(&#36;attributes as &#36;attr) {
      print "Attribute Name: ".&#36;attr-&gt;nodeName."&#92;n";
      print "Attribute Value: ".&#36;attr-&gt;nodeValue."&#92;n";
   }
}
</pre>

<p>If attributes exist on the &#36;root object, tested using the hasAttributes()
method, a <em>DOMNamedNodeMap object</em>, &#36;attributes, is returned
from the attributes property. This object is iterated in the same way the
DOMNodeList is iterated. The resulting output for this code is as follows:</p>

<pre>
Attribute Name: lang
Attribute Value: en
</pre>

<p><em class="highlight">One of the differences with the node map is that
attributes can be accessed directly by name rather than just a position
using</em> <em>DOMNamedNodeMap::getNamedItem().</em>
For example:</p>

<pre>
&#36;attr = &#36;attributes-&gt;<b>getNamedItem("lang")</b>;
print "Attribute Name: ".&#36;attr-&gt;nodeName."&#92;n";
print "Attribute Value: ".&#36;attr-&gt;nodeValue."&#92;n";
</pre>

<p>The document element contains only a single element, so the previous code
returns the same results as the code iterating the attributes. This time, the
lang attribute was accessed directly from the node map rather than iterating the
entire map. Just like a DOMNodeList, the position could also have been used to
access the attribute. Using a DOMNamedNodeMap, however, the items are unordered,
so you have no guarantee that an item at a certain position is the item for
which you are looking. For example:</p>

<pre>
if (&#36;attributes-&gt;length &gt; 0) {
   &#36;attr = &#36;attributes-&gt;item(0);
   print "Attribute Name: ".&#36;attr-&gt;nodeName."&#92;n";
   print "Attribute Value: ".&#36;attr-&gt;nodeValue."&#92;n";
}
</pre>

<p>This code outputs the same results as before. The difference here is the test
for the length of the DOMNamedNodeMap, which returns the number of items in the
collection, and the use of the <em>item() method</em> to access the item at the
zero-based index. Passing in the value of 0 for the argument returns the first
item in the list, which is the lang attribute.</p>

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

<p>The previous two pieces of code print the same results but perform the
operations differently. The first snippet returns the value of the named
attribute, lang, and prints the value. The second block of code retrieves the
attribute node named lang and prints the value from the returned node.</p>

<p>Although the document in Listing 6-1 is not using namespaces, the
namespace-aware methods can be used:</p>

<pre>
print "Attribute Value: ".&#36;root-&gt;getAttributeNS(NULL, "lang")."&#92;n";
&#36;attr = &#36;root-&gt;getAttributeNodeNS(NULL, "lang");
print "Attribute Value: ".&#36;attr-&gt;nodeValue."&#92;n";
</pre>

<p>The first argument for these methods is the namespace URI for the attribute
being accessed. Your attributes do not live in any namespaces, so by passing
NULL, you access the attributes normally. It is the same as accessing
attributes that do not live in any namespace. If the attributes were associated
with a namespace, the results from the methods would be empty unless the
appropriate namespace URI were passed as the first parameter.</p>

<h5>Declaring Namespaces</h5>

<p>Namespace declarations are handled as attributes within the DOM extension
and as such are created using the namespace’s attribute methods. The prefix
xmlns is bound to the http:// www.w3.org/2000/xmlns/ namespace as defined in
the XML 1.1 specification from the W3C (http://www.w3.org/TR/xml-names11/). For
example:</p>

<pre>
&#36;doc = DOMDocument::loadXML('&lt;root/&gt;');
&#36;root = &#36;doc-&gt;documentElement;

&#36;root-&gt;setAttributeNS('http://www.w3.org/2000/xmlns/',
                      'xmlns:exa','http://www.example.com/example');
&#36;root-&gt;appendChild(new DOMElement('exa:child', 'content',
                                  'http://www.example.com/example'));
&#36;doc-&gt;formatOutput = TRUE;
print &#36;doc-&gt;saveXML();
</pre>

<p>Using the <em>setAttributeNS() method</em>, a namespace that contains the
prefix exa and is bound to http://www.example.com/example is declared. The
namespace for the xmlns prefix is used as the namespace URI in this method, and
the value of the attribute is the namespace that will be created. To declare a
namespace, it is mandatory that the namespace URI parameter be the value
http://www.w3.org/200/xmlns/; otherwise, the DOM extension will not know that
a namespace is supposed to be created and a normal attribute will result. The
following line illustrates how to append a new element bound to this newly
created namespace, which results in the following document upon
serialization:</p>

<pre>
&lt;?xml version="1.0"?&gt;
&lt;root xmlns:exa="http://www.example.com/example"&gt;
  &lt;exa:child&gt;content&lt;/exa:child&gt;
&lt;/root&gt;
</pre>

EOPAGESTR5;
echo $page_str;

site_footer();

?>