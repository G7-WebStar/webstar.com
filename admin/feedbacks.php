<?php $activePage = 'feedback'; ?>
<?php
include('../shared/assets/database/connect.php');
include("../shared/assets/processes/admin-session-process.php");

// Get total feedback count (keeps showing total feedbacks overall)
$feedbackCountQuery = "SELECT COUNT(*) AS totalFeedback FROM feedback";
$feedbackCountResult = mysqli_query($conn, $feedbackCountQuery);
$feedbackCountRow = mysqli_fetch_assoc($feedbackCountResult);
$totalFeedbacks = $feedbackCountRow['totalFeedback'];

// Get filters from GET (defaults)
$sort = $_GET['sort'] ?? 'Newest';
$role = $_GET['role'] ?? 'All';

// Build WHERE clause (case-insensitive)
$where = "";
if ($role !== 'All') {
    $role_safe = mysqli_real_escape_string($conn, strtolower($role));
    $where = "WHERE LOWER(u.role) = '" . $role_safe . "'";
}

// ORDER
$orderBy = ($sort === "Oldest") ? "ASC" : "DESC";

$feedbackQuery = "
    SELECT 
        f.feedbackID,
        f.message,
        f.created_at,
        u.userID,
        u.role,
        ui.firstName,
        ui.middleName,
        ui.lastName
    FROM feedback f
    INNER JOIN users u ON f.senderID = u.userID
    INNER JOIN userinfo ui ON u.userID = ui.userID
    $where
    ORDER BY f.created_at $orderBy
