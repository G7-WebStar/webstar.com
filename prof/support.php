<?php
$activePage = 'support';
include('../shared/assets/database/connect.php');
include("../shared/assets/processes/prof-session-process.php");


$supportRole = "Professor";
$supportProfQuery = "SELECT * FROM supports WHERE supportRole = '$supportRole'";
$supportProfResult = executeQuery($supportProfQuery);

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Support ✦ Webstar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="../shared/assets/css/sidebar-and-container-styles.css">
    <link rel="stylesheet" href="../shared/assets/css/settings.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../shared/assets/img/webstar-icon.png">
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
            <?php include '../shared/components/prof-sidebar-for-mobile.php'; ?>
            <?php include '../shared/components/prof-sidebar-for-desktop.php'; ?>
            <?php include '../shared/components/prof-navbar-for-mobile.php'; ?>
            <div class="col main-container m-0 mx-0 mx-md-2 p-md-4 overflow-y-auto">
                <div class="card border-0 px-3 pt-3 m-0 h-100 w-100 rounded-0 shadow-none"
                    style="background-color: transparent;">


                    <div class="container-fluid py-3 row-padding-top">
                        <div class="row">
                            <div class="col-12">
                                <!-- Header Section -->
                                <div class="row align-items-center mb-3 text-center text-md-start">
                                    <div class="col-12 col-md-auto text-center text-md-start position-relative">
                                        <h1 class="text-sbold text-25 my-2" style="color: var(--black);">Support
                                        </h1>
                                    </div>

                                    <!-- Message Content -->
                                    <div class="message-container mt-3 pb-4">
                                        <div id="ratingAccordion">
                                            <!-- Questions -->
                                            <div class="row">
                                                <?php
                                                $counter = 1;
                                                if (mysqli_num_rows($supportProfResult) > 0) {
                                                    while ($faq = mysqli_fetch_assoc($supportProfResult)) {
                                                        $collapseID = 'faq' . $counter;
                                                        ?>
                                                        <div class="col-12 col-md-8 mb-2">
                                                            <button
                                                                class="btn w-100 d-flex flex-column align-items-start text-med text-14"
                                                                type="button" data-bs-toggle="collapse"
                                                                data-bs-target="#<?php echo $collapseID; ?>"
                                                                aria-expanded="false" aria-controls="<?php echo $collapseID; ?>"
                                                                style="background-color: var(--pureWhite); border-radius: 10px; border: 1px solid var(--black); text-align: left; white-space: normal; word-wrap: break-word;">

                                                                <div class="d-flex w-100 align-items-center">
                                                                    <span class="flex-grow-1 text-start text-16 text-sbold">
                                                                        <?php echo htmlspecialchars($faq['supportQuestion']); ?>
                                                                    </span>
                                                                    <span
                                                                        class="material-symbols-rounded transition">expand_more</span>
                                                                </div>

                                                                <div class="collapse w-100" id="<?php echo $collapseID; ?>"
                                                                    data-bs-parent="#ratingAccordion">
                                                                    <p class="mb-0 text-reg text-14 text-start pe-4 pb-2"
                                                                        style="white-space: normal; word-wrap: break-word;">
                                                                        <?php echo $faq['supportAnswer'];
                                                                        ?>
                                                                    </p>
                                                                </div>
                                                            </button>
                                                        </div>
                                                        <?php
                                                        $counter++;
                                                    }
                                                } else {
                                                    echo '<div class="col-12"><p class="text-center text-muted">No FAQs available.</p></div>';
                                                }
                                                ?>
                                            </div>


                                            <div class="row mt-3">
                                                <div class="col-12 col-md-6">
                                                    <div class="text-sbold text-16">
                                                        Contact us
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mb-2">
                                                <div class="col-12 col-md-6 mt-1">
                                                    <div
                                                        class="d-flex align-items-center justify-content-center justify-content-md-start text-med text-16">
                                                        <span class="material-symbols-rounded me-1"
                                                            style="font-size: 20px; vertical-align: middle;">
                                                            mail
                                                        </span>
                                                        <span>learn.webstar@gmail.com</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12 col-md-6">
                                                    <a href="https://mail.google.com/mail/?view=cm&fs=1&to=learn.webstar@gmail.com"
                                                        class="btn rounded-5 text-med text-12 px-4"
                                                        style="background-color: var(--primaryColor); border: 1px solid var(--black);"
                                                        target="_blank">
                                                        Send us an email
                                                    </a>

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
    </div>
    </div>

    </div>
    </div>
    </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const buttons = document.querySelectorAll('[data-bs-toggle="collapse"]');

            buttons.forEach(button => {
                const target = button.getAttribute('data-bs-target');
                const icon = button.querySelector('.material-symbols-rounded');
                const collapse = document.querySelector(target);

                if (collapse && icon) {
                    collapse.addEventListener('show.bs.collapse', () => {
                        // Reset all others
                        buttons.forEach(btn => btn.style.backgroundColor = 'var(--pureWhite)');
                        document.querySelectorAll('.material-symbols-rounded').forEach(ic => ic.style.transform = 'rotate(0deg)');

                        // Highlight this one
                        icon.style.transform = 'rotate(180deg)';
                        icon.style.transition = 'transform 0.3s';
                        button.style.backgroundColor = 'var(--primaryColor)';
                    });

                    collapse.addEventListener('hide.bs.collapse', () => {
                        icon.style.transform = 'rotate(0deg)';
                        button.style.backgroundColor = 'var(--pureWhite)';
                    });
                }
            });
        });
    </script>

</body>

</html>