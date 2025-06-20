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
                    <a class="nav-link selected p-0" href="index.php">Dashboard</a>
                </li>
                <li class="nav-item d-flex align-items-center gap-2 my-1 p-2">
                    <img src="shared/assets/img/courses.png" style="width: 30px; height: 30px;">
                    <a class="nav-link text-dark p-0" href="course.php">Courses</a>
                </li>
                <li class="nav-item d-flex align-items-center gap-2 my-1 p-2">
                    <img src="shared/assets/img/explore.png" style="width: 30px; height: 30px;">
                    <a class="nav-link text-dark p-0" href="feedback.php">Feedback</a>
                </li>
                <li class="nav-item d-flex align-items-center gap-2 my-1 p-2">
                    <img src="shared/assets/img/view_list.png" style="width: 30px; height: 30px;">
                    <a class="nav-link text-dark p-0" href="userslist.php">Users List</a>
                </li>
            </ul>

            <!-- Profile -->
            <hr>
            <div class="dropdown mt-auto p-2" style="font-family: var(--Bold); letter-spacing: -1px;">
                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="https://avatars.githubusercontent.com/u/181800261?s=96&v=4" alt="" width="32" height="32" class="rounded-circle me-2">
                    <strong class="text-dark">james</strong>
                </a>
                <ul class="dropdown-menu dropdown-menu-end text-small shadow">
                    <li><a class="dropdown-item" href="#">Settings</a></li>
                    <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item" href="login.php">Sign out</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>