<?php
$activePage = 'profIndex';
include('../shared/assets/database/connect.php');
include("../shared/assets/processes/prof-session-process.php");

$testID = $_GET['testID'];
if ($testID == null) {
    header("Location: 404.html");
    exit();
}

$selectTestQuery = "SELECT assessmentTitle, generalGuidance FROM tests 
                    INNER JOIN assessments
                        ON tests.assessmentID = assessments.assessmentID
                    INNER JOIN courses
                        ON assessments.courseID = courses.courseID
                    WHERE testID = $testID AND courses.userID = $userID";
$selectTestResult = executeQuery($selectTestQuery);

if (mysqli_num_rows($selectTestResult) <= 0) {
    echo "Test does not exists.";
    exit();
}

$selectQuestionsQuery = "SELECT 
testquestions.*
FROM tests 
INNER JOIN testquestions 
    ON tests.testID = testquestions.testID 
WHERE tests.testID = $testID";
$selectQuestionsResult = executeQuery($selectQuestionsQuery);
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webstar | Index</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="../shared/assets/css/sidebar-and-container-styles.css">
    <link rel="stylesheet" href="../shared/assets/css/index.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../shared/assets/img/webstar-icon.png">

    <!-- Material Design Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp" />
    <style>
        .choice-selected {
            transition: 0.3s ease-in-out;
            background-color: var(--primaryColor) !important;
        }

        .interactable {
            cursor: pointer;
        }

        .back-btn {
            cursor: pointer;
        }

        .interactable:hover {
            transition: 0.3s ease-in-out;
            background-color: var(--highlight75) !important;
        }

        .bg-correct {
            background-color: var(--highlight15) !important;
        }

        @media screen and (max-width: 767px) {
            .fs-sm-6 {
                font-size: 1rem !important;
            }
        }

        .btn-mobile {
            margin-bottom: calc(1.5rem + 80px) !important;
        }
    </style>
</head>

