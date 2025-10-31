
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webstar | Assess Exam Analytics</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="../shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="../shared/assets/css/sidebar-and-container-styles.css">
    <link rel="stylesheet" href="../shared/assets/css/profIndex.css">
    <link rel="stylesheet" href="../shared/assets/css/assess-exam-analytics.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="icon" type="image/png" href="../shared/assets/img/webstar-icon.png">

</head>

<body>
    <div class="container-fluid min-vh-100 d-flex justify-content-center align-items-center p-0 p-md-3"
        style="background-color: var(--black);">

        <div class="row w-100">

            <!-- Sidebar (mobile) -->
            <?php include '../shared/components/prof-sidebar-for-mobile.php'; ?>

            <!-- Sidebar (desktop) -->
            <?php include '../shared/components/prof-sidebar-for-desktop.php'; ?>

            <!-- Main Container -->
            <div class="col main-container m-0 p-0 mx-0 mx-md-2 p-md-4 overflow-y-auto">
                <div class="card border-0 px-3 pt-3 m-0 h-100 w-100 rounded-0 shadow-none"
                    style="background-color: transparent;">

                    <!-- Navbar (mobile) -->
                    <?php include '../shared/components/prof-navbar-for-mobile.php'; ?>

                    <!-- Fixed Header -->
                    <div class="row mb-3">
                        <div class="col-12 cardHeader p-3 mb-4">

                            <!-- DESKTOP VIEW -->
                            <div class="row desktop-header d-none d-sm-flex">
                                <div class="col-auto me-2">
                                    <a href="#" class="text-decoration-none">
                                        <i class="fa-solid fa-arrow-left text-reg text-16"
                                            style="color: var(--black);"></i>
                                    </a>
                                </div>
                                <div class="col">
                                    <span class="text-sbold text-25">Quiz #1</span>
                                    <div class="text-reg text-18">Active · Due Sept 9</div>
                                </div>
                            </div>


                            <!-- MOBILE VIEW -->
                            <div class="d-block d-sm-none mobile-assignment">
                                <div class="mobile-top">
                                    <div class="arrow">
                                        <a href="#" class="text-decoration-none">
                                            <i class="fa-solid fa-arrow-left text-reg text-16"
                                                style="color: var(--black);"></i>
                                        </a>
                                    </div>
                                    <div class="col">
                                        <span class="text-sbold text-25">Quiz #1</span>
                                        <div class="text-reg text-18">Active · Due Sept 9</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Scrollable Content Container -->
                        <div class="content-scroll-container">
                            <div class="container-fluid py-3">
                                <div class="row">
                                    <!-- Left Content -->
                                    <div class="col-12 col-lg-8">
                                        <div class="p-0 px-lg-5">
                                            <div class="tab-carousel-wrapper d-block"
                                                style="--tabs-right-extend: 60px;">
                                                <div class="tab-scroll">
                                                    <ul class="nav nav-tabs custom-nav-tabs mb-3 flex-nowrap" id="myTab"
                                                        role="tablist">

                                                        <li class="nav-item">
                                                            <a class="nav-link" href="assess-exam-details.php">Exam
                                                                Details</a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a class="nav-link"
                                                                href="assess-exam-submissions.php">Submissions</a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a class="nav-link active" id="announcements-tab"
                                                                data-bs-toggle="tab" href="#announcements"
                                                                role="tab">Analytics</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>

                                            <!-- Tab Content -->
                                            <div class="tab-content" id="myTabContent">
                                                <!-- Task Details Tab - Disabled -->
                                                <div class="tab-pane fade" id="announcements" role="tabpanel"
                                                    aria-labelledby="announcements-tab">
                                                    <!-- Empty content - tab is disabled -->
                                                </div>

                                                <!-- Analytics Summary (replaces submissions list) -->
                                                <div class="analytics-summary mt-5">
                                                    <div class="text-center mb-3">
                                                        <div class="mb-2">
                                                            <span class="material-symbols-outlined"
                                                                style="vertical-align: middle;">analytics</span>
                                                            <span class="analytics-score-value"
                                                                style="margin-left: 6px;">90%</span>
                                                        </div>
                                                        <div class="analytics-score-label" style="margin-top: -6px;">
                                                            average score</div>
                                                        <div class="analytics-score-desc mt-3"
                                                            style="max-width: 520px; margin: 0 auto;">
                                                            Most students scored nearly the same,<br>
                                                            showing balanced understanding.
                                                        </div>
                                                    </div>

                                                    <div class="row g-4 g-md-5 mt-2">
                                                        <div class="col-12 col-md-6">
                                                            <div class="analytics-metric text-center">
                                                                <div class="mb-2">
                                                                    <span class="material-symbols-outlined"
                                                                        style="vertical-align: middle;">
                                                                        check_circle
                                                                    </span>
                                                                    <span class="analytics-score-value"
                                                                        style=" margin-left: 8px;">100%</span>
                                                                </div>
                                                                <div class="analytics-score-label">passing rate</div>
                                                                <div class="analytics-score-desc mt-3">
                                                                    who met the passing score</div>
                                                            </div>
                                                        </div>

                                                        <div class="col-12 col-md-6">
                                                            <div class="analytics-metric text-center">
                                                                <div class="mb-2">
                                                                    <span class="material-symbols-outlined alarm-icon"
                                                                        style="vertical-align: middle;">
                                                                        alarm
                                                                    </span>
                                                                    <span class="analytics-score-value"
                                                                        style=" margin-left: 8px;">10 mins</span>
                                                                </div>
                                                                <div class="analytics-score-label">average time spent
                                                                </div>
                                                                <div class="analytics-score-desc mt-3">
                                                                    typical completion time</div>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-md-6">
                                                            <div class="analytics-metric text-center">
                                                                <div class="mb-2">
                                                                    <span class="material-symbols-outlined" style="vertical-align: middle;">
                                                                        trending_up
                                                                    </span>
                                                                    <span class="analytics-score-value"
                                                                        style=" margin-left: 8px;"> 50 </span>
                                                                </div>
                                                                <div class="analytics-score-label"> highest score
                                                                </div>
                                                                <div class="analytics-score-desc mt-3">
                                                                    by Christian James Torrillo</div>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-md-6">
                                                        <div class="analytics-metric text-center">
                                                        <div class="mb-2">
                                                                    <span class="material-symbols-outlined"
                                                                        style="vertical-align: middle;">trending_down</span>
                                                                        <span class="analytics-score-value"
                                                                        style=" margin-left: 8px;"> 10 </span>
                                                                </div>
                                                                <div class="analytics-score-label"> lowest score
                                                                </div>
                                                                <div class="analytics-score-desc mt-3">
                                                                    by Ariana Grande</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-lg-4 ms-lg-auto">
                                        <div class="cardSticky position-sticky" style="top: 20px;">
                                            <div class="p-2">
                                                <div class="exam-details-title mb-5">Exam Details</div>
                                                <div class="text-center mb-3">
                                                    <div class="exam-stat-value mb-2">50</div>
                                                    <div class="exam-stat-label mb-5">Total Exam Items</div>

                                                    <div class="exam-stat-value mb-2">50</div>
                                                    <div class="exam-stat-label mb-5">Total Exam Points</div>

                                                    <div class="exam-stat-value mb-2">50</div>
                                                    <div class="exam-stat-label">Exam Duration</div>
                                                </div>

                                                <div class="row align-items-center justify-content-center mt-5 mb-3">
                                                    <div class="col-auto">
                                                        <div class="chart-container"
                                                            style="width: 100px; height: 100px;">
                                                            <canvas id="taskChart" width="100" height="100"></canvas>
                                                        </div>
                                                    </div>
                                                    <div class="col-auto">
                                                        <!-- Submission Stats -->
                                                        <div class="submission-stats">
                                                            <div class="text-reg text-14"><span
                                                                    class="stat-value">10</span>
                                                                submitted</div>
                                                            <div class="text-reg text-14"><span
                                                                    class="stat-value">11</span>
                                                                pending submission</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Full Width Score Range Overview Chart -->
                                <div class="row chart-section">
                                    <div class="col-12">
                                        <!-- Chart Title (outside chart container) -->
                                        <div class="chart-title mb-3">
                                            <h5 class="text-sbold text-18 mb-1">Score Range Overview</h5>
                                            <p class="text-reg text-14 text-muted">Displays the number of students in each score bracket</p>
                                        </div>
                                        
                                        <!-- Chart Container -->
                                        <div class="score-range-chart-full">
                                            <div class="chart-container-full">
                                                <canvas id="scoreRangeChartFull" width="800" height="200"></canvas>
                                            </div>
                                        </div>
                                        
                                        <!-- Item Analysis section (content before badges) -->
                                        <div class="item-analysis mt-4">
                                            <div class="section-title">Item Analysis (Per Question)</div>
                                            <div class="section-desc">Performance insights per question to spot unclear or overly difficult items.</div>
                                            <hr class="item-divider mt-4 mb-4">

                                            <!-- Example Question 1 -->
                                            <div class="question-block">
                                                <div class="question-text mb-2 ">Which HTML tag is used to define the largest heading?</div>
                                                <span class="choice-ok">90% answered correctly</span>
                                                <div class="chart-badges mt-3">
                                                    <span class="metric-badge badge-blue">90% answered correctly</span>
                                                    <span class="metric-badge badge-green">10 students got it right</span>
                                                    <span class="metric-badge badge-red">10 students got it wrong</span>
                                                </div>
                                            </div>
                                            <hr class="item-divider mt-4 mb 4">

                                            <!-- Example Question 2 with choices -->
                                            <div class="question-block mt-4">
                                                <div class="question-text">Which HTML tag is used to define the largest heading?</div>
                                                <div class="chart-badges mt-4 mb-4">
                                                    <span class="metric-badge badge-blue">90% answered correctly</span>
                                                    <span class="metric-badge badge-green">10 students got it right</span>
                                                    <span class="metric-badge badge-red">10 students got it wrong</span>
                                                </div>
                                                <ul class="choice-list mt-3">
                                                    <li>Choice A - 10 students - 25%</li>
                                                    <li>Choice B - 10 students - 25%</li>
                                                    <li class="choice-ok">Choice C - 10 students - 25%</li>
                                                    <li>Choice D - 10 students - 25%</li>
                                                </ul>
                                            </div>
                                            <hr class="item-divider">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> <!-- Close content-scroll-container -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function createDoughnutChart(canvasId, submitted, pending, graded) {
            const ctx = document.getElementById(canvasId).getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [submitted, pending, graded],
                        backgroundColor: ['#3DA8FF', '#C7C7C7', '#E0E0E0'],
                        borderWidth: 0,
                    }]
                },
                options: {
                    cutout: '75%',
                    plugins: {
                        legend: { display: false },
                        tooltip: { enabled: false }
                    }
                }
            });
        }

        createDoughnutChart('taskChart', 10, 11, 0);

        // Full Width Score Range Overview Chart
        function createScoreRangeChartFull() {
            const ctx = document.getElementById('scoreRangeChartFull').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['0-10', '11-20', '21-30', '31-40', '41-50', '51-60'],
                    datasets: [{
                        label: 'Number of Students',
                        data: [57, 64, 77, 79, 70, 37],
                        backgroundColor: '#8DA9F7',
                        borderColor: '#8DA9F7',
                        borderWidth: 0,
                        borderRadius: 4,
                        borderSkipped: false,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            borderColor: '#8DA9F7',
                            borderWidth: 1,
                            cornerRadius: 6,
                            displayColors: false,
                            callbacks: {
                                title: function(context) {
                                    return 'Score Range: ' + context[0].label;
                                },
                                label: function(context) {
                                    return 'Students: ' + context.parsed.y;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                stepSize: 20,
                                color: '#666',
                                font: {
                                    family: 'Regular, sans-serif',
                                    size: 12
                                }
                            },
                            grid: {
                                color: '#E0E0E0',
                                lineWidth: 1,
                                drawBorder: false,
                                drawTicks: false
                            },
                            border: {
                                display: false
                            }
                        },
                        x: {
                            ticks: {
                                color: '#666',
                                font: {
                                    family: 'Regular, sans-serif',
                                    size: 12
                                }
                            },
                            grid: {
                                display: false
                            },
                            border: {
                                display: false
                            }
                        }
                    },
                    layout: {
                        padding: {
                            top: 20,
                            bottom: 10,
                            left: 10,
                            right: 10
                        }
                    }
                }
            });
        }

        // Initialize the full width score range chart
        createScoreRangeChartFull();
    </script>
</body>

</html>