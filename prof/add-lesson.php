<?php $activePage = 'add-lesson'; ?>

<?php
include('../shared/assets/database/connect.php');

session_start();


if (!isset($_SESSION['userID'])) {
    header("Location: ../login.php"); //mali pa yan
    exit;
}

$userID = $_SESSION['userID'];

$course = "SELECT courseID, courseCode 
           FROM courses 
           WHERE userID = '$userID'";
$courses = executeQuery($course);

if (isset($_POST['save_lesson'])) {
    $title = $_POST['lessonTitle'];
    $content = $_POST['lessonContent'];
    $createdAt = date("Y-m-d H:i:s");
    $updatedAt = date("Y-m-d H:i:s");

    if (!empty($_POST['courses'])) {
        foreach ($_POST['courses'] as $selectedCourseID) {
            $lessons = "INSERT INTO lessons 
                (courseID, lessonTitle, lessonDescription, createdAt, updatedAt) 
                VALUES 
                ('$selectedCourseID', '$title', '$content', '$createdAt', '$updatedAt')";
            executeQuery($lessons);
        }
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webstar | Add Lesson</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="../shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="../shared/assets/css/post-announcement.css">
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
                                        <span class="text-sbold text-25">Add Lesson</span>
                                    </div>
                                </div>

                                <!-- Form starts -->
                                <form action="" method="POST" enctype="multipart/form-data">
                                    <div class="row">
                                        <div class="col-12 pt-3 mb-3">
                                            <label for="lessonInfo" class="form-label text-med text-16">Lesson
                                                Information</label>
                                            <input type="text"
                                                class="form-control form-control textbox mb-3 p-2 text-reg text-14 text-muted"
                                                id="lessonInfo" name="lessonTitle" placeholder="Lesson Title" required>
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
                                            <!-- Hidden input to capture editor content -->
                                            <input type="hidden" name="lessonContent" id="lesson">
                                        </div>
                                    </div>

                                    <!-- Learning Materials -->
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="learning-materials">
                                                <label class="text-med text-16 mt-4">Learning Materials</label>
                                                <span class="fst-italic text-reg text-14 ms-2">You can add up to 10
                                                    files or links.</span>

                                                <!-- Example Link Item -->
                                                <div class="row mb-0">
                                                    <div class="col">
                                                        <div class="row mb-0 mt-3">
                                                            <div class="col-12">
                                                                <div
                                                                    class="materials-card d-flex align-items-stretch p-2 w-100">
                                                                    <div
                                                                        class="d-flex w-100 align-items-center justify-content-between">
                                                                        <div
                                                                            class="d-flex align-items-center flex-grow-1">
                                                                            <div class="mx-4">
                                                                                <img src="../shared/assets/img/web.png"
                                                                                    alt="File Icon"
                                                                                    style="width: 20px; height: 20px;">
                                                                            </div>
                                                                            <div>
                                                                                <div class="text-sbold text-16 py-1"
                                                                                    style="line-height: 1;">
                                                                                    Web Development Tutorial
                                                                                </div>
                                                                                <div class="text-reg text-12 text-break"
                                                                                    style="line-height: 1; word-break: break-word;">
                                                                                    https://open.spotify.com/album/05c49JgPmL4Uz2ZeqRx5SP
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="mx-4">
                                                                            <img src="../shared/assets/img/trash.png"
                                                                                alt="Delete Icon"
                                                                                style="width: 12px; height: 16px;">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Example File Item -->
                                                        <div class="row mb-0 mt-3">
                                                            <div class="col-12">
                                                                <div
                                                                    class="materials-card d-flex align-items-stretch p-2 w-100">
                                                                    <div
                                                                        class="d-flex w-100 align-items-center justify-content-between">
                                                                        <div
                                                                            class="d-flex align-items-center flex-grow-1">
                                                                            <div class="mx-4">
                                                                                <i class="bi bi-file-earmark-fill"
                                                                                    style="font-size: 18px;"></i>
                                                                            </div>
                                                                            <div>
                                                                                <div class="text-sbold text-16 py-1"
                                                                                    style="line-height: 1;">
                                                                                    Web Development Course Material
                                                                                </div>
                                                                                <div class="text-reg text-12"
                                                                                    style="line-height: 1;">
                                                                                    PPTX · 2 MB
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="mx-4">
                                                                            <img src="../shared/assets/img/trash.png"
                                                                                alt="Delete Icon"
                                                                                style="width: 12px; height: 16px;">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <!-- Buttons -->
                                                            <div class="mt-3 mb-4 text-center text-md-start">
                                                                <button type="button"
                                                                    class="btn btn-sm px-3 py-1 rounded-pill text-reg text-md-14 mt-2 ms-2"
                                                                    style="background-color: var(--primaryColor); border: 1px solid var(--black);"
                                                                    onclick="document.getElementById('fileUpload').click()">
                                                                    <img src="../shared/assets/img/upload.png"
                                                                        alt="Upload Icon"
                                                                        style="width:12px; height:14px;" class="me-1">
                                                                    File
                                                                </button>
                                                                <input type="file" name="materials[]" id="fileUpload"
                                                                    class="d-none" multiple>

                                                                <button type="button"
                                                                    class="btn btn-sm px-3 py-1 rounded-pill text-reg text-md-14 mt-2 ms-2"
                                                                    style="background-color: var(--primaryColor); border: 1px solid var(--black);"
                                                                    data-bs-container="body" data-bs-toggle="popover"
                                                                    data-bs-placement="right" data-bs-html="true"
                                                                    data-bs-content='<div class="form-floating mb-3"><input type="url" class="form-control" id="linkInput" placeholder="Paste link here"><label for="linkInput">Link</label></div><div class="link-popover-actions"><button type="button" class="btn btn-sm px-3 py-1 rounded-pill" id="addLinkBtn" style="background-color: var(--primaryColor); border: 1px solid var(--black); color: var(--black);">Add link</button></div>'>
                                                                    <img src="../shared/assets/img/link.png"
                                                                        alt="Upload Icon" class="me-1">
                                                                    Link
                                                                </button>
                                                                <!-- Hidden input to store added links -->
                                                                <input type="hidden" name="links[]" id="lessonLinks">
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Course selection + Post button -->
                                                <div class="row align-items-center mb-5 text-center text-md-start">
                                                    <div
                                                        class="col-12 col-md-auto mt-3 d-flex justify-content-center justify-content-md-start">
                                                        <div class="d-flex align-items-center flex-nowrap">
                                                            <span class="me-2 text-med text-16 pe-3">Add to
                                                                Course</span>
                                                            <button
                                                                class="btn dropdown-toggle dropdown-shape text-med text-16 me-md-5"
                                                                type="button" data-bs-toggle="dropdown"
                                                                aria-expanded="false">
                                                                <span>Select Course</span>
                                                            </button>
                                                            <ul class="dropdown-menu p-2" style="min-width: 200px;">
                                                                <?php
                                                                if ($courses && $courses->num_rows > 0) {
                                                                    while ($course = $courses->fetch_assoc()) {
                                                                        ?>
                                                                        <li>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="checkbox"
                                                                                    name="courses[]"
                                                                                    value="<?php echo htmlspecialchars($course['courseID']); ?>"
                                                                                    id="course<?php echo $course['courseID']; ?>">
                                                                                <label class="form-check-label text-reg"
                                                                                    for="course<?php echo $course['courseID']; ?>">
                                                                                    <?php echo htmlspecialchars($course['courseCode']); ?>
                                                                                </label>
                                                                            </div>
                                                                        </li>
                                                                        <?php
                                                                    }
                                                                } else {
                                                                    ?>
                                                                    <li><span class="dropdown-item-text text-muted">No
                                                                            courses found</span></li>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                    <!-- Add Button -->
                                                    <div
                                                        class="col-md-6 text-center text-md-center mt-3 mt-md-0 ms-md-5">
                                                        <button type="submit" name="save_lesson"
                                                            class="px-4 py-2 rounded-pill text-sbold text-md-14 mt-3 ms-3"
                                                            style="background-color: var(--primaryColor); border: 1px solid var(--black);">
                                                            Add
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <!-- End Form -->
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
            placeholder: 'Lesson Description / Objectives',
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
                quill.setSelection(quill.getLength());
            }

            counter.textContent = `${Math.min(words, maxWords)}/${maxWords}`;
        });

        // Sync editor content before form submit
        document.querySelector('form').addEventListener('submit', function () {
            document.querySelector('#lesson').value = quill.root.innerHTML;
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var triggers = document.querySelectorAll('[data-bs-toggle="popover"]');
            triggers.forEach(function (el) {
                var pop = new bootstrap.Popover(el, {
                    html: true,
                    container: 'body',
                    sanitize: false
                });
                el.addEventListener('shown.bs.popover', function () {
                    var tip = document.querySelector('.popover.show');
                    if (tip) {
                        tip.classList.add('link-popover');
                    }
                });
            });
        });
    </script>

</body>

</html>