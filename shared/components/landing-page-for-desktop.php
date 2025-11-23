<!-- Desktop Nav (Hidden on Mobile) -->
<div class="d-none d-lg-flex w-100 justify-content-between align-items-center">
  <!-- Left: Logo -->
  <div class="d-flex justify-content-center align-items-center">
    <img src="shared/assets/img/webstar-logo-black.png"
      class="img-fluid pt-4 pb-5 px-3 me-4 justify-content-center align-items-center"
      width="180px">
  </div>

  <!-- Center: Nav Links -->
  <div class="collapse navbar-collapse justify-content-center">
    <ul class="navbar-nav mb-2 mb-lg-0">
      <li class="nav-item text-sbold"><a class="nav-link" href="#about">About Us</a></li>
      <li class="nav-item text-sbold"><a class="nav-link" href="#features">Features</a></li>
      <li class="nav-item text-sbold"><a class="nav-link" href="#team">Our Team</a></li>
      <li class="nav-item text-sbold"><a class="nav-link" href="#contact">Contact</a></li>
    </ul>
  </div>

  <!-- Right: Buttons -->
  <div class="d-flex gap-2">
    <form action="login.php" method="get">
      <button type="submit" class="btn button px-3 py-1 flex-fill rounded-pill text-reg text-md-14">
        Sign in
      </button>
    </form>
    <form action="registration.php" method="get">
      <button type="submit"
        class="btn button px-3 py-1 flex-fill rounded-pill text-reg text-md-14"
        style="background-color: var(--primaryColor);">
        Register
      </button>
    </form>
  </div>
</div>

