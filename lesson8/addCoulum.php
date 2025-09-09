<?php


try {
    // Connect to the database
    $pdo = new PDO("mysql:host=localhost;dbname=testdb", "root", "");


    // Table alteration SQL
    $sql = "ALTER TABLE products ADD email VARCHAR(255)";


    // Execute the statement using the exec() method of PDO object
    $pdo->exec($sql);


    echo "Column created successfully!";
} catch (PDOException $e) {
    echo "Error creating column: " . $e->getMessage();
}


?>