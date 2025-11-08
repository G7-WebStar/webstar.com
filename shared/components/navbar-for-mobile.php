<?php
$webstarsQuery = "
SELECT
    p.webstars
FROM users u
JOIN userinfo ui ON u.userID = ui.userID
JOIN profile p ON u.userID = p.userID
WHERE u.userID = $userID
";

$webstarsResult = executeQuery($webstarsQuery);
$webstars = mysqli_fetch_assoc($webstarsResult);
?>

<!-- Styles -->
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,1,0"
  rel="stylesheet" />

<!-- Header Navbar -->
<nav class="navbar navbar-light p-2 px-4 d-md-none border-bottom border-secondary-subtle"
  style="position: absolute; top: 0; left: 0; right: 0; width: 100%; padding: 0; margin: 0; background-color: #fff; z-index: 1000;">
  <div class="container-fluid p-0 m-0 d-flex justify-content-between align-items-center">

    <!-- Logo (linked to home) -->
    <a class="navbar-brand mb-2" href="index.php" style="margin-left: 0; text-decoration: none;">
      <img src="shared/assets/img/webstar-logo-black.png" alt="Webstar" style="height: 25px;">
    </a>

    <div class="text-sbold mt-1 text-18" style="margin-right: 0;">
      <img class="me-1" src="shared/assets/img/webstar.png" alt="Description of Image" width="18">
      <?php echo $webstars['webstars'] ?>
    </div>

  </div>
</nav>


<!-- Bottom Navbar -->
<nav class="navbar fixed-bottom border-top d-block d-md-none" style="background-color:white; height: 80px;">
  <div class="container d-flex justify-content-around pb-1">

    <!-- Home -->
    <a href="index.php"
      class="btn d-flex nav-btn-navbar flex-column align-items-center <?php echo ($activePage == 'home') ? 'selected-nav-item' : ''; ?>"
      style="border-color:transparent; text-decoration:none;">
      <span class="material-symbols-rounded" style="font-size:20px">
        dashboard
      </span>
      <span class="text-med text-12">Home</span>
    </a>

    <!-- Courses -->
    <a href="course.php"
      class="btn d-flex nav-btn-navbar flex-column align-items-center <?php echo ($activePage == 'course') ? 'selected-nav-item' : ''; ?>"
      style="border-color:transparent; text-decoration:none;">
      <span class="material-symbols-rounded" style="font-size:20px">
        folder
      </span>
      <small class="text-med text-12">Courses</small>
    </a>

    <!-- Quests -->
    <a href="todo.php"
      class="btn d-flex nav-btn-navbar flex-column align-items-center <?php echo ($activePage == 'todo') ? 'selected-nav-item' : ''; ?>"
      style="border-color:transparent; text-decoration:none;">
      <span class="material-symbols-rounded" style="font-size:20px">
        extension
      </span>
      <small class="text-med text-12">Quests</small>
    </a>

    <!-- Inbox -->
    <a href="inbox.php"
      class="btn d-flex nav-btn-navbar flex-column align-items-center position-relative <?php echo ($activePage == 'inbox') ? 'selected-nav-item' : ''; ?>"
      style="border-color:transparent; text-decoration:none;">
      <i class="bi bi-inbox-fill" style="font-size:25px; color:var(--black); margin-top:-10px;"></i>
      <small class="text-med text-12" style="margin-top:-8px;">Inbox</small>
      <span
        class="mt-1 position-absolute top-0 start-100 z-3 translate-middle badge rounded-pill bg-danger text-reg text-white"
        style="color:white!important;">
        10
      </span>
    </a>


    <!-- More -->
    <div class="dropup">
      <button class="btn nav-btn-navbar d-flex flex-column align-items-center" data-bs-toggle="dropdown"
        aria-expanded="false" style="border-color:transparent;">
        <span class="material-symbols-rounded dehaze-icon" style="font-size:20px;">
          dehaze
        </span>
        <small class="text-med text-12">More</small>
      </button>

      <ul class="dropdown-menu dropdown-menu-end text-small shadow"
        style="bottom:100%; margin-bottom:8px; transform:none !important;">

        <!-- Settings -->
        <li style="margin-bottom:6px;">
          <a class="dropdown-item d-flex align-items-center text-med text-14" href="settings.php">
            <span class="material-symbols-rounded me-2" style="font-size:18px;">settings</span>
            Settings
          </a>
        </li>

        <!-- Support -->
        <li style="margin-bottom:6px;">
          <a class="dropdown-item d-flex align-items-center text-med text-14" href="support.php">
            <span class="material-symbols-rounded me-2" style="font-size:18px;">contact_support</span>
            Support
          </a>
        </li>

        <!-- Shop -->
        <li style="margin-bottom:6px;">
          <a class="dropdown-item d-flex align-items-center text-med text-14" href="shop.php">
            <span class="material-symbols-rounded me-2" style="font-size:18px;">shopping_bag</span>
            Shop
          </a>
        </li>

        <!-- Search -->
        <li style="margin-bottom:6px;">
          <a class="dropdown-item d-flex align-items-center text-med text-14" href="search.php" data-bs-toggle="modal"
            data-bs-target="#searchModalMobile">
            <span class="material-symbols-rounded me-2" style="font-size:18px;">search</span>
            Search
          </a>
        </li>

        <!-- Calendar -->
        <li style="margin-bottom:6px;">
          <a class="dropdown-item d-flex align-items-center text-med text-14" href="calendar.php">
            <span class="material-symbols-rounded me-2" style="font-size:18px;">calendar_month</span>
            Calendar
          </a>
        </li>

        <!-- Profile -->
        <li style="margin-bottom:6px;">
          <a class="dropdown-item d-flex align-items-center text-med text-14" href="profile.php">
            <span class="material-symbols-rounded me-2" style="font-size:18px;">person</span>
            My Profile
          </a>
        </li>

        <!-- Sign Out -->
        <li>
          <a class="dropdown-item d-flex align-items-center text-med text-14" href="login.php"
            style="color:var(--highlight);">
            <span class="material-symbols-rounded me-2" style="font-size:18px;">logout</span>
            Sign out
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const dropup = document.querySelector('.dropup');
    const dropupBtn = dropup.querySelector('button');
    const icon = dropupBtn.querySelector('.dehaze-icon');

    // When dropdown is shown
    dropup.addEventListener('shown.bs.dropdown', () => {
      icon.textContent = 'close';
      icon.classList.add('spin');
    });

    // When dropdown is hidden (click outside or menu item)
    dropup.addEventListener('hidden.bs.dropdown', () => {
      icon.textContent = 'dehaze';
      icon.classList.remove('spin');
    });

    // When "Search" item clicked, also reset icon
    const searchBtn = dropup.querySelector('a[data-bs-target="#searchModalMobile"]');
    if (searchBtn) {
      searchBtn.addEventListener('click', () => {
        icon.textContent = 'dehaze';
        icon.classList.remove('spin');
      });
    }
  });
</script>