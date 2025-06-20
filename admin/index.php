<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webstar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="shared/assets/css/sidebar-and-container-styles.css">
    <link rel="stylesheet" href="shared/assets/css/admin.css">
</head>

<body>
    <div class="container-fluid min-vh-100 d-flex justify-content-center align-items-center p-3">
        <div class="row w-100">
            <!-- Sidebar (only shows on mobile) -->
            <?php include 'shared/components/sidebar-for-mobile.php'; ?>

            <!-- Sidebar Column (fixed on desktop) -->
            <?php include 'shared/components/sidebar-for-desktop.php'; ?>

            <!-- Main Container Column-->
            <div class="col main-container m-0 p-0 mx-2 p-4 overflow-auto">
                <div class="card border-0 p-3 h-100 w-100 rounded-0 shadow-none">
                    <!-- Navbar for mobile -->
                    <?php include 'shared/components/navbar-for-mobile.php'; ?>

                    <!-- Main Container -->
                     <!-- No of users -->
                    <div class="container py-3">
                        <div class="row g-4 justify-content-center">
                            <div class="col-lg-4 col-md-6 col-sm-12">
                                <div class="cardTop blueCard">
                                    <div class="cardTitle pt-3 px-2">No. of Users</div>
                                    <div class="cardDivider"></div>
                                    <h1 class="cardNumber"></h1>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6 col-sm-12">
                                <div class="cardTop blueCard">
                                    <div class="cardTitle pt-3 px-2">No. of New Users this month</div>
                                    <div class="cardDivider"></div>
                                    <h1 class="cardNumber"></h1>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6 col-sm-12">
                                <div class="cardTop blueCard">
                                    <div class="cardTitle pt-3 px-2">No. of Total Visits</div>
                                    <div class="cardDivider"></div>
                                    <h1 class="cardNumber"></h1>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Daily Active Users -->
                    <div class="dailyActiveUserContainer pt-2">
                        <div class="dailyRow">
                            <div class="col-12">
                                <div class="dailyCard">
                                    <div class="card-body" style="height:320px;">
                                        <div class="dailyCardTitle">
                                            <div class="text-align-start px-4" style="font-size: 1.2rem; font-weight: bold; color: var(--blue);">Daily Active Users</div>
                                        </div>
                                        <div class="divider"></div>
                                        <div class="dailyCardText">
                                            <canvas id="dailyActiveUsers" style="width: 100%; height: auto; margin-left: 10px;"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Ranking of Course -->
                    <div class="rankingContainer">
                        <div class="dailyRow">
                            <div class="col-12">
                                <div class="rtCard">
                                    <div class="rtCardBody" style="height:320px;">
                                        <div class="rtCardTitle py-2">
                                            <div class="text-align-start px-5" style="font-size: 1.2rem; font-weight: bold; color: var(--blue);">Currently takers per Course</div>
                                        </div>
                                        <div class="divider"></div>
                                        <div class="rtCardText">
                                            <canvas id="rankingCourse"
                                                style="display: block; box-sizing: border-box; height: auto; width: 100%;"></canvas>
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
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            var chartLabels = <?php echo json_encode($chartLabels); ?>;
            var chartData = <?php echo json_encode($chartData); ?>;
        </script>
        <script src="../assets/js/chart.js"></script>

        <!-- JS Left column -->
        <script src="../assets/js/leftcolumn.js"></script>

        <script>
            const barDailyActiveUsers = document.getElementById('dailyActiveUsers');
            const ctxrankingCourse = document.getElementById('rankingCourse');

            const clicksLabels = [<?php echo '"' . implode('","', $clicksDate) . '"' ?>];
            const courseLabels = [<?php echo '"' . implode('","', $chartLabels) . '"' ?>];
    
            // Bar Chart: Daily Logins
            new Chart(barDailyActiveUsers, {
                type: 'bar',
                data: {
                    labels: clicksLabels,
                    datasets: [{
                        label: 'WebStar User Logins',
                        data: [<?php echo implode(',', $clicksCount) ?>],
                        backgroundColor: 'rgba(42, 99, 226, 100)',
                        borderColor: 'rgb(42, 99, 226, 100)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Horizontal Bar Chart: Course Rankings
            new Chart(ctxrankingCourse, {
                type: 'bar',
                data: {
                    labels: courseLabels,
                    datasets: [{
                        data: chartData,
                        backgroundColor: ['#FDB45C', '#46BFBD', '#949FB1', '#4D5360', '#FFC870'],
                        hoverBackgroundColor: ['#FFC870', '#5AD3D1', '#A8B3C5', '#616774', '#FFD980']
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true
                        }
                    }
                }
            });
        </script>
</body>

</html>