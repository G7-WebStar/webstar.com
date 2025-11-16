<?php
$activePage = 'manage';
include('../shared/assets/database/connect.php');
include("../shared/assets/processes/admin-session-process.php");

// Get filters from GET request
$search = trim($_GET['search'] ?? '');
$sort = $_GET['sort'] ?? 'Newest';
$status = $_GET['status'] ?? 'All';

// --------------------- Professors Query ---------------------
$profWhere = "WHERE users.role = 'professor'";
if ($search !== '') {
    $profWhere .= " AND (userinfo.firstName LIKE '%$search%' OR userinfo.lastName LIKE '%$search%' OR users.userName LIKE '%$search%' OR users.email LIKE '%$search%')";
}
if ($status !== 'All') {
    $profWhere .= " AND users.status = '$status'";
}

// Order by clause
$profOrder = "userinfo.createdAt DESC";
if ($sort === 'Oldest')
    $profOrder = "userinfo.createdAt ASC";

// Final query for professors
$profQuery = "
    SELECT 
        users.userID,
        users.userName AS username,
        users.email,
        users.status,
        userinfo.createdAt,
        userinfo.firstName,
        userinfo.middleName,
        userinfo.lastName
    FROM users
    INNER JOIN userinfo ON users.userID = userinfo.userID
    $profWhere
    ORDER BY $profOrder
";
$profResult = mysqli_query($conn, $profQuery);

// Total professors (reflects filters)
$totalProfessorsQuery = "
    SELECT COUNT(*) AS total 
    FROM users 
    INNER JOIN userinfo ON users.userID = userinfo.userID
    $profWhere
";
$totalProfessorsResult = mysqli_query($conn, $totalProfessorsQuery);
$totalProfessorsRow = mysqli_fetch_assoc($totalProfessorsResult);
$totalProfessors = $totalProfessorsRow['total'] ?? 0;

// --------------------- Students Query ---------------------
$studentWhere = "WHERE users.role = 'student'";
if ($search !== '') {
    $studentWhere .= " AND (userinfo.firstName LIKE '%$search%' OR userinfo.lastName LIKE '%$search%' OR users.userName LIKE '%$search%' OR users.email LIKE '%$search%')";
}
if ($status !== 'All') {
    $studentWhere .= " AND users.status = '$status'";
}

// Order by clause
$studentOrder = "userinfo.createdAt DESC";
if ($sort === 'Oldest')
    $studentOrder = "userinfo.createdAt ASC";

// Final query for students
$studentQuery = "
    SELECT 
        users.userID,
        users.userName AS username,
        users.email,
        users.status,
        userinfo.createdAt,
        userinfo.firstName,
        userinfo.middleName,
        userinfo.lastName
    FROM users
    INNER JOIN userinfo ON users.userID = userinfo.userID
    $studentWhere
    ORDER BY $studentOrder
";
$studentResult = mysqli_query($conn, $studentQuery);

// Total students (reflects filters)
$totalStudentsQuery = "
    SELECT COUNT(*) AS total 
    FROM users 
    INNER JOIN userinfo ON users.userID = userinfo.userID
    $studentWhere
";
$totalStudentsResult = mysqli_query($conn, $totalStudentsQuery);
$totalStudentsRow = mysqli_fetch_assoc($totalStudentsResult);
$totalStudents = $totalStudentsRow['total'] ?? 0;
?>


<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webstar | Manage</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="../shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="../shared/assets/css/sidebar-and-container-styles.css">
    <link rel="stylesheet" href="../shared/assets/css/course.css">
    <link rel="stylesheet" href="../shared/assets/css/admin.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="icon" type="image/png" href="../shared/assets/img/webstar-icon.png">

    <!-- Material Design Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,1,0"
        rel="stylesheet" />

</head>

