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


site_header("Creating & Editing a Tree");

$page_str = <<<EOPAGESTR5

<p>The DOM extension’s biggest strength comes from its functionality for
creating and editing trees. As you will see with the other XML technologies,
none comes close to the capabilities the DOM extension offers in this respect.
Unless you are a hard-core XML developer or integrator, you may end up using
only a quarter of the offered functionality yet still encounter no shortcomings
with the small subset of functionality used. Within the following sections, you
will begin by creating the document in Listing 6-1 from scratch and then work on
editing the result.</p>

<h3>Document Nodes</h3>

<p>Earlier in this chapter, you saw many different methods for creating a
DOMDocument object. The document being created contains a document type
declaration, so you will use the <em>DOMImplementation class</em> to create the
DOMDocument object; this allows you to create a <em>DOMDocType object</em>
that can be passed as a parameter to create a document with a subset. This class
allows static method calls, so in this case, you have no need to instantiate an
object. For example:</p>

<pre>
&#36;doctype = DOMImplementation::createDocumentType("book",
           "-//OASIS//DTD DocBook XML V4.1.2//EN",
           "http://www.oasis-open.org/docbook/xml/4.1.2/docbookx.dtd");
&#36;dom = DOMImplementation::createDocument(NULL, "book", &#36;doctype);
</pre>

<p>The first step is creating a DOMDoctType object, because it is needed when
creating the document. You do this using the <em>createDocumentType()
method</em> and passing the name for the document declaration, which, as you
recall from Chapter 2, must match the name of the document element, the public
identifier, and finally the system identifier. If the declaration is a system
identifier, you pass NULL for the public identifier argument. The final step is
to create the document using the <em>createDocument() method</em>. The first
argument is the namespace for the document element. In this case, the document
is not using namespaces, and you use NULL. The remaining parameters are the name
of the document element, which will be created when the method returns, and the
DOMDocType object, &#36;doctype, that was created in the previous line. Upon
executing this code, the DOMDocument object, &#36;dom, will contain the document
node with a DTD and the document element created.</p>

<p>At this point, if the tree were output using a method such as saveXML(), you
would notice that the encoding is missing. Using the DOMImplementation class to
create the document does not offer a way to set the version or encoding. The
version at least defaults to 1.0. You can set the encoding using the encoding
property of the document:</p>

<pre>
&#36;dom-&gt;encoding = "UTF-8";
</pre>

<p>This property does not affect how you create the document. Data that is not
conformant to the internal UTF-8 encoding of the tree still needs to be
converted to UTF-8. Upon output of the tree, however, the data is converted to
the proper encoding set by this property.</p>

<h3>Element Nodes</h3>

<p><em class="highlight">You can create, insert, and remove element nodes from
a tree, but you cannot (unlike with most other nodes) edit their contents.</em>
Whether they are just text or combinations of other nodes, in order to edit
them, you must access the child nodes or attributes. The next sections will take
you through how to create, insert, and remove element nodes in a document.</p>

<h4>Creating Elements</h4>

<p>You have two ways to create element nodes. One is to use the
<em class="highlight">factory methods</em> from the DOMDocument object, and the
other is <em class="highlight">direct instantiation</em>. According to the
specification, nodes must be associated with a document. The factory methods
follow this rule. As you will see following the factory methods, the DOM
extension allows <em class="highlight">direct instantiation</em> of DOMElement
objects, which results in element nodes with no tree association. This exists
not only for convenience during development, but as discussed later in this
chapter, it also allows for limited functionality of extending the DOM
classes.</p>

<p>As previously mentioned, the DOMDocument object is a focal object when using
the DOM extension. You can create a new element associated to the current
document using the factory methods <em>createElement()</em> and
<em>createElementNS()</em>. The document that has been created to this point
contains a DTD and the document element node book. Ignoring the attribute for
now, the next node to be created is the bookinfo element, which is the first
child element of book. For example:</p>

<pre>
&#36;bookinfo = &#36;dom-&gt;createElement("bookinfo");
</pre>

