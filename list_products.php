<?php
require_once 'config.php';

$result = $conn->query("SELECT id, name, image FROM products");
if ($result) {
    echo "Found " . $result->num_rows . " products:\n";
    while ($row = $result->fetch_assoc()) {
        echo "ID: " . $row['id'] . " | Name: " . $row['name'] . " | Image: " . $row['image'] . "\n";
    }
} else {
    echo "Error querying products: " . $conn->error;
}
?>
