<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require_once('../includes/login_funcs.php');
require_once('../includes/db_vars.php');
require_once('../includes/header_footer.php');

// User must be logged in and be someone special.
if (!user_isloggedin() || !user_type_cookieIsSet()) {
  $host = $_SERVER['HTTP_HOST'];
  header("Location: http://{$host}/web/login.php");
  exit;
}
if ($_COOKIE['user_type'] != 1) {
  die('Script aborted #3098. -Programmer.');
}


site_header('Mathematical Operators');

$page_str = <<<EOPAGESTR5

<p>Most of the mathematical action in PHP is in the form of built-in
functions rather than in the form of operators. In addition to the
comparison operators, PHP offers five operators for simple arithmetic,
as well as some shorthand operators that make incrementing and
assigning statements more concise.</p>

<h2>Arithmatical operators</h2>

<p>+</p>

<p>Sum of its two arguments.</p>

<pre>
4 + 9.5 evaluates to 13.5
</pre>

<p>-</p>

<p>If there are two arguments, the right-hand argument is subtracted
from the left-hand argument. If there is just a right-hand argument,
then the negative of that argument is returned.</p>

<pre>
50 - 75 evaluates to -25
- 3.9 evaluates to -3.9
</pre>

<p>*</p>

<p>Product of its two arguments.</p>

<pre>
3.14 * 2 evaluates to 6.28
</pre>

<p>/</p>

<p>Floating-point division of the left-hand
argument by the right-hand argument.</p>

<pre>
5 / 2 evaluates to 2.5
</pre>

<p>%</p>

<p>Integer remainder from division of left-hand argument by the absolute
value of the right-hand argument.</p>

<pre>
101 % 50 evaluates to 1
999 % 3 evaluates to 0
43 % 94 evaluates to 43
-12 % 10 evaluates to –2
-12 % -10 evaluates to -2
</pre>

<h2>tip</h2>

<hr />

<p>If you want integer division rather than floating-point division,
simply coerce or convert the division result to an integer. For example,
intval(5 / 2) evaluates to the integer 2.</p>

<hr />

<h2>Incrementing operators</h2>

<p>PHP inherits a lot of its syntax from C, and C programmers are
famously proud of their own conciseness. The incrementing/decrementing
operators taken from C make it possible to more concisely represent
statementslike &#36;count = &#36;count + 1,which tend to be typed frequently.</p>

<p>The increment operator (++) adds one to the variable it is attached
to, and the decrement operator (--) subtracts one from the variable.
Each one comes in two flavors, postincrement (which is placed immediately
after the affected variable), and preincrement (which comes immediately
before). Both flavors have the same side effect of changing the variable's
value, but they have different values as expressions. The postincrement
operator acts as if it changes the variable's value after the expression's
value is returned, whereas the preincre- ment operator acts as though it
makes the change first and then returns the variable's new value. You
can see the difference by using the operators in assignment statements,
like this:</p>

<pre>
&#36;count = 0;
&#36;result = &#36;count++;
print("Post ++: count is &#36;count, result is &#36;result<BR>");
&#36;count = 0;
&#36;result = ++&#36;count;
print("Pre ++: count is &#36;count, result is &#36;result<BR>");
&#36;count = 0;
&#36;result = &#36;count--;
print("Post --: count is &#36;count, result is &#36;result<BR>");
&#36;count = 0;
&#36;result = --&#36;count;
print("Pre --: count is &#36;count, result is &#36;result<BR>");
</pre>

<p>which gives the browser output:</p>

<pre>
Post ++: count is 1, result is 0
Pre ++: count is 1, result is 1
Post --: count is -1, result is 0
Pre --: count is -1, result is –1
</pre>

<p>In this example, the statement &#36;result = &#36;count++; is exactly
equivalent to</p>

<pre>
&#36;result = &#36;count;
&#36;count = &#36;count + 1;
</pre>

<p>while &#36;result = ++&#36;count; is equivalent to</p>

<pre>
&#36;count = &#36;count + 1;
&#36;result = &#36;count;
</pre>

<h2>Assignment operators</h2>

<p>Incrementing operators like ++ save keystrokes when adding one to a
variable, but they don't help when adding another number or performing
another kind of arithmetic. Luckily, all five arithmetic operators have
corresponding assignment operators (+=, -=, *=, /=, and %=) that assign
to a variable the result of an arithmetic operation on that variable in
one fell swoop. The statement:</p>

<pre>
&#36;count = &#36;count * 3;
</pre>

<p>can be shortened to:</p>

<pre>
&#36;count *= 3;
</pre>

<p>and the statement:</p>

<pre>
&#36;count = &#36;count + 17;
</pre>

<p>becomes:</p>

<pre>
&#36;count += 17;
</pre>

EOPAGESTR5;
echo $page_str;

site_footer();
?>