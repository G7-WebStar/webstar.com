<?php
include('shared/assets/database/connect.php');

include("shared/assets/processes/session-process.php");
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
    <link rel="stylesheet" href="shared/assets/css/test.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="shared/assets/img/webstar-icon.png">

    <style>
        /* small inline styles to support selection UI (you can move to your css files) */
        .choice {
            cursor: pointer;
            user-select: none;
            transition: transform .06s ease, box-shadow .06s ease;
        }

        .choice:hover {
            transform: translateY(-2px);
        }

        .choice.selected {
            background-color: var(--primaryColor) !important;
        }

        .choices-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .choice-radio {
            display: none;
        }

        /* hidden radios for accessibility/state */
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

                    <div class="container-fluid py-1 overflow-y-auto d-flex flex-column h-100 row-padding-top">
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
                                        <div id="timer-display" class="h2 mt-3 mt-md-0 mb-0 text-center text-md-end">
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
                                            <div id="progress-text" class="h2 text-reg text-center mt-4 fs-sm-6">
                                                Section Name · Question 1 of 20
                                            </div>
                                            <div id="question-title" class="h2 text-sbold text-center mt-4 fs-sm-6 mb-5">
                                                Which HTML tag is used to define the largest heading?
                                            </div>
                                        </div>

                                        <!-- Choices (dynamic) -->
                                        <div id="choices-wrapper" class="col-12 col-md-8 h4 text-reg mx-auto mt-4 px-3 px-md-0 text-center text-md-start fs-sm-6">
                                            <div id="choices" class="choices-list"></div>
                                        </div>

                                        <div class="mt-auto text-sbold">
                                            <div class="d-flex justify-content-center justify-content-md-around align-items-center mb-4 gap-3 gap-md-0 mt-5">

                                                <button id="prevBtn" class="btn d-flex align-items-center justify-content-center gap-2 border border-black rounded-5 px-sm-4 py-sm-2"
                                                    style="background-color: var(--primaryColor);" type="button">
                                                    <i class="fa-solid fs-6 fa-arrow-left text-reg" style="color: var(--black);"></i>
                                                    <span class="m-0 fs-sm-6">Prev</span>
                                                </button>

                                                <button id="submitBtn" class="btn d-flex align-items-center justify-content-center border border-black rounded-5 px-sm-4 py-sm-2"
                                                    style="background-color: var(--primaryColor);" type="button">
                                                    <span class="m-0 fs-sm-6">Submit</span>
                                                </button>

                                                <button id="nextBtn" class="btn d-flex align-items-center justify-content-center gap-2 border border-black rounded-5 px-sm-4 py-sm-2"
                                                    style="background-color: var(--primaryColor);" type="button">
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

    <!-- Optional: bootstrap bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        /*************************************************
         * Quiz dynamic loader + persistence (localStorage)
         * - Renders questions and choices dynamically
         * - Saves answers on click
         * - Retains answers between next/prev and page reloads
         *************************************************/

        // --- Configuration / sample questions ---
        const questions = [{
                section: "Section Name",
                q: "Which HTML tag is used to define the largest heading?",
                choices: ["<h3>", "<h6>", "<h1>", "<head>"]
            },
            {
                section: "Section Name",
                q: "Which tag is used for a paragraph?",
                choices: ["<p>", "<para>", "<text>", "<ph>"]
            },
            {
                section: "Section Name",
                q: "Which tag creates an unordered list?",
                choices: ["<ul>", "<ol>", "<li>", "<list>"]
            },
            {
                section: "Section Name",
                q: "Which attribute specifies an image source?",
                choices: ["href", "src", "alt", "link"]
            },
            {
                section: "Section Name",
                q: "Which tag is used to create a link?",
                choices: ["<link>", "<a>", "<href>", "<url>"]
            },
            {
                section: "Section Name",
                q: "Which tag defines a table row?",
                choices: ["<td>", "<tr>", "<th>", "<table>"]
            },
            {
                section: "Section Name",
                q: "Which tag is for bold text?",
                choices: ["<b>", "<bold>", "<strong>", "<em>"]
            },
            {
                section: "Section Name",
                q: "Which meta tag controls viewport on mobile?",
                choices: ["<meta name='viewport'>", "<meta charset>", "<meta http-equiv>", "<meta name='description'>"]
            },
            {
                section: "Section Name",
                q: "Which element is used to embed JavaScript?",
                choices: ["<js>", "<javascript>", "<script>", "<code>"]
            },
            {
                section: "Section Name",
                q: "Which tag contains page title shown in browser tab?",
                choices: ["<header>", "<title>", "<head>", "<meta>"]
            },
            {
                section: "Section Name",
                q: "Which HTML element represents navigation links?",
                choices: ["<nav>", "<ul>", "<header>", "<menu>"]
            },
            {
                section: "Section Name",
                q: "Which tag defines an ordered list?",
                choices: ["<ol>", "<ul>", "<li>", "<list>"]
            },
            {
                section: "Section Name",
                q: "Which attribute provides alternative text for an image?",
                choices: ["alt", "src", "title", "caption"]
            },
            {
                section: "Section Name",
                q: "Which tag is used to define a clickable button?",
                choices: ["<button>", "<input type='button'>", "<a>", "Both A and B"]
            },
            {
                section: "Section Name",
                q: "Which tag defines a section heading smaller than h1?",
                choices: ["<h2>", "<h7>", "<subheading>", "<head>"]
            },
            {
                section: "Section Name",
                q: "Which tag groups form elements?",
                choices: ["<fieldset>", "<group>", "<form>", "<div>"]
            },
            {
                section: "Section Name",
                q: "Which tag provides a caption for a table?",
                choices: ["<caption>", "<title>", "<thead>", "<label>"]
            },
            {
                section: "Section Name",
                q: "Which tag is used to embed an image?",
                choices: ["<img>", "<picture>", "<image>", "<figure>"]
            },
            {
                section: "Section Name",
                q: "Which attribute opens link in a new tab?",
                choices: ["target='_blank'", "rel='noopener'", "href='_new'", "open='new'"]
            },
            {
                section: "Section Name",
                q: "Which element represents emphasized text?",
                choices: ["<em>", "<strong>", "<i>", "<u>"]
            },
        ];

        // --- State ---
        const STORAGE_KEY = 'webstar_quiz_answers';
        const SUBMITTED_KEY = 'webstar_quiz_submitted';
        let currentIndex = 0;
        let answers = {}; // { 0: 2, 1: 0, ... } choice indexes
        let submitted = false;

        // --- Elements ---
        const progressText = document.getElementById('progress-text');
        const questionTitle = document.getElementById('question-title');
        const choicesEl = document.getElementById('choices');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const submitBtn = document.getElementById('submitBtn');
        const timerDisplay = document.getElementById('timer-display');

        // --- Helpers: localStorage load/save ---
        function loadState() {
            try {
                const raw = localStorage.getItem(STORAGE_KEY);
                if (raw) answers = JSON.parse(raw) || {};
            } catch (e) {
                answers = {};
            }
            try {
                submitted = JSON.parse(localStorage.getItem(SUBMITTED_KEY)) || false;
            } catch (e) {
                submitted = false;
            }
        }

        function saveAnswers() {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(answers));
        }

        function saveSubmitted() {
            localStorage.setItem(SUBMITTED_KEY, JSON.stringify(submitted));
        }

        // --- Render a question ---
        function renderQuestion(index) {
            const qObj = questions[index];
            progressText.textContent = `${qObj.section} · Question ${index + 1} of ${questions.length}`;
            questionTitle.textContent = qObj.q;

            // Clear choices
            choicesEl.innerHTML = '';

            qObj.choices.forEach((choiceText, i) => {
                const id = `choice-${index}-${i}`;

                // outer container
                const choiceWrap = document.createElement('div');
                choiceWrap.className = 'col-12 bg-white border border-black rounded-3 p-2 text-sbold text-center choice';
                choiceWrap.setAttribute('role', 'button');
                choiceWrap.setAttribute('tabindex', '0');
                choiceWrap.dataset.choiceIndex = i;

                // hidden radio for form semantics
                const radio = document.createElement('input');
                radio.type = 'radio';
                radio.name = `q-${index}`;
                radio.id = id;
                radio.className = 'choice-radio';
                radio.value = i;

                // text node
                const label = document.createElement('label');
                label.htmlFor = id;
                label.style.cursor = 'pointer';
                label.innerHTML = escapeHtml(choiceText);

                choiceWrap.appendChild(radio);
                choiceWrap.appendChild(label);

                // click handler
                choiceWrap.addEventListener('click', () => {
                    selectChoice(index, i);
                });

                // keyboard support (enter/space)
                choiceWrap.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        selectChoice(index, i);
                    }
                });

                choicesEl.appendChild(choiceWrap);
            });

            // reflect previously selected answer
            const prevSelected = answers[index];
            if (typeof prevSelected !== 'undefined' && prevSelected !== null) {
                highlightSelected(index, prevSelected);
            }

            updateNavButtons();
        }

        // mark a choice as selected visually and save
        function selectChoice(qIndex, choiceIndex) {
            answers[qIndex] = choiceIndex;
            saveAnswers();
            highlightSelected(qIndex, choiceIndex);
        }

        function highlightSelected(qIndex, choiceIndex) {
            // iterate through choices wrapper children
            const children = Array.from(choicesEl.children);
            children.forEach(child => {
                child.classList.remove('selected');
                const idx = parseInt(child.dataset.choiceIndex, 10);
                const radio = child.querySelector('.choice-radio');
                if (radio) radio.checked = false;
                if (idx === choiceIndex) {
                    child.classList.add('selected');
                    const radio2 = child.querySelector('.choice-radio');
                    if (radio2) radio2.checked = true;
                }
            });
        }

        // navigation handlers
        function gotoQuestion(index) {
            if (index < 0 || index >= questions.length) return;
            currentIndex = index;
            renderQuestion(currentIndex);
            // scroll to top of question area (if on mobile)
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        function updateNavButtons() {
            prevBtn.disabled = currentIndex === 0;
            nextBtn.disabled = currentIndex === (questions.length - 1);

            // change label on submit if already submitted
            if (submitted) {
                submitBtn.textContent = 'Submitted ✓';
                submitBtn.disabled = true;
            } else {
                submitBtn.textContent = 'Submit';
                submitBtn.disabled = false;
            }
        }

        // Submit handler
        function handleSubmit() {
            // simple confirmation
            if (!submitted) {
                // Basic validation: ensure at least one answer? optional
                // For now allow submit even if incomplete
                submitted = true;
                saveSubmitted();

                // You can add code here to POST to server instead:
                // fetch('save-answers.php', { method: 'POST', body: JSON.stringify(answers) ... })

                // UI feedback
                submitBtn.textContent = 'Submitted ✓';
                submitBtn.disabled = true;
                // show a simple bootstrap toast or alert
                showToast('Your answers have been saved and submitted.', 3000);
            }
        }

        // small toast utility
        function showToast(message, ms = 2500) {
            // create toast DOM
            const toast = document.createElement('div');
            toast.className = 'position-fixed bottom-0 end-0 m-3 p-3 rounded shadow';
            toast.style.background = '#fff';
            toast.style.zIndex = 9999;
            toast.textContent = message;
            document.body.appendChild(toast);
            setTimeout(() => {
                toast.style.transition = 'opacity .25s';
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 300);
            }, ms);
        }

        // utility to escape HTML when injecting plain text into DOM
        function escapeHtml(unsafe) {
            return unsafe
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;");
        }

        // --- Timer (optional) ---
        // sample countdown 10:00 (600 seconds) — you can adjust or disable
        let timerSeconds = 600; // 10 minutes
        let timerInterval = null;

        function startTimer() {
            function formatTime(s) {
                const mm = Math.floor(s / 60).toString().padStart(2, '0');
                const ss = (s % 60).toString().padStart(2, '0');
                return `${mm}:${ss}`;
            }
            timerDisplay.querySelector ? null : null;
            if (!timerDisplay) return;
            timerDisplay.querySelector; // no-op to avoid unused warnings

            // initial render
            if (timerDisplay) {
                // keep the icon — replace only the time text after icon
                timerDisplay.innerHTML = `<i class="bi bi-clock fa-xs me-2" style="color: var(--black);"></i> ${formatTime(timerSeconds)}`;
            }

            timerInterval = setInterval(() => {
                timerSeconds--;
                if (timerDisplay) {
                    timerDisplay.innerHTML = `<i class="bi bi-clock fa-xs me-2" style="color: var(--black);"></i> ${formatTime(timerSeconds)}`;
                }
                if (timerSeconds <= 0) {
                    clearInterval(timerInterval);
                    // Auto-submit when timer ends
                    handleSubmit();
                    showToast('Time is up — your answers were submitted automatically.', 4000);
                }
            }, 1000);
        }

        // --- Event listeners ---
        prevBtn.addEventListener('click', () => gotoQuestion(currentIndex - 1));
        nextBtn.addEventListener('click', () => gotoQuestion(currentIndex + 1));
        submitBtn.addEventListener('click', handleSubmit);

        // --- Init ---
        (function init() {
            loadState();

            // ensure currentIndex valid
            if (currentIndex < 0 || currentIndex >= questions.length) currentIndex = 0;

            renderQuestion(currentIndex);

            // Start timer if desired
            startTimer();
        })();

        // Optional: Expose a function to clear stored answers (useful during development)
        window._clearQuizStorage = function() {
            localStorage.removeItem(STORAGE_KEY);
            localStorage.removeItem(SUBMITTED_KEY);
            answers = {};
            submitted = false;
            saveAnswers();
            saveSubmitted();
            gotoQuestion(0);
            showToast('Local quiz storage cleared', 1200);
        };
    </script>
</body>

</html>