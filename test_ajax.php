<?php
$_GET['offset'] = 0;
$_GET['limit'] = 5;
ob_start();
include 'load_more.php';
$output = ob_get_clean();
echo $output;
?>
