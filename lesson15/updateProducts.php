<?php 

	include_once('config.php');

	if(isset($_POST['update']))
	{
		$id=$_POST['id'];
		$title = $_POST['title'];
		$description = $_POST['description'];
		$quantity = $_POST['quantity'];
		$price = $_POST['price'];

		if(empty($title) || empty($description) || empty($quantity) || empty($price))
		{
			echo "You need to fill all the fields.";
			header( "refresh:2; url=product.php" );
		}
		else
		{
			$sql= "UPDATE products SET title=:title, description=:description, quantity=:quantity, price=:price WHERE id=:id";

			$updateSql = $conn->prepare($sql);

			$updateSql->bindParam(':od', $id);
			$updateSql->bindParam(':title', $title);
			$updateSql->bindParam(':description', $description);
			$updateSql->bindParam(':quantity', $quantiy);
			$updateSql->bindParam(':price', $price);

			$updateSql->execute();

			header('Location: productDashboard.php');
		}
	}
?>