<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

$docRoot = $_SERVER["DOCUMENT_ROOT"];

require_once("$docRoot/web/includes/login_funcs.php");
require_once("$docRoot/web/includes/db_vars.php");
require_once("$docRoot/web/includes/header_footer.php");

// User must be logged in and be someone special.
if (!user_isloggedin() || !user_type_cookieIsSet()) {
  $host = $_SERVER['HTTP_HOST'];
  header("Location: http://{$host}/web/login.php");
  exit;
}
if ($_COOKIE['user_type'] != 1) {
  die('Script aborted #3098. -Programmer.');
}


site_header('Array Functions');

$page_str = <<<EOPAGESTR

<h2>each()</h2>

<p>Return the current key and value pair from an array and advance the array cursor.</p>

<p>After each() has executed, the array cursor will be left on the next element of the
array, or past the last element if it hits the end of the array. You have to use reset()
if you want to traverse the array again using each.</p>

<p>For an example of see "Iterate Array Using While".</p>

<h2>end()</h2>

<p>end() advances array's internal pointer to the last element, and returns its value. </p>

<pre>
&#36;lastSP = end(&#36;SP_arr);
</pre>

<h2>prev()</h2>

<p>prev() behaves just like next(), except it rewinds the internal array pointer one
place instead of advancing it. Note: You won't be able to distinguish the beginning
of an array from a boolean FALSE element. To properly traverse an array which may 
contain FALSE elements, see the each() function.</p>

<pre>
// example of backward iteration

&#36;ar = array ( 'a', 'b', 'c', 'd', 'e', 'f') ;

print_r(&#36;ar);

end(&#36;ar);
while(&#36;val = current(&#36;ar)) {
  echo &#36;val.' ';
  prev(&#36;ar);
}
</pre>

<h2>reset()</h2>

<p>reset() rewinds array's internal pointer to the first element and returns the value
of the first array element.</p>

<h2>next()</h2>

<p>next() behaves like current(), with one difference. It advances the internal array
pointer one place forward before returning the element value. That means it returns
the next array value and advances the internal array pointer by one.</p>

<pre>
&#36;mode = next(&#36;transport);
</pre>

<h2>current()</h2>

<p>The current() function simply returns the value of the array element that's currently
being pointed to by the internal pointer. It does not move the pointer in any way. If
the internal pointer points beyond the end of the elements list or the array is empty,
current() returns FALSE. </p>

<h2>key()</h2>

<p>key() returns the index element of the current array position.</p>

<pre>
&#36;array = array(
    'fruit1' => 'apple',
    'fruit2' => 'orange',
    'fruit3' => 'grape',
    'fruit4' => 'apple',
    'fruit5' => 'apple');

// this cycle echoes all associative array
// key where value equals "apple"
while (&#36;fruit_name = current(&#36;array)) {
    if (&#36;fruit_name == 'apple') {
        echo key(&#36;array).'&lt;br /&gt;';
    }
    next(&#36;array);
}
</pre>

<h2>is_array()</h2>

<p>Takes a single argument of any type and returns a true value if the argument is an
array, and false otherwise. See example:</p>

<pre>
if (!isset(&#36;vehicle_in) OR !is_array(&#36;vehicle_in) OR sizeof(&#36;vehicle_in) < 1) {
  die("Function failed to create select box because no array was passed.");
}
</pre>

<h2>count()</h2>

<p>Takes an array as argument and returns the number of nonempty elements in the array.
(This will be 1 for strings and numbers.)</p>

<h2>sizeof()</h2>

<p>Identical to count().</p>

<h2>in_array()</h2>

<p>Takes two arguments: an element (that might be a value in an array), and an array
(that might contain the element). Returns true if the element is contained as a value
in the array, false otherwise. (Note that this does not test for the presence of keys
in the array.)</p>

<h2>IsSet(&#36;array[&#36;key])</h2>

<p>Takes an array[key] form and returns true if the key portion is a valid key for
the array. (This is a specific use of the more general function IsSet(), which tests
whether a variable is bound.)</p>

EOPAGESTR;
echo $page_str;

site_footer();
?>