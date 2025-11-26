<?php
$activePage = 'settings';

include("../shared/assets/database/connect.php");
include("../shared/assets/processes/prof-session-process.php");
date_default_timezone_set('Asia/Manila');

$activeTab = 'edit-profile';
if (isset($_POST['activeTab'])) {
    $activeTab = $_POST['activeTab'];
}

$toastMessage = '';
$toastType = '';

if (isset($_POST['saveBio'])) {
    $bio = $_POST['bio'];
    $update = $conn->query("UPDATE profile SET bio = '$bio' WHERE userID = $userID");
    if ($update) {
        $toastMessage = 'Bio updated successfully!';
        $toastType = 'alert-success';
    }
}

if (isset($_POST['saveLogo'])) {
    $emblemID = $_POST['selectedEmblem'];
    $update = $conn->query("UPDATE profile SET emblemID = '$emblemID' WHERE userID = $userID");
    if ($update) {
        $toastMessage = 'Emblem updated successfully!';
        $toastType = 'alert-success';
    }
}

if (isset($_POST['saveCover'])) {
    $coverID = $_POST['selectedCover'];
    $update = $conn->query("UPDATE profile SET coverImageID = '$coverID' WHERE userID = $userID");
    if ($update) {
        $toastMessage = 'Cover image updated successfully!';
        $toastType = 'alert-success';
    }
}

if (isset($_POST['saveProfile'])) {
    $themeID = $_POST['selectedTheme'];
    $update = $conn->query("UPDATE profile SET colorThemeID = '$themeID' WHERE userID = $userID");
    if ($update) {
        $toastMessage = 'Color theme updated successfully!';
        $toastType = 'alert-success';
    }
}


