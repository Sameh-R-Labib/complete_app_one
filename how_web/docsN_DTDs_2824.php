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


site_header("Documents & DTDs");

$page_str = <<<EOPAGESTR5

<div class="remarkbox">
  <div class="rbtitle">cross-reference</div>
  <div class="rbcontent">
  <p>Character encoding is a related topic. Please, read about it.</p>
  <p>"XML Structure" page should be read after you read this page. That
  page contains things from the XML book which did not get included
  elsewhere.</p>
  </div>
</div>

<div class="remarkbox">
  <div class="rbtitle">note-to-self</div>
  <div class="rbcontent">
    <p>The title of this page would lead you to think I will cover
    the topic of XML documents here. Instead, you will find it in
    "What is XML? (from PHP Book)" page on this website.</p>
  </div>
</div>

<div class="remarkbox">
  <div class="rbtitle">Note</div>
  <div class="rbcontent">
    <p>The two Unicode formats, which all parsers must accept, are
    UTF-8 and UTF-16, although you can use other character encodings as
    long as they comply with Unicode.</p>
  </div>
</div>

<div class="remarkbox">
  <div class="rbtitle">Note</div>
  <div class="rbcontent">
  <p>"HTML entities" are also "character references."</p>
  <p>Some characters are not allowed anywhere in an XML document file
  except where they are used for a tag because they hold special meanings
  for XML parsers. These characters must be substituted to
  character references. You can express character references in two
  ways: using decimal notation or hexadecimal notation. For example:</p>
  <ul>
    <li>The character A in decimal format is &amp;#65;.</li>
    <li>The character A in hexadecimal format is &amp;#x41;.</li>
  </ul>
  </div>
</div>

<div class="remarkbox">
  <div class="rbtitle">Note</div>
  <div class="rbcontent">
  <p>Names can't just be made up using any characters. When I say "names" I'm
  talking about character sequences used for XML tags and such.</p>
  <p>The term name, as used here for explaining XML syntax,
  defines the valid sequence of characters that you can use. A name begins with
  an alphabetical character, an underscore, or a colon and is followed by any
  combination of alphanumeric characters, periods, hyphens, underscores, and
  colons, as well as a few additional characters defined by CombiningChar and
  Extender within the XML specification.</p>
  <p>Names beginning with the case-insensitive xml are also reserved by the
  current and future XML specifications. For example, names already in use
  include xmlns and xml. Basically, it is not wise to use a name beginning with
  those three letters. It is also not good practice to use colons in names.
  Although you will find people using them, especially when using the DOM and
  not using namespace-aware functionality, using colons can lead to problems
  when not used for namespace purposes.</p>
  </div>
</div>

<p>As we explained earlier, the requirements for a well-formed XML document are
fairly minimal. However, XML documents have another possible level of
“goodness,” which is called validity. A valid XML document is one that conforms
to certain stated rules that together are known as a document type definition
(DTD).</p>

<p>To get in the mood to understand the value of DTDs, imagine that you are the
head of an open source project that exists to make books and other documents
freely available in electronic form on the Internet. You’re very excited about
XML from the moment you learn about it because it seems to meet your need for a
data exchange format that can adapt easily to new display technologies as they
evolve. Your group members vote to encode all the project’s books and documents
in XML, and soon the XMLized documents start to pour in.</p>

<p>But when you look at the first couple of submissions, you get a rude shock.
One of them is in the same format as Listing 40-1, earlier in this chapter, but
one of them looks like what you see in Listing 40-2.</p>

<p>Listing 40-2: A book in XML format</p>

<pre>
&lt;?xml version="1.0"?&gt;
&lt;book title="PHP5 Bible"&gt;
 &lt;publisher name="Wiley Publishing"/&gt;
 &lt;chapter number="40"&gt;
  &lt;chapter_title&gt;PHP and XML&lt;/chapter_title&gt;
  &lt;p&gt;
   &lt;sentence&gt;If you know HTML, you're most of the way to
understanding XML.&lt;/sentence&gt;
   &lt;sentence&gt;They are both markup languages, but XML is more
structured than HTML.&lt;/sentence&gt;
  &lt;/p&gt;
 &lt;/chapter&gt;
&lt;/book&gt;
</pre>

<p>The two XML files express similar, but not identical, hierarchical
structures using similar but not identical tags. This is the potential downside
of the self-defined markup tags that XML enables: random variation that makes
it difficult to match up similar kinds of information across files. You quickly
realize that you will need to implement some rules about what kinds of
information should be in a book file and what the relationships between these
elements will be. You’ve just realized you need a DTD.</p>

