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
    <link rel="stylesheet" href="shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="shared/assets/css/sidebar-and-container-styles.css">
    <link rel="stylesheet" href="shared/assets/css/course-Info.css">
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

                    <div class="container-fluid py-3 overflow-y-auto">
                        <div class="row mb-3 row-padding-top">
                            <div class="row mt-0">
                                <!-- Title -->
                                <div class="col-12 col-lg-auto mb-3 mb-lg-0">
                                    <div class="text-bold text-30">Settings</div>
                                </div>

                                <!-- RIGHT: Tabs and Content -->
                                <div class="col-md-8 mt-1">
                                    <div class="tab-section">
                                        <!-- Desktop Tabs -->
                                        <div
                                            class="tab-carousel-wrapper d-none d-md-block position-relative w-100 ms-0 me-0 pe-0">
                                            <div class="d-flex align-items-center position-relative w-100"
                                                style="gap: 10px; padding: 0; margin: 0;">
                                                <!-- Scrollable Tabs -->
                                                <div class="tab-scroll flex-grow-1 overflow-visible"
                                                    style="white-space: normal;">
                                                    <ul class="nav nav-tabs custom-nav-tabs mb-3 flex-nowrap" id="myTab"
                                                        role="tablist" style="display: inline-flex; white-space: nowrap;">

                                                        <li class="nav-item">
                                                            <a class="nav-link <?php if ($activeTab == 'edit-profile') echo 'active'; ?>"
                                                                id="edit-profile-tab" data-bs-toggle="tab"
                                                                href="#edit-profile" role="tab">Edit Profile</a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a class="nav-link <?php if ($activeTab == 'customization') echo 'active'; ?>"
                                                                id="customization-tab" data-bs-toggle="tab"
                                                                href="#customization" role="tab">Customization</a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a class="nav-link <?php if ($activeTab == 'my-star-card') echo 'active'; ?>"
                                                                id="my-star-card-tab" data-bs-toggle="tab"
                                                                href="#my-star-card" role="tab">My Star Card</a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a class="nav-link <?php if ($activeTab == 'support') echo 'active'; ?>"
                                                                id="support-tab" data-bs-toggle="tab" href="#support"
                                                                role="tab">Support</a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a class="nav-link <?php if ($activeTab == 'send-feedback') echo 'active'; ?>"
                                                                id="send-feedback-tab" data-bs-toggle="tab"
                                                                href="#send-feedback" role="tab">Send Feedback</a>
                                                        </li>
                                                        <li class="nav-item nav-student">
                                                            <a class="nav-link <?php if ($activeTab == 'preferences') echo 'active'; ?>"
                                                                id="preferences-tab" data-bs-toggle="tab"
                                                                href="#preferences" role="tab">Preferences</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Mobile Tabs -->
                                        <div class="tab-carousel-wrapper position-relative d-md-none">
                                            <button id="scrollLeftBtn" class="scroll-arrow-btn start-0 d-none"
                                                aria-label="Scroll Left">
                                                <i class="fa-solid fa-chevron-left"></i>
                                            </button>
                                            <button id="scrollRightBtn" class="scroll-arrow-btn end-0"
                                                aria-label="Scroll Right">
                                                <i class="fa-solid fa-chevron-right"></i>
                                            </button>

                                            <ul class="nav nav-tabs custom-nav-tabs mb-3" id="mobileTabScroll"
                                                role="tablist">
                                                <li class="nav-item me-3" role="presentation">
                                                    <a class="nav-link <?php if ($activeTab == 'edit-profile') echo 'active'; ?>"
                                                        id="edit-profile-tab" data-bs-toggle="tab"
                                                        href="#edit-profile" role="tab">Edit Profile</a>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <a class="nav-link <?php if ($activeTab == 'customization') echo 'active'; ?>"
                                                        id="customization-tab" data-bs-toggle="tab"
                                                        href="#customization" role="tab">Customization</a>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <a class="nav-link <?php if ($activeTab == 'my-star-card') echo 'active'; ?>"
                                                        id="my-star-card-tab" data-bs-toggle="tab"
                                                        href="#my-star-card" role="tab">My Star Card</a>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <a class="nav-link <?php if ($activeTab == 'support') echo 'active'; ?>"
                                                        id="support-tab" data-bs-toggle="tab" href="#support"
                                                        role="tab">Support</a>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <a class="nav-link <?php if ($activeTab == 'send-feedback') echo 'active'; ?>"
                                                        id="send-feedback-tab" data-bs-toggle="tab"
                                                        href="#send-feedback" role="tab">Send Feedback</a>
                                                </li>
                                                <li class="nav-item nav-report" role="presentation">
                                                    <a class="nav-link <?php if ($activeTab == 'preferences') echo 'active'; ?>"
                                                        id="preferences-tab" data-bs-toggle="tab" href="#preferences"
                                                        role="tab">Preferences</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tab Content -->
                                <div class="row">
                                    <div class="col">
                                        <div class="tab-content" id="myTabContent">
                                            <div class="tab-pane fade <?php if ($activeTab == 'edit-profile') echo 'show active'; ?>"
                                                id="edit-profile" role="tabpanel">
                                                <?php include 'settings-info-contents/edit-profile.php'; ?>
                                            </div>
                                            <div class="tab-pane fade <?php if ($activeTab == 'customization') echo 'show active'; ?>"
                                                id="customization" role="tabpanel">
                                                <?php include 'settings-info-contents/customization.php'; ?>
                                            </div>
                                            <div class="tab-pane fade <?php if ($activeTab == 'my-star-card') echo 'show active'; ?>"
                                                id="my-star-card" role="tabpanel">
                                                <?php include 'settings-info-contents/my-star-card.php'; ?>
                                            </div>
                                            <div class="tab-pane fade <?php if ($activeTab == 'support') echo 'show active'; ?>"
                                                id="support" role="tabpanel">
                                                <?php include 'settings-info-contents/support.php'; ?>
                                            </div>
                                            <div class="tab-pane fade <?php if ($activeTab == 'send-feedback') echo 'show active'; ?>"
                                                id="send-feedback" role="tabpanel">
                                                <?php include 'settings-info-contents/send-feedback.php'; ?>
                                            </div>
                                            <div class="tab-pane fade <?php if ($activeTab == 'preferences') echo 'show active'; ?>"
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
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

    <!-- JS for Mobile Scroll Buttons -->
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
                tabContainer.scrollBy({ left: -100, behavior: 'smooth' });
            });

            scrollRightBtn.addEventListener('click', () => {
                tabContainer.scrollBy({ left: 100, behavior: 'smooth' });
            });

            tabContainer.addEventListener('scroll', updateArrowVisibility);
            updateArrowVisibility();
        });
    </script>
</body>
</html>
