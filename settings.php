<?php
$activePage = 'settings';

include("shared/assets/database/connect.php");
include("shared/assets/processes/session-process.php");

$activeTab = 'edit-profile';
if (isset($_POST['activeTab'])) {
    $activeTab = $_POST['activeTab'];
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webstar | Settings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="shared/assets/css/sidebar-and-container-styles.css">
    <link rel="stylesheet" href="shared/assets/css/settings.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="shared/assets/img/webstar-icon.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <!-- Material Design Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:FILL@1" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
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

                    <div class="container-fluid overflow-y-auto row-padding-top">

                        <!-- Sticky Header: Title + Tabs -->
                        <div class="settings-header-wrapper my-3">
                            <div class="row align-items-center">
                                <!-- Title -->
                                <div class="col-12 col-lg-auto">
                                    <div class="text-bold text-30">Settings</div>
                                </div>

                                <!-- Tabs -->
                                <div class="col">
                                    <div class="tab-carousel-wrapper mt-lg-1 mt-0">
                                        <div class="d-flex align-items-center" style="width: 100%;">
                                            <!-- Left Arrow -->
                                            <button id="settingsScrollLeftBtn" class="scroll-arrow-btn d-none"
                                                aria-label="Scroll Left">
                                                <i class="fa-solid fa-chevron-left"></i>
                                            </button>

                                            <!-- Scrollable Tabs -->
                                            <div class="tab-scroll flex-grow-1 overflow-auto nav-tabs"
                                                style="scroll-behavior: smooth; white-space: nowrap;">
                                                <ul class="nav custom-nav-tabs flex-nowrap w-100" id="settingsTab"
                                                    role="tablist"
                                                    style="display: inline-flex; white-space: nowrap; justify-content: space-between;">
                                                    <li class="nav-item">
                                                        <a class="nav-link text-14 <?php echo ($activeTab == 'edit-profile') ? 'active' : ''; ?>"
                                                            data-bs-toggle="tab" href="#edit-profile">Edit Profile</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link text-14 <?php echo ($activeTab == 'customization') ? 'active' : ''; ?>"
                                                            data-bs-toggle="tab" href="#customization">Customization</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link text-14 <?php echo ($activeTab == 'my-star-card') ? 'active' : ''; ?>"
                                                            data-bs-toggle="tab" href="#my-star-card">My Star Card</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link text-14 <?php echo ($activeTab == 'support') ? 'active' : ''; ?>"
                                                            data-bs-toggle="tab" href="#support">Support</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link text-14 <?php echo ($activeTab == 'send-feedback') ? 'active' : ''; ?>"
                                                            data-bs-toggle="tab" href="#send-feedback">Send Feedback</a>
                                                    </li>
                                                    <li class="nav-item nav-student">
                                                        <a class="nav-link text-14 <?php echo ($activeTab == 'preferences') ? 'active' : ''; ?>"
                                                            data-bs-toggle="tab" href="#preferences">Preferences</a>
                                                    </li>
                                                </ul>
                                            </div>

                                            <!-- Right Arrow -->
                                            <button id="settingsScrollRightBtn" class="scroll-arrow-btn"
                                                aria-label="Scroll Right">
                                                <i class="fa-solid fa-chevron-right"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TAB CONTENT BELOW -->
                        <div class="row mt-4">
                            <div class="col-12">
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

                                    <!-- My Star Card -->
                                    <div class="tab-pane fade <?php echo ($activeTab == 'my-star-card') ? 'show active' : ''; ?>"
                                        id="my-star-card" role="tabpanel">
                                        <?php include 'settings-info-contents/my-star-card.php'; ?>
                                    </div>

                                    <!-- Support -->
                                    <div class="tab-pane fade <?php echo ($activeTab == 'support') ? 'show active' : ''; ?>"
                                        id="support" role="tabpanel">
                                        <?php include 'settings-info-contents/support.php'; ?>
                                    </div>

                                    <!-- Send Feedback -->
                                    <div class="tab-pane fade <?php echo ($activeTab == 'send-feedback') ? 'show active' : ''; ?>"
                                        id="send-feedback" role="tabpanel">
                                        <?php include 'settings-info-contents/send-feedback.php'; ?>
                                    </div>

                                    <!-- Preferences -->
                                    <div class="tab-pane fade <?php echo ($activeTab == 'preferences') ? 'show active' : ''; ?>"
                                        id="preferences" role="tabpanel">
                                        <?php include 'settings-info-contents/preferences.php'; ?>
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
</body>

</html>