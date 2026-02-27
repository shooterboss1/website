<?php
require_once 'config.php';

// Update category 'ladies' to 'women'
$sql = "UPDATE products SET category='women' WHERE category='ladies'";

if ($conn->query($sql) === TRUE) {
    echo "Records updated successfully: " . $conn->affected_rows . " rows changed.\n";
} else {
    echo "Error updating record: " . $conn->error . "\n";
}

$conn->close();
?>
