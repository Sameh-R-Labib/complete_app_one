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


site_header('Advanced OOP Features');

$page_str = <<<EOPAGESTR5

<p>In the previous section, we presented a minimal subset of PHP’s
object-oriented constructs that let you use the most basic OOP
techniques. In this section, we look at some of the slightly more
unusual constructs, techniques, and gotchas that can get you into more
trouble. (We defer any discussion of the functions that give
meta-information about classes and objects to the section “Introspection
Functions,” later in this chapter.)</p>

<h2>Public, Private, and Protected Members</h2>

<p>Unless you specify otherwise, properties and methods of a class are
public. That is to say, they may be accessed in three possible situations:</p>

<ul>
  <li>From outside the class in which it is declared</li>
  <li>From within the class in which it is declared</li>
  <li>From within another class that implements the class in which it is
  declared</li>
</ul>

<p>If you wish to limit the accessibility of the members of a class, you
should use private or protected. These keywords are new under Zend Engine
2, which was first standard under PHP5.</p>

<h2>Private members</h2>

<p>By designating a member private, you limit its accessibility to the
class in which it is declared. The private member cannot be referred to
from classes that inherit the class in which it is declared and cannot be
accessed from outside the class.</p>

<p>Making a member private is straightforward:</p>

<pre>
class MyClass {

  private &#36;colorOfSky = "blue";
  &#36;nameOfShip = "Java Star";

  function __construct(&#36;incomingValue) {
  &#47;&#47; Statements here run every time an instance of the class
  &#47;&#47; is created.
  }

  function myPublicFunction (&#36;my_input) {
    return("I'm visible!");
  }

  private function myPrivateFunction (&#36;my_input) {
    global &#36;my_global;
    return(&#36;my_global * &#36;my_input * my_function(&#36;this-&gt;my_member));
  }

}
</pre>

<p>When that class is inherited by another class (using extends),
myPublicFunction() will be visible, as will &#36;nameOfShip. The extending
class will not have any awareness of or access to myPrivateFunction, because
it is declared private.</p>

<h2>Protected members</h2>

<p>A protected property or method is accessible in the class in which it is
declared, as well as in classes that extend that class. Protected members are
not available outside of those two kinds of classes, however.</p>

<p>Here is a different version of MyClass:</p>

<pre>
class MyClass {

  protected &#36;colorOfSky = "blue";
  &#36;nameOfShip = "Java Star";

  function __construct(&#36;incomingValue) {
  // Statements here run every time an instance
  // of the class is created.
  }

  function myPublicFunction (&#36;my_input) {
    return("I'm visible!");
  }

  protected function myProtectedFunction (&#36;my_input) {
    global &#36;my_global;
    return(&#36;my_global * &#36;my_input * my_function(&#36;this-&gt;my_member));
  }

}
</pre>

<p>If we had another class that extended MyClass, it would be able to see and
use &#36;colorOfSky and myProtectedFunction(), just as if they were public.
It would not, however, be possible to call MyClass::&#36;colorOfSky. You’ll
read more about the :: syntax later in this chapter.</p>

<h2>Interfaces</h2>

<p>In large object-oriented projects, there is some advantage to be realized in
having standard names for methods that do certain work. For example, if many
classes in a software application needed to be able to send e-mail messages,
it would be desirable if they all did the job with methods of the same name.
As of PHP5, it is possible to define an interface, like this:</p>

<pre>
interface Mail {
  public function sendMail();
}
</pre>

<p>Then, if another class implemented that interface, like this:</p>

<pre>
class Report implements Mail {
  // Definition goes here
}
</pre>

<p>it would be required to have a method called sendMail. It’s an aid to
standardization.</p>

<div class="remarkbox">
  <div class="rbtitle">Clarifications</div>
  <div class="rbcontent">
  <p>The term public does not mean sendMail is a built in PHP function.</p>
  <p>An interface is used to specify a collection of method names which must be
  defined for classes associated with that interface.</p>
  <p>See PHP manual for more details about interface and implements.</p>
  </div>
</div>

<h2>Constants</h2>

<p>A constant is somewhat like a variable, in that it holds a value, but is
really more like a function because a constant is immutable. Once you declare
a constant, it does not change. Declaring one is easy, as is done in this
version of MyClass:</p>

<pre>
class MyClass {

