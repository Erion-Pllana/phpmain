<?php include 'header.php'; ?>

<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <h2 class="text-center mb-4">Sign Up</h2>
      
      <!-- Show success message -->
      <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">Signup successful!</div>
      <?php endif; ?>

      <form action="signup-process.php" method="POST">
        <div class="mb-3">
          <label for="name" class="form-label">Full Name</label>
          <input type="text" class="form-control" id="name" name="name" required>
        </div>
        
        <div class="mb-3">
          <label for="email" class="form-label">Email address</label>
          <input type="email" class="form-control" id="email" name="email" required>
        </div>
        
        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input type="password" class="form-control" id="password" name="password" required>
        </div>
        
        <div class="mb-3">
          <label for="confirm_password" class="form-label">Confirm Password</label>
          <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
        </div>
        
        <button type="submit" class="btn btn-primary w-100">Sign Up</button>
      </form>
    </div>
  </div>
</div>

<?php include 'footer.php'; ?>
