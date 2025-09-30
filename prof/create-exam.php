<?php $activePage = 'create-exam'; ?>

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
                                        <span class="text-sbold text-25">Create Exam</span>
                                    </div>
                                </div>

                                <!-- Form starts -->
                                <form>
                                    <div class="row">
                                        <div class="col-12 pt-3 mb-3">
                                            <label for="lessonInfo" class="form-label text-med text-16">Exam
                                                Information</label>
                                            <input type="text"
                                                class="form-control textbox mb-3 p-2 text-reg text-14 text-muted"
                                                id="lessonInfo" aria-describedby="lessonInfo" placeholder="Exam Title">
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
                                            <input type="hidden" name="task" id="task">
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
                                                        class="form-control textbox text-reg text-14" />
                                                </div>
                                            </div>

                                            <!-- Time limit -->
                                            <div class="col-md-4">
                                                <label class="form-label text-med text-16">
                                                    Time Limit (minutes)
                                                </label>
                                                <span class="fst-italic text-reg text-12">Optional.</span>
                                                <input type="number" class="form-control textbox text-reg text-14"
                                                    style="max-width: 320px;" placeholder="100" />
                                            </div>
                                        </div>

                                        <div class="form-check mt-2 col ms-2">
                                            <input class="form-check-input" type="checkbox" id="stopSubmissions"
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
                                                Total Points: 10
                                            </label>
                                        </div>
                                    </div>
                                    <!-- Templates -->
                                    <template id="sectionTemplate">
                                        <div class="row section-block">
                                            <div class="col-12 pt-3 mb-3">
                                                <div
                                                    class="form-control textbox p-3 text-reg text-14 text-muted position-relative">

                                                    <!-- Delete Button (styled like Multiple Choice buttons) -->
                                                    <button type="button" class="delete-template"
                                                        style="position: absolute; top: 5px; right: 1px; background: none; border: none; color: var(--black); cursor: pointer;">
                                                        <i class="fas fa-times"></i>
                                                    </button>

                                                    <input type="text"
                                                        class="form-control textbox1 mb-1 p-2 text-reg text-14 text-muted"
                                                        placeholder="Section Name">

                                                    <input type="text"
                                                        class="form-control textbox1 p-2 text-reg text-14 text-muted"
                                                        placeholder="Instructions">
                                                </div>
                                            </div>
                                        </div>
                                    </template>


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

                                                    <div class="input-group text-reg text-14 text-muted mb-3 mt-2">
                                                        <span class="input-group-text text-bold rounded-left ms-3"
                                                            style="background-color: var(--primaryColor);">1</span>
                                                        <input type="text" class="question-box form-control"
                                                            placeholder="Question">
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

                                                        <input type="file" accept="image/*" class="image-upload"
                                                            style="display: none;">
                                                    </div>

                                                    <div class="row position-relative ms-3 mb-2">
                                                        <!-- Points Column -->
                                                        <div class="col-auto text-center me-4 flex-shrink-0">
                                                            <div class="text-reg mb-1">Points</div>
                                                            <input type="number" class="border rounded p-2"
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

                                                    <!-- Question -->
                                                    <div class="input-group text-reg text-14 text-muted mb-3 mt-2">
                                                        <span
                                                            class="input-group-text text-bold rounded-left ms-3 question-number"
                                                            style="background-color: var(--primaryColor);">1</span>
                                                        <input type="text" class="question-box form-control"
                                                            placeholder="Question">
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
                                                        <input type="file" accept="image/*" class="image-upload"
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
                                                            <input type="number" class="border rounded p-2"
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
                                            <button type="button" id="addSection" class="btn text-reg rounded-pill ms-1"
                                                style="background-color: var(--primaryColor); width: 180px;">
                                                <i class="fas fa-users me-2"></i> Section
                                            </button>

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
                                                    <li>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="courses[]" value="1" id="course1">
                                                            <label class="form-check-label text-reg" for="course1">
                                                                WEBDEV101 </label>
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="courses[]" value="2" id="course2">
                                                            <label class="form-check-label text-reg" for="course2">
                                                                WEBDEV102 </label>
                                                        </div>
                                                    </li>
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
            </div>
        </div>
    </div>
    </div>

    <!-- Quill JS -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script>
        var quill = new Quill('#editor', {
            theme: 'snow',
            placeholder: 'Exam General Guidelines',
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
                quill.setSelection(quill.getLength()); // keep cursor at end
            }

            counter.textContent = `${Math.min(words, maxWords)}/${maxWords}`;
        });
    </script>
    <!-- JS -->
    <script>
        const mainContainer = document.getElementById("allQuestionsContainer");
    </script>

    <!-- Section -->
    <script>
        document.getElementById("addSection").addEventListener("click", () => {
            const clone = document.getElementById("sectionTemplate").content.cloneNode(true);
            mainContainer.appendChild(clone);
        });
    </script>

    <!-- Identification -->
    <script>
        let identificationCount = 1; // counter for identification

        document.getElementById("addIdentification").addEventListener("click", () => {
            const clone = document.getElementById("identificationTemplate").content.cloneNode(true);

            // update the number inside the span
            const numberSpan = clone.querySelector(".input-group-text");
            if (numberSpan) {
                numberSpan.textContent = identificationCount;
            }

            mainContainer.appendChild(clone);
            identificationCount++;
        });

        // Add answer (delegated)
        document.addEventListener("click", function (e) {
            if (e.target.closest(".add-answer-btn")) {
                const button = e.target.closest(".add-answer-btn");
                const container = button.closest(".answers-scroll").querySelector(".answers-container");

                const wrapper = document.createElement("div");
                wrapper.classList.add("answer-wrapper", "me-2", "d-inline-flex", "align-items-center");

                const input = document.createElement("input");
                input.type = "text";
                input.placeholder = "Answer";
                input.classList.add("border", "rounded", "p-2");
                input.style.width = "120px";

                const removeBtn = document.createElement("button");
                removeBtn.type = "button";
                removeBtn.innerHTML = `<i class="fas fa-times"></i>`;
                removeBtn.onclick = () => wrapper.remove();

                wrapper.appendChild(input);
                wrapper.appendChild(removeBtn);

                container.appendChild(wrapper);
                container.scrollLeft = container.scrollWidth; // scroll to end
            }
        });
    </script>


    <!-- Multiple Choice -->
    <script>
        let questionCount = 1;

        document.getElementById("addMultipleChoice").addEventListener("click", () => {
            const clone = document.getElementById("multipleChoiceTemplate").content.cloneNode(true);
            clone.querySelector(".question-number").textContent = questionCount;

            // Add choice button logic
            const addChoiceBtn = clone.querySelector(".add-radio-btn");
            const choicesContainer = clone.querySelector(".radio-choices-container");
            let choiceCount = choicesContainer.querySelectorAll(".form-check").length;

            addChoiceBtn.addEventListener("click", () => {
                choiceCount++;
                const newChoice = document.createElement("div");
                newChoice.classList.add("form-check", "text-start", "d-flex", "align-items-center", "mb-2", "position-relative");

                // Radio button
                const radio = document.createElement("input");
                radio.type = "radio";
                radio.classList.add("form-check-input", "me-2");
                radio.name = "questionChoice" + questionCount;
                radio.value = choiceCount;

                // Editable input
                const input = document.createElement("input");
                input.type = "text";
                input.classList.add("choice-input");
                input.value = "Choice " + choiceCount;
                input.style.border = "none";
                input.style.outline = "none";
                input.style.width = "100%";
                input.style.maxWidth = "200px";
                input.style.background = "transparent";

                // Delete button
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

                // Delete function
                deleteBtn.addEventListener("click", () => {
                    newChoice.remove();
                });

                newChoice.appendChild(radio);
                newChoice.appendChild(input);
                newChoice.appendChild(deleteBtn);
                choicesContainer.appendChild(newChoice);
            });

            document.getElementById("allQuestionsContainer").appendChild(clone);
            questionCount++;
        });
    </script>



    <!-- Toggle Image Container -->
    <script>
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
    </script>

    <!-- Image Upload -->
    <script>
        // Function to bind upload logic to an image + input
        function bindImageUpload(img) {
            const fileInput = img.nextElementSibling; // hidden input

            // Open file dialog when image is clicked
            img.addEventListener("click", () => {
                fileInput.click();
            });

            // Show uploaded image instantly
            fileInput.addEventListener("change", () => {
                if (fileInput.files && fileInput.files[0]) {
                    const reader = new FileReader();
                    reader.onload = e => {
                        img.src = e.target.result;
                    };
                    reader.readAsDataURL(fileInput.files[0]);
                }
            });
        }

        // Run once for existing .question-image
        document.querySelectorAll(".question-image").forEach(img => bindImageUpload(img));

        // Also re-run whenever a new block is added
        const observer = new MutationObserver(() => {
            document.querySelectorAll(".question-image").forEach(img => {
                if (!img.dataset.bound) {
                    bindImageUpload(img);
                    img.dataset.bound = "true"; // prevent duplicate bindings
                }
            });
        });

        // Watch the main container for newly added elements
        observer.observe(document.getElementById("allQuestionsContainer"), {
            childList: true,
            subtree: true
        });
    </script>

    <script>
        document.addEventListener("click", function (e) {
            if (e.target.closest(".delete-template")) {
                const block = e.target.closest(".row");
                if (block) block.remove();
            }
        });
    </script>
    <script>
        // Delete only the image container
        document.addEventListener("click", function (e) {
            if (e.target.closest(".delete-image")) {
                const imageContainer = e.target.closest(".image-container");
                if (imageContainer) {
                    imageContainer.remove();
                }
            }
        });
    </script>
    <script>
        // Delete only the clicked template instance
        document.addEventListener("click", function (e) {
            if (e.target.closest(".delete-template")) {
                const templateBox = e.target.closest(".multiple-choice-item");
                if (templateBox) {
                    templateBox.remove();
                }
            }
        });

    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>