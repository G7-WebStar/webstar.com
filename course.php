<?php
$activePage = 'course';
include('shared/assets/database/connect.php');
include("shared/assets/processes/session-process.php");
$noResult = false;
$selectCourseQuery = "SELECT 
    courses.*, 
   	profInfo.firstName AS profFirstName,
    profInfo.middleName AS profMiddleName,
    profInfo.lastName AS profLastName,
    profInfo.profilePicture AS profPFP,
    SUBSTRING_INDEX(courses.schedule, ' ', 1)  AS courseDays,
    SUBSTRING_INDEX(courses.schedule, ' ', -1) AS courseTime
    FROM courses
    INNER JOIN userinfo AS profInfo
    	ON courses.userID = profInfo.userID
    INNER JOIN enrollments
    	ON courses.courseID = enrollments.courseID
    WHERE enrollments.userID = '$userID'
";

$baseCourseQuery = $selectCourseQuery;

if ((isset($_GET['search'])) && ($_GET['search'] !== '')) {
    $search = $_GET['search'];

    $searchCourseQuery = $selectCourseQuery .= " AND (courses.courseCode LIKE '%$search%' OR courses.courseTitle LIKE '%$search%')";
    $selectCourseResult = executeQuery($searchCourseQuery);

    if (mysqli_num_rows($selectCourseResult) == 0) {
        $selectCourseResult = executeQuery($baseCourseQuery);
        $noResult = true;
    }
} else {
    $search = '';
    $selectCourseResult = executeQuery($baseCourseQuery);
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webstar | My Courses</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="shared/assets/css/sidebar-and-container-styles.css">
    
    <!-- Material Design Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp" />

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="shared/assets/img/webstar-icon.png">
    <link rel="stylesheet" href="shared/assets/css/course.css">
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

                    <div class="container-fluid py-3 overflow-y-auto row-padding-top">
                        <div class="row m-4 justify-content-center">

                            <div class="row header-section align-items-center justify-content-center">
                                <!-- Start of Header Section -->
                                <div class="col-12 col-xl-8 mb-3 mb-xl-0">
                                    <div class="row g-3">
                                        <div class="col-12 col-lg-3 px-0">
                                            <p
                                                class="text-sbold mb-0 lh-md dynamic-text text-start text-sm-center text-md-start">
                                                My Courses</p>
                                        </div>
                                        <div class="col-12 col-lg-6 px-0 px-xl-auto">
                                            <div class="search-container d-flex mx-sm-auto">
                                                <form method="GET" class="form-control bg-transparent border-0">
                                                    <input type="text" placeholder="Search" name="search"
                                                        value="<?php echo $search ?>"
                                                        class="form-control py-1 text-reg text-14">
                                                    <button type="submit" class="btn-outline-secondary">
                                                        <i class="bi bi-search me-2"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                        <div
                                            class="col-12 col-sm-3 justify-content-lg-end justify-content-xl-start align-items-center px-0 px-xl-auto d-flex d-sm-none d-lg-flex">
                                            <div class="d-flex align-items-center flex-nowrap">
                                                <span class="dropdown-label me-2 text-reg">Status:</span>
                                                <button class="btn dropdown-toggle dropdown-custom" type="button"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                    <span class="text-reg text-14">Active</span>
                                                </button>
                                                <ul class="dropdown-menu text-reg text-14">
                                                    <li><a class="dropdown-item text-reg text-14" href="#">Active</a>
                                                    </li>
                                                    <li><a class="dropdown-item text-reg text-14" href="#">Archived</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-xl-4">
                                    <div class="row g-3">
                                        <div
                                            class="col-12 d-flex justsify-content-end align-items-center px-0 px-xl-auto">
                                            <div
                                                class="col-6 d-none d-sm-flex d-lg-none justify-content-center justify-content-md-start align-items-center flex-nowrap">
                                                <span class="dropdown-label me-2 text-reg">Status:</span>
                                                <button class="btn dropdown-toggle dropdown-custom" type="button"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                    <span class="text-reg text-14">Active</span>
                                                </button>
                                                <ul class="dropdown-menu text-reg text-14">
                                                    <li><a class="dropdown-item text-reg text-14" href="#">Active</a>
                                                    </li>
                                                    <li><a class="dropdown-item text-reg text-14" href="#">Archived</a>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div
                                                class="col-12 col-sm-6 col-lg-12 d-flex justify-content-end justify-content-sm-center justify-content-md-end align-items-center px-0">
                                                <button
                                                    class="add-course-btn btn btn-primary px-3 py-1 rounded-pill text-reg text-md-14">
                                                    <a href="course-join.php"
                                                        style="text-decoration: none; color: black;">+ Add Course</a>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- End of Header Section -->

                                <!-- Start of Cards Section -->
                                <div class="row px-0">
                                    <!-- Card -->
                                    <?php
                                    if (mysqli_num_rows($selectCourseResult) > 0 && !$noResult) {
                                        while ($courses = mysqli_fetch_assoc($selectCourseResult)) {
                                            ?>
                                            <div class="col-12 col-lg-6 col-xl-4 mt-4">
                                                <div class="card border border-black rounded-4">
                                                    <a href="course-info.php?courseID=<?php echo $courses['courseID']; ?>">
                                                        <img src="shared/assets/img/home/<?php echo $courses['courseImage']; ?>"
                                                            class="card-img-top object-fit-cover rounded-top-4" alt="..."
                                                            style="background-color: #FDDF94; height: 190px;">
                                                    </a>
                                                    <div class="card-body border-top border-black">
                                                        <div class="row lh-1 mb-2">
                                                            <a href="course-info.php?courseID=<?php echo $courses['courseID']; ?>"
                                                                class="text-decoration-none text-black">
                                                                <p class="card-text text-bold text-18 m-0">
                                                                    <?php echo $courses['courseCode']; ?></p>
                                                            </a>
                                                            <p class="card-text text-reg text-14 mb-2">
                                                                <?php echo $courses['courseTitle']; ?></p>
                                                        </div>
                                                        <div class="row px-3 mb-2">
                                                            <div
                                                                class="col-1 d-flex justify-content-center align-items-center m-0 p-0">
                                                                <img src="shared/assets/pfp-uploads/<?php echo $courses['profPFP']; ?>"
                                                                    alt="" width="32" height="32" class="rounded-circle">
                                                            </div>
                                                            <div class="col-11 my-0 lh-sm">
                                                                <p class="card-text text-bold text-14 m-0">
                                                                    <?php echo $courses['profFirstName'] . " " . $courses['profMiddleName'] . " " . $courses['profLastName']; ?>
                                                                </p>
                                                                <p class="card-text text-med text-12 mb-2">Professor</p>
                                                            </div>
                                                        </div>
                                                        <div class="row px-3 mb-2">
                                                            <div
                                                                class="col-1 d-flex justify-content-center align-items-center m-0 p-0">
                                                                <img src="shared/assets/img/course/Calendar.png" alt=""
                                                                    width="24" height="24">
                                                            </div>
                                                            <div class="col-11 my-0 lh-sm">
                                                                <p class="card-text text-reg text-14"><span
                                                                        class="text-med"><?php echo $courses['courseDays']; ?></span>
                                                                    <?php echo $courses['courseTime']; ?></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                    } else if ($noResult) { ?>
                                            <div class="text-sbold h1 text-center mt-5">No Result.</div>
                                        <?php
                                    } else {
                                        ?>
                                            <script>
                                                window.location.href = "course-join.php";
                                            </script>
                                        <?php
                                    }
                                    ?>
                                </div>
                                <!-- End of Cards Section -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>