<p>A DTD describes the structure of a class of XML documents. A DTD is a kind
of formal constraint, guaranteeing that all documents of its type will conform
to stated structural rules and naming conventions. A DTD enables you to specify
exactly what elements are allowed, how elements are related, what type each
element is, and a name for each element. DTDs also specify what attributes are
required or optional, and their default values. You could of course just write
down these rules in a text file:</p>

<pre>
The top-level object of this document is a BOOK
A BOOK has one and only one TABLE OF CONTENTS
A BOOK has one and only one TITLE
A BOOK is composed of multiple CHAPTERS
CHAPTERS have one and only one CHAPTERTITLE
All CHAPTERTITLEs are listed in the TABLE OF CONTENTS
etc.
</pre>

<p>You could give a copy of the list to anyone who might need it. A DTD is just
a more concise, well-defined, generally agreed upon grammar in which to do the
same thing. It’s a useful discipline to apply to XML documents, which can be
chaotic because of their entirely self-defined nature. Furthermore, if you can
get a group of people to agree on a DTD, you are well on the way to having a
standard format for all information of a certain type. Many professions and
industries, from mathematicians to sheet-music publishers to human-resources
departments, are eager to develop such domain-specific information formats.</p>

<p>In our previous example, which uses XML to store books electronically, your
group members may have to argue for months before hashing out the details of a
DTD that perfectly describes the relationships between the table of contents,
chapters, titles and headings, indexes, appendices, sections, paragraphs,
forwards, epilogues, and so on. You can, of course, iterate on DTDs as
frequently as necessary.</p>

<p>But after your DTD is finalized, you can enjoy another value-add of XML.
You can now run any XML document through a so-called “validating parser” which
will tell you whether it’s meeting all the requirements of its DTD. So instead
of a human editor having to read each electronic book submission to see whether
it has the required elements and attributes in the correct relationship, you
can just throw them all into a parser and let it do the formal checking. This
won’t tell you anything about the quality of the content in the XML document,
but it will tell you whether the form meets your requirements.</p>

<p>In order to work with XML in PHP, you need to learn about the basic
structure of DTDs and the XML documents they describe whether you choose to
validate or not.</p>

<h2>The structure of a DTD</h2>

<p>A document type definition is a set of rules that defines the structure of a
particular group of XML documents. A DTD can be either a part of the XML
document itself (in which case it is an internal DTD), or it can be located
externally, in another file on the same server or at a publicly available URL
anywhere on the Internet (in which case it is an external DTD).</p>

<div class="remarkbox">
  <div class="rbtitle">Note</div>
  <div class="rbcontent">
  <p>Although a DTD can be internal (part of the XML document itself), making
  it external (a separate file) is usually better. DTDs are meant to define a
  class of documents, so separating them from the XML saves you from editing
  every XML document of that class if you need to change the DTD later on.
  Because demonstrating on an internal DTD is easier for readers to follow in a
  book format, however, we use both as examples in this chapter.</p>
  </div>
</div>

<p>You can start by looking at a simple XML document with an internal DTD in
Listing 40-3.</p>

<p>Listing 40-3: An XML document with internal DTD (recipe.xml)</p>

<pre>
&lt;?xml version="1.0"?&gt;

