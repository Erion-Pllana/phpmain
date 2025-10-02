<?php include("header.php"); ?>


<div class="signup">
        
    <form class="form-signin" action="products.php" method="post">
        
        <h1 class="h3 mb-3 font-weight-normal">Add a Product</h1>
        <label for="inputTitle" class="sr-only">Title</label>
        <input type="text" id="inputTitle" class="form-control" placeholder="Title" name="title" required autofocus>


        <label for="inputDescription" class="sr-only">Description</label>
        <input type="text" id="inputDescription" class="form-control" placeholder="Description" name="description" required autofocus>


        <label for="inputQuantity" class="sr-only">Quantity</label>
        <input type="number" id="inputQuantity" class="form-control" placeholder="Quantity" name="quantity" required autofocus>

        
        <label for="inputPrice" class="sr-only">Price</label>
        <input type="number" id="inputPrice" class="form-control" placeholder="Price" name="price" required autofocus>



        <button class="btn btn-lg btn-primary btn-block" type="submit" name="submit">Add Product</button>


        <p class="mt-5 mb-3 text-muted">Digital School &copy; 2023</p>
    </form>
</div>


<?php include("footer.php"); ?>