<body oncopy="return false" onpaste="return false" oncut="return false" oncontextmenu="return false" onselectstart="return false">
    <div class="container-fluid min-vh-100 d-flex justify-content-center align-items-center p-0 p-md-3"
        style="background-color: var(--black);">

        <div class="row w-100 m-0">

            <!-- Sidebar (only shows on mobile) -->
            <?php include '../shared/components/prof-sidebar-for-mobile.php'; ?>

            <!-- Sidebar Column (fixed on desktop) -->
            <?php include '../shared/components/prof-sidebar-for-desktop.php'; ?>

            <!-- Main Container Column -->
            <div class="col-12 col-md main-container m-0 p-0 mx-0 mx-md-2 p-md-4 overflow-auto">
                <div class="card border-0 px-3 pt-3 m-0 h-100 w-100 rounded-0 shadow-none"
                    style="background-color: transparent;">

                    <!-- Navbar for mobile -->
                    <?php include '../shared/components/prof-navbar-for-mobile.php'; ?>

                    <div class="container-fluid py-1 overflow-y-auto d-flex flex-column h-100 row-padding-top">
                        <div class="row flex-grow-1">
                            <div class="col-12 d-flex flex-column h-100">

                                <!-- Quiz Nav -->
                                <div class="row bg-white border border-black rounded-4 my-3 text-sbold mx-0 mx-md-1">
                                    <i class="d-block d-md-none announcement-arrow fa-lg fa-solid fa-arrow-left text-reg text-12 mt-3 me-3"
                                        style="color: var(--black);"></i>
                                    <div class="quiz-nav col-12 d-flex flex-column flex-md-row align-items-center justify-content-between my-2 px-3 px-md-5 py-2 py-md-3">
                                        <div class="d-flex flex-row align-items-center mb-0">
                                            <a onclick="history.back()" class="text-decoration-none back-btn"><i class="d-none d-md-block announcement-arrow fa-lg fa-solid fa-arrow-left text-reg text-12 me-3"
                                                    style="color: var(--black);"></i></a>
                                            <?php
                                            if (mysqli_num_rows($selectTestResult) > 0) {
                                                while ($guideLines = mysqli_fetch_assoc($selectTestResult)) {
                                            ?>
                                                    <div class="text-center text-md-auto h2 m-0">
                                                        <?php echo $guideLines['assessmentTitle']; ?>
                                                    </div>
                                            <?php
                                                }
                                            } ?>
                                        </div>
                                        <div class="h2 mt-3 mt-md-0 mb-0 text-center text-md-end" id="timer">

                                        </div>
                                    </div>
                                </div>
                                <!-- End of Quiz Nav -->

                                <!-- Content -->
                                <div class="row flex-grow-1">
                                    <div class="col-12 d-flex flex-column flex-grow-1">
                                        <div class="question-container">
                                            <div class="h2 text-reg text-center mt-4 fs-sm-6" id="question-number">
                                                <div class="text-sbold">

                                                </div>
                                            </div>
                                            <div class="h2 text-sbold text-center mt-4 fs-sm-6" id="question-container">

                                            </div>
                                        </div>

                                        <div class="row my-0 mx-auto justify-content-center align-content-center" id="question-image">

                                        </div>
                                        <div class="col-12 col-md-8 h5 text-reg mx-auto my-0 px-3 px-md-0 text-center text-md-start fs-sm-6 d-flex justify-content-center flex-column" id="choices">
                                        </div>

                                        <div class="mt-auto text-sbold">
                                            <div class="d-flex justify-content-center justify-content-md-around align-items-center btn-mobile mb-4 gap-3 gap-md-0 mt-5" id="buttonSection">
                                                <button class="btn d-flex align-items-center justify-content-center gap-2 border border-black rounded-5 px-sm-4 py-sm-2 interactable" id="prevBtn"
                                                    style="background-color: var(--primaryColor);" onclick="prevQuestion();">
                                                    <i class="fa-solid fs-6 fa-arrow-left text-reg" style="color: var(--black);"></i>
                                                    <span class="m-0 fs-sm-6">Prev</span>
                                                </button>

                                                <button class="btn d-flex align-items-center justify-content-center gap-2 border border-black rounded-5 px-sm-4 py-sm-2 interactable" id="nextBtn"
                                                    style="background-color: var(--primaryColor);" onclick="nextQuestion();">
                                                    <span class="m-0 fs-sm-6">Next</span>
                                                    <i class="fa-solid fs-6 fa-arrow-right text-reg" style="color: var(--black);"></i>
                                                </button>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <!-- End Content -->

                            </div>
                        </div>
                    </div>
                </div> <!-- End Card -->
            </div>
        </div>
    </div>
    <!-- Modal for each image -->
    <div class="modal fade" id="imageModal0" tabindex="-1" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content bg-black border-0 d-flex justify-content-center align-items-center position-relative" style="width: 100vw; height: 100vh; border-radius: 0;">

                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-4" data-bs-dismiss="modal" aria-label="Close"></button>

                <div id="modal-img-container" class="modal-img-wrapper p-0 m-0 w-100 h-100 d-flex justify-content-center align-items-center">
                    <img id="modal-img" class="modal-zoomable" src="" alt="Full - image" style="max-width: 100%; max-height: 100%; object-fit: contain; cursor: zoom-in; transition: transform 0.12s; transform-origin: 50% 50%; transform: translate(0px, 0px) scale(1);">
                </div>
            </div>

        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        //Fetch Questions and Choices from DB
        const questions = [
            <?php
            if (mysqli_num_rows($selectQuestionsResult) > 0) {
                $totalQuestion = mysqli_num_rows($selectQuestionsResult);
                $countQuestion = 0;
                while ($questions = mysqli_fetch_assoc($selectQuestionsResult)) {
                    $testQuestionID = $questions['testQuestionID'];
                    $selectChoicesQuery = "SELECT 
                                       testquestions.testQuestionID, 
                                       testquestionchoices.* 
                                       FROM tests 
                                       INNER JOIN testquestions 
                                           ON tests.testID = testquestions.testID 
                                       INNER JOIN testquestionchoices 
                                           ON testquestions.testQuestionID = testquestionchoices.testQuestionID 
                                       WHERE tests.testID = $testID AND testquestions.testQuestionID = $testQuestionID;
                                       ";
                    $selectChoicesResult = executeQuery($selectChoicesQuery);
                    $countQuestion++;
            ?> {
                        question: "<?php echo $questions['testQuestion']; ?>",
                        questionID: "<?php echo $questions['testQuestionID']; ?>",
                        img: "<?php echo $questions['testQuestionImage']; ?>",
                        type: "<?php echo $questions['questionType']; ?>",
                        correctAnswer: "<?php echo $questions['correctAnswer'] ?>",
                        answers: [
                            <?php
                            if (mysqli_num_rows($selectChoicesResult) > 0) {
                                $totalChoice = mysqli_num_rows($selectChoicesResult);
                                $countChoice = 0;
                                while ($choices = mysqli_fetch_assoc($selectChoicesResult)) {
                            ?> {
                                        text: "<?php echo $choices['choiceText']; ?>"
                                    }
                                    <?php echo ($countChoice < $totalChoice) ? "," : null; ?>
                            <?php
                                }
                            }
                            ?>
                        ]
                    }
                    <?php echo ($countQuestion < $totalQuestion) ? "," : null; ?>
            <?php
                }
            }
            ?>
        ];

        //Get IDs of elements
        const questionContainer = document.getElementById('question-container');
        const choices = document.getElementById('choices');
        const imgContainer = document.getElementById('img-container');
        const modalImgContainer = document.getElementById('modal-img-container');
        const questionNumber = document.getElementById('question-number');

        let currentQuestionIndex = 0;
        let score = 0;

        //Arrays used for handling answers
        let choiceIDs = [];

        //Prevents strings turning into HTML special entities
        function encodeHTML(str) {
            let txt = document.createElement("textarea");
            txt.textContent = str;
            return txt.innerHTML;
        }

        //Shows questions
        function showQuestion() {
            let currentQuestion = questions[currentQuestionIndex];
            let questionNo = currentQuestionIndex + 1;
            let totalQuestion = questions.length;
            let choiceNo = 1;

            choiceIDs = [];
            questionContainer.innerHTML = currentQuestion.
            question;
            //Indicates which question is currently on screen
            questionNumber.innerHTML = "Section Name · Question " + questionNo + " of " + totalQuestion;

            //Disables pagination buttons if there are no content on the next page
            if (currentQuestionIndex == 0) {
                document.getElementById('prevBtn').classList.remove('interactable');
                document.getElementById('prevBtn').disabled = true;
            }

            if (currentQuestionIndex == questions.length - 1) {
                document.getElementById('nextBtn').classList.remove('interactable');
                document.getElementById('nextBtn').disabled = true;
            }

            //Adds a margin when there is an img
            if (questions[currentQuestionIndex].img == "") {
                choices.classList.add('mt-4');
            }

            //Displays the choices for the current question
            if (questions[currentQuestionIndex].type == "Multiple Choice") {
                currentQuestion.answers.forEach(answer => {
                    const button = document.createElement('div');
                    button.textContent = answer.text;
                    button.classList.add('col-12', 'bg-white', 'border', 'border-black', 'rounded-3', 'my-2', 'p-2', 'text-sbold', 'text-center');
                    if (currentQuestion.correctAnswer == button.textContent) {
                        button.classList.add('bg-correct');
                        button.classList.remove('bg-white');
                    }

                    button.id = "question" + questionNo + "-choice" + choiceNo;
                    choiceIDs.push(button.id);
                    choiceNo++;
                    choices.appendChild(button);
                });

            } else {
                //Displays an input text field if the question is of identification type
                let textValue = document.getElementById('input' + currentQuestionIndex)
                correctIdentification = document.createElement('div');
                correctIdentification.id = "identificationCorrection" + questionNo;
                correctIdentification.classList.add('mt-3', 'text-sbold', 'text-center');
                correctIdentification.innerHTML = "Correct Answer: " + encodeHTML(questions[currentQuestionIndex].correctAnswer);
                choices.appendChild(correctIdentification);
            }

            //Displays the image if it exists
            if (questions[currentQuestionIndex].img != '') {
                document.getElementById('question-image').innerHTML =
                    `<div class="row g-4 justify-content-center">
                    <div class="col-12">
                        <div id="img-container" class="pdf-preview-box text-center" data-bs-toggle="modal" data-bs-target="#imageModal0" style="cursor: zoom-in; overflow: hidden; border-radius: 10px; width: auto; height: 180px; position: relative; border: 1px solid var(--black);">
                            <img id="img-question" src="" alt="image" style="width: 100%; height: 100%; object-fit: cover; object-position: center center; transition: transform 0.3s; transform: scale(1);">
                        </div>
                    </div>
                </div>`;

                imgDiv = document.getElementById('question-image');
                if (imgDiv && imgDiv.innerHTML != '') {
                    const img = document.getElementById('img-question');
                    const modalImg = document.getElementById('modal-img');

                    if (modalImg && modalImgContainer) {
                        modalImg.src = "../shared/assets/prof-uploads/" + questions[currentQuestionIndex].img;
                    }


                    img.src = "../shared/assets/prof-uploads/" + questions[currentQuestionIndex].img;

                }
            } else {
                document.getElementById('question-image').innerHTML = ``;
            }


        }

        function nextQuestion() {
            if (currentQuestionIndex == 0) {
                document.getElementById('prevBtn').classList.add('interactable');
                document.getElementById('prevBtn').disabled = false;
            }

            (currentQuestionIndex + 1 < questions.length) ? currentQuestionIndex++ : null;
            if ((currentQuestionIndex + 1) <= questions.length) {
                choices.innerHTML = '';
                showQuestion();
            }
        }

        function prevQuestion() {
            if (currentQuestionIndex == questions.length - 1) {
                document.getElementById('nextBtn').classList.add('interactable');
                document.getElementById('nextBtn').disabled = false;
            }

            if ((currentQuestionIndex + 1) > 1) {
                currentQuestionIndex--;
                choices.innerHTML = '';
                showQuestion();
            }
        }

        showQuestion();
        console.log(questions);
    </script>
</body>

</html>