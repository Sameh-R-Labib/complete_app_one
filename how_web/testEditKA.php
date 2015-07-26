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

<p>&amp; &#x65; Compared to other tree-based parsers (in PHP 5, the SimpleXML extension is
the only other native tree-based extension),
sections to illustrate how to navigate a document tree.</p>

<hr/>

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

<pre>
&#36;dom = new DOMDocument();
&#36;dom-&gt;load('mydocbook.xml');
</pre>

<div>
  <img src="http://www.gxsam11.net/web/how_web/domobview.jpg" width="458" height="551" alt="pic of example tree" />
</div>

<h3>Understanding the Document Element</h3>

<p>The <em>document element</em>, like the document node, is a focal point in an
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

<table class="events" width="678">
  <caption>Exponential Functions</caption>
  <tr>
    <th>Function</th>
    <th>Behavior</th>
  </tr>
  <tr>
    <td>pow()</td>
    <td>Takes two numerical arguments and returns the first argument raised to the
    power of the second. The value of pow(&#36;x, &#36;y)is x<sup>y</sup>.</td>
  </tr>
  <tr>
    <td>exp()</td>
    <td>Takes a single argument and raises e to that power. The value of exp(&#36;x)
    is e<sup>x</sup>.</td>
  </tr>
  <tr>
    <td>log()</td>
    <td>The "natural log" function. Takes a single argument and returns its base e logarithm.
If e<sup>y</sup> = x, then the value of log(&#36;x) is y.</td>
  </tr>
  <tr>
    <td>log10()</td>
    <td>Takes a single argument and returns its base-10 logarithm. If 10<sup>y</sup> = x,
    then the value of log10(&#36;x) is y.</td>
  </tr>
</table>

<p>information NULL when the called property is not applicable for the LONG ONE LINE STRING is not applicable for the LONG ONE LINE STRING node.</p>

<table class="events" width="678">
  <caption>Common Perl-Compatible Pattern Constructs</caption>
  <tr>
    <th>Construct</th>
    <th>Interpretation</th>
  </tr>
  <tr>
    <td>Simple literal character matches</td>
    <td>If the character involved is not special, Perl will match characters in sequence.
    The example pattern /abc/ matches any string that has the substring 'abc' in it.</td>
  </tr>
  <tr>
    <td>Character class matches:</td>
    <td>Will match a single instance of any of the characters between the brackets. For
    example, /[xyz]/ matches a single character, as long as that character is either x,
    y, or z. A sequence of characters (in ASCII order) is indicated by a hyphen, so that
    a class matching all digits is [0-9].</td>
  </tr>
  <tr>
    <td>Predefined character class abbreviations</td>
    <td>The patterns &#92;d will match a single digit (from the character class [0-9]), and
    the pattern &#92;s matches any whitespace character.</td>
  </tr>
  <tr>
    <td>Multiplier patterns</td>
    <td>
    <p>Any pattern followed by * means: "Match this pattern 0 or more times."</p>
    <p>Any pattern followed by ? means: "Match this pattern exactly once."</p>
    <p>Any pattern followed by + means: "Match this pattern 1 or more times."</p>
    <blockquote>Text in td blockquote.</blockquote>
    </td>
  </tr>
  <tr>
    <td>Anchoring characters</td>
    <td>The caret character ^ at the beginning of a pattern means that the pattern must
    start at the beginning of the string; the &#36; character at the end of a pattern means
    that the pattern must end at the end of the string. The caret character at the
    beginning of a character class [^abc] means that the set is the complement of the
    characters listed (that is, any character that is not in the list).</td>
  </tr>
  <tr>
    <td>Escape character '&#92;'</td>
    <td>
    <p>Any character that has a special meaning to regex can be treated as a simple
    matching character by preceding it with a backslash. The special characters that
    might need this treatment are:</p>
    <p>.&#92;+*&#63;[]^&#36;(){}=!|:</p>
    </td>
  </tr>
  <tr>
    <td>Parentheses</td>
    <td>A parenthesis grouping around a portion of any pattern means: "Add the substring
    that matches this pattern to the list of substring matches."</td>
  </tr>
</table>

<pre>
&lt;!DOCTYPE courses SYSTEM "http://www.example.com/courses.dtd" [
     <b>&lt;!ELEMENT pre-requisite (#PCDATA)&gt;</b>
]&gt;
</pre>

<h4>Node Name</h4>

<pre>
print &#36;dom-&gt;nodeName."&#92;n";
print &#36;root-&gt;nodeName."&#92;n";
</pre>

<p>The property <em>nodeValue</em> offers access to the contents of certain
nodes. <em class="highlight">Nodes having values are attributes, CDATA
sections, comments, PIs, and text. This is according to the specification. For
convenience, the DOM implementation in PHP 5 allows you to access this
property by element node as well:</em></p>

<pre>
                DOM in PHP 5

                        Rob
                        Richards



                        2005
/* Rest of Output Omitted for Brevity */
</pre>

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

<p>Using the &#36;first object created in the previous section, you can access
the sibling nodes using the nextSibling property:</p>

<pre>
&#36;result = &#36;dom-&gt;getElementsByTagNameNS("*", "*");
</pre>

<ul>
  <li>Key: 0<br/>Value: current-key</li>
  <li>Key: 1<br/>Value: current-value</li>
  <li>Key: 'key'<br/>Value: current-key</li>
  <li>Key: 'value'<br/>Value: current-value</li>
</ul>

<h5>Individual Attributes</h5>

<pre>
/* Access lang attribute value directly */
print "Attribute Value: ".&#36;root-&gt;getAttribute("lang")."&#92;n";

</pre>

EOPAGESTR5;
echo $page_str;

site_footer();

?>