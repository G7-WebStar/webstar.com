<?php
include('shared/assets/database/connect.php');
include("shared/assets/processes/session-process.php");

$userID = $_SESSION['userID'] ?? 2;

// ---  Get Assessments (Deadlines)
$assessmentsQuery = "
SELECT a.assessmentTitle, a.deadline, c.courseCode
FROM assessments a
JOIN courses c ON a.courseID = c.courseID
JOIN enrollments e ON c.courseID = e.courseID
WHERE e.userID = ? AND c.isActive = 1
";
$stmt1 = $conn->prepare($assessmentsQuery);
$stmt1->bind_param('i', $userID);
$stmt1->execute();
$assessmentsResult = $stmt1->get_result();
$assessments = $assessmentsResult->fetch_all(MYSQLI_ASSOC);

// ---  Get Course Schedules
$schedulesQuery = "
SELECT cs.day, cs.startTime, cs.endTime, c.courseTitle
FROM courseschedule cs
JOIN courses c ON cs.courseID = c.courseID
JOIN enrollments e ON c.courseID = e.courseID
WHERE e.userID = ? AND c.isActive = 1
";
$stmt2 = $conn->prepare($schedulesQuery);
$stmt2->bind_param('i', $userID);
$stmt2->execute();
$schedulesResult = $stmt2->get_result();
$schedules = $schedulesResult->fetch_all(MYSQLI_ASSOC);

// ---  Build Events
$events = [];

foreach ($assessments as $a) {
    $events[] = [
        'title' => $a['assessmentTitle'] . " · " . $a['courseCode'],
        'start' => $a['deadline'],
        'backgroundColor' => 'var(--primaryColor)', // Blueish background for schedules
        'borderColor' => 'var(--primaryColor)',
        'textColor' => 'var(--black)',

    ];
}

$dayMap = [
    'Sunday' => 0,
    'Monday' => 1,
    'Tuesday' => 2,
    'Wednesday' => 3,
    'Thursday' => 4,
    'Friday' => 5,
    'Saturday' => 6
];

foreach ($schedules as $s) {
    $events[] = [
        'title' => $s['courseTitle'] . ' ' . $s['startTime'] . '-' . $s['endTime'], // show time in title
        'daysOfWeek' => [$dayMap[$s['day']]], // keep recurring
        'allDay' => true, // makes it a pill instead of a dot
        'backgroundColor' => 'var(--primaryColor)',
        'borderColor' => 'var(--primaryColor)',
        'textColor' => 'var(--black)'
    ];
}

