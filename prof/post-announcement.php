<?php $activePage = 'post-announcement'; ?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webstar | Post Announcement</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="../shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="../shared/assets/css/post-announcement.css">
    <link rel="stylesheet" href="../shared/assets/css/sidebar-and-container-styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../shared/assets/img/webstar-icon.png">
    <!-- Quill CSS -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

</head>

<body>
    <div class="container-fluid min-vh-100 d-flex justify-content-center align-items-center p-0 p-md-3"
        style="background-color: var(--black);">

        <div class="row w-100">

            <!-- Sidebar (mobile) -->
            <?php include '../shared/components/prof-sidebar-for-mobile.php'; ?>

            <!-- Sidebar (desktop) -->
            <?php include '../shared/components/prof-sidebar-for-desktop.php'; ?>

            <!-- Main Container -->
            <div class="col main-container m-0 p-0 mx-0 mx-md-2 p-md-4 overflow-y-auto">
                <div class="card border-0 px-3 pt-3 m-0 h-100 w-100 rounded-0 shadow-none"
                    style="background-color: transparent;">

                    <!-- Navbar (mobile) -->
                    <?php include '../shared/components/prof-navbar-for-mobile.php'; ?>

                    <div class="container-fluid py-3 overflow-y-auto">
                        <div class="row">
                            <div class="col-12">
                                <!-- Header -->
                                <div class="row mb-3 align-items-center">
                                    <div class="col-auto">
                                        <a href="#" class="text-decoration-none">
                                            <i class="fa-solid fa-arrow-left text-reg text-16"
                                                style="color: var(--black);"></i>
                                        </a>
                                    </div>
                                    <div class="col">
                                        <span class="text-sbold text-25">Post announcement</span>
                                    </div>
                                </div>

                                <!-- Rich Text Editor -->
                                <form method="post">
                                    <div class="mb-3">
                                        <div class="editor-wrapper">
                                            <!-- Editor box -->
                                            <div id="editor"></div>
                                            <!-- Toolbar -->
                                            <div id="toolbar" class="row align-items-center p-2 p-md-4 g-2 g-md-5">
                                                <!-- Quill formatting buttons -->
                                                <div class="col d-flex align-items-center px-2 px-md-4 gap-1 gap-md-3">
                                                    <button class="ql-bold"></button>
                                                    <button class="ql-italic"></button>
                                                    <button class="ql-underline"></button>
                                                    <button class="ql-list" value="bullet"></button>
                                                    <button class="ql-upload"></button>
                                                    <button class="ql-link"></button>

                                                    <!-- Word counter aligned to right -->
                                                    <span id="word-counter" class="ms-auto text-muted text-med text-16">0/120</span>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="announcement" id="announcement">
                                    </div>

                                    <!-- Course selection + Post button -->
                                    <div class="row align-items-center mb-3 text-center text-md-start">
                                        <!-- Dropdown -->
                                        <div
                                            class="col-12 col-md-auto mt-3 d-flex justify-content-center justify-content-md-start">
                                            <div class="d-flex align-items-center flex-nowrap">
                                                <span class="me-2 text-med text-16 pe-3">Post to Course</span>
                                                <button
                                                    class="btn dropdown-toggle dropdown-shape text-med text-16 me-md-5"
                                                    type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <span>COMP-006</span>
                                                </button>
                                                <ul class="dropdown-menu p-2" style="min-width: 200px;">
                                                    <li>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                value="COMP-006" id="course1" checked>
                                                            <label class="form-check-label text-reg" for="course1">
                                                                COMP-006
                                                            </label>
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                value="COMP-007" id="course2">
                                                            <label class="form-check-label text-reg" for="course2">
                                                                COMP-007
                                                            </label>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>

                                        <!-- Post button -->
                                        <div class="col-md-6 text-md-center text-center mt-3 mt-md-0">
                                            <button type="submit"
                                                class="px-4 py-2 rounded-pill text-reg text-md-14 mt-3 ms-3"
                                                style="background-color: var(--primaryColor); border: 1px solid var(--black);">
                                                Post
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quill JS -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script>
        var icons = Quill.import("ui/icons");

        // Custom upload icon (styled same as bold/italic)
        icons['upload'] = '<svg viewBox="0 0 18 18">' +
            '<line class="ql-stroke" x1="9" x2="9" y1="15" y2="3"></line>' +
            '<polyline class="ql-stroke" points="5 7 9 3 13 7"></polyline>' +
            '<rect class="ql-fill" height="2" width="12" x="3" y="15"></rect>' +
            '</svg>';

        var quill = new Quill('#editor', {
            theme: 'snow',
            placeholder: 'Announce something to your class',
            modules: {
                toolbar: '#toolbar'
            }
        });

        const maxWords = 120;
        const counter = document.getElementById("word-counter");

        quill.on('text-change', function () {
            let text = quill.getText().trim();
            let words = text.length > 0 ? text.split(/\s+/).length : 0;

            if (words > maxWords) {
                let limited = text.split(/\s+/).slice(0, maxWords).join(" ");
                quill.setText(limited + " ");
                quill.setSelection(quill.getLength()); // keep cursor at end
            }

            counter.textContent = `${Math.min(words, maxWords)}/${maxWords}`;
        });

        // Sync content to hidden input before submit
        // document.querySelector('form').addEventListener('submit', function () {
        //     document.querySelector('#announcement').value = quill.root.innerHTML;
        // });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>