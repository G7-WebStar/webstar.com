<?php $activePage = 'dashboard'; ?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webstar | Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="shared/assets/css/sidebar-and-container-styles.css">
    <link rel="stylesheet" href="shared/assets/css/dashboard.css">
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
                        <div class="row g-3">
                            <div class="col-lg-4 col-md-6 col-sm-12">
                                <div class="card shadow-sm p-3 text-center" style="border-radius: 25px; height: 150px; border-color: var(--blue);">
                                    <div class="mb-1 pt-2" style="font-size: 1.2rem; color: var(--blue); font-family: var(--Medium);">No. of Users</div>
                                    <hr class="my-2" style="border-top: 2px solid var(--blue);">
                                    <div style="font-size: 2.2rem; color: var(--blue); font-family: var(--Bold);">567</div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-12">
                                <div class="card shadow-sm p-3 text-center" style="border-radius: 25px; height: 150px; border-color: var(--blue);">
                                    <div class="mb-1 pt-2" style="font-size: 1.1rem; color: var(--blue); font-family: var(--Medium);">No. of New Users this month</div>
                                    <hr class="my-2" style="border-top: 2px solid var(--blue);">
                                    <div style="font-size: 2.2rem; color: var(--blue); font-family: var(--Bold);">25</div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-12">
                                <div class="card shadow-sm p-3 text-center" style="border-radius: 25px; height: 150px; border-color: var(--blue);">
                                    <div class="mb-1 pt-2" style="font-size: 1.2rem; color: var(--blue); font-family: var(--Medium);">No. of Daily Visits</div>
                                    <hr class="my-2" style="border-top: 2px solid var(--blue);">
                                    <div style="font-size: 2.2rem; color: var(--blue); font-family: var(--Bold);">95</div>
                                </div>
                            </div>
                        </div>

                        <!-- Daily active user -->
                        <div class="row mt-4 ">
                            <div class="col-12">
                                <div class="card p-4" style="border-radius: 25px; border-color: var(--blue);">
                                    <div class="mb-3" style="font-size: 1.1rem; color: var(--blue); font-family: var(--Bold);">Daily Active Users this Week</div>
                                    <hr class="mt-0 mb-3" style="border-top: 2px solid var(--blue);">
                                    <div style="height: 230px; overflow-x: auto;">
                                        <canvas id="dailyUsersChart" style="height: 100%; min-width: 250px;"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Current course -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card p-4" style="border-radius: 25px; border-color: var(--blue);">
                                    <div class="mb-3" style="font-size: 1.1rem; color: var(--blue); font-family: var(--Bold);">Currently takers per Course</div>
                                    <hr class="mt-0 mb-3" style="border-top: 2px solid var(--blue);">
                                    <div style="height: 230px; overflow-x: auto;">
                                        <canvas id="horizontalUsersChart" style="height: 100%; min-width: 200px;"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end of div -->
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Chart js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('dailyUsersChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Daily Active Users',
                    data: [12, 25, 65, 45, 85, 18, 20],
                    backgroundColor: '#2A63E2'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        ticks: {
                            color: '#2A63E2'
                        },
                        grid: {
                            color: '#2A63E2'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        min: 0,
                        max: 100,
                        ticks: {
                            stepSize: 5,
                            color: '#2A63E2'
                        },
                        grid: {
                            color: '#2A63E2'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // Course Takers
        const ctx2 = document.getElementById('horizontalUsersChart').getContext('2d');
        new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: ['Web Design', 'App Dev', 'HTML'],
                datasets: [{
                    label: 'Currently Takers',
                    data: [80, 55, 40],
                    backgroundColor: '#2A63E2'
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        beginAtZero: true,
                        min: 0,
                        max: 100,
                        ticks: {
                            stepSize: 20,
                            color: '#2A63E2'
                        },
                        grid: {
                            color: '#2A63E2'
                        }
                    },
                    y: {
                        ticks: {
                            color: '#2A63E2'
                        },
                        grid: {
                            color: '#2A63E2'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    </script>

</body>


</html>