&lt;!DOCTYPE recipe [
&lt;!ELEMENT recipe (ingredients, directions, servings)&gt;
&lt;!ATTLIST recipe name CDATA #REQUIRED&gt;
&lt;!ELEMENT ingredients (#PCDATA)&gt;
&lt;!ELEMENT directions (#PCDATA)&gt;
&lt;!ELEMENT servings (#PCDATA)&gt;
]&gt;

&lt;recipe name ="Beef Burgundy"&gt;
 &lt;ingredients&gt;Beef&lt;/ingredients&gt;
 &lt;ingredients&gt;Burgundy&lt;/ingredients&gt;
 &lt;directions&gt;
 Add beef to burgundy. Serve.
 &lt;/directions&gt;
 &lt;servings&gt;12&lt;/servings&gt;
&lt;/recipe&gt;
</pre>

<p>We’ve divided the XML document into three subsections for easier reading.
The first section is the standard one-line XML declaration that should begin
every XML document. The second section is the internal DTD, marked by lines
beginning with the &lt;! sequence. The third section is the XML itself, strictly
speaking. For the moment, we are focusing on the second section, the DTD. In
our example, the stuff outside the square brackets is a document type
declaration (not to be confused with document type definition):
&lt;!DOCTYPE recipe [...]&gt;. The document type declaration gives information about
the DTD this document is using. Because this is an internal DTD, we simply give
the name of the root element (recipe) and then include the rest of the
definition within square brackets. If you are using an external DTD, however,
you use the document type declaration to state the type and location of the
DTD. Two example document type declarations referring to external DTDs are as
follows:</p>

<pre>
&lt;!DOCTYPE recipe SYSTEM "recipe.dtd"&gt;
&lt;!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
 "http://www.w3.org/TR/html4/loose.dtd"&gt;
</pre>

<p>External document type declarations give a root element name, the type
(SYSTEM, meaning on the server, or PUBLIC, meaning a standardized DTD)
and the location where it can be found. You are doubtless familiar with
document type declarations because, without exception, you always include one,
like the preceding example, in every single HTML or XHTML document you write —
right?</p>

<p>The DTD proper consists of the lines inside the square brackets. These lay
out the elements, element types, and attributes contained in the XML
document.</p>

<ul>
  <li>Element: A start and end tag pair — for example, &lt;b&gt; something &lt;/b&gt; — or an empty element (&lt;br/&gt;). Elements have types and sometimes content and attributes.</li>
  <li>Element Type: A constraint on the content and attributes of an element. A type can be used to specify what kind of data it can contain and to specify what attributes it can have.</li>
  <li>Attribute: A name and value pair associated with an element, in the form &lt;element attributename="attributevalue"&gt;.</li>
</ul>

<p>In the example DTD in Listing 40-3, we’ve declared that our root element,
recipe, contains three child elements — ingredients, directions, and servings
— and has one required attribute, name. Each child element is of the parsed
character data type, and the attribute is of the character data type.</p>

<p>If you wanted to split up Listing 40-3 into an XML document and an external
DTD, it would look much the same, except that, instead of providing the
definition in square brackets, you would give a reference to the external DTD
file. The result would look like Listings 40-4 and 40-5.</p>

<p>Listing 40-4: An XML document with external DTD (recipe_ext.xml)</p>

<pre>
&lt;?xml version="1.0"?&gt;
&lt;!DOCTYPE recipe SYSTEM "recipe.dtd"&gt;

&lt;recipe name ="Beef Burgundy"&gt;
 &lt;ingredients&gt;Beef&lt;/ingredients&gt;
 &lt;ingredients&gt;Burgundy&lt;/ingredients&gt;
 &lt;directions&gt;Add beef to burgundy. Serve.&lt;/directions&gt;
 &lt;servings&gt;12&lt;/servings&gt;
&lt;/recipe&gt;
</pre>

<p>Listing 40-5: An external DTD (recipe.dtd)</p>

<pre>
&lt;!ELEMENT recipe (ingredients, directions, servings)&gt;
&lt;!ATTLIST recipe name CDATA #REQUIRED&gt;
&lt;!ELEMENT ingredients (#PCDATA)&gt;
&lt;!ELEMENT directions (#PCDATA)&gt;
&lt;!ELEMENT servings (#PCDATA)&gt;
</pre>

<p>Because the XML used in both examples conforms to the internal and external
DTDs, both documents should be declared valid by a validating parser.</p>

<p>You could learn a lot more about the specifics of DTDs and XML documents,
but these basics should enable you to understand most of PHP’s XML
functions.</p>

<h2>Validating and nonvalidating parsers</h2>

<p>XML parsers come in two flavors: validating and nonvalidating. Nonvalidating
parsers care only that an XML document is well formed — that it obeys all the
rules for closing tags, quotation marks, and so on. Validating parsers require
well-formed documents as well, but they also check the XML document against a
DTD. If the XML document doesn’t conform to its DTD, the validating parser
outputs specific error messages explaining what has gone wrong.</p>

<p>PHP5’s SAX parser, libxml2, is nonvalidating (as was the expat parser used
in PHP4). That doesn’t mean that you should ignore DTDs. Going through the
process of creating a DTD for each of your document types is a good design
practice. It forces you to think out the document structure very carefully.
And if your documents ever need to go through a validating parser, you’re
covered. In fact, many experts recommend that you put all XML documents through
a validating parser even if you never plan to use one again.</p>

<p>Most validating parsers are written in Java and are a pain to set up and
use. The easiest way to validate your XML is to use an online validator. A
well-known one is the STG validator at www.stg.brown.edu/service/xmlvalid.</p>

<p>Actually, using Gnome libxml to validate an XML document is possible — but
it takes some work. Examples of validation using C are on the libxml Web site
(at www.xmlsoft.org).</p>

<div class="remarkbox">
  <div class="rbtitle">Cross-Referrence</div>
  <div class="rbcontent">
  <p>Parsers such as SAX, DOM and SimpleXML are covered on other pages.</p>
  </div>
</div>

EOPAGESTR5;
echo $page_str;

site_footer();

?>