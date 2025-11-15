<div class="offcanvas offcanvas-start d-md-none" tabindex="-1" id="sidebarOffcanvas"
    aria-labelledby="sidebarOffcanvasLabel" style="background-color:var(--dirtyWhite); width: 250px;">
    <div class="offcanvas-header">
        <button type="button" class="mt-2 btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body d-flex flex-column p-0">
        <div class="d-flex flex-column flex-shrink-0 p-3" style="width: 100%;">
            <a class="d-flex align-items-center  text-decoration-none">
                <img src="../shared/assets/img/webstar-logo-black.png" class="img-fluid w-100 py-3 px-2" />
            </a>

            <hr>
            <ul class="nav nav-pills flex-column mb-auto">

                <li class="nav-item d-flex align-items-center gap-2 my-1 p-2 rounded-3 <?php echo ($activePage == 'adminIndex') ? 'selected-box' : ''; ?>"
                    data-page="home">
                    <img src="../shared/assets/img/dashboard.png" style="width: 30px; height: 30px;">
                    <a class="nav-link text-dark p-0 text-med text-18 ps-2 <?php echo ($activePage == 'adminIndex') ? 'selected' : ''; ?>"
                        href="index.php"><strong>Home</strong></a>
                </li>

                <li class="nav-item d-flex align-items-center gap-2 my-1 p-2 rounded-3 <?php echo ($activePage == 'manage') ? 'selected-box' : ''; ?>"
                    data-page="course">
                    <img src="../shared/assets/img/explore.png" style="width: 30px; height: 30px;">
                    <a class="nav-link text-dark p-0 text-med text-18 ps-2 <?php echo ($activePage == 'manage') ? 'selected' : ''; ?>"
                        href="manage.php"><strong>Manage</strong></a>
                </li>

                <li class="nav-item d-flex align-items-center gap-2 my-1 p-2 rounded-3 <?php echo ($activePage == 'feedback') ? 'selected-box' : ''; ?>"
                    data-page="shop">
                    <img src="../shared/assets/img/inbox.png" style="width: 30px; height: 30px;">
                    <a class="nav-link text-dark p-0 text-med text-18 ps-2 <?php echo ($activePage == 'feedback') ? 'selected' : ''; ?>"
                        href="feedbacks.php"><strong>Feedback</strong></a>
                </li>

            </ul>

            <!-- Profile -->
            <hr>
            <div class="dropdown mt-auto py-4 px-2" style="letter-spacing: -1px;">
                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="https://avatars.githubusercontent.com/u/181800261?s=96&v=4" alt="" width="32" height="32"
                        class="rounded-circle me-2">
                    <strong class="text-dark text-med text-16 px-1">Admin</strong>
                </a>
                <ul class="dropdown-menu dropdown-menu-end text-small shadow">
                    <li class="ms-3 my-1" style="font-family: var(--Bold);"><i
                            class="fa-solid fa-gear me-2"></i>Settings</li>
                    <li><a class="dropdown-item" style="font-family: var(--Regular);"
                            href="termsAndConditions.php">Terms &
                            Conditions</a></li>
                    <li><a class="dropdown-item" style="font-family: var(--Regular);" href="changepassword.php">Change
                            Password</a></li>
                    <li><a class="dropdown-item" style="font-family: var(--Regular);" href="faqs.php">FAQs</a>
                    </li>
                    <li><a class="dropdown-item" style="font-family: var(--Regular);" href="feedback.php">Send
                            Feedback</a></li>
                    <hr class="dropdown-divider">
                    <li><a class="dropdown-item" style="font-family: var(--Bold);" href="profile.php"><i
                                class="fa-solid fa-user me-2"></i>My Profile</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item" style="font-family: var(--Bold); color:var(--highlight)"
                            href="#" onclick="logout();"><i class="fa-solid fa-right-from-bracket me-2"></i>Sign
                            out</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>