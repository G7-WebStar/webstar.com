<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="shared/assets/css/sidebar-and-container-styles.css">

    <style>
        .profile {
            background-color: var(--blue);
        }

        .logo-badge {
            background-color: var(--black);
        }
    </style>
</head>

<body>
    <div class="container-fluid min-vh-100 d-flex justify-content-center align-items-center p-3">

        <div class="row w-100">

            <!-- Sidebar (only shows on mobile) -->
            <?php include 'shared/components/sidebar-for-mobile.php'; ?>

            <!-- Sidebar Column (fixed on desktop) -->
            <?php include 'shared/components/sidebar-for-desktop.php'; ?>

            <!-- Main Container Column-->
            <div class="col main-container m-0 p-0 mx-2 p-4 ">
                <div class="card border-0 p-3 h-100 w-100 rounded-0 shadow-none">

                    <!-- Navbar for mobile -->
                    <?php include 'shared/components/navbar-for-mobile.php'; ?>

                    <div class="row g-0 w-100 h-100">

                        <!-- First Column -->
                        <div class="col d-flex flex-column h-100 me-2">
                            <!-- Profile -->
                            <div class="row m-0 profile mb-2 rounded-4 h-50 w-100">

                            </div>

                            <!-- Logo Badge -->
                            <div class="row m-0 logo-badge mt-2 rounded-4 h-50 w-100">

                            </div>

                        </div>
                        <!-- Second Column -->
                        <div class="col m-0 d-flex flex-column h-100 profile rounded-4 ms-2">
                            <!-- Currently Learning -->
                                <div class="row m-0 h-50 w-100">

                                </div>
                            <!-- My Badges -->
                                <div class="row m-0 h-50 w-100 mt-2 ">

                                </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

</body>


</html>