<?php $activePage = 'home';
include('shared/assets/database/connect.php');
include("shared/assets/processes/session-process.php");
$invalidCode = false;
$enrolled = false;

if (isset($_POST['access_code'])) {
    $code = $_POST['access_code'];

    $checkCourseQuery = "SELECT * FROM courses WHERE code = '$code'";
    $checkCourseResult = executeQuery($checkCourseQuery);

    if (mysqli_num_rows($checkCourseResult) > 0) {
        $availableCourses = mysqli_fetch_assoc($checkCourseResult);
        $courseID = $availableCourses['courseID'];

        $checkEnrollmentQuery = "SELECT * FROM enrollments WHERE userID = '$userID' AND courseID = '$courseID'";
        $checkEnrollmentResult = executeQuery($checkEnrollmentQuery);

        if (mysqli_num_rows($checkEnrollmentResult) > 0) {
            $enrolled = true;
        } else {
            $selectUserQuery = "SELECT yearSection FROM userinfo WHERE userID = '$userID'";
            $selectUserResult = executeQuery($selectUserQuery);

            if (mysqli_num_rows($selectUserResult) > 0) {
                $selectedUser = mysqli_fetch_assoc($selectUserResult);
                $yearSection = $selectedUser['yearSection'];

                $enrollQuery = "INSERT INTO enrollments (`userID`, `courseID`, `yearSection`) VALUES ('$userID','$courseID','$yearSection')";
                $enrollResult = executeQuery($enrollQuery);

                header("Location: index.php");
                exit();
            }
        }
    } else {
        $invalidCode = true;
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webstar | Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="shared/assets/css/sidebar-and-container-styles.css">
    <link rel="stylesheet" href="shared/assets/css/courseJoin.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="icon" type="image/png" href="shared/assets/img/webstar-icon.png">
    
    <!-- Material Design Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp" />

    <!-- Bootstrap Icons SVG Sprite -->
    <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
        <symbol id="exclamation-triangle-fill" fill="currentColor" viewBox="0 0 16 16">
            <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
        </symbol>
    </svg>

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

                    <div class="container-fluid py-1 overflow-y-auto row-padding-top h-100 d-flex align-items-center justify-content-center">
                        <div class="row">
                            <div class="col-12 d-flex justify-content-center align-items-center" style="min-height: 40vh;">
                                <div class="text-center">
                                    <img src="shared/assets/img/courseJoin/folder-dynamic-color.png" alt="Course Enrollment" class="course-join-image" style="width:200px; height:100%">
                                    <h1 class="course-join-headline">Enroll in your first course to begin</h1>
                                    <p class="course-join-subheadline">Enter the access code provided by your professor.</p>

                                    <form method="POST" id="enrollForm">
                                        <div class="row mb-3 gx-3">
                                            <div class="col">
                                                <div class="form-floating">
                                                    <input type="text" name="access_code" class="form-control" id="access_code" placeholder=" ">
                                                    <label for="access_code">Access Code</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div id="alertContainer" style="display: <?php echo ($invalidCode || $enrolled) ? 'block' : 'none'; ?>;">
                                            <div class="alert alert-danger d-flex align-items-center" role="alert">
                                                <svg class="bi flex-shrink-0 me-2" role="img" aria-label="Danger:">
                                                    <use xlink:href="#exclamation-triangle-fill" />
                                                </svg>
                                                <div>
                                                    <?php echo $invalidCode ? 'The access code you entered does not exist.' : ($enrolled ? 'Course already enrolled.' : 'The access code you entered does not exist.'); ?>
                                                </div>
                                            </div>
                                        </div>

                                        <button type="submit" class="course-join-button">Enroll</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const form = document.getElementById('enrollForm');
                    const alertContainer = document.getElementById('alertContainer');
                    const inputField = document.querySelector('input[name="access_code"]');

                    form.addEventListener('submit', function(e) {
                        const accessCode = inputField.value.trim();

                        console.log('Form submitted, access code:', accessCode);

                        if (accessCode === '') {
                            // Show the alert
                            alertContainer.style.display = 'block';
                            inputField.classList.add('alert-active');
                            console.log('Showing alert');
                            e.preventDefault();
                            return;
                        } else {
                            // Hide the alert if there is input
                            alertContainer.style.display = 'none';
                            inputField.classList.remove('alert-active');
                            console.log('Hiding alert');
                        }

                        // If we get here, the input is valid
                        // You can add success logic here
                        console.log('Form submitted with access code:', accessCode);
                    });
                });
            </script>

</body>


</html>