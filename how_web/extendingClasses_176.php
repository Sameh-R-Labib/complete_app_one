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


site_header("Extending Classes");

$page_str = <<<EOPAGESTR5

<p>One of the nice features of the DOM extension is the ability to extend the
core classes. Under PHP 4 and the domxml extension, this functionality was
impossible to achieve. With the capabilities from the new Zend Engine in PHP 5,
this has finally become a reality in DOM. This feature has its limits, which
will be explained within this section.</p>

<p>You can extend one of the DOM classes in the same manner as extending any
other class within PHP 5. You define a class using the extends keyword:</p>

<pre>
class customDoc extends DOMDocument {}

&#36;mydoc = new customDoc();
print &#36;mydoc-&gt;saveXML();
</pre>

<p>In this case, other than creating a new class type extending the DOMDocument
class, you have not defined any custom constructor or additional methods and
properties. The methods and properties from the DOMDocument class, though, are
inherited and, as shown by the last print statement, are invoked just as if you
were using a DOMDocument object.</p>

<p>You can also override the constructor and methods as well as add custom
methods and properties. You must remember a few points when extending the DOM
classes:</p>

<ul>
  <li>Overriding the constructor requires the parent constructor to be
  called.</li>
  <li>Properties built into the DOM classes cannot be overridden.</li>
  <li>Methods built into the DOM classes can be overridden.</li>
  <li>The life span of an extended object is that of the object itself.</li>
</ul>

<p>Other than these points, extended DOM classes work in the same manner as
regular objects and extended objects.</p>

<h3>Overriding the Constructor</h3>

<p>A subclass can override the constructor of a base class by defining its own
constructor. When using the DOM classes, you must invoke the parent constructor
within the extended class’s constructor, or an instantiated object will not be
usable with the DOM extension. For example:</p>