if (isset($_POST['selectedCourse'])) {
    $selectedCourseID = intval($_POST['selectedCourse']);

    $update = $conn->query("
        UPDATE profile 
        SET starCard = '$selectedCourseID' 
        WHERE userID = '$userID'
    ");

    if ($update) {
        $toastMessage = 'Star Card display updated successfully!';
        $toastType = 'alert-success';
    }
}

if (isset($_POST['saveChanges'])) {
    $firstName = $_POST['firstName'] ?? '';
    $middleName = $_POST['middleName'] ?? '';
    $lastName = $_POST['lastName'] ?? '';
    $userName = strtolower($_POST['userName']) ?? '';
    $studentID = strtoupper($_POST['studentID']) ?? '';
    $programID = $_POST['program'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $yearLevel = $_POST['yearLevel'] ?? '';
    $yearSection = $_POST['yearSection'] ?? '';
    $schoolEmail = $_POST['schoolEmail'] ?? '';
    $fbLink = $_POST['fbLink'] ?? '';
    $linkedInLink = $_POST['linkedInLink'] ?? '';
    $githubLink = $_POST['githubLink'] ?? '';

    // Handle profile picture upload
    $uploadField = null;
    if (!empty($_FILES['fileUpload']['name'])) {
        $uploadField = 'fileUpload';
    } elseif (!empty($_FILES['fileUploadMobile']['name'])) {
        $uploadField = 'fileUploadMobile';
    }

    if ($uploadField !== null) {

        $file = $_FILES[$uploadField];

        $fileTmp = $file['tmp_name'];
        $fileName = $file['name'];
        $fileSize = $file['size'];
        $fileType = mime_content_type($fileTmp);

        $allowedTypes = ['image/jpeg', 'image/png'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        if (!in_array($fileType, $allowedTypes)) {
            $toastMessage = "Invalid file type. Only JPG/PNG allowed.";
            $toastType = "alert-danger";
        } elseif ($fileSize > $maxSize) {
            $toastMessage = "File too large. Max 5MB.";
            $toastType = "alert-danger";
        } else {
            $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $newFileName = "profile_{$userID}_" . time() . "." . $fileExt;
            $uploadDir = "../shared/assets/pfp-uploads/";

            if (!is_dir($uploadDir))
                mkdir($uploadDir, 0777, true);

            $uploadPath = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmp, $uploadPath)) {
                $profilePicture = $newFileName;
            } else {
                $toastMessage = "Upload failed!";
                $toastType = "alert-danger";
            }
        }
    }

    // Check if username is taken by another user
    $userNameEscaped = mysqli_real_escape_string($conn, $userName);
    $userIDInt = intval($userID);
    $checkQuery = "SELECT userID FROM users WHERE username='$userNameEscaped' AND userID != $userIDInt";
    $checkResult = mysqli_query($conn, $checkQuery);

    if ($checkResult && mysqli_num_rows($checkResult) > 0) {
        $usernameTaken = true;
        $usernameTakenMessage = "Username has already been taken";
        $toastMessage = 'Username has already been taken.';
        $toastType = 'alert-danger';
    } else {
        // Update users table
        executeQuery("UPDATE users SET username='$userName' WHERE userID='$userID'");

        // Update userinfo table
        $updateInfoQuery = "
            UPDATE userinfo SET
                firstName='$firstName',
                middleName='$middleName',
                lastName='$lastName',
                studentID='$studentID',
                gender='$gender',
                yearLevel='$yearLevel',
                yearSection='$yearSection',
                schoolEmail='$schoolEmail',
                facebookLink='$fbLink',
                linkedInLink='$linkedInLink',
                githubLink='$githubLink',
                programID='$programID'
        ";
        if (!empty($profilePicture))
            $updateInfoQuery .= ", profilePicture='$profilePicture'";
        $updateInfoQuery .= " WHERE userID='$userID'";

        $result = executeQuery($updateInfoQuery);

        if ($result) {
            $profileUpdated = true;
            $toastMessage = 'Profile updated successfully!';
            $toastType = 'alert-success';
        }
    }
}
// --- Handle save action ---
if (isset($_POST['save'])) {
    $courseUpdateEnabled = isset($_POST['courseUpdateEnabled']) ? 1 : 0;
    $questDeadlineEnabled = isset($_POST['questDeadlineEnabled']) ? 1 : 0;
    $announcementEnabled = isset($_POST['announcementEnabled']) ? 1 : 0;

    executeQuery("
        UPDATE settings SET 
            courseUpdateEnabled = '$courseUpdateEnabled',
            questDeadlineEnabled = '$questDeadlineEnabled',
            announcementEnabled = '$announcementEnabled'
        WHERE userID = '$userID'
    ");

    // Refresh settings after update
    $result = executeQuery("SELECT * FROM settings WHERE userID = '$userID'");
    $settings = mysqli_fetch_assoc($result);

    // Keep the tab as preferences
    $activeTab = 'preferences';

     if ($result) {
            $toastMessage = 'Preferences updated successfully!';
            $toastType = 'alert-success';
        }
}

// Handle feedback form submission
if (isset($_POST['feedback'])) {
    $feedback = trim($_POST['feedback']);

    if ($userID && !empty($feedback)) {
        // Find the admin (receiver)
        $adminResult = executeQuery("SELECT userID FROM users WHERE role = 'admin' LIMIT 1");
        $adminData = mysqli_fetch_assoc($adminResult);

        if ($adminData) {
            $receiverID = $adminData['userID'];
            $createdAt = date('Y-m-d H:i:s');

            executeQuery("
                INSERT INTO feedback (senderID, receiverID, message, createdAt)
                VALUES ('$userID', '$receiverID', '$feedback', '$createdAt')
            ");
        }

        if ($adminResult) {
            $toastMessage = 'Thanks for your feedback! We’ll review it carefully and use it to improve your experience.';
            $toastType = 'alert-success';
        }
    }
}

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Settings ✦ Webstar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="../shared/assets/css/sidebar-and-container-styles.css">
    <link rel="stylesheet" href="../shared/assets/css/settings.css">
    <link rel="stylesheet" href="../shared/assets/css/shop.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../shared/assets/img/webstar-icon.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <!-- Material Design Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:FILL@1" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />

    <style>
        #desktopScrollRightBtn {
            visibility: hidden;
            /* hidden by default */
        }
    </style>
</head>

<body>
    <div class="container-fluid min-vh-100 d-flex justify-content-center align-items-center p-0 p-md-3"
        style="background-color: var(--black);">

        <div class="row w-100">

            <?php include '../shared/components/prof-sidebar-for-mobile.php'; ?>
            <?php include '../shared/components/prof-sidebar-for-desktop.php'; ?>

            <!-- Main Container Column-->
            <div class="col main-container m-0 p-0 mx-0 mx-md-2 p-0 p-md-4 overflow-y-auto">
                <div class="card border-0 px-3 pt-3 m-0 h-100 w-100 rounded-0 shadow-none"
                    style="background-color: transparent;">

                    <!-- Toast container -->
                    <div id="toastContainer"
                        class="position-absolute top-0 start-50 translate-middle-x pt-5 pt-md-1 d-flex flex-column align-items-center"
                        style="z-index: 1100;"></div>


                    <!-- Navbar for mobile -->
                    <?php include '../shared/components/prof-navbar-for-mobile.php'; ?>

                    <div class="container-fluid py-3 overflow-y-auto row-padding-top shop-container h-100">

                        <!-- Sticky Header: Title + Tabs -->
                        <div class="settings-header-wrapper my-1">
                            <div class="row align-items-center">
                                <!-- Title -->
                                <div class="col-12 col-md-auto shop-title">
                                    <h1 class="text-sbold text-25" style="color: var(--black); ">Settings
                                    </h1>
                                </div>

                                <!-- Tabs -->
                                <div class="col-12 col-md-auto">
                                    <div class="tab-carousel-wrapper">
                                        <div class="d-flex align-items-center" style="width: 100%;">

                                            <!-- Left Arrow -->
                                            <button id="desktopScrollLeftBtn" class="scroll-arrow-btn d-none"
                                                aria-label="Scroll Left"
                                                style="background: none; border: none; color: var(--black); flex-shrink: 0; margin-top:-2px;">
                                                <i class="fa-solid fa-chevron-left"></i>
                                            </button>

                                            <!-- Scrollable Tabs -->
                                            <div class="tab-scroll flex-grow-1 overflow-auto nav-tabs"
                                                style="scroll-behavior: smooth; white-space: nowrap;">
                                                <ul class="nav custom-nav-tabs flex-nowrap d-flex justify-content-between justify-content-md-start"
                                                    id="shopTabs" role="tablist"
                                                    style="display: inline-flex; white-space: nowrap;">
                                                    <li class="nav-item">
                                                        <a class="nav-link text-14 fade <?php echo ($activeTab == 'edit-profile') ? 'active' : ''; ?>"
                                                            data-bs-toggle="tab" data-bs-target="#edit-profile"
                                                            href="#edit-profile">Edit Profile</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link text-14 <?php echo ($activeTab == 'customization') ? 'active' : ''; ?>"
                                                            data-bs-toggle="tab" data-bs-target="#customization"
                                                            href="#customization">Customization</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link text-14 <?php echo ($activeTab == 'send-feedback') ? 'active' : ''; ?>"
                                                            data-bs-toggle="tab" data-bs-target="#send-feedback"
                                                            href="#send-feedback">Send Feedback</a>
                                                    </li>
                                                </ul>
                                            </div>
                                            <!-- Right Arrow -->
                                            <button id="desktopScrollRightBtn" class="scroll-arrow-btn"
                                                aria-label="Scroll Right"
                                                style="background: none; border: none; color: var(--black); flex-shrink: 0; margin-top:-2px;">
                                                <i class="fa-solid fa-chevron-right"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TAB CONTENT BELOW -->
                        <div class="row mt-2 d-flex px-3 px-md-0 justify-content-center justify-content-md-start">
                            <div class="col-12 p-0 w-auto">
                                <div class="tab-content" id="settingsTabContent">
                                    <!-- Edit Profile -->
                                    <div class="tab-pane fade <?php echo ($activeTab == 'edit-profile') ? 'show active' : ''; ?>"
                                        id="edit-profile" role="tabpanel">
                                        <?php include 'settings-info-contents/edit-profile.php'; ?>
                                    </div>

                                    <!-- Customization -->
                                    <div class="tab-pane fade <?php echo ($activeTab == 'customization') ? 'show active' : ''; ?>"
                                        id="customization" role="tabpanel">
                                        <?php include 'settings-info-contents/customization.php'; ?>
                                    </div>
                                    <!-- Send Feedback -->
                                    <div class="tab-pane fade <?php echo ($activeTab == 'send-feedback') ? 'show active' : ''; ?>"
                                        id="send-feedback" role="tabpanel">
                                        <?php include 'settings-info-contents/send-feedback.php'; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

    <!-- JS for Mobile Scroll Buttons -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const scrollContainer = document.querySelector(".tab-scroll");
            const leftBtn = document.getElementById("settingsScrollLeftBtn");
            const rightBtn = document.getElementById("settingsScrollRightBtn");

            function updateArrowVisibility() {
                if (!scrollContainer) return;

                leftBtn.classList.toggle("d-none", scrollContainer.scrollLeft <= 0);

                const atFarRight =
                    scrollContainer.scrollLeft + scrollContainer.clientWidth >= scrollContainer.scrollWidth - 2;

                rightBtn.classList.toggle("d-none", atFarRight);
            }

            leftBtn.addEventListener("click", () => {
                scrollContainer.scrollBy({ left: -150, behavior: "smooth" });
            });

            rightBtn.addEventListener("click", () => {
                scrollContainer.scrollBy({ left: 150, behavior: "smooth" });
            });

            scrollContainer.addEventListener("scroll", updateArrowVisibility);
            window.addEventListener("resize", updateArrowVisibility);

            setTimeout(updateArrowVisibility, 200);
        });
    </script>

    <!-- Nav Script -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const tabContainer = document.getElementById('mobileTabScroll');
            const scrollLeftBtn = document.getElementById('scrollLeftBtn');
            const scrollRightBtn = document.getElementById('scrollRightBtn');

            function updateArrowVisibility() {
                if (!tabContainer) return;

                scrollLeftBtn.classList.toggle('d-none', tabContainer.scrollLeft === 0);
                scrollRightBtn.classList.toggle('d-none', tabContainer.scrollLeft + tabContainer.clientWidth >= tabContainer.scrollWidth);
            }

            scrollLeftBtn.addEventListener('click', () => {
                tabContainer.scrollBy({
                    left: -100,
                    behavior: 'smooth'
                });
            });

            scrollRightBtn.addEventListener('click', () => {
                tabContainer.scrollBy({
                    left: 100,
                    behavior: 'smooth'
                });
            });

            tabContainer.addEventListener('scroll', updateArrowVisibility);

            updateArrowVisibility();
        });
    </script>

    <!-- JS for Desktop Scroll Buttons -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const desktopTabScroll = document.querySelector(".tab-scroll");
            const desktopScrollLeftBtn = document.getElementById("desktopScrollLeftBtn");
            const desktopScrollRightBtn = document.getElementById("desktopScrollRightBtn");

            function updateDesktopArrowVisibility() {
                if (!desktopTabScroll) return;
                desktopScrollLeftBtn.classList.toggle("d-none", desktopTabScroll.scrollLeft === 0);
                desktopScrollRightBtn.style.visibility = desktopTabScroll.scrollLeft + desktopTabScroll.clientWidth >= desktopTabScroll.scrollWidth ? 'hidden' : 'visible';
            }

            desktopScrollLeftBtn.addEventListener("click", () => {
                desktopTabScroll.scrollBy({
                    left: -150,
                    behavior: "smooth"
                });
            });

            desktopScrollRightBtn.addEventListener("click", () => {
                desktopTabScroll.scrollBy({
                    left: 150,
                    behavior: "smooth"
                });
            });

            desktopTabScroll.addEventListener("scroll", updateDesktopArrowVisibility);

            updateDesktopArrowVisibility();
        });
    </script>

    <!-- Toast Handling -->
    <?php if (!empty($toastMessage)): ?>
        <script>
            window.addEventListener('DOMContentLoaded', () => {
                const container = document.getElementById("toastContainer");
                if (!container) return;

                const alert = document.createElement("div");
                alert.className = `alert mb-2 shadow-lg text-med text-12 d-flex align-items-center justify-content-center gap-2 px-3 py-2 <?= $toastType ?>`;
                alert.role = "alert";
                alert.innerHTML = `
            <i class="bi <?= ($toastType === 'alert-success') ? 'bi-check-circle-fill' : 'bi-x-circle-fill'; ?> fs-6"></i>
            <span><?= addslashes($toastMessage) ?></span>
        `;
                container.appendChild(alert);

                setTimeout(() => alert.remove(), 3000);
            });
        </script>
    <?php endif; ?>


</body>

</html>