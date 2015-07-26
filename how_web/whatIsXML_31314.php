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


site_header("What Is XML? (from PHP Book)");

$page_str = <<<EOPAGESTR5

<p>XML stands for eXtensible Markup Language. XML is a form of SGML, the
Standard Generalized Markup Language, but you don’t need to know anything about
SGML to use XML. It defines syntax for structured documents that both humans
and machines can read.</p>

<div class="remarkbox">
  <div class="rbtitle">Note</div>
  <div class="rbcontent">
  <p>Our explanation of XML will necessarily be extremely brief (because this
  is a book about PHP rather than XML). For those who want to learn more, we
  highly recommend Elliotte Rusty Harold’s XML 1.1 Bible, Third Edition (Wiley,
  2004). Although this book is neither short nor a specific guide to
  programming XML-based applications, it will give you a firm conceptual grasp
  of XML that should set you up nicely for any particular XML-based task.</p>
  </div>
</div>

<p>Perhaps the easiest way to understand XML is to think about all the things
HTML can’t do. HTML is also a markup language, but HTML documents are anything
but structured. HTML tags (technically known as elements) and attributes are
just simple identification markers to the browser. For instance, a pair of
matched &lt;H1&gt; and &lt;/H1&gt; tags designate a top-level heading. Browsers interpret
this to mean you want heading text to be displayed in a really big, bold,
possibly italicized font. HTML does not, however, indicate whether the text
between those tags is the title of the page, the name of the author, an
invitation to enter the site, a pertinent quotation, a promise of special sale
prices, or what. It’s just some text that happens to be big.</p>

<p>One implication of HTML’s lack of structure is that search engines have
little built-in guidance about what’s important on each page of your site or
what each chunk of text means in relation to the others. They use various
methods to guess, none of which are foolproof. &lt;META&gt; tags are notoriously
prone to abuse — porn sites often load popular but irrelevant search terms
into their headers to fool unwary Web surfers — and spiders can end up giving
too much weight to portions of the page that designers might think are
unimportant. If XML becomes ubiquitous, it could eliminate many of these
problems and lead the way to much more meaningful Web searching.</p>

<p>Let’s say you work for a content Web site that has just signed a major
distribution deal with a top-five portal. After you wake up from the champagne
hangover, you’re faced with the hard question of how you plan to deliver the
content. HTML isn’t going to do the job: Obviously the portal’s page design
and Web serving technology are different from your site’s, and they won’t be
able to just plug your HTML into theirs. Just to make things really
interesting, let’s presume you and Big Portal Company use different programming
languages, different data stores, different HTML editors, different style
sheets — in short, different everything. The necessary bridge is a
data-exchange format, which is easy for you to output with your technical
setup, clearly understood by both parties with existing software, and equally
easy for the Big Portal Company to convert to its own purposes and designs.
XML is that data-exchange format.</p>

<p>You could, of course, write a script to dump data from your data store into
a tab-delimited file. Then you could write out the details of your custom data
format and send it with the tab-delimited file to Big Portal Company. There one
of its engineers would try to figure out your schema and write code to
transform your data into its format. However, anyone who has actually done this
knows how much fiddly work it is, how many tests need to be performed, how much
time even the tiniest error can suck up. On the other hand, you could just
output your data in XML, and the Big Portal Company engineer could write a very
short script — perhaps just three functions long — to transform your XML tags
to its corresponding XML tags. Then Big Portal Company could treat the data
just like its own data. XML is an attempt to move toward a common language and
set of methods for performing tasks like these, instead of having data-exchange
involve a series of custom jobs each time.</p>

<p>We hope these examples begin to answer the “Why XML?” question. If you
forget the hype and focus on what problems XML might begin to solve, you’ll be
in a much better position to assess whether it can help you today or sometime
in the future. In the simplest terms, XML is a flexible data-exchange format
that is not dependent on any particular software or domain, can be parsed
easily by both machines and humans, and allows content providers to include
information about the structure of the data along with the data itself.</p>

<p>The next question about XML is typically, “What does XML look like anyway?”
Actually, XML looks a lot like HTML. A simple XML file, such as the one shown
in Listing 40-1, is easy for HTML users to understand.</p>

