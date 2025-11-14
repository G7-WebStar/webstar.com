<?php $activePage = 'webstars';
include('shared/assets/database/connect.php');
include("shared/assets/processes/session-process.php");

// Get current month range
$currentMonthStart = date('Y-m-01');
$currentMonthEnd = date('Y-m-t');

// Fetch only this month's webstars history, latest first
$query = "
    SELECT webstarsID, sourceType, pointsChanged, dateEarned
    FROM webstars
    WHERE userID = $userID
    AND dateEarned BETWEEN '$currentMonthStart' AND '$currentMonthEnd'
    ORDER BY dateEarned DESC
";
$result = mysqli_query($conn, $query);

?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Transactions ✦ Webstar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="shared/assets/css/global-styles.css" />
    <link rel="stylesheet" href="shared/assets/css/sidebar-and-container-styles.css" />
    <link rel="stylesheet" href="shared/assets/css/inbox.css" />
    <link rel="stylesheet" href="shared/assets/css/sidebar-and-container-styles.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
    <link rel="icon" type="image/png" href="shared/assets/img/webstar-icon.png" />

    <!-- Material Design Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp" />
    <style>
        .webstars-table thead th {
            background-color: var(--primaryColor) !important;
            color: var(--black) !important;
        }

        .webstars-table,
        .webstars-table th,
        .webstars-table td {
            border: 1px solid var(--black) !important;
            border-collapse: collapse !important;
        }
    </style>

</head>

<body>
    <div class="container-fluid min-vh-100 d-flex justify-content-center align-items-center p-0 p-md-3"
        style="background-color: var(--black);">
        <div class="row w-100">
            <?php include 'shared/components/sidebar-for-mobile.php'; ?>
            <?php include 'shared/components/sidebar-for-desktop.php'; ?>

            <div class="col main-container m-0 p-0 mx-0 mx-md-2 p-md-4 overflow-y-auto">
                <div class="card border-0 px-3 pt-3 m-0 h-100 w-100 rounded-0 shadow-none"
                    style="background-color: transparent;">
                    <?php include 'shared/components/navbar-for-mobile.php'; ?>

                    <div class="container-fluid py-3 row-padding-top" >
                        <div class="row">
                            <div class="col-12"style="padding-bottom:50px">
                                <!-- Header Section -->
                                <div class="row align-items-center mb-3 text-center text-md-start">
                                    <div class="col-12 col-md-auto text-center text-md-start position-relative">
                                        <h1 class="text-sbold text-25 my-2" style="color: var(--black);">Monthly
                                            Webstars
                                            Transactions
                                        </h1>
                                    </div>

                                    <!-- Table -->
                                    <div class="message-container mt-3 pb-4">
                                        <div class="table-responsive">
                                            <table
                                                class="table table-bordered text-center align-middle text-reg text-14 mb-0 webstars-table">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Source</th>
                                                        <th>Points</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if (mysqli_num_rows($result) > 0): ?>
                                                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                                            <tr>
                                                                <td><?= htmlspecialchars(date('M d, Y', strtotime($row['dateEarned']))) ?>
                                                                </td>
                                                                <td><?= htmlspecialchars($row['sourceType']) ?></td>
                                                                <td>
                                                                    <strong
                                                                        style="color: <?= $row['pointsChanged'] >= 0 ? 'var(--successColor, #3b873e)' : 'var(--dangerColor, #b04a4a)' ?>;">
                                                                        <?= $row['pointsChanged'] >= 0 ? '+' : '' ?>
                                                                        <?= htmlspecialchars($row['pointsChanged']) ?>
                                                                    </strong>
                                                                </td>
                                                            </tr>
                                                        <?php endwhile; ?>
                                                    <?php else: ?>
                                                        <tr>
                                                            <td colspan="3" class="text-muted">No Webstars transactions this
                                                                month.</td>
                                                        </tr>
                                                    <?php endif; ?>
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
    </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>