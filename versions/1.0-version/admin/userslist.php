<?php $activePage = 'list'; ?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webstar | admin | Users List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="shared/assets/css/sidebar-and-container-styles.css">
    <style>
        .custom-thead th {
            background-color: var(--blue);
            color: white;
        }
    </style>
</head>

<body>
    <div class="container-fluid min-vh-100 d-flex justify-content-center align-items-center p-3">

        <div class="row w-100">

            <!-- Sidebar (only shows on mobile) -->
            <?php include 'shared/components/sidebar-for-mobile.php'; ?>

            <!-- Sidebar Column (fixed on desktop) -->
            <div class="col-auto d-none d-md-block">
                <?php include 'shared/components/sidebar-for-desktop.php'; ?>
            </div>

            <!-- Main Container Column-->
            <div class="col main-container m-0 p-0 mx-2 p-4 overflow-y-auto">
                <div class="card border-0 p-3 h-100 w-100 rounded-0 shadow-none">

                    <!-- Navbar for mobile -->
                    <?php include 'shared/components/navbar-for-mobile.php'; ?>

                    <!-- PUT CONTENT HERE -->
                    <div class="container-fluid py-3 overflow-y-auto">
                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                            <div class="m-0" style="font-size: 2rem; font-weight:bold;">User List</div>
                            <form class="d-flex justify-content-end" role="search">
                                <input class="form-control rounded-pill px-3" type="search" placeholder="Search users..." aria-label="Search" style="min-width: 230px; max-width: 300px;">
                            </form>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover align-middle text-nowrap">
                                <thead class="custom-thead">
                                    <tr>
                                        <th>Name</th>
                                        <th>Username</th>
                                        <th>Birthdate</th>
                                        <th>Email</th>
                                        <th>Phone Number</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>John Doe</td>
                                        <td>jdoe123</td>
                                        <td>1990-05-14</td>
                                        <td>johndoe@example.com</td>
                                        <td>0917-123-4567</td>
                                    </tr>
                                    <tr>
                                        <td>Jane Smith</td>
                                        <td>jsmith88</td>
                                        <td>1988-09-21</td>
                                        <td>janesmith@example.com</td>
                                        <td>0928-765-4321</td>
                                    </tr>
                                    <tr>
                                        <td>Carlos Reyes</td>
                                        <td>creyes</td>
                                        <td>1995-12-03</td>
                                        <td>carlosr@example.com</td>
                                        <td>0905-555-7890</td>
                                    </tr>
                                    <tr>
                                        <td>Maria Lopez</td>
                                        <td>mlopez2020</td>
                                        <td>1993-07-19</td>
                                        <td>maria.lopez@example.com</td>
                                        <td>0918-888-1234</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

</body>


</html>