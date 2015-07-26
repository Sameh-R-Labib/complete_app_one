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


site_header("A Sample XML Application");

$page_str = <<<EOPAGESTR5

<div class="remarkbox">
  <div class="rbtitle">note-to-self</div>
  <div class="rbcontent">
  <p>After I started copying this example I stopped because I realized
  I was not going to be use any XML API that is part of PHP. I
  will write my own API to parse HTML for my KDS. My API will be made from my own
  custom classes and string proccessing functions.</p>
  </div>
</div>

<p>This series of scripts will write out XML to a file by using data from an
HTML form, and then will allow you to edit the values in that file.</p>

<p>Listing 40-9 is an HTML form that can be used by nontechnical users to
define forms. (They don’t care that this data will be formatted and stored in
XML.) Listing 40-10 is a script to write out the XML file.</p>

<p>Listing 40-9: A form to collect values for an XML file (pollform.php)</p>

<pre>
&lt;HTML&gt;
&lt;HEAD&gt;
&lt;TITLE&gt;Make-a-poll&lt;/TITLE&gt;
&lt;/HEAD&gt;

&lt;BODY&gt;
&lt;CENTER&gt;&lt;H3&gt;Make-a-poll&lt;/H3&gt;&lt;/CENTER&gt;

&lt;P&gt;Use this form to define a poll:&lt;/P&gt;
&lt;FORM METHOD="post" ACTION="writepoll.php"&gt;

&lt;P&gt;Give this poll a &lt;B&gt;short&lt;/B&gt; name, like &lt;FONT COLOR="red"&gt;Color
Poll&lt;/FONT&gt;.&lt;BR&gt;
&lt;INPUT TYPE=TEXT NAME="PollName" SIZE=30&gt;
&lt;/P&gt;

&lt;P&gt;This poll should &lt;B&gt;begin&lt;/B&gt; on this date (MM/DD/YYYY):
&lt;INPUT TYPE=TEXT Name="Poll_Startdate" SIZE=10&gt;
&lt;/P&gt;

&lt;P&gt;This poll should &lt;B&gt;end&lt;/B&gt; on this date (MM/DD/YYYY):
&lt;INPUT TYPE=TEXT NAME="Poll_Enddate" SIZE=10&gt;
&lt;/P&gt;

&lt;P&gt;This is the poll question (&lt;FONT COLOR="blue"&gt;e.g. Why did the
chicken cross the road?&lt;/FONT&gt;):
&lt;INPUT TYPE=TEXT NAME="Poll_Question", size=100&gt;
&lt;/P&gt;

&lt;P&gt;These are the potential answer choices you want to offer (&lt;FONT
COLOR="darkgreen"&gt;e.g. Yes, No, Say what?&lt;/FONT&gt;). Fill in only as many as
you need. Keep in mind that brevity is the soul of good poll-making.&lt;BR&gt;
&lt;INPUT TYPE=TEXT NAME="Raw_Poll_Option[]" SIZE=25&gt;&lt;BR&gt;
&lt;INPUT TYPE=TEXT NAME="Raw_Poll_Option[]" SIZE=25&gt;&lt;BR&gt;
&lt;INPUT TYPE=TEXT NAME="Raw_Poll_Option[]" SIZE=25&gt;&lt;BR&gt;
&lt;INPUT TYPE=TEXT NAME="Raw_Poll_Option[]" SIZE=25&gt;&lt;BR&gt;
&lt;INPUT TYPE=TEXT NAME="Raw_Poll_Option[]" SIZE=25&gt;&lt;BR&gt;
&lt;INPUT TYPE=TEXT NAME="Raw_Poll_Option[]" SIZE=25&gt;&lt;BR&gt;
&lt;/P&gt;

&lt;INPUT TYPE="submit" NAME="Submit" VALUE="Add a poll"&gt;
&lt;/FORM&gt;

&lt;/BODY&gt;
&lt;/HTML&gt;
</pre>

<p>Listing 40-10: A script to write out an XML file (writepoll.php)</p>

EOPAGESTR5;
echo $page_str;

site_footer();

?>