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


site_header('Break Down a String');

$page_str = <<<EOPAGESTR

<p>Definition of Explode from <a href="http://php.net/manual/en/function.explode.php">http://php.net/manual/en/function.explode.php</a>:</p>

<pre>
array explode ( string &#36;delimiter , string &#36;string [, int &#36;limit ] )
</pre>

<p>explode() returns an array of strings, each of which is a substring of
<em>string</em> formed by splitting it on boundaries formed by the string <em>delimiter</em>.</p>

<p>Example:</p>

<pre>
/*
Break down the composite to get the phrases.
NOTE: explode also gives the empty string on the left of :::
We don't want that.
*/
&#36;phrases = explode(":::", &#36;composite_NO_SLASHES);

// get rid of first element
unset(&#36;phrases[0]);
</pre>

<h2>Tokenizing and Parsing Functions</h2>

<p>Sometimes you need to take strings apart at the seams, and you have your own notions
of what should count as a seam. The process of breaking up a long string into words is
called tokenizing, and among other things it is part of the internals of interpreting or
compiling any computer program, including PHP. PHP offers a special function for this
purpose, called strtok().</p>

<p>The strtok() function takes two arguments: the string to be broken up into tokens and
a string containing all the delimiters (characters that count as boundaries between
tokens). On the first call, both arguments are used, and the string value returned is the
first token. To retrieve subsequent tokens, make the same call, but omit the source
string argument. It will be remembered as the current string, and the function will
remember where it left off. For example</p>

<pre>
&#36;token = strtok(
     "open-source HTML-embedded server-side Web scripting",
     " ");
while(&#36;token){
  print(&#36;token . "&lt;BR>");
  &#36;token = strtok(" ");
}
</pre>

<p>gives the browser output:</p>

<pre>
open-source
HTML-embedded
server-side
Web
scripting
</pre>

<p>The original string would be broken at each space. At our discretion, we could change
the delimiter set, like so:</p>

<pre>
&#36;token = strtok(
     "open-source HTML-embedded server-side Web scripting",
     "-");
while(&#36;token){
  print(&#36;token . "&lt;BR>");
  &#36;token = strtok("-");
}
</pre>

<p>This gives us (less sensibly):</p>

<pre>
Open
source HTML
embedded server
side Web scripting
</pre>

<p>Finally, we can break the string at all these places at once by giving it a delimiter
string like " -", containing both a space and a dash. The code:</p>

<pre>
&#36;token = strtok(
     "open-source HTML-embedded server-side Web scripting",
     " -");
while(&#36;token){
  print(&#36;token . "&lt;BR>");
  &#36;token = strtok(" -");
}
</pre>

<p>prints this output:</p>

<pre>
open
source
HTML
embedded
server
side
Web
scripting
</pre>

<p>Notice that in every case the delimiter characters do not show up anywhere in the
retrieved tokens.</p>

<p>The strtok() function doles out its tokens one by one. You can also use the explode()
function to do something similar, except it stores the tokens all at once into an array.
After the tokens are in the array, you can do anything you like with them, including sort
them.</p>

<p>The explode() function takes two arguments: a separator string and the string to be
separated. It returns an array where each element is a substring between instances of the
separator in the string to be separated. For example:</p>

<pre>
&#36;explode_result = explode("AND", "one AND a two AND a three");
</pre>

<p>results in the array &#36;explode_result having three elements, each of which is a string:
"one ", " a two ", and " a three". In this particular example, there would be no capital
letters anywhere in the strings contained in the array, because the AND separator does
not show up in the result.</p>

<p>The separator string in explode() is significantly different from the delimiter string
used in strtok(). The separator is a full-fledged string, and all its characters must be
found in the right order for an instance of the separator to be detected. The delimiter
string of strtok() specifies a set of single characters, any one of which will count as a
delimiter. This makes explode() both more precise and more brittle — if you leave out a
space or a newline character from a long string, the entire function will be broken.</p>

<p>Because the entire separator string disappears into the ether when explode() is used,
this function can be the basis for many useful effects. The examples given in most PHP
documentation use short strings for convenience, but remember that a string can be
almost any length — and explode() is especially useful with longer strings that might be
tedious to parse some other way. For instance, you can use it to count how many times a
particular string appears within a text file by turning the file into a string and using
explode() on it, as in this example (which uses some functions we haven’t explained yet,
but we hope make sense in context).</p>

<pre>
&lt;&#63;php
//First, turn a text file into a string called &#36;filestring.
&#36;filename = "complex_layout.html";
&#36;fd = fopen(&#36;filename, "r");
&#36;filestring = fread(&#36;fd, filesize(&#36;filename));
fclose (&#36;fd);

//Explode on the beginning of the &lt;TABLE> HTML tag
&#36;tables = explode("&lt;TABLE", &#36;filestring); // assumes uppercase
//Count the number of pieces
&#36;num_tables = count(&#36;tables);

//Subtract one to get the number of &lt;TABLE> tags, and echo
echo (&#36;num_tables - 1);
&#63;>
</pre>

<p>The explode() function has an inverse function, implode(), which takes two arguments:
a "glue" string (analogous to the separator string in explode()) and an array of strings
like that returned by explode(). It returns a string created by inserting the glue string
between each string element in the array.</p>

<p>You can use the two functions together to replace every instance of a particular
string within a text file. Remember that the separator string will vanish into the ether
when you perform an explode() — if you want it to appear in the final file, you have to
replace it by hand. In this example, we’re changing the font tags on a Web page.</p>

<pre>
&lt;&#63;php
//Turn text file into string
&#36;filename = "someoldpage.html";
&#36;fd = fopen(&#36;filename, "r");
&#36;filestring = fread(&#36;fd, filesize(&#36;filename));
fclose (&#36;fd);
&#36;parts = explode("arial, sans-serif", &#36;filestring);
&#36;whole = implode("arial, verdana, sans-serif", &#36;parts);

//Overwrite the original file
&#36;fd = fopen(&#36;filename, "w");
fwrite(&#36;fd, &#36;whole);
fclose (&#36;fd);
&#63;>
</pre>

EOPAGESTR;
echo $page_str;

site_footer();
?>