<p>This piece of code returns a DOMElement object, &#36;bookinfo, with the name
bookinfo. The createElement method takes one mandatory parameter and one
optional parameter. The first parameter is the qualified name of the element to
be created, which in this case is bookinfo. The second optional parameter is the
value of the element. In the event the element node will contain text content,
you can do this at the same time the element is created. In actuality, a text
node is created and appended as a child of the element being created. For
instance, the first child of the bookinfo element is a title element, consisting
of only text:</p>

<pre>
&#36;bititle = &#36;dom-&gt;createElement("title", "DOM in PHP 5");
</pre>

<p>With these two lines of code, you have created two new objects. The variable
&#36;bookinfo holds the DOMElement object for the bookinfo node, and the
variable &#36;bititle holds the DOMElement object for a title node. This
&#36;bititle node also has a child text node, with the contents DOM in PHP 5.
For now they exist as stand-alone nodes. They are associated with the current
document but are not within the tree at this point. Before inserting these
nodes, it is helpful to look at other ways to create element nodes.</p>

<p>You can also create elements within a namespace. The document being created
here does not use namespaces, but you could still use the createElementNS()
method:</p>

<pre>
&#36;biauthor = &#36;dom-&gt;createElementNS(NULL, "author");
</pre>

<p>This method requires two mandatory parameters and accepts a third parameter,
which is an optional value parameter. The first parameter is the namespace URI.
In this case, nodes are not within any namespace, so NULL is passed. The second
parameter is the qualified name of the element. As you probably recall, this
consists of the prefix and the local name. For example, you could create an
element named trash in the http://www.example.com/trash namespace. The prefix
tr will also be associated with this element:</p>

<pre>
&#36;trash = &#36;dom-&gt;createElementNS("http://www.example.com/trash", "tr:trash");
</pre>

<p>When the &#36;trash object is inserted into a tree, the element will be
associated with the prefix, and if needed, the namespace declaration will be
created within the document. If possible, however, an existing namespace
declaration within scope at the insertion point will be used. This may result in
a change to the prefix, which is not incorrect, because the namespace itself is
the important aspect here and not the prefix. I will illustrate how to do this
in the examples in the “Building an XSL Template” example toward the end of this
chapter.</p>

<p>You can also directly instantiate elements using the <em>new keyword</em>.
The firstname and surname elements, which will be the children of the bookinfo
element, will be created using the new keyword. <em class="highlight">The
constructor for the DOMElement class takes the same parameters as the
createElement() method.</em> The first required parameter is the name of the
elements, and the second is an optional value for the element:</p>

<pre>
&#36;firstname = new DOMElement("firstname", "Rob");
&#36;surname = new DOMElement("surname", "Richards");
</pre>

<p><em class="highlight">These two new elements, unlike the previous created
elements, are not associated with a document and are read-only.</em> Until they
are associated with a document, they can be inserted into a tree, but no
children, other than any text nodes that may have been created during
instantiation, can be appended to these elements.</p>

<p>When creating elements, you have a possibility of a <em>DOMException</em>
being thrown. The name of the element is checked to ensure that it is valid. In
the event the check fails, the object is not created and a DOMException
indicating that invalid characters were used may be thrown. For example, the
name 123 is used when trying to instantiate a DOMElement object:</p>

