<?php $activePage = 'miniGames'; ?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webstar | Typing Game</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="shared/assets/css/sidebar-and-container-styles.css">
    <link rel="stylesheet" href="shared/assets/css/typingGame.css">
</head>

<body>
    <div class="container-fluid min-vh-100 d-flex justify-content-center align-items-center p-3">
        <div class="row w-100">

            <!-- Sidebar (only shows on mobile) -->
            <?php include 'shared/components/sidebar-for-mobile.php'; ?>

            <!-- Sidebar Column (fixed on desktop) -->
            <?php include 'shared/components/sidebar-for-desktop.php'; ?>

            <!-- Main Container Column-->
            <div class="col main-container m-0 p-0 mx-2 p-4 position-relative">
                <div id="countdownOverlay">
                    <div id="countdownNumber">3</div>
                </div>
                <div class="card border-0 p-3 h-100 w-100 rounded-0 shadow-none">

                    <!-- Navbar for mobile -->
                    <?php include 'shared/components/navbar-for-mobile.php'; ?>

                    <!-- PUT CONTENT HERE -->
                    <!-- Main scrollable content area -->
                    <div class="scrollable-content" id="scrollableContent">
                        <div class="container pt-3">
                            <div class="progress mb-5">
                                <div id="progressBar" class="progressBar" role="progressbar" style="width: 100%">
                                </div>
                            </div>

                            <div class="card shadow p-5 cardGame">
                                <div class="mb-4 header text-center d-none d-md-block">Typing Challenge</div>
                                <div
                                    class="d-flex flex-column flex-md-row justify-content-between statusBar gap-2 mb-3 text-center text-md-start">
                                    <p class="statusBar m-0"><strong>Time Left:</strong> <span id="timeLeft">60</span>s
                                    </p>
                                    <p class="statusBar m-0"><strong>Points:</strong> <span id="points">0</span></p>
                                </div>
                                <p id="textDisplay" class="fs-5 lh-lg p-3 rounded text-center"></p>
                                <textarea id="inputText" autofocus></textarea>
                                <p id="result" class="result"></p>
                            </div>

                            <div class="d-none d-md-flex justify-content-center gap-4 mt-4">
                                <button class="btn btn-primary px-5 py-2 fs-5" onclick="retryGame()">Retry</button>
                                <button class="btn btn-danger px-5 py-2 fs-5" onclick="exitGame()">Exit</button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            const sentences = [
                "A fox darted across the road and vanished into the bushes.",
                "She watered the plant every day, even though it never bloomed.",
                "The bell tower chimed unexpectedly, echoing through the empty town square.",
                "He forgot why he came into the room but stayed anyway.",
                "Snowflakes landed on her coat and melted without a trace.",
                "The radio crackled, then a familiar voice filled the silence.",
                "They had dinner under the stars using flashlights and blankets.",
                "A single balloon floated into the sky and disappeared from view.",
                "The cat stared at the wall like it saw something invisible.",
                "He always wore mismatched socks, just for fun.",
                "The bakery smelled like cinnamon, sugar, and early morning joy.",
                "Even the wind paused when she began to sing.",
                "The dog barked once, then returned to its nap by the fire.",
                "She wrote letters she never planned to send.",
                "The road stretched endlessly toward the horizon, lined with golden fields."
            ];

            let sentence = "";
            let charIndex = 0;
            let score = 0;
            let timeLeft = 60;
            let interval;

            const textDisplay = document.getElementById("textDisplay");
            const inputText = document.getElementById("inputText");
            const points = document.getElementById("points");
            const timeDisplay = document.getElementById("timeLeft");
            const result = document.getElementById("result");
            const progressBar = document.getElementById("progressBar");
            const countdownOverlay = document.getElementById("countdownOverlay");
            const countdownNumber = document.getElementById("countdownNumber");

            function loadNewSentence() {
                sentence = sentences[Math.floor(Math.random() * sentences.length)];
                textDisplay.innerHTML = sentence.split('').map((char, idx) =>
                    `<span id='char-${idx}' ${idx === 0 ? 'class="current"' : ''}>${char}</span>`).join('');
                charIndex = 0;
            }

            function startGame() {
                loadNewSentence();
                inputText.value = "";
                inputText.focus();
                interval = setInterval(updateTime, 1000);
            }

            function updateTime() {
                timeLeft--;
                timeDisplay.textContent = timeLeft;
                progressBar.style.width = `${(timeLeft / 60) * 100}%`;
                if (timeLeft <= 0) {
                    clearInterval(interval);
                    inputText.disabled = true;
                    textDisplay.innerHTML = `<span class='result'>Game over!<br>You scored ${Math.floor(score)} points.</span>`;
                }
            }

            inputText.addEventListener("input", () => {
                const input = inputText.value;
                const currentChar = document.getElementById(`char-${charIndex}`);
                document.querySelectorAll("#textDisplay span").forEach(span => span.classList.remove("current"));

                if (!currentChar) return;

                if (input[input.length - 1] === sentence[charIndex]) {
                    currentChar.classList.remove("incorrect");
                    currentChar.classList.add("highlight");
                    score += 0.25;
                    charIndex++;
                    points.textContent = Math.floor(score);
                } else {
                    currentChar.classList.add("incorrect");
                }

                const nextChar = document.getElementById(`char-${charIndex}`);
                if (nextChar) nextChar.classList.add("current");

                if (charIndex === sentence.length) {
                    loadNewSentence();
                    inputText.value = "";
                }
            });

            function retryGame() {
                clearInterval(interval);
                score = 0;
                timeLeft = 60;
                points.textContent = "0";
                result.textContent = "";
                inputText.disabled = false;
                progressBar.style.width = "100%";
                timeDisplay.textContent = timeLeft;
                startGame();
            }

            function exitGame() {
                window.location.href = "miniGames.php";
            }

            let countdown = 3;
            countdownNumber.textContent = countdown;
            const countdownInterval = setInterval(() => {
                countdown--;
                if (countdown > 0) {
                    countdownNumber.textContent = countdown;
                } else {
                    clearInterval(countdownInterval);
                    countdownOverlay.style.display = "none";
                    startGame();
                }
            }, 1000);

            function handleScrollStyle() {
                const scrollable = document.getElementById("scrollableContent");
                if (window.innerWidth < 768) {
                    scrollable.style.maxHeight = "calc(100vh - 100px)";
                    scrollable.style.overflowY = "auto";
                    scrollable.style.paddingRight = "10px";
                } else {
                    scrollable.style.maxHeight = "none";
                    scrollable.style.overflowY = "visible";
                    scrollable.style.paddingRight = "0";
                }
            }

            window.addEventListener("resize", handleScrollStyle);
            window.addEventListener("load", handleScrollStyle);
        </script>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
            integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
            crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.min.js"
            integrity="sha384-RuyvpeZCxMJCqVUGFI0Do1mQrods/hhxYlcVfGPOfQtPJh0JCw12tUAZ/Mv10S7D"
            crossorigin="anonymous"></script>
</body>

</html>