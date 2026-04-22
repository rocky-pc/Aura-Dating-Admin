<?php
try {
    $mysqli = new mysqli("localhost", "u273464849_aura_dating", "Wellsee@321", "u273464849_aura_dating");
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }
    echo "Database connection is WORKING perfectly!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>