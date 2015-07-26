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


site_header("PHP's OOP Introspection Functions");

$page_str = <<<EOPAGESTR5

<p>While PHP lacks some features of full OO languages like Java or C++, it is
surprisingly good in the esoteric area of introspection. Introspection allows
the programmer to ask objects about their classes, ask classes about their
parents, and find out all the parts of an object without have to crunch the
source code to do it. Introspection also can help you to write some
surprisingly flexible code, as we will see.</p>

<h2>Function overview</h2>

<p>Most of this section will be example-driven, but we begin by looking at the
introspection functions provided by PHP. Table 20-1 summarizes these functions,
what they do, and what version of PHP introduced them. (This table is
essentially a reframing of information from the online manual; we offer it here
mainly because it highlights features that we found somewhat confusing the
first time we studied the manual.)</p>

<table class="events" width="678">
  <caption>Table 20-1: Class/Object Functions</caption>
  <tr>
    <th>Function</th>
    <th>Description</th>
    <th>Operates on Class Names</th>
    <th>Operates on Instances</th>
    <th>As of PHP Version</th>
  </tr>
  <tr>
    <td>
      <p>get_class()</p>
    </td>
    <td>
      <p>Returns the name of the class an object belongs to.</p>
    </td>
    <td>
      <p>No</p>
    </td>
    <td>
      <p>Yes</p>
    </td>
    <td>
      <p>4.0.0</p>
    </td>
  </tr>
  <tr>
    <td>
      <p>get_parent_class()</p>
    </td>
    <td>
      <p>Returns the name of the parent class of the given instance or
      class.</p>
    </td>
    <td>
      <p>Yes (as of PHP v.4.0.5)</p>
    </td>
    <td>
      <p>Yes</p>
    </td>
    <td>
      <p>4.0.0, 4.0.5</p>
    </td>
  </tr>
  <tr>
    <td>
      <p>class_exists()</p>
    </td>
    <td>
      <p>Returns TRUE if the string argument is the name of a class, FALSE
      otherwise.</p>
    </td>
    <td>
      <p>Yes</p>
    </td>
    <td>
      <p>No</p>
    </td>
    <td>
      <p>4.0.0</p>
    </td>
  </tr>
  <tr>
    <td>
      <p>get_declared_classes()</p>
    </td>
    <td>
      <p>Returns an array of strings representing names of classes defined in
      the current script.</p>
    </td>
    <td>
      <p>N/A</p>
    </td>
    <td>
      <p>N/A</p>
    </td>
    <td>
      <p>4.0.0</p>
    </td>
  </tr>
  <tr>
    <td>
      <p>is_subclass_of()</p>
    </td>
    <td>
      <p>Returns TRUE if the class of its first argument (an object instance)
      is a subclass of the second argument (a class name), FALSE otherwise.</p>
    </td>
    <td>
      <p>No</p>
    </td>
    <td>
      <p>Yes</p>
    </td>
    <td>
      <p>4.0.0</p>
    </td>
  </tr>
  <tr>
    <td>
      <p>is_a()</p>
    </td>
    <td>
      <p>Returns TRUE if the class of its first argument (an object instance)
      is a subclass of the second argument (a class name), or is the same
      class, and FALSE otherwise.</p>
    </td>
    <td>
      <p>No</p>
    </td>
    <td>
      <p>Yes</p>
    </td>
    <td>
      <p>4.2.0(?) (CVS only)</p>
    </td>
  </tr>
  <tr>
    <td>
      <p>get_class_vars()</p>
    </td>
    <td>
      <p>Returns an associative array of var/value pairs representing the name
      of variables in the class and their default values. Variables without
      default values will not be included.</p>
    </td>
    <td>
      <p>Yes</p>
    </td>
    <td>
      <p>No</p>
    </td>
    <td>
      <p>4.0.0</p>
    </td>
  </tr>
  <tr>
    <td>
      <p>get_object_vars()</p>
    </td>
    <td>
      <p>Returns an associative array of var/value pairs representing the name
      of variables in the instance and their default values. Variables without
      values will not be included.</p>
    </td>
    <td>
      <p>No</p>
    </td>
    <td>
      <p>Yes</p>
    </td>
    <td>
      <p>4.0.0</p>
    </td>
  </tr>
  <tr>
    <td>
      <p>method_exists()</p>
    </td>
    <td>
      <p>Returns TRUE if the first argument (an instance) has a method named by
      the second argument (a string) and FALSE otherwise.</p>
    </td>
    <td>
      <p>No</p>
    </td>
    <td>
      <p>Yes</p>
    </td>
    <td>
      <p>4.0.0</p>
    </td>
  </tr>
  <tr>
    <td>
      <p>get_class_methods()</p>
    </td>
    <td>
      <p>Returns an array of strings representing the methods in the object or
      instance.</p>
    </td>
    <td>
      <p>Yes</p>
    </td>
    <td>
      <p>Yes (as of v4.0.6)</p>
    </td>
    <td>
      <p>4.0.0, 4.0.6</p>
    </td>
  </tr>
  <tr>
    <td>
      <p>call_user_method()</p>
    </td>
    <td>
      <p>Takes a string representing a method name, an instance that should
      have such a method, and additional arguments. Returns the result of
      applying the method (and the arguments) to the instance.</p>
    </td>
    <td>
      <p>No</p>
    </td>
    <td>
      <p>Yes</p>
    </td>
    <td>
      <p>3.0.3, 4.0.0</p>
    </td>
  </tr>
  <tr>
    <td>
      <p>call_user_method _array()</p>
    </td>
    <td>
      <p>Same as call_user_method(), except that it expects its third argument
      to be an array containing the arguments to the method.</p>
    </td>
    <td>
      <p>No</p>
    </td>
    <td>
      <p>Yes</p>
    </td>
    <td>
      <p>4.0.5</p>
    </td>
  </tr>