<body>
    <div class="container-fluid min-vh-100 d-flex justify-content-center align-items-center p-0 p-md-3"
        style="background-color: var(--black);">

        <div class="row w-100">

            <!-- Sidebar (only shows on mobile) -->
            <?php include '../shared/components/admin-sidebar-for-mobile.php'; ?>

            <!-- Sidebar Column (fixed on desktop) -->
            <?php include '../shared/components/admin-sidebar-for-desktop.php'; ?>

            <!-- Main Container Column-->
            <div class="col main-container m-0 p-0 mx-0 mx-md-2 p-0 p-md-4 overflow-y-auto">
                <div class="card border-0 px-3 pt-3 m-0 h-100 w-100 rounded-0 shadow-none"
                    style="background-color: transparent;">

                    <!-- Navbar for mobile -->
                    <?php include '../shared/components/admin-navbar-for-mobile.php'; ?>


                    <div class="container-fluid py-3 overflow-y-auto row-padding-top">
                        <div class="row">
                            <!-- Header Title -->
                            <div class="col-12 mb-3">
                                <div class="d-flex align-items-center ps-1">
                                    <div class="text-sbold text-22">Manage instructors</div>
                                    <span class="material-symbols-outlined ms-3" style=" font-size: 30px">
                                        supervisor_account
                                    </span>

                                    <div class="stats-count text-22 text-bold ms-1">
                                        <?php echo $totalProfessors; ?>
                                    </div>

                                </div>
                            </div>

                            <!-- Header Section (Search, Sort, Status, Add Button) -->
                            <div class="row align-items-center g-2 flex-wrap px-2 search-sort-row">
                                <!-- Search -->
                                <div class="col-12 col-lg-4 px-0 px-md-auto">
                                    <div class="search-container d-flex mx-sm-auto">
                                        <form method="GET" class="form-control bg-transparent border-0">
                                            <input type="text" id="searchInput" placeholder="Search" name="search"
                                                class="form-control py-1 text-reg text-14">

                                            <button type="submit" class="btn-outline-secondary">
                                                <i class="bi bi-search me-2"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <!-- Sort By -->
                                <div class="col-auto mobile-dropdown">
                                    <div class="d-flex align-items-center flex-nowrap">
                                        <span class="dropdown-label me-2 text-reg">Sort by</span>
                                        <div class="custom-dropdown">
                                            <button class="dropdown-btn text-reg text-14">Newest</button>
                                            <ul class="dropdown-list text-reg text-14">
                                                <li data-value="Newest">Newest</li>
                                                <li data-value="Oldest">Oldest</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <!-- Status -->
                                <div class="col-auto mobile-dropdown">
                                    <div class="d-flex align-items-center flex-nowrap">
                                        <span class="dropdown-label me-2 text-reg">Status</span>
                                        <div class="custom-dropdown">
                                            <button class="dropdown-btn text-reg text-14">All</button>
                                            <ul class="dropdown-list text-reg text-14">
                                                <li data-value="Active">Active</li>
                                                <li data-value="Inactive">Inactive</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <!-- Add Course Button -->
                                <div class="col-auto d-none d-lg-block ms-auto">
                                    <a href="register-instructor.php" style="text-decoration: none;">
                                        <button
                                            class="add-instructor-btn btn btn-primary px-3 py-1 rounded-pill text-reg text-md-14">
                                            <div style="text-decoration: none; color: var(--black);">
                                                + Register new instructor
                                            </div>
                                        </button>
                                    </a>
                                </div>


                                <!-- Mobile Button -->
                                <div class="col-12 d-lg-none d-flex justify-content-center mt-2">
                                    <a href="register-instructor.php" style="text-decoration: none;">
                                        <button
                                            class="add-instructor-btn btn btn-primary px-3 py-1 rounded-pill text-reg text-md-14">
                                            <div style="text-decoration: none; color: var(--black);">
                                                + Register new instructor
                                            </div>
                                        </button>
                                    </a>
                                </div>
                            </div>

                            <!-- Table Section -->
                            <div class="table-container mt-4 px-3" style="max-height: 900px; overflow-y: auto;">
                                <div class="table-responsive-sm">
                                    <table class="custom-table align-middle mb-0 text-med text-14">
                                        <thead>
                                            <tr>
                                                <th scope="col">Name</th>
                                                <th scope="col">Username</th>
                                                <th scope="col">Email</th>
                                                <th scope="col">Status</th>
                                                <th scope="col">Date Created</th>
                                                <th scope="col">Date Updated</th>
                                                <th scope="col" class="text-end"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="professorsTableBody">
                                            <?php while ($p = mysqli_fetch_assoc($profResult)): ?>

                                                <?php
                                                // Format Name: Last, First Middle
                                                $fullName = $p['lastName'] . ", " . $p['firstName'];
                                                if (!empty($p['middleName'])) {
                                                    $fullName .= " " . $p['middleName'];
                                                }
                                                ?>

                                                <tr>
                                                    <td><?php echo $fullName; ?></td>
                                                    <td><?php echo $p['username']; ?></td>
                                                    <td><?php echo $p['email']; ?></td>

                                                    <!-- Status -->
                                                    <td>
                                                        <?php
                                                        $status = strtolower(trim($p['status']));
                                                        echo $status === 'active' ? 'Active' : 'Inactive';
                                                        ?>
                                                    </td>


                                                    <!-- Created At -->
                                                    <td><?php echo date("m-d-y h:i:s A", strtotime($p['createdAt'])); ?>
                                                    </td>

                                                    <!-- Created At -->
                                                    <td><?php echo date("m-d-y h:i:s A", strtotime($p['createdAt'])); ?>
                                                    </td>

                                                    <!-- Updated At -->
                                                    <!-- <td><?php echo date("m-d-y h:i:s A", strtotime($p['updatedAt'])); ?>
                                                    </td> -->

                                                    <td>
                                                        <div class="dropdown">
                                                            <button class="btn btn-link text-dark p-0" type="button"
                                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="bi bi-three-dots-vertical fs-5"></i>
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-menu-end">
                                                                <li><a class="dropdown-item"
                                                                        href="manage-edit.php?userID=<?php echo $p['userID']; ?>">Edit</a>
                                                                </li>
                                                                <li><a class="dropdown-item text-danger"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#deleteModal">Delete</a></li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>

                                            <?php endwhile; ?>
                                        </tbody>

                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Student -->
                        <div class="row mt-5 mb-3">
                            <!-- Header Title -->
                            <div class="col-12 mb-3">
                                <div class="d-flex align-items-center ps-1">
                                    <div class="text-sbold text-22">Manage students</div>
                                    <span class="material-symbols-outlined ms-3" style=" font-size: 30px">
                                        supervisor_account
                                    </span>

                                    <div class="stats-count text-22 text-bold ms-1">
                                        <?php echo $totalStudents; ?>
                                    </div>

                                </div>
                            </div>

                            <!-- Table Section -->
                            <div class="table-container mt-4 px-3" style="max-height: 900px; overflow-y: auto;">
                                <div class="table-responsive-sm">
                                    <table class="custom-table align-middle mb-0 text-med text-14">
                                        <thead>
                                            <tr>
                                                <th scope="col">Name</th>
                                                <th scope="col">Username</th>
                                                <th scope="col">Email</th>
                                                <th scope="col">Status</th>
                                                <th scope="col">Date Created</th>
                                                <th scope="col">Date Updated</th>
                                                <th scope="col" class="text-end"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="studentsTableBody">
                                            <?php while ($p = mysqli_fetch_assoc($studentResult)): ?>

                                                <?php
                                                // Format Name: Last, First Middle
                                                $fullName = $p['lastName'] . ", " . $p['firstName'];
                                                if (!empty($p['middleName'])) {
                                                    $fullName .= " " . $p['middleName'];
                                                }
                                                ?>

                                                <tr>
                                                    <td><?php echo $fullName; ?></td>
                                                    <td><?php echo $p['username']; ?></td>
                                                    <td><?php echo $p['email']; ?></td>

                                                    <!-- Status -->
                                                    <td>
                                                        <?php
                                                        $status = strtolower(trim($p['status']));
                                                        echo $status === 'active' ? 'Active' : 'Inactive';
                                                        ?>
                                                    </td>


                                                    <!-- Created At -->
                                                    <td><?php echo date("m-d-y h:i:s A", strtotime($p['createdAt'])); ?>
                                                    </td>

                                                    <!-- Created At -->
                                                    <td><?php echo date("m-d-y h:i:s A", strtotime($p['createdAt'])); ?>
                                                    </td>

                                                    <!-- Updated At -->
                                                    <!-- <td><?php echo date("m-d-y h:i:s A", strtotime($p['updatedAt'])); ?>
                                                    </td> -->

                                                    <td>
                                                        <!-- <div class="dropdown">
                                                            <button class="btn btn-link text-dark p-0" type="button"
                                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="bi bi-three-dots-vertical fs-5"></i>
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-menu-end">
                                                                <li><a class="dropdown-item"
                                                                        href="edit-instructor.php?userID=<?php echo $p['userID']; ?>">Edit</a>
                                                                </li>
                                                                <li><a class="dropdown-item text-danger"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#deleteModal">Delete</a></li>
                                                            </ul>
                                                        </div> -->
                                                    </td>
                                                </tr>

                                            <?php endwhile; ?>
                                        </tbody>

                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- Delete Modal-->
    <div class="modal" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        style="transform: scale(0.8);"></button>
                </div>
                <div class="modal-body d-flex flex-column justify-content-center align-items-center text-center">
                    <span class="mt-4 text-bold text-22">This action cannot be
                        undone.</span>
                    <span class="mb-4 text-reg text-14">Are you sure you want to delete this
                        item?</span>
                </div>
                <div class="modal-footer text-sbold text-18">
                    <button type="button" class="btn rounded-pill px-4"
                        style="background-color: var(--primaryColor); border: 1px solid var(--black);"
                        data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn rounded-pill px-4"
                        style="background-color: rgba(255, 80, 80, 1); border: 1px solid var(--black);">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Live search for both professors and students
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('searchInput');
            let timeout = null;

            searchInput.addEventListener('input', function () {
                clearTimeout(timeout);
                const query = this.value;

                timeout = setTimeout(() => {
                    fetch(`manage.php?search=${encodeURIComponent(query)}&sort=<?php echo $sort; ?>&status=<?php echo $status; ?>`)
                        .then(response => response.text())
                        .then(data => {
                            const parser = new DOMParser();
                            const doc = parser.parseFromString(data, 'text/html');

                            // Update professors table
                            const profBody = doc.getElementById('professorsTableBody');
                            if (profBody) document.getElementById('professorsTableBody').innerHTML = profBody.innerHTML;

                            // Update students table
                            const studentBody = doc.getElementById('studentsTableBody');
                            if (studentBody) document.getElementById('studentsTableBody').innerHTML = studentBody.innerHTML;

                            // Update professors count
                            const profCount = doc.getElementById('professorsCount');
                            if (profCount) document.getElementById('professorsCount').textContent = profCount.textContent;

                            // Update students count
                            const studentCount = doc.getElementById('studentsCount');
                            if (studentCount) document.getElementById('studentsCount').textContent = studentCount.textContent;
                        });
                }, 300); // Delay 300ms
            });
        });
    </script>

    <script>
        document.querySelectorAll('.custom-dropdown').forEach(dropdown => {
            const btn = dropdown.querySelector('.dropdown-btn');
            const list = dropdown.querySelector('.dropdown-list');

            // Toggle dropdown visibility
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                list.style.display = list.style.display === 'block' ? 'none' : 'block';
            });

            // Handle dropdown item click
            list.querySelectorAll('li').forEach(item => {
                item.addEventListener('click', () => {
                    const value = item.dataset.value;
                    btn.textContent = value;
                    list.style.display = 'none';

                    // Get current URL params
                    const params = new URLSearchParams(window.location.search);

                    // Determine which dropdown it is by label
                    const label = dropdown.parentElement.querySelector('.dropdown-label')?.textContent.trim();
                    if (label === 'Sort by') {
                        params.set('sort', value);
                    } else if (label === 'Status') {
                        params.set('status', value);
                    }

                    // Preserve search input
                    const searchInput = document.getElementById('searchInput').value.trim();
                    if (searchInput !== '') {
                        params.set('search', searchInput);
                    }

                    // Reload page with new filters
                    window.location.search = params.toString();
                });
            });

            // Close dropdown if clicked outside
            document.addEventListener('click', (e) => {
                if (!dropdown.contains(e.target)) list.style.display = 'none';
            });
        });
    </script>




</body>


</html>