<pre>
try {
   &#36;test = new DOMElement("123");
} catch (DOMException &#36;e) {
   var_dump(&#36;e);
}
</pre>

<p>According to the XML specification, names cannot start with a numeric, which
results in a DOMException being thrown.</p>

<p>As previously mentioned, the constructor can take a third parameter
indicating the URI for the namespace of the element. When this is passed, the
first argument, being a qualified name, will split the name parameter into any
prefix and local name values. Without the third parameter being used, the name
passed is used as the local name even if it contains a colon:</p>

<pre>
&#36;nsElement = new DOMElement("nse:myelement", NULL, "http://www.example.com/ns");
</pre>

<p>This instantiates a DOMElement object with the myelement element prefixed
with nse and living in the http://www.example.com/ns namespace. A value can be
passed for the content, but in this case, NULL is passed, and the element is
created without any children.</p>

<h4>Inserting Elements</h4>

<p>With a few elements currently created, they need to be inserted into the
tree. <em class="highlight">The methods for appending and inserting nodes come
from the DOMNode class and thus are not specific to element nodes; in other
words, they can be called from other node types as well.</em> Currently, the
document contains only a single document element. Using the document node,
&#36;dom, the document element will be retrieved and the bookinfo element
appended:</p>

<pre>
&#36;dom-&gt;documentElement-&gt;appendChild(&#36;bookinfo);
</pre>

<p>The <em>appendChild() method</em> takes a node to be appended as a child of
the current node for a parameter and returns the node appended. The node is
appended as the last child of the current node’s children. In this case, the
book element currently has no children, so the bookinfo is added as the first
child. Also, you already have a handle on the node being inserted, so you have
no need to capture the return value.</p>

<p>This method, like the other insertion methods, may throw a DOMException. The
possible cases for an exception are a hierarchy error, when the node being
appended already exists in the tree and is a parent of the current node; a
wrong document error, when the node being appended is associated with a document
other than the current nodes document; and lastly a “no modification allowed”
error, when the current node is read-only. One point to note about a hierarchy
error is that it is not considered an error to append a node without an
associated document to a node with a document because the appended node will
become part of the tree and automatically be associated with the document. In
cases where the current node is not associated with a document, a “no
modification allowed” error is issued, because these nodes are read-only.</p>

<p>Before appending the author element, &#36;biauthor, into the tree, you can
append the firstname and surname nodes to the author element. Remember,
&#36;biauthor was created with an association to the document, so the firstname
and surname elements, once appended, will inherit this association:</p>

<pre>
&#36;biauthor-&gt;appendChild(&#36;surname);
&#36;biauthor-&gt;insertBefore(&#36;firstname, &#36;surname);
</pre>

<p>The first line should look familiar because it was used to append the
bookinfo element. The second line uses a new <em>method, insertBefore()</em>.
It works similarly to appendChild(), but the second argument, which must be a
child node of the current node, is used as a reference point to insert the new
node before. This code is the same as writing the following:</p>

<pre>
&#36;biauthor-&gt;appendChild(&#36;firstname);
&#36;biauthor-&gt;appendChild(&#36;surname);
</pre>

<p>You will typically use insertBefore() when trying to insert elements in the
middle of a list of child nodes, but it’s used in the example to show how it
works. With the author element complete with content, you can now insert it into
the document:</p>

<pre>
&#36;bookinfo-&gt;appendChild(&#36;biauthor);
</pre>

<p>If you look at the output now, you will see the document beginning to take
shape. The document may look odd because it is all strung together without any
line feeds, so you can beautify the output using the
<em>formatOutput property</em>:</p>

<pre>
&#36;dom-&gt;formatOutput = TRUE;
print &#36;dom-&gt;saveXML();
</pre>

<p>Well, it looks like the title element was omitted and needs to be inserted.
In this case, insertBefore() is definitely appropriate. The title node is
supposed to come before the author element, which is already in the tree:</p>

<pre>
&#36;bookinfo-&gt;insertBefore(&#36;bititle, &#36;biauthor);
</pre>

<p>You can deal with the remainder of the elements for the tree later because
you already have enough information to create them. For now, you’ll move on to
dealing with attribute nodes.</p>

<h3>Attribute Nodes</h3>

<p>You can handle attribute nodes, as well as specific attribute functionality
from the DOMElement class, in a similar fashion to element nodes. You can create
them using factory methods from the DOMDocument class by directly instantiating
them, and you can create them using methods from a DOMElement object. You can
also insert and remove them using the methods from the DOMNode class as well as
methods from a DOMElement object.</p>

<p>Equivalent methods for attribute creation exist for a DOMDocument object as
for element creation. Currently (though this may change in future version of
PHP), you cannot create attributes with values using the factory methods. The
only parameter is a name (or in the case of using namespaces, a namespace URI
and a name):</p>

<pre>
/* Equivalent methods for creation of lang attribute */
&#36;lang = &#36;dom-&gt;<b>createAttribute</b>("lang");
&#36;lang = &#36;dom-&gt;<b>createAttributeNS</b>(NULL, "lang");
</pre>

<p>Notice <em>createAttribute()</em> and <em>createAttributeNS()</em> in the
example.</p>

<p>Both of these lines of code result in the creation of a <em>DOMAttr
object</em> named lang. Using these methods, you need to specify a value, which
you can do using the <em>nodeValue property</em> from the DOMNode
class<em class="highlight"> or </em>using the <em>value property</em> from the
DOMAttr class:</p>

<pre>
/* Equivalent calls to set the value for the lang attribute to "en" */
&#36;lang-&gt;nodeValue = "en";
&#36;lang-&gt;value = "en";
</pre>

<p>You can also create attributes with values at the same time using the new
keyword. Again, these nodes will not be associated with a document:</p>

<pre>
&#36;lang = new DOMAttr("lang", "en");
</pre>

<p>Using any of these methods to create an attribute requires the attribute to
be inserted into the tree. Using methods already covered, you could add it doing
this:</p>

<pre>
/* Equivalent methods for inserting an attribute */
&#36;bookinfo-&gt;appendChild(&#36;lang);
&#36;bookinfo-&gt;insertBefore(&#36;lang, NULL);
</pre>

<p>The last method uses <em>insertBefore()</em> with the reference node
parameter being NULL. <em class="highlight">When NULL is passed as the
reference node, the function works in the same way as appendChild(). The node
is inserted as the last node.</em></p>

<div class="remarkbox">
  <div class="rbtitle">Note</div>
  <div class="rbcontent">
  <p>Attributes are not children of element nodes. When using the appending
  child functions, such as appendChild(), the attribute is not appended as a
  child but instead appended in the attribute property list of the element.</p>
  </div>
</div>

<p>You can also add attribute nodes using the <em>setAttributeNode()</em> and
<em>setAttributeNodeNS()</em> methods from the DOMElement class. These methods
take a single DOMAttr object as a parameter. These methods will first check
whether an attribute with the same name—and in the case of setAttributeNodeNS(),
the same name and namespace—exists. Then, <em class="highlight">if it exists,
these methods remove the attribute and replace it with the new attribute. These
methods return NULL if no attribute was replaced or return the replaced
attribute.</em> For example:</p>

<div class="remarkbox">
  <div class="rbtitle">note-to-self</div>
  <div class="rbcontent">
  <p>The manual says: DOMElement::setAttributeNode — Adds new attribute
  node to element. Returns old node if the attribute has been replaced or
  NULL.</p>
  </div>
</div>

<pre>
/* Equivalent calls for this document as no namespaces are being used */
&#36;oldlang = &#36;bookinfo-&gt;setAttributeNode(&#36;lang);
&#36;oldlang = &#36;bookinfo-&gt;setAttributeNodeNS(&#36;lang);
</pre>

<p>You can also create attributes without ever having to directly create a
DOMAttr object. The DOMElement class includes the methods
<em>setAttribute()</em> and <em>setAttributeNS()</em>. These methods are the
counterparts to the getAttribute() and getAttributeNS() methods you encountered
earlier when navigating the tree. Both of the set methods create an attribute
based on the name and value, passed as parameters, and return the newly created
DOMAttr object. Just like all the other namespace functions, setAttributeNS()
accepts a namespace URI as a parameter and uses a qualified name as an
argument:</p>

<pre>
/* Equivalent calls to create the lang attribute with value "en" */
&#36;bookinfo-&gt;setAttribute("lang", "en");
&#36;bookinfo-&gt;setAttributeNS(NULL, "lang", "en");
</pre>

<div class="remarkbox">
  <div class="rbtitle">Caution</div>
  <div class="rbcontent">
  <p>When creating an attribute with an entity reference as a value, you must
  create a DOMAttr object and set the value manually. The value argument for
  the constructor of a DOMAttr and for the setAttribute() and setAttributeNS()
  methods is simple text that is not parsed and treated as literal text.</p>
  </div>
</div>

<h3>Text Nodes</h3>

<p>Text nodes are simple nodes, because they <em class="highlight">cannot have
child nodes or attributes</em>. In other words, they simply contain text
content. This does not mean they offer little functionality, though. You can
use the text nodes to set content as well as <em class="highlight">perform
string functions.</em> You create and insert them in the same manner as element
nodes. You can create them either using a factory method from a DOMDocument
object or using the new keyword. You can insert them using the normal
appendChild() and insertBefore() methods.</p>

<h4>Creating and Inserting Text Nodes</h4>

<p>You use a DOMDocument object to create a text node with the
<em>createTextNode() method</em>. A data parameter is required that specifies
the content, or value, for the text node. Instantiating a <em>DOMText
object</em> with the new keyword does not require a value, because the default
is to create a text node with empty content. For example:</p>

<pre>
/* Equivalent creation of DOMText objects */
&#36;yeartxt = &#36;dom-&gt;createTextNode("2005");
&#36;yeartxt = new DOMText("2005");
</pre>

<p>The text node created, whichever method you decide to use, will be used as
the content for the yet-to-be-created year element, which will be the child of
a yet-to-be-created copyright element. While inserting these nodes, this also
creates the holder element. For example:</p>

<pre>
/* Create and Append a copyright element */
&#36;copyright = &#36;bookinfo-&gt;appendChild(new DOMElement("copyright"));
</pre>

<p>In one line, a new copyright element is instantiated using the new keyword
and is appended to the bookinfo element. <em class="highlight">You might have
wondered why the return values mattered before because all examples previously
used instantiated objects when appending nodes. In this case, the &#36;copyright
variable, upon the method returning, will contain the newly created DOMElement
object that contains the copyright element.</em> For example:</p>

<pre>
/* Create year element */
&#36;year = &#36;dom-&gt;createElement("year");

/* Append text node to set content */
&#36;year-&gt;appendChild(&#36;yeartxt);
&#36;copyright-&gt;appendChild(&#36;year);
</pre>

<p>After creating the year element, the DOMText object, previously created, is
appended as content. Once this is done, the year element is appended to the
copyright element. For example:</p>

<pre>
/* Append a newly created holder element with content "Rob Richards" */
&#36;copyright-&gt;appendChild(new DOMElement("holder", "Rob Richards"));
</pre>

<p>Again, a single line of code performs multiple operations. A new DOMElement
object is created with the name holder and the value Rob Richards. This element
is appended to the copyright element.</p>

<div class="remarkbox">
  <div class="rbtitle">remember</div>
  <div class="rbcontent">
  <p>When creating an element you can specify the value of that element if that
  value is text. Thus, a text node will have been creeated and added
  automatically!</p>
  </div>
</div>

<h4>Manipulating Text</h4>

<p>The DOMText class derives from the <em>DOMCharacterData class</em>. Methods
exist in both classes that can manipulate text on DOMText objects. For example,
take the following piece of code, which includes the appropriate output that
will print after the colon in each of the comments:</p>

<pre>
/* If content is not whitespace then ... */
if (! &#36;yeartxt-&gt;isElementContentWhitespace()) {
   /* Print substring at offset 1 and length 2: 00 */
   print &#36;yeartxt-&gt;substringData(1,2)."&#92;n";
   
   /* Append the string -2006 to the content and print output: 2005-2006 */
   &#36;yeartxt-&gt;appendData("-2006");
   print &#36;yeartxt-&gt;nodeValue."&#92;n";
   
   /* Delete content at offset 4 with length of 5 and print output: 2005 */
   &#36;yeartxt-&gt;deleteData(4,5);
   print &#36;yeartxt-&gt;nodeValue."&#92;n";

   /* Insert string "ABC" at offset 1 and print output: 2ABC005 */
   &#36;yeartxt-&gt;insertData(1, "ABC");
   print &#36;yeartxt-&gt;nodeValue."&#92;n";
   
   /* Replace content at ofset 1 with length of 3 with an empty string: 2005 */
   &#36;yeartxt-&gt;replaceData(1, 3, "");
   print &#36;yeartxt-&gt;nodeValue."&#92;n";
}
</pre>

<p>At this point the tree is really starting to take shape. The output at this
point—using formatting, of course—looks like this:</p>

<pre>
&lt;?xml version="1.0" encoding="UTF-8"?&gt;
&lt;!DOCTYPE book PUBLIC "-//OASIS//DTD DocBook XML V4.1.2//EN"
                      "http://www.oasis-open.org/docbook/xml/4.1.2/docbookx.dtd"&gt;
&lt;book&gt;
  &lt;bookinfo lang="en"&gt;
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
&lt;/book&gt;
</pre>

<p>The serialized tree looks almost exactly like the tree in Listing 6-1. This
is good because that is the goal you are working toward. The only missing pieces
are the preface and chapter subtrees. This will be left as an exercise for you
to finish because I have already covered everything you need to complete the
tree.</p>

<h3>Other Node Types</h3>

<p>The node types covered to this point are the most frequently used, which is
why I have given them much greater emphasis. You can create and insert the
remaining node types in the same manner as the previous nodes. Because the
complete API is included in Appendix B, I will show how to create the remaining
nodes through code:</p>

<pre>
/* Create a DOMDocumentFragment */
&#36;frag = &#36;dom-&gt;createDocumentFragment();
&#36;frag = new DOMDocumentFragment();

/* Create DOMComment */
&#36;comment = &#36;dom-&gt;createComment("this is a comment");
&#36;comment = new DOMComment("this is a comment");
/* Results in &lt;!-- this is a comment --&gt; */

/* Create DOMCDATASection */
&#36;cdata = &#36;dom-&gt;createCDATASection("&lt;html&gt;&lt;/html");
&#36;cdata = new DOMCDATASection("&lt;html&gt;&lt;/html");
/* Results in &lt;![CDATA[&lt;html&gt;&lt;/html]]&gt; */

/* Create DOMProcessingInstruction */
&#36;pi = &#36;dom-&gt;createProcessingInstruction("php", "echo 'Hello World';");
&#36;pi = new DOMProcessingInstruction("php", "echo 'Hello World';");
/* Results in &lt;?php echo 'Hello World';?&gt; */

/* Create DOMEntityReference */
&#36;entityref = &#36;dom-&gt;createEntityReference("lt");
&#36;entityref = new DOMEntityReference("lt");
/* Results in &lt; */
</pre>

<p>Outside the methods inherited from the DOMNode class, the
<em>DOMDocumentFragment class</em> is the only class with additional
functionality. This functionality is only a single method and available only in
PHP 5.1 and higher. Rather than having to build a fragment manually by appending
nodes, you can use the <em>method appendXML()</em> to create a fragment from
string data. Take the case of building a fragment manually versus building it
from a string:</p>

<pre>
&#36;frag = &#36;dom-&gt;createDocumentFragment();
&#36;frag-&gt;appendChild(new DOMElement("node1", "node1 value"));
&#36;frag-&gt;appendChild(new DOMElement("node2", "node2 value"));
</pre>

<p>It would have been so much easier to append the data as a string. You had no
need to manually create the DOMElement objects because the appropriate nodes are
automatically created through the appendXML() method:</p>

<pre>
&#36;frag = &#36;dom-&gt;createDocumentFragment();
&#36;frag-&gt;appendXML("&lt;node1&gt;node1 value&lt;/node1&gt;&lt;node2&gt;node2 value&lt;/node2&gt;");
</pre>

<div class="remarkbox">
  <div class="rbtitle">Note</div>
  <div class="rbcontent">
  <p>When appending a DOMDocumentFragment object into a tree, only the children
  on the fragment are added. The DOMDocumentFragment object that is left after
  an append will be empty because the nodes have been removed and inserted into
  the tree.</p>
  </div>
</div>

<h2>Removing and Replacing Nodes</h2>

<p>The last piece of editing a document is removing and replacing nodes in a
tree. Some of the methods encountered so far will perform this type of
functionality. Take, for instance, the setAttributeNode() method. When a node
with the same name exists on the element, the old attribute is removed and
replaced with the new attribute node, and the old attribute is returned. The
same functionality can happen with other node types using the replaceChild()
method. Sometimes, however, you want just to remove a node. In this case, you
can use the removeChild() method.</p>

<p>Given the following document loaded into a DOMDocument object:</p>

<pre>
&#36;doc = DOMDocument::loadXML('&lt;?xml version="1.0"?&gt;
&lt;root&gt;
   &lt;child1&gt;child1 content&lt;/child1&gt;
   &lt;child2&gt;child2 content&lt;/child2&gt;
   &lt;child3&gt;child3 content&lt;/child3&gt;
&lt;/root&gt;');
</pre>

<p>the element child2 needs to be removed from this document, and child3 needs
to be replaced with the element newchild. The first step is to get access to
each of these nodes. To reduce the number of steps, I will show how to retrieve
the elements using the getElementsByTagName() method:</p>

<pre>
&#36;root = &#36;doc-&gt;documentElement;
&#36;child2 = &#36;root-&gt;getElementsByTagName("child2");
&#36;child3 = &#36;root-&gt;getElementsByTagName("child3");
</pre>

<div class="remarkbox">
  <div class="rbtitle">Errata</div>
  <div class="rbcontent">
  <p>The author seems to be confused about whether he is talking
  about the text node in the element or the element itself. Either that or
  he made an editing mistake. No-matter, I've taken the liberty of correcting
  the code section above.</p>
  </div>
</div>

<p>The first step is to remove the &#36;child object:</p>

<pre>
&#36;root-&gt;removeChild(&#36;child2);
</pre>

<p>If you look at the serialized tree now, you would see this:</p>

<pre>
&lt;?xml version="1.0"?&gt;
&lt;root&gt;
   &lt;child1&gt;child1 content&lt;/child1&gt;

   &lt;child3&gt;child3 content&lt;/child3&gt;
&lt;/root&gt;
</pre>

<p>The whitespaces are left in the document, causing the blank line in the
output. The &#36;child3 object is still in scope so can now be replaced with a
new element. This also will be condensed using the new keyword for the new
element:</p>

<pre>
&#36;oldchild = &#36;root-&gt;replaceChild(new DOMElement("newchild", "new content"), &#36;child3);
</pre>

<p>In this case, the new element is being created inline. Unfortunately, using
the new keyword here does not give direct access to the newly created node. This
method returns the node being removed from the tree. The resulting serialized
tree is as follows:</p>

<pre>
&lt;?xml version="1.0"?&gt;
&lt;root&gt;
   &lt;child1&gt;child1 content&lt;/child1&gt;
   
   &lt;newchild&gt;new content&lt;/newchild&gt;
&lt;/root&gt;
</pre>

<p>Wrapping up this section, you might want to remove those whitespaces within
the root element children. I have already covered everything you need to know
in order to do this. One way is to use the following piece of code:</p>

<div class="remarkbox">
  <div class="rbtitle">Warning</div>
  <div class="rbcontent">
  <p>Read paragraph after this code section before using it in a modified
  way.</p>
  </div>
</div>

<pre>
&#36;children = &#36;root-&gt;childNodes;
for (&#36;x=&#36;children-&gt;length; &#36;x--; &#36;x&gt;=0) {
   &#36;node = &#36;children-&gt;item(&#36;x);
   if (&#36;node-&gt;nodeType == XML_TEXT_NODE && &#36;node-&gt; isElementContentWhitespace()) {
      &#36;root-&gt;removeChild(&#36;node);
   }
}
</pre>

<p>You have many ways to accomplish this task. One question you may have is why
the iteration was performed from last to first. Based on how this code was
written, DOMNodeList objects are being used. These are live collections
resulting in changes of indexes when nodes are added or removed. For now, I will
let you think about this and possibly come up with the answer. Have no worries
if you are unsure of why the code was written in this manner, because I answer
this question in depth in the section “Common Questions, Misconceptions, and
Problems.”</p>

EOPAGESTR5;
echo $page_str;

site_footer();

?>