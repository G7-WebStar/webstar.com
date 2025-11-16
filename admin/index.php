<?php $activePage = 'adminIndex'; ?>
<?php
include('../shared/assets/database/connect.php');
include("../shared/assets/processes/admin-session-process.php");

?>
<?php
// GET TOTAL USERS
$totalUsersQuery = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users");
$totalUsers = mysqli_fetch_assoc($totalUsersQuery)['total'];

// GET TOTAL PROFESSORS
$totalProfQuery = mysqli_query($conn, "SELECT COUNT(*) AS totalProf FROM users WHERE role = 'professor'");
$totalProfessors = mysqli_fetch_assoc($totalProfQuery)['totalProf'];

// GET TOTAL STUDENTS
$totalStudQuery = mysqli_query($conn, "SELECT COUNT(*) AS totalStud FROM users WHERE role = 'student'");
$totalStudents = mysqli_fetch_assoc($totalStudQuery)['totalStud'];

// TOTAL COURSES (ALL-TIME)
$totalCoursesQuery = mysqli_query($conn, "SELECT COUNT(*) AS total FROM courses");
$totalCourses = mysqli_fetch_assoc($totalCoursesQuery)['total'];

// ACTIVE COURSES
$activeCoursesQuery = mysqli_query($conn, "SELECT COUNT(*) AS active FROM courses WHERE isActive = 1");
$activeCourses = mysqli_fetch_assoc($activeCoursesQuery)['active'];

// TOTAL FEEDBACK
$totalFeedbackQuery = mysqli_query($conn, "SELECT COUNT(*) AS totalFeedback FROM feedback");
$totalFeedback = mysqli_fetch_assoc($totalFeedbackQuery)['totalFeedback'];

// IF YOU WANT TO COUNT "UNREAD"
// (Only works if you add a `status` column later)
$unreadFeedback = 0; // default for now
?>


<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webstar | Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="../shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="../shared/assets/css/sidebar-and-container-styles.css">
    <link rel="stylesheet" href="../shared/assets/css/profIndex.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
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


                    <div class="container-fluid py-1 overflow-y-auto">
                        <div class="row">
                            <div class="col-12 col-md-7 mb-3 mb-md-0">
                                <div class="row ps-4">
                                    <div class="d-flex flex-column flex-sm-row align-items-center justify-content-between w-100 text-center text-sm-start"
                                        style="position: relative;">
                                        <div class="d-flex align-items-center mb-3 mb-sm-0">
                                            <!-- Image hidden on mobile -->
                                            <img src="../shared/assets/img/settings.png" alt="Folder"
                                                class="img-fluid rounded-circle me-3 folder-img d-none d-sm-block"
                                                style="width:68px; height:68px;">
                                            <div class="text-truncate w-100">
                                                <div class="text-sbold text-22">Welcome back, Administrator!</div>
                                                <div class="text-reg text-16">Continue managing and monitoring the
                                                    platform.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Stats Section -->
                            <div class="row stats mt-5 align-items-center">
                                <div class="col-12 col-md-3 mb-3">
                                    <div class="d-flex align-items-center">
                                        <span class="material-symbols-outlined me-2" style="font-size: 30px;">
                                            supervisor_account
                                        </span>
                                        <div class="stats-count text-22 text-bold"><?php echo $totalUsers; ?></div>

                                    </div>
                                    <div class="stats-label text-18 text-sbold">total users</div>
                                    <div class="text-reg text-16">
                                        <?php echo $totalProfessors; ?> instructors;
                                        <?php echo $totalStudents; ?> students
                                    </div>

                                </div>

                                <div class="col-12 col-md-3 mb-3">
                                    <div class="d-flex align-items-center">
                                        <span class="material-symbols-outlined me-2" style="font-size: 30px;">
                                            folder
                                        </span>
                                        <div class="stats-count text-22 text-bold">
                                            <?php echo $activeCourses; ?>
                                        </div>

                                    </div>
                                    <div class="stats-label text-18 text-sbold">active courses</div>
                                    <div class="text-reg text-16">
                                        <?php echo $totalCourses; ?> courses created all-time
                                    </div>

                                </div>

                                <div class="col-12 col-md-3 mb-3">
                                    <div class="d-flex align-items-center">
                                        <span class="material-symbols-outlined me-2" style="font-size: 30px;">
                                            feedback
                                        </span>
                                        <div class="stats-count text-22 text-bold">
                                            <?php echo $totalFeedback; ?>
                                        </div>

                                    </div>
                                    <div class="stats-label text-18 text-sbold">feedbacks</div>
                                    <div class="text-reg text-16">
                                        <?php echo $unreadFeedback; ?> unread feedbacks
                                    </div>

                                </div>

                                <div class="col-12 col-md-3 mb-3">
                                    <div class="d-flex align-items-center">
                                        <span class="material-symbols-outlined me-2" style="font-size: 30px;">
                                            assignment
                                        </span>
                                        <div class="stats-count text-22 text-bold">5,000</div>
                                    </div>
                                    <div class="stats-label text-18 text-sbold">website visits</div>
                                    <div class="text-reg text-16">500 visits this month</div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="card shadow-sm p-3"
                                        style="background: transparent; border: none; height: auto;">

                                        <div style="position: relative; height: 400px; width: 100%;">
                                            <canvas id="visitsChart"></canvas>
                                        </div>

                                        <div class="text-center mt-4">
                                            <span class="text-medium text-18">Website visits per day</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('visitsChart').getContext('2d');

        function getBarThickness() {
            if (window.innerWidth <= 576) return 40;
            if (window.innerWidth <= 768) return 70;
            return 120;
        }

        const visitsChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Oct 15', 'Oct 16', 'Oct 17', 'Oct 18', 'Oct 19', 'Oct 21'],
                datasets: [{
                    label: 'Visits',
                    data: [55, 65, 75, 78, 70, 35],
                    backgroundColor: 'rgba(90, 120, 255, 0.8)',
                    borderColor: 'rgba(90, 120, 255, 1)',
                    borderWidth: 1,
                    borderRadius: 2,
                    barThickness: getBarThickness(), // dynamic based on screen size
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        min: 0,
                        max: 100,
                        ticks: {
                            stepSize: 20,
                            color: '#555',
                            font: {
                                size: 13
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.08)',
                            drawBorder: false
                        }
                    },
                    x: {
                        ticks: {
                            color: '#555',
                            font: {
                                size: 13
                            }
                        },
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.75)',
                        titleFont: {
                            size: 14
                        },
                        bodyFont: {
                            size: 13
                        }
                    }
                }
            }
        });

        //  Dynamically on screen resize
        window.addEventListener('resize', () => {
            const newThickness = getBarThickness();
            if (visitsChart.data.datasets[0].barThickness !== newThickness) {
                visitsChart.data.datasets[0].barThickness = newThickness;
                visitsChart.update();
            }
        });
    </script>

</body>


</html>