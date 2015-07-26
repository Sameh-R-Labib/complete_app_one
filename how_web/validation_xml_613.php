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


site_header("Validation (from XML book)");

$page_str = <<<EOPAGESTR5

<p>By now, you have most likely heard that all XML documents must be well-formed
but that documents are not required to be valid. This chapter will explain what
it means for a document to be valid and will show how to create valid documents.
I will cover DTDs, XML Schemas, and Relax NG in depth and discuss the
differences between them.</p>

<h2>Introducing Validation</h2>

<p>A well-formed document is one that is written using legal XML syntax and
structure according to the XML specification. A valid document is one that is
well-formed and conforms to a structure outlined in a DTD or schema. You can
think of this as a database schema. A table definition defines the fields and
their data types, lengths, and defaults. Using primary and foreign keys, you
can also define a database structure. If someone tries to insert data that does
not fit the model, they’ll get an error.</p>

<p>Validation in XML works in almost the same way. The schema defines how an XML
document must look. It can define the order of elements in the document, what
child elements are valid for particular elements, and what type of content
particular elements can have. You can apply similar constraints to other pieces
of an XML document.</p>

<p>If you were receiving XML from some undefined source and were expecting a
document that looked like the following one, you would use validation to ensure
the document conforms to your expectations. The system you are processing the
documents with must have the document in this format; otherwise, it will cause
an error. Therefore, validating the document prior to processing is essential in
this case.</p>

<pre>
&lt;question number="1"&gt;
   &lt;query&gt;Is this XML?&lt;/query&gt;
   &lt;answer&gt;true&lt;/answer&gt;
&lt;/question&gt;
</pre>

<p>Validation allows you to describe a document in generic terms. You know that
this example’s document element must be the element question. The question
element must have a number attribute that can have an integer for its value.
Here you don’t care what the specific value is, just that the value is an
integer. The question element must also contain two elements, query and
answer, in that order. No other content is allowed for the question element.
The query element cannot have any attributes and can have only text content. You
don’t care what the text is, just that there is text and no XML markup. The
answer element cannot have any attributes and must contain true or false.
Validation allows you to take this verbal description of the constraints placed
on a document, write the description in a schema using the schema’s grammar (the
language it uses to describe a document), and then perform automated validation
of the document. You will be able to determine whether the document conforms to
your expectations before actually sending the document to be processed.</p>

<h2>Introducing Document Type Definitions</h2>

<p>Chapter 2 briefly touched on DTDs in respect to ID, IDREF, and IDREFS. These
are just a small aspect of DTDs. The main purpose of a DTD is to perform
document validation. Although other methods to perform document validation
exist, DTDs are part of the XML 1.0 specification so have been around for some
time now. Before getting under the hood of a DTD, though, you need to back up
and re-examine document type declarations, mentioned in Chapter 2.</p>

<h3>Document Type Declarations</h3>

<p>The document type declaration is not a DTD but is the declaration to declare
a DTD. It can include an internal subset, an external subset, or both. These
subsets together make up the document’s DTD. The difference between an internal
and external subset is, as their names imply, that an external subset is a
subset that is not defined within the document. The document must access the
subset from an external resource, such as from the file system or the network.
An internal subset is defined directly within the document. You may be
wondering why two different subsets exist. External subsets allow documents to
share common DTDs. If you were working at a large company, for example, you
might have a standard DTD for documents created within the company. Rather than
having to define the same DTD within each document, documents can reference a
common standard DTD via an external subset. As mentioned in Chapter 2, a
declaration looks like the following:</p>

<pre>
&lt;!DOCTYPE document_element definitions&gt;
</pre>

<p>The document_element is the root, or document element, of the body of the XML
document, and definitions is the internal and/or external subsets. The document
type declaration must contain the document_element and at least an internal or
external subset declaring the element; otherwise, the document type declaration
is not written properly and has no DTD to validate against. In the following
sections, you’ll examine external subsets and how they are declared.</p>

<h4>External Subsets</h4>

