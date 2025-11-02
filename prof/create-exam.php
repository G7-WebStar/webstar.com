<?php
$activePage = 'create-exam';

include('../shared/assets/database/connect.php');
include("../shared/assets/processes/prof-session-process.php");

$course = "SELECT courseID, courseCode 
           FROM courses 
           WHERE userID = '$userID'";
$courses = executeQuery($course);

if (isset($_POST['save_lesson'])) {
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

            // 2. Insert into tests (hindi na kasama ang deadline dito)
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
                        $tmpName  = $_FILES['fileUpload']['tmp_name'][$qIndex];

                        $uploadFolder = __DIR__ . "/../shared/assets/prof-uploads/";

                        if (!is_dir($uploadFolder)) {
                            mkdir($uploadFolder, 0777, true);
                        }

                        // Unique filename
                        $newFileName = time() . "_" . basename($fileName);
                        $destination = $uploadFolder . $newFileName;

                        if (move_uploaded_file($tmpName, $destination)) {
                            $testQuestionImage = $newFileName;
                        }
                    }

                    // ✅ Insert test question
                    $testQuestionQuery = "INSERT INTO testQuestions 
                        (testID, testQuestion, questionType, testQuestionPoints, correctAnswer, testQuestionImage)
                        VALUES 
                        ('$testID', '$testQuestion', '$questionType', '$testQuestionPoints', '$correctAnswer', " .
                        ($testQuestionImage ? "'$testQuestionImage'" : "NULL") . ")";
                    executeQuery($testQuestionQuery);

                    // Kunin bagong testQuestionID
                    $testQuestionID = mysqli_insert_id($conn);

                    // ✅ If Multiple Choice → insert choices
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

                    <div class="container-fluid py-3 overflow-y-auto">
                        <div class="row row-padding-top">
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
                                        <span class="text-sbold text-25">Create Test</span>
                                    </div>
                                </div>

                                <!-- Form starts -->
                                <form action="" method="POST" enctype="multipart/form-data">

                                    <!-- Hidden input for test type -->
                                    <input type="hidden" name="testType" value="test">

                                    <div class="row">
                                        <div class="col-12 pt-3 mb-3">
                                            <label for="lessonInfo" class="form-label text-med text-16">Test
                                                Information</label>
                                            <input type="text"
                                                class="form-control textbox mb-3 p-2 text-reg text-14 text-muted"
                                                id="lessonInfo" name="taskTitle" aria-describedby="lessonInfo" placeholder="Test Title" required>
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
                                            <input type="hidden" name="generalGuidance" id="generalGuidance">
                                        </div>
                                    </div>

                                    <div class="row g-3">
                                        <div class="row g-3 mt-3">
                                            <!-- Deadline -->
                                            <div class="col-md-4">
                                                <label class="form-label text-med text-16">
                                                    Deadline
                                                </label>
                                                <!-- <span class="fst-italic text-reg text-12">Optional.</span> -->
                                                <div class="input-group" style="max-width: 320px;">
                                                    <input type="datetime-local"
                                                        name="deadline" class="form-control textbox text-reg text-14" required>
                                                </div>
                                            </div>

                                            <!-- Time limit -->
                                            <div class="col-md-4">
                                                <label class="form-label text-med text-16">
                                                    Time Limit (minutes)
                                                </label>
                                                <span class="fst-italic text-reg text-12">Optional.</span>
                                                <input type="number" name="testTimeLimit" class="form-control textbox text-reg text-14" required
                                                    style="max-width: 320px;" placeholder="100" />
                                            </div>
                                        </div>
                                        <!-- wala pa to -->
                                        <div class="form-check mt-2 col ms-2">
                                            <input class="form-check-input" type="checkbox" id="stopSubmissions" name="stopSubmissions"
                                                style="border: 1px solid var(--black);" />
                                            <label class="form-check-label" for="stopSubmissions">
                                                Stop accepting submissions after the deadline.
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Learning Materials -->
                                    <div class="row align-items-center">
                                        <div class="col-6">
                                            <div class="learning-materials">
                                                <label class="text-med text-16 mt-5">Exam Items</label>
                                            </div>
                                        </div>
                                        <div class="col-6 text-end d-block d-md-none mt-5">
                                            <!-- Show only on mobile -->
                                            <label for="TotalPoints" class="form-label text-med text-16 mt-2">
                                                Total Points: <span id="totalPoints">0</span>
                                            </label>
                                        </div>
                                    </div>
                                    <!-- Templates -->
                                    <template id="identificationTemplate">
                                        <div class="row position-relative">
                                            <div class="col-12 mb-3">
                                                <div
                                                    class="form-control textbox mb-3 p-2 text-reg text-14 text-muted position-relative">
                                                    <!-- Delete Button -->
                                                    <button type="button" class="delete-template"
                                                        style="position: absolute; top: 5px; right: 1px; background: none; border: none; color: var(--black); cursor: pointer;">
                                                        <i class="fas fa-times"></i>
                                                    </button>

                                                    <!-- Hidden input for question type -->
                                                    <input type="hidden" name="questions[0][questionType]" value="identification">

                                                    <div class="input-group text-reg text-14 text-muted mb-3 mt-2">
                                                        <span class="input-group-text text-bold rounded-left ms-3"
                                                            style="background-color: var(--primaryColor);">1</span>
                                                        <input type="text" class="question-box form-control"
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
                                                            style="position: absolute; top: 5px; right: 5px; background: none; border: none; color: var(--black); cursor: pointer; z-index: 2;">
                                                            <i class="fas fa-times"></i>
                                                        </button>

                                                        <img src="" class="question-image"
                                                            style="width: 100%; height: 100%; border-radius: 10px; border: 1px solid var(--black); object-fit: cover; background-color: var(--primaryColor); cursor: pointer;">

                                                        <input type="file" name="fileUpload[0]" accept="image/*" class="image-upload"
                                                            style="display: none;">
                                                    </div>

                                                    <div class="row position-relative ms-3 mb-2">
                                                        <!-- Points Column -->
                                                        <div class="col-auto text-center me-4 flex-shrink-0">
                                                            <div class="text-reg mb-1">Points</div>
                                                            <input type="number" name="questions[0][testQuestionPoints]" class="border rounded p-2"
                                                                placeholder="1" min="1"
                                                                style="width: 60px; text-align: center;">
                                                        </div>

                                                        <!-- Correct Answers Column -->
                                                        <div class="col text-center">
                                                            <div class="text-reg mb-1">Correct Answers</div>
                                                            <div class="d-flex align-items-center overflow-auto answers-scroll"
                                                                style="white-space: nowrap;">
                                                                <div
                                                                    class="answers-container d-flex align-items-center flex-nowrap">
                                                                    <!-- Answer inputs appended here -->
                                                                </div>
                                                                <button type="button"
                                                                    class="btn text-reg rounded-pill add-answer-btn flex-shrink-0 ms-2"
                                                                    style="background-color: var(--primaryColor);">
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
                                            <div class="col-12 mb-3">
                                                <div
                                                    class="form-control textbox mb-3 p-2 text-reg text-14 text-muted position-relative">

                                                    <!-- Delete Button -->
                                                    <button type="button" class="delete-template"
                                                        style="position: absolute; top: 5px; right: 1px; background: none; border: none; color: var(--black); cursor: pointer;">
                                                        <i class="fas fa-times"></i>
                                                    </button>

                                                    <!-- Hidden input for question type -->
                                                    <input type="hidden" name="questions[0][questionType]" value="multipleChoice">

                                                    <!-- Question -->
                                                    <div class="input-group text-reg text-14 text-muted mb-3 mt-2">
                                                        <span
                                                            class="input-group-text text-bold rounded-left ms-3 question-number"
                                                            style="background-color: var(--primaryColor);">1</span>
                                                        <input type="text" class="question-box form-control"
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
                                                        <button type="button" class="delete-image"
                                                            style="position: absolute; top: 5px; right: 5px; background: none; border: none; color: var(--black); cursor: pointer; z-index: 2;">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                        <img src="" class="question-image"
                                                            style="width: 100%; height: 100%; border-radius: 10px; border: 1px solid var(--black); object-fit: cover; background-color: var(--primaryColor); cursor: pointer;">
                                                        <input type="file" name="fileUpload[0]" accept="image/*" class="image-upload"
                                                            style="display: none;">
                                                    </div>

                                                    <!-- Choices -->
                                                    <div class="d-flex align-items-center ms-3 mb-2">
                                                        <div class="text-center me-4">
                                                            <div class="text-reg mb-1">Choices</div>
                                                            <div class="radio-choices-container"
                                                                style="max-height: 200px; overflow-y: auto; padding-right: 5px;">
                                                                <!-- Choices will be added here dynamically -->
                                                            </div>
                                                            <!-- Add button -->
                                                            <button type="button"
                                                                class="btn text-reg rounded-pill add-radio-btn mt-2"
                                                                style="background-color: var(--primaryColor);">+
                                                                Add</button>
                                                        </div>
                                                    </div>

                                                    <!-- Points -->
                                                    <div class="d-flex align-items-center ms-3 mb-2">
                                                        <div class="text-center me-4">
                                                            <div class="text-reg mb-1">Points</div>
                                                            <input type="number" name="questions[0][testQuestionPoints]" class="border rounded p-2"
                                                                placeholder="1" min="1"
                                                                style="width: 60px; text-align: center;">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>


                                    <!-- Master Container -->
                                    <div id="allQuestionsContainer"></div>

                                    <!-- Buttons -->
                                    <div class="row mt-2">
                                        <div class="col-12 mb-3">
                                            <button type="button" id="addMultipleChoice"
                                                class="btn text-reg rounded-pill ms-1"
                                                style="background-color: var(--primaryColor); width: 180px;">
                                                <i class="far fa-dot-circle me-2"></i> Multiple Choice
                                            </button>

                                            <button type="button" id="addIdentification"
                                                class="btn text-reg rounded-pill ms-1 me-1"
                                                style="background-color: var(--primaryColor); width: 180px;">
                                                <i class="fas fa-align-left me-2"></i> Identification
                                            </button>

                                            <label for="TotalPoints"
                                                class="form-label text-med text-16 mt-2 ms-3 d-none d-md-inline">
                                                Total Points: 10
                                            </label>

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
                                                    class="btn-select dropdown-toggle dropdown-shape text-med text-16 me-md-5"
                                                    type="button" data-bs-toggle="dropdown" aria-expanded="false">
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
                                        <!-- Add Button -->
                                        <div class="col-md-6 text-center text-md-center mt-3 mt-md-0">
                                            <button type="submit" name="save_lesson"
                                                class="px-4 py-2 rounded-pill text-sbold text-md-14 mt-3"
                                                style="background-color: var(--primaryColor); border: 1px solid var(--black);">
                                                Add
                                            </button>
                                        </div>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Alert Container (Centered Top in main container) -->
                <div id="toastContainer"
                    class="position-absolute top-0 start-50 translate-middle-x p-3 d-flex flex-column align-items-center"
                    style="z-index:1100; pointer-events:none;">
                </div>

            </div>
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

        const maxWords = 120;
        const counter = document.getElementById("word-counter");

        quill.on('text-change', function() {
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

        form.addEventListener("submit", function(e) {
            // --- Quill ---
            let plainText = quill.getText().trim();
            document.getElementById("generalGuidance").value = plainText;

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
            alert.className = "alert alert-danger alert-dismissible fade show mb-2 text-center d-flex align-items-center justify-content-center shadow-lg text-reg text-16";
            alert.role = "alert";
            alert.innerHTML = `
            <i class="bi bi-exclamation-triangle-fill me-2 fs-6"></i>
            <span>${message}</span>
            <button type="button" class="btn-close ms-2" data-bs-dismiss="alert" aria-label="Close"></button>
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
        document.addEventListener("click", function(e) {
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
                input.classList.add("border", "rounded", "p-2");
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
        document.addEventListener("click", function(e) {
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
        document.getElementById("allQuestionsContainer").addEventListener("click", function(e) {
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
                input.classList.add("choice-input");
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
                deleteBtn.style.top = "5px";
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
        document.addEventListener("click", function(e) {
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
                if (fileInput.files && fileInput.files[0]) {
                    const reader = new FileReader();
                    reader.onload = e => img.src = e.target.result;
                    reader.readAsDataURL(fileInput.files[0]);
                }
            });
        }

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

        mainContainer.addEventListener('input', function(e) {
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