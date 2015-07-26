<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require_once('../includes/login_funcs.php');
require_once('../includes/db_vars.php');
require_once('../includes/header_footer.php');

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


site_header('Network Functions Overview');

$page_str = <<<EOPAGESTR5

<p>The network functions are a bunch of relatively little-used functions that provide network
information or connections. Many of these may be more useful from the command line than the
Web page, unless you're writing some kind of monitoring tool.</p>

<h2>Syslog functions</h2>

<p>The syslog functions allow you to open the system log for a program, generate a message,
and close it again.</p>

<table class="events" width="678">
  <caption></caption>
  <tr>
    <th>Function</th>
    <th>Description</th>
  </tr>
  <tr>
    <td>openlog([ident], option, facility)</td>
    <td>is entirely optional when used with syslog(). The ident value is generated
    automatically.</td>
  </tr>
  <tr>
    <td>syslog(priority, message)</td>
    <td>generates a system log entry.</td>
  </tr>
  <tr>
    <td>closelog()</td>
    <td>is entirely optional when used with syslog(). It takes no arguments.</td>
  </tr>
</table>

<h2>DNS functions</h2>

<p>PHP offers some very slick DNS-querying functions, outlined in the Table 23-2. These
functions allow PHP scripts to do some jiggering between IP address (which is available via
the Apache REMOTE_ADDR variable, for instance) and hostname, or vice versa.</p>

<table class="events" width="678">
  <caption>Table 23-2</caption>
  <tr>
    <th>Function</th>
    <th>Description</th>
  </tr>
  <tr>
    <td>checkdnsrr(&#36;host, [&#36;type])</td>
    <td>Checks for existence of DNS records. Default is MX; other types are A, ANY, CNAME, NS,
    SOA, PTR and AAAA.</td>
  </tr>
  <tr>
    <td>gethostbyaddr(&#36;Ipaddress)</td>
    <td>Gets hostname corresponding to address.</td>
  </tr>
  <tr>
    <td>gethostbyname(&#36;hostname)</td>
    <td>Gets address corresponding to hostname.</td>
  </tr>
  <tr>
    <td>gethostbynamel(&#36;hostname)</td>
    <td>Gets list of addresses corresponding to hostname.</td>
  </tr>
  <tr>
    <td>getmxrr(&#36;hostname, [mxhosts array], [weight])</td>
    <td>Checks for existence of MX records corresponding to hostname, places in mxhosts
    array, fills in weight info.</td>
  </tr>
</table>

<h2>Socket functions</h2>

<p>A socket is a kind of dedicated connection that allows different programs (which may be
on different machines) to communicate by sending text back and forth. PHP socket functions
allow scripts to establish such connections to socket-based servers. For instance, Web and
database servers communicate via fsockopen() — so you could theoretically write your own Web
server in PHP using this function, if you had lost all contact with reality. The connection
can then be read from or written to with the standard file-writing functions fputs(), fgets(),
and so on.</p>

<p>The standard socket-opening function is fsockopen(). The pfsockopen() function is
identical except that sockets are not destroyed when your script exits; instead, the
connection is pooled for later use. The blocking behavior of socket connections can be
toggled with set_socket_blocking(). When blocking is enabled, functions that read from
sockets will hang until there is some input to return; when it is disabled, such functions
will return immediately if there is no input. These functions are summarized in Table 23-3.</p>

<table class="events" width="678">
  <caption>Table 23-3</caption>
  <tr>
    <th>Function</th>
    <th>Description</th>
  </tr>
  <tr>
    <td>fsockopen(&#36;hostname, &#36;port, [error number], [error string], [timeout in
    seconds])</td>
    <td>Opens the socket connection to specified port on the host, and returns a file pointer
    suitable for use by functions like fgets().</td>
  </tr>
  <tr>
    <td>getservbyname(&#36;service, &#36;protocol)</td>
    <td>Returns the port number of the specified service.</td>
  </tr>
  <tr>
    <td>getservbyport(&#36;port, &#36;protocol)</td>
    <td>Returns service name on port.</td>
  </tr>
  <tr>
    <td>pfsockopen(&#36;hostname, &#36;port, [error number], [error string], [timeout in
    seconds])</td>
    <td>Opens the specified persistent socket connection.</td>
  </tr>
  <tr>
    <td>stream_set_blocking (&#36;socket descriptor, &#36;mode)</td>
    <td>TRUE for blocking mode, FALSE for nonblocking. Default is nonblocking.</td>
  </tr>
</table>

EOPAGESTR5;
echo $page_str;

site_footer();
?>