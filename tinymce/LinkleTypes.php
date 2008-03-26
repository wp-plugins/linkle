<?php
require_once('../../../../wp-blog-header.php');
require_once('../LinkleOptions.php');
$data = LinkleOptions::build_from_options();
header('Content-type: text/xml');
echo "<linkletypes>";
foreach($data->type_to_info_map as $key => $value) {
	echo "<type>";
	echo "<name>$key</name>";
	echo "<description>$value->description</description>";
	echo "</type>";
}
echo "</linkletypes>";
?>