  const requiredMargin = 1.3;

  function __construct(&#36;incomingValue) {
  // Statements here run every time an instance of the class
  // is created.
  }
}
</pre>

<p>In that class, requiredMargin is a constant. It is declared with the
keyword const, and under no circumstances can it be changed to anything other
than 1.3. Note that the constant’s name does not have a leading &#36;, as
variable names do.</p>

<h2>Abstract Classes</h2>

<p>An abstract class is one that cannot be instantiated, only inherited. You
declare an abstract class with the keyword abstract, like this:</p>

<pre>
abstract class MyAbstractClass {

  abstract function myAbstractFunction() {
  }

}
</pre>

<p>Note that function definitions inside an abstract class must also be
preceded by the keyword abstract. It is not legal to have abstract function
definitions inside a non-abstract class.</p>

<h2>Simulating class functions</h2>

<p>Some other OOP languages make a distinction between instance member
variables, on the one hand, and class or static member variables on the
other. Instance variables are those that every instance of a class has a
copy of (and may possibly modify individually); class variables are shared
by all instances of the class. Similarly, instance functions depend on
having a particular instance to look at or modify; class (or static)
functions are associated with the class but are independent of any instance
of that class.</p>

<p>In PHP, there are no declarations in a class definition that indicate
whether a function is intended for per-instance or per-class use. But PHP
does offer a syntax for getting to functions in a class even when no instance
is handy. The :: syntax operates much like the -&gt; syntax does, except
that it joins class names to member functions rather than instances to members.
For example, in the following implementation of an extremely primitive
calculator, we have some functions that depend on being called in a particular
instance and one function that does not:</p>

<pre>
class Calculator {

  var &#36;current = 0;

  function add(&#36;num) {
    &#36;this-&gt;current += &#36;num;
  }

  function subtract(&#36;num) {
    &#36;this-&gt;current -= &#36;num;
  }

