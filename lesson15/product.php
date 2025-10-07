<?php
session_start();
    include_once('config.php');

    if(empty($_SESSION['username']))
    {
        header('Location: login.php');
    }
    $id = $_GET['id'];
    $sql = "SELECT * FROM product WHERE id=:id";
    $selectProduct = $conn->prepare($sql);
    $selectProduct->bindParam(':id', $id);
    $selectProduct->execute();

    $product_data = $selectProduct->fetch();
?>

<?php include("header.php"); ?>

<nav class="navbar navbar-dark fixed-top bg-dark flex-md-nowrap p-0 shadow">
  <a class="navbar-brand col-sm-3 col-md-2 mr-0" href="#">Hello, <i> <?php echo $_SESSION['username']; ?> </i></a>


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
            <a class="nav-link " href="Dashboard.php">
              <span data-feather="home"></span>
               Dashboard <span class="sr-only">(current)</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link " href="productDashboard.php">
              <span data-feather="home"></span>
               Product Dashboard <span class="sr-only">(current)</span>
            </a>
          </li>
          <li class="nav-item">
          <a class="nav-link active" href="profile.php?id=<?= $user_data['id'];?>">
              <span data-feather="file"></span>
              Edit Profile
            </a>
          </li>
    
        </ul>


      </div>
    </nav>
    
    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">


      <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Products Dashboard</h1>
      </div>
          
      <div class="container">
        <div class="row">
          <div class="col-md-5">
            <form class="form-profile" action="updateProduct.php" method="post">
                <input type="hidden" name="id" value="<?php echo  $product_data['id'] ?>">
              <span class="text-muted" for='id'>Id</span>
              <input  type="number" class="form-control" id="floatingInput" placeholder="Id" name="id" value="<?php echo  $product_data['id'] ?>" readonly>

               <span class="text-muted" for='title'> Title </span>
              <input class="form-control" type="text" name="title" value="<?php echo $product_data['title'] ?>" required><br>


              <span class="text-muted"> Description </span>
              <input class="form-control" type="text" name="description" value="<?php echo $product_data['description'] ?>" required><br>


              <span class="text-muted"> Quantity </span>
              <input class="form-control" type="number" name="quantity" value="<?php echo $product_data['quantity'] ?>" required><br>


              <span class="text-muted">Price</span>
              <input class="form-control" type="number" name="price" value="<?php echo $product_data['price']/100 ?>" required><br><br><br>
              
              <button class="btn btn-lg btn-primary" type="submit" name="update">Update</button>
            </form>
          </div>
        </div>    
      </div>
    </main>
  </div>
</div>


<?php include("footer.php"); ?>