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


site_header("SAX PHP Parser");

$page_str = <<<EOPAGESTR5

<p>The Simple API for XML is widely used to parse XML documents. It is an
event-based API, which means that the parser calls designated functions after
it recognizes a certain trigger in the event stream.</p>

<p>SAX has an interesting history, especially in contrast to the DOM. The SAX
API is not shepherded by an official standardizing body. Instead, it was
hammered out by a group of programmers on the XML-DEV mailing list, many of
whom had already implemented their own XML parsers (in Java first!) without a
standard API. You can learn more at the Web sites of SAX team members, such as
www.saxproject.org.</p>

<p>SAX works from a number of event hooks supplied by you via PHP. As the
parser goes through an XML document, it recognizes pieces of XML such as
elements, character data, and external entities. Each of these is an event.
If you have supplied the parser with a function to call for the particular kind
of event, it pauses to call your function after it reaches that event. The
parsed data associated with an event is made available to the called function.
After the event-handling function finishes, the SAX parser continues through
the document, calling functions on events, until it reaches the end. This
process is unidirectional from beginning to end of the document — the parser
cannot back up or loop.</p>

<p>A very simple example is an event hook that directs PHP to recognize the XML
element &lt;paragraph&gt;&lt;/paragraph&gt; and substitute the HTML tags
&lt;p&gt;&lt;/p&gt; around the character data. If you wrote this event hook, you
could not specify a particular paragraph — instead, the function is called for
every instance of this event.</p>

<p>The parser behind the scenes in the PHP SAX extension is libxml2, which you
can read about on its project site at www.xmlsoft.org.</p>

<p>Prior to version 5, PHP used James Clark’s expat, a widely used XML parser
toolkit. More information about expat can be found on Clark’s Web site at
www.jclark.com/xml/. If you compile with libxml2, you should be able to use all
your PHP4 SAX code in PHP5 without problems.</p>

<div class="remarkbox">
  <div class="rbtitle">Caution</div>
  <div class="rbcontent">
  <p>Unfortunately, the term parser can refer either to a software library such
  as libxml2, or to a block of XML-handling functions in PHP. Verbs such as
  create and call indicate the latter, more specific meaning. Any PHP XML
  function that uses the term parser also refers to the latter meaning.</p>
  </div>
</div>

<h2>Using SAX</h2>

<p>How you use the SAX will depend on your goals, but these steps are
common:</p>

<ol>
  <li>Determine what kinds of events you want to handle.</li>
  <li>Write handler functions for each event. You almost certainly want to
  write a character data handler, plus start element and end element
  handlers.</li>
  <li>Create a parser by using xml_parser_create() and then call it by using
  xml_parse().</li>
  <li>Free the memory used up by the parser by using xml_parser_free().</li>
</ol>

<p>The simple example in Listing 40-7 shows all the basic XML functions in
use.</p>

<p>Listing 40-7: A simple XML parser (simpleparser.php)</p>

<pre>
&lt;?php
&#36;file = "recipe.xml";

// Call this at the beginning of every element
function startElement(&#36;parser, &#36;name, &#36;attrs) {
  print "&lt;B&gt;&#36;name =&gt;&lt;/B&gt; ";
}

// Call this at the end of every element
function endElement(&#36;parser, &#36;name) {
  print "&#92;n";
}

// Call this whenever there is character data
function characterData(&#36;parser, &#36;value) {
  print "&#36;value&lt;BR&gt;";
}

