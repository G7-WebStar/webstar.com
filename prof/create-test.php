<?php
$activePage = 'create-test';

include('../shared/assets/database/connect.php');
date_default_timezone_set('Asia/Manila');
include("../shared/assets/processes/prof-session-process.php");

$mode = '';
$testID = 0;

if (isset($_GET['edit'])) {
    $mode = 'edit';
    $testID = intval($_GET['edit']);
} elseif (isset($_GET['reuse'])) {
    $mode = 'reuse';
    $testID = intval($_GET['reuse']);
}

// Get all courses owned by this user
$course = "SELECT courseID, courseCode 
           FROM courses 
           WHERE userID = '$userID'";
$courses = executeQuery($course);

// Save exam query
if (isset($_POST['save_exam'])) {
    $title = isset($_POST['taskTitle']) ? $_POST['taskTitle'] : '';
    $generalGuidance = $_POST['generalGuidance'];
    $testDeadline = !empty($_POST['deadline']) ? $_POST['deadline'] : null;
    $testTimeLimit = !empty($_POST['testTimeLimit']) ? $_POST['testTimeLimit'] : null;
    $testType = $_POST['testType'];
    $createdAt = date("Y-m-d H:i:s");
    $deadlineEnabled = isset($_POST['stopSubmissions']) ? 1 : 0;

    if (!empty($_POST['courses'])) {
        foreach ($_POST['courses'] as $courseID) {

            $assessments = "INSERT INTO assessments
                (courseID, assessmentTitle, type, deadline, createdAt, deadlineEnabled) 
                VALUES 
                ('$courseID', '$title', '$testType', " .
                ($testDeadline ? "'$testDeadline'" : "NULL") . ", 
                '$createdAt', '$deadlineEnabled')";

            executeQuery($assessments);

            // Kunin ang bagong assessmentID
            $assessmentID = mysqli_insert_id($conn);

            // Insert todo for each enrolled student
            $studentsQuery = "SELECT userID FROM enrollments WHERE courseID = '$courseID'";
            $studentsResult = executeQuery($studentsQuery);

            while ($student = mysqli_fetch_assoc($studentsResult)) {
                $userID = $student['userID'];
                $todoQuery = "INSERT INTO todo (userID, assessmentID, status) 
                  VALUES ('$userID', '$assessmentID', 'Pending')";
                executeQuery($todoQuery);
            }

            $testQuery = "INSERT INTO tests 
                (assessmentID, generalGuidance, testTimeLimit) 
                VALUES 
                ('$assessmentID', '$generalGuidance', '$testTimeLimit')";

            executeQuery($testQuery);

            // Kunin ang bagong testID
            $testID = mysqli_insert_id($conn);

            if (!empty($_POST['questions'])) {
                foreach ($_POST['questions'] as $qIndex => $question) {
                    $testQuestion = $question['testQuestion'] ?? '';
                    $questionType = $question['questionType'] ?? '';
                    $testQuestionPoints = $question['testQuestionPoints'] ?? 1;
                    $correctAnswer = !empty($question['correctAnswer'])
                        ? (is_array($question['correctAnswer']) ? implode(',', $question['correctAnswer']) : $question['correctAnswer'])
                        : '';

                    $testQuestionImage = null;
                    if (isset($_FILES['fileUpload']['name'][$qIndex]) && $_FILES['fileUpload']['error'][$qIndex] === UPLOAD_ERR_OK) {
                        $fileName = $_FILES['fileUpload']['name'][$qIndex];
                        $tmpName = $_FILES['fileUpload']['tmp_name'][$qIndex];

                        $uploadFolder = __DIR__ . "/../shared/assets/prof-uploads/";

                        if (!is_dir($uploadFolder)) {
                            mkdir($uploadFolder, 0777, true);
                        }

                        $newFileName = time() . "_" . basename($fileName);
                        $destination = $uploadFolder . $newFileName;

                        if (move_uploaded_file($tmpName, $destination)) {
                            $testQuestionImage = $newFileName;
                        }
                    }

                    $testQuestionQuery = "INSERT INTO testQuestions 
                        (testID, testQuestion, questionType, testQuestionPoints, correctAnswer, testQuestionImage)
                        VALUES 
                        ('$testID', '$testQuestion', '$questionType', '$testQuestionPoints', '$correctAnswer', " .
                        ($testQuestionImage ? "'$testQuestionImage'" : "NULL") . ")";
                    executeQuery($testQuestionQuery);

                    // Kunin bagong testQuestionID
                    $testQuestionID = mysqli_insert_id($conn);

                    if ($questionType === "Multiple Choice" && !empty($question['choices'])) {
                        foreach ($question['choices'] as $choiceText) {
                            $choiceText = mysqli_real_escape_string($conn, $choiceText);

                            $choiceQuery = "INSERT INTO testQuestionChoices 
                                (testQuestionID, choiceText) 
                                VALUES 
                                ('$testQuestionID', '$choiceText')";
                            executeQuery($choiceQuery);
                        }
                    }
                }
            }
        }
    }
}

// Selecting Tests made by the Instructor logged in
$testQuery = "
    SELECT 
        a.assessmentID,
        a.assessmentTitle,
        t.generalGuidance,
        a.createdAt,
        c.courseCode
    FROM assessments a
    JOIN tests t ON a.assessmentID = t.assessmentID
    JOIN courses c ON a.courseID = c.courseID
    JOIN users u ON c.userID = u.userID
    WHERE a.type = 'Test' AND u.userID = '$userID'
    GROUP BY a.assessmentTitle, t.generalGuidance
    ORDER BY a.createdAt DESC
";

$tests = executeQuery($testQuery);

// Storing reused data
$reusedData = null;

// If the instructor chose to reuse, it'll check the button if it was set
if (isset($_GET['reuse']) || isset($_GET['edit'])) {
    $reuseID = isset($_GET['reuse']) ? intval($_GET['reuse']) : intval($_GET['edit']);

    // Getting the data of the selected test to reuse or edit
    $reuseQuery = "
        SELECT 
            a.assessmentTitle,
            a.deadline,
            a.deadlineEnabled,
            a.type AS testType,
            t.testID,
            t.generalGuidance,
            t.testTimeLimit,
            tq.testQuestionID,
            tq.testQuestion,
            tq.questionType,
            tq.testQuestionPoints,
            tq.correctAnswer,
            tq.testQuestionImage,
            tqc.choiceID,
            tqc.choiceText
        FROM assessments a
        JOIN tests t ON a.assessmentID = t.assessmentID
        LEFT JOIN testQuestions tq ON t.testID = tq.testID
        LEFT JOIN testQuestionChoices tqc ON tq.testQuestionID = tqc.testQuestionID
        JOIN courses crs ON a.courseID = crs.courseID
        JOIN users u ON crs.userID = u.userID
        WHERE a.assessmentID = '$reuseID'
          AND a.type = 'Test'
          AND u.userID = '$userID'
        ORDER BY tq.testQuestionID, tqc.choiceID
    ";

    $reuseResult = executeQuery($reuseQuery);
    if ($reuseResult && $reuseResult->num_rows > 0) {
        $reusedData = [];
        while ($row = $reuseResult->fetch_assoc()) {
            $reusedData[] = $row; // store all rows with questions, choices, and files
        }
    } else {
        // Invalid or unauthorized reuse/edit attempt
        header("Location: create-test.php");
        exit();
    }
}

if (!empty($reusedData)) {
    // Populate main test fields
    $mainData = [
        'assessmentTitle' => $reusedData[0]['assessmentTitle'],
        'generalGuidance' => $reusedData[0]['generalGuidance'],
        'testTimeLimit' => $reusedData[0]['testTimeLimit'],
        'deadline' => $reusedData[0]['deadline'],
        'deadlineEnabled' => $reusedData[0]['deadlineEnabled'],
        'testType' => $reusedData[0]['testType']
    ];

    // Collect test questions and their choices
    $testQuestions = [];
    foreach ($reusedData as $row) {
        $qID = $row['testQuestionID'];
        if (empty($qID))
            continue;

        if (!isset($testQuestions[$qID])) {
            $testQuestions[$qID] = [
                'testQuestionID' => $row['testQuestionID'],
                'testQuestion' => $row['testQuestion'],
                'questionType' => $row['questionType'],
                'testQuestionPoints' => $row['testQuestionPoints'],
                'correctAnswer' => $row['correctAnswer'],
                'testQuestionImage' => $row['testQuestionImage'],
                'choices' => []
            ];
        }

        // Add choices if present
        if (!empty($row['choiceID']) && !empty($row['choiceText'])) {
            $testQuestions[$qID]['choices'][] = [
                'choiceID' => $row['choiceID'],
                'choiceText' => $row['choiceText']
            ];
        }
    }

    // Re-index test questions for easier frontend use
    $testQuestions = array_values($testQuestions);
}

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        <?php
        echo isset($_GET['reuse']) ? 'Recreate Test' : (isset($_GET['edit']) ? 'Edit Test' : 'Post Test');
        ?> ✦ Webstar
    </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="../shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="../shared/assets/css/create-exam.css">
    <link rel="stylesheet" href="../shared/assets/css/add-lesson.css">
    <link rel="stylesheet" href="../shared/assets/css/sidebar-and-container-styles.css">
    <link rel="stylesheet" href="../shared/assets/css/post-announcement.css">
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
            <div class="col main-container m-0 p-0 mx-0 mx-md-2 p-md-4 overflow-y-auto position-relative">
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
                                        <span class="text-sbold text-20"><?php
                                        if (isset($_GET['edit'])) {
                                            echo 'Edit Test';
                                        } elseif (isset($_GET['reuse'])) {
                                            echo 'Recreate Test';
                                        } else {
                                            echo 'Create Test';
                                        }
                                        ?></span>
                                    </div>

                                    <!-- Assign Existing Task Button -->
                                    <div
                                        class="col-12 col-md-auto text-center d-flex d-md-block justify-content-center justify-content-md-end mt-3 mt-md-0">
                                        <button type="button"
                                            class="btn btn-sm px-3 py-1 rounded-pill text-reg text-md-14 my-1 d-flex align-items-center gap-2"
                                            style="background-color: var(--primaryColor); border: 1px solid var(--black); color: var(--black); width: auto!important;"
                                            data-bs-toggle="modal" data-bs-target="#reuseTaskModal">
                                            <span class="material-symbols-rounded" style="font-size:16px">quiz</span>
                                            <span>Create an existing exam</span>
                                        </button>
                                    </div>
                                </div>

                                <!-- Form starts -->
                                <form action="" method="POST" enctype="multipart/form-data">

                                    <!-- Hidden input for test type -->
                                    <input type="hidden" name="mode"
                                        value="<?= isset($_GET['edit']) ? 'edit' : (isset($_GET['reuse']) ? 'reuse' : 'new') ?>">
                                    <input type="hidden" name="testID"
                                        value="<?= $_GET['edit'] ?? $_GET['reuse'] ?? '' ?>">
                                    <input type="hidden" name="testType" value="test">

                                    <div class="row">
                                        <div class="col-12 pt-3">
                                            <label for="lessonInfo" class="form-label text-med text-16">Test
                                                Information</label>
                                            <input type="text"
                                                class="form-control textbox mb-2 px-3 py-2 text-reg text-16"
                                                id="lessonInfo" name="taskTitle" aria-describedby="lessonInfo"
                                                placeholder="Test Title" required>
                                        </div>
                                    </div>

                                    <!-- Rich Text Editor -->
                                    <div class="row">
                                        <div class="col-12 mb-3">
                                            <div class="editor-wrapper">
                                                <div id="editor"></div>
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
                                            <input type="hidden" name="generalGuidance" id="generalGuidance">
                                        </div>
                                    </div>

                                    <div class="row g-3 m-0 p-0">
                                        <div class="row g-3 mt-4 m-0 p-0">
                                            <!-- Deadline -->
                                            <div class="col-md-6 m-0 p-0 mb-3 mb-md-0">
                                                <label class="form-label text-med text-16">
                                                    Deadline
                                                </label>

                                                <div class="input-group">
                                                    <input type="datetime-local" name="deadline"
                                                        class="form-control textbox text-reg text-16 me-0 me-md-2"
                                                        required>
                                                </div>
                                            </div>

                                            <!-- Time limit -->
                                            <div class="col-md-6 m-0 p-0">
                                                <label class="form-label text-med text-16">
                                                    Time Limit (in minutes)
                                                </label>
                                                <input type="number" name="testTimeLimit"
                                                    class="form-control textbox text-reg text-16"
                                                    placeholder="No time limit if left blank" />
                                            </div>
                                        </div>
                                        <!-- wala pa to -->
                                        <div class="form-check mt-2 col d-flex align-items-center">
                                            <input class="form-check-input" type="checkbox" id="stopSubmissions"
                                                name="stopSubmissions" style="border: 1px solid var(--black);" />
                                            <label class="form-check-label text-reg text-14 ms-2"
                                                style="margin-top: 4.5px;" for="stopSubmissions">
                                                Stop accepting submissions after the deadline.
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Learning Materials -->
                                    <div class="row align-items-center">
                                        <div class="col-6">
                                            <div class="learning-materials">
                                                <label class="text-med text-16 mt-5 mb-3">Exam Items</label>
                                            </div>
                                        </div>
                                        <div class="col-6 text-end">
                                            <!-- Show only on mobile -->
                                            <label for="TotalPoints" class="form-label text-med text-16 mt-5 mb-3">
                                                Total Points: <span id="totalPoints">0</span>
                                            </label>
                                        </div>
                                    </div>
                                    <!-- Templates -->
                                    <template id="identificationTemplate">
                                        <div class="row position-relative">
                                            <div class="col-12 mb-1">
                                                <div
                                                    class="form-control textbox mb-3 ps-2 pe-3 pt-3 text-reg text-14 text-muted position-relative">
                                                    <!-- Delete Button -->
                                                    <button type="button" class="delete-template"
                                                        style="position: absolute; top: 17px; right: 5px; background: none; border: none; color: var(--black); cursor: pointer;">
                                                        <i class="fas fa-times"></i>
                                                    </button>

                                                    <!-- Hidden input for question type -->
                                                    <input type="hidden" name="questions[0][questionType]"
                                                        value="identification">

                                                    <div class="input-group text-reg text-14 text-muted mb-3 mt-2">
                                                        <span class="input-group-text text-bold rounded-left ms-3 p-3"
                                                            style="background-color: var(--primaryColor);">1</span>
                                                        <input type="text" class="question-box form-control text-reg"
                                                            placeholder="Question" name="questions[0][testQuestion]">
                                                        <span
                                                            class="input-group-text bg-light rounded-right me-3 image-icon"
                                                            style="cursor: pointer;">
                                                            <i class="fas fa-image"></i>
                                                        </span>
                                                    </div>

                                                    <div class="mb-3 ms-3 image-container position-relative"
                                                        style="display: none; width: 300px; height: 200px;">

                                                        <!-- Delete Button (inside upper right of image) -->
                                                        <button type="button" class="delete-image"
                                                            style="position: absolute; top: 8px; right: 8px; background: white; border: none; color: var(--black); cursor: pointer; z-index: 2; border-radius: 50%; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center;">
                                                            <i class="fas fa-times"></i>
                                                        </button>


                                                        <img src="../shared/assets/img/placeholder/placeholder.png"
                                                            class="question-image"
                                                            style="width: 100%; height: 100%; border-radius: 10px; border: 1px solid var(--black); object-fit: cover; background-color: var(--primaryColor); cursor: pointer;">

                                                        <input type="file" name="fileUpload[0]" accept="image/*"
                                                            class="image-upload" style="display: none;">
                                                    </div>

                                                    <div class="row position-relative p-3">
                                                        <!-- Points Column -->
                                                        <div class="col-auto flex-shrink-0 mb-2">
                                                            <div class="text-reg mb-1">Points</div>
                                                            <input type="number" name="questions[0][testQuestionPoints]"
                                                                class="border rounded p-2" placeholder="0" min="0"
                                                                required
                                                                style="width: 70px; outline: none; box-shadow: none; border: 1px solid var(--black);">
                                                        </div>

                                                        <!-- Correct Answers Column -->
                                                        <div class="col-auto">
                                                            <div class="text-reg mb-1">Correct Answers</div>
                                                            <div class="d-flex align-items-center overflow-auto answers-scroll"
                                                                style="white-space: nowrap;">
                                                                <div
                                                                    class="answers-container d-flex align-items-center flex-nowrap">
                                                                    <!-- Answer inputs appended here -->
                                                                </div>
                                                                <button type="button"
                                                                    class="btn text-reg rounded-pill add-answer-btn flex-shrink-0"
                                                                    style="background-color: var(--primaryColor);transform: none !important; box-shadow: none !important;color:var(--black)!important">
                                                                    + Add
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </template>

                                    <template id="multipleChoiceTemplate">
                                        <div class="row position-relative multiple-choice-item">
                                            <div class="col-12 mb-1">
                                                <div
                                                    class="form-control textbox mb-3 ps-2 pe-3 pt-3 text-reg text-14 text-muted position-relative">

                                                    <!-- Delete Button -->
                                                    <button type="button" class="delete-template"
                                                        style="position: absolute; top: 17px; right: 5px; background: none; border: none; color: var(--black); cursor: pointer;">
                                                        <i class="fas fa-times"></i>
                                                    </button>

                                                    <!-- Hidden input for question type -->
                                                    <input type="hidden" name="questions[0][questionType]"
                                                        value="multipleChoice">

                                                    <!-- Question -->
                                                    <div class="input-group text-reg text-14 text-muted mb-4 mt-2">
                                                        <span
                                                            class="input-group-text text-bold rounded-left ms-3 question-number p-3"
                                                            style="background-color: var(--primaryColor);">1</span>
                                                        <input type="text" class="question-box form-control text-reg"
                                                            placeholder="Question" name="questions[0][testQuestion]">
                                                        <span
                                                            class="input-group-text bg-light rounded-right me-3 image-icon"
                                                            style="cursor: pointer;">
                                                            <i class="fas fa-image"></i>
                                                        </span>
                                                    </div>

                                                    <!-- Image upload -->
                                                    <div class="mb-3 ms-3 image-container position-relative"
                                                        style="display: none; width: 300px; height: 200px;">
                                                        <!-- Delete Button (inside upper right of image) -->
                                                        <button type="button" class="delete-image"
                                                            style="position: absolute; top: 8px; right: 8px; background: white; border: none; color: var(--black); cursor: pointer; z-index: 2; border-radius: 50%; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center;">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                        <img src="../shared/assets/img/placeholder/placeholder.png"
                                                            class="question-image"
                                                            style="width: 100%; height: 100%; border-radius: 10px; border: 1px solid var(--black); object-fit: cover; background-color: var(--primaryColor); cursor: pointer;">
                                                        <input type="file" name="fileUpload[0]" accept="image/*"
                                                            class="image-upload" style="display: none;">
                                                    </div>

                                                    <!-- Choices -->
                                                    <div class="d-flex align-items-center ms-3 mb-2">
                                                        <div class="me-4">
                                                            <div class="text-reg mb-1">Choices</div>
                                                            <div class="radio-choices-container ps-2"
                                                                style="max-height: 200px; overflow-y: auto; padding-right: 5px;">
                                                                <!-- Choices will be added here dynamically -->
                                                            </div>
                                                            <!-- Add button -->
                                                            <button type="button"
                                                                class="btn text-reg rounded-pill add-radio-btn"
                                                                style="background-color: var(--primaryColor); transform: none !important; box-shadow: none !important;color:var(--black)!important">+
                                                                Add</button>
                                                        </div>
                                                    </div>

                                                    <!-- Points -->
                                                    <div class="d-flex align-items-center ms-3 mb-4 mt-3">
                                                        <div class="me-4">
                                                            <div class="text-reg mb-1">Points</div>
                                                            <input type="number" name="questions[0][testQuestionPoints]"
                                                                class="border rounded p-2" placeholder="0" min="0"
                                                                required
                                                                style="width: 70px; outline: none; box-shadow: none; ">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>

                                    <!-- Master Container -->
                                    <div id="allQuestionsContainer"></div>

                                    <!-- Buttons -->
                                    <div class="row">
                                        <div class="col-12 mb-3">
                                            <button type="button" id="addMultipleChoice"
                                                class="btn text-reg rounded-pill mt-2 me-2"
                                                style="background-color: var(--primaryColor); width: 180px;">
                                                <i class="far fa-dot-circle me-2"></i> Multiple Choice
                                            </button>

                                            <button type="button" id="addIdentification"
                                                class="btn text-reg rounded-pill me-1 mt-2"
                                                style="background-color: var(--primaryColor); width: 180px;">
                                                <i class="fas fa-align-left me-2"></i> Identification
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Course selection + Post button -->
                                    <div class="row align-items-center justify-content-between text-center text-md-start mt-3"
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
                                                    type="button" data-bs-toggle="dropdown" aria-expanded="false"
                                                    style=" width: auto!important;">
                                                    <span class="me-2">Select Course</span>
                                                </button>
                                                <ul class="dropdown-menu p-2 mt-2"
                                                    style="min-width: 200px; border: 1px solid var(--black);">
                                                    <?php
                                                    if ($courses && $courses->num_rows > 0) {
                                                        while ($course = $courses->fetch_assoc()) { ?>
                                                            <li>
                                                                <div class="form-check">
                                                                    <input class="form-check-input course-checkbox"
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
                                            <button type="submit" name="save_exam"
                                                class="px-4 py-2 rounded-pill text-sbold text-md-14 mt-4 mt-md-0"
                                                style="background-color: var(--primaryColor); border: 1px solid var(--black);">
                                                 <?php
                                                        if (isset($_GET['edit'])) {
                                                            echo 'Save Changes';
                                                        } elseif (isset($_GET['reuse'])) {
                                                            echo 'Recreate';
                                                        } else {
                                                            echo 'Create';
                                                        }
                                                        ?>
                                            </button>

                                        </div>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Alert Container Toasts -->
                <div id="toastContainer"
                    class="position-absolute top-0 start-50 translate-middle-x pt-5 pt-md-1  d-flex flex-column align-items-center"
                    style="z-index:1100; pointer-events:none;">
                </div>
            </div>
        </div>
    </div>

    <!-- Reuse Test Modal -->
    <div class="modal fade" id="reuseTaskModal" tabindex="-1" aria-labelledby="reuseTaskModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered py-4" style="max-width: 700px; height: 80vh;">
            <div class="modal-content d-flex flex-column" style="height: 100%;">

                <!-- HEADER -->
                <div class="modal-header flex-shrink-0">
                    <div class="modal-title text-sbold text-20 ms-3" id="reuseTestModalLabel">
                        Reuse an existing test
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        style="transform: scale(0.8); filter: grayscale(100%);"></button>
                </div>

                <!-- BODY -->
                <div class="modal-body flex-grow-1 overflow-auto">
                    <p class="mb-3 text-med text-14 mx-3" style="color: var(--black);">
                        Select a test you’ve previously created to reuse its questions, settings, and details.
                        You can review and edit before posting again.
                    </p>
                    <div class="col p-0 m-3">
                        <div class="card rounded-3 mb-2 border-0">
                            <div class="card-body p-0">
                                <!-- TEST OPTIONS -->
                                <?php if ($tests && $tests->num_rows > 0) {
                                    while ($test = $tests->fetch_assoc()) { ?>
                                        <div class="rubric-option rounded-3 d-flex align-items-center justify-content-between mb-2 w-100"
                                            style="cursor: pointer; background-color: var(--pureWhite); border: 1px solid var(--black);"
                                            onclick='window.location.href="create-test.php?reuse=<?php echo $test["assessmentID"]; ?>"'>
                                            <div style="line-height: 1.5; padding:10px 15px;">
                                                <div class="text-sbold text-14"
                                                    style="display:-webkit-box; -webkit-line-clamp:1; -webkit-box-orient:vertical; overflow:hidden;">
                                                    <?php echo htmlspecialchars($test['assessmentTitle']); ?>
                                                </div>
                                                <div class="text-med text-muted text-12"
                                                    style="display:-webkit-box; -webkit-line-clamp:1; -webkit-box-orient:vertical; overflow:hidden;">
                                                    <?php
                                                    echo date('F j, Y g:i A', strtotime($test['createdAt'])) . ' · ' . $test['courseCode'];
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php }
                                } else { ?>
                                    <div class="text-muted text-reg text-14 py-2">No existing tests found.</div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FOOTER -->
                <div class="modal-footer border-top flex-shrink-0 py-4"></div>
            </div>
        </div>
    </div>


    <!-- Quill JS -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script>
        // Quill Editor
        var quill = new Quill('#editor', {
            theme: 'snow',
            placeholder: 'Test General Guidelines',
            modules: {
                toolbar: '#toolbar'
            }
        });

        const maxWords = 200;
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

        const form = document.querySelector("form");

        form.addEventListener("submit", function (e) {
            // --- Quill ---
            let plainText = quill.getText().trim();
            const guidelinesInput = document.getElementById("generalGuidance"); // hidden input
            guidelinesInput.value = plainText;

            if (plainText.length === 0) {
                e.preventDefault();
                quill.root.focus(); // focus editor
                guidelinesInput.setCustomValidity('Please fill out this field.');
                guidelinesInput.reportValidity();
                return false;
            } else {
                guidelinesInput.setCustomValidity('');
            }

            let valid = true;
            let message = "";

            // Multiple Choice Validation
            document.querySelectorAll(".multiple-choice-item").forEach(mc => {
                if (!mc.offsetParent) return;
                const radios = mc.querySelectorAll("input[type='radio']");
                let oneChecked = Array.from(radios).some(r => r.checked);

                const innerCard = mc.querySelector(".textbox, .card, .form-control");
                if (!oneChecked) {
                    valid = false;
                    if (!message) message = "Choose the correct answers for all multiple choice questions.";
                    if (innerCard) innerCard.style.border = "2px solid red";
                } else {
                    if (innerCard) innerCard.style.border = "";
                }

                // Remove empty choices
                mc.querySelectorAll("input.choice-input").forEach(input => {
                    if (!input.value.trim()) input.closest(".form-check")?.remove();
                });
            });

            // Identification Validation
            document.querySelectorAll(".textbox").forEach(idBox => {
                if (!idBox.offsetParent) return;
                const type = idBox.querySelector("input[type='hidden'][name*='questionType']")?.value.toLowerCase();
                if (type === "identification") {
                    const answers = Array.from(idBox.querySelectorAll("input[name*='correctAnswer']"));
                    const hasAnswer = answers.some(a => a.value.trim() !== "");
                    if (!hasAnswer) {
                        valid = false;
                        if (!message) message = "Provide at least one correct answer for all identification questions.";
                        idBox.style.border = "2px solid red";
                    } else {
                        idBox.style.border = "";
                    }
                }
            });

            // Deadline Validation
            const deadlineInput = document.querySelector('input[name="deadline"]');
            if (deadlineInput) {
                const deadlineValue = new Date(deadlineInput.value);
                const now = new Date();
                now.setSeconds(0, 0);

                if (!deadlineInput.value || deadlineValue < now) {
                    valid = false;
                    if (!message) message = "Please set the deadline to a future date or time within today.";
                    deadlineInput.style.border = "2px solid red";
                } else {
                    deadlineInput.style.border = "";
                }
            }

            // Selected Courses Validation
            const checkedCourses = document.querySelectorAll('.course-checkbox:checked');
            if (checkedCourses.length === 0) {
                valid = false;
                if (!message) message = "Please select at least one course before submitting.";
            }

            if (!valid) {
                e.preventDefault();
                showAlert(message);
            }
        });

        // Show Alert
        function showAlert(message) {
            const container = document.getElementById("toastContainer");

            const alert = document.createElement("div");
            alert.className = "alert alert-danger fade show mb-2 text-center d-flex align-items-center justify-content-center shadow-lg px-3 py-2 text-reg text-14";
            alert.innerHTML = `
            <i class="bi bi-x-circle-fill me-2 fs-6"></i>
            <span>${message}</span>
        `;

            container.appendChild(alert);

            setTimeout(() => {
                alert.classList.remove("show");
                alert.classList.add("fade");
                setTimeout(() => alert.remove(), 500);
            }, 4000);
        }

        // Identification / Multiple Choice Management
        let questionCount = 0;
        const mainContainer = document.getElementById("allQuestionsContainer");

        function renumberQuestions() {
            const allQuestions = mainContainer.querySelectorAll(".textbox");
            let count = 0;
            allQuestions.forEach(q => {
                const hidden = q.querySelector("input[type='hidden'][name*='questionType']");
                if (hidden) {
                    count++;
                    const numberSpan = q.querySelector(".question-number, .input-group-text");
                    if (numberSpan) numberSpan.textContent = count;
                }
            });
        }

        // Add Identification
        document.getElementById("addIdentification").addEventListener("click", () => {
            const clone = document.getElementById("identificationTemplate").content.cloneNode(true);
            const numberSpan = clone.querySelector(".input-group-text");
            if (numberSpan) numberSpan.textContent = mainContainer.querySelectorAll(".textbox").length + 1;

            clone.querySelectorAll("input, textarea, select").forEach(input => {
                if (input.name) input.name = input.name.replace(/\[\d+\]/, `[${questionCount}]`);
            });

            const fileInput = clone.querySelector(".image-upload");
            if (fileInput) fileInput.name = `fileUpload[${questionCount}]`;

            mainContainer.appendChild(clone);
            questionCount++;
            renumberQuestions();
        });

        // Add answer (delegated)
        document.addEventListener("click", function (e) {
            if (e.target.closest(".add-answer-btn")) {
                const button = e.target.closest(".add-answer-btn");
                const container = button.closest(".answers-scroll").querySelector(".answers-container");
                const questionBox = button.closest(".textbox");
                const questionIndexInput = questionBox.querySelector("input[type='hidden'][name*='questionType']");
                const questionIndex = questionIndexInput.name.match(/questions\[(\d+)\]/)[1];

                const wrapper = document.createElement("div");
                wrapper.classList.add("answer-wrapper", "me-2", "d-inline-flex", "align-items-center");

                const input = document.createElement("input");
                input.type = "text";
                input.placeholder = "Answer";
                input.classList.add("border", "rounded", "p-2", "text-reg", "me-5");
                input.style.width = "120px";
                input.name = `questions[${questionIndex}][correctAnswer][]`;

                const removeBtn = document.createElement("button");
                removeBtn.type = "button";
                removeBtn.innerHTML = `<i class="fas fa-times"></i>`;
                removeBtn.onclick = () => wrapper.remove();

                wrapper.appendChild(input);
                wrapper.appendChild(removeBtn);
                container.appendChild(wrapper);
                container.scrollLeft = container.scrollWidth;
            }
        });

        // Single Delete Handler for All Blocks
        document.addEventListener("click", function (e) {
            const delBtn = e.target.closest(".delete-template");
            if (!delBtn) return;

            const block = delBtn.closest(".textbox, .multiple-choice-item, .row");
            if (block) block.remove();

            renumberQuestions();
            updateTotalPoints();
        });

        // Multiple Choice
        document.getElementById("addMultipleChoice").addEventListener("click", () => {
            const clone = document.getElementById("multipleChoiceTemplate").content.cloneNode(true);
            const numberSpan = clone.querySelector(".question-number");
            if (numberSpan) numberSpan.textContent = mainContainer.querySelectorAll(".textbox").length + 1;

            clone.querySelectorAll("input, select, textarea").forEach(input => {
                if (input.name && !input.name.includes("questionType")) {
                    input.name = input.name.replace(/\[\d+\]/g, `[${questionCount}]`);
                }
            });

            const hiddenType = clone.querySelector("input[type='hidden'][name*='questionType']");
            if (hiddenType) {
                hiddenType.name = `questions[${questionCount}][questionType]`;
                hiddenType.value = "Multiple Choice";
            }

            const fileInput = clone.querySelector(".image-upload");
            if (fileInput) fileInput.name = `fileUpload[${questionCount}]`;

            mainContainer.appendChild(clone);
            questionCount++;
            renumberQuestions();
        });

        // Add Multiple Choice Choices
        document.getElementById("allQuestionsContainer").addEventListener("click", function (e) {
            if (e.target.closest(".add-radio-btn")) {
                const button = e.target.closest(".add-radio-btn");
                const container = button.closest(".multiple-choice-item").querySelector(".radio-choices-container");
                const existingChoices = container.querySelectorAll(".form-check").length;
                if (existingChoices >= 4) return;

                const questionIndex = button.closest(".multiple-choice-item").querySelector("input[type='hidden']").name.match(/\d+/)[0];
                const newChoice = document.createElement("div");
                newChoice.classList.add("form-check", "text-start", "d-flex", "align-items-center", "mb-2", "position-relative");

                const radio = document.createElement("input");
                radio.type = "radio";
                radio.classList.add("form-check-input", "me-2");
                radio.name = `questions[${questionIndex}][correctAnswer]`;
                radio.value = "";

                const input = document.createElement("input");
                input.type = "text";
                input.classList.add("choice-input", "text-reg", "me-4", "text-truncate");
                input.name = `questions[${questionIndex}][choices][]`;
                input.placeholder = "Choice";
                input.style.border = "none";
                input.style.outline = "none";
                input.style.width = "100%";
                input.style.maxWidth = "200px";
                input.style.background = "transparent";

                input.addEventListener("input", () => {
                    radio.value = input.value;
                });

                const deleteBtn = document.createElement("button");
                deleteBtn.type = "button";
                deleteBtn.classList.add("delete-template");
                deleteBtn.style.position = "absolute";
                deleteBtn.style.top = "2px";
                deleteBtn.style.right = "1px";
                deleteBtn.style.background = "none";
                deleteBtn.style.border = "none";
                deleteBtn.style.color = "var(--black)";
                deleteBtn.style.cursor = "pointer";
                deleteBtn.innerHTML = '<i class="fas fa-times"></i>';
                deleteBtn.addEventListener("click", () => newChoice.remove());

                newChoice.appendChild(radio);
                newChoice.appendChild(input);
                newChoice.appendChild(deleteBtn);

                container.appendChild(newChoice);
            }
        });

        // Toggle Image Container
        document.addEventListener("click", function (e) {
            if (e.target.closest(".image-icon")) {
                const card = e.target.closest(".textbox");
                const imageContainer = card.querySelector(".image-container");
                if (imageContainer.style.display === "none" || imageContainer.style.display === "") {
                    imageContainer.style.display = "block";
                } else {
                    imageContainer.style.display = "none";
                }
            }
        });

        // Image Upload
        function bindImageUpload(img) {
            const fileInput = img.nextElementSibling;
            img.addEventListener("click", () => fileInput.click());
            fileInput.addEventListener("change", () => {
                const file = fileInput.files[0];
                if (!file) return;

                const allowed = ["image/jpeg", "image/png", "image/jpg"];
                const maxSize = 5 * 1024 * 1024;

                // validate before preview
                if (!allowed.includes(file.type)) {
                    showAlert("Only JPG, JPEG, and PNG image formats are allowed.");
                    fileInput.value = "";
                    return;
                }

                if (file.size > maxSize) {
                    showAlert("Maximum file size is 5MB.");
                    fileInput.value = "";
                    return;
                }

                const reader = new FileReader();
                reader.onload = e => img.src = e.target.result;
                reader.readAsDataURL(file);
            });

        }

        document.addEventListener("click", function (e) {
            const delImgBtn = e.target.closest(".delete-image");
            if (!delImgBtn) return;

            const container = delImgBtn.closest(".image-container");
            if (!container) return;

            // Reset preview
            const img = container.querySelector(".question-image");
            const fileInput = container.querySelector(".image-upload");

            img.src = "../shared/assets/img/placeholder/placeholder.png";
            fileInput.value = "";
            container.style.display = "none"; // hide again
        });

        document.querySelectorAll(".question-image").forEach(img => bindImageUpload(img));

        const observer = new MutationObserver(() => {
            document.querySelectorAll(".question-image").forEach(img => {
                if (!img.dataset.bound) {
                    bindImageUpload(img);
                    img.dataset.bound = "true";
                }
            });
        });

        observer.observe(mainContainer, {
            childList: true,
            subtree: true
        });

        // Update Total Points
        function updateTotalPoints() {
            let total = 0;
            document.querySelectorAll('#allQuestionsContainer input[type="number"]').forEach(input => {
                const val = parseInt(input.value);
                if (!isNaN(val)) total += val;
            });

            document.querySelectorAll('label[for="TotalPoints"]').forEach(label => {
                label.textContent = `Total Points: ${total}`;
            });
        }

        mainContainer.addEventListener('input', function (e) {
            if (e.target.type === "number") updateTotalPoints();
        });

        const totalPointsObserver = new MutationObserver(() => updateTotalPoints());
        totalPointsObserver.observe(mainContainer, {
            childList: true,
            subtree: true
        });

        updateTotalPoints();
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>