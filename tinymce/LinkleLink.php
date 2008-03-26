<?php
require_once('../../../../wp-blog-header.php');
require_once('../LinkleOptions.php');
header('Content-type: text/xml');
echo '<linklelink>'.htmlentities(linkle($_REQUEST['q'])).'</linklelink>';
?>
