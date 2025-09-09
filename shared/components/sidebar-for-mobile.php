<div class="offcanvas offcanvas-start d-md-none" tabindex="-1" id="sidebarOffcanvas"
    aria-labelledby="sidebarOffcanvasLabel" style="background-color:var(--dirtyWhite); width: 250px;">
    <div class="offcanvas-header">
        <button type="button" class="mt-2 btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body d-flex flex-column p-0">
        <div class="d-flex flex-column flex-shrink-0 p-3" style="width: 100%;">
            <a class="d-flex align-items-center  text-decoration-none">
                <img src="shared/assets/img/webstar-logo-black.png" class="img-fluid w-100 py-3 px-2" />
            </a>

            <hr>
            <ul class="nav nav-pills flex-column mb-auto">

                <li class="nav-item d-flex align-items-center gap-2 my-1 p-2 rounded-3 <?php echo ($activePage == 'home') ? 'selected-box' : ''; ?>"
                    data-page="home">
                    <img src="shared/assets/img/dashboard.png" style="width: 30px; height: 30px;">
                    <a class="nav-link text-dark p-0 text-med text-18 ps-2 <?php echo ($activePage == 'home') ? 'selected' : ''; ?>"
                        href="index.php"><strong>Home</strong></a>
                </li>

                <li class="nav-item d-flex align-items-center gap-2 my-1 p-2 rounded-3 <?php echo ($activePage == 'course') ? 'selected-box' : ''; ?>"
                    data-page="course">
                    <img src="shared/assets/img/courses.png" style="width: 30px; height: 30px;">
                    <a class="nav-link text-dark p-0 text-med text-18 ps-2 <?php echo ($activePage == 'course') ? 'selected' : ''; ?>"
                        href="course.php"><strong>Courses</strong></a>
                </li>

                <li class="nav-item d-flex align-items-center gap-2 my-1 p-2 rounded-3 <?php echo ($activePage == 'inbox') ? 'selected-box' : ''; ?>"
                    data-page="inbox">
                    <img src="shared/assets/img/inbox.png" style="width: 30px; height: 30px;">
                    <a class="nav-link text-dark p-0 text-med text-18 ps-2 <?php echo ($activePage == 'inbox') ? 'selected' : ''; ?>"
                        href="inbox.php"><strong>Inbox</strong></a>
                </li>

                <li class="nav-item d-flex align-items-center gap-2 my-1 p-2 rounded-3 <?php echo ($activePage == 'todo') ? 'selected-box' : ''; ?>"
                    data-page="todo">
                    <img src="shared/assets/img/todo.png" style="width: 30px; height: 30px;">
                    <a class="nav-link text-dark p-0 text-med text-18 ps-2 <?php echo ($activePage == 'todo') ? 'selected' : ''; ?>"
                        href="todo.php"><strong>To-do</strong></a>
                </li>

                <li class="nav-item d-flex align-items-center gap-2 my-1 p-2 rounded-3 <?php echo ($activePage == 'explore') ? 'selected-box' : ''; ?>"
                    data-page="explore" data-bs-toggle="modal" data-bs-target="#searchModalMobile">
                    <img src="shared/assets/img/explore.png" class="img-fluid" style="width: 30px; height: 30px;">
                    <a class="nav-link text-dark p-0 text-med text-18 ps-2 <?php echo ($activePage == 'explore') ? 'selected' : ''; ?>">
                        <strong>Explore</strong></a>
                </li>


                <li class="nav-item d-flex align-items-center gap-2 my-1 p-2 rounded-3 <?php echo ($activePage == 'shop') ? 'selected-box' : ''; ?>"
                    data-page="shop">
                    <img src="shared/assets/img/shop.png" style="width: 30px; height: 30px;">
                    <a class="nav-link text-dark p-0 text-med text-18 ps-2 <?php echo ($activePage == 'shop') ? 'selected' : ''; ?>"
                        href="shop.php"><strong>Shop</strong></a>
                </li>

            </ul>

            <!-- Profile -->
            <hr>
            <div class="dropdown mt-auto py-4 px-2" style="letter-spacing: -1px;">
                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="https://avatars.githubusercontent.com/u/181800261?s=96&v=4" alt="" width="32" height="32"
                        class="rounded-circle me-2">
                    <strong class="text-dark text-med text-16 px-1">jamesdoe</strong>
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
                            href="login.php"><i class="fa-solid fa-right-from-bracket me-2"></i>Sign
                            out</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Search Modal -->
<div class="modal fade" id="searchModalMobile" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="background: transparent !important; box-shadow: none !important;">

            <!-- Search Bar (separated, clean, no background) -->
            <div class="p-3 position-relative">
                <input type="text" class="form-control rounded-pill pe-5 border-black" placeholder="Search students & professors">
                <i class="bi bi-search position-absolute top-50 end-0 translate-middle-y me-5 text-muted"></i>
            </div>

            <!-- Search Results -->
            <div class="p-3">
                <div class="list-group rounded-3 shadow-sm border border-black">
                    <a href="#" class="list-group-item list-group-item-action d-flex align-items-center border-0">
                        <div class="rounded-circle bg-primary me-3" style="width:40px; height:40px;"></div>
                        <div>
                            <div class="fw-bold">Christian James D. Torrillo</div>
                            <small class="text-muted">@jamesdoe</small>
                        </div>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action d-flex align-items-center border-0">
                        <div class="rounded-circle bg-primary me-3" style="width:40px; height:40px;"></div>
                        <div>
                            <div class="fw-bold">Christian James D. Torrillo</div>
                            <small class="text-muted">@jamesdoe</small>
                        </div>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action d-flex align-items-center border-0">
                        <div class="rounded-circle bg-primary me-3" style="width:40px; height:40px;"></div>
                        <div>
                            <div class="fw-bold">Christian James D. Torrillo</div>
                            <small class="text-muted">@jamesdoe</small>
                        </div>
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>