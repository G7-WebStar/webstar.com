<?php
include('shared/assets/database/connect.php');

include("shared/assets/processes/session-process.php");

$selectTestQuery = "SELECT * FROM tests WHERE testID = 1";
$selectTestResult = executeQuery($selectTestQuery);

$selectQuestionsQuery = "SELECT 
testquestions.*
FROM tests 
INNER JOIN testquestions 
    ON tests.testID = testquestions.testID 
WHERE tests.testID = 1";
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
    <link rel="stylesheet" href="shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="shared/assets/css/sidebar-and-container-styles.css">
    <link rel="stylesheet" href="shared/assets/css/index.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="shared/assets/img/webstar-icon.png">
    <style>
        .choice-selected {
            transition: 0.3s ease-in-out;
            background-color: var(--primaryColor) !important;
        }

        .interactable {
            cursor: pointer;
        }

        .interactable:hover {
            transition: 0.3s ease-in-out;
            background-color: var(--highlight75) !important;
        }

        @media screen and (max-width: 767px) {
            .fs-sm-6 {
                font-size: 1rem !important;
            }
        }

        ::-webkit-scrollbar {
            width: 10px;
        }

        ::-webkit-scrollbar-track {
            background: var(--dirtyWhite);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primaryColor);
            /* Your accent color */
            border-radius: 10px;
            border: 2px solid var(--dirtyWhite);
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #ff7b82;
        }

        * {
            scrollbar-width: thin;
            scrollbar-color: var(--primaryColor) var(--dirtyWhite);
        }
    </style>
</head>

