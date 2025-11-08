<?php $activePage = 'assign-task';

include('../shared/assets/database/connect.php');
date_default_timezone_set('Asia/Manila');
include("../shared/assets/processes/prof-session-process.php");

$mode = '';
$taskID = 0;

if (isset($_GET['edit'])) {
    $mode = 'edit';
    $taskID = intval($_GET['edit']); // task to duplicate
} elseif (isset($_GET['reuse'])) {
    $mode = 'reuse';
    $taskID = intval($_GET['reuse']); // task to update
}

$course = "SELECT courseID, courseCode 
           FROM courses 
           WHERE userID = '$userID'";
$courses = executeQuery($course);

// Save Assignment Query
if (isset($_POST['saveAssignment'])) {
    $mode = $_POST['mode'] ?? 'new';
    $taskID = intval($_POST['taskID'] ?? 0);

    $title = mysqli_real_escape_string($conn, $_POST['assignmentTitle'] ?? '');
    $desc = mysqli_real_escape_string($conn, $_POST['assignmentContent'] ?? '');
    $deadline = !empty($_POST['deadline']) ? $_POST['deadline'] : null;
    $points = !empty($_POST['points']) ? $_POST['points'] : 0;
    $rubricID = !empty($_POST['selectedRubricID']) ? intval($_POST['selectedRubricID']) : null;
    $links = $_POST['links'] ?? [];
    $uploadedFiles = !empty($_FILES['materials']['name'][0]);

    foreach ($_POST['courses'] ?? [] as $selectedCourseID) {
        $deadlineEnabled = isset($_POST['stopSubmissions']) ? 1 : 0;

        if ($mode === 'new' || $mode === 'reuse') {
            // INSERT new assessment + assignment
            $insertAssessment = "INSERT INTO assessments 
                (courseID, assessmentTitle, type, deadline, deadlineEnabled, createdAt)
                VALUES 
                ('$selectedCourseID', '$title', 'task', " . ($deadline ? "'$deadline'" : "NULL") . ", '$deadlineEnabled', NOW())";
            executeQuery($insertAssessment);
            $assessmentID = mysqli_insert_id($conn);

            $insertAssignment = "INSERT INTO assignments 
                (assessmentID, assignmentDescription, assignmentPoints, rubricID)
                VALUES 
                ('$assessmentID', '$desc', '$points', '$rubricID')";
            executeQuery($insertAssignment);

            $assignmentID = $assessmentID;

            // Insert into todo
            $insertTodo = "INSERT INTO todo 
                (userID, assessmentID, status, isRead)
                VALUES ('$userID', '$assignmentID', 'Pending', 0)";
            executeQuery($insertTodo);

        } elseif ($mode === 'edit') {
            // UPDATE existing assessment + assignment
            $updateAssessment = "UPDATE assessments 
                SET assessmentTitle='$title', deadline=" . ($deadline ? "'$deadline'" : "NULL") . " 
                WHERE assessmentID='$taskID'";
            executeQuery($updateAssessment);

            $updateAssignment = "UPDATE assignments 
                SET assignmentDescription='$desc', assignmentPoints='$points', rubricID='$rubricID'
                WHERE assessmentID='$taskID'";
            executeQuery($updateAssignment);

            $assignmentID = $taskID;

            // Delete old files **only if new files or links were provided**
            if ($uploadedFiles || !empty($links)) {
                executeQuery("DELETE FROM files WHERE assignmentID='$assignmentID'");
            }
        }

        // --- Handle uploaded files ---
        if ($uploadedFiles) {
            $uploadDir = __DIR__ . "/../shared/assets/files/";
            if (!is_dir($uploadDir))
                mkdir($uploadDir, 0777, true);

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

        // --- Handle link files ---
        if (!empty($links)) {
            foreach ($links as $link) {
                $link = trim($link);
                if ($link === '')
                    continue;

                $fileTitle = $link;
                $context = stream_context_create(["http" => ["header" => "User-Agent: Mozilla/5.0"]]);
                $html = @file_get_contents($link, false, $context);

                if ($html !== false) {
                    if (preg_match('/<meta property="og:title" content="([^"]+)"/i', $html, $matches)) {
                        $fileTitle = $matches[1];
                    } elseif (preg_match("/<title>(.*?)<\/title>/i", $html, $matches)) {
                        $fileTitle = $matches[1];
                    }
                }

                $insertLink = "INSERT INTO files 
                    (courseID, userID, assignmentID, fileAttachment, fileTitle, fileLink) 
                    VALUES 
                    ('$selectedCourseID', '$userID', '$assignmentID', '', '" . mysqli_real_escape_string($conn, $fileTitle) . "', '$link')";
                executeQuery($insertLink);
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

// Selecting Tasks made by the Instructor logged in
$taskQuery = "SELECT a.assessmentTitle, asg.assignmentDescription, a.createdAt, a.assessmentID, c.courseCode
        FROM assessments a
        JOIN assignments asg 
        ON a.assessmentID = asg.assessmentID
        JOIN courses c
        ON a.courseID = c.courseID
        JOIN users u
        ON c.userID = u.userID
        WHERE a.type = 'Task' AND u.userID = '$userID'
        ORDER BY a.createdAt DESC";

$tasks = executeQuery($taskQuery);

// Storing reused data
$reusedData = null;

// Storing selected rubric
$activeRubric = null; // always exists

// If the instructor chose to reuse, it'll check the button if it was set
if (isset($_GET['reuse'])) {
    $reuseID = intval($_GET['reuse']);

    // Getting the data of the selected task to reuse
    $reuseQuery = "
    SELECT 
        a.assessmentTitle, 
        asg.assignmentDescription, 
        a.deadline, 
        asg.assignmentPoints,
        r.rubricID,
        r.rubricTitle,
        r.totalPoints,
        COUNT(c.criterionID) AS criteriaCount,
        f.fileID,
        f.fileAttachment,
        f.fileTitle,
        f.fileLink
    FROM assessments a
    JOIN assignments asg ON a.assessmentID = asg.assessmentID
    LEFT JOIN files f ON asg.assignmentID = f.assignmentID
    LEFT JOIN rubric r ON asg.rubricID = r.rubricID
    LEFT JOIN criteria c ON c.rubricID = r.rubricID
    WHERE a.assessmentID = '$reuseID' 
      AND a.type = 'Task'
    GROUP BY r.rubricID, r.rubricTitle, r.totalPoints, f.fileID
";

    $reuseResult = executeQuery($reuseQuery);
    if ($reuseResult && $reuseResult->num_rows > 0) {
        $reusedData = [];
        while ($row = $reuseResult->fetch_assoc()) {
            $reusedData[] = $row; // store all file rows
        }
    }
}

if (!empty($reusedData)) {
    // If the instructor chose to reuse, it'll populate these fields with the selected task's info
    $mainData = [
        'assessmentTitle' => $reusedData[0]['assessmentTitle'],
        'assignmentDescription' => $reusedData[0]['assignmentDescription'],
        'deadline' => $reusedData[0]['deadline'],
        'assignmentPoints' => $reusedData[0]['assignmentPoints']
    ];

    // Display the attachments of the reused tasks 
    $files = [];
    foreach ($reusedData as $row) {
        if (!empty($row['fileID'])) {
            $files[] = [
                'fileID' => $row['fileID'],
                'fileTitle' => $row['fileTitle'],
                'fileAttachment' => $row['fileAttachment'],
                'fileLink' => $row['fileLink']
            ];
        }
    }

    // Fetch rubric for the reused assignment
    $reusedRubricInfo = null;

    if (!empty($reusedData)) {
        // Find the first row that has a rubricID
        foreach ($reusedData as $row) {
            if (isset($row['rubricID']) && $row['rubricID'] != null && $row['rubricID'] != 0) {
                $reusedRubricInfo = [
                    'rubricID' => $row['rubricID'],
                    'rubricTitle' => $row['rubricTitle'] ?? 'Untitled Rubric',
                    'criteriaCount' => $row['criteriaCount'] ?? 0,
                    'totalPoints' => $row['totalPoints'] ?? 0
                ];
                break; // stop at the first one
            }
        }
    }

    // Default to null (no rubric selected)
    $activeRubric = null;

    // If this task is reused, get the reused rubric info
    if (!empty($reusedRubricInfo)) {
        $activeRubric = $reusedRubricInfo;
    }

    // If user manually selects a rubric via GET/POST, override
    $selectedRubricID = isset($_GET['selectedRubricID']) ? intval($_GET['selectedRubricID']) : 0;
    if ($selectedRubricID > 0) {
        // Look it up from $rubricsList
        foreach ($rubricsList as $r) {
            if ($r['rubricID'] == $selectedRubricID) {
                $activeRubric = $r;
                break;
            }
        }
    }
}

// Fetch latest rubric for current user to display in the rubric card
// Detect if totalPoints column exists to avoid SQL errors on older DBs
$hasTotalPoints = false;
$colCheck = executeQuery("SHOW COLUMNS FROM rubric LIKE 'totalPoints'");
if ($colCheck && $colCheck->num_rows > 0) {
    $hasTotalPoints = true;
}

// Fetch all rubrics for modal list
$rubricsList = [];
$tpSelectList = $hasTotalPoints ? "IFNULL(r.totalPoints,0) AS totalPoints" : "0 AS totalPoints";
$tpGroupList = $hasTotalPoints ? ", r.totalPoints" : "";
$tpGroupList = $hasTotalPoints ? ", r.totalPoints" : "";
$rubricsQuery = "SELECT r.rubricID, r.rubricTitle, r.rubricType, $tpSelectList, COUNT(c.criterionID) AS criteriaCount
                 FROM rubric r
                 LEFT JOIN criteria c ON c.rubricID = r.rubricID
                 WHERE r.userID = '$userID'
                 GROUP BY r.rubricID, r.rubricTitle, r.rubricType$tpGroupList
                 ORDER BY r.rubricID DESC";

$rubricsRes = executeQuery($rubricsQuery);
if ($rubricsRes && $rubricsRes->num_rows > 0) {
    while ($row = $rubricsRes->fetch_assoc()) {
        $rubricsList[] = $row;
    }
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


    <style>
        /* Change font for text typed inside the Quill editor */
        .ql-editor {
            font-family: 'Regular', sans-serif;
            letter-spacing: -0.03em;
            color: var(--black);
            font-size: 16px;
            /* optional */
        }

        .ql-editor {
            padding: 12px 16px;
            /* customize as you like */
        }

        .ql-editor::before {
            padding: 12px 16px;
        }

        .ql-editor::before {
            font-family: inherit;
            /* use same font as the text */
            font-size: inherit;
            /* same size as the text */
            line-height: inherit;
            /* align vertically */
            padding: 1px 1px;
            /* match .ql-editor padding */
            opacity: 1;
            /* ensure visibility */
        }
    </style>

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

                    <div class="container-fluid py-3 overflow-y-auto row-padding-top">
                        <div class="create-prof-row">
                            <div class="col-12">
                                <!-- Header -->
                                <div class="row mb-3 align-items-center">

                                    <!-- Back Arrow -->
                                    <div class="col-auto d-none d-md-block">
                                        <a href="javascript:history.back()" class="text-decoration-none">
                                            <i class="fa-solid fa-arrow-left text-reg text-16"
                                                style="color: var(--black);"></i>
                                        </a>
                                    </div>

                                    <!-- Page Title -->
                                    <div class="col text-center text-md-start">
                                        <span class="text-sbold text-20">Assign Task</span>
                                    </div>

                                    <!-- Assign Existing Task Button -->
                                    <div
                                        class="col-12 col-md-auto text-center d-flex d-md-block justify-content-center justify-content-md-end mt-3 mt-md-0">
                                        <button type="button"
                                            class="btn btn-sm px-3 py-1 rounded-pill text-reg text-md-14 my-1 d-flex align-items-center gap-2"
                                            style="background-color: var(--primaryColor); border: 1px solid var(--black); color: var(--black);"
                                            data-bs-toggle="modal" data-bs-target="#reuseTaskModal">
                                            <span class="material-symbols-rounded"
                                                style="font-size:16px">add_task</span>
                                            <span>Assign an existing task</span>
                                        </button>
                                    </div>
                                </div>


                                <!-- Form starts -->
                                <form action="" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="selectedRubricID" id="selectedRubricID"
                                        value="<?= $activeRubric['rubricID'] ?? '' ?>">
                                    <input type="hidden" name="mode"
                                        value="<?= isset($_GET['edit']) ? 'edit' : (isset($_GET['reuse']) ? 'reuse' : 'new') ?>">
                                    <input type="hidden" name="taskID"
                                        value="<?= $_GET['edit'] ?? $_GET['reuse'] ?? '' ?>">



                                    <div class="row">
                                        <div class="col-12 pt-3">
                                            <label for="taskInfo" class="form-label text-med text-16">Task
                                                Information</label>
                                            <input type="text"
                                                class="form-control textbox mb-2 px-3 py-2 text-reg text-16"
                                                id="taskInfo" name="assignmentTitle" placeholder="Task Title *"
                                                value="<?php echo isset($mainData) ? htmlspecialchars($mainData['assessmentTitle']) : ''; ?>"
                                                required>
                                        </div>
                                    </div>

                                    <!-- Rich Text Editor -->
                                    <div class="row">
                                        <div class="col-12 mb-3">
                                            <div class="editor-wrapper">
                                                <div class="text-reg " id="editor"></div>
                                                <div id="toolbar" class="row align-items-center p-3 p-md-3 g-2 g-md-5">
                                                    <div
                                                        class="col d-flex align-items-center px-2 px-md-4 gap-1 gap-md-3">
                                                        <button class="ql-bold"></button>
                                                        <button class="ql-italic"></button>
                                                        <button class="ql-underline"></button>
                                                        <button class="ql-list" value="bullet"></button>
                                                        <span id="word-counter"
                                                            class="ms-auto text-muted text-med text-16 me-2">0/120</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="hidden" name="assignmentContent" id="task">
                                            <input type="hidden" name="assignmentContent" id="task">
                                        </div>
                                    </div>

                                    <div class="row g-3 m-0 p-0">
                                        <div class="row g-3 mt-4 m-0 p-0">
                                            <!-- Deadline -->
                                            <div class="col-md-6 m-0 p-0 mb-3 mb-md-0">
                                                <label class="form-label text-med text-16">
                                                    Deadline *
                                                </label>
                                                <div class="input-group">
                                                    <input type="datetime-local"
                                                        class="form-control textbox text-reg text-16 me-0 me-md-2"
                                                        name="deadline"
                                                        value="<?php echo isset($mainData['deadline']) ? date('Y-m-d\TH:i', strtotime($mainData['deadline'])) : ''; ?>"
                                                        required>
                                                </div>
                                            </div>

                                            <!-- Points -->
                                            <div class="col-md-6 m-0 p-0">
                                                <label class="form-label text-med text-16">
                                                    Points
                                                </label>
                                                <input type="number" id="rubricPointsInput"
                                                    class="form-control textbox text-reg text-16" name="points"
                                                    placeholder="Ungraded if left blank"
                                                    value="<?php echo isset($mainData['assignmentPoints']) ? htmlspecialchars($mainData['assignmentPoints']) : ''; ?>" />

                                            </div>
                                        </div>

                                        <div class="form-check mt-2 col d-flex align-items-center">
                                            <input class="form-check-input" type="checkbox" id="stopSubmissions"
                                                name="stopSubmissions" value="1"
                                                style="border: 1px solid var(--black);" />
                                            <label class="form-check-label text-reg text-14 ms-2"
                                                style="margin-top: 4.5px;" for="stopSubmissions">
                                                Stop accepting submissions after the deadline.
                                            </label>
                                        </div>

                                    </div>


                                    <!-- Learning Materials -->
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="learning-materials">
                                                <div class="d-flex align-items-center mt-5 mb-0 mb-md-2">
                                                    <label class="text-med text-16">Attachments</label>
                                                    <!-- For desktop -->
                                                    <span class="material-symbols-outlined ms-3 me-1 d-none d-md-block"
                                                        style="font-size:16px">
                                                        info
                                                    </span>
                                                    <span class="text-reg text-14 d-none d-md-block">You can add up to
                                                        10
                                                        files or links.</span>
                                                </div>
                                                <!-- For mobile -->
                                                <div class="d-block d-md-none d-flex align-items-center mb-2">
                                                    <span class="material-symbols-outlined me-1" style="font-size:16px">
                                                        info
                                                    </span>
                                                    <span class="text-reg text-14">You can add up to 10
                                                        files or links.</span>
                                                </div>


                                                <!-- Container for dynamically added file & link cards -->
                                                <div id="filePreviewContainer" class="row mb-0"></div>

                                                <!-- Upload buttons -->
                                                <div class="mt-0 mb-4 text-start ">
                                                    <input type="file" name="materials[]" class="d-none" id="fileUpload"
                                                        multiple>
                                                    <button type="button"
                                                        class="btn btn-sm px-3 py-1 rounded-pill text-reg text-md-14 mt-1"
                                                        style="background-color: var(--primaryColor); border: 1px solid var(--black);"
                                                        onclick="document.getElementById('fileUpload').click();">
                                                        <div style="display: flex; align-items: center; gap: 5px;">
                                                            <span class="material-symbols-rounded"
                                                                style="font-size:16px">upload</span>
                                                            <span>Upload</span>
                                                        </div>
                                                    </button>

                                                    <button type="button"
                                                        class="btn btn-sm px-3 py-1 rounded-pill text-reg text-md-14 ms-2 mt-1"
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

                                                    <div class="d-flex align-items-center mt-4 mb-0 mb-md-2">
                                                        <label class="text-med text-16">Rubric</label>
                                                        <!-- For desktop -->
                                                        <span
                                                            class="material-symbols-outlined ms-3 me-1 d-none d-md-block"
                                                            style="font-size:16px">

                                                            info
                                                        </span>
                                                        <span class="text-reg text-14 d-none d-md-block">Any
                                                            points entered above will be replaced by the rubric’s total
                                                            points.</span>
                                                    </div>
                                                    <!-- For mobile -->
                                                    <div class="d-block d-md-none d-flex align-items-center mb-2">
                                                        <span class="material-symbols-outlined me-1"
                                                            style="font-size:16px">
                                                            info
                                                        </span>
                                                        <span class="text-reg text-14">Any
                                                            points entered above will be replaced by the rubric’s total
                                                            points.</span>
                                                    </div>


                                                    <div class="row mb-0 mt-3"
                                                        data-has-rubric="<?= !empty($activeRubric) ? '1' : '0' ?>"
                                                        <?= $activeRubric ? '' : 'style="display:none;"' ?>>
                                                        <div class="col-12">
                                                            <div
                                                                class="materials-card rubric-card d-flex align-items-stretch px-2 py-2 w-100 rounded-3">
                                                                <div
                                                                    class="d-flex w-100 align-items-center justify-content-between">
                                                                    <div class="d-flex align-items-center flex-grow-1">
                                                                        <div class="mx-3 d-flex align-items-center">
                                                                            <span
                                                                                class="material-symbols-rounded">rate_review</span>
                                                                        </div>
                                                                        <div>
                                                                            <div class="text-sbold text-16"
                                                                                style="line-height: 1.5;">
                                                                                <?= $activeRubric['rubricTitle'] ?? '' ?>
                                                                            </div>
                                                                            <div class="text-reg text-12 text-break"
                                                                                style="line-height: 1.5;">
                                                                                <?= ($activeRubric['totalPoints'] ?? 0) ?>
                                                                                Points ·
                                                                                <?= intval($activeRubric['criteriaCount'] ?? 0) ?>
                                                                                Criteria
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="mx-3 d-flex align-items-center">
                                                                        <span class="material-symbols-outlined"
                                                                            style="cursor:pointer;">close</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <script>
                                                        (function () {
                                                            try {
                                                                var cardRow = document.querySelector('.row.mb-0.mt-3');
                                                                if (!cardRow) return;

                                                                // Only show the card if PHP has an active rubric
                                                                var hasActiveRubric = cardRow.dataset.hasRubric === '1';
                                                                if (hasActiveRubric) {
                                                                    cardRow.style.display = '';
                                                                } else {
                                                                    cardRow.style.display = 'none';
                                                                }
                                                            } catch (e) { }
                                                        })();
                                                    </script>


                                                    <?php if ($activeRubric) { ?>
                                                        <script>
                                                            (function () {
                                                                try {
                                                                    var ptsInput = document.querySelector('input[name="points"]');
                                                                    if (ptsInput) { ptsInput.value = '<?= (float) ($activeRubric['totalPoints'] ?? 0) ?>'; }
                                                                } catch (e) { }
                                                            })();
                                                        </script>
                                                    <?php } ?>

                                                    <!-- Buttons -->
                                                    <div class="mt-2 mb-5 text-start">
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
                                                    <div class="row align-items-center justify-content-between text-center text-md-start"
                                                        style="margin-bottom: <?php echo ($courses && $courses->num_rows > 0) ? ($courses->num_rows * 50) : 0; ?>px;">
                                                        <!-- Dynamic Border Bottom based on the number of courses hehe -->
                                                        <!-- Add to Course -->
                                                        <div
                                                            class="col-12 col-md-auto mt-3 mt-md-0 d-flex align-items-center flex-nowrap justify-content-center justify-content-md-start">
                                                            <span class="me-2 text-med text-16 pe-3">Add to
                                                                Course</span>
                                                            <div class="dropdown">
                                                                <button
                                                                    class="btn dropdown-toggle dropdown-shape text-med text-16 me-md-5"
                                                                    type="button" data-bs-toggle="dropdown"
                                                                    aria-expanded="false">
                                                                    <span class="me-2">Select Course</span>
                                                                </button>
                                                                <ul class="dropdown-menu p-2 mt-2"
                                                                    style="min-width: 200px; border: 1px solid var(--black);">
                                                                    <?php
                                                                    if ($courses && $courses->num_rows > 0) {
                                                                        while ($course = $courses->fetch_assoc()) { ?>
                                                                            <li>
                                                                                <div class="form-check">
                                                                                    <input
                                                                                        class="form-check-input course-checkbox"
                                                                                        type="checkbox" name="courses[]"
                                                                                        style="border: 1px solid var(--black);"
                                                                                        value="<?php echo $course['courseID']; ?>"
                                                                                        id="course<?php echo $course['courseID']; ?>">
                                                                                    <label class="form-check-label text-reg"
                                                                                        for="course<?php echo $course['courseID']; ?>">
                                                                                        <?php echo $course['courseCode']; ?>
                                                                                    </label>
                                                                                </div>
                                                                            </li>
                                                                        <?php }
                                                                    } else { ?>
                                                                        <li><span class="dropdown-item-text text-muted">No
                                                                                courses found</span></li>
                                                                    <?php } ?>
                                                                </ul>
                                                            </div>
                                                        </div>

                                                        <!-- Assign Button -->
                                                        <div class="col-12 col-md-auto mt-3 mt-md-0 text-center">
                                                            <button type="submit" name="saveAssignment"
                                                                class="px-4 py-2 rounded-pill text-sbold text-md-14 mt-4 mt-md-0"
                                                                style="background-color: var(--primaryColor); border: 1px solid var(--black);">
                                                                <?php echo isset($reusedData) ? 'Reassign Task' : 'Assign'; ?>
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
                                <?php if (!empty($rubricsList)) {
                                    foreach ($rubricsList as $rub) {
                                        $isSelected = ($activeRubric && $activeRubric['rubricID'] == $rub['rubricID']);
                                        ?>
                                        <div class="rubric-option rounded-3 d-flex align-items-center justify-content-between mb-2"
                                            style="cursor: pointer; background-color: <?= $isSelected ? 'var(--primaryColor)' : 'var(--pureWhite)' ?>; border: 1px solid var(--black);"
                                            data-rubric-id="<?= $rub['rubricID'] ?>"
                                            data-rubric-title="<?= htmlspecialchars($rub['rubricTitle']) ?>"
                                            data-rubric-points="<?= $rub['totalPoints'] ?>"
                                            data-rubric-criteria="<?= (int) $rub['criteriaCount'] ?>">
                                            <div style="line-height: 1.5; padding:10px 15px;">
                                                <div class="text-sbold text-14"><?= $rub['rubricTitle'] ?></div>
                                                <div class="text-med text-12"><?= $rub['totalPoints'] ?> Points ·
                                                    <?= (int) $rub['criteriaCount'] ?> Criteria
                                                </div>
                                            </div>
                                            <?php if (($rub['rubricType'] ?? '') === 'Created') { ?>
                                                <a href="edit-rubric.php?rubricID=<?= $rub['rubricID'] ?>"
                                                    class="text-decoration-none" onclick="event.stopPropagation();">
                                                    <span class="material-symbols-rounded"
                                                        style="font-variation-settings: 'FILL' 1; padding-right:15px; color: var(--black);">
                                                        edit
                                                    </span>
                                                </a>
                                            <?php } ?>
                                        </div>
                                    <?php }
                                } else { ?>
                                    <div class="text-muted text-reg text-14 px-3 py-2">No rubrics found. Create one.</div>
                                <?php } ?>

                                <!-- Select Rubric Button -->
                                <a href="create-rubric.php"
                                    class="btn btn-sm px-3 py-1 rounded-pill text-reg text-md-14"
                                    style="background-color: var(--primaryColor); border: 1px solid var(--black);">
                                    <div style="display: flex; align-items: center; gap: 5px;">
                                        <span class="material-symbols-outlined" style="font-size:16px">add_circle</span>
                                        <span>Create</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FOOTER -->
                <div class="modal-footer border-top flex-shrink-0">

                    <!-- BUTTON -->
                    <button type="button" id="confirmRubricBtn" class="btn rounded-5 px-4 text-sbold text-14 me-3"
                        style="background-color: var(--primaryColor); border: 1px solid var(--black);">
                        Select
                    </button>

                </div>
            </div>


        </div>
    </div>
    </div>

    <script>
        document.getElementById('confirmRubricBtn').addEventListener('click', function () {
            const hiddenInput = document.getElementById('selectedRubricID');
            const cardRow = document.querySelector('.row.mb-0.mt-3'); // your rubric card row
            const titleDiv = cardRow.querySelector('.text-sbold.text-16');
            const infoDiv = cardRow.querySelector('.text-reg.text-12');

            const selectedOption = document.querySelector(`.rubric-option[data-rubric-id="${selectedRubric.id}"]`);
            if (selectedOption) {
                cardRow.style.display = '';
                titleDiv.textContent = selectedOption.dataset.rubricTitle;
                infoDiv.textContent = `${selectedOption.dataset.rubricPoints} Points · ${selectedOption.dataset.rubricCriteria} Criteria`;
                hiddenInput.value = selectedRubric.id;
            }

            const modalEl = document.getElementById('selectRubricModal');
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
        });
    </script>

    <!-- Reuse Task Modal -->
    <div class="modal fade" id="reuseTaskModal" tabindex="-1" aria-labelledby="reuseTaskModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered py-4 " style="max-width: 700px;  height: 80vh">
            <div class="modal-content d-flex flex-column" style="height: 100%;">

                <!-- HEADER -->
                <div class="modal-header flex-shrink-0">
                    <div class="modal-title text-sbold text-20 ms-3" id="selectRubricModalLabel">Assign an existing task
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        style="transform: scale(0.8); filter: grayscale(100%);"></button>
                </div>

                <!-- DESC -->
                <div class="modal-body flex-grow-1 overflow-auto">
                    <p class="mb-3 text-med text-14 ms-3" style="color: var(--black);">
                        Select a task you’ve previously created to reuse its details. You can review and edit the title,
                        instructions, or settings before assigning or saving the task.
                    </p>
                    <div class="col p-0 m-3">
                        <div class="card rounded-3 mb-2 border-0">
                            <div class="card-body p-0">
                                <!-- OPTIONS -->
                                <?php if ($tasks && $tasks->num_rows > 0) {
                                    while ($task = $tasks->fetch_assoc()) {
                                        ?>
                                        <div class="rubric-option rounded-3 d-flex align-items-center justify-content-between mb-2 w-100"
                                            style="cursor: pointer; background-color: var(--pureWhite); border: 1px solid var(--black);"
                                            onclick='window.location.href="assign-task.php?reuse=<?php echo $task["assessmentID"]; ?>"'>
                                            <div style="line-height: 1.5; padding:10px 15px;">
                                                <div class="text-sbold text-14">
                                                    <?php echo htmlspecialchars($task['assessmentTitle']); ?>
                                                </div>
                                                <div class="text-med text-12"
                                                    style="display:-webkit-box; -webkit-line-clamp:1; -webkit-box-orient:vertical; overflow:hidden;">
                                                    <?php echo htmlspecialchars($task['assignmentDescription']); ?>
                                                </div>
                                                <div class="text-med text-muted text-12"
                                                    style="display:-webkit-box; -webkit-line-clamp:1; -webkit-box-orient:vertical; overflow:hidden;">
                                                    <?php
                                                    echo date('F j, Y g:i A', strtotime($task['createdAt']))
                                                        . ' · ' . $task['courseCode'];
                                                    ?>

                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                } else {
                                    ?>
                                    <div class="text-muted text-reg text-14 py-2">No existing assignments found.</div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FOOTER -->
                <div class="modal-footer border-top flex-shrink-0 py-4">



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
            placeholder: 'Task Instructions *',
            modules: {
                toolbar: '#toolbar'
            }
        });

        <?php if (isset($reusedData)) { ?>
            quill.root.innerHTML = <?php echo json_encode($mainData['assignmentDescription']); ?>;
        <?php } ?>


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
        document.querySelector('form').addEventListener('submit', function (e) {
            let text = quill.getText().trim(); // plain text
            let html = quill.root.innerHTML;
            const taskInput = document.querySelector('#task');

            // Convert Quill HTML
            html = html.replace(/<p>/g, '').replace(/<\/p>/g, '<br>');
            html = html.replace(/<li>/g, '• ').replace(/<\/li>/g, '<br>');
            html = html.replace(/<\/?(ul|ol)>/g, '');
            html = html.replace(/(<br>)+$/g, '');
            taskInput.value = html.trim();

            // Validation for task instructions
            if (text.length === 0) {
                e.preventDefault();
                quill.root.focus();
                taskInput.setCustomValidity('Please fill out this field.');
                taskInput.reportValidity();
                return false;
            } else {
                taskInput.setCustomValidity('');
            }
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
                    <div class="col-12 my-1" data-id="${uniqueID}">
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

                // Remove only file previews, keep links
                container.querySelectorAll('.file-preview').forEach(el => el.remove());

                // Rebuild file previews
                allFiles.forEach((file, index) => {
                    const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
                    const ext = file.name.split('.').pop().toUpperCase();
                    const fileHTML = `
                <div class="col-12 my-1 file-preview">
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
<script>
    // Pass reused files from PHP to JavaScript
    const reusedFiles = <?= json_encode($files) ?>;
</script>

<?php if (!empty($files)): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const container = document.getElementById('filePreviewContainer');
            if (!container) return;

            <?php foreach ($files as $file): ?>
                <?php if (!empty($file['fileLink'])): ?>
                        // Render Link Preview
                        (function () {
                            const linkValue = <?php echo json_encode($file['fileLink']); ?>;
                            const uniqueID = Date.now() + Math.floor(Math.random() * 1000);
                            try {
                                const urlObj = new URL(linkValue);
                                const domain = urlObj.hostname;
                                const faviconURL = `https://www.google.com/s2/favicons?sz=64&domain=${domain}`;
                                const html = `
                        <div class="col-12 my-1" data-id="${uniqueID}">
                            <div class="materials-card d-flex align-items-stretch p-2 w-100 rounded-3">
                                <div class="d-flex w-100 align-items-center justify-content-between">
                                    <div class="d-flex align-items-center flex-grow-1">
                                        <div class="mx-3 d-flex align-items-center">
                                            <img src="${faviconURL}" alt="${domain} Icon"
                                                onerror="this.onerror=null;this.src='../shared/assets/img/web.png';"
                                                style="width: 30px; height: 30px;">
                                        </div>
                                        <div>
                                            <div id="title-${uniqueID}" class="text-sbold text-16" style="line-height: 1.5;"><?= htmlspecialchars($file['fileTitle']) ?></div>
                                            <div class="text-reg text-12 text-break" style="line-height: 1.5;">
                                                <a href="${linkValue}" target="_blank">${linkValue}</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mx-3 d-flex align-items-center delete-file" style="cursor:pointer;">
                                        <span class="material-symbols-outlined">close</span>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="links[]" value="${linkValue}" class="link-hidden">
                        </div>`;
                                container.insertAdjacentHTML('beforeend', html);
                            } catch (e) { }
                        })();
                <?php elseif (!empty($file['fileAttachment'])): ?>
                        // Render File Preview
                        (function () {
                            const fileName = <?php echo json_encode($file['fileTitle'] ?: basename($file['fileAttachment'])); ?>;
                            const ext = fileName.split('.').pop().toUpperCase();
                            const uniqueID = Date.now() + Math.floor(Math.random() * 1000);
                            <?php
                            $filePath = "../shared/assets/files/" . $file['fileAttachment'];
                            $fileExt = strtoupper(pathinfo($file['fileAttachment'], PATHINFO_EXTENSION));
                            $fileSize = (file_exists($filePath)) ? filesize($filePath) : 0;
                            $fileSizeMB = $fileSize > 0 ? round($fileSize / 1048576, 2) . " MB" : "Unknown size";
                            ?>

                            const html = `
                    <div class="col-12 my-1 file-preview">
                        <div class="materials-card d-flex align-items-stretch p-2 w-100 rounded-3">
                            <div class="d-flex w-100 align-items-center justify-content-between">
                                <div class="d-flex align-items-center flex-grow-1">
                                    <div class="mx-3 d-flex align-items-center">
                                        <span class="material-symbols-rounded">description</span>
                                    </div>
                                    <div>
                                        <div class="text-sbold text-16" style="line-height: 1.5;"><?= htmlspecialchars($file['fileAttachment']) ?></div>
                                        <div class="text-reg text-12" style="line-height: 1.5;">
    <?= $fileExt ?> · <?= $fileSizeMB ?>
</div>

                                    </div>
                                </div>
                                <div class="mx-3 d-flex align-items-center delete-file" style="cursor:pointer;">
                                    <span class="material-symbols-outlined">close</span>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="existingFiles[]" value="<?php echo htmlspecialchars($file['fileAttachment']); ?>">
                    </div>`;
                            container.insertAdjacentHTML('beforeend', html);
                        })();
                <?php endif; ?>
            <?php endforeach; ?>
        });
        //  Enable delete buttons for preloaded (reused) items
        document.addEventListener('click', function (event) {
            if (event.target.closest('.delete-file')) {
                const col = event.target.closest('.col-12');
                if (col) col.remove();
            }
        });

    </script>
<?php endif; ?>

<!-- Rubric JS -->
<script>
    // Auto-open rubric modal when returning from create-rubric
    (function () {
        function openRubricModal() {
    (function () {
        function openRubricModal() {
            var modalEl = document.getElementById('selectRubricModal');
            if (!modalEl) return;
            var modal = new bootstrap.Modal(modalEl);
            // slight delay to ensure layout ready
            setTimeout(function () { modal.show(); }, 50);
            setTimeout(function () { modal.show(); }, 50);
        }
        function needsOpen() {
        function needsOpen() {
            try {
                var params = new URLSearchParams(window.location.search);
                if (params.get('openRubric') === '1') return true;
            } catch (e) { }
            } catch (e) { }
            // also support hash trigger
            if (window.location.hash === '#openRubric') return true;
            return false;
        }
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function () { if (needsOpen()) openRubricModal(); });
            document.addEventListener('DOMContentLoaded', function () { if (needsOpen()) openRubricModal(); });
        } else {
            if (needsOpen()) openRubricModal();
        }
    })();

    var selectedRubric = null;
    (function () {
        // Only run if the card exists
        var cardRow = document.querySelector('.row.mb-0.mt-3');
        if (!cardRow) return;

        var hiddenInput = document.getElementById('selectedRubricID');
        var titleDiv = cardRow.querySelector('.text-sbold.text-16');
        var infoDiv = cardRow.querySelector('.text-reg.text-12');

        // Check if PHP has an active rubric
        var hasActiveRubric = cardRow.dataset.hasRubric === '1';
        if (hasActiveRubric && hiddenInput) {
            // Populate hidden input
            hiddenInput.value = "<?= $activeRubric['rubricID'] ?? '' ?>";

            // Populate card content
            titleDiv.textContent = "<?= addslashes($activeRubric['rubricTitle'] ?? '') ?>";
            infoDiv.textContent = "<?= ($activeRubric['totalPoints'] ?? 0) ?> Points · <?= intval($activeRubric['criteriaCount'] ?? 0) ?> Criteria";

            // Also set selectedRubric JS variable
            window.selectedRubric = {
                id: "<?= $activeRubric['rubricID'] ?? '' ?>",
                title: "<?= addslashes($activeRubric['rubricTitle'] ?? '') ?>",
                points: "<?= ($activeRubric['totalPoints'] ?? 0) ?>",
                criteria: "<?= intval($activeRubric['criteriaCount'] ?? 0) ?>"
            };

            // Ensure card is visible
            cardRow.style.display = '';
        }
    })();


    // Click on a rubric option: highlight and store selection
    document.addEventListener('click', function (e) {
    document.addEventListener('click', function (e) {
        var opt = e.target.closest && e.target.closest('.rubric-option');
        if (!opt) return;

        // Clear previous highlight
        document.querySelectorAll('.rubric-option').forEach(function (el) {
        document.querySelectorAll('.rubric-option').forEach(function (el) {
            el.style.backgroundColor = 'var(--pureWhite)';
        });
        // Highlight selected (yellow)
        opt.style.backgroundColor = 'var(--primaryColor)';

        selectedRubric = {
            id: opt.getAttribute('data-rubric-id'),
            title: opt.getAttribute('data-rubric-title'),
            points: opt.getAttribute('data-rubric-points'),
            criteria: opt.getAttribute('data-rubric-criteria')
        };
    });

    // Confirm selection: apply to card and close modal
    document.addEventListener('click', function (e) {
    document.addEventListener('click', function (e) {
        var btn = e.target.closest && e.target.closest('#confirmRubricBtn');
        if (!btn) return;
        if (!selectedRubric) return;

        var cardRow = document.querySelector('.row.mb-0.mt-3');
        if (cardRow) cardRow.style.display = '';
        var titleEl = document.querySelector('.rubric-card .text-sbold.text-16');
        var metaEl = document.querySelector('.rubric-card .text-reg.text-12');
        if (titleEl) titleEl.textContent = selectedRubric.title;
        if (metaEl) metaEl.textContent = selectedRubric.points + ' Points · ' + selectedRubric.criteria + ' Criteria';

        // Also reflect total points into the Points input field
        try {
            var ptsInput = document.querySelector('#rubricPointsInput'); // add an ID to the points input
            if (ptsInput) ptsInput.value = selectedRubric.points;

        } catch (e) { }

        var modalEl = document.getElementById('selectRubricModal');
        if (modalEl) {
            var modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
            modal.hide();
        }
    });

    // Close rubric card
    document.addEventListener('click', function (e) {
        const closeIcon = e.target.closest('.rubric-card .material-symbols-outlined');
        if (!closeIcon) return;

        const cardRow = closeIcon.closest('.row.mb-0.mt-3');
        if (cardRow) cardRow.style.display = 'none';

        // Clear selection
        selectedRubric = null;
        const hiddenInput = document.getElementById('selectedRubricID');
        if (hiddenInput) hiddenInput.value = '';

        // Clear card text
        const titleDiv = cardRow.querySelector('.text-sbold.text-16');
        const infoDiv = cardRow.querySelector('.text-reg.text-12');
        if (titleDiv) titleDiv.textContent = '';
        if (infoDiv) infoDiv.textContent = '';

        // Reset modal option highlights
        document.querySelectorAll('.rubric-option').forEach(el => {
            el.style.backgroundColor = 'var(--pureWhite)';
        });
    });

</script>

<!-- Assign Task JS -->
<script>
    // Persist Assign Task form fields using localStorage so input survives navigating away and back
    (function () {
    (function () {
        var STORAGE_KEY = 'assignTaskDraft_v1';
        var NAVIGATION_FLAG = 'assignTaskNavigatedAway_v1';

        function getElementById(id) { return document.getElementById(id); }

        function getDeadlineInput() { return document.querySelector('input[name="deadline"]'); }
        function getPointsInput() { return document.querySelector('input[name="points"]'); }

        function getSelectedCourseIds() {
            var selectedIds = [];
            var courseCheckboxes = document.querySelectorAll('.course-checkbox');
            for (var i = 0; i < courseCheckboxes.length; i++) {
                if (courseCheckboxes[i].checked) selectedIds.push(courseCheckboxes[i].value);
            }
            return selectedIds;
        }

        function getCurrentLinks() {
            var links = [];
            var hiddenLinkInputs = document.querySelectorAll('#filePreviewContainer input.link-hidden');
            for (var i = 0; i < hiddenLinkInputs.length; i++) {
                var value = hiddenLinkInputs[i].value;
                if (value && value.trim() !== '') links.push(value.trim());
            }
            return links;
        }

        function saveDraftToStorage() {
            try {
                var titleInput = getElementById('taskInfo');
                var deadlineInput = getDeadlineInput();
                var pointsInput = getPointsInput();
                var stopCheckbox = getElementById('stopSubmissions');
                var draft = {
                    assignmentTitle: titleInput ? titleInput.value : '',
                    assignmentContentHtml: (typeof quill !== 'undefined' ? quill.root.innerHTML : ''),
                    deadline: deadlineInput ? deadlineInput.value : '',
                    points: pointsInput ? pointsInput.value : '',
                    stopSubmissions: stopCheckbox ? !!stopCheckbox.checked : false,
                    courses: getSelectedCourseIds(),
                    links: getCurrentLinks()
                };
                localStorage.setItem(STORAGE_KEY, JSON.stringify(draft));
            } catch (e) { }
            } catch (e) { }
        }

        function clearDraftInStorage() {
            try { localStorage.removeItem(STORAGE_KEY); } catch (e) { }
            try { localStorage.removeItem(STORAGE_KEY); } catch (e) { }
        }

        function appendLinkPreview(linkValue) {
            var container = document.getElementById('filePreviewContainer');
            if (!container || !linkValue) return;
            var uniqueId = Date.now() + Math.floor(Math.random() * 1000);
            var domain = '';
            try { var urlObj = new URL(linkValue); domain = urlObj.hostname; } catch (e) { domain = linkValue; }
            var faviconUrl = 'https://www.google.com/s2/favicons?sz=64&domain=' + domain;
            var html = '' +
                '<div class="col-12 my-1" data-id="' + uniqueId + '">' +
                '  <div class="materials-card d-flex align-items-stretch p-2 w-100 rounded-3">' +
                '    <div class="d-flex w-100 align-items-center justify-content-between">' +
                '      <div class="d-flex align-items-center flex-grow-1">' +
                '        <div class="mx-3 d-flex align-items-center">' +
                '          <img src="' + faviconUrl + '" alt="' + domain + ' Icon" onerror="this.onerror=null;this.src=\'../shared/assets/img/web.png\';" style="width: 30px; height: 30px;">' +
                '        </div>' +
                '        <div>' +
                '          <div id="title-' + uniqueId + '" class="text-sbold text-16" style="line-height: 1.5;">' + linkValue + '</div>' +
                '          <div class="text-reg text-12 text-break" style="line-height: 1.5;"><a href="' + linkValue + '" target="_blank">' + linkValue + '</a></div>' +
                '        </div>' +
                '      </div>' +
                '      <div class="mx-3 d-flex align-items-center delete-file" style="cursor:pointer;">' +
                '        <span class="material-symbols-outlined">close</span>' +
                '      </div>' +
                '    </div>' +
                '  </div>' +
                '  <input type="hidden" name="links[]" value="' + linkValue + '" class="link-hidden">' +
                '</div>';
            container.insertAdjacentHTML('beforeend', html);
        }

        function hasUserEnteredData(draft) {
            if (!draft) return false;
            var title = draft.assignmentTitle ? draft.assignmentTitle.trim() : '';
            if (title !== '') return true;
            var content = draft.assignmentContentHtml ? draft.assignmentContentHtml.trim() : '';
            if (content !== '' && content !== '<p><br></p>' && content !== '<p></p>') return true;
            var deadline = draft.deadline ? draft.deadline.trim() : '';
            if (deadline !== '') return true;
            var points = draft.points ? draft.points.trim() : '';
            if (points !== '') return true;
            if (draft.stopSubmissions === true) return true;
            if (draft.courses && Array.isArray(draft.courses) && draft.courses.length > 0) return true;
            if (draft.links && Array.isArray(draft.links) && draft.links.length > 0) return true;
            return false;
        }
        
        function clearAllFormFields() {
            try {
                var titleInput = getElementById('taskInfo');
                if (titleInput) titleInput.value = '';
                var deadlineInput = getDeadlineInput();
                if (deadlineInput) deadlineInput.value = '';
                var pointsInput = getPointsInput();
                if (pointsInput) pointsInput.value = '';
                var stopCheckbox = getElementById('stopSubmissions');
                if (stopCheckbox) stopCheckbox.checked = false;
                var courseCheckboxes = document.querySelectorAll('.course-checkbox');
                for (var i = 0; i < courseCheckboxes.length; i++) courseCheckboxes[i].checked = false;
                var container = document.getElementById('filePreviewContainer');
                if (container) container.innerHTML = '';
                if (typeof quill !== 'undefined' && quill.root) {
                    quill.root.innerHTML = '';
                }
            } catch (e) { }
            } catch (e) { }
        }

        function restoreDraftFromStorage() {
            var raw = null;
            try { raw = localStorage.getItem(STORAGE_KEY); } catch (e) { }
            try { raw = localStorage.getItem(STORAGE_KEY); } catch (e) { }
            if (!raw) {
                clearAllFormFields();
                return;
            }
            var draft = null;
            try { draft = JSON.parse(raw); } catch (e) {
            try { draft = JSON.parse(raw); } catch (e) {
                draft = null;
                clearDraftInStorage();
                clearAllFormFields();
                return;
            }
            if (!draft) {
                clearAllFormFields();
                return;
            }

            // Only restore if there's actual user-entered data (not just empty defaults)
            if (!hasUserEnteredData(draft)) {
                clearDraftInStorage();
                clearAllFormFields();
                return;
            }

            try {
                var titleInput = getElementById('taskInfo');
                if (titleInput) titleInput.value = draft.assignmentTitle || '';

                if (typeof quill !== 'undefined' && draft.assignmentContentHtml) {
                    var contentHtml = draft.assignmentContentHtml;
                    if (contentHtml && contentHtml.trim() !== '' && contentHtml !== '<p><br></p>') {
                        quill.root.innerHTML = contentHtml;
                    }
                }

                var deadlineInput = getDeadlineInput();
                if (deadlineInput) deadlineInput.value = draft.deadline || '';

                var pointsInput = getPointsInput();
                if (pointsInput) pointsInput.value = draft.points || '';

                var stopCheckbox = getElementById('stopSubmissions');
                if (stopCheckbox) stopCheckbox.checked = !!draft.stopSubmissions;

                var courseCheckboxes = document.querySelectorAll('.course-checkbox');
                var selectedMap = {};
                var i;
                if (draft.courses && draft.courses.length) {
                    for (i = 0; i < draft.courses.length; i++) selectedMap[String(draft.courses[i])] = true;
                }
                for (i = 0; i < courseCheckboxes.length; i++) {
                    var value = String(courseCheckboxes[i].value);
                    courseCheckboxes[i].checked = !!selectedMap[value];
                }

                if (draft.links && draft.links.length) {
                    for (i = 0; i < draft.links.length; i++) appendLinkPreview(draft.links[i]);
                }
            } catch (e) { }
            } catch (e) { }
        }

        function bindDraftPersistenceListeners() {
            var form = document.querySelector('form');
            if (form) {
                form.addEventListener('input', function () { saveDraftToStorage(); });
                form.addEventListener('change', function () { saveDraftToStorage(); });
                form.addEventListener('submit', function () {
                    clearDraftInStorage();
                    clearNavigationFlag();
                form.addEventListener('input', function () { saveDraftToStorage(); });
                form.addEventListener('change', function () { saveDraftToStorage(); });
                form.addEventListener('submit', function () {
                    clearDraftInStorage();
                    clearNavigationFlag();
                });
            }
            try {
                if (typeof quill !== 'undefined') {
                    quill.on('text-change', function () { saveDraftToStorage(); });
                    quill.on('text-change', function () { saveDraftToStorage(); });
                }
            } catch (e) { }
            document.addEventListener('click', function (event) {
            } catch (e) { }
            document.addEventListener('click', function (event) {
                var isAddLink = event.target && event.target.id === 'addLinkBtn';
                var isDelete = event.target && (event.target.closest ? event.target.closest('.delete-file') : null);
                if (isAddLink || isDelete) { setTimeout(function () { saveDraftToStorage(); }, 0); }
                if (isAddLink || isDelete) { setTimeout(function () { saveDraftToStorage(); }, 0); }
            });
        }

        function setNavigationFlag() {
            try { localStorage.setItem(NAVIGATION_FLAG, '1'); } catch (e) { }
            try { localStorage.setItem(NAVIGATION_FLAG, '1'); } catch (e) { }
        }

        function clearNavigationFlag() {
            try { localStorage.removeItem(NAVIGATION_FLAG); } catch (e) { }
            try { localStorage.removeItem(NAVIGATION_FLAG); } catch (e) { }
        }

        function hasNavigatedAway() {
            try {
                var flag = localStorage.getItem(NAVIGATION_FLAG);
                return flag === '1';
            } catch (e) { return false; }
        }

        function checkAndClearOrRestore() {
            try {
                var params = new URLSearchParams(window.location.search);
                var hasReuse = params.get('reuse');
                if (hasReuse) {
                    clearDraftInStorage();
                    clearNavigationFlag();
                    bindDraftPersistenceListeners();
                    return;
                }

                var hasSelectedRubric = params.get('selectedRubricID');
                var shouldRestore = hasNavigatedAway() || hasSelectedRubric;

                if (!shouldRestore) {
                    clearNavigationFlag();
                    clearAllFormFields();
                    bindDraftPersistenceListeners();
                    return;
                }

                clearNavigationFlag();

                var raw = localStorage.getItem(STORAGE_KEY);
                if (!raw) {
                    bindDraftPersistenceListeners();
                    return;
                }

                var draft = null;
                try { draft = JSON.parse(raw); } catch (e) {
                try { draft = JSON.parse(raw); } catch (e) {
                    clearDraftInStorage();
                    bindDraftPersistenceListeners();
                    return;
                }

                if (!draft) {
                    bindDraftPersistenceListeners();
                    return;
                }

                if (!hasUserEnteredData(draft)) {
                    clearDraftInStorage();
                    bindDraftPersistenceListeners();
                    return;
                }

                restoreDraftFromStorage();
                bindDraftPersistenceListeners();
            } catch (e) {
                bindDraftPersistenceListeners();
            }
        }

        function bindNavigationAwayListeners() {
            document.addEventListener('click', function (event) {
            document.addEventListener('click', function (event) {
                var link = event.target.closest ? event.target.closest('a') : null;
                if (!link || !link.href) return;
                var href = link.href;
                if (href.indexOf('create-rubric.php') !== -1 || href.indexOf('edit-rubric.php') !== -1) {
                    setNavigationFlag();
                }
            }, true);


            function bindEditLinks() {
                var editLinks = document.querySelectorAll('a[href*="edit-rubric.php"]');
                for (var i = 0; i < editLinks.length; i++) {
                    var link = editLinks[i];
                    if (link.dataset.navFlagSet) continue;
                    link.dataset.navFlagSet = '1';
                    link.addEventListener('click', function () {
                    link.addEventListener('click', function () {
                        setNavigationFlag();
                    });
                }
            }


            bindEditLinks();


            var modalEl = document.getElementById('selectRubricModal');
            if (modalEl) {
                modalEl.addEventListener('shown.bs.modal', function () {
                modalEl.addEventListener('shown.bs.modal', function () {
                    setTimeout(bindEditLinks, 100);
                });
            }
        }

        function initializeDraftPersistenceWithCleanStart() {
            bindNavigationAwayListeners();


            function attemptCheck() {
                if (typeof quill !== 'undefined' && quill.root) {
                    checkAndClearOrRestore();
                } else {
                    setTimeout(attemptCheck, 100);
                }
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function () { attemptCheck(); });
                document.addEventListener('DOMContentLoaded', function () { attemptCheck(); });
            } else {
                attemptCheck();
            }
        }

        initializeDraftPersistenceWithCleanStart();
    })();
</script>

<!-- Change the selected rubric  -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const rubricOptions = document.querySelectorAll('.rubric-option');
        const hiddenInput = document.getElementById('selectedRubricID');
        const cardRow = document.querySelector('.rubric-card-row');
        const titleDiv = cardRow.querySelector('.rubric-title');
        const infoDiv = cardRow.querySelector('.rubric-info');

        // Track currently selected option
        let selectedRubricId = hiddenInput.value;

        rubricOptions.forEach(option => {
            option.addEventListener('click', function () {
                // Highlight selected
                rubricOptions.forEach(o => o.style.backgroundColor = 'var(--pureWhite)');
                this.style.backgroundColor = 'var(--primaryColor)';

                // Update hidden input
                selectedRubricId = this.dataset.rubricId;
                hiddenInput.value = selectedRubricId;
            });
        });

        document.getElementById('confirmRubricBtn').addEventListener('click', function () {
            // Find selected option
            const selectedOption = document.querySelector(`.rubric-option[data-rubric-id="${selectedRubricId}"]`);
            if (selectedOption) {
                // Show card if hidden
                cardRow.style.display = '';

                // Update card content
                titleDiv.textContent = selectedOption.dataset.rubricTitle;
                infoDiv.textContent = `${selectedOption.dataset.rubricPoints} Points · ${selectedOption.dataset.rubricCriteria} Criteria`;
            }

            // Close modal
            const modalEl = document.getElementById('selectRubricModal');
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();

        });
    });


</script>