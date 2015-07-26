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


site_header("Summary - Rel. to XHTML Edit Proj.");

$page_str = <<<EOPAGESTR5

<p><b>Minimal demand:</b> An XML document must have a single root element that
encloses all the other elements, similar to &lt;HTML&gt;&lt;/HTML&gt; in HTML
documents. This is also sometimes called the <em>document element</em>.</p>

<p>BTW, the main document for my web pages uses this root element:</p>

<pre>
&lt;html xmlns="http://www.w3.org/1999/xhtml"&gt;&lt;/html&gt;
</pre>

<p>Text in a text node can't have the characters <em class="highlight">&amp;,
&lt; or &gt;</em>. Value strings for attributes can't have
a<em class="highlight"> ' </em>if the attribute value is using ' to
enclose it. Likewise for<em class="highlight"> "</em>. These characters must be
substituted for by their escape sequences where they are not allowed to be. Here
are there escape sequences: &amp;amp;, &amp;lt;, &amp;gt;,
&amp;#39;(&amp;apos;) and &amp;#34;(&amp;quot;). The entity references (those
things with words not numbers) for these special characters do not need to be
defined in a DTD because they are automatically built into the parser. All the
ones with numbers don't need to be defined in a DTD either.</p>

<p>XML is case sensitive; some variants, such as XHTML, require lowercase tags
and attributes.</p>

<p>The standard one-line <em>XML declaration</em>:</p>

<pre>
/* basic */
&lt;?xml version="1.0"?&gt;
/* complete */
&lt;?xml version="1.0" encoding="UTF-8" standalone="yes"?&gt;
</pre>

<p><em class="highlight">Document type declaration</em> for my XHTML:</p>

<pre>
&lt;!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"&gt;
</pre>

<p>The XML declaration and document type declaration (in that order) belong in
the prolog. Neither of which is required. Only a single element (document
element) is required for well-formedness.</p>

<p>When encoding is omitted and not specified by other means, such as byte order
mark (BOM) or external protocol, the XML document must use either UTF-8 or
UTF-16 encoding.</p>

<p>The stand-alone declaration (standalone), also not required within the XML
declaration, indicates whether the document requires outside resources, such as
an external DTD.</p>

<p>Element content may consist of character data, elements, <em>references</em>,
CDATA sections, PIs, and comments.</p>

<pre>
&lt;myElement&gt;
    &lt;nestedElement&gt;content of nestedElement&lt;/nestedElement&gt;
&lt;/myElement&gt;
</pre>

<p>Breaking this document down, the element named nestedElement contains a <br/>
<em>string of character data</em>. The document element myElement contains
content consisting of <em>whitespace</em> (a line feed and then a tab), followed
by the element nestedElement and its content, followed by more whitespace (line
feed). Note that whitespace outside the document element is considered markup;
However, whitespace inside an element is considered character data (I'm talking
about whitespace between the tags of the element).</p>

<p><em class="highlight">Attributes must also have a value, even if the value is
empty.</em> Again, referring to HTML, you may be accustomed to seeing lone
attribute names such as &lt;HR size="5" noshade&gt; or &lt;frame name="xxx"
scrolling="NO" noresize&gt;. Notice that noshade and noresize have no defined
values. These are not well-formed XML and to be made conformant must be written
as &lt;HR size="5" noshade="noshade"&gt; and &lt;frame name="xxx" scrolling="NO"
noresize="noresize"&gt;, which now makes them XHTML and XML compliant. In cases
where an attribute value is empty and there are no rules for any default values,
such as those for converting HTML to XHTML, you would write an attribute as
such: attrname="".</p>

<p><em class="highlight">Attribute values are character data.</em> You can use
any character including a space; however, follow the rules for characters that
need escaping (&lt; &gt; &quot; &apos;).</p>

<pre>
&lt;Car color="black &amp;amp; white" owner="Rob&amp;apos;s" score="&amp;lt; 5" /&gt;
</pre>

<p>I'm not including anything about <em>CDATA sections</em>, <em>comments</em>
or <em>processing instructions</em>.</p>

<hr/>

<h4>Entity References</h4>