</table>

<p>These functions break down into the following four broad categories:</p>

<ul>
  <li>Getting information about the class hierarchy</li>
  <li>Finding out about member variables</li>
  <li>Finding out about member functions</li>
  <li>Actually calling member functions</li>
</ul>

<p>The first group of functions (get_class() through is_a()) deal with
discovering what classes exist, asking an object about its class, and
discovering class inheritance relationships. Some of these functions start
with an instance of an object, some start with the class name as a string,
and some are happy with either one. (We’ve included columns in the table to try
to clarify this.) Note that after we have the get_class() function, it’s easy
to satisfy functions that require a class as input; for example, if
get_parent_class() insists on a class name, and we want to know the parent
class of an object instance, we could just wrap it like this:</p>

<pre>
&#36;parent_class = get_parent_class(get_class(&#36;my_instance));
</pre>

<p>Bear in mind that as of PHP4.3, the constant __CLASS__ exists. It contains
the class name.</p>

<p>Going in the other direction (trying to satisfy a function that wants an
instance when all we have is a class) would be more problematic because you
don’t want to instantiate a class just to ask questions of it.</p>

<p>The difference between get_class_vars() and get_object_vars() is subtle,
but it’s more than just a question of what type of input they prefer. The
get_class_vars() function returns information about variables and default
values as they exist in the class definition itself, independent of any
instance; get_object_vars() returns information about the current state of a
particular instance. For example, consider this class definition and use:</p>

<pre>
class Example {

  var &#36;var1 = "initialized";
  var &#36;var2 = "initialized";
  var &#36;var3;
  var &#36;var4;

  function __construct() {
    &#36;this->var3 = "set";
    &#36;this->var1 = "changed";
  }

}

&#36;example = new Example();
print_r(get_class_vars("Example"));
print_r(get_object_vars(&#36;example));
</pre>

<p>For the first call (to get_class_vars()), we should expect to find var1 and
var2 both bound to "initialized" as in the class definition itself. The second
call (to get_object_ vars()) should return bindings of var1, var2, and var3 to
"changed", "initialized", and "set", respectively. In neither case will either
function retrieve var4.</p>

<p>The third group of functions (method_exists(), get_class_methods())
manipulate member function names as strings. The first allows you to ask an
instance if it contains a given function, and the second recovers all function
names from an instance or class. (Notice that we don’t need two separate
functions as we did with get_class_vars() and get_object_vars(); PHP doesn’t
offer you a way to add or delete member functions from instances on the
fly.)</p>

<p>Finally, the fourth group lets you apply method names (presumably recovered
using functions from the third group) to instances. But these are probably
best explained by example, so let’s dive in.</p>

<div class="remarkbox">
  <div class="rbtitle">note-to-self</div>
  <div class="rbcontent">
  <p>Okay! I'm going to leave this portion of the book out.</p>
  </div>
</div>

EOPAGESTR5;
echo $page_str;

site_footer();

?>