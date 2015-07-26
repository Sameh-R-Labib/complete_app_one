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


site_header('Basic PHP Constructs for OOP');

$page_str = <<<EOPAGESTR5

<p>In this section, we cover the basic PHP syntax for OOP from the ground up, with some simple 
examples.</p>

<h2>Defining classes</h2>

<p>The general form for defining a new class in PHP is as follows:</p>

<pre>
class myclass extends myparent {
  var &#36;var1; 
  var &#36;var2 = "constant string"; 
  function myfunc (&#36;arg1, &#36;arg2) {
    [..] 
  }
  [..]
}
</pre>

<p>The form of the syntax is as described, in order, in the following list:</p>

<ul>
  <li>The special form class, followed by the name of the class that you want to define.</li>
  <li>An optional extension clause, consisting of the word extends and then the name of the class 
  that should be inherited from.</li>
  <li>A set of braces enclosing any number of variable declarations and function definitions. 
  Variable declarations start with the special form var, which is followed by a conventional &#36; 
  variable name; they may also have an initial assignment to a constant value. Function 
  definitions look much like standalone PHP functions but are local to the class.</li>
</ul>

<p>As an example, consider the simple class definition below, which prints out a box of text in 
HTML. This is an extremely simple class definition. It has no parent (and, therefore, no extends
clause). It has a single member variable (the variable &#36;body_text), and a single member function 
(the function display()). The display function simply prints out the text variable, wrapped up in 
an HTML table definition.</p>

<pre>
class TextBoxSimple {
  var &#36;body_text = "my text"; 
  function display() {
    print("&lt;TABLE BORDER=1&gt;&lt;TR&gt;&lt;TD&gt;&#36;this-&gt;body_text"); 
    print("&lt;/TD&gt;&lt;/TR&gt;&lt;/TABLE&gt;");
  }
}
</pre>

<h2>Accessing member variables</h2>

<p>In general, the way to refer to a member variable from an object is to follow a variable
containing the object with -&gt; and then the name of the member. So if we had a variable &#36;box
containing an object instance of the class TextBox, we could retrieve its body_text variable with
an expression like:</p>

<pre>
&#36;text_of_box = &#36;box-&gt;body_text;
</pre>

<p>However, when we are writing code within a member function, we haven’t yet created the
object instance, and so we have no variable like &#36;box to refer to. The answer is the magic
variable &#36;this, which (when used inside a member function of a class) refers to the object
instance itself. Note that this is how the display() function in Listing 20-1 retrieves the
text it displays (&#36;this-&gt;body_text).</p>

<p>This syntax can be a little counterintuitive. You might think that we could simply refer
to &#36;body_text in functions within our TextBox class because we have declared it in the class
definition, but in fact the only way to get to members from within a member function definition
is via &#36;this. Notice also that the syntax for this access does not put a &#36; before the member
variable name itself, only the &#36;this variable.</p>

<h2>Creating instances</h2>

<p>After we have a class definition, the default way to make an instance of that class is by
using the new operator. If we have already defined the class TextBoxSimple as in Listing 20-1,
we can make an instance of it, and then use it, like so:</p>

<pre>
&#36;box = new TextBoxSimple;
&#36;box-&gt;display();
</pre>

<p>The result of evaluating this code will be to print an HTML fragment containing a table
definition enclosing the text my text.(Not especially useful, but it's a start.)</p>

<h2>Constructor functions</h2>

<div class="remarkbox">
  <div class="rbtitle">Note</div>
  <div class="rbcontent">
  <p>In this section the author says the name of the constructor is __construct
  whereas in a later section he acts as if the name should be the same as the
  class name.</p>
  <p>Quoting the author: "a subclass constructor and a superclass constructor
  always have different names."</p>
  </div>
</div>

<p>One way in which our TextBoxSimple class is not very useful is that its instances do not contain
any data when they are created, except for the static initialization of the variable
&#36;body_text. The point of such a class would be to display arbitrary pieces of text, not the
same message every time. It’s true that we could make an instance and then install the right
data in the instance's internal variables, like so:</p>

<pre>
&#36;box = new TextBoxSimple;
&#36;box-&gt;body_text = "custom text";
&#36;box-&gt;display();
</pre>

<p>But that would be cumbersome and error-prone as we build more complex objects.</p>

<p>The correct way to arrange for data to be appropriately initialized is by writing a
constructor function — a special function called __construct(), which will be called
automatically whenever a new instance is created.</p>

<div class="remarkbox">
  <div class="rbtitle">Note</div>
  <div class="rbcontent">
  <p>The __ in __construct() is actually two underscores next to each other.</p>
  </div>
</div>

<p>Modifying our previous example to include a constructor function gives us Listing 20-2.</p>

<pre>
class TextBox {
  var &#36;body_text = "my text";
  // Constructor function
  function __construct(&#36;text_in) {
    &#36;this-&gt;body_text = &#36;text_in;
  }
  function display() {
    print("&lt;TABLE BORDER=1&gt;&lt;TR&gt;&lt;TD&gt;&#36;this-&gt;body_text");
    print("&lt;/TD&gt;&lt;/TR&gt;&lt;/TABLE&gt;");
  }
}
// creating an instance
&#36;box = new TextBox("custom text");
&#36;box-&gt;display();
</pre>

<p>As the preceding code is executed, the output is an HTML table enclosing the text custom
text.</p>

<div class="remarkbox">
  <div class="rbtitle">Note</div>
  <div class="rbcontent">
  <p>There should be only one constructor function per class definition. Defining more than
  one such function is syntactically legal, but pointless, as only the definition that occurs
  last will be in effect. If you'd like to have different constructors to handle different
  numbers and types of input arguments, see the section "Simulating Polymorphism" later in this
  chapter.</p>
  </div>
</div>

<h2>Inheritance</h2>

<p>PHP class definitions can optionally inherit from a parent class definition by using the
extends clause. The syntax is as follows:</p>

<pre>
class Child extends Parent {
 &lt;definition body&gt;
}
</pre>

<p>The effect of inheritance is that the child class (or subclass or derived class) has the
following characteristics:</p>

<ul>
  <li>Automatically has all the member variable declarations of the parent class (or super-class
  or base class).</li>
  <li>Automatically has all the same member functions as the parent, which (by default) will
  work the same way as those functions do in the parent.</li>
</ul>

<p>In addition, the child class can add on any desired variables or functions simply by including
them in the class definition in the usual way.</p>

<p>In Listing 20-2, we defined a class called TextBox; now we'll define a class called
TextBoxHeader that extends TextBox (seeListing20-3). TextBoxHeader has two member variables:
one (&#36;body_text) that it receives through inheritance from TextBox, and another
(&#36;header_text) that it defines itself. Like TextBox, it has a constructor function and a
function called display. This function definition overrides the display function in TextBox.</p>

<pre>
class TextBoxHeader extends TextBox
{
  var &#36;header_text;

  // CONSTRUCTOR
  function __construct(&#36;header_text_in, &#36;body_text_in) {
    &#36;this-&gt;header_text = &#36;header_text_in;
    &#36;this-&gt;body_text = &#36;body_text_in;
  }

  // MAIN DISPLAY FUNCTION
  function display() {
    &#36;header_html = &#36;this-&gt;make_header(&#36;this-&gt;header_text);
    &#36;body_html = &#36;this-&gt;make_body(&#36;this-&gt;body_text);
    print("&lt;TABLE BORDER=1&gt;&lt;TR&gt;&lt;TD&gt;&#92;n");
    print("&#36;header_html&#92;n");
    print("&lt;/TD&gt;&lt;/TR&gt;&lt;TR&gt;&lt;TD&gt;&#92;n");
    print("&#36;body_html&#92;n");
    print("&lt;/TD&gt;&lt;/TR&gt;&lt;/TABLE&gt;&#92;n");
  }

  // HELPER FUNCTIONS
  function make_header (&#36;text) {
    return(&#36;text);
  }
  function make_body (&#36;text) {
    return(&#36;text);
  }
}
</pre>

<h2>Overriding functions</h2>

<p>Function definitions in child classes override definitions with the same name in parent
classes. This just means that the overriding definition in the more specific class takes
precedence and will be the one actually executed. In the example in Listing 20-3, the
TextBoxHeader class defines a function called display(), which means that executing the
following code:</p>

<pre>
&#36;text_box_header = new TextBoxHeader("The Header", "The Body");
&#36;text_box_header-&gt;display();
</pre>

<p>will result in a call to TextBoxHeader’s display() function, not the display() function in
TextBox. The resulting HTML output prints a box with a header of The Header and a body of The
Body. The more specific display() function takes total responsibility here; there is no call,
either
explicit or implicit, to the display() function defined in the TextBox class. (Although PHP
makes no such implicit calls, it is possible to explicitly call functions that have been
defined in a parent class — see "Calling parent functions" in the "Advanced OOP Features"
section later in the chapter.)</p>

<p>The flip side of overriding functions, however, is that whenever a subclass does not
override a parental definition, the parent’s definition will be in effect. Note that the
"helper" functions in the definition of TextBoxHeader don't really do anything interesting,
and you might wonder why we bothered to separate them out. The answer is that this provides
an opportunity for an inheriting class to do something interesting with those functions by
selectively overriding them — or not, as they see fit.</p>

<p>PHP5 (as a result of Zend Engine 2) introduces the final keyword. If, in the previous
example, the definition of display() in class TextBox had looked like this:</p>

<pre>
  final function display() {
    print("&lt;TABLE BORDER=1&gt;&lt;TR&gt;&lt;TD&gt;&#36;this-&gt;body_text");
    print("&lt;/TD&gt;&lt;/TR&gt;&lt;/TABLE&gt;");
  }
</pre>

<p>then the method could not have been overridden by a definition in TextBoxHeader.</p>

<p>It is possible to declare whole classes final, and individual methods, but not individual
properties.</p>

<h2>Chained subclassing</h2>

<p>PHP does not support multiple inheritance but does support chained subclassing. This
is a fancy way of saying that, although each class can have only a single parent, classes
can still have a long and distinguished ancestry (grandparents, great-grandparents, and
so on). Also, there’s no restriction on family size; each parent class can have an
arbitrary number of children.</p>

<p>As example, see Listing 20-4, where our definition of TextBoxBoldHeader inherits from
TextBoxHeader, which in turn inherits from TextBox.</p>

<p>Listing 20-4: TextBoxBoldHeader</p>

<pre>
class TextBoxBoldHeader extends TextBoxHeader {

  // CONSTRUCTOR
  function __construct(&#36;header_text_in, &#36;body_text_in) {
    &#36;this-&gt;header_text = &#36;header_text_in;
    &#36;this-&gt;body_text = &#36;body_text_in;
  }

  // HELPER FUNCTIONS
  // make_header overrides parent
  function make_header (&#36;text) {
    return("&lt;B&gt;&#36;text&lt;/B&gt;");
  }
}
</pre>

<p>This definition of TextBoxBoldHeader is minimal; it defines no new member
variables and defines only one function besides its constructor. That new
function (make_header()) overrides the definition in its parent. Now what
happens when we actually use this definition in the usual way?</p>

<pre>
&#36;text_box_bold_header = new TextBoxBoldHeader("The Header", "The Body");
&#36;text_box_bold_header-&gt;display();
</pre>

<p>It's worth looking in a bit of detail to see exactly what happens when
we make these two function calls.</p>

<ol>
  <li>No display() function is found in TextBoxBoldHeader, so the version from
TextBoxHeader is called.</li>
  <li>The first function call in that version of display() is to
  &#36;this-&gt;make_header(). Remember that &#36;this refers to the object instance
  that we started with, which happens to be an instance of
  TextBoxBoldHeader, so PHP looks first of all for a definition from that
  class. It finds one and uses it to return the header string wrapped up in the
  HTML bold text construct (&lt;B&gt;&lt;/B&gt;).</li>
  <li>The second function call is to &#36;this-&gt;make_body(). This time, though,
  there is no overriding definition in TextBoxBoldHeader, so the version from
  TextBoxHeader is used.</li>
</ol>

<p>The upshot is that, in defining TextBoxBoldHeader, we mostly exploited
the behavior of the parent class, but were able to change its behavior
slightly by overriding a single member function.</p>

<h2>Modifying and assigning objects</h2>

<p>Prior to PHP5, when you assigned an object to a variable or passed it to
a function, that object was actually copied, bit-for-bit, into the variable
or function scope. That caused tremendous hassles, and programmers had to be
careful to devise clever workarounds for the problems.</p>

<p>The problem is solved as of PHP5, which incorporates Zend Engine 2. Zend
Engine 2 copies by reference, rather than explicitly. That is, several
variables can point to the exact same object and expect changes made via one
reference to be reflected in the others.</p>

<h2>Scoping issues</h2>

<p>Before we move onto the more advanced features of PHP’s version of OOP,
it's important to nail down issues of scope — that is, which names are
meaningful in what way to different parts of our code. It may seem as though
the introduction of classes, instances, and member functions have made
questions of scope much more complicated. Actually, though, there are only a
few basic rules we need to add to make OOP scope sensible within the rest of
PHP:</p>

<ul>
  <li>Names of member variables and member functions are never meaningful to
  calling code on their own — they must always be reached via the -&gt;
  construct (or, as we'll see in the "Advanced OOP Features" section, the
  :: construct). This is true both outside the class definition and inside
  member functions.</li>
  <li>The names visible within member functions are exactly the same as the
  names visible within global functions — that is, member functions can refer
  freely to other global functions, but can’t refer to normal global variables
  unless those variables have been declared global inside the member function
  definition.</li>
</ul>

<p>These rules, together with the usual rules about variable scope in PHP,
are respected in the intentionally confusing example in Listing 20-5. What
number would you expect that code to print when executed?</p>

<p>Listing 20-5: Confusing scope</p>

<pre>
&#36;my_global = 3;

function my_function (&#36;my_input) {
  global &#36;my_global;
  return(&#36;my_global * &#36;my_input);
}

class MyClass {
  var &#36;my_member;
  function __construct(&#36;my_constructor_input) {
    &#36;this-&gt;my_member = &#36;my_constructor_input;
  }

  function myMemberFunction (&#36;my_input) {
    global &#36;my_global;
    return(&#36;my_global * &#36;my_input * my_function(&#36;this-&gt;my_member));
  }
}

&#36;my_instance = new MyClass(4);
print("The answer is: " . &#36;my_instance-&gt;myMemberFunction(5));
</pre>

<p>The answer is: 180 (or 3 * 5 * (3 * 4)). If any of these numerical variables
had been undefined when multiplied, we would have expected the variable to have
a default value of 0, making the answer have a value of 0 as well. This would
have happened if we had:</p>

<ul>
  <li>Left out the global declaration in my_function()</li>
  <li>Left out the global declaration in myMemberFunction()</li>
  <li>Referred to &#36;my_member rather than &#36;this-&gt;my_member</li>
</ul>

EOPAGESTR5;
echo $page_str;

site_footer();

?>