<p>You have already encountered some of the built-in entity references
(&amp;amp;, &amp;lt;, &amp;gt;, &amp;apos;, and &amp;quot;) throughout this
chapter. <em class="highlight">Just as characters can be represented using
numeric character references, entity references are used to reference strings,
which are defined in the DTD.</em> They take the form of &amp;, which is
followed by a legal name, and they terminate with a semicolon. You are probably
familiar with the concept from HTML:</p>

<pre>
&lt;P&gt;Copyright &amp;copy; 2002&lt;/P&gt;
</pre>

<p>The entity reference &amp;copy; is defined in the HTML DTD and represents the
copyright symbol. Entity references cannot just be used blindly, however. The
document must provide a meaning to an entity reference. For instance, if you
were looking at a document that contained &lt;p&gt;&amp;myref;&lt;p&gt;, the
entity reference &amp;myref; has absolutely no meaning to you or may mean
something completely different to you than to me. You can use DTDs to define an
entity reference. It is mandatory that any entity reference, other than those
that are built in, must be defined. Looking at an HTML page, you may notice the
DOCTYPE tag at the top of the page. The contents depend upon the type of HTML
you are writing. For instance, -//W3C//DTD HTML 4.01 Transitional//EN refers to
the DTD http://www.w3.org/TR/html4/loose.dtd. This file contains a reference to
http://www.w3.org/TR/html4/HTMLlat1.ent. If you looked at the contents of this
file, you will notice that the entity copy is defined as
&lt;!ENTITY copy	CDATA &amp;#169; -- copyright sign, U+00A9 ISOnum
--&gt;.</p>

<p>The entity reference, when used within the document, then is able to take its
“meaning” from the definition. This is further explained in Chapter 3.</p>

<h4>General Entity Declaration</h4>

<p>Entity declarations may be either general or parameter entity declarations.
Entity declarations will be covered in more depth in Chapter 3, though general
entities have some bearing to this discussion with respect to entity references.
The common use of general entities is to declare the text replacement value for
entity references. General entities are commonly referred to as entities unless
used in a context where that name would be ambiguous; therefore, for the sake of
this section, entities will refer to general entities.</p>

<p>Entities are defined within the DTD, which is part of the prolog. Suppose you
had the string "This is replacement text", which you want to use many times
within the document. You could create an entity with a legal name, in this case
"replaceit":</p>

<pre>
&lt;?xml version="1.0"?&gt;
&lt;!DOCTYPE foo [
  &lt;!ENTITY replaceit "This is replacement text"&gt;

]&gt;
&lt;foo&gt;&replaceit;&lt;/foo&gt;
</pre>

<p>If this document were loaded into a parser that was substituting entities,
which means it is replacing the entity reference (&replaceit;) with the text
string defined in the entity declaration, the results would look something like
this:</p>

<pre>
&lt;?xml version="1.0"?&gt;
&lt;!DOCTYPE foo [
  &lt;!ENTITY replaceit "This is replacement text"&gt;

]&gt;
&lt;foo&gt;This is replacement text&lt;/foo&gt;
</pre>

<hr/>

<div class="remarkbox">
  <div class="rbtitle">note</div>
  <div class="rbcontent">
  <p>I'm not including ID or Namespace stuff!</p>
  </div>
</div>

<p><em>Validation</em> allows you to determine whether the document conforms to
your expectations before actually sending the document to be processed.</p>

<div class="remarkbox">
  <div class="rbtitle">note</div>
  <div class="rbcontent">
  <p>I'll only talk about DTD validation on this page!</p>
  </div>
</div>

<hr/>

<h4>Document Type Declarations</h4>

<p>The document type declaration is not a DTD but is the declaration to declare
a DTD. It can include an internal subset, an external subset, or both. A
declaration looks like the following:</p>

<pre>
&lt;!DOCTYPE document_element definitions&gt;
</pre>

<p>Here is a sample of a document type declaration which adds external
subsets to the DTD.</p>

<pre>
&lt;!-- Using System Identifier --&gt;
&lt;!DOCTYPE courses SYSTEM "http://www.example.com/courses.dtd"&gt;
&lt;!-- Using Public Identifier --&gt;
&lt;!DOCTYPE courses PUBLIC "-//Example//Courses DTD//EN"
                         "http://www.example.com/courses.dtd"&gt;
