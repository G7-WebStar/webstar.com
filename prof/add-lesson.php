<?php $activePage = 'add-lesson'; ?>
<?php
include('../shared/assets/database/connect.php');
date_default_timezone_set('Asia/Manila');

include("../shared/assets/processes/prof-session-process.php");

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

            // Get the actual last inserted ID from MySQL
            $lessonID = mysqli_insert_id($conn);

            if (!empty($_FILES['materials']['name'][0])) {
                $uploadDir = __DIR__ . "/../shared/assets/files/";

                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                foreach ($_FILES['materials']['name'] as $key => $fileName) {
                    $tmpName = $_FILES['materials']['tmp_name'][$key];
                    $fileError = $_FILES['materials']['error'][$key];

                    if ($fileError === UPLOAD_ERR_OK) {
                        $safeName = str_replace(" ", "_", basename($fileName));
                        $targetPath = $uploadDir . $safeName;

                        if (move_uploaded_file($tmpName, $targetPath)) {
                            // For uploaded files
                            $insertFile = "INSERT INTO files 
                            (courseID, userID, lessonID, fileAttachment, fileLink) 
                            VALUES 
                            ('$selectedCourseID', '$userID', '$lessonID', '$safeName', '')";
                            executeQuery($insertFile);
                        }
                    }
                }
            }

            // Handle file links
            if (!empty($_POST['links'])) {
                $links = $_POST['links'];
                if (is_array($links)) {
                    foreach ($links as $link) {
                        $link = trim($link);
                        if ($link !== '') {
                            // Fetch link title using get_meta_tags or fallback to <title> tag
                            $title = '';
                            $metaTags = @get_meta_tags($link);
                            if (!empty($metaTags['title'])) {
                                $title = $metaTags['title'];
                            } else {
                                $html = @file_get_contents($link);
                                if ($html && preg_match("/<title>(.*?)<\/title>/i", $html, $matches)) {
                                    $title = $matches[1];
                                }
                            }

                            $insertLink = "INSERT INTO files 
                            (courseID, userID, lessonID, fileAttachment, fileTitle, fileLink) 
                            VALUES 
                            ('$selectedCourseID', '$userID', '$lessonID', '', '$title', '$link')";
                            executeQuery($insertLink);
                        }
                    }
                }
            }
        }
    }
}