<pre>
&lt;?xml version="1.0"?&gt;
&lt;book&gt;
 &lt;publisher&gt;IDG Books&lt;/publisher&gt;
 &lt;title&gt;PHP5 Bible&lt;/title&gt;
 &lt;chapter title="PHP and XML"&gt;
  &lt;section title="What is XML?"&gt;
   &lt;paragraph&gt;
If you know HTML, you're most of the way to understanding XML.
   &lt;/paragraph&gt;
   &lt;paragraph&gt;
They are both markup languages, but XML is more structured than HTML.
   &lt;/paragraph&gt;
  &lt;/section&gt;
 &lt;/chapter&gt;
&lt;/book&gt;
</pre>

<p>As you can see, XML has tags and attributes and the hierarchical structure
that you’re used to seeing in HTML. In XML, each pair of tags
(&lt;paragraph&gt;&lt;/paragraph&gt;) is known as an element. Actually, this
is true in HTML, too, but most people strongly prefer the term tag (the
construction that marks an element) over element (the conceptual thing that is
being marked by a tag) — we’re not picky. Use whatever term you want as long as
you know what you mean. The biggest difference is that XML tags are
self-defined; they carry absolutely no display directive to the Web browser or
other viewing application.</p>

<p>XML makes the following minimal demands:</p>

<ul>
  <li>There must be a single root element that encloses all the other elements,
  similar to &lt;HTML&gt;&lt;/HTML&gt; in HTML documents. This is also
  sometimes called the document element.</li>
  <li>Elements must be hierarchical. That is, &lt;X&gt; &lt;Y&gt; &lt;/Y&gt;
  &lt;/X&gt; is allowed, but &lt;X&gt; &lt;Y&gt; &lt;/X&gt; &lt;/Y&gt; is not.
  In the first example, &lt;X&gt; clearly contains all of &lt;Y&gt;. In the
  second example, &lt;X&gt; and &lt;Y&gt; overlap. XML does not allow
  overlapped tags.</li>
  <li>All elements must be deliberately closed (in contrast to HTML, which
  allows some unclosed elements such as &lt;OPTION&gt; or &lt;LI&gt;). This can
  be accomplished with a closing tag (&lt;title&gt;&lt;/title&gt;) as in HTML
  or by using an XML feature with no HTML equivalent called a self-closing
  element (&lt;logo href="graphic.jpg"/&gt;). A self-closing element is also
  known as an empty element.</li>
  <li>Elements can contain elements, text, and other data. If an element
  encloses something that looks like it might be XML — such as &lt;hello&gt;
  — but isn’t, or if you don’t want something parsed, it must be escaped.</li>
</ul>

<div class="remarkbox">
  <div class="rbtitle">Caution</div>
  <div class="rbcontent">
  <p>The &amp;, &lt;, &gt;, ', and " characters are all restricted in XML. You
  can use them in your data by escaping them — using codes such as &amp;amp;
  and &amp;lt; — or by putting them in CDATA sections, which we discuss in the
  section “Documents and DTDs,” later in this chapter.</p>
  </div>
</div>

<p>In addition to these mandatory requirements for what is called
well-formedness, the XML standard also suggests that XML documents should start
with an identifying XML declaration. This is a processing instruction giving
the MIME type and version number, such as &lt;?xml version="1.0"?&gt;. This is
not required, but some parsers complain if it isn’t present. Also, XML is case
sensitive; some variants, such as XHTML, require lowercase tags and attributes.
Lowercase tags are not absolutely required by the XML standard itself, but
unless you have a good reason to do otherwise you should use lowercase tags and
attributes.</p>

<div class="remarkbox">
  <div class="rbtitle">Note</div>
  <div class="rbcontent">
  <p>It’s the XML declaration, and other processing instructions with the same
  format, that prevents you from using PHP’s short tags with XML. Because the
  two tag styles are identical (&lt;? ?&gt;), it would be unclear whether this
  character sequence set off a PHP block or an XML processing instruction.</p>
  </div>
</div>

<p>XML documents are usually text. They can contain binary data, but they
aren’t really meant to. If you want to put binary data in your XML documents,
you have to encode it first and decode it later. Note that including binary
data may break some of the platform-independence of pure XML.</p>

EOPAGESTR5;
echo $page_str;

site_footer();

?>