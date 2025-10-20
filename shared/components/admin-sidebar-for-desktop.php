<div class="col-auto d-none d-md-block">
    <div class="col-auto d-none d-md-block">
        <div class="row">
            <!-- Sidebar -->
            <div class="card border-0 sidebar mx-2 p-2 overflow-y-auto" style="width: 220px;">
                <!-- Logo -->
                <div class="d-flex justify-content-center">
                    <img src="../shared/assets/img/webstar-logo-black.png" class="img-fluid pt-5 pb-5 px-3"
                        width="180px;">
                </div>

                <!-- Navigation -->
                <ul class="nav flex-column">

                    <li class="nav-item my-1 d-flex align-items-center gap-2 m-3 p-2 rounded-3 <?php echo ($activePage == 'adminIndex') ? 'selected-box' : ''; ?>"
                        data-page="home">
                        <img src="../shared/assets/img/dashboard.png" class="img-fluid"
                            style="width: 30px; height: 30px;">
                        <a class="nav-link text-dark p-0 text-med text-18 ps-2 <?php echo ($activePage == 'adminIndex') ? 'selected' : ''; ?>"
                            href="index.php"><strong>Home</strong></a>
                    </li>

                    <li class="nav-item my-1 d-flex align-items-center gap-2 m-3 p-2 rounded-3 <?php echo ($activePage == 'manage') ? 'selected-box' : ''; ?>"
                        data-page="">
                        <img src="../shared/assets/img/explore.png" class="img-fluid"
                            style="width: 30px; height: 30px;">
                        <a class="nav-link text-dark p-0 text-med text-18 ps-2 <?php echo ($activePage == 'manage') ? 'selected' : ''; ?>"
                            href="manage.php"><strong>Manage</strong></a>
                    </li>

                    <li class="nav-item my-1 d-flex align-items-center gap-2 m-3 p-2 rounded-3 <?php echo ($activePage == 'feedback') ? 'selected-box' : ''; ?>"
                        data-page="">
                        <img src="../shared/assets/img/inbox.png" class="img-fluid"
                            style="width: 30px; height: 30px;">
                        <a class="nav-link text-dark p-0 text-med text-18 ps-2 <?php echo ($activePage == 'feedback') ? 'selected' : ''; ?>"
                            href="feedbacks.php"><strong>Feedback</strong></a>
                    </li>

                </ul>


                <div class="dropdown mt-auto p-4" style="letter-spacing: -1px;">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="https://avatars.githubusercontent.com/u/181800261?s=96&v=4" alt="" width="32"
                            height="32" class="rounded-circle me-2">
                        <strong class="text-dark text-med text-16 px-1">Admin</strong>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end text-small shadow">
                        <li class="ms-3 my-1" style="font-family: var(--Bold);"><i
                                class="fa-solid fa-gear me-2"></i>Settings</li>
                        <li><a class="dropdown-item" style="font-family: var(--Regular);"
                                href="termsAndConditions.php">Terms &
                                Conditions</a></li>
                        <li><a class="dropdown-item" style="font-family: var(--Regular);"
                                href="changepassword.php">Change
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
                                href="../login.php"><i class="fa-solid fa-right-from-bracket me-2"></i>Sign
                                out</a></li>
                    </ul>
                </div>

            </div>
        </div>
    </div>
</div>