<?php
require_once 'config.php';

if ($conn) {
    echo "Successfully connected to the database '" . DB_NAME . "' on '" . DB_SERVER . "'.\n";
    
    // Check if tables exist
    $tables = ['products', 'users', 'orders', 'sales_data'];
    foreach ($tables as $table) {
        $result = $conn->query("SHOW TABLES LIKE '$table'");
        if ($result->num_rows > 0) {
            echo "Table '$table': Exists\n";
        } else {
            echo "Table '$table': MISSING\n";
        }
    }
}
?>