</pre>

<p>Here is a sample external subset:</p>

<pre>
&lt;?xml encoding="ISO-8859-1"?&gt;
&lt;!ELEMENT courses (course+)&gt;
&lt;!ELEMENT course (title, description, pre-requisite*)&gt;
&lt;!ATTLIST course cid ID #REQUIRED&gt;
&lt;!ELEMENT title (#PCDATA)&gt;
&lt;!ELEMENT description (#PCDATA)&gt;
&lt;!ELEMENT pre-requisite EMPTY&gt;
&lt;!ATTLIST pre-requisite cref IDREFS #REQUIRED&gt;
</pre>

<p>Here is a sample document type declaration with internal subset:</p>

<pre>
&lt;!DOCTYPE courses [
     &lt;!ELEMENT courses (course+)&gt;
     &lt;!ELEMENT course (title, description, pre-requisite*)&gt;
     &lt;!ATTLIST course cid ID #REQUIRED&gt;
     &lt;!ELEMENT title (#PCDATA)&gt;
     &lt;!ELEMENT description (#PCDATA)&gt;
     &lt;!ELEMENT pre-requisite EMPTY&gt;
     &lt;!ATTLIST pre-requisite cref IDREFS #REQUIRED&gt;
]&gt;
</pre>

<p>This one has both internal and external subsets:</p>

<pre>
&lt;!DOCTYPE courses SYSTEM "http://www.example.com/courses.dtd" [
     <b>&lt;!ELEMENT pre-requisite (#PCDATA)&gt;</b>
]&gt;
</pre>

<hr/>

<h4>Stuff I left out from last section:</h4>

<p>The subsets include three types of things:</p>

<ul>
  <li>markup declarations</li>
  <li>conditional sections (* are specific to external subsets and external
  parameter entities)</li>
  <li>declaration separators</li>
</ul>

<p>What this is:</p>

<pre>
&lt;?xml version="1.0" encoding="ISO-8859-1" ?&gt;
</pre>

<p>This is a <em>text declaration</em>. You are already familiar with the syntax
for text declarations. They are similar to <em>XML declarations of
documents</em>; however, the <em>standalone</em> declaration is not valid,
<em>version</em> is optional, and <em>encoding</em> is required.</p>

<div class="remarkbox">
  <div class="rbtitle">note</div>
  <div class="rbcontent">
  <p>If I decide to implement validation then go back and read the
  source material which I'm summarizing on this page.</p>
  </div>
</div>

<p>You can use an internal subset to override an external subset.</p>

<hr/>

<h3>Markup Declarations</h3>

<p>Markup declarations declare elements types, attribute lists, entities, and
notations. ... When writing declarations, you will encounter a few wildcards,
which can be used in your grammar. Before examining element declarations,
you’ll learn more about the wildcards.</p>

<div class="remarkbox">
  <div class="rbtitle">cross reference</div>
  <div class="rbcontent">
  <p>Read about wildcards in "Validation (from XML book)."</p>
  </div>
</div>

<h4>Element Type Declaration</h4>

<pre>
&lt;!ELEMENT element_name contentspec&gt;
</pre>

<p>The <b>element_name</b> is exactly what it implies. It is the name of the
element you are defining. The <b>contentspec</b> defines what type of content,
if any, is valid for the element. It can take the value<br/> <em>EMPTY</em> or
<em>ANY</em> <em class="highlight">or may be a content model of the type mixed
or child.</em> EMPTY simply implies the element cannot contain content. Within
the document, the element must be an empty-element tag or must be a start and
end tag with nothing in between, not even white-space. ANY implies that any type
of content, including none at all, is allowed. You can use this when you have no
specific rules for an element. It doesn’t matter if there are child elements or
what their names are, and it doesn’t matter what other content may appear, as
long as the content follows the rules for allowable content in the XML
specification. Using the pre-requisite element as an example, in the external
subset it is empty; and in the internal subset, you want to allow any type of
content, so it takes the following forms:</p>

<pre>
&lt;!-- declaration from external subset requiring the element to be empty --&gt;
&lt;!ELEMENT pre-requisite EMPTY&gt;
&lt;!-- declaration from internal subset allowing any content for element --&gt;
&lt;!ELEMENT pre-requisite ANY&gt;
</pre>

<p>Mixed and child content model types are not as simple, as these are
user-written rules to which the element content must conform.</p>

<h5>Child Content Model</h5>

<p>An <em class="highlight">element that can contain only child elements</em>
and no other content, excluding insignificant whitespace, follows the child
content model.</p>

<div class="remarkbox">
  <div class="rbtitle">note</div>
  <div class="rbcontent">
  <p>When the author says: "follows the child content model" he is trying to
  emphasize that writing XML for some people means writing it this way
  where there is no loose text content floating around like the way HTML is.</p>
  </div>
</div>

<p>As mentioned in Chapter 2, whitespace is typically significant and consists
of spaces, tabs, carriage returns, and line feeds. When dealing with validation,
this whitespace is considered insignificant when it’s not used with any other
text. An element following this model would look like the following:</p>

<pre>
&lt;course&gt;
     &lt;title&gt;French II&lt;/title&gt;
     &lt;description&gt;Intermediate French&lt;/description&gt;
     &lt;pre-requisite&gt;
          ... some type of content
     &lt;/pre-requisite&gt;
&lt;/course&gt;
</pre>

<p>The pre-requisite element is not a required element because not all courses
have prerequisites. You could now write the element declaration for the course
element as follows:</p>

<pre>
&lt;!ELEMENT course (title, description, pre-requisite*)&gt;
</pre>

<p>The content specification, which defines the data content, for this
declaration is (title, description, pre-requisite*). This is a sequence list,
denoted by the list of elements separated by commas. Using a list means that
each of the types used must appear in a document in the exact order they are
specified in the sequence list. Based upon the wildcard used in the expression,
the content specification would translate to a course element that may contain
only the child element’s title, description, and any number, including zero
pre-requisite elements. These elements must appear in this order within a course
element. Therefore, the following fragment would not be valid according to this
declaration:</p>

<pre>
&lt;course&gt;
     &lt;description&gt;Intermediate French&lt;/description&gt;
     &lt;title&gt;French II&lt;/title&gt;
&lt;/course&gt;
</pre>

<p>The problem with this document is that according to the declaration, title
must come before the description element, which is not the case here. To allow
both ordering schemes, the declaration would need to define the two cases as
follows:</p>

<pre>
&lt;!ELEMENT course (((title, description) | (description, title)), pre-requisite*)&gt;
</pre>

<p>Notice the use of parentheses. Following the order of precedence, the course
element must contain either title followed by description or description
followed by title. Either of these variants then must be followed by zero or
more pre-requisite elements.</p>

<div class="remarkbox">
  <div class="rbtitle">cross reference</div>
  <div class="rbcontent">
  <p>See page called "Validation (from XML book)" for more examples.</p>
  </div>
</div>

<h5>Mixed Content Model</h5>

<p>Mixed content allows for <em class="highlight">PCDATA</em> (which can
include stuff other than characters), which stands for parsed character data,
<em class="highlight">and child elements.</em> Recall
that you must escape special characters such as &lt; and &amp; when
using them within parsed text sections. PCDATA is such a section. It can,
however, contain nonparsed character sections, such as comments, CDATA sections,
and PIs.</p>

<div class="remarkbox">
  <div class="rbtitle">another way to say this is:</div>
  <div class="rbcontent">
  <p>PCDATA is text-only content (which includes text sections, comments
  sections, CDATA sections, and PI sections).</p>
  </div>
</div>

<p>I'll show an example of an element type declaration for an XML element
that fits in the mixed content model; however, you should see the material on
the page Validation (from XML book):</p>

<pre>
&lt;!ELEMENT course_info (#PCDATA)&gt;
</pre>

<h4>Entity Declaration</h4>

<p>Entities are not only declared but can also be used within other
declarations. You will encounter two types of entities: <em>general
entities</em> and <em>parameter entities</em>.</p>

<p>Entity <em>references</em> reference the content of a declared entity. A
parsed general entity reference takes the form of &amp;name;, and a parameter
entity reference takes the form of %name;. Unparsed general entities, used with
the ENTITY attribute type (which is the only place they can be used), take no
special form and are referenced directly by name.</p>

<div class="remarkbox">
  <div class="rbtitle">note</div>
  <div class="rbcontent">
  <p>"parsed general entity" is equivalent to "parsed entity"</p>
  </div>
</div>

<p>You can declare an internal parsed entity in an internal subset in the
following manner:</p>

<pre>
&lt;!ENTITY name "replacement"&gt;
</pre>

<p>The replacement must be well-formed XML. This means replacement can include
entity references, character references, and parameter entity references.</p>

<p>Parameter entities are similar to general entities in the respect that they
are also used for replacement. Parameter entities, however, are used only within
a DTD. They allow for the replacement of grammar.</p>

<h4>Attribute-List Declaration</h4>

<p>Within the scope of validation, the declarations specify the name, type, and
any default value for attributes associated with an element. A declaration takes
the following form:</p>

<pre>
&lt;!ATTLIST element_name att_definition*&gt;
</pre>

<p>The att_definition includes the name of the attribute being defined as well
as the rules for the attribute. Note the * in the definition. You can define
multiple attributes within a single attribute-list declaration.</p>

<p>The att_definition can be broken down into Name AttType DefaultDecl, where
Name is the name of the attribute being defined, AttType is the type of
attribute, and DefaultDecl is the rule for the default value.</p>

<div class="remarkbox">
  <div class="rbtitle">note</div>
  <div class="rbcontent">
  <p>Read the page "Validation (from XML book)" for specifics on this.</p>
  </div>
</div>

<hr/>

<h3>Using DOM Extension</h3>

<p>See "Node Types" page for a table about the DOM objects which
correspond to each of the node types. Here is a list of the important
node type classes:</p>

<ul>
  <li>DOMAttr</li>
  <li>DOMComment</li>
  <li>DOMDocumentFragment</li>
  <li>DOMDocument</li>
  <li>DOMDocumentType</li>
  <li>DOMElement</li>
  <li>DOMEntity</li>
  <li>DOMEntityReference</li>
  <li>DOMProcessingInstruction</li>
  <li>DOMText</li>
</ul>

<p>See "Non-Node Objects" page for a table about DOM objects that do not
inherit from the node object. Here is a list of the important ones (class name
only):</p>

<ul>
  <li>DOMNodeList</li>
  <li>DOMNamedNodeMap</li>
  <li>DOMImplementation</li>
  <li>DOMException</li>
  <li>DOMCharacterData</li>
  <li>DOMXPath</li>
</ul>

<p>A <em>DOMDocument</em> object is the <b>root of the tree</b>. This
class/object not only serves to create, load, and save XML documents but also
contains the factory methods for creating other node type objects. The
constructor for this object takes the following form:</p>

<pre>
__construct([string version], [string encoding])
</pre>

<pre>
&#36;dom = new DOMDocument('1.0');
</pre>

<p>This is equivalent to the following XML declaration:</p>

<pre>
&lt;?xml version="1.0"?&gt;
</pre>

<p>Using this object, a tree can either be manually created using the DOM API or
be loaded from an XML document (string/file).</p>

<pre>
&#36;xmldata = '&lt;?xml version="1.0"?&gt;
&lt;root&gt;
   &lt;child&gt;contents&lt;/child&gt;
&lt;/root&gt;';

&#36;dom-&gt;<b>loadXML</b>(&#36;xmldata, <b>LIBXML_NOBLANKS</b>);
</pre>

<p>Given an already instantiated DOMDocument and the string &#36;xmldata
containing the XML document to load, the <em>loadXML()</em> method populates
the tree while also removing all blanks, which are the insignificant
whitespaces. That is because the <em>LIBXML_NOBLANKS</em> parser option was
used.</p>

<p>The <em>load()</em> method works in the same way as the loadXML() method,
except a URI is passed as the first parameter. As you probably recall from
Chapter 5, you use PHP streams when loading URIs, allowing for more than the
typical file and http protocols to be used. If the contents of the &#36;xmldata
string from the previous example were contained within the file xmldata.xml, you
could build the tree in the following ways depending upon where the file was
located:</p>

<pre>
/* File located in current script directory */
&#36;dom-&gt;<b>load</b>('xmldata.xml', LIBXML_NOBLANKS);

/* File loaded using absolute path */
&#36;dom-&gt;load('file:///tmp/xmldata.xml', LIBXML_NOBLANKS);

/* File loaded from http://www.example.com/xmldata.xml */
&#36;dom-&gt;load('http://www.example.com/xmldata.xml', LIBXML_NOBLANKS);
</pre>

<p>A DOMDocument object does not always need to be instantiated to load a tree.
These methods may also be called statically. The following examples illustrate
how to use the methods statically, which results in the same tree structure for
the &#36;dom objects as previously shown. (I’ve removed the XML declaration for
brevity.)</p>

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
<em>preserveWhiteSpace</em> property:</p>

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
same way as an XML-based tree. The methods are <em>loadHTML()</em> and
<em>loadHTMLFile()</em>.
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

<p>To output a document containing a tree as XML, you’ll use the function
<em>saveXML()</em> to output the contents to a string and the function
<em>save()</em> to output to a URI.</p>

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

<p>Running this code, you might be surprised to see &#36;bytes equal to 58.
Whether a document was loaded with an XML declaration or the version and
encoding parameters were passed when creating a document, an XML declaration is
present when serializing the document with at least the version parameter,
defaulting to 1.0, set.</p>

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
format. The methods <em>saveHTML()</em> and <em>saveHTMLFile()</em> perform this
operation. The
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

<p>Navigation all begins with a <em>DOMDocument object</em>. These objects have
no attributes; they have only child nodes. At a minimum, all XML documents must
have a document element, but as mentioned in previous chapters, a document can
also have a DTD and any number of comment and PI nodes. You can access these
nodes using any of the many child properties and methods available from the base
DOMNode class. The body of the document is the most commonly accessed and
modified portion of the tree. Before examining how to access child nodes, which
will be covered later in the “Moving Within the Tree” section, you will first
see how to easily access the body.</p>

<p>The <em>document element</em>, like the document node, is a focal point in an
XML document. Being the root of the body for the document, it is a node with a
fixed position—the entry point for the body and universally accessible. Objects
derived from the DOMNode class are able to access the <em>documentElement
property</em>, which returns the document element as a DOMElement to also
navigate back to the document element.</p>

<pre>
&#36;root = &#36;dom-&gt;documentElement;
</pre>

<p>This call returns a DOMElement object, which is the book element node, and
sets it to the variable &#36;root. Armed with the document element, you can now
explore the rest of the body.</p>

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
constant. You can find the complete list of node type constants in Appendix
B.</p>

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

<p>The property <em>nodeValue</em> offers access to the contents of certain
nodes. <em class="highlight">Nodes having values are attributes, CDATA
sections, comments, PIs, and text. This is according to the specification. For
convenience, the DOM implementation in PHP 5 allows you to access this
property by element node as well:</em></p>

<pre>
print &#36;root-&gt;nodeValue."&#92;n";
</pre>

<p>For an element node the string returned is a concatination of all the
text in the element and its children. Keep in mind that its children may
include text nodes that hold insignificant white space.</p>

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

<p>All child nodes can be returned as a <em>DOMNodeList</em> using the
<em>childNodes property</em>. <em class="highlight">An object of the
DOMNodeList class is an iterable object;</em> you can access the child nodes
using the <em>DOMNodeList::item method</em> to retrieve a specific node from
the list or even the iterator functions in PHP.</p>

<p>You can also access a subtree directly using the <em>firstChild</em> and
<em>lastChild</em> properties. Rather than having to retrieve the entire
collection of children, these properties are quick ways to access the start or
end of the subtree:</p>

<pre>
&#36;first = &#36;root-&gt;firstChild;
&#36;last = &#36;root-&gt;lastChild;
</pre>

<p>Using the &#36;first object created in the previous section, you can access
the sibling nodes using the <em>nextSibling</em> property:</p>

<pre>
&#36;node = &#36;first;
while(&#36;node) {
   if (&#36;node-&gt;nodeType == XML_ELEMENT_NODE) {
      print &#36;node-&gt;nodeName."&#92;n";
   }
   &#36;node = &#36;node-&gt;nextSibling;
}
</pre>

<p>The <em>previousSibling</em> property allows navigation to be performed in
reverse:</p>

<pre>
&#36;node = &#36;last;
while(&#36;node) {
   if (&#36;node-&gt;nodeType == XML_ELEMENT_NODE) {
      print &#36;node-&gt;nodeName."&#92;n";
   } &#36;node = &#36;node-&gt;previousSibling;
}
</pre>

<p>Nodes can also perform ascending movement within a tree. Every node within a
document has a parent with the exception of the document node. You can access
the parent using the <em>parentNode property</em>:</p>

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
      if (! &#36;node-&gt;<b>parentNode</b>-&gt;isSameNode(&#36;root)) {
         print "ERROR: Parent Node Test FAILED";
         break 2;
      }
      &#36;node = &#36;node-&gt;nextSibling;
   }
   print "All parent node tests PASSED";
} while(0);
</pre>

<div class="remarkbox">
  <div class="rbtitle">note</div>
  <div class="rbcontent">
  <p>Note that the cascading property/method dereferencers collapse
  from left to right in the sample code above.</p>
  </div>
</div>

<p>The <em>isSameNode() method</em> tests the current node against the node
passed as an argument to determine whether they are the same node. By “same
node,” I mean the nodes must be the same node within the document.</p>

<p>Nodes have <em class="highlight">direct access to</em> their associated
document through the <em>ownerDocument property</em>. Although the body is
accessible using the
<em>documentElement property</em>, the document node is still an important node
even when not needing or using a DTD.</p>

<pre>
&#36;node = &#36;root-&gt;ownerDocument;
print &#36;node-&gt;nodeName."&#92;n";
</pre>

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
the total number of elements. Using <em>dereferencing</em>
(available in PHP 5), the element at the current index, &#36;x, is retrieved,
and the nodeValue for the node is printed.</p>

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

<p>Attributes inherit the same methods and properties from the DOMNode class as
other node types, but they are not accessed in the same manner as other nodes in
a document. As you have seen so far, nodes are traversed through children of
nodes. Attributes are different because they are not children of elements, which
is the only node type from which attributes may reside; rather, attributes,
conceptually, are properties of elements. You access them through their own set
of properties and methods.</p>

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

<p>Just like a DOMNodeList, the position could also have been used to
access the attribute. Using a DOMNamedNodeMap, however, the items are unordered,
so you have no guarantee that an item at a certain position is the item for
which you are looking.</p>

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

<p>You have two ways to create element nodes. One is to use the
<em class="highlight">factory methods</em> from the DOMDocument object, and the
other is <em class="highlight">direct instantiation</em>. According to the
specification, nodes must be associated with a document. The factory methods
follow this rule.</p>

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
node before.</p>

<p>With the author element complete with content, you can now insert it into
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

<p>Equivalent methods for attribute creation exist for a DOMDocument object as
for element creation. Currently (though this may change in future version of
PHP), you cannot create attributes <em class="highlight">with values</em> using
the factory methods. The
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

<p>Text nodes are simple nodes, because they <em class="highlight">cannot have
child nodes or attributes</em>. In other words, they simply contain text
content. This does not mean they offer little functionality, though. You can
use the text nodes to set content as well as <em class="highlight">perform
string functions.</em> You create and insert them in the same manner as element
nodes. You can create them either using a factory method from a DOMDocument
object or using the new keyword. You can insert them using the normal
appendChild() and insertBefore() methods.</p>

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

<p>Use <em>removeChild</em> to remove a child node.</p>

<pre>
&#36;root-&gt;removeChild(&#36;child2);
</pre>

<p>Use <em>replaceChild</em> to replace a child node.</p>

<pre>
&#36;oldchild = &#36;root-&gt;replaceChild(new DOMElement("newchild", "new content"), &#36;child3);
</pre>

<p>Samples of working with document fragments:</p>

<pre>
/* Create a DOMDocumentFragment */
&#36;frag = &#36;dom-&gt;createDocumentFragment();
&#36;frag = new DOMDocumentFragment();
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

EOPAGESTR5;
echo $page_str;

site_footer();

?>