<pre>
class customDoc extends DOMDocument {
   function __construct(&#36;rootName, &#36;rootValue = "") {
      parent::__construct();
      if (! empty(&#36;rootName)) {
         &#36;element = &#36;this-&gt;appendChild(new DOMElement(&#36;rootName, &#36;rootValue));
      }
   }
}
</pre>

<p>The class customDoc is defined and extends the DOMDocument class. A
constructor for this class is also defined that accepts the variables
&#36;rootName for the document element and &#36;rootValue, which is passed when
text content is to be created for the document element when this class is
instantiated.</p>

<p>When an object of the customDoc type is instantiated, this new constructor
is used. The first thing that takes place is the constructor for the parent
class, DOMDocument, is called. This parent constructor must be called prior to
using any of the DOM functionality with this class; otherwise, &#36;this will
not have been properly initialized, and the DOM methods will fail. Once this is
completed, you can use the appendChild() method to set the document element
within the tree. The output of this code results in the following:</p>

<pre>
&lt;?xml version="1.0"?&gt;
&lt;root&gt;content&lt;/root&gt;
</pre>

<h3>Understanding That Properties Cannot Be Overridden</h3>

<p>Properties of a base DOM class cannot be overridden. They can be defined in
the subclass definition but are silently ignored, and the built-in properties
are used. This is a big difference between the constructor and the methods
defined in DOM, because those can both be overridden. For example:</p>

<pre>
class customDoc extends DOMDocument {
   public &#36;nodeName = "customDoc";
}

&#36;myc = new customDoc();
print &#36;myc-&gt;nodeName;
</pre>

<p>This piece of code defines the property nodeName within the customDoc
definition. The nodeName property is also defined in the DOMNode class, which is
inherited by the DOMDocument class. Looking at the code, you might expect
customDoc to be printed, but in actuality #document is printed. Some people may
consider this behavior to be an issue, but it has worked this way from the
beginning, will not be changing, and can easily be worked around by using
different property names.</p>

<h3>Overriding Built-in Methods</h3>

<p>You can override DOM class methods, unlike the properties, through
user-implemented methods. PHP is a typeless language and does not allow casting
an object to a specific class. The method createElement() from the DOMDocument
class returns only an object that is a DOMElement class type. Of course, you can
instantiate different classes that extend a DOMElement using the new keyword;
you might want the createElement() method to return some other class type as
well. For example:</p>

<pre>
class customElement extends DOMElement { }

class customDoc extends DOMDocument {
   function createElement(&#36;name, &#36;value) {
      &#36;custom = new customElement(&#36;name, &#36;value);
      return &#36;custom;
   }
}

&#36;myc = new customDoc();
&#36;myelement = &#36;myc-&gt;createElement("myname", "myvalue");
if (&#36;myelement <b>instanceof</b> customElement) {
   print "This is a customElement";
}
</pre>

<div class="remarkbox">
  <div class="rbtitle">note-to-self</div>
  <div class="rbcontent">
  <p>The reason we can pass name and value to the constructor even though
  we didn't define a constructor which accepts those is that the
  constructor is being inherited...and that one accepts those parameters.</p>
  </div>
</div>

<p>This code implements a custom createElement() method that returns an element
of the customElement class type rather than a DOMElement class. It works well in
that the test using the <em>instanceof operator</em> results in the text This is
a customElement being printed. The only issue with this code is that the new
element is not associated with a document, which occurs through the use of the
native createElement() method. Eventually the <em>adoptNode() method</em> will
be implemented, allowing the node to be associated with a document, but until
that time, the node exists without a document associated until inserted into a
tree.</p>

<h3>Understanding Object Lifetime and Scope</h3>

<p>Scope and object lifetime are features many people struggle with when using
extended classes within the DOM extension. It is important to understand that
DOM objects are not nodes within the tree. This is confusing because accessing
the object directly affects the underlying node in the tree, but the object is
just an “accessor” to the underlying node. This being said, when an object is
instantiated, either by using new or by accessing a node within a tree, the
object itself is not part of any tree or subtree, just the internal node.</p>

<p>Just as in most other languages, objects have a lifetime and are eventually
destroyed. Once an object goes out of scope and no references to this object
exist, it is destroyed. The same rules pertain to objects from the DOM
extension. This is where much confusion comes into play. When no object is
currently referencing an underlying node and the node is accessed, a new object
is created based on the pertinent built-in DOM class. By “pertinent,” I mean
that the DOM class type that pertains to the specific node type is
instantiated.</p>

<p>You may be wondering why all this matters. Using subclasses with the DOM
extension does not guarantee that the original class used to create a node will
be the type of class of the object returned when the node is accessed later in a
script. Consider the effects of using the unset() function on an instantiated
subclassed object:</p>

<pre>
class customElement extends DOMElement { }

&#36;doc = new DOMDocument();

&#36;myelement = &#36;doc-&gt;appendChild(new customElement("custom", "element"));
print get_class(&#36;myelement)."&#92;n";

unset(&#36;myelement);

&#36;myelement = &#36;doc-&gt;documentElement;
print <b>get_class</b>(&#36;myelement)."&#92;n";
</pre>

<p>This code initially defines the class customElement that does not override
anything from the DOMElement class. A DOMDocument object is instantiated, and a
new customElement is appended. This new element is returned as a customElement
object and set to the &#36;myelement variable. The output of the first
<em>get_class() function</em> is customElement and clearly shows that the object
associated with this node is of the customElement type.</p>

<p>Unset() is then called on the &#36;myelement variable; because no other
references exist for this object, the object is destroyed. The element node that
was previously appended as the document element is then accessed with the
documentElement property, and the resulting object is set to the &#36;myelement
variable. Examining the output of the last get_class() function call reveals
that this object is of the DOMElement class and not the customElement class.</p>

<div class="remarkbox">
  <div class="rbtitle">note-to-self</div>
  <div class="rbcontent">
  <p>If what the author is saying is true (word-for-word) then the
  unset function gets rid of the variable but not the object it
  refers to. Hmm! I guess that's another thing that makes objects
  different from other types of values. I assume then that the only
  time an object is destroyed is when there is no way of accessing it
  (either by variable name or tree traversal). If you want to remove
  an object that is in a tree don't use unset.</p>
  </div>
</div>

<div class="remarkbox">
  <div class="rbtitle">Caution</div>
  <div class="rbcontent">
  <p>Objects based on <b>extended</b> DOM classes have a life span and once
  destroyed   no longer associate the extended class type with the underlying
  XML node in the tree. Accessing a node after the object has been destroyed
  results in an object based on a DOM built-in class type and not the extended
  class type.</p>
  </div>
</div>

<p>This is the limitation I previously mentioned about extending DOM classes.
How your code is written will determine whether an object based on an extended
class will be returned or whether it will be based on a built-in DOM class when
accessing a node. You must think about scope carefully when using extended
classes. For example:</p>

<pre>
class customElement extends DOMElement { }

class customDoc extends DOMDocument {
   function addRoot(&#36;name, &#36;value) {
      if (! &#36;this-&gt;documentElement) {
         &#36;custom = new customElement(&#36;name, &#36;value);
         return &#36;this-&gt;appendChild(&#36;custom);
      }
      return NULL;
   }
}

&#36;dom = new customDoc();
&#36;dom-&gt;addRoot("root", "content");
&#36;myelement = &#36;dom-&gt;documentElement;
print get_class(&#36;myelement)."&#92;n";
</pre>

<p>This piece of code creates a customDoc object and adds a document element
using the addRoot() method. The method returns the newly created object or NULL
if a document element already exists. Within the script, however, the return
value is not captured, and when the get_class() is called on the &#36;myelement
object, DOMElement is printed.</p>

<p>You can make a slight change to the code and capture the return value:</p>

<pre>
&#36;myelement = &#36;dom-&gt;addRoot("root", "content");
&#36;myelement = &#36;dom-&gt;documentElement;
print get_class(&#36;myelement)."&#92;n";
</pre>

<p><em class="highlight">In this case, get_class() returns customElement.</em>
Upon returning from the addRoot() method, the object is captured and set to the
&#36;myelement variable. Previously, even though the resulting element was being
returned, it was not captured, and the customElement object created was
immediately destroyed. With this object destroyed, accessing the documentElement
property resulted in a new object associated with the node being created. This
new object, being created automatically from a node access, ends up being based
on the DOMElement class. The updated code keeps the &#36;myelement object in
scope, so when the documentElement property is accessed, it returns the object
already associated with the node, which is of the customElement class.</p>

EOPAGESTR5;
echo $page_str;

site_footer();

?>