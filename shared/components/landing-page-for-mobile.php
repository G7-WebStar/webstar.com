<!-- Mobile Offcanvas Sidebar -->
<div class="offcanvas offcanvas-start d-md-none" tabindex="-1" id="sidebarOffcanvas"
  aria-labelledby="sidebarOffcanvasLabel"
  style="background-color: var(--dirtyWhite); width: 250px;">
  
 <div class="offcanvas-header justify-content-end">
  <button type="button" class="mt-2 btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
</div>


  <div class="offcanvas-body d-flex flex-column align-items-center p-0">
    <!-- Logo -->
    <a class="d-flex align-items-center text-decoration-none mb-3">
      <img src="shared/assets/img/webstar-logo-black.png" class="img-fluid w-100 py-3 px-5" alt="WebStar Logo">
    </a>

    <hr class="w-100">

    <!-- Nav Links -->
    <ul class="navbar-nav text-center w-100">
      <li class="nav-item"><a href="#about" class="nav-link text-dark fw-medium">About Us</a></li>
      <li class="nav-item"><a href="#fearures" class="nav-link text-dark fw-medium">Features</a></li>
      <li class="nav-item"><a href="#team" class="nav-link text-dark fw-medium">Our Team</a></li>
      <li class="nav-item"><a href="#contact" class="nav-link text-dark fw-medium">Contact</a></li>
    </ul>

    <!-- Buttons -->
    <div class="d-flex flex-column gap-2 w-75 mt-4">
      <form action="login.php" method="get">
        <button type="submit" class="btn button w-100 px-3 py-1 rounded-pill text-reg text-md-14">
          Sign in
        </button>
      </form>
      <form action="registration.php" method="get">
        <button type="submit" class="btn button w-100 px-3 py-1 rounded-pill text-reg text-md-14"
          style="background-color: var(--primaryColor);">
          Register
        </button>
      </form>
    </div>
  </div>
</div>
