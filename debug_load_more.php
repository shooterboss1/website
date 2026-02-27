<?php
/**
 * Debugging script for load_more.php
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

$_GET['offset'] = 0;
$_GET['limit'] = 12;
$_GET['category'] = '';
$_GET['search'] = '';
$_GET['condition'] = '';
$_GET['sort'] = 'newest';

echo "--- STARTING LOAD_MORE.PHP DEBUG ---\n";

try {
    include 'load_more.php';
} catch (Exception $e) {
    echo "\nFATAL ERROR: " . $e->getMessage() . "\n";
}

echo "\n--- DEBUG END ---\n";
?>
