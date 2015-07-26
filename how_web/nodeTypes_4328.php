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


site_header("Node Types");

$page_str = <<<EOPAGESTR5

<p>Each node type corresponds to a DOM object.</p>

<table class="events" width="678">
  <caption></caption>
  <tr>
    <th>type/interface/constant</th>
    <th>corresponding class</th>
    <th>description</th>
  </tr>
  <tr>
    <td>Attr/ XML_ATTRIBUTE_NODE</td>
    <td>DOMAttr</td>
    <td>This class represents an attribute node. The DOM extension does not
    consider attributes to be part of the tree because they are not child nodes.
    They are treated as properties of elements.</td>
  </tr>
  <tr>
    <td>CDATASection/ XML_CDATA_SECTION_NODE</td>
    <td>DOMCDATASection</td>
    <td>This class represents a CDATA node.</td>
  </tr>
  <tr>
    <td>Comment/ XML_COMMENT_NODE</td>
    <td>DOMComment</td>
    <td>This class represents comments within a document.</td>
  </tr>
  <tr>
    <td>DocumentFragment/ DOMFragment/ XML_DOCUMENT_FRAG_NODE</td>
    <td>DOMDocumentFragment</td>
    <td>This is used to extract a portion of the tree or create lightweight
    documents. It can consist of nodes that by themselves would not be
    well-formed XML. A document fragment is useful when wanting to move
    portions of the tree around or even append some new XML into a tree.</td>
  </tr>
  <tr>
    <td>Document/ XML_DOCUMENT_NODE</td>
    <td>DOMDocument</td>
    <td>This class represents the entire XML or HTML document. It serves as the
    root node for the tree, which means the tree begins with this and only this
    node. Everything within the document is contained within this node.</td>
  </tr>
  <tr>
    <td>DocumentType/ XML_DOCUMENT_TYPE_NODE</td>
    <td>DOMDocumentType</td>
    <td>This class represents the DocumentType for the document. Objects of this
    type are read-only.</td>
  </tr>
  <tr>
    <td>Element/ XML_ELEMENT_NODE</td>
    <td>DOMElement</td>
    <td>This class represents an element node.</td>
  </tr>
  <tr>
    <td>Entity/ XML_ENTITY_NODE</td>
    <td>DOMEntity</td>
    <td>This class represents an entity in the document. Objects of this type
    are read-only.</td>
  </tr>
  <tr>
    <td>EntityReference/ XML_ENTITY_REF_NODE</td>
    <td>DOMEntityReference</td>
    <td>This class represents entity references within the document. Objects of
    this type are read-only.</td>
  </tr>
  <tr>
    <td>Notation/ XML_NOTATION_NODE</td>
    <td>DOMNotation</td>
    <td>This class represents a notation declared in the DTD. Objects of this
    type are read-only.</td>
  </tr>
  <tr>
    <td>ProcessingInstruction/ XML_PI_NODE</td>
    <td>DOMProcessingInstruction</td>
    <td>This class represents a PI within the document.</td>
  </tr>
  <tr>
    <td>Text/ XML_TEXT_NODE</td>
    <td>DOMText</td>
    <td>This class represents a text node.</td>
  </tr>
</table>

EOPAGESTR5;
echo $page_str;

site_footer();

?>