  function getValue() {
    return(&#36;current);
  }

  function pi() {
    return(M_PI); // the PHP constant
  }

}
</pre>

<p>We are free to treat the pi() function as either a class function or an
instance function and access it using either syntax:</p>

<pre>
&#36;calc_instance = new Calculator;
&#36;calc_instance-&gt;add(2);
&#36;calc_instance-&gt;add(5);
print("Current value is " . &#36;calc_instance-&gt;current . "&lt;BR&gt;");
print("Value of pi is " . &#36;calc_instance-&gt;pi() . "&lt;BR&gt;");
print("Value of pi is " . Calculator::pi() . "&lt;BR&gt;");
</pre>

<p>This means that we can use the pi() function even when we don’t have an
instance of Calculator at hand. The Calculator class has to be accessible
in either case, though, meaning it has to have been imported with a
require_once statement, or something similar.</p>

<h2>Calling parent functions</h2>

<p>Asking an instance to call a function will always result in the most
specific version of that function being called, because of the way overriding
works. If the function exists in the instance’s class, the parent’s version of
that function will not be executed.</p>

<p>Sometimes it is handy for code in a subclass to explicitly call functions
from the parent class, even if those names have been overridden. It’s also
sometimes useful to define subclass functions in terms of superclass functions,
even when the name is available.</p>

<h3>Calling parent constructors</h3>

<p>In the section “Inheritance” earlier in this chapter, we showed you code
(Listing 20-3) where both subclass and superclass had constructors, and both
constructors set a variable that was defined by the superclass. This might be
stylistically dodgy, but more importantly, we would like to avoid duplicating
work across the two constructors, especially if a lot of code is involved.</p>

<p>Instead of writing an entirely new constructor for the subclass, let’s write
it by calling the parent’s constructor explicitly and then doing whatever is
necessary in addition for instantiation of the subclass. Here’s a simple
example:</p>

<pre>
class Name {

  var &#36;_firstName;
  var &#36;_lastName;

  function Name(&#36;first_name, &#36;last_name) {
    &#36;this-&gt;_firstName = &#36;first_name;
    &#36;this-&gt;_lastName = &#36;last_name;
  }

  function toString() {
    return(&#36;this-&gt;_lastName . ", " . &#36;this-&gt;_firstName);
  }

}


class NameSub1 extends Name {

  var &#36;_middleInitial;

  function NameSub1(&#36;first_name, &#36;middle_initial, &#36;last_name) {
    Name::Name(&#36;first_name, &#36;last_name);
    &#36;this-&gt;_middleInitial = &#36;middle_initial;
  }

  function toString() {
    return(Name::toString() . " " . &#36;this-&gt;_middleInitial);
  }

}
</pre>

<p>In this example, we have a parent class (Name), which has a two-argument
constructor, and a subclass (NameSub1), which has a three-argument constructor.
The constructor of NameSub1 functions by calling its parent constructor
explicitly using the :: syntax (passing two of its arguments along) and then
setting an additional field. Similarly, NameSub1 defines its nonconstructor
toString() function in terms of the parent function that it overrides.</p>

<p>It might seem strange to call Name::Name() here, without reference to
&#36;this. The good news is that both &#36;this and any member variables that
are local to the parent are available to a parent function when invoked from a
child instance.</p>

<h3>The special name parent</h3>

<p>There is a stylistic objection to the previous example, which is that we
have hardcoded the name of a parent class into the code for a subclass. Some
would say that this is bad style because it makes it harder to revise the class
hierarchy later. A fix is to use the special name parent, which when used in a
member function, always refers to the parent class of the current class. Here
is a revised version of the example using parent rather than Name:</p>

<pre>
class NameSub2 extends Name {

  var &#36;_middleInitial;

  function NameSub2(&#36;first_name, &#36;middle_initial, &#36;last_name) {
    &#36;parent_class = get_parent_class(&#36;this);
    parent::&#36;parent_class(&#36;first_name, &#36;last_name);
    &#36;this-&gt;_middleInitial = &#36;middle_initial;
  }

  function toString() {
    return(parent::toString() . " " . &#36;this-&gt;_middleInitial);
  }

}
</pre>

<p>Notice that we’ve swapped in parent for all instances of Name whenever it’s
used as the name of a class. We had to do a little bit of extra work, though,
when finding a replacement for the call Name::Name() because the second name in
that call is actually the name of a constructor function. PHP does not accept
parent as the name of a function, so we retrieve the constructor name using the
function get_parent_class() (which we cover in the section “Introspection
Functions” later in this chapter).</p>

<p>This replacement version could be attached to a different parent class
simply by changing the class named in the extends clause (as long as the parent
constructor is expecting the same two arguments).</p>

<h2>Automatic calls to parent constructors</h2>

<p>In a sense, constructor functions in a subclass override the constructors in
superclasses. (We say “in a sense” because we usually only say that one
function overrides another if the two functions have the same name; a subclass
constructor and a superclass constructor always have different names.)</p>

<p>As you saw in the previous section, if you want both the subclass
constructor and the superclass constructor to be called, you must include code
in the subclass to call the superclass code explicitly. Beginning with PHP4, if
a subclass lacks a constructor function and a superclass has one, the
superclass’s constructor will be invoked. The most specific constructor that
can be found (if any) will be called — anything else is up to the
programmer.</p>

<h2>Simulating method overloading</h2>

<p>One neat trick offered by some OOP languages (and not offered by PHP) is
automatic overloading of member functions. This means that you can define
several different member functions with the same name but different signatures
(number and types of arguments). The language itself takes care of matching up
calls to those functions with the right version of the function, based on the
arguments that are given.</p>

<p>PHP does not offer such a capability, but the loose typing of PHP lets you
take care of one half of the overloading equation — you can define a single
function of a given name that behaves differently based on the number and types
of arguments it is called with. The result looks like an overloaded function to
the caller (but not to the definer).</p>

<p>Here’s an example of an apparently overloaded constructor function:</p>

<pre>
class MyClass {

  var &#36;string_var = "default string";
  var &#36;num_var = 42;

  function __construct(&#36;arg1) {
    if (is_string(&#36;arg1)) {
      &#36;this-&gt;string_var = &#36;arg1;
    } elseif (is_int(&#36;arg1) || is_double(&#36;arg1)) {
      &#36;this-&gt;num_var = &#36;arg1;
    }
  }

}

&#36;instance1 = new MyClass("new string");
&#36;instance2 = new MyClass(5);
</pre>

<p>The constructor of this class will look to its caller as though it is
overloaded, with different behavior based on the type of its inputs. You can
also vary behavior based on the number of arguments by testing the number of
arguments supplied by the caller.</p>

<div class="remarkbox">
  <div class="rbtitle">Cross-Reference</div>
  <div class="rbcontent">
    <p>For information on writing functions with variable numbers of arguments,
    see Chapter 26. The techniques work the same way with member functions in
    classes as they do with standalone user-defined functions.</p>
  </div>
</div>

<h2>Serialization</h2>

<p>Serialization of data means converting it into a string of bytes in such a
way that you can produce the original data again from the string (via a
process known, unsurprisingly, as unserialization). After you have the
ability to serialize/unserialize, you can store your serialized string pretty
much anywhere (a system file, a database, and so on) and recreate a copy of the
data again when needed.</p>

<div class="remarkbox">
  <div class="rbtitle">In other words:</div>
  <div class="rbcontent">
  <p>This is useful for storing or passing PHP values around without losing
  their type and structure. Objects and arrays are good candidates for
  serialization. Keep in mind that even an int value comes back from a database
  as a string.</p>
  </div>
</div>

<p>PHP offers two functions, serialize() and unserialize(), which take a value
of any type (except type resource) and encode the value into string form and
decode again, respectively. The PHP3 implementation of object serialization
wasn’t very useful because member function definitions didn’t survive the
serialization/unserialization process; beginning with version 4, however, PHP
robustly recreates all important aspects of the instance from the string, as
long as the class definition is available to the code where unserialize() is
called.</p>

<p>Here is a quick example, which we’ll extend later in this section:</p>

<pre>
class ClassToSerialize {

  var &#36;storedStatement = "data";

  function __construct(&#36;statement) {
    &#36;this-&gt;storedStatement = &#36;statement;
  }

  function display () {
    print(&#36;this-&gt;storedStatement . "&lt;BR&gt;");
  }

}

&#36;instance1 = new ClassToSerialize("You're objectifying me!");
&#36;serialization = serialize(&#36;instance1);
&#36;instance2 = unserialize(&#36;serialization);
&#36;instance2-&gt;display();
</pre>

<p>This class has just one member variable and a couple of member functions,
but it’s sufficient to demonstrate that both member variables and member
functions can survive serialization. We create an object, convert it to a
serialized string, convert it back to a new instance, and the printed result
is the accurate complaint (You’re objectifying me!).</p>

<p>Of course, there is no point in serializing and unserializing an object in
the same script. Serialization is only worthwhile when we expect the serialized
string to outlive the script (and the variable) that it currently lives in and
be reincarnated in another execution. This may be because we store the
serialization in a file or a database and read it back in again. It can also
happen automatically as a result of PHP’s session mechanism — variables that
are registered as belonging to a session will be serialized and unserialized
from page to page.</p>

<div class="remarkbox">
  <div class="rbtitle">Cross-Reference</div>
  <div class="rbcontent">
  <p>For more on how the session mechanism uses serialization, see Chapter 26.</p>
  </div>
</div>

<h3>Sleeping and waking up</h3>

<p>PHP provides a hook mechanism so that objects can specify what should
happen just before serialization and just after unserialization. The special
member function __sleep() (that’s two underscores before the word sleep), if
defined in an object that is being serialized, will be called automatically at
serialization time. It is also required to return an array of the names of
variables whose values are to be serialized. This offers a way to not bother
serializing member variables that are not expected to survive serialization
anyway (such as database resources) or that are expensive to store and can be
easily recreated. The special function __wakeup() (again, two underscores) is
the flip side — it is called at unserialization time (if defined in the class)
and is likely to do the inverse of whatever is done by __sleep() (restore
database connections that were dropped by __sleep() or recreate variables that
__sleep() said not to bother with).</p>

<p>You may wonder why these functions are necessary — couldn’t the code that
calls serialize() also just do whatever is necessary to shut down the object?
Actually, it’s very much in keeping with OOP to include such knowledge in the
class definition rather than expecting code using the objects to know about
their special needs. Also the calling code may have no knowledge of the
object’s internals at all (as in the code that serializes all session objects).
The author of the class is uniquely qualified to say what should happen when an
instance is sent away or revived.</p>

<p>As an example of how to use these functions, here is the previous
serialization example, augmented with an extra variable, and the __sleep() and
__wakeup() functions.</p>

<pre>
class ClassToSerialize2 {

  var &#36;storedStatement = "data";
  var &#36;easilyRecreatable = "data again";

  function __construct(&#36;statement) {
    &#36;this-&gt;storedStatement = &#36;statement;
    &#36;this-&gt;easilyRecreatable = &#36;this-&gt;storedStatement . " Again!";
  }

  function __sleep() {
    // Could include DB cleanup code here
    return array(‘storedStatement');
  }

  function __wakeup() {
    // Could include DB restoration code here
    &#36;this-&gt;easilyRecreatable = &#36;this-&gt;storedStatement . " Again!";
  }

  function display () {
    print(&#36;this-&gt;easilyRecreatable . "&lt;BR&gt;");
  }

}

&#36;instance1 = new ClassToSerialize2("You're objectifying me!");
&#36;serialization = serialize(&#36;instance1);
&#36;instance2 = unserialize(&#36;serialization);
&#36;instance2-&gt;display();
</pre>

<p>The variable called &#36;easilyRecreatable is meant to stand in for a piece
of data that is 1) expensive to store and 2) is implied by the other data in
the class anyway. The definition of __sleep() does no cleanup itself, but it
returns an array that contains only one variable name and does not include
easilyRecreatable. At serialization time, only the value of the variable
storedStatement is included in the string. When the object is recreated, the
__wakeup() function assigns a value into &#36;this->easilyRecreatable, which is
then displayed: You’re objectifying me! Again!.</p>

<h3>Serialization gotchas</h3>

<p>The serialization mechanism is pretty reliable for objects, but there are
still a few things that can trip you up:</p>

<ul>
  <li>The code that calls unserialize() must also have loaded the definition
  of the relevant class. (This is also true of the code that calls serialize()
  too, of course, but that will usually be true because the class definition is
  needed for object creation in the first place.)</li>
  <li>Object instances can be created from the serialized string only if it is
  really the same string (or a copy thereof). A number of things can happen to
  the string along the way, if stored in a database (make sure that slashes
  aren’t being added or subtracted in the process), or if passed as url or form
  arguments. (Make sure that your URL-encoding/decoding is preserving exactly
  the same string and that the string is not long enough to be truncated by
  length limits.)</li>
  <li>If you choose to use __sleep(), make sure that it returns an array of the
  variables to be preserved; otherwise no variable values will be preserved.
  (If you do not define a __sleep() function for your class, all values will be
  preserved.)</li>
</ul>

<p>One other potential gotcha: At press time, using PHP 5.0 b2, the values of
variables declared private did not survive serialization/unserialization using
__sleep(). This may be fixed in the final release, but if you find that objects
are not identical after undergoing serialization, investigate whether only the
private variables are missing.</p>

<div class="remarkbox">
  <div class="rbtitle">Useful-to-Know</div>
  <div class="rbcontent">
  <p>When storing a serialized value in a database it is good to use
  base64_encode() and base64_decode() since the [", ', :, ;] charachters
  cause problems for the database and you can't use addslashes or stripslashes
  (explicitly or implicitly).</p>
  <p>Make the database field type something like BLOB which is binary and
  can store very long strings. The encoded value is greater in size than
  the original value. You need binary so there won't be problems with
  character sets used either by the PHP string or the database.</p>
  </div>
</div>

EOPAGESTR5;
echo $page_str;

site_footer();

?>