<?php
/*
NAME: header_footer_printerFriendly
*/




function site_headerPF($title) {
/*
Generates all the HTML before the main content of the page.
*/

  $site_header = <<<EOSITEHEADER
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="pragma" CONTENT="no-cache">
<title>$title</title>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
<link href="style2.css" rel="stylesheet" type="text/css"/>
</head>

<body>



<div id="header">
<div id="sitebranding">
<h1>School Bus Transportation Company</h1>
</div>
</div> <!-- end of header div -->



<div id="bodycontent">
<!-- title goes here -->
<h1 class="pagetitle">$title</h1>
<!-- end title -->
<!-- content goes here -->
EOSITEHEADER;
  echo $site_header;
  unset($site_header);
  return;
}


function site_footerPF() {
/*
Generates all the HTML after the main content of the page.
*/

  $site_footer = <<<EOSITEFOOTER
<!-- end content -->
</div> <!-- end of bodycontent div -->


<div id="navright">
<!-- bus img goes here -->
<div>
<img src="school-bus-clipart11.gif" class="navrtimg" alt="School Bus gif" />
</div>
</div> <!-- end of navright div -->


<div id="footer">
<!-- footer goes here -->
<p class="footcopy">Copyright &copy; 2010 SAMEH R LABIB, LLC</p>
</div> <!-- end of footer div -->


</body>

</html>
EOSITEFOOTER;
  echo $site_footer;
  unset($site_footer);
  return;
}

?>