<body>
    <div class="container-fluid min-vh-100 d-flex justify-content-center align-items-center p-0 p-md-3"
        style="background-color: var(--black);">

        <div class="row w-100 m-0">

            <!-- Sidebar (only shows on mobile) -->
            <?php include 'shared/components/sidebar-for-mobile.php'; ?>

            <!-- Sidebar Column (fixed on desktop) -->
            <?php include 'shared/components/sidebar-for-desktop.php'; ?>

            <!-- Main Container Column -->
            <div class="col-12 col-md main-container m-0 p-0 mx-0 mx-md-2 p-md-4 overflow-auto">
                <div class="card border-0 px-3 pt-3 m-0 h-100 w-100 rounded-0 shadow-none"
                    style="background-color: transparent;">

                    <!-- Navbar for mobile -->
                    <?php include 'shared/components/navbar-for-mobile.php'; ?>

                    <div class="container-fluid py-1 overflow-y-auto d-flex flex-column h-100">
                        <div class="row flex-grow-1">
                            <div class="col-12 d-flex flex-column h-100">

                                <!-- Quiz Nav -->
                                <div class="row bg-white border border-black rounded-4 my-3 text-sbold mx-0 mx-md-1">
                                    <i class="d-block d-md-none announcement-arrow fa-lg fa-solid fa-arrow-left text-reg text-12 mt-3 me-3"
                                        style="color: var(--black);"></i>
                                    <div class="quiz-nav col-12 d-flex flex-column flex-md-row align-items-center justify-content-between my-2 px-3 px-md-5 py-2 py-md-3">
                                        <div class="d-flex flex-row align-items-center mb-0">
                                            <i class="d-none d-md-block announcement-arrow fa-lg fa-solid fa-arrow-left text-reg text-12 me-3"
                                                style="color: var(--black);"></i>
                                            <div class="text-center text-md-auto h2 m-0">
                                                Quiz #1
                                            </div>
                                        </div>
                                        <div class="h2 mt-3 mt-md-0 mb-0 text-center text-md-end">
                                            <i class="bi bi-clock fa-xs me-2" style="color: var(--black);"></i>
                                            10:00
                                        </div>
                                    </div>
                                </div>
                                <!-- End of Quiz Nav -->

                                <!-- Content -->
                                <div class="row flex-grow-1">
                                    <div class="col-12 d-flex flex-column flex-grow-1">
                                        <div class="question-container">
                                            <div class="h2 text-reg text-center mt-4 fs-sm-6" id="question-number">
                                                Section Name · Question 1 of 20
                                            </div>
                                            <div class="h2 text-sbold text-center mt-4 fs-sm-6" id="question-container">

                                            </div>
                                        </div>


                                        <div class="col-12 col-md-8 h4 text-reg mx-auto mt-4 px-3 px-md-0 text-center text-md-start fs-sm-6" id="choices">

                                        </div>

                                        <div class="mt-auto text-sbold">
                                            <div class="d-flex justify-content-center justify-content-md-around align-items-center mb-4 gap-3 gap-md-0 mt-5">

                                                <div class="btn d-flex align-items-center justify-content-center gap-2 border border-black rounded-5 px-sm-4 py-sm-2 interactable"
                                                    style="background-color: var(--primaryColor);" onclick="prevQuestion();">
                                                    <i class="fa-solid fs-6 fa-arrow-left text-reg" style="color: var(--black);"></i>
                                                    <span class="m-0 fs-sm-6">Prev</span>
                                                </div>

                                                <div class="btn d-flex align-items-center justify-content-center border border-black rounded-5 px-sm-4 py-sm-2 interactable"
                                                    style="background-color: var(--primaryColor);" onclick="submitQuiz();">
                                                    <span class="m-0 fs-sm-6">Submit</span>
                                                </div>

                                                <div class="btn d-flex align-items-center justify-content-center gap-2 border border-black rounded-5 px-sm-4 py-sm-2 interactable"
                                                    style="background-color: var(--primaryColor);" onclick="nextQuestion();">
                                                    <span class="m-0 fs-sm-6">Next</span>
                                                    <i class="fa-solid fs-6 fa-arrow-right text-reg" style="color: var(--black);"></i>
                                                </div>

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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script>
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
                                       WHERE tests.testID = 1 AND testquestions.testQuestionID = $testQuestionID;
                                       ";
                    $selectChoicesResult = executeQuery($selectChoicesQuery);
                    $countQuestion++;
            ?> {
                        question: "<?php echo $questions['testQuestion']; ?>",
                        answers: [
                            <?php
                            if (mysqli_num_rows($selectChoicesResult) > 0) {
                                $totalChoice = mysqli_num_rows($selectChoicesResult);
                                $countChoice = 0;
                                while ($choices = mysqli_fetch_assoc($selectChoicesResult)) {
                            ?> {
                                        text: "<?php echo $choices['choiceText']; ?>",
                                        correct: <?php echo ($questions['correctAnswer'] == $choices['choiceText']) ? "true" : "false"; ?>
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

        console.log(questions);

        let seconds = 600;
        let minutes = seconds / 60;
        let hours = minutes / 60


        let selectedAnswers = new Array(questions.length).fill(null);

        const questionContainer = document.getElementById('question-container');
        const choices = document.getElementById('choices');
        const questionNumber = document.getElementById('question-number');

        let currentQuestionIndex = 0;
        let score = 0;

        let choiceIDs = [];

        function startQuiz() {
            currentQuestionIndex = 0;
            score = 0;
            nextButton.innerHTML = "Next";
            showQuestion();
        }

        function showQuestion() {
            let currentQuestion = questions[currentQuestionIndex];
            let questionNo = currentQuestionIndex + 1;
            let totalQuestion = questions.length;
            let choiceNo = 1;

            choiceIDs = [];
            questionContainer.innerHTML = currentQuestion.
            question;
            questionNumber.innerHTML = "Section Name · Question " + questionNo + " of " + totalQuestion

            currentQuestion.answers.forEach(answer => {
                const button = document.createElement('div');
                button.innerHTML = answer.text;
                button.classList.add('col-12', 'bg-white', 'border', 'border-black', 'rounded-3', 'my-3', 'p-2', 'text-sbold', 'text-center', 'interactable');
                button.id = "question" + questionNo + "-choice" + choiceNo;
                choiceIDs.push(button.id);
                choiceNo++;
                button.addEventListener("click", answerQuestion);
                choices.appendChild(button);
            });

            const savedIndex = selectedAnswers[currentQuestionIndex];
            if (savedIndex !== null) {
                savedChoice = document.getElementById(choiceIDs[savedIndex]);
                savedChoice.classList.add('choice-selected');
                savedChoice.classList.remove('bg-white');
            }
        }

        function nextQuestion() {
            (currentQuestionIndex + 1 < 10) ? currentQuestionIndex++ : null;
            if ((currentQuestionIndex + 1) <= questions.length) {
                choices.innerHTML = '';
                showQuestion();
            }
        }

        function prevQuestion() {
            if ((currentQuestionIndex + 1) > 1) {
                currentQuestionIndex--;
                choices.innerHTML = '';
                showQuestion();
            }
        }

        function answerQuestion() {
            const hasSelected = choiceIDs.some(choice => {
                const choiceElement = document.getElementById(choice);
                return choiceElement.classList.contains('choice-selected');
            });

            if (hasSelected) {
                choiceIDs.forEach(choiceID => {
                    const clearChoice = document.getElementById(choiceID);
                    clearChoice.classList.remove('choice-selected');
                    clearChoice.classList.add('bg-white');
                });
                this.classList.add('choice-selected');
                this.classList.remove('bg-white');
                const selectedChoiceIndex = choiceIDs.indexOf(this.id);
                selectedAnswers[currentQuestionIndex] = selectedChoiceIndex;
            } else {
                this.classList.add('choice-selected');
                this.classList.remove('bg-white');
                const selectedChoiceIndex = choiceIDs.indexOf(this.id);
                selectedAnswers[currentQuestionIndex] = selectedChoiceIndex;
            }
        }

        showQuestion();
    </script>
</body>

</html>