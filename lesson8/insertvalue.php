<?php
try {
  // Connect to the database
  $pdo = new PDO("mysql:host=localhost;dbname=db", "root", "");


  // Set the values to be inserted
  $username = "Jack";
  // Create a password hash from a plaintext password
  // The first argument is the plaintext password that you want to hash
  // The second argument specifies the hashing algorithm to be used
  // PASSWORD_DEFAULT specifies that the default algorithm should be used
  $password = password_hash("mypassword", PASSWORD_DEFAULT);


  // Insert statment for SQL 
  $sql = "INSERT INTO users (username, password) VALUES ('$username', '$password')";


  // Execute the statement using the exec() method of the PDO object
  $pdo->exec($sql);



  echo "New record created successfully.";


} catch(PDOException $e) {
  echo $e->getMessage();
}
?>