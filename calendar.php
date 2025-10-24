<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Calendar Demo</title>

    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Moment.js -->
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>

    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">

    <!-- Icons and Fonts -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" type="image/png" href="shared/assets/img/webstar-icon.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,1,0"
        rel="stylesheet" />

    <!-- Global and Layout Styles -->
    <link rel="stylesheet" href="shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="shared/assets/css/sidebar-and-container-styles.css">
    <link rel="stylesheet" href="shared/assets/css/profile.css">

    <style>
        body {
            font-family: var(--Medium) !important;

        }

        #kt_docs_fullcalendar_basic {
            width: 100%;
            max-width: 100%;
            /* remove fixed limit */
            margin: 0 auto;
            min-height: 500px;
        }
        
    </style>
</head>

<body>
    <div class="container-fluid min-vh-100 d-flex justify-content-center align-items-center p-0 p-md-3"
        style="background-color: var(--black);">

        <div class="row w-100">

            <!-- Sidebar (only shows on mobile) -->
            <?php include 'shared/components/sidebar-for-mobile.php'; ?>

            <!-- Sidebar Column (fixed on desktop) -->
            <?php include 'shared/components/sidebar-for-desktop.php'; ?>

            <!-- Main Container Column -->
            <div class="col main-container m-0 p-0 mx-0 mx-md-2 p-0 p-md-4 overflow-y-auto">
                <div class="card border-0 px-3 pt-3 m-0 h-100 w-100 rounded-0 shadow-none"
                    style="background-color: transparent;">

                    <!-- Navbar for mobile -->
                    <nav class="navbar navbar-light px-3 d-md-none">
                        <div class="container-fluid position-relative">
                            <!-- Toggler -->
                            <button class="navbar-toggler position-absolute start-0 p-1" type="button"
                                data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas">
                                <span class="navbar-toggler-icon"></span>
                            </button>

                            <!-- Logo -->
                            <a class="navbar-brand mx-auto" href="#">
                                <img src="shared/assets/img/webstar-logo-black.png" alt="Webstar"
                                    style="height: 40px; padding-left: 30px">
                            </a>
                        </div>
                    </nav>

                    <div class="container-fluid py-3 overflow-y-auto" style="position: relative;">
                        <div class="row g-0 w-100">
                            <!-- CALENDAR -->
                            <div id="kt_docs_fullcalendar_basic"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        "use strict";

        var KTGeneralFullCalendarBasicDemos = function () {
            var exampleBasic = function () {
                var todayDate = moment().startOf('day');
                var YM = todayDate.format('YYYY-MM');
                var YESTERDAY = todayDate.clone().subtract(1, 'day').format('YYYY-MM-DD');
                var TODAY = todayDate.format('YYYY-MM-DD');
                var TOMORROW = todayDate.clone().add(1, 'day').format('YYYY-MM-DD');

                var calendarEl = document.getElementById('kt_docs_fullcalendar_basic');
                var calendar = new FullCalendar.Calendar(calendarEl, {
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
                    },
                    height: 800,
                    nowIndicator: true,
                    initialView: 'dayGridMonth',
                    initialDate: TODAY,
                    editable: true,
                    dayMaxEvents: true,
                    navLinks: true,
                    events: [
                        { title: 'All Day Event', start: YM + '-01', className: "fc-event-danger" },
                        { title: 'Meeting', start: TODAY + 'T10:30:00', end: TODAY + 'T12:30:00' },
                        { title: 'Lunch', start: TODAY + 'T12:00:00' },
                        { title: 'Birthday Party', start: TOMORROW + 'T07:00:00', className: "fc-event-primary" },
                        { title: 'Click for Google', url: 'https://google.com/', start: YM + '-28' }
                    ]
                });

                calendar.render();
            }

            return {
                init: function () {
                    exampleBasic();
                }
            };
        }();

        // Initialize after DOM ready
        document.addEventListener("DOMContentLoaded", function () {
            KTGeneralFullCalendarBasicDemos.init();
        });
    </script>

</body>

</html>