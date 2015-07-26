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


site_header("Non-Node Objects");

$page_str = <<<EOPAGESTR5

<p>In addition to the DOM objects which correspond to each of the node types
there are DOM objects that do not inherit from the node object:</p>

<table class="events" width="678">
  <caption></caption>
  <tr>
    <th>class/interface</th>
    <th>description</th>
  </tr>
  <tr>
    <td>NodeList</td>
    <td>A NodeList is a collection of ordered nodes accessed by index starting
    at position 0. An object of this type is often returned from DOM methods
    that can return more than a single node. It is important to know that these
    objects are live. In simple terms, modifications within the document tree
    are reflected in these objects. For example, if you had an instance of a
    NodeList object containing the children of a certain element, all changes to
    the children would be reflected in the instantiated NodeList object. If a
    child were removed, then it would no longer be contained within the
    NodeList, and this would also affect the indexing of the NodeList.</td>
  </tr>
  <tr>
    <td>DOMNamedNodeMap/ NameNodeMap</td>
    <td>A NameNodeMap is similar to a NodeList, except in that the collection
    can be accessed via item name as well as via index. The difference in the
    indexing is that these objects have no specific ordering for the objects
    they contain because the most important aspects of the contained objects are
    the names. These collections are also live, so the same issues surrounding a
    NodeList are applicable to a NameNodeMap.</td>
  </tr>
  <tr>
    <td>DOMImplementation</td>
    <td>A DOMImplementation object is used to perform functionality
    independently of a document. Within PHP 5, its primary use is to create a
    DOMDocumentType node or a new document containing a DOMDocumentType
    node.</td>
  </tr>
  <tr>
    <td>DOMException</td>
    <td>Certain cases and methods within the DOM extension throw a DOMException
    when an error is encountered.</td>
  </tr>
  <tr>
    <td>DOMCharacterData/ CharacterData</td>
    <td>The CharacterData interface extends from the Node interface but does not
    correspond directly to any specific node type within the document. This
    interface actually is used as the base type for text and comment nodes in
    order to provide some additional functionality for dealing with textual
    content.</td>
  </tr>
  <tr>
    <td>DOMXPath</td>
    <td>This class is an add-on to the DOM extension. It is used to provide
    XPath functionality within the DOM extension.</td>
  </tr>
</table>

<p>CharacterData is a special type of object in this list. It actually inherits
from a node object but is not a direct DOM object. It provides some additional
functionality from which a text node inherits.</p>

EOPAGESTR5;
echo $page_str;

site_footer();

?>