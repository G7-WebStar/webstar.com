<?php 
$activePage = 'manage'; 
include('../shared/assets/database/connect.php');
include("../shared/assets/processes/admin-session-process.php");

// Get userID from URL
if (isset($_GET['userID'])) {
    $userID = $_GET['userID'];

    // Fetch user info and status only if role is 'professor'
    $sql = "SELECT users.status, users.role, userinfo.firstName, userinfo.middleName, userinfo.lastName
            FROM users
            INNER JOIN userinfo ON users.userID = userinfo.userID
            WHERE users.userID = '$userID' AND users.role = 'professor'";

    $result = executeQuery($sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $firstName = $user['firstName'];
        $middleName = $user['middleName'];
        $lastName = $user['lastName'];
        $status = $user['status'];
    } else {
        echo "User not found or is not a professor.";
        exit;
    }
} else {
    echo "No userID provided.";
    exit;
}

// Handle form submission
if (isset($_POST['updateBtn'])) {
    $firstName = $_POST['firstName'];
    $middleName = $_POST['middleName'];
    $lastName = $_POST['lastName'];
    $status = $_POST['status'];

    // Update userinfo table
    $updateInfo = "UPDATE userinfo 
             SET firstName='$firstName', middleName='$middleName', lastName='$lastName'
             WHERE userID='$userID'";
    executeQuery($updateInfo);

    // Update users table
    $updateStatus = "UPDATE users 
             SET status='$status'
             WHERE userID='$userID'";
    executeQuery($updateStatus);

    // Redirect to manage-instructor.php
    header("Location: manage.php");
    exit();
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webstar | Edit </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="../shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="../shared/assets/css/sidebar-and-container-styles.css">
    <link rel="stylesheet" href="../shared/assets/css/course.css">
    <link rel="stylesheet" href="../shared/assets/css/admin.css">
    <link rel="stylesheet" href="../shared/assets/css/registration.css">
    <link rel="stylesheet" href="../shared/assets/css/registration-profile.css">
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
                            <div class="col-12 mb-1">
                                <div class="d-flex align-items-center ps-1">
                                    <a href="manage.php" style="text-decoration: none; color: inherit; line-height: 1;">
                                        <span class="material-symbols-outlined me-2">
                                            arrow_left_alt
                                        </span>
                                    </a>
                                    <div class="text-sbold text-22 ms-1">Edit</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Registration Form -->
                    <div class="container p-4 d-flex">
                        <form method="POST" action="" class="w-100 custom-form" id="registration-instructor-form">
                            <div class="row" style="max-width: 900px;">

                                <!-- Left: Registration Form -->
                                <div class="col-12 col-md-6">
                                    <div>
                                        <div class="mb-3 text-med text-16 text-center text-md-start"
                                            style="color: var(--black);">
                                            Basic Information
                                        </div>

                                        <!-- First & Middle Name -->
                                        <div class="row mb-3 gx-3">
                                            <div class="col">
                                                <div class="form-floating">
                                                    <input type="text" name="firstName" class="form-control"
                                                        id="firstName" placeholder=" "
                                                        value="<?php echo $firstName; ?>">
                                                    <label for="firstName">First Name</label>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="form-floating">
                                                    <input type="text" name="middleName" class="form-control"
                                                        id="middleName" placeholder=" "
                                                        value="<?php echo $middleName; ?>">
                                                    <label for="middleName">Middle Name</label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Last Name -->
                                        <div class="row mb-4 gx-3">
                                            <div class="col">
                                                <div class="form-floating">
                                                    <input type="text" name="lastName" class="form-control"
                                                        id="lastName" placeholder="Last Name"
                                                        value="<?php echo $lastName; ?>">
                                                    <label for="lastName">Last Name</label>
                                                </div>
                                            </div>
                                            <div class="col form-floating">
                                                <select class="form-select p-1 ps-3" name="status" id="status"
                                                    style="width: 130px; height: 48px; line-height: 48px;">
                                                    <option value="Active" <?php if ($status == 1)
                                                        echo 'selected'; ?>>Active
                                                    </option>
                                                    <option value="Inactive" <?php if ($status == 0)
                                                        echo 'selected'; ?>>Inactive
                                                    </option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Update Button  -->
                                        <div class="text-center d-flex flex-column align-items-end justify-content-end">
                                            <button type="submit" name="updateBtn"
                                                class="text-sbold text-12 btn btn-finish mt-3 mb-3">
                                                Update
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

</body>


</html>