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
                <li
                    class="nav-item my-1 d-flex align-items-center gap-2 m-3 my-1 p-2 rounded-3 <?php echo ($activePage == 'dashboard') ? 'selected-box' : ''; ?>">
                    <img src="shared/assets/img/dashboard<?php echo ($activePage == 'dashboard') ? '-w' : ''; ?>.png" class="img-fluid" style="width: 30px; height: 30px;">
                    <a class="nav-link text-dark p-0 <?php echo ($activePage == 'dashboard') ? 'selected' : ''; ?>"
                        href="index.php">Dashboard</a>
                </li>

                <li
                    class="nav-item my-1 d-flex align-items-center gap-2 m-3 my-1 p-2 rounded-3 <?php echo ($activePage == 'course') ? 'selected-box' : ''; ?>">
                    <img src="shared/assets/img/courses<?php echo ($activePage == 'course') ? '-w' : ''; ?>.png" class="img-fluid" style="width: 30px; height: 30px;">
                    <a class="nav-link text-dark p-0 <?php echo ($activePage == 'course') ? 'selected' : ''; ?>"
                        href="course.php">Courses</a>
                </li>

                <li
                    class="nav-item my-1 d-flex align-items-center gap-2 m-3 my-1 p-2 rounded-3 <?php echo ($activePage == 'feedback') ? 'selected-box' : ''; ?>">
                    <img src="shared/assets/img/explore<?php echo ($activePage == 'feedback') ? '-w' : ''; ?>.png" class="img-fluid" style="width: 30px; height: 30px;">
                    <a class="nav-link text-dark p-0 <?php echo ($activePage == 'feedback') ? 'selected' : ''; ?>"
                        href="feedback.php">Feedback</a>
                </li>

                <li
                    class="nav-item my-1 d-flex align-items-center gap-2 m-3 my-1 p-2 rounded-3 <?php echo ($activePage == 'list') ? 'selected-box' : ''; ?>">
                    <img src="shared/assets/img/view_list<?php echo ($activePage == 'list') ? '-w' : ''; ?>.png" class="img-fluid" style="width: 30px; height: 30px;">
                    <a class="nav-link text-dark p-0 <?php echo ($activePage == 'list') ? 'selected' : ''; ?>"
                        href="userslist.php">Users List</a>
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