<p>External subsets are accessed through external IDs. The external ID includes
a system identifier and possibly a public identifier, which serve to locate the
external subset. The system literal is a URI that provides the specific location
of the subset. Note that the URI cannot be a fragment (which is a URI using the
# character to point to a specific portion of a document). You may be more
familiar with this when using anchors in HTML. Public identifiers allow the use
of some other identifier, which your parser would then translate to a URI. When
using public identifiers, a system identifier is also required in the event the
parser is unable to resolve the public identifier.</p>

<p>Listing 3-1 illustrates how to use both system and public identifiers. You
denote system identifiers, when not used with a public identifier, by using the
keyword SYSTEM. You denote a public identifier by using the PUBLIC keyword.
Normally, unless the document is used internally, public identifiers are rarely
used. This is because anyone outside your organization would not understand what
the public identifier was referring to or even how to resolve it.</p>

<p>Listing 3-1. System and Public Identifiers</p>

<pre>
&lt;!-- Using System Identifier --&gt;
&lt;!DOCTYPE courses SYSTEM "http://www.example.com/courses.dtd"&gt;
&lt;!-- Using Public Identifier --&gt;
&lt;!DOCTYPE courses PUBLIC "-//Example//Courses DTD//EN"
                         "http://www.example.com/courses.dtd"&gt;
</pre>

<p>The external subset contains the markup that makes up the DTD. It consists of
an optional text declaration followed by the external subset declarations.
Chapter 2 didn’t cover text declarations, as they pertain only to external
entities; I’ll cover them next.</p>

<h5>Text Declaration</h5>

<p>You are already familiar with the syntax for text declarations. They are
similar to XML declarations of documents; however, the standalone declaration is
not valid, version is optional, and encoding is required. It is also recommended
that you use a text declaration for external entities. A text declaration
primarily indicates the encoding of the external entity, which is necessary when
the entity uses a different encoding than the main XML document. The examples in
Listing 3-2 illustrate the two possible structures of a text declaration, where
the only difference is the use of the optional version attribute.</p>

<pre>
&lt;!-- Text declaration without version --&gt;
&lt;?xml encoding="ISO-8859-1" ?&gt;

&lt;!-- Text declaration with version --&gt;
&lt;?xml version="1.0" encoding="ISO-8859-1" ?&gt;
</pre>

<h5>External Subset Declaration</h5>

<p>The external subset declaration is where the actual grammar for the DTD
resides. It consists of one or many markup declarations, conditional sections,
and declaration separators. I’ll cover all these in depth in upcoming sections;
markup declarations and declaration separators, which are explained later in the
chapter in the “Parameter Entities” section, are common to both external and
internal subsets, and conditional sections are specific to external subsets and
external parameter entities. Listing 3-3 shows an example, which is explained in
more detail throughout this chapter, for the courses.dtd file from Listing
3-1.</p>

<div class="remarkbox">
  <div class="rbtitle">important-termanology</div>
  <div class="rbcontent">
  <p>The previous paragraph associates the terms: external subset declaration,
  markup declarations, conditional sections, declaration separators,
  parameter entities, internal subsets, external parameter entities.</p>
  </div>
</div>

<p>Listing 3-3. External Subset</p>

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

<p>If you refer to the previous chapter, this external subset looks fairly
similar to describing the structure of the document body. Note that the case
has changed on the elements—they are now all lowercase. If you lowercased all
the elements in the IDREF example, you could use this external subset as the DTD
for courses.dtd.</p>

<h4>Internal Subset</h4>

<p>An internal subset consists of the grammar for the DTD defined directly
within the document. Within the document type declaration, the internal subset
is enclosed within the characters [ and ]. When used with an external subset,
the internal subset is defined right after the external subset. Although defined
last, any declarations defined in the internal subset take precedence over
definitions from the external subset. Basically, you can use an internal subset
to override an external subset.</p>

<p>If you refer to the external subset declaration section in Listing
3-3—specifically to the markup used to define the contents of the course.dtd
file as well as Listing 3-1—you could rewrite the document type using an
internal subset as follows:</p>

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

<p>But, as previously mentioned, using internal subsets is restrictive because
they cannot be shared. It’s best to use an external subset. According to this
DTD, pre-requisite elements contain attributes but must be empty. What happens,
however, if this document will contain content within the pre-requisite element
but the external subset is being used for the document? This is where the
internal subset really comes in handy. Using the external subset in Listing
3-3, you can override the element declaration for the pre-requisite element in
an internal subset, as shown in Listing 3-4.</p>

<p>Listing 3-4. Overriding Prerequisite Declaration Using Internal Subset</p>

<pre>
&lt;!DOCTYPE courses SYSTEM "http://www.example.com/courses.dtd" [
     <b>&lt;!ELEMENT pre-requisite (#PCDATA)&gt;</b>
]&gt;
</pre>

<p>If you notice the bold code in Listing 3-4, the definition of the
pre-requisite element now allows data. This differs from Listing 3-3, where it
is defined as EMPTY in the external subset. The declaration within the internal
subset takes precedence in definitions, so a document written according to
this new DTD (Listing 3-4) would allow certain content within the pre-requisite
element.</p>

<h3>Markup Declarations</h3>

<p>So far you have seen how to declare internal and external subsets as well as
what they look like, but now it’s time to look at all the markup they contain.
Markup declarations declare elements types, attribute lists, entities, and
notations. They can also take the form of PIs and comments; although these do
not actually declare anything for the document, they can be used for application
instructions or author notes, as described in Chapter 2. When writing
declarations, you will encounter a few wildcards, which can be used in your
grammar. Before examining element declarations, you’ll learn more about the
wildcards.</p>

<h4>Wildcards</h4>

<p>A grammar, within a declaration, is written through expressions. Wildcards
determine grouping as well as the number of matches. This is similar to using
wildcards when writing regular expressions. For those of you unfamiliar with
regular expressions, they are a syntax used to write rules and perform pattern
matches against strings. Just as you could write the expression [A-Z]+ in a
regular expression, which would match one or more characters in the range of
A–Z, you could use similar functionality when writing declaration rules. Within
the declaration, an expression can be an element type or element name. The
following list shows some of the basic wildcards that can be used, where
expression could be as simple as an element name:</p>

<ul>
  <li>? : The expression is optional (expression?).</li>
  <li>expression1 expression2 : Matches an expression1 followed by
  expression2.</li>
  <li>| : Matches either expression (expression1 | expression2).</li>
  <li>- : Matches the first expression but not the second (expression1 -
  expression2).</li>
  <li>+ : Matches one or more occurrences of the expression (expression+).</li>
  <li>* : Matches zero or more occurrences of the expression (expression*).</li>
  <li>(expression) : The expression within the parentheses is treated as a
  single unit.</li>
</ul>

<div class="remarkbox">
  <div class="rbtitle">cross-reference</div>
  <div class="rbcontent">
  <p>(expression1, expression2, ...) : See "sequence list" below.</p>
  </div>
</div>

<p>For example, if you wanted to match on the logic that element1 must be
followed by one or more element2 or that it should match on zero or more
element3 elements or a single element4, the expression would look like this:</p>

<pre>
(element1 element2+) | (element3* | element4)
</pre>

<p>Notice that element1 and element2 are within parentheses and so are element3
and element4. The parentheses will take each of the two expressions as a whole
and match on either one of them, because of the | character.</p>

<p>You will see more examples of writing expressions and what they translate to
as you take a closer look at the declarations within a DTD.</p>

<h4>Element Type Declaration</h4>

<p>In this chapter, you have encountered examples of element type declarations
many times. These have been the markup that begins with <!ELEMENT followed by
whitespace. They define an element and what is valid for its content. Element
type declarations take the following form:</p>

<pre>
&lt;!ELEMENT element_name contentspec&gt;
</pre>

<p>The element_name is exactly what it implies. It is the name of the element
you are defining. The contentspec defines what type of content, if any, is valid
for the element. It can take the value EMPTY or ANY or may be a content model of
the type mixed or child. EMPTY simply implies the element cannot contain
content. Within the document, the element must be an empty-element tag or must
be a start and end tag with nothing in between, not even white-space. ANY
implies that any type of content, including none at all, is allowed. You can use
this when you have no specific rules for an element. It doesn’t matter if there
are child elements or what their names are, and it doesn’t matter what other
content may appear, as long as the content follows the rules for allowable
content in the XML specification. Using the pre-requisite element as an example,
in the external subset it is empty; and in the internal subset, you want to
allow any type of content, so it takes the following forms:</p>

<pre>
&lt;!-- declaration from external subset requiring the element to be empty --&gt;
&lt;!ELEMENT pre-requisite EMPTY&gt;
&lt;!-- declaration from internal subset allowing any content for element --&gt;
&lt;!ELEMENT pre-requisite ANY&gt;
</pre>

<p>Mixed and child content model types are not as simple, as these are
user-written rules to which the element content must conform.</p>

<h5>Child Content Model</h5>

<div class="remarkbox">
  <div class="rbtitle">enlightening</div>
  <div class="rbcontent">
  <p>The paragraph below explains some very subtle (yet important) things.</p>
  </div>
</div>

<p>An element that can contain only child elements and no other content,
excluding insignificant whitespace, follows the child content model. As
mentioned in Chapter 2, whitespace is typically significant and consists of
spaces, tabs, carriage returns, and line feeds. When dealing with validation,
this whitespace is considered insignificant when it’s not used with any other
text. This means you can’t use any other type of text besides these whitespace
characters directly within the element’s content. When thinking of element
content in these terms, the text content would include text, which is in the
immediate scope of the element being defined. Text contained within any of the
child elements of this element would be validated according to the declarations
of the child elements. An element following this model would look like the
following:</p>

<pre>
&lt;course&gt;
     &lt;title&gt;French II&lt;/title&gt;
     &lt;description&gt;Intermediate French&lt;/description&gt;
     &lt;pre-requisite&gt;
          ... some type of content
     &lt;/pre-requisite&gt;
&lt;/course&gt;
</pre>

<p>You may remember this structure from Chapter 2. It is a fragment from the
courses document. Notice that the course element contains no text, other than
the insignificant whitespace, but has three child elements. Also, the
pre-requisite element is not a required element because not all courses have
prerequisites. You could now write the element declaration for the course
element as follows:</p>

<pre>
&lt;!ELEMENT course (title, description, pre-requisite*)&gt;
</pre>

<p>The content specification, which defines the data content, for this
declaration is (title, description, pre-requisite*). This is a sequence list,
denoted by the list of elements separated by commas. A sequence list accepts
other types than just elements, but in this case, under the child content model,
no other types are allowed. Using a list means that each of the types used must
appear in a document in the exact order they are specified in the sequence list.
Based upon the wildcard used in the expression, the content specification would
translate to a course element that may contain only the child element’s title,
description, and any number, including zero pre-requisite elements. These
elements must appear in this order within a course element. Therefore, the
following fragment would not be valid according to this declaration:</p>

<pre>
&lt;course&gt;
     &lt;description&gt;Intermediate French&lt;/description&gt;
     &lt;title&gt;French II&lt;/title&gt;
&lt;/course&gt;
</pre>

<p>This document has no pre-requisite element, but that is perfectly fine. The
definition indicates that zero or more pre-requisite elements are considered
valid, denoted by pre-requisite* in the declaration. The problem with this
document is that according to the declaration, title must come before the
description element, which is not the case here. To allow both ordering schemes,
the declaration would need to define the two cases as follows:</p>

<pre>
&lt;!ELEMENT course (((title, description) | (description, title)), pre-requisite*)&gt;
</pre>

<p>Notice the use of parentheses. Following the order of precedence, the course
element must contain either title followed by description or description
followed by title. Either of these variants then must be followed by zero or
more pre-requisite elements.</p>

<p>Expanding upon the course element, you can add some new information to a
course, which will provide more information on the course being offered. It can
take the form of a URL or embedded text, but not both. Say you decide to add two
more possible elements, course_url and course_info, to the course element. The
document could look like any of the following:</p>

<pre>
&lt;!-- course without course_info and course_url --&gt;
&lt;course&gt;
     &lt;description&gt;Intermediate French&lt;/description&gt;
     &lt;title&gt;French II&lt;/title&gt;
&lt;/course&gt;
&lt;!-- course with course_url --&gt;
&lt;course&gt;
     &lt;title&gt;French II&lt;/title&gt;
     &lt;description&gt;Intermediate French&lt;/description&gt;
     &lt;course_url&gt;http://www.example.com/french.html&lt;/course_url&gt;
&lt;/course&gt;
&lt;!-- course with course_info --&gt;
&lt;course&gt;
     &lt;title&gt;French II&lt;/title&gt;
     &lt;description&gt;Intermediate French&lt;/description&gt;
     &lt;course_info&gt;This is miscellaneous info on French II&lt;/course_info&gt;
&lt;/course&gt;
</pre>

<p>Although the pre-requisite element does not appear in any of these fragments,
it is still valid (it was omitted for brevity). Enforcement of element order has
also been reinstituted, so description must follow title. Listing 3-5 shows how
you would write the new declaration.</p>

<p>Listing 3-5. New course Element Declaration</p>

<pre>
&lt;!ELEMENT course (title, description, (course_url | course_info)?, pre-requisite*)&gt;
</pre>

<p>Breaking down this grammar, course must contain title followed by
description. The description element then can be followed by a single, optional
course_url or course_info element, but not both. Regardless of whether one of
these elements exists as a child, the last element in the order would be zero or
more pre-requisite elements. Based on these rules, the following fragment is
invalid:</p>

<pre>
&lt;course&gt;
     &lt;title&gt;French II&lt;/title&gt;
     &lt;description&gt;Intermediate French&lt;/description&gt;
     &lt;course_info&gt;This is miscellaneous info on French II&lt;/course_info&gt;
     &lt;course_url&gt;http://www.example.com/french.html&lt;/course_url&gt;
&lt;/course&gt;
</pre>

<p>The course element cannot, according to the declaration, contain both the
course_info and course_url elements.</p>

<p>So far, you have looked at child elements only as an element’s content. Using
what you’ve learned up to now, you’ll see content that can include a mix of text
and other element types.</p>

<h5>Mixed Content Model</h5>

<p>Many times the child content model is too strict for a document. You might
want to add comments, PIs, or even text within an element’s content. Depending
upon your expression, mixed content allows for PCDATA, which stands for parsed
character data, and possibly child elements. Recall from Chapter 2 that you must
escape special characters such as < and & when using them within parsed text
sections. PCDATA is such a section. It can, however, contain nonparsed character
sections, such as comments, CDATA, and PIs. The simplest form of mixed content
is text-only content.</p>

<div class="remarkbox">
  <div class="rbtitle">let's-be-clear</div>
  <div class="rbcontent">
  <p>"mixed content" &#8800; "PCDATA." PCDATA is text-only content.</p>
  </div>
</div>

<p>Text-only content means that an element contains no child elements, and its
content is pure text, including comments, CDATA, and PI sections. Examining the
course element in this chapter, examples of elements containing pure text are
the title, description, and course_info elements. Referring to Listing 3-3, the
external subset, you will notice that title and description have been declared
as follows:</p>

<pre>
&lt;!ELEMENT title (#PCDATA)&gt;
&lt;!ELEMENT description (#PCDATA)&gt;
</pre>

<p>Declaring the course_info is the same. The following element will have no
child elements, but CDATA content may be desired:</p>

<pre>
&lt;!-- Declaration of course info --&gt;
&lt;!ELEMENT course_info (#PCDATA)&gt;
&lt;!-- example of course_info content allowed based on declaration --&gt;
&lt;course_info&gt;&lt;![CDATA[
     Trip available to Corsica &amp; Ile-de-France.
     GPA &lt; 3.0 requires instructor permission.
      ]]&gt;
     Trip coordinators will be Mr. Smith &amp;amp; Mr. Jones.
     &lt;!-- Need to check scheduling --&gt;
&lt;/course_info&gt;
</pre>

<p>Pure text content may suffice for a majority of the elements within a
document, but sometimes you’ll need both text and child elements. In cases like
these, you’ll need to mix PCDATA with the child elements.</p>

<p>In Listing 3-4, pre-requisite has been defined as #PCDATA. This is so that
you can add comments to the content. However, when writing this document, this
definition ends up being too restrictive. Sometimes not only are some courses
required, but also instructor approval is required. To indicate whether prior
approval is required before being able to take the course, you need to add an
optional element, instructor_approval, as a child element to the pre-requisite
element. It has also been determined that when this new element is missing, no
prior approval is required. With this new element, however, the pre-requisite
element may now look like this:</p>

<pre>
&lt;pre-requisite cref="1"&gt;
     &lt;!-- This prerequisite may not be required next semester --&gt;
     &lt;instructor_approval&gt;Y&lt;/instructor_approval&gt;
&lt;/pre-requisite&gt;
</pre>

<p>The new declaration for pre-requisite is as follows:</p>

<pre>
&lt;!ELEMENT pre-requisite (#PCDATA | instructor_approval)*&gt;
</pre>

<p>Notice that when mixing content, you use the | character as well as the *
character. These are required per the specifications, which means you are
unable to use strict element ordering in mixed content. For example, if you
added a child element to the pre-requisite element—say you were adding an
element for the required next semester flag called req_next_sem—you would just
add it as part of the OR expression.</p>

<p>This means that the pre-requisite element may contain zero or more #PCDATA
(text content), instructor_approval elements, and/or req_next_sem elements and
may appear in any order. For example:</p>

<pre>
&lt;!ELEMENT pre-requisite (#PCDATA | instructor_approval | req_next_sem)*>
</pre>

<p>As you may infer from the translation, mixed content may not be a good idea
to use when validation is a major concern for a document. Using the declaration,
you could end up with a pre-requisite element that has multiple
instructor_approval elements or multiple req_next_sem elements that may even
contain conflicting values. Consider the pre-requisite element in Listing 3-6;
it is valid according to the declaration but is not what is intended to be
valid.</p>

<p>Listing 3-6. Valid pre-requisite Element and Conflicting Data</p>

<pre>
&lt;pre-requisite cref="c1" &gt;
     &lt;!-- This prerequisite may not be required next semester --&gt;
     &lt;req_next_sem&gt;N&lt;/req_next_sem&gt;
     &lt;instructor_approval&gt;Y&lt;/instructor_approval&gt;
     &lt;instructor_approval&gt;N&lt;/instructor_approval&gt;
     &lt;req_next_sem&gt;Y&lt;/req_next_sem&gt;
&lt;/pre-requisite&gt;
</pre>

<div class="remarkbox">
  <div class="rbtitle">Caution</div>
  <div class="rbcontent">
  <p>Although it is much easier to declare elements using the mixed content
  model, you must be careful when using it. You lose much of the stricter
  control that you get when using child content, which can lead to documents
  that are valid according to the DTD but contain conflicting content that is
  not valid for processes you may be using the document with.</p>
  </div>
</div>

<h4>Entity Declaration</h4>

<p>Before moving to declaring attributes, which is the next logical step, it is
important to understand entities. <em class="highlight">Entities are not only
declared but can also be used within other declarations.</em> Although an area
more difficult than most of the
others, the following sections cover entities, including the different types and
how they are declared. As you read this chapter, you will encounter entity usage
within other declarations, so I will now help clarify questions that may arise
from their usage.</p>

<div class="remarkbox">
  <div class="rbtitle">let's-clear-this-up-NOW</div>
  <div class="rbcontent">
  <p>With regard to references, when the author says "external" he doesn't
  mean "in an externally defined DTD." He means "defined externally using
  &lt;!ENTITY."</p>
  </div>
</div>

<p>Entities are simply references to data regardless of whether the data is a
simple string or from an external location. Rather than having to include the
same block of data repetitively throughout a document, you can use a simple
entity instead. They can reduce the overall physical size of a document, and you
can use them to quickly change data and have the changes reflected throughout a
document. You will encounter two types of entities: <em>general entities</em>
and <em>parameter entities</em>. Before examining the declarations of entities,
a brief refresher on entity references is in order.</p>

<h5>Entity References</h5>

<p>As mentioned in Chapter 2, entity references reference the content of a
declared entity. They can reference general entities or parameter entities,
both of which are examined in the following sections. A
<em class="highlight">parsed general entity
reference</em>, usually just called an entity reference, takes the form of
<em>&amp;name;</em>, and a <em class="highlight">parameter entity reference</em>
takes the form of <em>%name;</em>. The name
in each case is the name of an entity declared in the DTD. You have already
encountered some of the built-in ones, such as &amp;amp; and &amp;lt;, which
refer to &amp; and &lt;, respectively. <em class="highlight">Unparsed general
entities</em>, used with the
ENTITY attribute type (which is the only place they can be used), take no
special form and are referenced directly by name.</p>

<h5>General Entities</h5>

<p>General entities come in three flavors: <em>internal parsed entities</em>,
<em>external parsed entities</em>, and <em>unparsed entities</em>, which are
always external. Parsed
entities define replacement text. Unparsed entities, being external to the
document, are resources containing data. The data can be of any type such as
text, including non-XML text and binary text.</p>

<p><em>Parsed Entities</em> As previously mentioned, <em class="highlight">you
use parsed entities for replacing text within a document.</em>
They can be either internal, which are declared within the internal
subset, or external, which point to an external subset. The easiest one to start
with is an internal parsed entity.</p>

<p>You can declare an internal parsed entity in an internal subset in the
following manner:</p>

<pre>
&lt;!ENTITY name "replacement"&gt;
</pre>

<p>The name must be a legal name as defined in Chapter 2. The replacement must
be well-formed XML. This means replacement can include entity references,
character references, and parameter entity references. When using references
within the value, circular references are not legal. It is incorrect to include
an entity reference pointing to the entity being defined, as well as to include
an entity reference pointing to an entity that may include the entity being
defined in its replacement. All the entity declarations within Listing 3-7 are
invalid because of circular references.</p>

<p>Listing 3-7. Circular Entity References</p>

<pre>
&lt;!-- Entity references cannot be circular --&gt;
&lt;!ENTITY myentity "Some replacement text &amp;secondentity;"&gt;
&lt;!ENTITY secondentity "Expanded with &amp;myentity;"&gt;
</pre>

<p>You may think that the entities declared in Listing 3-7 are not valid because
the myentity declaration is using the &amp;secondentity; reference before
secondentity has been declared. However, this is perfectly legal. The only time
the ordering of an entity declaration is important is when using an entity
reference within the value of an attribute-list declaration. In this case, the
entity must be declared before the attribute-list declaration. The reason these
declarations are invalid is that they are circular. The myentity declaration is
using an entity reference to secondentity, and secondentity is using an entity
reference right back to myentity. This ends up in an infinite loop scenario.</p>

<div class="remarkbox">
  <div class="rbtitle">Caution</div>
  <div class="rbcontent">
  <p>The ordering of a general entity declaration is significant when using the
  entity reference as a default value within an attribute-list declaration. You
  must declare the entity declaration before the attribute-list declaration. In
  all other cases, you can declare entities in any order.</p>
  </div>
</div>

<p>Listing 3-8 illustrates the proper usage of entity references within
content.</p>

<p>Listing 3-8. Valid Entity Reference Usage Within Content</p>

<pre>
&lt;!ENTITY myentity "Some replacement text"&gt;
&lt;!-- Entity defined using references within content --&gt;
&lt;!ENTITY secondentity "Expanded with &amp;myentity; &amp;amp; char A: &amp;#65;"&gt;
&lt;!-- Entity Reference Usage --&gt;
&lt;myelement&gt;&amp;secondentity;&lt;/myelement&gt;
</pre>

<p>When the &amp;secondentity; reference is expanded within the myelement
element, it would look like this:</p>

<pre>
&lt;myelement&gt;Expanded with Some replacement text &amp;amp; char A: A&lt;/myelement&gt;
</pre>

<p><em class="highlight">Content can also come from external resources rather
than from text included directly within the DTD. In this case, you must use an
</em><em>external parsed
entity</em>.</p>

<p>You declare external parsed entities similarly to how you declare the
external subset on the DOCTYPE:</p>

<pre>
&lt;!ENTITY name SYSTEM "URI"&gt;
&lt;!ENTITY name PUBLIC "publicID" "URI"&gt;
</pre>

<p>name is the same as name for an internal parsed entity and follows the same
rules. Taking the myentity from Listing 3-8 and changing it to an external
parsed entity, the text "Some replacement text" would reside within a file,
called foo.txt. The resulting declarations would now look like this:</p>

<pre>
&lt;!ENTITY myentity SYSTEM "foo.txt"&gt;
&lt;!-- Entity defined using references within content --&gt;
&lt;!ENTITY secondentity "Expanded with &amp;myentity; &amp;amp; char A: &amp;#65;"&gt;
&lt;!-- Entity Reference Usage --&gt;
&lt;myelement&gt;&amp;secondentity;&lt;/myelement&gt;
</pre>

<p>Once &amp;secondentity; is expanded, the myelement element would again look
like this:</p>

<pre>
&lt;myelement&gt;Expanded with Some replacement text &amp;amp; char A: A&lt;/myelement&gt;
</pre>

<p>One thing to remember about the foo.txt file is that it should contain a text
declaration like in Listing 3-2. This sets the encoding of the content within
this external file.</p>

<p><em>Unparsed Entities</em> Unparsed entities are external entities that can contain any type of data.
The data need not be XML, and it doesn’t even need to be text.
<em class="highlight">These entities are used for attributes of type ENTITY or
ENTITIES.</em> Earlier, an entity named
myimage was defined and referenced a GIF image file. You can declare unparsed
entities in one of two ways:</p>

<pre>
&lt;!ENTITY name SYSTEM "URI" NDATA notation&gt;
&lt;!ENTITY name PUBLIC "publicID" "URI" NDATA notation&gt;
</pre>

<p>These are quite similar to the declarations of external parsed entities. The
name is used for the same purpose and follows the same rules. The difference
comes from the use of the last two parameters. The NDATA keyword indicates that
this entity is an unparsed entity. The last parameter, notation, is a reference
to a notation declared in the DTD and must match the notation name it is
referencing. Refer to the section “ENTITY/ENTITIES” later in this chapter for an
example of how an unparsed entity is used and its relationship to NOTATION and
ATTLIST.</p>

<h5>Parameter Entities</h5>

<p>Parameter entities are similar to general entities in the respect that they
are also used for replacement. Parameter entities, however, are used only within
a DTD. They allow for the replacement of grammar. The caveat is that parameter
entities, although they can be declared within external and internal subsets,
cannot be referenced within markup in the internal subset. I will return to this
point in a moment. These entities may also be internal or external, with their
declarations taking the following form:</p>

<pre>
&lt;!ENTITY % name "entity_value"&gt;
&lt;!ENTITY % name SYSTEM "URI"&gt;
&lt;!ENTITY % name PUBLIC "publicID" "URI"&gt;
</pre>

<p>Because these may appear in markup only in an external subset, first look at
the grammar within the foo.dtd file, as shown in Listing 3-9.</p>

<p>Listing 3-9. External Subset Defined in File foo.dtd</p>

<pre>
&lt;?xml encoding="ISO-8859-1"?&gt;
&lt;!ENTITY % pc "(#PCDATA)"&gt;
&lt;!ELEMENT courses (course+)&gt;
&lt;!ELEMENT course (title, description, pre-requisite*)&gt;
&lt;!ATTLIST course cid ID #REQUIRED&gt;
&lt;!ELEMENT title %pc;&gt;
&lt;!ELEMENT description %pc;&gt;
&lt;!ELEMENT pre-requisite EMPTY&gt;
&lt;!ATTLIST pre-requisite cref IDREFS #REQUIRED&gt;
</pre>

<p>You will notice the first declaration after the text declaration is the
parameter entity pc. The replacement text is (PCDATA). The element declarations
for title and description both use the parameter entity reference %pc; where the
contentspec would go. Based on the substitution, it is equivalent to writing
them as follows:</p>

<pre>
&lt;!ELEMENT title (#PCDATA)&gt;
&lt;!ELEMENT description (#PCDATA)&gt;
</pre>

<p>As long as you’re using the parameter entity references within an external
subset, you can use them as text replacements for any of the grammar. You can
also modify the cref attribute-list declaration to use a parameter entity
reference, like so:</p>

<pre>
&lt;!ENTITY % IDREFREQ "IDREFS #REQUIRED"&gt;
&lt;!ATTLIST pre-requisite cref %IDREFREQ;&gt;
</pre>

<p>Using parameter entities in these cases really depends upon how often you
might need to repeat the same grammar as well as how readable you would like the
document to be. Using short names to save some keystrokes may also cause the
document to be hard to decipher. And this would just get worse as the document
became more complex.</p>

<p>You can also use parameter entities within the internal subset. Although I
said you couldn’t use it within markup in the internal subset, you won’t use it
in that way. Consider the possibility that you write a document that includes a
shared external subset; in fact, say you’re using the one from Listing 3-9
called foo.dtd. Then, say you need to include another external subset, the file
foo2.dtd in Listing 3-10, to be part of the DTD; however, you cannot modify
foo.dtd and just copy the declarations into the file, because it is shared.</p>

<p>Listing 3-10. External Subset from File foo2.dtd</p>

<pre>
&lt;?xml encoding="ISO-8859-1"?&gt;
&lt;!ELEMENT instructor_approval (#PCDATA)&gt;
&lt;!ELEMENT req_next_sem (#PCDATA)&gt;
</pre>

<p>This is a scenario where it is possible to use a parameter entity reference
within the internal subset. For example:</p>

<pre>
&lt;!DOCTYPE courses SYSTEM "foo.dtd" [
     &lt;!ENTITY % foo2 SYSTEM "foo2.dtd"&gt;
     %foo2;
]&gt;
</pre>

<p>The parameter entity foo2 refers to the external subset foo2.dtd. The
parameter entity reference %foo2; is not within any markup so is perfectly
valid. This is equivalent to writing the following:</p>

<pre>
&lt;!DOCTYPE courses SYSTEM "dtddef.dtd" [
     &lt;!ENTITY % foo2 SYSTEM "dtddef2.dtd"&gt;
     &lt;!ELEMENT instructor_approval (#PCDATA)&gt;
     &lt;!ELEMENT req_next_sem (#PCDATA)&gt;
]&gt;
</pre>

<p>The only issue you may run into is that by having used the parameter entity
reference within the internal subset, everything declared within the external
subset referenced by the parameter entity is now considered part of the internal
subset. This may cause problems if you are overriding some declarations. In this
case, ordering within the internal subset is important; another way is to use a
general external subset file for the DOCTYPE and use parameter entities and
references within the general file to include the other external subsets,
foo.dtd and foo2.dtd. In this case, you may end up with a file such as
general.dtd that looks like this:</p>

<pre>
&lt;!ENTITY % foo SYSTEM "dtddef.dtd"&gt;
%foo;
&lt;!ENTITY % foo2 SYSTEM "dtddef2.dtd"&gt;
%foo2;
</pre>

<p>You could then modify the DOCTYPE to the following:</p>

<pre>
&lt;!DOCTYPE courses SYSTEM "general.dtd"&gt;
</pre>

<p>This would allow you to keep all external subsets truly external and leave
the internal subset for your own personal declarations.</p>

<p>Parameter entity references, when used in this fashion outside of markup,
are called declaration separators.</p>

<h4>Attribute-List Declaration</h4>

<p>You have already encountered attribute-list declarations when using
ID/IDREF/IDREFS in Chapter 2. Those cases are just a small piece of
functionality provided by using attribute-list declarations. Within the scope of
validation, the declarations specify the name, type, and any default value for
attributes associated with an element. A declaration takes the following
form:</p>

<pre>
&lt;!ATTLIST element_name att_definition*&gt;
</pre>

<p>This is similar to the declaration of an element, although two names are
required. The element_name is the name of the element to which this
attribute-list declaration applies. <em class="highlight">The att_definition
includes the name of the attribute being defined</em> as well as the rules for
the attribute.</p>

<p>Note the * in the definition. You can define multiple attributes within a
single attribute-list declaration. If the same attribute is defined multiple
times within the declaration, the first definition encountered is the binding
one, and the rest are ignored. Depending upon the options used for the parser,
which you will see in later chapters when using the PHP extensions, sometimes
you’ll get warnings. Defining an attribute multiple times for an element is not
an error though may result in a warning from the parser. Declaring multiple
attribute-list declarations for an element is also not an error, because you may
prefer to define one attribute per attribute-list declaration for an element,
though that may also result in a warning for a parser. Just keep in mind that
these are warnings and not errors and can be controlled by the parser.</p>

<p>The att_definition is the grammar for defining the rules for an attribute. It
can be broken down into <em class="highlight">Name AttType DefaultDecl</em>,
where Name is the name of the
attribute being defined, AttType is the type of attribute, and DefaultDecl is
the rule for the default value. Referring to Listing 2-17 from Chapter 2, when
the notion of an ID was introduced, you may recall the declaration <!ATTLIST
Course cid ID #REQUIRED>. Breaking this declaration down now makes much more
sense. Course refers to the attribute element_name, cid refers to the attribute
Name, ID is the attribute AttType, and #REQUIRED is the attribute DefaultDecl.
Let’s take a closer look at the AttType and DefaultDecl attributes.</p>

<h5>Attribute Defaults</h5>

<p>The attribute default (DefaultDecl) indicates any default value for an
attribute as well as whether an attribute is required and how it should be
handled if it’s not. DefaultDecl may take one of four forms: #REQUIRED,
#IMPLIED, #FIXED plus a default value, or just a default value. During the
course of examining attribute defaults, you’ll see the attribute type (AttType)
set to CDATA. I’ll explain this in more detail in the “Attribute Types” section,
but for now using the CDATA type means that the attribute is a character type;
therefore, its value must be a literal string. For example, within the fragment
in Listing 3-11, the attribute make has the string value "Ford".</p>

<p>Listing 3-11. Example Element with the make Attribute</p>

<pre>
&lt;Car make='Ford' /&gt;
</pre>

<p><em>#REQUIRED</em> Attributes with the #REQUIRED default are exactly that. The attribute is
required for every element within a document for which the attribute is defined.
In the case of the Car element in Listing 3-11, you could define the
attribute-list declaration as follows:</p>

<pre>
&lt;!ATTLIST Car make CDATA #REQUIRED&gt;
</pre>

<p>Based on this declaration, the fragments in Listing 3-12 illustrate both
valid and invalid structures, though the elements themselves are
well-formed.</p>

<p>Listing 3-12. Examples of Valid and Invalid Attributes Defined As
#REQUIRED</p>

<pre>
&lt;!-- Valid attribute because it exists and contains a string value --&gt;
&lt;Car make='Ford' /&gt;

&lt;!-- Valid attribute because it exists and contains empty string value --&gt;
&lt;Car make='' /&gt;

&lt;!-- Invalid attribute because it does not exist on the Car element --&gt;
&lt;Car /&gt;
</pre>

<p><em>#IMPLIED</em> Attributes with the #IMPLIED default means no default value is specified and
the attribute is optional on the element for which it is defined. Returning to
the Car element in Listing 3-11, you can change the attribute-list declaration
so that make is an optional attribute, as illustrated in Listing 3-13.</p>

<p>Listing 3-13. Attribute-List Declaration Using the #IMPLIED Default</p>

<pre>
&lt;!ATTLIST Car make CDATA #IMPLIED&gt;
</pre>

<p>Comparing the elements from Listing 3-12 to those in Listing 3-14, you will
notice that by declaring the attribute as #IMPLIED, all fragments are now
valid.</p>

<p>Listing 3-14. Examples of Valid Attributes Defined As #IMPLIED</p>

<pre>
&lt;!-- Valid attribute because it exists and contains a string value --&gt;
&lt;Car make='Ford' /&gt;

&lt;!-- Valid attribute because it exists and contains empty string value --&gt;
&lt;Car make='' /&gt;

&lt;!-- Valid attribute even though it does not exist on the Car element --&gt;
&lt;Car /&gt;
</pre>

<p><em>#FIXED</em> Attributes with the #FIXED default require a default value within the
attribute-list declaration. These types of attributes have values that must be
identical to the value specified by the default value. The good thing, though,
is that it is optional to add the attribute to the element. When the attribute
is not specifically added, the parser will automatically provide the default
value specified in the declaration.</p>

<p>Using the Car element from Listing 3-11 and building upon the ATTLIST
attribute from Listing 3-13, you may also want to limit the scope to
automobiles manufactured in 2002, where the attribute year indicates the
manufacturing year for the auto. To enforce this rule, you can write the
attribute-list declaration as demonstrated in Listing 3-15.</p>

<p>Listing 3-15. Combined Attribute-List Declaration for the make and year
Attributes</p>

<pre>
&lt;!ATTLIST Car
     make CDATA #IMPLIED
     year CDATA #FIXED "2002"&gt;
</pre>

<p>This declaration combines the rule for the make attribute with the new rule
for the year attribute into a single declaration. You could also write the
declaration like so:</p>

<pre>
&lt;!ATTLIST Car make CDATA #IMPLIED&gt;
&lt;!ATTLIST Car year CDATA #FIXED "2002"&gt;
</pre>

<p>Based upon the declaration in Listing 3-15, the following illustrates some
valid and invalid fragments:</p>

<pre>
&lt;!-- Valid with unspecified attribute year defaulting to fixed value of "2002" --&gt;
&lt;Car make='Ford' /&gt;

&lt;!-- Valid as attribute year is "2002" which is the same as the fixed value --&gt;
&lt;Car make='Ford' year="2002" /&gt;

&lt;!-- Invalid as year is "2003" which IS NOT the same as the fixed value --&gt;
&lt;Car make='Ford' year="2003" /&gt;
</pre>

<p><em>Default Value</em> So far, you have looked at requiring attributes, making them optional, and
restricting attributes. The last case offers a bit more flexibility because it
allows for optional attributes, such as using #IMPLIED, but also adds default
values, similar to using #FIXED, when attributes are not specified. Unlike using
#FIXED, however, the attribute is not restricted to the default value. The
default value is used only when the attribute is missing from the element.
Taking the declaration from Listing 3-15 and changing the year to default to
"2002" but not restricting it to that value, you would have this new
declaration:</p>

<pre>
&lt;!ATTLIST Car
     make CDATA #IMPLIED
     year CDATA "2002"&gt;
</pre>

<p>With this new declaration, you can update the valid and invalid fragment
list:</p>

<pre>
&lt;!-- Valid with unspecified attribute year defaulting to value of "2002" --&gt;
&lt;Car make='Ford' /&gt;

&lt;!-- Valid with value of year being "2002"--&gt;
&lt;Car make='Ford' year="2002" /&gt;

&lt;!-- Valid with value of year being "2003" --&gt;
&lt;Car make='Ford' year="2003" /&gt;
</pre>

<p>Now that you understand an attribute’s default types, you can examine the
attribute types in some detail.</p>

<h5>Attribute Types</h5>

<p>Attribute types (AttType) simply define the type of attribute. An attribute 
can
be a string type (CDATA), enumerated type, or tokenized type. The easiest to 
begin
with is the string type, which was used within the previous “Attributes
Defaults”
section.</p>

<p><em>CDATA Type</em> The CDATA type simply means the attribute has character data content. The
vast
majority of attributes fall into this type. As mentioned in Chapter 2, you must
escape the characters &lt; and &amp; when using them literally. Character and
entity references are also valid content for an attribute default value,
although unless
using the built-in entity references, such as &amp;lt; and &amp;amp;, the entity
(which was covered earlier in this chapter) cannot be an external entity
reference.
In simple terms, if the attribute-list declaration is within the internal
subset,
then the entity must be declared within the internal subset; otherwise, the
entity
may be declared in the internal subset or the same external subset as the
attribute-list declaration. From reading Chapter 2 and from seeing the earlier
examples in this chapter, which used the CDATA type, you should have a basic
understanding of how to use character data with attributes. Here, however, I
will
demonstrate how to use entity references when declaring attribute lists. The
following listings, Listing 3-16 and Listing 3-17, are examples of how
attribute-list declarations interact with entity declarations.</p>

<div class="remarkbox">
  <div class="rbtitle">Errata</div>
  <div class="rbcontent">
  <p>The author messed up the example below; but, I understand what
  he is saying!</p>
  </div>
</div>

<p>Listing 3-16. External Subset Defining coursedata Entity Using ext.dtd
Filename</p>

<pre>
&lt;?xml version="1.0" ?&gt;
&lt;!ENTITY coursedata "Some Course Data"&gt;
&lt;!ENTITY moredata "More Course Data"&gt;
&lt;!-- ATTLIST IS valid as moredata is declared in this subset --&gt;
&lt;!ATTLIST courses mcdata CDATA "&amp;moredata;"&gt;
&lt;!-- ATTLIST IS valid as evenmoredata is declared in internal subset --&gt;
&lt;!ATTLIST courses emcdata CDATA "&amp;evenmoredata;"&gt;
</pre>

<p>Listing 3-17. Invalid ATTLIST Declaration in Internal Subset Referencing
External Entity</p>

<pre>
&lt;!DOCTYPE courses SYSTEM "ext.dtd" [
&lt;!ELEMENT courses ANY&gt;
&lt;!-- ATTLIST is invalid as it references the external entity from Listing 3-16 --&gt;
&lt;!ATTLIST courses somedata CDATA "&amp;coursedata;"&gt;
&lt;!ENTITY evenmoredata "More Course Data"&gt;
&lt;!-- ATTLIST IS valid as evenmoredata is declared in this subset --&gt;
&lt;!ATTLIST courses evenmcdata CDATA "&amp;evenmoredata;"&gt;
]&gt;
</pre>

<p>The CDATA type is probably the easiest and most often used attribute type.
The only real complexity may come when using entities, which are covered later
in this chapter in the “ENTITY/ENTITIES” section. For now, though, you will
examine the attribute’s enumerated type.</p>

<p><em>Enumerated Type</em> Enumerated types allows you to define certain values that are valid for an
attribute. Any value set for the attribute, which is not in the defined list
within the declaration, is considered invalid. Returning to the course element
from the courses document, you can add an attribute named iscurrent. This
attribute indicates whether the content has been updated. Say the values Y and N
are the only acceptable values you want for the attribute value. Therefore, you
could write a declaration as follows:</p>

<pre>
&lt;!ATTLIST course iscurrent (Y | N) #REQUIRED&gt;
</pre>

<p>By this definition, iscurrent is required and must have the value Y or N,
so the following illustrates how to use the iscurrent attribute with the course
element:</p>

<pre>
&lt;course iscurrent="Y" /&gt;
&lt;course iscurrent="N" /&gt;

&lt;!-- The following are invalid because XML is case-sensitive --&gt;
&lt;course iscurrent="y" /&gt;
&lt;course iscurrent="n" /&gt;
</pre>

<p>This might be fine if you wrote the DTD before you had some data, but in this
case, you already have course data in XML format. Someone could manually fix all
the course elements within the document, but a much easier approach is to just
use a default value based on one of the listed values. Since this attribute is
new to the document, you can assume that the default will be N, indicating that
any course element without this attribute is to be considered as not having been
updated. For example:</p>

<pre>
&lt;!ATTLIST course iscurrent (Y | N) "N"&gt;
</pre>

<p>Based on this new declaration, the following are all valid:</p>

<pre>
&lt;course iscurrent="Y" /&gt;
&lt;course iscurrent="N" /&gt;
&lt;!-- following course element uses default value of "N" for iscurrent attribute --&gt;
&lt;course /&gt;
</pre>

<div class="remarkbox">
  <div class="rbtitle">Caution</div>
  <div class="rbcontent">
  <p>XML is case-sensitive. When using an enumerated type, you must be careful,
  because the attribute value must match one of the values defined within the
  attribute type. For example, the value Y is not the same as the value y.</p>
  </div>
</div>

<p>Notations, which are covered later in this chapter in the section “Notation
Declaration,” are also of the enumerated type. An attribute of this type must
match one of the notations listed, and the mutation must have been declared in
the DTD. This is an example of the declaration:</p>

<pre>
&lt;!ATTLIST image type NOTATION (gif|jpg) "gif"&gt;
</pre>

<p>An image attribute within a document using this declaration could have the
value gif or jpg, where the default value, if not set on the image element, is
gif. Furthermore, gif and jpg must also be declared as notations within the DTD.
Please refer to the “Notation Declaration” section for information about
notations.</p>

<p><em>ID/IDREF/IDREFS</em> Chapter 2 covered these types in detail, along with examples. You should
note, however, attributes of type ID must use the #REQUIRED or #IMPLIED default
within their declarations (because of the nature of attribute IDs). To summarize
their functionality, an ID uniquely identifies an element, and IDREF and IDREFS
reference an element identified by an attribute of the ID type. Their
declarations, from Chapter 2, take the following form:</p>

<pre>
&lt;!ATTLIST Course cid ID #REQUIRED&gt;
&lt;!ATTLIST Pcourse cref IDREF #REQUIRED&gt;
&lt;!ATTLIST pre-requisite cref IDREFS #REQUIRED&gt;
</pre>

<p><em>NMTOKEN/NMTOKENS</em> Up until now, you have seen that the CDATA type allows virtually any value
for an attribute, assuming the value is legal for an attribute. Enumerated
types restrict attribute values to one of a given list. An NMTOKEN offers a
little more restriction than CDATA and much less than an enumeration. The value
for an NMTOKEN is restricted to the characters that make up a name, as defined
in Chapter 2. You have no restriction, however, on the first character like you
have with a name. To put it simply, an NMTOKEN is similar to CDATA, except
values containing whitespace, certain punctuation, character references, and
entity references are not valid. The use of whitespace has an exception. The
value of an attribute is first normalized before validity checks are performed
on it. Leading and trailing whitespace is removed during normalization, so
att="	value	" would validate the same for an NMTOKEN as att="value".
Attributes of this type are defined as follows:</p>

<pre>
&lt;!ATTLIST course code NMTOKEN "default_value"&gt;
</pre>

<p>This declaration defines the attribute code on the course element with a
default value of default_value. Based on this declaration, Listing 3-18
illustrates valid and invalid usage.</p>

<p>Listing 3-18. Valid and Invalid NMTOKEN Type Usage</p>

<pre>
&lt;!-- Valid NMTOKEN type usage --&gt;
&lt;course code=" 123 " /&gt;
&lt;course code="123" /&gt;

&lt;!-- Invalid NMTOKEN usage --&gt;
&lt;course code=" 1 2 3 " /&gt;
&lt;!-- The / character is not valid for NMTOKEN --&gt;
&lt;course code="1/2/3" /&gt;
&lt;!-- The character references are not valid for NMTOKEN --&gt;
&lt;course code="1#x20" /&gt;
&lt;!-- Entity references (&amp;amp;) are not valid for NMTOKEN --&gt;
&lt;courses code=" 1&amp;amp;2&amp;amp;3 " /&gt;
</pre>

<p>If the attribute had been declared a CDATA type, all examples would have been
valid.</p>

<p>An NMTOKENS allows for the value of an attribute to contain more than one
NMTOKEN separated by whitespace. This, in simple terms, just means that by
defining an attribute as an NMTOKENS type, whitespace characters become valid
within the attribute value. In reality, the attribute value consists of multiple
NMTOKEN values. By changing the declaration used for Listing 3-18 to the
following:</p>

<pre>
&lt;!ATTLIST course code NMTOKENS "default_value"&gt;
</pre>

<p>the example &lt;course code=" 1 2 3 " /&gt; is now valid.</p>

<p><em>ENTITY/ENTITIES</em> The last tokenized attribute types are ENTITY and ENTITIES. These types
reference unparsed entities within a document. You have already been introduced
to entities in the “Entity Declaration” section, but a quick synopsis of an
unparsed entity is that an unparsed entity is an external entity, such as a
remote file, that contains non-XML data.</p>

<p>Consider what is involved in adding an image to an XML document. The first
thing that may come to mind is using a CDATA section. This has issues, however.
The binary data may contain invalid characters such as ]]&gt;. You may then
decide to Base64 encode the image and use the encoded data as content. This
would work; however, not only does the size of your document increase, but you
would also need to include information for the image, such as how it should be
handled. Another option would be to use an attribute of type ENTITY to reference
the image, such as declared in Listing 3-19.</p>

<p>Listing 3-19. Attribute Type ENTITY Declaration</p>

<pre>
&lt;!NOTATION GIF SYSTEM "image/gif"&gt;
&lt;!ENTITY myimage SYSTEM "mypicture.gif" NDATA GIF&gt;
&lt;!ATTLIST image imgsrc ENTITY #REQUIRED&gt;
</pre>

<p>To use an ENTITY type, you must declare the entity, myimage; also, because it
is an unparsed entity, you must declare a NOTATION, GIF, and associate it with
the entity. Based on these declarations, Listing 3-20 illustrates the usage of
the unparsed entity.</p>

<p>Listing 3-20. Usage of Unparsed Entity Reference</p>

<pre>
&lt;image imgsrc="myimage" /&gt;
</pre>

<p>The attribute value must be one of the unparsed entities defined in the DTD.
In this case, this uses myimage, which refers to the file mypicture.gif.</p>

<p>The attribute type ENTITIES is just a whitespace-separated list of entities.
It is similar to the NMTOKEN/NMTOKENS relationship. For example:</p>

<pre>
&lt;!NOTATION GIF SYSTEM "image/gif"&gt;
&lt;!ENTITY myimage SYSTEM "mypicture.gif" NDATA GIF&gt;
&lt;!ENTITY yourimage SYSTEM "yourpicture.gif" NDATA GIF&gt;
&lt;!ATTLIST courses imgsrc ENTITIES #REQUIRED&gt;
</pre>

<p>An example for the ENTITIES type based on these declarations is as
follows:</p>

<pre>
&lt;image imgsrc="myimage yourimage" /&gt;
</pre>

<p>Before you get too excited and think you can change all your image references
to use this format, you need to understand the ramifications. Using attribute
entities in this manner works well for traditional publishing. Everything is
within a controlled environment. On the Web, however, you have little control
over the client side. The actual MIME type for a file is usually determined by
the Web server and sent to the client. If you were to call the file
mypicture.gif, the file could actually be a JPG, and the Web server might send
you MIME type information for a JPG rather than a GIF. Based on the declarations
you have here, however, you are setting the handling of the unparsed entity
within the notation declaration. So, in short, most people find using attribute
entities and notations in a Web environment not a good idea, but in reality, it
really depends upon how you are using and what you are using them to do.</p>

<h4>Notation Declaration</h4>

<p>A notation indicates how data should be processed. Typically, notations
identify the format of unparsed entities and elements bearing a NOTATION type
attribute. You can use the provided external identifier to provide the location
of a helper application that is able to process the noted data. Do you remember
the use of the NOTATION type for an attribute? The notation provided an
identifier of image/gif. Based on this MIME type, an application could call the
program associated with the image/gif MIME type to handle the image data. You
declare notations as you would declare the external subset on the DOCTYPE:</p>

<pre>
&lt;!NOTATION name SYSTEM "URI"&gt;
&lt;!NOTATION name PUBLIC "publicID"&gt;
&lt;!NOTATION name PUBLIC "publicID" "URI"&gt;
</pre>

<p>The name portion of the notation declaration must be a valid name as defined
in Chapter 2. Using the previous declaration, &lt;!NOTATION GIF SYSTEM
"image/gif"&gt;, you have declared a notation named GIF with a system identifier
of image/gif. In a controlled environment, you might rather want to specifically
identify an application to handle the data. Suppose all desktops in an
organization were clones of each other and locked down to prevent modification,
and an application called GIFProcessor existed in /usr/local/bin on all systems.
You could then modify the notation to &lt;!NOTATION GIF SYSTEM
"/usr/local/bin/GIFProcessor"&gt;. If the image/gif MIME type were associated
with this program, then these two declarations would be equivalent. If the MIME
type were set to something else, then using a specified application rather than
a MIME type would ensure that the data was handled correctly.</p>

<p>Now that you have a better idea of what a notation is, you need to revisit
the NOTATION type within an attribute-list declaration. Remember, the notation
type is an enumerated type. Enumerated types mean that the allowed values for
attributes must be specified within the attribute-list declaration. When used in
this case, the notation provides information for the element. For example,
suppose an image is embedded directly within an XML document. It has been
Base64 encoded so that it can live within the content of an element. Using a
notation attribute, you can associate a handler for the element contents with
the element. For example:</p>

<pre>
&lt;!NOTATION BASE64 SYSTEM "location of base64 handler"&gt;
&lt;!ATTLIST embededdata enctype NOTATION (BASE64) #REQUIRED&gt;

&lt;!-- example of enctype attribute on embededdata element --&gt;
&lt;embededdata enctype="BASE64"&gt;Some Base64 embedded data&lt;/embededdata&gt;
</pre>

<p>Because this is an enumerated type, you could use multiple notations for the
attribute-list declaration. You will now add a handler for UUencode:</p>

<pre>
&lt;!NOTATION BASE64 SYSTEM "location of base64 handler"&gt;
&lt;!NOTATION UUENCODE SYSTEM "location of UUencode handler"&gt;
&lt;!ATTLIST embededdata enctype NOTATION (BASE64 | UUENCODE) #REQUIRED&gt;

&lt;!-- example of enctype attribute on embededdata element --&gt;
&lt;embededdata enctype="BASE64"&gt;Some Base64 embedded data&lt;/embededdata&gt;
&lt;embededdata enctype="UUENCODE"&gt;Some UUencoded embedded data&lt;/embededdata&gt;
</pre>

<p>As illustrated, the enctype attribute may now use either BASE64 or UUENCODE
notations for its value. Any other value, as well as not associating the
attribute with the embededdata element, is deemed invalid because of the
#REQUIRED default.</p>

<p>Notations are also required when using unparsed entities. Please refer to the
ENTITY attribute type and the section “Unparsed Entities” within this chapter
for more information. Notations are declared as described in this section, and
their usage is similar to the NOTATION attribute type. The only difference is
the applicable XML structure.</p>

<h3>Conditional Sections</h3>

<p>You use conditional sections to selectively include and exclude sections of a
DTD; you can use them only within an external subset. You may be wondering why
you would need such functionality. You may need this functionality for several
reasons. Consider publishing from the traditional sense.</p>

<p>A document may be a draft, or it may be the finalized version. When it is
still a draft, additional information, such as user notes and comments attached
to paragraphs, may be considered valid for the document. Certainly when the
document is ready to be published in its finalized state, these must not appear
in the final version. Of course, you could always define two completely separate
DTDs for the document, but then each must be managed, and the document must be
altered to reference the correct one depending upon the state. A much simpler
way would to use the same external subset with conditional sections
encapsulating the appropriate sections for the current state of the
document.</p>

<p>Another possible scenario is working on a shared external subset that is
currently in production. If you have had to debug applications in a live
environment before, then this is a similar case. The original code must be left
unaltered because it is currently running, but you need to alter and test code
at the same time. You possibly can use if/else blocks based on your terminal ID
(yes, terminals still do exist, as I know from experience) or IP address,
assuming you have a dedicated IP addresses at your workstation and are not
behind a firewall. Using conditional sections will allow the subset to continue
working for everyone else except you, giving you the time you need to fix or
alter it without disrupting anyone else’s productivity.</p>

<p>This should give you a basic idea on why you might need conditional sections,
and by now you are probably on the edge of your seat, waiting in anticipation on
how to use these sections. You can define conditional sections in one of two
ways, depending upon whether you want a section included or ignored:</p>

<pre>
&lt;![ IGNORE [
    declarations
]]&gt;

&lt;![ INCLUDE [
    declarations
]]&gt;
</pre>

<p>Within the INCLUDE and IGNORE blocks, declarations refers to any declaration
you want included or suppressed. So you might have a subset list the one in
Listing 3-21.</p>

<p>Listing 3-21. Example Using Conditional Sections in course.dtd</p>

<pre>
&lt;?xml encoding="ISO-8859-1"?&gt;
&lt;!ELEMENT courses (course+)&gt;
&lt;!ELEMENT course (title, description, pre-requisite*)&gt;
&lt;!ATTLIST course cid ID #REQUIRED&gt;
&lt;!ELEMENT title (#PCDATA)&gt;
&lt;!ELEMENT description (#PCDATA)&gt;
&lt;!ELEMENT pre-requisite ANY&gt;
&lt;![ INCLUDE [
     &lt;!ATTLIST pre-requisite cref IDREFS #REQUIRED&gt;
     &lt;!ELEMENT instructor_approval EMPTY&gt;
     &lt;!ELEMENT req_next_sem (#PCDATA)&gt;
]]&gt;
&lt;![ IGNORE [
     &lt;!ATTLIST pre-requisite cref CDATA #IMPLIED&gt;
     &lt;!ELEMENT instructor_approval ANY&gt;
     &lt;!ELEMENT req_next_sem ANY&gt;
]]&gt;
</pre>

<p>This may not look very useful because INCLUDE and IGNORE are both hard-coded
into the subset, but it should give you the basic idea. Everything within the
INCLUDE section will be used for validation, and everything within the IGNORE
section is ignored. When using conditional sections, parameter entities are your
friends. Remember that you can use them within the DTD to replace a grammar. You
can modify the course.dtd file to use parameter entities, as shown in Listing
3-22.</p>

<p>Listing 3-22. Conditional Sections in course.dtd Using Parameter Entities in
course.dtd</p>

<pre>
&lt;?xml encoding="ISO-8859-1"?&gt;
&lt;!ENTITY % livedata "INCLUDE"&gt;
&lt;!ENTITY % debugdata "IGNORE"&gt;
&lt;!ELEMENT courses (course+)&gt;
&lt;!ELEMENT course (title, description, pre-requisite*)&gt;
&lt;!ATTLIST course cid ID #REQUIRED&gt;
&lt;!ELEMENT title (#PCDATA)&gt;
&lt;!ELEMENT description (#PCDATA)&gt;
&lt;!ELEMENT pre-requisite ANY&gt;
&lt;![ %livedata; [
     &lt;!ATTLIST pre-requisite cref IDREFS #REQUIRED&gt;
     &lt;!ELEMENT instructor_approval EMPTY&gt;
     &lt;!ELEMENT req_next_sem (#PCDATA)&gt;
]]&gt;
&lt;![ %debugdata; [
     &lt;!ATTLIST pre-requisite cref CDATA #IMPLIED&gt;
     &lt;!ELEMENT instructor_approval ANY&gt;
     &lt;!ELEMENT req_next_sem ANY&gt;
]]&gt;
</pre>

<p>This code adds the parameter entities livedata and debugdata to the subset.
The previously hard-coded text INCLUDE and IGNORE have also been removed and 
replaced with the parameter entity references for these new entities. Anyone now
using this subset will be using the declarations in Listing 3-23.</p>

<p>Listing 3-23. Declarations Used by Default Within course.dtd</p>

<pre>
&lt;!ATTLIST pre-requisite cref IDREFS #REQUIRED&gt;
&lt;!ELEMENT instructor_approval EMPTY&gt;
&lt;!ELEMENT req_next_sem (#PCDATA)&gt;
</pre>

<p>Within the working document, you can override the livedata and debugdata
entity declarations within the internal subset:</p>

<pre>
&lt;!DOCTYPE courses SYSTEM "course.dtd" [
     &lt;!ENTITY % livedata "IGNORE"&gt;
     &lt;!ENTITY % debugdata "INCLUDE"&gt;
]&gt;
</pre>

<p>While everyone else uses the declarations listed in Listing 3-23, this
document will be using this:</p>

<pre>
&lt;!ATTLIST pre-requisite cref CDATA #IMPLIED&gt;
&lt;!ELEMENT instructor_approval ANY&gt;
&lt;!ELEMENT req_next_sem ANY&gt;
</pre>

<p>The last point to discuss on the topic of conditional sections is nesting. It
is perfectly valid to nest sections within each other. Everything within an
IGNORE section is completely ignored. Basically, once the parser sees an IGNORE,
it skips to the closing marker for that particular section. For INCLUDE
sections, everything is included except any IGNORE sections. A section written
like this:</p>

<pre>
&lt;![ INCLUDE [
     &lt;!ATTLIST pre-requisite cref IDREFS #REQUIRED&gt;
     &lt;![ IGNORE [
          &lt;!ELEMENT instructor_approval EMPTY&gt;
     ]]&gt;
     &lt;!ELEMENT req_next_sem (#PCDATA)&gt;
]]&gt;
</pre>

<p>could have just as well been written like this:</p>

<pre>
&lt;![ INCLUDE [
     &lt;!ATTLIST pre-requisite cref IDREFS #REQUIRED&gt;
     &lt;!ELEMENT req_next_sem (#PCDATA)&gt;
]]&gt;
</pre>

<p>Though basic, this should give you the idea of how nesting works. Through the
use of parameter entities, it can get quite complex.</p>

<p>You should now be well on your way to validating documents using a DTD. This
is just one of the possible ways to perform validation. The next section will
cover XML Schemas and their role in validation.</p>

<div class="remarkbox">
  <div class="rbtitle">continued on a different page</div>
  <div class="rbcontent">
  <p>The rest of this chapter includes: Using XML Schemas, and
  Using RELAX NG. They will be on different pages because this page is too
  long!</p>
  </div>
</div>

EOPAGESTR5;
echo $page_str;

site_footer();

?>