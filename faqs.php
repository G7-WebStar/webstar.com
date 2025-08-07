<?php $activePage = 'course'; ?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webstar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Comic+Neue&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="shared/assets/css/sidebar-and-container-styles.css">
    <link rel="stylesheet" href="shared/assets/css/faqs.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>

<body>
    <div class="container-fluid min-vh-100 d-flex justify-content-center align-items-center p-3">
        <div class="row w-100">

            <!-- Sidebar (only shows on mobile) -->
            <?php include 'shared/components/sidebar-for-mobile.php'; ?>

            <!-- Sidebar Column (fixed on desktop) -->
            <div class="col-auto d-none d-md-block">
                <?php include 'shared/components/sidebar-for-desktop.php'; ?>
            </div>

            <!-- Main Container Column-->
            <div class="col main-container m-0 p-0 mx-2 p-2">
                <div class="card border-0 p-2 h-100 w-100 rounded-0 shadow-none">

                    <!-- Navbar for mobile -->
                    <?php include 'shared/components/navbar-for-mobile.php'; ?>

                    <!-- PUT CONTENT HERE -->
                    <div class="container-fluid overflow-y-auto" style="max-height: 100vh;">
                        <div class="col">
                            <!-- Main panel -->
                            <div class="main px-4 py-5">
                                <h1 class="faq-heading mb-3">Frequently Asked Questions</h1>

                                <details class="faq-item">
                                    <summary><img src="shared/assets/img/dropdown.png" class="arrow-icon" alt="toggle">
                                        What is WebStar?</summary>
                                    <div class="faq-content">
                                        WebStar is a web-based interactive learning platform designed to teach web
                                        development using gamification techniques. It is specifically developed for
                                        second- and third-year BSIT students of PUP-STC to enhance learning through
                                        hands-on practice and engaging game-like features.
                                    </div>
                                </details>

                                <details class="faq-item">
                                    <summary><img src="shared/assets/img/dropdown.png" class="arrow-icon" alt="toggle">
                                        Who
                                        can use WebStar?</summary>
                                    <div class="faq-content">
                                        WebStar is primarily for PUP-STC BSIT students but open to anyone interested in
                                        learning web development.
                                    </div>
                                </details>

                                <details class="faq-item">
                                    <summary><img src="shared/assets/img/dropdown.png" class="arrow-icon" alt="toggle">
                                        What topics are covered in WebStar?</summary>
                                    <div class="faq-content">
                                        WebStar covers HTML, CSS, JavaScript, and responsive design along with
                                        interactive activities.
                                    </div>
                                </details>

                                <details class="faq-item">
                                    <summary><img src="shared/assets/img/dropdown.png" class="arrow-icon" alt="toggle">
                                        How
                                        does gamification work in WebStar?</summary>
                                    <div class="faq-content">
                                        Learners earn points, badges, and rank up through coding challenges, quizzes,
                                        and achievements.
                                    </div>
                                </details>

                                <details class="faq-item">
                                    <summary><img src="shared/assets/img/dropdown.png" class="arrow-icon" alt="toggle">
                                        Do
                                        I need prior experience in coding to use WebStar?</summary>
                                    <div class="faq-content">
                                        No. WebStar is beginner-friendly and provides step-by-step tutorials and hints
                                        for each challenge.
                                    </div>
                                </details>

                                <details class="faq-item">
                                    <summary><img src="shared/assets/img/dropdown.png" class="arrow-icon" alt="toggle">
                                        Is
                                        WebStar accessible outside the campus?</summary>
                                    <div class="faq-content">
                                        Yes, WebStar is fully web-based and accessible from anywhere with an internet
                                        connection.
                                    </div>
                                </details>

                                <details class="faq-item">
                                    <summary><img src="shared/assets/img/dropdown.png" class="arrow-icon" alt="toggle">
                                        What should I do if I forget my password?</summary>
                                    <div class="faq-content">
                                        You can reset your password via the “Forgot Password” link on the login page and
                                        follow the email instructions.
                                    </div>
                                </details>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <script>
            document.querySelectorAll('.faq-item').forEach(detail => {
                const icon = detail.querySelector('.arrow-icon');
                const closedSrc = "shared/assets/img/dropdown.png";
                const openSrc = "shared/assets/img/updown.png";

                // Add transition effect
                icon.style.transition = "opacity 0.3s ease";

                detail.addEventListener("toggle", () => {
                    icon.style.opacity = "0"; // Fade out
                    setTimeout(() => {
                        icon.src = detail.open ? openSrc : closedSrc;
                        icon.style.opacity = "1"; // Fade in
                    }, 150); // Delay matches half the transition time
                });
            });

        </script>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>