<div class="offcanvas offcanvas-start d-md-none" tabindex="-1" id="sidebarOffcanvas"
    aria-labelledby="sidebarOffcanvasLabel" style="background-color: #fff; width: 250px;">
    <div class="offcanvas-header">
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body d-flex flex-column p-0">
        <div class="d-flex flex-column flex-shrink-0 p-3" style="width: 100%;">
            <a href="/" class="d-flex align-items-center  text-decoration-none">
                <img src="shared/assets/img/webstar-logo-blue.png" class="img-fluid w-100 py-3 px-2" />
            </a>

             <hr>
            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item d-flex align-items-center gap-2 my-1 p-2 rounded-3 selected-box">
                    <img src="shared/assets/img/dashboard-w.png" style="width: 30px; height: 30px;">
                    <a class="nav-link selected p-0" href="index.php">Home</a>
                </li>
                <li class="nav-item d-flex align-items-center gap-2 my-1 p-2">
                    <img src="shared/assets/img/courses.png" style="width: 30px; height: 30px;">
                    <a class="nav-link text-dark p-0" href="course.php">Courses</a>
                </li>
                <li class="nav-item d-flex align-items-center gap-2 my-1 p-2">
                    <img src="shared/assets/img/explore.png" style="width: 30px; height: 30px;">
                    <a class="nav-link text-dark p-0" href="explore.php">Explore</a>
                </li>
                <li class="nav-item d-flex align-items-center gap-2 my-1 p-2">
                    <img src="shared/assets/img/shop.png" style="width: 30px; height: 30px;">
                    <a class="nav-link text-dark p-0" href="shop.php">Shop</a>
                </li>
                <li class="nav-item d-flex align-items-center gap-2 my-1 p-2">
                    <img src="shared/assets/img/games.png" style="width: 30px; height: 30px;">
                    <a class="nav-link text-dark p-0" href="miniGames.php">Mini-games</a>
                </li>
            </ul>

            <!-- Profile -->
            <hr>
             <div class="dropdown mt-auto p-2" style="letter-spacing: -1px;">
                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="https://avatars.githubusercontent.com/u/181800261?s=96&v=4" alt="" width="32" height="32"
                        class="rounded-circle me-2">
                    <strong class="text-dark"  style="font-family: var(--Bold);" >james</strong>
                </a>
                <ul class="dropdown-menu dropdown-menu-end text-small shadow">
                    <li class="ms-3 my-1"style="font-family: var(--Bold);" ><i class="fa-solid fa-gear me-2"></i>Settings</li>
                    <li><a class="dropdown-item" style="font-family: var(--Regular);" href="#">Terms & Conditions</a></li>
                    <li><a class="dropdown-item" style="font-family: var(--Regular);" href="#">Change Password</a></li>
                    <li><a class="dropdown-item" style="font-family: var(--Regular);" href="#">FAQs</a></li>
                    <li><a class="dropdown-item" style="font-family: var(--Regular);" href="#">Send Feedback</a></li>
                     <hr class="dropdown-divider">
                    <li><a class="dropdown-item" style="font-family: var(--Bold);" href="profile.php"><i class="fa-solid fa-user me-2"></i>My Profile</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item" style="font-family: var(--Bold);" href="login.php"><i class="fa-solid fa-right-from-bracket me-2"></i>Sign out</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>