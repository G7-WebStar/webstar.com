<?php $activePage = 'assign-task'; ?>
<?php
include('../shared/assets/database/connect.php');
include("../shared/assets/processes/prof-session-process.php");

$course = "SELECT courseID, courseCode 
           FROM courses 
           WHERE userID = '$userID'";
$courses = executeQuery($course);

if (isset($_POST['saveAssignment'])) {
    $title = $_POST['assignmentTitle'];
    $content = $_POST['assignmentContent'];
    $assignmentDeadline = !empty($_POST['deadline']) ? $_POST['deadline'] : null;
    $assignmentPoints = !empty($_POST['points']) ? $_POST['points'] : 0;
    $createdAt = date("Y-m-d H:i:s");

    if (!empty($_POST['courses'])) {
        foreach ($_POST['courses'] as $selectedCourseID) {

            $deadlineEnabled = isset($_POST['stopSubmissions']) ? 1 : 0;

            $insertAssessment = "INSERT INTO assessments 
            (courseID, assessmentTitle, type, deadline, deadlineEnabled, createdAt)
            VALUES 
            ('$selectedCourseID', '$title', 'task', " .
                ($assignmentDeadline ? "'$assignmentDeadline'" : "NULL") . ", '$deadlineEnabled', '$createdAt')";
            executeQuery($insertAssessment);

            // Retrieve assessmentID based on unique data
            $assessmentID = mysqli_insert_id($conn);

            // Insert into assignments (linked to assessmentID)
            $insertAssignment = "INSERT INTO assignments 
            (assessmentID,  assignmentDescription, assignmentPoints)
            VALUES 
            ('$assessmentID', '$content', '$assignmentPoints')";
            executeQuery($insertAssignment);

            // Get assignmentID right after inserting into assignments
            $assignmentID = mysqli_insert_id($conn);

            // Then insert into todo
            $insertTodo = "INSERT INTO todo 
            (userID, assessmentID, title, status, isRead)
            VALUES 
            ('$userID', '$assessmentID', '$title', 'Pending',  0)";
            executeQuery($insertTodo);


            // Handle file uploads
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
                            $insertFile = "INSERT INTO files 
                            (courseID, userID, assignmentID, fileAttachment, fileLink) 
                            VALUES 
                            ('$selectedCourseID', '$userID', '$assignmentID', '$safeName', '')";
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

                            // Try to fetch link title
                            $fileTitle = $link;
                            $context = stream_context_create([
                                "http" => ["header" => "User-Agent: Mozilla/5.0"]
                            ]);
                            $html = @file_get_contents($link, false, $context);

                            if ($html !== false) {
                                if (preg_match('/<meta property="og:title" content="([^"]+)"/i', $html, $matches)) {
                                    $fileTitle = $matches[1];
                                } elseif (preg_match("/<title>(.*?)<\/title>/i", $html, $matches)) {
                                    $fileTitle = $matches[1];
                                }
                            }

                            // Insert with title
                            $insertLink = "INSERT INTO files 
                            (courseID, userID, assignmentID, fileAttachment, fileTitle, fileLink) 
                            VALUES 
                            ('$selectedCourseID', '$userID', '$assignmentID', '', '" . mysqli_real_escape_string($conn, $fileTitle) . "', '$link')";
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
    <title>Webstar | Assign Task</title>
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
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,1,0"
        rel="stylesheet" />


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
                                        <span class="text-sbold text-25">Assign Task</span>
                                    </div>
                                </div>

                                <!-- Form starts -->
                                <form action="" method="POST" enctype="multipart/form-data">
                                    <div class="row">
                                        <div class="col-12 pt-3 mb-3">
                                            <label for="taskInfo" class="form-label text-med text-16">Task
                                                Information</label>
                                            <input type="text"
                                                class="form-control textbox mb-3 p-2 text-reg text-14 text-muted"
                                                id="taskInfo" name="assignmentTitle" placeholder="Task Title" required>
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
                                            <input type="hidden" name="assignmentContent" id="task">
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
                                                        class="form-control textbox text-reg text-14" name="deadline">
                                                </div>
                                            </div>

                                            <!-- Points -->
                                            <div class="col-md-4">
                                                <label class="form-label text-med text-16">
                                                    Points
                                                </label>
                                                <span class="fst-italic text-reg text-12">Optional. Ungraded if left
                                                    blank</span>
                                                <input type="number" class="form-control textbox text-reg text-14"
                                                    style="max-width: 320px;" name="points" placeholder="100" />
                                            </div>
                                        </div>

                                        <div class="form-check mt-2 col ms-2">
                                            <input class="form-check-input" type="checkbox" id="stopSubmissions"
                                                name="stopSubmissions" value="1"
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
                                                <label class="text-med text-16 mt-4">Attachments</label>
                                                <span class="fst-italic text-reg text-14 ms-2">You can add up to 10
                                                    files or links.</span>

                                                <!-- Container for dynamically added file & link cards -->
                                                <div id="filePreviewContainer" class="row mb-0 mt-3"></div>

                                                <!-- Upload buttons -->
                                                <div class="mt-2 mb-4 text-center text-md-start">
                                                    <input type="file" name="materials[]" class="d-none" id="fileUpload"
                                                        multiple>
                                                    <button type="button"
                                                        class="btn btn-sm px-3 py-1 rounded-pill text-reg text-md-14 mt-2"
                                                        style="background-color: var(--primaryColor); border: 1px solid var(--black);"
                                                        onclick="document.getElementById('fileUpload').click();">
                                                        <div style="display: flex; align-items: center; gap: 5px;">
                                                            <span class="material-symbols-rounded"
                                                                style="font-size:16px">upload</span>
                                                            <span>Upload</span>
                                                        </div>
                                                    </button>

                                                    <button type="button"
                                                        class="btn btn-sm px-3 py-1 rounded-pill text-reg text-md-14 mt-2 ms-2"
                                                        style="background-color: var(--primaryColor); border: 1px solid var(--black);"
                                                        data-bs-container="body" data-bs-toggle="popover"
                                                        data-bs-placement="right" data-bs-html="true"
                                                        data-bs-content='<div class="form-floating mb-3"><input type="url" class="form-control" id="linkInput" placeholder="Paste link here"><label for="linkInput">Link</label></div><div class="link-popover-actions"><button type="button" class="btn btn-sm px-3 py-1 rounded-pill" id="addLinkBtn" style="background-color: var(--primaryColor); border: 1px solid var(--black); color: var(--black);">Add link</button></div>'>
                                                        <div style="display: flex; align-items: center; gap: 5px;">
                                                            <span class="material-symbols-rounded"
                                                                style="font-size:20px">link</span>
                                                            <span>Link</span>
                                                        </div>
                                                    </button>

                                                    <!-- Hidden input to store added links -->
                                                    <input type="hidden" name="links[]" id="taskLinks">
                                                </div>
                                            </div>

                                            <!-- Rubrics -->
                                            <div class="row mb-0">
                                                <div class="col">
                                                    <label class="text-med text-16 mt-3">Rubric</label>
                                                    <span class="fst-italic text-reg text-12 ms-2">Optional. Any
                                                        points entered above will be replaced by the rubric’s total
                                                        points.</span>
                                                    <div class="row mb-0 mt-3">
                                                        <div class="col-12">
                                                            <!-- Rubric Card -->
                                                            <div
                                                                class="materials-card d-flex align-items-stretch px-2 py-2 w-100 rounded-3">
                                                                <div
                                                                    class="d-flex w-100 align-items-center justify-content-between">
                                                                    <div class="d-flex align-items-center flex-grow-1">
                                                                        <div class="mx-3 d-flex align-items-center">
                                                                            <span class="material-symbols-rounded">
                                                                                rate_review
                                                                            </span>
                                                                        </div>
                                                                        <div>
                                                                            <div class="text-sbold text-16"
                                                                                style="line-height: 1.5;">
                                                                                Essay Rubric
                                                                            </div>
                                                                            <div class="text-reg text-12 text-break"
                                                                                style="line-height: 1.5;">
                                                                                20 Points · 2 Criteria
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="mx-3 d-flex align-items-center">
                                                                        <span
                                                                            class="material-symbols-outlined">close</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- Buttons -->
                                                    <div class="mt-2 mb-4 text-center text-md-start">
                                                        <!-- Select Rubric Button -->
                                                        <button type="button"
                                                            class="btn btn-sm px-3 py-1 rounded-pill text-reg text-md-14 mt-2"
                                                            style="background-color: var(--primaryColor); border: 1px solid var(--black);"
                                                            data-bs-toggle="modal" data-bs-target="#selectRubricModal">
                                                            <div style="display: flex; align-items: center; gap: 5px;">
                                                                <span class="material-symbols-outlined"
                                                                    style="font-size:16px">add_circle</span>
                                                                <span>Rubric</span>
                                                            </div>
                                                        </button>
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
                                                                                    <input
                                                                                        class="form-check-input course-checkbox"
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
                                                                        <li><span class="dropdown-item-text text-muted">No
                                                                                courses
                                                                                found</span></li>
                                                                        <?php
                                                                    }
                                                                    ?>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                        <!-- Assign Button -->
                                                        <div
                                                            class="col-md-6 text-center text-md-center mt-3 mt-md-0 ms-md-3">
                                                            <button type="submit" name="saveAssignment"
                                                                class="px-4 py-2 rounded-pill text-sbold text-md-14 mt-3 ms-3"
                                                                style="background-color: var(--primaryColor); border: 1px solid var(--black);">
                                                                Assign
                                                            </button>
                                                        </div>
                                                    </div>
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

    <!-- Select Rubric Modal -->
    <div class="modal fade" id="selectRubricModal" tabindex="-1" aria-labelledby="selectRubricModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered py-4 " style="max-width: 700px;  height: 80vh">
            <div class="modal-content d-flex flex-column" style="height: 100%;">

                <!-- HEADER -->
                <div class="modal-header flex-shrink-0">
                    <div class="modal-title text-sbold text-20 ms-3" id="selectRubricModalLabel">Select Rubric</div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        style="transform: scale(0.8); filter: grayscale(100%);"></button>
                </div>

                <!-- DESC -->
                <div class="modal-body flex-grow-1 overflow-auto">
                    <p class="mb-3 text-med text-14 ms-3" style="color: var(--black);">
                        Choose from preset rubrics, create your own, or edit an existing one.
                    </p>
                    <div class="col p-0 m-3">
                        <div class="card rounded-3 mb-2 border-0">
                            <div class="card-body p-0">
                                <!-- OPTIONS -->
                                <div class="rubric-option rounded-3 d-flex align-items-center justify-content-between mb-2"
                                    style="cursor: pointer; background-color: var(--pureWhite); border: 1px solid var(--black);">
                                    <div
                                        style="line-height: 1.5; padding-left:15px; padding-right:15px; padding-top:10px; padding-bottom:10px">
                                        <div class="text-sbold text-14">Essay Rubric</div>
                                        <div class="text-med text-12">
                                            20 Points · 2 Criteria
                                        </div>
                                    </div>
                                    <span class="material-symbols-rounded"
                                        style="font-variation-settings: 'FILL' 1; padding-right:15px">
                                        edit
                                    </span>
                                </div>
                                <div class="rubric-option rounded-3 d-flex align-items-center justify-content-between mb-2"
                                    style="cursor: pointer; background-color: var(--primaryColor); border: 1px solid var(--black);">
                                    <div
                                        style="line-height: 1.5; padding-left:15px; padding-right:15px; padding-top:10px; padding-bottom:10px">
                                        <div class="text-sbold text-14">Essay Rubric</div>
                                        <div class="text-med text-12">
                                            20 Points · 2 Criteria
                                        </div>
                                    </div>
                                    <span class="material-symbols-rounded"
                                        style="font-variation-settings: 'FILL' 1; padding-right:15px">
                                        edit
                                    </span>
                                </div>
                                <div class="rubric-option rounded-3 d-flex align-items-center justify-content-between mb-2"
                                    style="cursor: pointer; background-color: var(--pureWhite); border: 1px solid var(--black);">
                                    <div
                                        style="line-height: 1.5; padding-left:15px; padding-right:15px; padding-top:10px; padding-bottom:10px">
                                        <div class="text-sbold text-14">Essay Rubric</div>
                                        <div class="text-med text-12">
                                            20 Points · 2 Criteria
                                        </div>
                                    </div>
                                    <span class="material-symbols-rounded"
                                        style="font-variation-settings: 'FILL' 1; padding-right:15px">
                                        edit
                                    </span>
                                </div>
                                <div class="rubric-option rounded-3 d-flex align-items-center justify-content-between mb-2"
                                    style="cursor: pointer; background-color: var(--pureWhite); border: 1px solid var(--black);">
                                    <div
                                        style="line-height: 1.5; padding-left:15px; padding-right:15px; padding-top:10px; padding-bottom:10px">
                                        <div class="text-sbold text-14">Essay Rubric</div>
                                        <div class="text-med text-12">
                                            20 Points · 2 Criteria
                                        </div>
                                    </div>
                                    <span class="material-symbols-rounded"
                                        style="font-variation-settings: 'FILL' 1; padding-right:15px">
                                        edit
                                    </span>
                                </div>
                                <div class="rubric-option rounded-3 d-flex align-items-center justify-content-between mb-2"
                                    style="cursor: pointer; background-color: var(--pureWhite); border: 1px solid var(--black);">
                                    <div
                                        style="line-height: 1.5; padding-left:15px; padding-right:15px; padding-top:10px; padding-bottom:10px">
                                        <div class="text-sbold text-14">Essay Rubric</div>
                                        <div class="text-med text-12">
                                            20 Points · 2 Criteria
                                        </div>
                                    </div>
                                    <span class="material-symbols-rounded"
                                        style="font-variation-settings: 'FILL' 1; padding-right:15px">
                                        edit
                                    </span>
                                </div>
                                <div class="rubric-option rounded-3 d-flex align-items-center justify-content-between mb-2"
                                    style="cursor: pointer; background-color: var(--pureWhite); border: 1px solid var(--black);">
                                    <div
                                        style="line-height: 1.5; padding-left:15px; padding-right:15px; padding-top:10px; padding-bottom:10px">
                                        <div class="text-sbold text-14">Essay Rubric</div>
                                        <div class="text-med text-12">
                                            20 Points · 2 Criteria
                                        </div>
                                    </div>
                                    <span class="material-symbols-rounded"
                                        style="font-variation-settings: 'FILL' 1; padding-right:15px">
                                        edit
                                    </span>
                                </div>
                                <div class="rubric-option rounded-3 d-flex align-items-center justify-content-between mb-2"
                                    style="cursor: pointer; background-color: var(--pureWhite); border: 1px solid var(--black);">
                                    <div
                                        style="line-height: 1.5; padding-left:15px; padding-right:15px; padding-top:10px; padding-bottom:10px">
                                        <div class="text-sbold text-14">Essay Rubric</div>
                                        <div class="text-med text-12">
                                            20 Points · 2 Criteria
                                        </div>
                                    </div>
                                    <span class="material-symbols-rounded"
                                        style="font-variation-settings: 'FILL' 1; padding-right:15px">
                                        edit
                                    </span>
                                </div>
                                <!-- Select Rubric Button -->
                                <button type="button" class="btn btn-sm px-3 py-1 rounded-pill text-reg text-md-14"
                                    style="background-color: var(--primaryColor); border: 1px solid var(--black);">
                                    <div style="display: flex; align-items: center; gap: 5px;">
                                        <span class="material-symbols-outlined" style="font-size:16px">add_circle</span>
                                        <span>Create</span>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FOOTER -->
                <div class="modal-footer border-top flex-shrink-0">

                    <!-- BUTTON -->
                    <button type="submit" class="btn rounded-5 px-4 text-sbold text-14 me-3"
                        style="background-color: var(--primaryColor); border: 1px solid var(--black);">
                        Select
                    </button>

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
                quill.setSelection(quill.getLength());
            }

            counter.textContent = `${Math.min(words, maxWords)}/${maxWords}`;
        });

        // Sync Quill content to hidden input before form submit
        document.querySelector('form').addEventListener('submit', function () {
            let html = quill.root.innerHTML;
            html = html.replace(/<p>/g, '').replace(/<\/p>/g, '<br>');
            html = html.replace(/<li>/g, '• ').replace(/<\/li>/g, '<br>');
            html = html.replace(/<\/?(ul|ol)>/g, '');
            html = html.replace(/(<br>)+$/g, '');
            document.querySelector('#task').value = html.trim();
        });

        // Ensure at least one course is selected
        document.querySelector("form").addEventListener("submit", function (e) {
            let checkboxes = document.querySelectorAll(".course-checkbox");
            let checked = Array.from(checkboxes).some(cb => cb.checked);
            if (!checked) {
                e.preventDefault();
                alert("Please select at least one course before submitting.");
            }
        });

        // File & Link preview logic with total limit of 10
        document.addEventListener('DOMContentLoaded', function () {
            const fileInput = document.getElementById('fileUpload');
            const container = document.getElementById('filePreviewContainer');
            let allFiles = [];

            // Link popovers
            const popovers = document.querySelectorAll('[data-bs-toggle="popover"]');
            popovers.forEach(el => {
                new bootstrap.Popover(el, { html: true, sanitize: false });

                el.addEventListener('shown.bs.popover', function () {
                    const tip = document.querySelector('.popover.show');
                    if (tip) tip.classList.add('link-popover');

                    const addLinkBtn = tip.querySelector('#addLinkBtn');
                    const linkInput = tip.querySelector('#linkInput');

                    addLinkBtn.addEventListener('click', function () {
                        const linkValue = linkInput.value.trim();
                        if (!linkValue) return;

                        // Check total attachments limit
                        const totalAttachments = container.querySelectorAll('.col-12').length;
                        if (totalAttachments >= 10) {
                            alert("You can only add up to 10 files or links total.");
                            return;
                        }

                        // Get domain and favicon
                        const urlObj = new URL(linkValue);
                        const domain = urlObj.hostname;
                        const faviconURL = `https://www.google.com/s2/favicons?sz=64&domain=${domain}`;

                        const uniqueID = Date.now();
                        let displayTitle = "Loading...";

                        const previewHTML = `
                    <div class="col-12 mt-2" data-id="${uniqueID}">
                        <div class="materials-card d-flex align-items-stretch p-2 w-100 rounded-3">
                            <div class="d-flex w-100 align-items-center justify-content-between">
                                <div class="d-flex align-items-center flex-grow-1">
                                    <div class="mx-3 d-flex align-items-center">
                                        <img src="${faviconURL}" alt="${domain} Icon" 
                                            onerror="this.onerror=null;this.src='../shared/assets/img/web.png';" 
                                            style="width: 30px; height: 30px;">
                                    </div>
                                    <div>
                                        <div id="title-${uniqueID}" class="text-sbold text-16" style="line-height: 1.5;">${displayTitle}</div>
                                        <div class="text-reg text-12 text-break" style="line-height: 1.5;">
                                            <a href="${linkValue}" target="_blank">${linkValue}</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="mx-3 d-flex align-items-center delete-file" style="cursor:pointer;">
                                    <span
                                    class="material-symbols-outlined">close</span>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="links[]" value="${linkValue}" class="link-hidden">
                    </div>
                `;

                
                        container.insertAdjacentHTML('beforeend', previewHTML);

                        // Fetch page title
                        fetch("?fetchTitle=" + encodeURIComponent(linkValue))
                            .then(res => res.json())
                            .then(data => {
                                const titleEl = document.getElementById(`title-${uniqueID}`);
                                if (titleEl) titleEl.textContent = data.title || linkValue;
                            }).catch(() => {
                                const titleEl = document.getElementById(`title-${uniqueID}`);
                                if (titleEl) titleEl.textContent = linkValue.split('/').pop() || "Link";
                            });

                        // Delete handler
                        container.querySelectorAll('.delete-file').forEach((btn) => {
                            btn.addEventListener('click', function () {
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
            fileInput.addEventListener('change', function (event) {
                // Merge new selections with existing files
                let dt = new DataTransfer();
                Array.from(allFiles).forEach(f => dt.items.add(f));
                Array.from(event.target.files).forEach(f => dt.items.add(f));
                fileInput.files = dt.files;
                allFiles = Array.from(fileInput.files); // update allFiles list

                // 🔹 Remove only file previews, keep links
                container.querySelectorAll('.file-preview').forEach(el => el.remove());

                // Rebuild file previews
                allFiles.forEach((file, index) => {
                    const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
                    const ext = file.name.split('.').pop().toUpperCase();
                    const fileHTML = `
                <div class="col-12 mt-2 file-preview">
                    <div class="materials-card d-flex align-items-stretch p-2 w-100 rounded-3">
                        <div class="d-flex w-100 align-items-center justify-content-between">
                            <div class="d-flex align-items-center flex-grow-1">
                                <div class="mx-3 d-flex align-items-center">
                                    <span class="material-symbols-rounded">
                                        description
                                    </span>
                                </div>
                                <div>
                                    <div class="text-sbold text-16" style="line-height: 1.5;">${file.name}</div>
                                    <div class="text-reg text-12 " style="line-height: 1.5;">${ext} · ${fileSizeMB} MB</div>
                                </div>
                            </div>
                            <div class="mx-3 d-flex align-items-center delete-file" style="cursor:pointer;" data-index="${index}">
                                <span
                                    class="material-symbols-outlined">close</span>
                            </div>
                        </div>
                    </div>
                </div>`;
                    container.insertAdjacentHTML('beforeend', fileHTML);
                });

                // Allow deletion of specific files
                container.querySelectorAll('.delete-file').forEach((btn) => {
                    btn.addEventListener('click', function () {
                        const index = parseInt(this.dataset.index);
                        if (!isNaN(index)) {
                            allFiles.splice(index, 1);
                            let dt2 = new DataTransfer();
                            allFiles.forEach(f => dt2.items.add(f));
                            fileInput.files = dt2.files;
                        }
                        this.closest('.col-12').remove();
                    });
                });
            });
        });

        // Checkbox function for Stop Submissions
        const stopCheckbox = document.getElementById('stopSubmissions');
        stopCheckbox.addEventListener('change', function () {
            if (this.checked) {
                console.log('Stop submissions enabled');
            } else {
                console.log('Stop submissions disabled');
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>