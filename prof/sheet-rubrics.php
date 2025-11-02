<?php $activePage = 'sheet-rubrics'; ?>
<?php
include("../shared/assets/database/connect.php");

// Use GET lessonID if provided, otherwise default to 5
$lessonID = isset($_GET['lessonID']) ? intval($_GET['lessonID']) : 19;

// Fetch all files under this lessonID
$sql = "SELECT fileAttachment FROM files WHERE lessonID = $lessonID";
$result = $conn->query($sql);

$fileLinks = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // store original attachment value and build safe path
        $fileName = basename($row['fileAttachment']);
        $fileLinks[] = [
            'name' => $fileName,
            'path' => "../shared/assets/files/" . $fileName
        ];
    }
}

// helper to check extension
function is_image_ext($ext)
{
    $ext = strtolower($ext);
    return in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg']);
}
function is_pdf_ext($ext)
{
    return strtolower($ext) === 'pdf';
}

// decide view type: if no files -> none; else check first file's ext and use that
$viewType = 'none';
if (!empty($fileLinks)) {
    $firstExt = pathinfo($fileLinks[0]['name'], PATHINFO_EXTENSION);
    if (is_image_ext($firstExt))
        $viewType = 'image';
    else if (is_pdf_ext($firstExt))
        $viewType = 'pdf';
    else
        $viewType = 'other';
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webstar | Assignment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="../shared/assets/css/grading-sheet-pdf-with-image.css">
    <link rel="stylesheet" href="../shared/assets/css/sidebar-and-container-styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../shared/assets/img/webstar-icon.png">

    <!-- Material Design Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:FILL@1" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp" />
</head>

<body>
    <div class="container-fluid min-vh-100 d-flex justify-content-center align-items-center align-items-md-start p-0 p-md-3"
        style="background-color: var(--black); overflow-y:auto;">

        <div class="row w-100">
            <!-- Sidebar -->
            <?php include '../shared/components/prof-sidebar-for-mobile.php'; ?>
            <?php include '../shared/components/prof-sidebar-for-desktop.php'; ?>

            <!-- Main Container -->
            <div class="col main-container m-0 p-0 mx-0 mx-md-2 p-0 p-md-4 overflow-y-auto">
                <div class="card border-0 px-3 pt-3 m-0 h-100 w-100 rounded-0 shadow-none"
                    style="background-color: transparent;">
                    <?php include '../shared/components/prof-navbar-for-mobile.php'; ?>

                    <div class="container-fluid py-3 overflow-y-auto row-padding-top">
                        <div class="row mb-3">
                            <div class="col-12 cardHeader p-3 mb-4">
                                <div
                                    class="row desktop-header d-none d-sm-flex align-items-center justify-content-between">
                                    <div class="col-auto d-flex align-items-center gap-3">
                                        <a href="todo.php?userID=2" class="text-decoration-none">
                                            <i class="fa-solid fa-arrow-left text-20" style="color: var(--black);"></i>
                                        </a>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="rounded-circle"
                                                style="width: 40px; height: 40px; background-color: var(--highlight75);">
                                            </div>
                                            <div>
                                                <div class="text-sbold text-18" style="color: var(--black);">Christian
                                                    James D. Torrillo · BSIT 3-1</div>
                                                <div class="text-reg text-14 text-muted">Assessing 2 of 30 students with
                                                    submissions</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-auto text-end" style="line-height: 1.3;">
                                        <div class="text-sbold text-16" style="color: var(--black);">Assignment #1</div>
                                        <div class="text-reg text-muted text-14">COMP–006<br>Web Development</div>
                                    </div>
                                </div>

                                <!-- MOBILE VIEW HEADER -->
                                <div class="d-block d-sm-none mobile-assignment mt-3">
                                    <div class="mobile-top d-flex align-items-center gap-3">
                                        <div class="arrow">
                                            <a href="todo.php?userID=2" class="text-decoration-none">
                                                <i class="fa-solid fa-arrow-left text-reg text-16"
                                                    style="color: var(--black);"></i>
                                            </a>
                                        </div>
                                        <div class="title text-sbold text-18">Assignment #1</div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="row">
                            <!-- Left Content -->
                            <div class="col-12 col-lg-8 mb-5">
                                <div class="p-0 px-lg-5">
                                    <?php
                                    $images = [];
                                    $pdfs = [];
                                    $others = [];

                                    foreach ($fileLinks as $f) {
                                        $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
                                        if (is_image_ext($ext)) {
                                            $images[] = $f;
                                        } elseif (is_pdf_ext($ext)) {
                                            $pdfs[] = $f;
                                        } else {
                                            $others[] = $f;
                                        }
                                    }
                                    ?>

                                    <div class="text-sbold text-14 mt-4">Attachments</div>

                                    <?php if (!empty($images) || !empty($pdfs) || !empty($others)): ?>

                                        <!-- 🖼 IMAGE SECTION -->
                                        <?php if (!empty($images)): ?>
                                            <div class="container mt-3">
                                                <div class="row g-4">
                                                    <?php foreach ($images as $index => $f):
                                                        $link = htmlspecialchars($f['path']);
                                                        $name = htmlspecialchars($f['name']);
                                                        ?>
                                                        <div class="col-12 col-sm-6 col-md-4">
                                                            <div class="pdf-preview-box text-center" data-bs-toggle="modal"
                                                                data-bs-target="#imageModal<?php echo $index; ?>"
                                                                style="cursor: zoom-in; overflow: hidden; border-radius: 10px; height: 180px; position: relative;">
                                                                <img src="<?php echo $link; ?>" alt="Preview - <?php echo $name; ?>"
                                                                    style="width: 100%; height: 100%; object-fit: cover; object-position: center; transition: transform 0.3s ease;">
                                                            </div>
                                                        </div>

                                                        <!-- Modal for each image -->
                                                        <div class="modal fade" id="imageModal<?php echo $index; ?>" tabindex="-1"
                                                            aria-hidden="true">
                                                            <div class="modal-dialog modal-fullscreen">
                                                                <div
                                                                    class="modal-content bg-black border-0 d-flex justify-content-center align-items-center position-relative">
                                                                    <button type="button"
                                                                        class="btn-close btn-close-white position-absolute top-0 end-0 m-4"
                                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                                    <div class="modal-img-wrapper p-0 m-0 w-100 h-100">
                                                                        <img class="modal-zoomable" src="<?php echo $link; ?>"
                                                                            alt="Full - <?php echo $name; ?>">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <!-- 📄 PDF SECTION -->
                                        <?php if (!empty($pdfs)): ?>
                                            <?php foreach ($pdfs as $f): ?>
                                                <div class="mt-4 mb-4">
                                                    <iframe src="<?php echo htmlspecialchars($f['path']); ?>" width="100%"
                                                        height="600px" style="border:none; border-radius:10px;"></iframe>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>

                                        <!-- 🔗 OTHER FILES SECTION -->
                                        <?php if (!empty($others)): ?>
                                            <p class="text-warning mt-3">Other file types:</p>
                                            <ul class="mt-2">
                                                <?php foreach ($others as $f): ?>
                                                    <li><a href="<?php echo htmlspecialchars($f['path']); ?>" target="_blank"
                                                            rel="noopener">
                                                            <?php echo htmlspecialchars($f['name']); ?></a></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php endif; ?>

                                    <?php else: ?>
                                        <p class="text-danger mt-3">No attachments found for lessonID
                                            <?php echo $lessonID; ?>.
                                        </p>
                                    <?php endif; ?>

                                </div>
                            </div>

                            <!-- Right Content -->
                            <div class="col-12 col-lg-4">
                                <div class="cardSticky position-sticky" style="top: 20px;">
                                    <div class="ms-2 me-2">
                                        <div class="d-flex align-items-center justify-content-center mt-5">
                                            <div class="text-sbold">90/100</div>
                                        </div>

                                        <div class="d-flex align-items-center justify-content-center mb-5">
                                            <div class="text-reg"><i>Grade</i></div>
                                        </div>

                                        <div class="text-center mt-5">
                                            <div class="text-sbold text-15 mb-3" style="color: var(--black);">Content
                                                Relevance
                                            </div>

                                            <div id="ratingAccordion">
                                                <!-- Excellent -->
                                                <div class="mb-2">
                                                    <button
                                                        class="btn w-100 d-flex align-items-center justify-content-center flex-column text-med text-14"
                                                        type="button" data-bs-toggle="collapse"
                                                        data-bs-target="#excellent" aria-expanded="false"
                                                        aria-controls="excellent"
                                                        style="background-color: var(--pureWhite); border-radius: 10px; border: 1px solid var(--black);">

                                                        <div
                                                            class="d-flex justify-content-between align-items-center w-100 px-3">
                                                            <span class="flex-grow-1 text-center ps-3">Excellent · 5
                                                                pts</span>
                                                            <span
                                                                class="material-symbols-rounded transition">expand_more</span>
                                                        </div>

                                                        <div class="collapse w-100 mt-2" id="excellent"
                                                            data-bs-parent="#ratingAccordion">
                                                            <p class="mb-0 px-3 pb-2 text-reg text-14">
                                                                Ideas are insightful, well-developed, and directly
                                                                address the topic.
                                                            </p>
                                                        </div>
                                                    </button>
                                                </div>

                                                <!-- Good -->
                                                <div class="mb-2">
                                                    <button
                                                        class="btn w-100 d-flex align-items-center justify-content-center flex-column text-med text-14"
                                                        type="button" data-bs-toggle="collapse" data-bs-target="#good"
                                                        aria-expanded="false" aria-controls="good"
                                                        style="background-color: var(--pureWhite); border-radius: 10px; border: 1px solid var(--black);">

                                                        <div
                                                            class="d-flex justify-content-between align-items-center w-100 px-3">
                                                            <span class="flex-grow-1 text-center ps-3">Good · 4
                                                                pts</span>
                                                            <span
                                                                class="material-symbols-rounded transition">expand_more</span>
                                                        </div>

                                                        <div class="collapse w-100 mt-2" id="good"
                                                            data-bs-parent="#ratingAccordion">
                                                            <p class="mb-0 px-3 pb-2 text-reg text-14;">
                                                                Ideas are clear and relevant but may need further
                                                                development.
                                                            </p>
                                                        </div>
                                                    </button>
                                                </div>

                                                <!-- Fair -->
                                                <div class="mb-2">
                                                    <button
                                                        class="btn w-100 d-flex align-items-center justify-content-center flex-column text-med text-14"
                                                        type="button" data-bs-toggle="collapse" data-bs-target="#fair"
                                                        aria-expanded="false" aria-controls="fair"
                                                        style="background-color: var(--pureWhite); border-radius: 10px; border: 1px solid var(--black);">

                                                        <div
                                                            class="d-flex justify-content-between align-items-center w-100 px-3">
                                                            <span class="flex-grow-1 text-center ps-3">Fair · 3
                                                                pts</span>
                                                            <span
                                                                class="material-symbols-rounded transition">expand_more</span>
                                                        </div>

                                                        <div class="collapse w-100 mt-2" id="fair"
                                                            data-bs-parent="#ratingAccordion">
                                                            <p class="mb-0 px-3 pb-2 text-reg text-14">
                                                                Ideas are limited or partially address the topic.
                                                            </p>
                                                        </div>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="text-center mt-5">
                                            <div class="text-sbold text-15 mb-3" style="color: var(--black);">Optional
                                                Actions</div>
                                            <div class="d-flex flex-column align-items-center gap-2 mb-5">
                                                <!-- ADD AWARD BADGE BUTTON (opens modal) -->
                                                <button
                                                    class="btn custom-btn d-flex align-items-center justify-content-center"
                                                    data-bs-toggle="modal" data-bs-target="#awardBadgeModal">
                                                    <span class="material-symbols-rounded me-2">trophy</span>
                                                    Award badge
                                                </button>
                                                <!-- ADD FEEDBACK BUTTON (opens modal) -->
                                                <button class="btn custom-btn d-flex align-items-center justify-content-center" data-bs-toggle="modal"
                                                    data-bs-target="#feedbackModal">
                                                    <i class="material-symbols-rounded me-2">comment</i> Add feedback
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> <!-- row -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- bootstrap bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // THUMBNAIL hover zoom
        document.querySelectorAll('.pdf-preview-box img').forEach(img => {
            img.addEventListener('mouseenter', () => img.style.transform = 'scale(1.05)');
            img.addEventListener('mouseleave', () => img.style.transform = 'scale(1)');
        });

        // Zoom & scroll zoom for modal images
        document.querySelectorAll('.modal img').forEach(img => {
            let zoomLevel = 1;
            let isDragging = false;
            let startX, startY, scrollLeft, scrollTop;

            // Toggle zoom on click
            img.addEventListener('click', () => {
                if (zoomLevel === 1) {
                    zoomLevel = 2;
                    img.style.cursor = 'zoom-out';
                } else {
                    zoomLevel = 1;
                    img.style.cursor = 'zoom-in';
                }
                img.style.transform = `scale(${zoomLevel})`;
            });

            // Scroll to zoom in/out
            img.addEventListener('wheel', (e) => {
                e.preventDefault();
                zoomLevel += e.deltaY * -0.001; // scroll up = zoom in
                zoomLevel = Math.min(Math.max(1, zoomLevel), 5); // limit zoom (1x–5x)
                img.style.transform = `scale(${zoomLevel})`;
            });

            // Enable dragging when zoomed in
            img.addEventListener('mousedown', (e) => {
                if (zoomLevel > 1) {
                    isDragging = true;
                    startX = e.pageX - img.offsetLeft;
                    startY = e.pageY - img.offsetTop;
                    img.style.cursor = 'grabbing';
                    e.preventDefault();
                }
            });

            img.addEventListener('mouseup', () => {
                isDragging = false;
                img.style.cursor = zoomLevel > 1 ? 'grab' : 'zoom-in';
            });

            img.addEventListener('mouseleave', () => {
                isDragging = false;
            });

            img.addEventListener('mousemove', (e) => {
                if (!isDragging || zoomLevel === 1) return;
                e.preventDefault();
                const x = e.pageX - startX;
                const y = e.pageY - startY;
                img.style.transformOrigin = `${(e.offsetX / img.width) * 100}% ${(e.offsetY / img.height) * 100}%`;
                img.style.transform = `scale(${zoomLevel}) translate(${x / 100}px, ${y / 100}px)`;
            });
        });

        // Modal image zoom/scroll/drag logic
        (function () {
            // initialize for each modal image present
            const modalImages = document.querySelectorAll('.modal-zoomable');

            modalImages.forEach(img => {
                let zoom = 1;
                let isDragging = false;
                let lastClientX = 0;
                let lastClientY = 0;
                let translateX = 0;
                let translateY = 0;
                let originX = 50;
                let originY = 50;

                // helper: apply transform
                function applyTransform() {
                    img.style.transformOrigin = `${originX}% ${originY}%`;
                    img.style.transform = `translate(${translateX}px, ${translateY}px) scale(${zoom})`;
                }

                // reset function used when modal closes
                function reset() {
                    zoom = 1;
                    isDragging = false;
                    translateX = 0;
                    translateY = 0;
                    originX = 50;
                    originY = 50;
                    img.style.cursor = 'zoom-in';
                    img.style.transition = 'transform 0.12s ease';
                    applyTransform();
                }

                // click toggles between 1x and 2x
                img.addEventListener('click', (e) => {
                    // don't toggle if user is dragging
                    if (isDragging) return;
                    if (zoom === 1) {
                        // set origin based on click
                        const rect = img.getBoundingClientRect();
                        const offsetX = e.clientX - rect.left;
                        const offsetY = e.clientY - rect.top;
                        originX = (offsetX / rect.width) * 100;
                        originY = (offsetY / rect.height) * 100;
                        zoom = 2;
                        img.style.cursor = 'zoom-out';
                    } else {
                        zoom = 1;
                        translateX = 0;
                        translateY = 0;
                        img.style.cursor = 'zoom-in';
                    }
                    applyTransform();
                });

                // wheel for zoom in/out
                img.addEventListener('wheel', (e) => {
                    e.preventDefault();
                    // compute pointer position to keep focal point
                    const rect = img.getBoundingClientRect();
                    const pointerX = e.clientX - rect.left;
                    const pointerY = e.clientY - rect.top;
                    const prevZoom = zoom;
                    zoom += -e.deltaY * 0.0015; // adjust sensitivity
                    zoom = Math.min(Math.max(1, zoom), 5);

                    // recompute translate to keep pointer stable
                    if (zoom !== prevZoom) {
                        const relX = (pointerX / rect.width) * 2 - 1; // relative [-1,1]
                        const relY = (pointerY / rect.height) * 2 - 1;
                        // adjust translate proportionally
                        translateX += -relX * (zoom - prevZoom) * 50;
                        translateY += -relY * (zoom - prevZoom) * 50;
                        img.style.cursor = zoom > 1 ? 'grab' : 'zoom-in';
                        applyTransform();
                    }
                }, { passive: false });

                // dragging
                img.addEventListener('mousedown', (e) => {
                    if (zoom <= 1) return;
                    isDragging = true;
                    lastClientX = e.clientX;
                    lastClientY = e.clientY;
                    img.style.cursor = 'grabbing';
                    img.style.transition = 'none';
                    e.preventDefault();
                });
                window.addEventListener('mousemove', (e) => {
                    if (!isDragging) return;
                    const dx = e.clientX - lastClientX;
                    const dy = e.clientY - lastClientY;
                    translateX += dx;
                    translateY += dy;
                    lastClientX = e.clientX;
                    lastClientY = e.clientY;
                    applyTransform();
                });
                window.addEventListener('mouseup', () => {
                    if (!isDragging) return;
                    isDragging = false;
                    img.style.cursor = zoom > 1 ? 'grab' : 'zoom-in';
                    img.style.transition = 'transform 0.12s ease';
                });

                // Reset zoom/position when modal closes
                // find the closest modal ancestor and hook into bootstrap's hidden event
                let modalEl = img.closest('.modal');
                if (modalEl) {
                    modalEl.addEventListener('hidden.bs.modal', () => {
                        reset();
                    });
                    modalEl.addEventListener('shown.bs.modal', () => {
                        // ensure starting values
                        reset();
                    });
                }
            });
        })();

        document.addEventListener('DOMContentLoaded', function () {
            const buttons = document.querySelectorAll('[data-bs-toggle="collapse"]');

            buttons.forEach(button => {
                const target = button.getAttribute('data-bs-target');
                const icon = button.querySelector('.material-symbols-rounded');
                const collapse = document.querySelector(target);

                if (collapse && icon) {
                    collapse.addEventListener('show.bs.collapse', () => {
                        // Reset all others
                        buttons.forEach(btn => btn.style.backgroundColor = 'var(--pureWhite)');
                        document.querySelectorAll('.material-symbols-rounded').forEach(ic => ic.style.transform = 'rotate(0deg)');

                        // Highlight this one
                        icon.style.transform = 'rotate(180deg)';
                        icon.style.transition = 'transform 0.3s';
                        button.style.backgroundColor = 'var(--primaryColor)';
                    });

                    collapse.addEventListener('hide.bs.collapse', () => {
                        icon.style.transform = 'rotate(0deg)';
                        button.style.backgroundColor = 'var(--pureWhite)';
                    });
                }
            });
        });
    </script>
</body>

</html>