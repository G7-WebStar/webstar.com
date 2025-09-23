<?php $activePage = 'assign-task'; ?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webstar | Assign Task</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="../shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="../shared/assets/css/create-exam.css">
    <link rel="stylesheet" href="../shared/assets/css/add-lesson.css">
    <link rel="stylesheet" href="../shared/assets/css/sidebar-and-container-styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
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
                                    <div class="col text-center text-md-start">
                                        <span class="text-sbold text-25">Create Exam</span>
                                    </div>
                                </div>

                                <!-- Form starts -->
                                <form>
                                    <div class="row">
                                        <div class="col-12 pt-3 mb-3">
                                            <label for="lessonInfo" class="form-label text-med text-16">Exam
                                                Information</label>
                                            <input type="text"
                                                class="form-control textbox mb-3 p-2 text-reg text-14 text-muted"
                                                id="lessonInfo" aria-describedby="lessonInfo" placeholder="Task Title">
                                        </div>
                                    </div>

                                    <!-- Rich Text Editor -->
                                    <div class="row">
                                        <div class="col-12 mb-3">
                                            <div class="editor-wrapper">
                                                <div id="editor"></div>
                                                <div id="toolbar" class="row align-items-center p-2 p-md-4 g-2 g-md-5">
                                                    <div
                                                        class="col d-flex align-items-center px-2 px-md-4 gap-1 gap-md-3">
                                                        <button class="ql-bold"></button>
                                                        <button class="ql-italic"></button>
                                                        <button class="ql-underline"></button>
                                                        <button class="ql-list" value="bullet"></button>
                                                        <span id="word-counter"
                                                            class="ms-auto text-muted text-med text-16">0/120</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="hidden" name="task" id="task">
                                        </div>
                                    </div>

                                    <div class="row g-3">
                                        <div class="row g-3 mt-3">
                                            <!-- Deadline -->
                                            <div class="col-md-4">
                                                <label class="form-label text-med text-16">
                                                    Deadline
                                                </label>
                                                <span class="fst-italic text-reg text-12">Optional.</span>
                                                <div class="input-group" style="max-width: 320px;">
                                                    <input type="datetime-local"
                                                        class="form-control textbox text-reg text-14" />
                                                </div>
                                            </div>

                                            <!-- Time limit -->
                                            <div class="col-md-4">
                                                <label class="form-label text-med text-16">
                                                    Time Limit (minutes)
                                                </label>
                                                <span class="fst-italic text-reg text-12">Optional</span>
                                                <input type="number" class="form-control textbox text-reg text-14"
                                                    style="max-width: 320px;" placeholder="100" />
                                            </div>
                                        </div>

                                        <div class="form-check mt-2 col ms-2">
                                            <input class="form-check-input" type="checkbox" id="stopSubmissions"
                                                style="border: 1px solid var(--black);" />
                                            <label class="form-check-label" for="stopSubmissions">
                                                Stop accepting submissions after the deadline.
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Learning Materials -->
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="learning-materials">
                                                <label class="text-med text-16 mt-5">Exam Items</label>
                                            </div>
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
    </div>

    <!-- Quill JS -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script>
        var quill = new Quill('#editor', {
            theme: 'snow',
            placeholder: 'Task Instructions',
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