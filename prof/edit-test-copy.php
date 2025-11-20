<?php
$activePage = 'create-test';

// PHPMailer removed as requested
// use PHPMailer\PHPMailer\PHPMailer;
// use PHPMailer\PHPMailer\Exception;

include('../shared/assets/database/connect.php');
date_default_timezone_set('Asia/Manila');
include("../shared/assets/processes/prof-session-process.php");

// Removed PHPMailer requires
// if (!class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
//     require './shared/assets/phpmailer/src/Exception.php';
//     require './shared/assets/phpmailer/src/PHPMailer.php';
//     require './shared/assets/phpmailer/src/SMTP.php';
// }

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
    $mode = $_POST['mode'] ?? 'new';
    $existingTestID = intval($_POST['testID'] ?? 0);

    $titleRaw = $_POST['taskTitle'] ?? '';
    $title = mysqli_real_escape_string($conn, $titleRaw);

    $generalGuidanceRaw = $_POST['generalGuidance'] ?? '';
    $generalGuidance = mysqli_real_escape_string($conn, $generalGuidanceRaw);

    $testDeadline = !empty($_POST['deadline']) ? $_POST['deadline'] : null;
    $testTimeLimitMinutes = !empty($_POST['testTimeLimit']) ? max(1, intval($_POST['testTimeLimit'])) : null;
    $testTimeLimitSeconds = $testTimeLimitMinutes !== null ? $testTimeLimitMinutes * 60 : null;
    $testTypeRaw = $_POST['testType'] ?? 'Test';
    $testType = mysqli_real_escape_string($conn, $testTypeRaw);
    $createdAt = date("Y-m-d H:i:s");
    $deadlineEnabled = isset($_POST['stopSubmissions']) ? 1 : 0;

    if (!empty($_POST['courses'])) {
        foreach ($_POST['courses'] as $courseID) {

            if ($mode === 'new' || $mode === 'reuse') {

                /* INSERT NEW ASSESSMENT */
                $assessmentInsert = "INSERT INTO assessments
                (courseID, assessmentTitle, type, deadline, createdAt, deadlineEnabled)
                VALUES 
                ('$courseID', '$title', '$testType', " .
                    ($testDeadline ? "'" . mysqli_real_escape_string($conn, $testDeadline) . "'" : "NULL") . ",
                '$createdAt', '$deadlineEnabled')";
                executeQuery($assessmentInsert);

                $assessmentID = mysqli_insert_id($conn);

                /* Insert TODOS */
                $studentsQuery = "SELECT userID FROM enrollments WHERE courseID = '$courseID'";
                $studentsResult = executeQuery($studentsQuery);
                while ($student = mysqli_fetch_assoc($studentsResult)) {
                    $uid = $student['userID'];
                    $todoInsert = "INSERT INTO todo (userID, assessmentID, status)
                       VALUES ('$uid', '$assessmentID', 'Pending')";
                    executeQuery($todoInsert);
                }

                /* INSERT NEW TEST */
                $testInsert = "INSERT INTO tests 
                (assessmentID, generalGuidance, testTimeLimit)
                VALUES 
                ('$assessmentID', '$generalGuidance', " .
                    ($testTimeLimitSeconds !== null ? "'$testTimeLimitSeconds'" : "NULL") . ")";
                executeQuery($testInsert);

                $testID = mysqli_insert_id($conn);

            } elseif ($mode === 'edit') {

                /* UPDATE EXISTING ASSESSMENT*/
                $updateAssessment = "UPDATE assessments SET 
                assessmentTitle='$title',
                deadline=" .
                    ($testDeadline ? "'" . mysqli_real_escape_string($conn, $testDeadline) . "'" : "NULL") . "
                WHERE assessmentID='$testID'";
                executeQuery($updateAssessment);

                /* UPDATE EXISTING TEST*/
                $updateTest = "UPDATE tests SET
                generalGuidance='$generalGuidance',
                testTimeLimit=" .
                    ($testTimeLimitSeconds !== null ? "'$testTimeLimitSeconds'" : "NULL") . "
                WHERE assessmentID='$testID'";
                executeQuery($updateTest);

                // Get existing testID
                $getTest = executeQuery("SELECT testID FROM tests WHERE assessmentID='$testID' LIMIT 1");
                $row = mysqli_fetch_assoc($getTest);
                $testID = $row['testID'];

                // Delete only if the mode is edit + user provided new questions
                if ($mode === 'edit' && !empty($_POST['questions'])) {
                    executeQuery("DELETE FROM testQuestionChoices WHERE testQuestionID IN 
                     (SELECT testQuestionID FROM testQuestions WHERE testID='$testID')");
                    executeQuery("DELETE FROM testQuestions WHERE testID='$testID'");
                }
            }

            /* INSERT NEW QUESTIONS + CHOICES (BOTH FOR NEW/REUSE AND EDIT)*/
            if (!empty($_POST['questions'])) {

                foreach ($_POST['questions'] as $qIndex => $question) {

                    $testQuestion = $question['testQuestion'] ?? '';
                    $questionType = $question['questionType'] ?? '';
                    $testPoints = $question['testQuestionPoints'] ?? 1;

                    $correctAnswer = !empty($question['correctAnswer'])
                        ? (is_array($question['correctAnswer'])
                            ? implode(',', $question['correctAnswer'])
                            : $question['correctAnswer'])
                        : '';

                    /* IMAGE UPLOAD */
                    $testQuestionImage = null;
                    if (
                        isset($_FILES['fileUpload']['name'][$qIndex]) &&
                        $_FILES['fileUpload']['error'][$qIndex] === UPLOAD_ERR_OK
                    ) {

                        $file = $_FILES['fileUpload'];
                        $tmp = $file['tmp_name'][$qIndex];
                        $name = $file['name'][$qIndex];

                        $uploadDir = __DIR__ . "/../shared/assets/prof-uploads/";
                        if (!is_dir($uploadDir))
                            mkdir($uploadDir, 0777, true);

                        $newName = time() . "_" . $name;
                        $dest = $uploadDir . $newName;

                        if (move_uploaded_file($tmp, $dest)) {
                            $testQuestionImage = $newName;
                        }
                    }

                    /* INSERT QUESTION*/
                    $insertQ = "INSERT INTO testQuestions
                    (testID, testQuestion, questionType, testQuestionPoints, correctAnswer, testQuestionImage)
                    VALUES
                    ('$testID', '$testQuestion', '$questionType', '$testPoints', '$correctAnswer', " .
                        ($testQuestionImage ? "'" . mysqli_real_escape_string($conn, $testQuestionImage) . "'" : "NULL") . ")";
                    executeQuery($insertQ);

                    $insertedQuestionID = mysqli_insert_id($conn);

                    // Insert choices if multiple choice
                    if ($questionType === "Multiple Choice" && !empty($question['choices'])) {
                        foreach ($question['choices'] as $choice) {
                            $choiceText = mysqli_real_escape_string($conn, $choice);
                            $insertChoice = "INSERT INTO testQuestionChoices (testQuestionID, choiceText) VALUES ('$insertedQuestionID', '$choiceText')";
                            executeQuery($insertChoice);
                        }
                    }
                }
            }
        }
    }
    // After loop, redirect or set success message
    header("Location: create-test.php?success=1");
    exit;
}


// Only keep mainData population
$mainData = [
    'assessmentTitle' => '',
    'generalGuidance' => '',
    'testTimeLimit' => null,
    'deadline' => null,
    'deadlineEnabled' => 0,
    'testType' => 'Test',
    'questions' => []
];

// If editing or reusing, populate mainData from database
if (isset($_GET['edit']) || isset($_GET['reuse'])) {
    $reuseID = intval($_GET['edit'] ?? $_GET['reuse']);

    // Get courseID of the test for saving
$courseIDQuery = "SELECT courseID FROM assessments WHERE assessmentID = '$reuseID' LIMIT 1";
$courseIDRes = executeQuery($courseIDQuery);
$courseIDRow = mysqli_fetch_assoc($courseIDRes);
$courseIDFromDB = $courseIDRow ? $courseIDRow['courseID'] : 0;


    // Fetch main test info
    $testInfoQuery = "SELECT a.assessmentTitle, t.generalGuidance, t.testTimeLimit,
                      a.deadline, a.deadlineEnabled, a.type AS testType, t.testID
                      FROM assessments a
                      JOIN tests t ON a.assessmentID = t.assessmentID
                      WHERE a.assessmentID = '$reuseID' AND a.type='Test'";
    $infoResult = executeQuery($testInfoQuery);
    if ($infoResult && $row = $infoResult->fetch_assoc()) {
        $mainData['assessmentTitle'] = $row['assessmentTitle'];
        $mainData['generalGuidance'] = $row['generalGuidance'];
        $mainData['testTimeLimit'] = $row['testTimeLimit'];
        $mainData['deadline'] = $row['deadline'];
        $mainData['deadlineEnabled'] = $row['deadlineEnabled'];
        $mainData['testType'] = $row['testType'];
        $testID = $row['testID'];
    }

    // Fetch questions and choices
    $questionsQuery = "SELECT * FROM testquestions WHERE testID = '$testID' ORDER BY testQuestionID ASC";
    $questionsResult = executeQuery($questionsQuery);

    if ($questionsResult && $questionsResult->num_rows > 0) {
        while ($row = $questionsResult->fetch_assoc()) {
            $qID = $row['testQuestionID'];
            $row['choices'] = [];

            if ($row['questionType'] === 'Multiple Choice') {
                $choicesQuery = "SELECT * FROM testquestionchoices WHERE testQuestionID = '$qID'";
                $choicesResult = executeQuery($choicesQuery);
                if ($choicesResult) {
                    while ($choice = $choicesResult->fetch_assoc()) {
                        $row['choices'][] = $choice;
                    }
                }
            }

            $mainData['questions'][] = $row;
        }
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        <?php
        echo isset($_GET['reuse']) ? 'Recreate Test' : (isset($_GET['edit']) ? 'Edit Test' : 'Create Test');
        ?> ✦ Webstar
    </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        crossorigin="anonymous">
    <link rel="stylesheet" href="../shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="../shared/assets/css/create-exam.css">
    <link rel="stylesheet" href="../shared/assets/css/add-lesson.css">
    <link rel="stylesheet" href="../shared/assets/css/sidebar-and-container-styles.css">
    <link rel="stylesheet" href="../shared/assets/css/post-announcement.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../shared/assets/img/webstar-icon.png">
    <!-- Quill CSS -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid min-vh-100 d-flex justify-content-center align-items-center p-0 p-md-3"
        style="background-color: var(--black);">
        <div class="row w-100">
            <!-- Sidebar includes -->
            <?php include '../shared/components/prof-sidebar-for-mobile.php'; ?>
            <?php include '../shared/components/prof-sidebar-for-desktop.php'; ?>

            <!-- Main Container -->
            <div class="col main-container m-0 p-0 mx-0 mx-md-2 p-md-4 overflow-y-auto position-relative">
                <div class="card border-0 px-3 pt-3 m-0 h-100 w-100 rounded-0 shadow-none"
                    style="background-color: transparent;">
                    <?php include '../shared/components/prof-navbar-for-mobile.php'; ?>

                    <div class="container-fluid py-3 overflow-y-auto row-padding-top">
                        <div class="create-prof-row">
                            <div class="col-12">
                                <!-- Header -->
                                <div class="row mb-3 align-items-center">
                                    <div class="col-auto d-none d-md-block">
                                        <a href="javascript:history.back()" class="text-decoration-none">
                                            <i class="fa-solid fa-arrow-left text-reg text-16"
                                                style="color: var(--black);"></i>
                                        </a>
                                    </div>

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

                                    <div class="col-12 col-md-auto text-center d-flex d-md-block justify-content-center justify-content-md-end mt-3 mt-md-0">
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
                                <form action="" id="guidedanceForm" method="POST" enctype="multipart/form-data">

                                    <!-- Hidden input for test type -->
                                    <input type="hidden" name="mode"
                                        value="<?= isset($_GET['edit']) ? 'edit' : (isset($_GET['reuse']) ? 'reuse' : 'new') ?>">
                                    <input type="hidden" name="testID"
                                        value="<?= $_GET['edit'] ?? $_GET['reuse'] ?? '' ?>">
                                        <input type="hidden" name="courses[]" value="<?php echo $courseIDFromDB ?? ''; ?>">
                                        

                                    <input type="hidden" name="testType" value="test">

                                    <div class="row">
                                        <div class="col-12 pt-3">
                                            <label for="lessonInfo" class="form-label text-med text-16">Test Information</label>
                                            <input type="text"
                                                class="form-control textbox mb-2 px-3 py-2 text-reg text-16"
                                                id="lessonInfo" name="taskTitle" aria-describedby="lessonInfo"
                                                placeholder="Test Title"
                                                value="<?php echo isset($mainData) ? htmlspecialchars($mainData['assessmentTitle']) : ''; ?>"
                                                required>
                                        </div>
                                    </div>

                                    <div class="row g-3 align-items-center">
                                        <div class="col-md-4">
                                            <label class="form-label text-med text-14">Time Limit (minutes)</label>
                                            <input type="number" name="testTimeLimit" class="form-control textbox text-reg text-16"
                                                   placeholder="No time limit if left blank" min="1" value="<?php
                                                   if (isset($mainData['testTimeLimit'])) {
                                                       echo htmlspecialchars(intval($mainData['testTimeLimit']) / 60);
                                                   }
                                                   ?>" />
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label text-med text-14">Deadline</label>
                                            <input type="datetime-local" name="deadline" class="form-control textbox text-reg text-16"
                                                   value="<?php echo isset($mainData['deadline']) && $mainData['deadline'] ? date('Y-m-d\TH:i', strtotime($mainData['deadline'])) : ''; ?>">
                                        </div>
                                        <div class="form-check mt-2 col d-flex align-items-center">
                                            <input class="form-check-input" type="checkbox" id="stopSubmissions"
                                                name="stopSubmissions" style="border: 1px solid var(--black);" <?php if (!empty($mainData['deadlineEnabled']) && $mainData['deadlineEnabled'] == 1) echo 'checked'; ?> />
                                            <label class="form-check-label text-reg text-14 ms-2" style="margin-top: 4.5px;" for="stopSubmissions">
                                                Stop accepting submissions after the deadline.
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Learning Materials -->
                                    <div class="row align-items-center mt-4">
                                        <div class="col-6">
                                            <div class="learning-materials">
                                                <label class="text-med text-16 mt-5 mb-3">Exam Items</label>
                                            </div>
                                        </div>
                                        <div class="col-6 text-end">
                                            <label for="TotalPoints" class="form-label text-med text-16 mt-5 mb-3">
                                                Total Points: <span id="totalPoints">0</span>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Templates -->
                                    <!-- Identification Template -->
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

                                                        <!-- Answer Column -->
                                                        <div class="col mb-2">
                                                            <div class="text-reg mb-1">Correct Answer</div>
                                                            <div
                                                                class="answers-container d-flex align-items-center flex-nowrap">
                                                                <div
                                                                    class="answer-wrapper me-2 d-inline-flex align-items-center">
                                                                    <input type="text" placeholder="Answer"
                                                                        class="border rounded p-2 text-reg"
                                                                        style="width: 220px;"
                                                                        name="questions[0][correctAnswer]">
                                                                </div>
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

                                    <div class="mt-4">
                                        <!-- Quill Editor -->
                                        <label class="form-label text-med text-16">General Guidance</label>
                                        <div id="toolbar"></div>
                                        <div id="editor" style="height: 200px;"><?php echo isset($mainData['generalGuidance']) ? $mainData['generalGuidance'] : ''; ?></div>
                                        <input type="hidden" id="generalGuidance" name="generalGuidance" value="<?php echo isset($mainData['generalGuidance']) ? htmlspecialchars($mainData['generalGuidance']) : ''; ?>">
                                        <div id="word-counter" class="text-muted mt-2">0/200</div>
                                    </div>

                                    <div class="mt-4 d-flex justify-content-end gap-2">
                                        <button type="submit" name="save_exam" class="btn btn-primary">Save Test</button>
                                        <a href="create-test.php" class="btn btn-outline-secondary">Cancel</a>
                                    </div>

                                </form>

                                <!-- Existing tests list modal / reuse modal preserved as-is below if you had it -->
                                <!-- ... (kept unchanged) ... -->

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <!-- Quill JS -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

    <!-- Export serverData for client-side population -->
    <script>
        const serverData = <?php echo json_encode($mainData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
    </script>

    <script>
        // Quill Editor
        var quill = new Quill('#editor', {
            theme: 'snow',
            placeholder: 'Test General Guidelines',
            modules: {
                toolbar: '#toolbar'
            }
        });

        // Populate quill with server html if editing/reusing
        if (serverData && serverData.generalGuidance) {
            try {
                quill.root.innerHTML = serverData.generalGuidance;
            } catch (e) {
                // ignore if malformed
            }
        }

        const maxWords = 200;
        const counter = document.getElementById("word-counter");

        quill.on('text-change', function () {
            let text = quill.getText().trim();
            let words = text.length > 0 ? text.split(/\s+/).length : 0;

            if (words > maxWords) {
                let limited = text.split(/\s+/).slice(0, maxWords).join(" ");
                quill.setText(limited + " ");
                quill.setSelection(quill.getLength());
                quill.setSelection(quill.getLength());
            }

            counter.textContent = `${Math.min(words, maxWords)}/${maxWords}`;
        });

        const form = document.querySelector('#guidedanceForm');

        form.addEventListener("submit", function (e) {
            // --- Quill ---
            let plainText = quill.root.innerHTML.trim();
            const guidelinesInput = document.getElementById("generalGuidance");
            guidelinesInput.value = plainText;

            if (plainText.length === 0) {
                e.preventDefault();
                quill.root.focus();
                guidelinesInput.setCustomValidity('Please fill out this field.');
                guidelinesInput.reportValidity();
                return false;
            } else {
                guidelinesInput.setCustomValidity('');
            }

            // Basic multiple choice validation: ensure at least one radio selected
            let valid = true;
            document.querySelectorAll(".multiple-choice-item").forEach(mc => {
                if (!mc.offsetParent) return;
                const radios = mc.querySelectorAll("input[type='radio']");
                let oneChecked = Array.from(radios).some(r => r.checked);
                if (!oneChecked) {
                    valid = false;
                }
            });

            if (!valid) {
                e.preventDefault();
                alert('Please select the correct answer for each multiple choice question.');
                return false;
            }
        });

        // Question templates + adding/removing logic
        const mainContainer = document.getElementById("allQuestionsContainer");
        let questionCount = (serverData && Array.isArray(serverData.questions)) ? serverData.questions.length : 0;

        function renumberQuestions() {
            // update visual numbers if you have those elements (optional)
            // update total points
            updateTotalPoints();
        }

        function updateTotalPoints() {
            let total = 0;
            document.querySelectorAll("input[name$='[testQuestionPoints]']").forEach(inp => {
                const val = parseFloat(inp.value) || 0;
                total += val;
            });
            document.getElementById("totalPoints").textContent = total;
        }

        document.getElementById("addIdentification").addEventListener("click", () => {
            const clone = document.getElementById("identificationTemplate").content.cloneNode(true);
            // update names indices
            clone.querySelectorAll("input, textarea, select").forEach(input => {
                if (input.name) input.name = input.name.replace(/questions\[\d+\]/, `questions[${questionCount}]`);
            });
            // update hidden questionType
            const hiddenType = clone.querySelector("input[type='hidden'][name*='questionType']");
            if (hiddenType) {
                hiddenType.value = "Identification";
                hiddenType.name = `questions[${questionCount}][questionType]`;
            }
            // update file input
            const fileInput = clone.querySelector(".image-upload");
            if (fileInput) fileInput.name = `fileUpload[${questionCount}]`;

            // delete button binding
            const del = clone.querySelector(".delete-template");
            if (del) del.addEventListener("click", (e) => {
                e.target.closest(".textbox").remove();
                updateTotalPoints();
            });

            mainContainer.appendChild(clone);
            questionCount++;
            renumberQuestions();
        });

        document.getElementById("addMultipleChoice").addEventListener("click", () => {
            const clone = document.getElementById("multipleChoiceTemplate").content.cloneNode(true);
            clone.querySelectorAll("input, textarea, select").forEach(input => {
                if (input.name) input.name = input.name.replace(/questions\[\d+\]/, `questions[${questionCount}]`);
            });
            const hiddenType = clone.querySelector("input[type='hidden'][name*='questionType']");
            if (hiddenType) {
                hiddenType.value = "Multiple Choice";
                hiddenType.name = `questions[${questionCount}][questionType]`;
            }
            // update file input
            const fileInput = clone.querySelector(".image-upload");
            if (fileInput) fileInput.name = `fileUpload[${questionCount}]`;

            // handle add choice button inside template
            clone.querySelectorAll(".add-choice-btn").forEach(btn => {
                btn.addEventListener("click", (e) => {
                    const container = e.target.closest(".radio-choices-container");
                    if (!container) return;
                    const div = document.createElement("div");
                    div.className = "form-check text-start d-flex align-items-center mb-2 position-relative";
                    const radio = document.createElement("input");
                    radio.type = "radio";
                    radio.className = "form-check-input me-2";
                    radio.name = `questions[${questionCount}][correctAnswer]`;
                    radio.value = "";

                    const input = document.createElement("input");
                    input.type = "text";
                    input.className = "choice-input text-reg me-4 text-truncate";
                    input.name = `questions[${questionCount}][choices][]`;
                    input.placeholder = "Choice";
                    input.style.border = "none";
                    input.style.outline = "none";
                    input.style.width = "100%";
                    input.style.maxWidth = "200px";

                    input.addEventListener("input", () => {
                        radio.value = input.value;
                    });

                    const deleteBtn = document.createElement("button");
                    deleteBtn.type = "button";
                    deleteBtn.className = "delete-choice-btn btn btn-sm btn-outline-danger";
                    deleteBtn.style.position = "absolute";
                    deleteBtn.style.right = "0";
                    deleteBtn.style.top = "0";
                    deleteBtn.innerHTML = '<i class="fas fa-times"></i>';
                    deleteBtn.addEventListener("click", () => div.remove());

                    div.appendChild(radio);
                    div.appendChild(input);
                    div.appendChild(deleteBtn);
                    container.appendChild(div);
                });
            });

            // delete button binding
            const del = clone.querySelector(".delete-template");
            if (del) del.addEventListener("click", (e) => {
                e.target.closest(".textbox").remove();
                updateTotalPoints();
            });

            mainContainer.appendChild(clone);
            questionCount++;
            renumberQuestions();
        });

        // Populate existing questions from serverData (for edit/reuse)
        function populateExistingQuestions() {
            if (!serverData || !Array.isArray(serverData.questions) || serverData.questions.length === 0) return;

            serverData.questions.forEach((q, idx) => {
                const typeRaw = (q.questionType || q.questiontype || "").toLowerCase();
                let clone;
                if (typeRaw.indexOf("multiple") !== -1) {
                    clone = document.getElementById("multipleChoiceTemplate").content.cloneNode(true);
                } else {
                    clone = document.getElementById("identificationTemplate").content.cloneNode(true);
                }

                // Replace name indices in cloned inputs
                clone.querySelectorAll("input, textarea, select").forEach(input => {
                    if (input.name) {
                        input.name = input.name.replace(/questions\[\d+\]/, `questions[${questionCount}]`);
                    }
                });

                // Set hidden question type
                const hiddenType = clone.querySelector("input[type='hidden'][name*='questionType']");
                if (hiddenType) {
                    hiddenType.name = `questions[${questionCount}][questionType]`;
                    hiddenType.value = q.questionType || q.questiontype || 'Identification';
                }

                // Update file input name
                const fileInput = clone.querySelector(".image-upload");
                if (fileInput) fileInput.name = `fileUpload[${questionCount}]`;

                // Fill main question text
                const questionInput = clone.querySelector("input[name*='testQuestion']");
                if (questionInput && (q.testQuestion || q.testquestion)) {
                    questionInput.value = q.testQuestion || q.testquestion;
                }

                // Fill points
                const pointsInput = clone.querySelector("input[name*='testQuestionPoints']");
                if (pointsInput && (q.testQuestionPoints !== undefined)) {
                    pointsInput.value = q.testQuestionPoints;
                }

                // Handle image preview if testQuestionImage exists
                if (q.testQuestionImage) {
                    const img = clone.querySelector(".question-image");
                    const container = clone.querySelector(".image-container");
                    if (img && container) {
                        img.src = "../shared/assets/prof-uploads/" + q.testQuestionImage;
                        container.style.display = "block";
                    }
                }

                // Correct answer(s) for identification
                if (q.correctAnswer !== undefined && q.correctAnswer !== null) {
                    const identAnswer = clone.querySelector("input[name*='correctAnswer'][type='text']");
                    if (identAnswer && typeof q.correctAnswer === 'string') {
                        identAnswer.value = q.correctAnswer;
                    }
                }

                // Multiple choice choices
                if ((q.questionType === "Multiple Choice" || (q.questionType && q.questionType.toLowerCase().includes('multiple'))) && Array.isArray(q.choices)) {
                    const container = clone.querySelector(".radio-choices-container");
                    if (container) {
                        // Clear placeholder if any
                        container.innerHTML = '';
                        q.choices.forEach(choiceObj => {
                            const div = document.createElement("div");
                            div.className = "form-check text-start d-flex align-items-center mb-2 position-relative";

                            const radio = document.createElement("input");
                            radio.type = "radio";
                            radio.className = "form-check-input me-2";
                            radio.name = `questions[${questionCount}][correctAnswer]`;
                            radio.value = choiceObj.choiceText || choiceObj.choicetext || '';

                            const input = document.createElement("input");
                            input.type = "text";
                            input.className = "choice-input text-reg me-4 text-truncate";
                            input.name = `questions[${questionCount}][choices][]`;
                            input.placeholder = "Choice";
                            input.style.border = "none";
                            input.style.outline = "none";
                            input.style.width = "100%";
                            input.style.maxWidth = "200px";
                            input.value = choiceObj.choiceText || choiceObj.choicetext || '';

                            if ((q.correctAnswer || "").toString() === input.value) {
                                radio.checked = true;
                            }

                            const deleteBtn = document.createElement("button");
                            deleteBtn.type = "button";
                            deleteBtn.className = "delete-template btn btn-sm btn-outline-danger";
                            deleteBtn.style.position = "absolute";
                            deleteBtn.style.top = "2px";
                            deleteBtn.style.right = "1px";
                            deleteBtn.innerHTML = '<i class="fas fa-times"></i>';
                            deleteBtn.addEventListener("click", () => div.remove());

                            input.addEventListener("input", () => {
                                radio.value = input.value;
                            });

                            div.appendChild(radio);
                            div.appendChild(input);
                            div.appendChild(deleteBtn);
                            container.appendChild(div);
                        });
                    }
                }

                // Add delete binding for the cloned question
                const del = clone.querySelector(".delete-template");
                if (del) del.addEventListener("click", (e) => {
                    e.target.closest(".textbox").remove();
                    updateTotalPoints();
                });

                mainContainer.appendChild(clone);
                questionCount++;
            });

            renumberQuestions();
            updateTotalPoints();
        }

        // call populate on load
        populateExistingQuestions();

        // Listen for changes to point inputs to recalc total
        document.addEventListener('input', (e) => {
            if (e.target && e.target.name && e.target.name.endsWith('[testQuestionPoints]')) {
                updateTotalPoints();
            }
        });

    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
