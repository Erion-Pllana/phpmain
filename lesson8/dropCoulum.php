<?php
try {
  // Connect to the database
  $pdo = new PDO("mysql:host=localhost;dbname=testdb", "root", "");


  //  Table alteration SQL
  $sql = "ALTER TABLE products DROP COLUMN name";


  // Execute the statement using the exec() method of the PDO object
  $pdo->exec($sql);
  
  echo "Column dropped succefully";


} catch(PDOException $e) {
  echo $e->getMessage();
}
?>