$eventsJson = json_encode($events);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Calendar ✦ Webstar</title>
    <link rel="icon" type="image/png" href="shared/assets/img/webstar-icon.png">


    <!-- FullCalendar -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>

    <!-- jQuery + Moment -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <!-- Global Styles -->
    <link rel="stylesheet" href="shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="shared/assets/css/sidebar-and-container-styles.css">
    <link rel="stylesheet" href="shared/assets/css/calendar.css">

    <style>
        body {
            font-family: var(--Medium) !important;
            letter-spacing: -0.03em !important;
        }

        a {
            text-decoration: none !important;
            color: var(--black) !important;
        }

        #kt_docs_fullcalendar_basic {
            width: 100%;
            max-width: 100%;
            margin: 0 auto;
            min-height: 500px;
        }

        .fc-toolbar-title {
            font-family: var(--SemiBold) !important;
            letter-spacing: -0.03em !important;
        }

        .fc-scroller {
            overflow: hidden !important;
        }

        /* Make buttons smaller on mobile */
        @media (max-width: 768px) {
            .fc .fc-toolbar.fc-header-toolbar {
                flex-wrap: wrap;
                gap: 5px;
            }

            .fc-toolbar-title {
                font-size: 1.2rem !important;
            }
        }

        .fc-event-title,
        .fc-event-time {
            color: var(--black) !important;
        }

        /* Shrink day cells on mobile for better fit */
        @media (max-width: 500px) {
            .fc-daygrid-day-number {
                font-size: 0.8rem;
            }

            .fc-daygrid-event {
                font-size: 0.75rem;
                padding: 2px 4px;
            }
        }

        /* FullCalendar arrow buttons */
        .fc .fc-prev-button,
        .fc .fc-next-button {
            background-color: transparent;
            /* pastel blue background */
            color: var(---black);
            /* arrow color */
            border-radius: 8px;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.2s ease;
        }

        /* Remove all hover, focus, active effects */
        .fc .fc-prev-button:hover,
        .fc .fc-next-button:hover,
        .fc .fc-today-button:hover,
        .fc .fc-prev-button:focus,
        .fc .fc-next-button:focus,
        .fc .fc-today-button:focus,
        .fc .fc-prev-button:active,
        .fc .fc-next-button:active,
        .fc .fc-today-button:active {
            background-color: transparent !important;
            color: var(--black) !important;
            box-shadow: none !important;
            outline: none !important;
            transform: none !important;
            /* remove click “shift” */
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

            <!-- Main Container Column-->
            <div class="col main-container m-0 p-0 mx-0 mx-md-2 p-0 p-md-4 overflow-y-auto">
                <div class="card border-0 px-3 pt-3 m-0 h-100 w-100 rounded-0 shadow-none"
                    style="background-color: transparent;">

                    <!-- Navbar for mobile -->
                    <?php include 'shared/components/navbar-for-mobile.php'; ?>
                    <div class="container-fluid overflow-y-auto row-padding-top" style="position: relative;">
                        <div class="row g-0 w-100 mt-4 mt-md-0">
                            <div id="kt_docs_fullcalendar_basic"></div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>


    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const calendarEl = document.getElementById('kt_docs_fullcalendar_basic');

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                events: <?php echo $eventsJson; ?>,
                // Format event time as AM/PM
                eventTimeFormat: {
                    hour: 'numeric',
                    minute: '2-digit',
                    hour12: true // <-- this makes it AM/PM
                },
                headerToolbar: {
                    left: 'prev',      // Previous button on the left
                    center: 'title',   // Month title in center
                    right: 'next'      // Next button on the right
                },
                initialView: 'dayGridMonth', // Show monthly view
                // Show all events without collapsing into "+N more"
                dayMaxEventRows: true, // allows multiple rows of events
                dayMaxEvents: false,   // prevents the "+N more" collapse

                // Optional styling
                height: "auto",

                eventDidMount: function (info) {
                    // Make event cursor a pointer
                    info.el.style.cursor = 'pointer';

                    // Remove default title tooltip
                    info.el.removeAttribute('title');

                    info.el.addEventListener('click', function (e) {
                        e.stopPropagation();

                        // Remove existing tooltips
                        document.querySelectorAll('.fc-tooltip').forEach(t => t.remove());

                        // Create custom tooltip
                        const tooltip = document.createElement('div');
                        tooltip.className = 'fc-tooltip';
                        tooltip.textContent = info.event.title;

                        // Tooltip styling
                        Object.assign(tooltip.style, {
                            position: 'absolute',
                            backgroundColor: 'var(--pureWhite)',
                            color: 'var(--black)',
                            border: '1px solid var(--black)',
                            padding: '8px 5px',
                            borderRadius: '5px',
                            boxShadow: '0 2px 8px rgba(0,0,0,0.15)',
                            fontSize: '14px',
                            zIndex: 9999,
                            whiteSpace: 'nowrap',
                            pointerEvents: 'none',
                            transition: 'opacity 0.2s',
                            opacity: 0
                        });

                        document.body.appendChild(tooltip);

                        const rect = info.el.getBoundingClientRect();
                        const scrollTop = window.scrollY;
                        const scrollLeft = window.scrollX;

                        // Position above the event
                        let top = rect.top + scrollTop - tooltip.offsetHeight - 8;
                        let left = rect.left + scrollLeft + rect.width / 2 - tooltip.offsetWidth / 2;

                        // Flip below if near top
                        if (top < scrollTop + 10) {
                            top = rect.bottom + scrollTop + 8;
                        }

                        // Prevent overflow
                        if (left + tooltip.offsetWidth > scrollLeft + window.innerWidth - 10) {
                            left = scrollLeft + window.innerWidth - tooltip.offsetWidth - 10;
                        }
                        if (left < scrollLeft + 10) {
                            left = scrollLeft + 10;
                        }

                        tooltip.style.top = top + 'px';
                        tooltip.style.left = left + 'px';
                        tooltip.style.opacity = 1;

                        // Remove tooltip on next click anywhere
                        const removeTooltip = () => {
                            tooltip.remove();
                            document.removeEventListener('click', removeTooltip);
                        };
                        setTimeout(() => {
                            document.addEventListener('click', removeTooltip);
                        }, 0);
                    });
                }
            });
            calendar.render();

            // Force FullCalendar to recalc sizes after a small delay
            setTimeout(() => {
                calendar.updateSize();
            }, 100);
        });

    </script>

</body>

</html>