//Fetch the Title of link
if (isset($_GET['fetchTitle'])) {
    $url = $_GET['fetchTitle'];
    $title = $url;

    // Get HTML content
    $context = stream_context_create([
        "http" => ["header" => "User-Agent: Mozilla/5.0"]
    ]);
    $html = @file_get_contents($url, false, $context);

    if ($html !== false) {
        if (preg_match('/<meta property="og:title" content="([^"]+)"/i', $html, $matches)) {
            $title = $matches[1];
        } elseif (preg_match("/<title>(.*?)<\/title>/i", $html, $matches)) {
            $title = $matches[1];
        }
    }

    echo json_encode(["title" => $title]);
    exit;
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

    <!-- Material Design Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp" />

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
                                                <label class="text-med text-16 mt-4">Attachments</label>
                                                <span class="fst-italic text-reg text-14 ms-2">You can add up to 10
                                                    files or links.</span>

                                                <!-- Container for dynamically added file & link cards -->
                                                <div id="filePreviewContainer" class="row mb-0 mt-3"></div>

                                                <!-- Upload buttons -->
                                                <div class="mt-3 mb-4 text-center text-md-start">
                                                    <input type="file" name="materials[]" class="d-none" id="fileUpload"
                                                        multiple>
                                                    <button type="button"
                                                        class="btn btn-sm px-3 py-1 rounded-pill text-reg text-md-14 mt-2 ms-2"
                                                        style="background-color: var(--primaryColor); border: 1px solid var(--black);"
                                                        onclick="document.getElementById('fileUpload').click();">
                                                        <img src="../shared/assets/img/upload.png" alt="Upload Icon"
                                                            style="width:12px; height:14px;" class="me-1">
                                                        File
                                                    </button>

                                                    <button type="button"
                                                        class="btn btn-sm px-3 py-1 rounded-pill text-reg text-md-14 mt-2 ms-2"
                                                        style="background-color: var(--primaryColor); border: 1px solid var(--black);"
                                                        data-bs-container="body" data-bs-toggle="popover"
                                                        data-bs-placement="right" data-bs-html="true"
                                                        data-bs-content='<div class="form-floating mb-3"><input type="url" class="form-control" id="linkInput" placeholder="Paste link here"><label for="linkInput">Link</label></div><div class="link-popover-actions"><button type="button" class="btn btn-sm px-3 py-1 rounded-pill" id="addLinkBtn" style="background-color: var(--primaryColor); border: 1px solid var(--black); color: var(--black);">Add link</button></div>'>
                                                        <img src="../shared/assets/img/link.png" alt="Upload Icon"
                                                            class="me-1">
                                                        Link
                                                    </button>

                                                    <!-- Hidden input to store added links -->
                                                    <input type="hidden" name="links[]" id="lessonLinks">
                                                </div>
                                            </div>

                                            <!-- Course selection + Post button -->
                                            <div class="row align-items-center mb-5 text-center text-md-start">
                                                <div
                                                    class="col-12 col-md-auto mt-3 d-flex justify-content-center justify-content-md-start">
                                                    <div class="d-flex align-items-center flex-nowrap">
                                                        <span class="me-2 text-med text-16 pe-3">Add to Course</span>
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
                                                                            <input class="form-check-input course-checkbox"
                                                                                type="checkbox" name="courses[]"
                                                                                value="<?php echo $course['courseID']; ?>"
                                                                                id="course<?php echo $course['courseID']; ?>">
                                                                            <label class="form-check-label text-reg"
                                                                                for="course<?php echo $course['courseID']; ?>">
                                                                                <?php echo $course['courseCode']; ?>
                                                                            </label>
                                                                        </div>
                                                                    </li>
                                                                <?php
                                                                }
                                                            } else {
                                                                ?>
                                                                <li><span class="dropdown-item-text text-muted">No courses
                                                                        found</span></li>
                                                            <?php
                                                            }
                                                            ?>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <!-- Add Button -->
                                                <div class="col-md-6 text-center text-md-center mt-3 mt-md-0 ms-md-5">
                                                    <button type="submit" name="save_lesson"
                                                        class="px-4 py-2 rounded-pill text-sbold text-md-14 mt-3 ms-3"
                                                        style="background-color: var(--primaryColor); border: 1px solid var(--black);">
                                                        Add
                                                    </button>
                                                </div>
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

    <!-- Quill JS -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script>
        var icons = Quill.import("ui/icons");

        // Custom upload icon
        icons['upload'] = '<svg viewBox="0 0 18 18">' +
            '<line class="ql-stroke" x1="9" x2="9" y1="15" y2="3"></line>' +
            '<polyline class="ql-stroke" points="5 7 9 3 13 7"></polyline>' +
            '<rect class="ql-fill" height="2" width="12" x="3" y="15"></rect>' +
            '</svg>';

        // Initialize Quill editor
        var quill = new Quill('#editor', {
            theme: 'snow',
            placeholder: 'Lesson Description / Objectives',
            modules: {
                toolbar: '#toolbar'
            }
        });

        // Word counter
        const maxWords = 120;
        const counter = document.getElementById("word-counter");

        quill.on('text-change', function() {
            let text = quill.getText().trim();
            let words = text.length > 0 ? text.split(/\s+/).length : 0;

            if (words > maxWords) {
                let limited = text.split(/\s+/).slice(0, maxWords).join(" ");
                quill.setText(limited + " ");
                quill.setSelection(quill.getLength());
            }

            counter.textContent = `${Math.min(words, maxWords)}/${maxWords}`;
        });

        // Sync Quill content to hidden input before form submit
        document.querySelector('form').addEventListener('submit', function() {
            let html = quill.root.innerHTML;
            html = html.replace(/<p>/g, '').replace(/<\/p>/g, '<br>');
            html = html.replace(/<li>/g, '• ').replace(/<\/li>/g, '<br>');
            html = html.replace(/<\/?(ul|ol)>/g, '');
            html = html.replace(/(<br>)+$/g, '');
            document.querySelector('#lesson').value = html.trim();
        });

        // Ensure at least one course is selected
        document.querySelector("form").addEventListener("submit", function(e) {
            let checkboxes = document.querySelectorAll(".course-checkbox");
            let checked = Array.from(checkboxes).some(cb => cb.checked);
            if (!checked) {
                e.preventDefault();
                alert("Please select at least one course before submitting.");
            }
        });

        // File & Link preview logic with total limit of 10
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('fileUpload');
            const container = document.getElementById('filePreviewContainer');

            // Link popovers
            const popovers = document.querySelectorAll('[data-bs-toggle="popover"]');
            popovers.forEach(el => {
                new bootstrap.Popover(el, {
                    html: true,
                    sanitize: false
                });

                el.addEventListener('shown.bs.popover', function() {
                    const tip = document.querySelector('.popover.show');
                    if (tip) tip.classList.add('link-popover');

                    const addLinkBtn = tip.querySelector('#addLinkBtn');
                    const linkInput = tip.querySelector('#linkInput');

                    addLinkBtn.addEventListener('click', function() {
                        const linkValue = linkInput.value.trim();
                        if (!linkValue) return;

                        // Check total attachments limit
                        const totalAttachments = container.querySelectorAll('.col-12').length;
                        if (totalAttachments >= 10) {
                            alert("You can only add up to 10 files or links total.");
                            return;
                        }

                        // Get domain and favicon for link icon
                        const urlObj = new URL(linkValue);
                        const domain = urlObj.hostname;
                        const faviconURL = `https://www.google.com/s2/favicons?sz=64&domain=${domain}`;

                        // Unique ID for link preview elements
                        const uniqueID = Date.now();
                        let displayTitle = "Loading...";

                        // Create link preview HTML
                        const previewHTML = `
                            <div class="col-12 mt-2" data-id="${uniqueID}">
                                <div class="materials-card d-flex align-items-stretch p-2 w-100">
                                    <div class="d-flex w-100 align-items-center justify-content-between">
                                        <div class="d-flex align-items-center flex-grow-1">
                                            <div class="mx-4">
                                                <img src="${faviconURL}" alt="${domain} Icon" 
                                                    onerror="this.onerror=null;this.src='../shared/assets/img/web.png';" 
                                                    style="width: 30px; height: 30px;">
                                            </div>
                                            <div>
                                                <div id="title-${uniqueID}" class="text-sbold text-16 py-1">${displayTitle}</div>
                                                <div class="text-reg text-12 text-break">
                                                    <a href="${linkValue}" target="_blank">${linkValue}</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mx-4 delete-file" style="cursor:pointer;">
                                            <img src="../shared/assets/img/trash.png" alt="Delete Icon">
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="links[]" value="${linkValue}" class="link-hidden">
                            </div>
                            `;
                        container.innerHTML += previewHTML;

                        // Fetch real page title for link
                        fetch("?fetchTitle=" + encodeURIComponent(linkValue))
                            .then(res => res.json())
                            .then(data => {
                                const titleEl = document.getElementById(`title-${uniqueID}`);
                                if (titleEl) titleEl.textContent = data.title || linkValue;
                            }).catch(() => {
                                const titleEl = document.getElementById(`title-${uniqueID}`);
                                if (titleEl) titleEl.textContent = linkValue.split('/').pop() || "Link";
                            });

                        // Delete handler for link
                        container.querySelectorAll('.delete-file').forEach((btn) => {
                            btn.addEventListener('click', function() {
                                const col = this.closest('.col-12');
                                col.remove();
                            });
                        });

                        linkInput.value = '';
                        const popover = bootstrap.Popover.getInstance(el);
                        popover.hide();
                    });
                });
            });

            // File input change
            fileInput.addEventListener('change', function(event) {
                const currentCount = container.querySelectorAll('.col-12').length;
                const incomingCount = event.target.files.length;

                // Limit check
                if (currentCount + incomingCount > 10) {
                    alert("You can only add up to 10 files or links total.");
                    fileInput.value = '';
                    return;
                }

                let previewHTML = "";
                Array.from(event.target.files).forEach((file, index) => {
                    const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
                    const ext = file.name.split('.').pop().toUpperCase();

                    // File preview HTML with icon
                    previewHTML += `
                        <div class="col-12 mt-2">
                            <div class="materials-card d-flex align-items-stretch p-2 w-100">
                                <div class="d-flex w-100 align-items-center justify-content-between">
                                    <div class="d-flex align-items-center flex-grow-1">
                                        <div class="mx-4">
                                            <i class="bi bi-file-earmark-fill" style="font-size: 22px;"></i>
                                        </div>
                                        <div>
                                            <div class="text-sbold text-16 py-1">${file.name}</div>
                                            <div class="text-reg text-12">${ext} · ${fileSizeMB} MB</div>
                                        </div>
                                    </div>
                                    <div class="mx-4 delete-file" style="cursor:pointer;">
                                        <img src="../shared/assets/img/trash.png" alt="Delete Icon">
                                    </div>
                                </div>
                            </div>
                        </div>`;

                });

                container.innerHTML += previewHTML;

                // Delete handler for files
                container.querySelectorAll('.delete-file').forEach((btn, idx) => {
                    btn.addEventListener('click', function() {
                        let dt = new DataTransfer();
                        Array.from(fileInput.files).forEach((f, i) => {
                            if (i !== idx) dt.items.add(f);
                        });
                        fileInput.files = dt.files;
                        this.closest('.col-12').remove();
                    });
                });
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>