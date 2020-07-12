<?php
include("../../mainfile.php");
header("HTTP/1.1 301 Moved Permanently");
header("Location: {$_GET['url']}");
exit();
?>