// Define the parser
&#36;simpleparser = xml_parser_create();
xml_set_element_handler(&#36;simpleparser, "startElement", "endElement");
xml_set_character_data_handler(&#36;simpleparser, "characterData");

// Open the XML file for reading
if (!(&#36;fp = fopen(&#36;file, "r"))) {
  die("could not open XML input");
}

// Parse it
while (&#36;data = fread(&#36;fp, filesize(&#36;file))) {
  if (!xml_parse(&#36;simpleparser, &#36;data, feof(&#36;fp))) {
    die(xml_error_string(xml_get_error_code(&#36;simpleparser)));
  }
}

// Free memory
xml_parser_free(&#36;simpleparser);
?&gt;
</pre>

<h2>SAX options</h2>

<p>The XML parser in the SAX API has two configurable options: one for case
folding and the other for target encoding.</p>

<p>Case folding is the residue of a series of past decisions and may not be
relevant now that XML has been definitely declared case sensitive. Early
versions of SGML and HTML were not case sensitive and, therefore, employed case
folding (making all characters uppercase or lowercase during parsing) as a
means of getting a uniform result to compare. This is how your browser knew to
match up a &lt;P&gt; tag with a &lt;/p&gt; tag. Case folding fell out of favor due to
problems with internationalization, so after much debate XML was declared case
sensitive. When case folding is enabled, node names passed to event handlers
are turned into all uppercase characters. A node named mynode would be received
as MYNODE. When case folding is disabled, a &lt;paragraph&gt; tag will not match a
&lt;/PARAGRAPH&gt; closing tag.</p>

<div class="remarkbox">
  <div class="rbtitle">Note</div>
  <div class="rbcontent">
    <p>Case folding is enabled by default, which violates the XML 1.0
    specification. Unless you disable it by using xml_parser_set_option() as
    explained in a moment, your event handlers receive tags in uppercase
    letters.</p>
  </div>
</div>

<p>Event handlers receive text data from the XML parser in one of three
encodings: ISO-8859-1, US-ASCII, or UTF-8. The default is ISO-8859-1. The
encoding of text passed to event handlers is known as the target encoding.
This is by default the same encoding as in the source document, which is known
as the source encoding. You can change the target encoding if you need to
process the text in an encoding other than the encoding it was stored in.</p>

<p>Encoding options are retrieved and set with the functions
xml_parser_get_option() and xml_parser_set_option(). Case folding is controlled
by using the constant XML_OPTION_CASE_FOLDING, and target encoding by using the
constant XML_OPTION_ TARGET_ENCODING.</p>

<div class="remarkbox">
  <div class="rbtitle">PHP and Internationalization</div>
  <div class="rbcontent">
    <p>Computer programs store letters as integers, which they convert back to
    letters according to encodings. Early programs used English, which
    conveniently needs only one byte (actually only seven bits) to represent
    all the common letters and symbols. This encoding standard was promulgated
    in 1968 as ASCII (American Standard Code for Information Interchange).</p>
    <p>However, programmers soon found that English has an unusually small
    number of characters, and thus the only languages that can be expressed
    with any completeness in ASCII are Hawaiian, Kiswahili, Latin, and American
    English. Ever since then, programmers concerned with internationalization
    have tried to promote encoding standards that promise to assign a unique
    integer to every one of the letters of every one of the world’s
    alphabetical languages. The result of this effort is referred to as
    Unicode.</p>
    <p>The three encodings supported by PHP’s XML extension are ISO-8859-1,
    US-ASCII, and UTF-8. US-ASCII is the simplest of these, a slight renaming
    of the original 7-bit ASCII set. ISO-8859-1 is also known as the Latin1,
    Western, or Western European encoding. It can represent almost all western
    European languages adequately. UTF-8 allows the use of up to 4 bytes to
    represent as many of the world’s languages as possible. If your XML
    document is written in Han-gul or Zulu, you have no choice but to use
    UTF-8.</p>
  </div>
</div>

<p>In the following example, we create an XML parser that reads in data as
ASCII, turns off case folding, and spits out the output as UTF-8.</p>

<pre>
&#36;new_parser = xml_parser_create('US-ASCII');
&#36;case_folding = xml_parser_get_option(XML_OPTION_CASE_FOLDING);
echo &#36;case_folding;
&#36;change_folding = xml_parser_set_option(&#36;new_parser,
  XML_OPTION_CASE_FOLDING,0);

&#36;target_encoding = xml_parser_get_option(XML_TARGET_ENCODING);
echo &#36;target_encoding;
&#36;change_encoding = xml_parser_set_option(&#36;new_parser,
  XML_OPTION_TARGET_ENCODING, 'UTF-8');
</pre>

<h2>SAX functions</h2>

<p>Table 40-6 lists the most important SAX functions, with descriptions of
what they do.</p>

<table class="events" width="678">
  <caption>Table 40-6: XML SAX Function Summary</caption>
  <tr>
    <th>Function</th>
    <th>Behavior</th>
  </tr>
  <tr>
    <td>xml_parser_create([encoding])</td>
    <td>This function creates a new XML parser instance. You may have several
    distinct parsers at any time. The return value is an XML parser or false on
    failure. Takes one optional argument, a character-encoding identifier
    (such as UTF-8). If no encoding is supplied, ISO-8859-1 is assumed.</td>
  </tr>
  <tr>
    <td>xml_parser_free(parser)</td>
    <td>Frees the memory associated with a parser created by
    xml_parser_create().</td>
  </tr>
  <tr>
    <td>xml_parse(parser, data[,final])</td>
    <td>This function starts the XML parser. Its arguments are a parser created
    by using xml_parser_create(), a string containing XML, and an optional
    finality flag. The finality flag indicates that this is the last piece of
    data handled by this parser.</td>
  </tr>
  <tr>
    <td>xml_get_error_code(parser)</td>
    <td>If the parser has encountered a problem, its parse fails. Call this
    function to find out the error code.</td>
  </tr>
  <tr>
    <td>xml_error_string(errorcode)</td>
    <td>Given an error code returned by xml_get_error_code(), it returns a
    string containing a description of the error suitable for logging.</td>
  </tr>
  <tr>
    <td>xml_set_element_handler(parser, start_element_handler,
    end_element_handler)</td>
    <td>This function actually sets two handlers, which are simply functions.
    The first is a start-of-element handler, which has access to the name of
    the element and an associative array of its elements. The second is an
    end-of-element handler, at which time the element is fully parsed.</td>
  </tr>
  <tr>
    <td>xml_set_character_data_handler (parser, cd_handler)</td>
    <td>Sets the handler function to call whenever character
    data is encountered. The handler function takes a string containing the
    character data as an argument.</td>
  </tr>
  <tr>
    <td>xml_set_default_handler (parser, handler)</td>
    <td>Sets the default handler. If no handler is specified for an event, the
    default handler is called if it is specified. Takes as arguments the parser
    and a string containing unhandled data, such as a notation declaration or
    an external entity reference.</td>
  </tr>
</table>

EOPAGESTR5;
echo $page_str;

site_footer();

?>