";
$feedbackResult = mysqli_query($conn, $feedbackQuery);
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webstar | Feedback</title>
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

    <style>
        /* keep dropdown-list hidden by default (your existing CSS might already do this) */
        .dropdown-list { display: none; position: absolute; z-index: 2000; list-style:none; padding:0; margin:6px 0 0 0; background:var(--white); border-radius:6px;}
        .custom-dropdown { position: relative; }
        .custom-dropdown .dropdown-btn { cursor: pointer; }
        .custom-dropdown .dropdown-list li { padding:8px 12px; cursor:pointer; white-space:nowrap; }
        .custom-dropdown .dropdown-list li:hover { background: rgba(0,0,0,0.04); }
    </style>
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

                    <div class="container-fluid py-1">
                        <div class="row">
                            <!-- Header Title -->
                            <div class="col-12 mb-3">
                                <div class="d-flex align-items-center ps-2">
                                    <div class="text-sbold text-22">Feedbacks</div>
                                    <span class="material-symbols-outlined ms-4" style="font-size: 30px;">
                                        feedback
                                    </span>
                                    <div class="stats-count text-22 text-bold ms-1"><?= $totalFeedbacks ?></div>
                                </div>
                            </div>

                            <!-- Header Section -->
                            <div class="row align-items-center g-2 flex-wrap px-2">
                                <!-- Sort By -->
                                <div class="col-auto ms-3 mobile-dropdown">
                                    <div class="d-flex align-items-center flex-nowrap">
                                        <span class="dropdown-label me-2 text-reg">Sort by</span>
                                        <div class="custom-dropdown" data-filter="sort">
                                            <button class="dropdown-btn text-reg text-14"><?= htmlspecialchars($sort) ?></button>
                                            <ul class="dropdown-list text-reg text-14" style="background-color: white;">
                                                <li data-value="Newest">Newest</li>
                                                <li data-value="Oldest">Oldest</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <!-- Role -->
                                <div class="col-auto ms-3 mobile-dropdown">
                                    <div class="d-flex align-items-center flex-nowrap">
                                        <span class="dropdown-label me-2 text-reg">Role</span>
                                        <div class="custom-dropdown" data-filter="role">
                                            <button class="dropdown-btn text-reg text-14"><?= htmlspecialchars($role) ?></button>
                                            <ul class="dropdown-list text-reg text-14" style="background-color: white;">
                                                <li data-value="All">All</li>
                                                <li data-value="Student">Student</li>
                                                <li data-value="Professor">Professor</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <!-- Table Section -->
                                <div class="table-container mt-4 px-3" style="max-height: 400px; overflow-y: auto;">
                                    <div class="table-responsive-sm">
                                        <table class="custom-table align-middle mb-0 text-med text-14">
                                            <thead>
                                                <tr>
                                                    <th scope="col"></th>
                                                    <th scope="col">Name</th>
                                                    <th scope="col"></th>
                                                    <th scope="col">Role</th>
                                                    <th scope="col"></th>
                                                    <th scope="col">Content</th>
                                                    <th scope="col"></th>
                                                    <th scope="col">Date Submitted</th>

                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while ($f = mysqli_fetch_assoc($feedbackResult)): ?>
                                                    <tr>
                                                        <td></td>
                                                        <td><?= htmlspecialchars($f['lastName'] . ', ' . $f['firstName'] . ' ' . $f['middleName']) ?>
                                                        </td>
                                                        <td></td>
                                                        <td><?= ucfirst($f['role']) ?></td>
                                                        <td></td>
                                                        <td data-bs-toggle="modal"
                                                            data-bs-target="#feedbackModal<?= $f['feedbackID'] ?>">
                                                            <?= htmlspecialchars($f['message']) ?>
                                                        </td>
                                                        <td></td>
                                                        <td><?= date("m-d-Y H:i:s", strtotime($f['created_at'])) ?></td>

                                                    </tr>

                                                    <!-- Modal for this feedback -->
                                                    <div class="modal" id="feedbackModal<?= $f['feedbackID'] ?>"
                                                        tabindex="-1">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content">
                                                                <div class="modal-header border-bottom">
                                                                    <div class="text-sbold text-22">Feedback</div>
                                                                    <button type="button" class="btn-close"
                                                                        data-bs-dismiss="modal" aria-label="Close"
                                                                        style="transform: scale(0.8);"></button>
                                                                </div>
                                                                <div class="modal-body d-flex flex-column justify-content-start align-items-start mt-2 text-medium text-14 w-100"
                                                                    style="text-align: justify;">
                                                                    <p class="mb-0">
                                                                        <?= htmlspecialchars($f['lastName'] . ', ' . $f['firstName'] . ' ' . $f['middleName']) ?>
                                                                    </p>
                                                                    <p class="mb-0">
                                                                        <?= date("m-d-Y H:i:s", strtotime($f['created_at'])) ?>
                                                                    </p>
                                                                    <p class="mb-0"><?= ucfirst($f['role']) ?></p>
                                                                    <p class="mt-3 mb-4">
                                                                        <?= htmlspecialchars($f['message']) ?>
                                                                    </p>
                                                                </div>
                                                                <div class="modal-footer border-top"
                                                                    style="padding-top: 45px;"></div>
                                                            </div>
                                                        </div>
                                                    </div>
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

    <!-- Feedback Modal-->
    <div class="modal" id="feedbackModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-bottom">
                    <div class="text-sbold text-22">Feedback</div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        style="transform: scale(0.8);"></button>
                </div>
                <!-- <div class="modal-body d-flex flex-column justify-content-start align-items-start mt-2 text-medium text-14 w-100"
                    style="text-align: justify;">
                    <p class="mb-0">Torrillo, Christian James D.</p>
                    <p class="mb-0">10-21-25 00:11:21</p>
                    <p class="mb-0">Student</p>
                    <p class="mt-3 mb-4">
                        Webstar has made learning more engaging and fun. The gamified system motivates students to
                        participate actively in every lesson,
                        and earning XP through quests adds an exciting twist to traditional coursework. The interface is
                        intuitive, and the progress tracking features help
                        students see their improvement over time. As a professor, managing courses and monitoring
                        student activity has become much easier. The dashboard
                        provides clear insights, and the feedback tools make communication seamless. Overall, Webstar
                        creates an enjoyable and efficient learning environment
                        for both teachers and students.
                    </p>
                </div> -->

                <div class="modal-footer border-top" style="padding-top: 45px;"></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Dropdown js -->
    <script>
        document.querySelectorAll('.custom-dropdown').forEach(dropdown => {
            const btn = dropdown.querySelector('.dropdown-btn');
            const list = dropdown.querySelector('.dropdown-list');

            // toggle dropdown
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                // close other dropdowns first
                document.querySelectorAll('.custom-dropdown .dropdown-list').forEach(l => {
                    if (l !== list) l.style.display = 'none';
                });
                list.style.display = list.style.display === 'block' ? 'none' : 'block';
            });

            // click on item
            list.querySelectorAll('li').forEach(item => {
                item.addEventListener('click', (ev) => {
                    ev.stopPropagation();
                    const value = item.dataset.value;
                    btn.textContent = value;
                    list.style.display = 'none';

                    // Build URL params and update correct filter
                    const params = new URLSearchParams(window.location.search);

                    const filterType = dropdown.dataset.filter; // "sort" or "role"
                    if (filterType === 'sort') {
                        params.set('sort', value);
                    } else if (filterType === 'role') {
                        params.set('role', value);
                    }

                    // Navigate with new params (preserve other params)
                    window.location.search = params.toString();
                });
            });
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', () => {
            document.querySelectorAll('.custom-dropdown .dropdown-list').forEach(l => l.style.display = 'none');
        });
    </script>

    <script>
        // Modal backdrop cleanup (keeps your previous logic but scoped safely)
        document.querySelectorAll('[id^="feedbackModal"]').forEach(modal => {
            const modalID = modal.id;

            modal.addEventListener('show.bs.modal', () => {
                document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
                document.body.classList.remove('modal-open');
                setTimeout(() => {
                    document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
                    document.body.classList.remove('modal-open');
                }, 50);
            });

            modal.addEventListener('shown.bs.modal', () => {
                document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
                document.body.classList.remove('modal-open');
            });

            modal.addEventListener('hidden.bs.modal', () => {
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                const checkbox = document.querySelector(`.feedback-checkbox[data-bs-target="#${modalID}"]`);
                if (checkbox) checkbox.checked = false;
            });
        });
    </script>

</body>

</html>
