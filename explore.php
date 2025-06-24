<?php $activePage = 'explore'; ?>


<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webstar | Explore</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="shared/assets/css/sidebar-and-container-styles.css">
    <link rel="stylesheet" href="shared/assets/css/explore.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>

<body>
    <div class="container-fluid min-vh-100 d-flex justify-content-center align-items-center p-3">
        <div class="row w-100">
            <!-- Sidebar (only shows on mobile) -->
            <?php include 'shared/components/sidebar-for-mobile.php'; ?>

            <!-- Sidebar Column (fixed on desktop) -->
            <?php include 'shared/components/sidebar-for-desktop.php'; ?>

            <!-- Main Container Column-->
            <div class="col main-container m-0 p-0 mx-2 p-4">
                <div class="card border-0 p-3 h-100 w-100 rounded-0 shadow-none">
                    <!-- Navbar for mobile -->
                    <?php include 'shared/components/navbar-for-mobile.php'; ?>

                    <div class="container p-3" <div class="searchbar-container">
                        <div class="searchbar">
                            <span class="searchbar-icon bg-white border-0" id="search">
                                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="31" fill="currentColor"
                                    class="bi bi-search" viewBox="0 0 16 16">
                                    <path
                                        d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85zm-5.442 1.398a5.5 5.5 0 1 1 0-11 5.5 5.5 0 0 1 0 11z" />
                                </svg>
                            </span>
                            <input type="text" class="form-control border-0 explore-search-input" placeholder="Search">
                        </div>
                    </div>
                    <div class="user-list">
                        <div class="user-row">
                            <div class="avatar rounded-circle bg-primary"></div>
                            <div>
                                <div class="fw-bold">John Doe</div>
                                <div class="text-secondary">@johndoe</div>
                            </div>
                        </div>
                        <hr>
                        <div class="user-row">
                            <div class="avatar rounded-circle bg-primary"></div>
                            <div>
                                <div class="fw-bold">Jane Smith</div>
                                <div class="text-secondary">@janesmith</div>
                            </div>
<<<<<<< Updated upstream
=======
                            <hr>
                            <div class="user-row">
                                <div class="avatar rounded-circle bg-primary"></div>
                                <div>
                                    <div class="fw-bold">Jane Smith</div>
                                    <div class="text-secondary">@janesmith</div>
                                </div>
                            </div>
                            <hr>
                            <div class="user-row">
                                <div class="avatar rounded-circle bg-primary"></div>
                                <div>
                                    <div class="fw-bold">John Smith</div>
                                    <div class="text-secondary">@johnsmith</div>
                                </div>
                            </div>
                            <hr>
                            <div class="user-row">
                                <div class="avatar rounded-circle bg-primary"></div>
                                <div>
                                    <div class="fw-bold">Jane Doe</div>
                                    <div class="text-secondary">@janedoe</div>
                                </div>
                            </div>
                            <hr>
                            <div class="user-row">
                                <div class="avatar rounded-circle bg-primary"></div>
                                <div>
                                    <div class="fw-bold">Bill Gates</div>
                                    <div class="text-secondary">@billgates</div>
                                </div>
                            </div>
                            <hr>
                            <hr>
                            <div class="user-row">
                                <div class="avatar rounded-circle bg-primary"></div>
                                <div>
                                    <div class="fw-bold">Bill Gates</div>
                                    <div class="text-secondary">@billgates</div>
                                </div>
                            </div>
                            <hr>
                            <hr>
                            <div class="user-row">
                                <div class="avatar rounded-circle bg-primary"></div>
                                <div>
                                    <div class="fw-bold">Bill Gates</div>
                                    <div class="text-secondary">@billgates</div>
                                </div>
                            </div>
                            <hr>
                            <hr>
                            <div class="user-row">
                                <div class="avatar rounded-circle bg-primary"></div>
                                <div>
                                    <div class="fw-bold">Bill Gates</div>
                                    <div class="text-secondary">@billgates</div>
                                </div>
                            </div>
                            <hr>
>>>>>>> Stashed changes
                        </div>
                        <hr>
                        <div class="user-row">
                            <div class="avatar rounded-circle bg-primary"></div>
                            <div>
                                <div class="fw-bold">John Smith</div>
                                <div class="text-secondary">@johnsmith</div>
                            </div>
                        </div>
                        <hr>
                        <div class="user-row">
                            <div class="avatar rounded-circle bg-primary"></div>
                            <div>
                                <div class="fw-bold">Jane Doe</div>
                                <div class="text-secondary">@janedoe</div>
                            </div>
                        </div>
                        <hr>
                        <div class="user-row">
                            <div class="avatar rounded-circle bg-primary"></div>
                            <div>
                                <div class="fw-bold">Bill Gates</div>
                                <div class="text-secondary">@billgates</div>
                            </div>
                        </div>
                        <hr>
                        <hr>
                        <div class="user-row">
                            <div class="avatar rounded-circle bg-primary"></div>
                            <div>
                                <div class="fw-bold">Bill Gates</div>
                                <div class="text-secondary">@billgates</div>
                            </div>
                        </div>
                        <hr>
                        <hr>
                        <div class="user-row">
                            <div class="avatar rounded-circle bg-primary"></div>
                            <div>
                                <div class="fw-bold">Bill Gates</div>
                                <div class="text-secondary">@billgates</div>
                            </div>
                        </div>
                        <hr>
                        <hr>
                        <div class="user-row">
                            <div class="avatar rounded-circle bg-primary"></div>
                            <div>
                                <div class="fw-bold">Bill Gates</div>
                                <div class="text-secondary">@billgates</div>
                            </div>
                        </div>
                        <hr>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>