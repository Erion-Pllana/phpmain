<?php 
	include_once('config.php'); 

	if(empty($_SESSION['title']))
	{
		header('Location: login.php');
	}
  $sql = "SELECT * FROM products";
  $selectUsers = $conn->prepare($sql);
  $selectUsers->execute();

  $users_data = $selectUsers->fetchAll();

?>

<?php include("header.php"); ?>

<style>
		
  table
  {
    border: 1px solid black;
  }

  tr,td,th
  {
    border: 1px solid black;
    
  }
  table,tr,td
  {
    border-collapse: collapse;
  }
  td
  {
    padding: 10px;
  }

</style>

<nav class="navbar navbar-dark fixed-top bg-dark flex-md-nowrap p-0 shadow">
  <a class="navbar-brand col-sm-3 col-md-2 mr-0" href="#">Welcome, <i> <?php echo $_SESSION['title']; ?> </i></a>
  <ul class="navbar-nav px-3">
    <li class="nav-item text-nowrap">
      <a class="nav-link" href="logout.php">Sign out</a>
    </li>
  </ul>
</nav>

<div class="container-fluid">
  <div class="row">
    <nav class="col-md-2 d-none d-md-block bg-light sidebar">
      <div class="sidebar-sticky">
        <ul class="nav flex-column">
          <li class="nav-item">
            <a class="nav-link active" href="productDashboard.php">
              <span data-feather="home"></span>
              Dashboard <span class="sr-only">(current)</span>
            </a>
          </li>
          <li class="nav-item">
          <?php foreach ($products_data as $products_data) { ?>

            <a class="nav-link" href="profile.php?id=<?= $products_data['id'];?>">
            <?php  } ?>
              <span data-feather="file"></span>
              Edit Product
            </a>
          </li>
        </ul>
      </div>
    </nav>

    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
      <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">ProductDashboard</h1>
      </div>  

      <div>
        <?php 

        include_once('config.php');

        $getUsers = $conn->prepare("SELECT * FROM products");

        $getUsers->execute();

        $users = $getUsers->fetchAll();

        ?>

        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Title</th>
              <th>Description</th>
              <th>Quantity</th>
              <th>Price</th>
              <th>Update</th>
            </tr>
          </thead>
          <?php
            foreach ($products as $product ) {
          ?>
          <tbody>
            <tr> 
              <td> <?= $product['id'] ?> </td>
              <td> <?= $product['title'] ?> </td>
              <td> <?= $product['description']  ?> </td> 
              <td> <?= $product['quantity']  ?> </td> 
              <td> <?= $product['price']  ?> </td>
              <td> <?= "<a href='delete.php?id=$product[id]'> Delete</a>| <a href='profile.php?id=$product[id]'> Update </a>"?></td>
            </tr>
          
            <?php 
              }
            ?>
          </tbody>
        </table>
      </div>
    </main>
  </div>
</div>

<?php include("footer.php"); ?>