<?php $activePage = 'course'; ?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Webstar</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Comic+Neue&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="shared/assets/css/global-styles.css">
  <link rel="stylesheet" href="shared/assets/css/sidebar-and-container-styles.css">
  <link rel="stylesheet" href="shared/assets/css/lesson.css">
  <link rel="stylesheet" href="shared/assets/css/lessonmodal.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>

<body>
  <div class="container-fluid min-vh-100 d-flex justify-content-center align-items-center p-3">
    <div class="row w-100">

      <!-- Sidebar (only shows on mobile) -->
      <?php include 'shared/components/sidebar-for-mobile.php'; ?>

      <!-- Sidebar Column (fixed on desktop) -->
      <div class="col-auto d-none d-md-block">
        <?php include 'shared/components/sidebar-for-desktop.php'; ?>
      </div>

      <!-- Main Container Column-->
      <div class="col main-container m-0 p-0 mx-2 p-2">
        <div class="card border-0 p-2 h-100 w-100 rounded-0 shadow-none">

          <!-- Navbar for mobile -->
          <?php include 'shared/components/navbar-for-mobile.php'; ?>

          <!-- PUT CONTENT HERE -->


          <div class="container-fluid overflow-y-auto" style="max-height: 100vh;">
            <div class="col">
              <!-- Main panel -->
              <div class="main px-4 py-4">
                <p class="fs-5">CSS Course Lesson 3</p>
                <h1 class="fw-bold mb-3">Fonts and Formatting</h1>
                <p>Fonts are the different styles or designs of text that appear on a webpage. Just like handwriting
                  styles, fonts change the way letters and words look. Fonts can feel professional, fun, serious, or
                  creative—depending on their design.</p>

                <!-- Hoverrable button -->
                <p class="mt-3">Hover to reveal these fonts!</p>
                <div class="d-flex flex-wrap gap-3 my-3 font-buttons">
                  <button class="btn font-btn" data-font="Helvetica">Helvetica</button>
                  <button class="btn font-btn" data-font="'Comic Neue', cursive">Comic Neue</button>
                  <button class="btn font-btn" data-font="'Poppins', sans-serif">Poppins</button>
                </div>

                <p>The <span class="bold">font</span> shorthand property in CSS is used to define all the font-related
                  settings of an element in one line. It can also be used to apply a system-defined font to the element.
                </p>

                <!-- Realtime code -->
                <div class="mt-5 d-flex align-items-center gap-2">
                  <img src="shared/assets/img/library_books.png" alt="Book Icon" style="width: 40px; height: 40px;">
                  <div class="bold fs-5 m-0">Interactive Activity: Try Your Own Fonts!</div>
                </div>

                <div class="flex-grow-1">
                  <p>Replace "Arial" with any font that’s installed on your computer—for example, Verdana, Times New
                    Roman, Comic Sans MS, or another font you like! </p>
                  <p>font-family: Arial; font-size: 30px; color: red;</p>

                  <div class="d-flex flex-column flex-md-row align-items-center gap-3">
                    <div class="code-block">
                      <input type="text" id="cssInput" class="form-control bg-white text-dark w-100"
                        placeholder="font-family: Arial; font-size: 30px; color: red;">
                    </div>
                    <div class="output-box" id="liveOutput"
                      style="background-color: #2A63E2; color: #FAFAE6; display: flex; justify-content: center; align-items: center;">
                      This is a text
                    </div>
                  </div>
                </div>

                <!-- Observe section -->
                <div class="mt-5">
                  <p class="fs-5">
                    <img src="shared/assets/img/lightbulb.png" alt="Observe Icon" style="width: 40px; height: 40px;">
                    <span class="bold">Observe</span>
                  </p>
                  <p>
                    Does the text look different? Try a few different fonts and see how each one changes the style or
                    feel
                    of the text.</p>
                </div>
              </div>

              <!-- Mini game drag and drop-->
              <div class="mt-5 px-4">
                <div class="title">
                  <img src="shared/assets/img/puzzle.png" alt="Mini Game Icon"
                    style="width: 40px; height: 40px; vertical-align: middle; margin-right: 8px;">
                  Mini Game: Match the Font Style!
                </div>
                <div class="descriptiongame">
                  <p>In this game, your goal is to match text with its correct font style. This will help you become
                    familiar with the different ways text can be styled in web design. Just drag and drop to arrange the
                    code blocks on the right to match the output on the left!</p>
                </div>
                <div class="container" id="codePairs">
                  <div class="row mb-3 mt-4 justify-content-center">
                    <div class="col-12 col-md-3 pb-2 pb-md-0">
                      <div class="game-output-text" style="font-weight: bold;">Hello, World!</div>
                    </div>
                    <div class="col-12 col-md-7">
                      <div class="game-code-block" draggable="true">
                        &lt;p style="font-style: italic;"&gt;Hello, World!&lt;/p&gt;
                      </div>
                    </div>
                  </div>

                  <div class="row mb-3 justify-content-center">
                    <div class="col-12 col-md-3 pb-2 pb-md-0">
                      <div class="game-output-text" style="font-weight: 100; font-style: italic;">Hello, World!</div>
                    </div>
                    <div class="col-12 col-md-7">
                      <div class="game-code-block" draggable="true">
                        &lt;p&gt;Hello, World!&lt;/p&gt;
                      </div>
                    </div>
                  </div>

                  <div class="row mb-3 justify-content-center">
                    <div class="col-12 col-md-3 pb-2 pb-md-0">
                      <div class="game-output-text" style="font-weight: 100;">Hello, World!</div>
                    </div>
                    <div class="col-12 col-md-7">
                      <div class="game-code-block" draggable="true">
                        &lt;p style="font-weight: bold;"&gt;Hello, World!&lt;/p&gt;
                      </div>
                    </div>
                  </div>

                  <div class="row mb-3 justify-content-center">
                    <div class="col-12 col-md-3 pb-2 pb-md-0">
                      <div class="game-output-text" style="font-weight: 100; text-decoration: line-through;">Hello, World!</div>
                    </div>
                    <div class="col-12 col-md-7">
                      <div class="game-code-block" draggable="true">
                        &lt;p style="text-decoration: underline;"&gt;Hello, World!&lt;/p&gt;
                      </div>
                    </div>
                  </div>

                  <div class="row mb-3 justify-content-center">
                    <div class="col-12 col-md-3 pb-2 pb-md-0">
                      <div class="game-output-text" style="font-weight: 100; text-decoration: underline;">Hello, World!</div>
                    </div>
                    <div class="col-12 col-md-7">
                      <div class="game-code-block" draggable="true">
                        &lt;p style="text-decoration: line-through;"&gt;Hello, World!&lt;/p&gt;
                      </div>
                    </div>
                  </div>
                </div>

                <div class="submit-container">
                  <button class="submit-btn" onclick="checkAnswers()">Submit</button>
                </div>
              </div>
            </div>

            <!-- MODAL -->
            <div class="modal fade" id="cardModal" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content custom-modal text-center p-4 rounded-4 position-relative overflow-hidden">

                  <!-- Custom Close Button (✕) -->
                  <div class="d-flex justify-content-end px-4 pt-4">
                    <button type="button" class="custom-close" data-bs-dismiss="modal" aria-label="Close">✕</button>
                  </div>

                  <!-- Divider Line Under Close Button -->
                  <hr class="modal-divider mb-3">

                  <div id="modalResultTitle" class="mb-3">Good Job!</div>

                  <div id="modalResultMessage" class="mb-3" style="color: var(--blue);" >
                    <div><strong>Fonts and Formatting</strong></div>
                    <div>You’ve completed the <strong>Fonts and Formatting</strong> lesson!</div>
                    <div>You’ve learned how to use bold, italic, underline, and strikethrough styles in HTML and CSS.
                    </div>
                    <br />
                    <div>Rewards:</div>
                    <div class="highlight" style="color: var(--blue);">+300 WebStars</div>
                    <div class="badge-unlocked mb-2">Badge Unlocked: Font Master</div>
                    <img src="shared/assets/img/done.png" alt="Done Icon" style="width: 50px; height: 50px;"
                      class="mb-3">
                  </div>

                  <button class="btn btn-primary continue-btn submit-btn" data-bs-dismiss="modal">Continue</button>
                </div>
              </div>
            </div>


            <script>
              // Live css input
              const cssInput = document.getElementById("cssInput");
              const liveOutput = document.getElementById("liveOutput");

              cssInput.addEventListener("input", () => {
                liveOutput.style.cssText = cssInput.value;
                if (!cssInput.value.includes("background-color")) {
                  liveOutput.style.backgroundColor = "#2A63E2";
                }
                if (!cssInput.value.includes("color")) {
                  liveOutput.style.color = "#FAFAE6";
                }
              });

              // Hover font selector
              const fontButtons = document.querySelectorAll(".font-btn");
              const mainContent = document.querySelector(".main");

              fontButtons.forEach(button => {
                const font = button.getAttribute("data-font");
                button.addEventListener("mouseenter", () => {
                  mainContent.style.fontFamily = font;
                });
                button.addEventListener("mouseleave", () => {
                  mainContent.style.fontFamily = "";
                });
              });

              // Drag and drop minigame
              const correctOrder = [
                '&lt;p style="font-weight: bold;"&gt;Hello, World!&lt;/p&gt;',
                '&lt;p style="font-style: italic;"&gt;Hello, World!&lt;/p&gt;',
                '&lt;p&gt;Hello, World!&lt;/p&gt;',
                '&lt;p style="text-decoration: line-through;"&gt;Hello, World!&lt;/p&gt;',
                '&lt;p style="text-decoration: underline;"&gt;Hello, World!&lt;/p&gt;'
              ];

              const blocks = document.querySelectorAll('.game-code-block');

              blocks.forEach(block => {
                block.addEventListener('dragstart', e => {
                  e.dataTransfer.setData('text/plain', block.innerHTML);
                  block.classList.add('dragging');
                });

                block.addEventListener('dragover', e => e.preventDefault());

                block.addEventListener('drop', e => {
                  e.preventDefault();
                  const dragged = document.querySelector('.dragging');
                  const temp = block.innerHTML;
                  block.innerHTML = e.dataTransfer.getData('text/plain');
                  dragged.innerHTML = temp;
                  dragged.classList.remove('dragging');
                });

                block.addEventListener('dragend', () => {
                  block.classList.remove('dragging');
                });
              });

              // Check answer
              function checkAnswers() {
                const currentBlocks = document.querySelectorAll('.game-code-block');
                let correct = true;
                currentBlocks.forEach((block, index) => {
                  if (block.innerHTML.trim() !== correctOrder[index].trim()) {
                    correct = false;
                  }
                });

                // Modal design
                const title = document.getElementById('modalResultTitle');
                const message = document.getElementById('modalResultMessage');

                if (correct) {
                  title.textContent = "Good Job!";
                  title.classList.remove("var(--blue)");
                  title.classList.add("text");
                  message.innerHTML = `<div>You’ve completed the <strong>Fonts and Formatting</strong> lesson!</div>
                  <div>You’ve learned how to use bold, italic, underline, and strikethrough styles in HTML and CSS.</div>
                  <br />
                  <div>Rewards:</div>
                  <div class="highlight" style="color: var(--blue);">+300 WebStars</div>
                  <div class="badge-unlocked mb-2">Badge Unlocked: Font Master</div>
                  <img src="shared/assets/img/done.png" alt="Done Icon" style="width: 50px; height: 50px;" class="mb-3">
                `;
                } else {
                  title.textContent = "Oops!";
                  title.classList.remove("text-primary");
                  title.classList.add("var(--blue)");
                  message.innerHTML = `Something is wrong.<br />Please try again.`;
                }

                const modal = new bootstrap.Modal(document.getElementById('cardModal'));
                modal.show();
              }
            </script>

          </div>
        </div>

      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>