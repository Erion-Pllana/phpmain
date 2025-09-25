<?php include 'header.php'; ?>

<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <h2 class="text-center mb-4">Login</h2>

      <!-- Display login error -->
      <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger">Invalid email or password.</div>
      <?php endif; ?>

      <form action="login-process.php" method="POST">
        <div class="mb-3">
          <label for="email" class="form-label">Email address</label>
          <input type="email" class="form-control" id="email" name="email" required>
        </div>

        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input type="password" class="form-control" id="password" name="password" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">Login</button>
      </form>
    </div>
  </div>
</div>

<?php include 'footer.php'; ?>
