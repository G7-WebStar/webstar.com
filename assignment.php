<?php
$activePage = 'assignment';

include('shared/assets/database/connect.php');
include("shared/assets/processes/session-process.php");

$assignmentID = intval($_GET['assignmentID'] ?? 0);

// --- Fetch assessment and course info ---
$assessmentQuery = "
    SELECT assessments.assessmentID, courses.courseID
    FROM assignments
    INNER JOIN assessments ON assignments.assessmentID = assessments.assessmentID
    INNER JOIN courses ON assessments.courseID = courses.courseID
    WHERE assignments.assignmentID = '$assignmentID'
    LIMIT 1
";

$assessmentResult = executeQuery($assessmentQuery);
$assessmentRow = mysqli_fetch_assoc($assessmentResult);
$assessmentID = $assessmentRow['assessmentID'];
$courseID = $assessmentRow['courseID'];

// --- Check if user already submitted ---
$submissionQuery = "
    SELECT submissionID, isSubmitted 
    FROM submissions 
    WHERE assessmentID = '$assessmentID' AND userID = '$userID'
    ORDER BY submittedAt DESC
    LIMIT 1
";
$submissionResult = executeQuery($submissionQuery);

if ($submissionResult && mysqli_num_rows($submissionResult) > 0) {
    $submissionRow = mysqli_fetch_assoc($submissionResult);
    $isSubmitted = $submissionRow['isSubmitted'];
    $submissionID = $submissionRow['submissionID'];
} else {
    $isSubmitted = 0;
    $submissionID = null;
}

// --- Handle multiple file uploads (Turn In) ---
if (!empty($_FILES['fileAttachment']['name'][0])) {
    $targetDir = "shared/assets/img/files/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    // Always check if the user already has a submission for this assessment
    $submissionCheck = executeQuery("
        SELECT submissionID FROM submissions 
        WHERE assessmentID = '$assessmentID' AND userID = '$userID'
        LIMIT 1
    ");

    if ($submissionCheck && mysqli_num_rows($submissionCheck) > 0) {
        $submissionRow = mysqli_fetch_assoc($submissionCheck);
        $submissionID = $submissionRow['submissionID'];
    } else {
        executeQuery("
        INSERT INTO submissions (assessmentID, userID, scoreID, submittedAt, isSubmitted)
        VALUES ('$assessmentID', '$userID', NULL, NOW(), 1)
    ");
        $submissionID = mysqli_insert_id($conn);
    }

    // Always update timestamp and mark submitted
    executeQuery("
        UPDATE submissions 
        SET submittedAt = NOW(), isSubmitted = 1 
        WHERE submissionID = '$submissionID'
    ");


    // Loop through all uploaded files
    foreach ($_FILES['fileAttachment']['name'] as $key => $fileName) {
        $fileTmp = $_FILES['fileAttachment']['tmp_name'][$key];
        $fileSize = $_FILES['fileAttachment']['size'][$key];
        $fileError = $_FILES['fileAttachment']['error'][$key];

        if ($fileError !== 0)
            continue;
        if ($fileSize > 25 * 1024 * 1024) {
            echo "<script>alert('{$fileName} exceeds 25MB limit and was skipped.');</script>";
            continue;
        }

        $fileName = basename($fileName);
        $targetFilePath = $targetDir . $fileName;

        if (move_uploaded_file($fileTmp, $targetFilePath)) {
            $insertFile = "
                INSERT INTO files (courseID, userID, submissionID, fileAttachment)
                VALUES ('$courseID', '$userID', '$submissionID', '$fileName')
            ";
            executeQuery($insertFile);
        }
    }

    $isSubmitted = 1;
    $showSubmittedModal = true;
}

// --- Handle Turn In without new file uploads ---
if (isset($_POST['assessmentID']) && empty($_FILES['fileAttachment']['name'][0])) {
    if ($submissionID) {
        executeQuery("
            UPDATE submissions 
            SET submittedAt = NOW(), isSubmitted = 1 
            WHERE submissionID = '$submissionID' AND userID = '$userID'
        ");
        $isSubmitted = 1;
    } else {
        executeQuery("
            INSERT INTO submissions (assessmentID, userID, scoreID, submittedAt, isSubmitted)
            VALUES ('$assessmentID', '$userID', NULL, NOW(), 1)
        ");
        $submissionID = mysqli_insert_id($conn);
        $isSubmitted = 1;
    }
}

// --- Handle Unsubmit ---
if (isset($_POST['unsubmit'])) {
    if ($submissionID) {
        $updateUnsubmit = "
            UPDATE submissions 
            SET isSubmitted = 0 
            WHERE submissionID = '$submissionID' AND userID = '$userID'
        ";
        executeQuery($updateUnsubmit);
        $isSubmitted = 0;
    }
}

// --- Handle deleted files on re-submit ---
if (isset($_POST['deletedFiles']) && !empty($_POST['deletedFiles'])) {
    $deletedFiles = json_decode($_POST['deletedFiles'], true);

    if (is_array($deletedFiles)) {
        foreach ($deletedFiles as $fileToDelete) {
            $fileToDelete = mysqli_real_escape_string($conn, $fileToDelete);

            // Delete from DB
            $deleteQuery = "
                DELETE FROM files 
                WHERE submissionID = '$submissionID' 
                  AND userID = '$userID' 
                  AND fileAttachment = '$fileToDelete'
            ";
            executeQuery($deleteQuery);

            // Delete from folder
            $filePath = "shared/assets/img/files/" . $fileToDelete;
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
    }
}

// --- Fetch existing uploaded files (to show in sticky card) ---
$files = [];
if ($submissionID) {
    $filesQuery = "
        SELECT fileAttachment 
        FROM files 
        WHERE submissionID = '$submissionID'
    ";
    $filesResult = executeQuery($filesQuery);
    while ($row = mysqli_fetch_assoc($filesResult)) {
        $files[] = $row['fileAttachment'];
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webstar | Assignment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="shared/assets/css/assignment.css">
    <link rel="stylesheet" href="shared/assets/css/sidebar-and-container-styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="shared/assets/img/webstar-icon.png">

    <!-- Material Design Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp" />
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

</head>

<body>
    <div class="container-fluid min-vh-100 d-flex justify-content-center align-items-center p-0 p-md-3"
        style="background-color: var(--black);">

        <div class="row w-100">

            <!-- Sidebar (only shows on mobile) -->
            <?php include 'shared/components/sidebar-for-mobile.php'; ?>

            <!-- Sidebar Column (fixed on desktop) -->
            <?php include 'shared/components/sidebar-for-desktop.php'; ?>

            <!-- Main Container Column-->
            <div class="col main-container m-0 p-0 mx-0 mx-md-2 p-0 p-md-4 overflow-y-auto">
                <div class="card border-0 px-3 pt-3 m-0 h-100 w-100 rounded-0 shadow-none"
                    style="background-color: transparent;">

                    <!-- Navbar for mobile -->
                    <?php include 'shared/components/navbar-for-mobile.php'; ?>

                    <div class="container-fluid py-3 overflow-y-auto row-padding-top">
                    </div>

                    <div class="col-12 col-lg-4">
                        <!-- Sticky Card -->
                        <div class="cardSticky position-sticky" id="stickyCard" style="top: 20px;">
                            <div class="p-2">
                                <!-- My Work Section -->
                                <div class="myWorkContainer"
                                    style="<?= !empty($files) ? 'display:block;' : 'display:none;' ?>">

                                    <div class="text-sbold text-16 mb-2" id="myWorkLabel"
                                        style="<?= empty($files) ? 'display:none;' : 'display:block;' ?>">
                                        My work
                                    </div>

                                    <div class="uploadedFiles">
                                        <?php if (!empty($files)): ?>
                                            <?php foreach ($files as $file): ?>
                                                <div
                                                    class="cardFile text-sbold text-16 my-2 d-flex align-items-center justify-content-between">
                                                    <div class="d-flex align-items-center">
                                                        <span class="material-symbols-outlined p-2 pe-2"
                                                            style="font-variation-settings:'FILL' 1;">draft</span>
                                                        <a href="shared/assets/img/files/<?= htmlspecialchars($file) ?>"
                                                            target="_blank" class="ms-2 text-decoration-none text-dark">
                                                            <?= htmlspecialchars($file) ?>
                                                        </a>
                                                    </div>

                                                    <?php if ($isSubmitted == 0): ?>
                                                        <button type="button"
                                                            class="border-0 bg-transparent mt-2 remove-existing-file"
                                                            data-filename="<?= htmlspecialchars($file) ?>" aria-label="Remove">
                                                            <span class="material-symbols-outlined">close</span>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <!-- Status Section -->
                                <div class="text-sbold text-16">Status</div>
                                <ul class="timeline list-unstyled small my-3">
                                    <li class="timeline-item">
                                        <div class="timeline-circle bg-dark"></div>
                                        <div class="timeline-content">
                                            <div class="text-reg text-16">Assignment is ready to work on.</div>
                                            <div class="text-reg text-12">Sep 9, 2024, 10:00PM</div>
                                        </div>
                                    </li>
                                    <li class="timeline-item">
                                        <div class="timeline-circle bg-dark"></div>
                                        <div class="timeline-content">
                                            <div class="text-reg text-16">Your assignment has been submitted.
                                            </div>
                                            <div class="text-reg text-12">Sep 9, 2024, 10:00PM</div>
                                        </div>
                                    </li>
                                    <!-- Already Graded -->
                                    <li class="timeline-item">
                                        <div class="timeline-circle big" style="background-color: var(--primaryColor);">
                                        </div>
                                        <div class="timeline-content">
                                            <div class="text-reg text-16">Your assignment has been graded.</div>
                                            <div class="text-reg text-12">Sep 9, 2024, 10:00PM</div>
                                        </div>
                                    </li>
                                </ul>

                                <!-- Upload / Link Buttons -->
                                <div class="mt-0 d-flex flex-column align-items-center">
                                    <input type="file" name="fileAttachment[]" class="d-none" id="fileUpload"
                                        accept=".pdf, .jpg, .jpeg, .png" multiple>

                                    <div class="d-flex gap-2 mb-3">
                                        <?php if ($isSubmitted == 0): ?>
                                            <button type="button"
                                                class="btn btn-sm px-3 py-1 rounded-pill text-reg text-md-14"
                                                style="border: 1px solid var(--black);"
                                                onclick="document.getElementById('fileUpload').click();">
                                                <div class="d-flex align-items-center gap-1">
                                                    <span class="material-symbols-outlined"
                                                        style="font-size:20px">upload</span>
                                                    <span>File</span>
                                                </div>
                                            </button>

                                            <button type="button"
                                                class="btn btn-sm px-3 py-1 rounded-pill text-reg text-md-14"
                                                style="border: 1px solid var(--black);" data-bs-toggle="modal"
                                                data-bs-target="#linkModal">
                                                <div class="d-flex align-items-center gap-1">
                                                    <span class="material-symbols-rounded"
                                                        style="font-size:20px">link</span>
                                                    <span>Link</span>
                                                </div>
                                            </button>
                                        <?php endif; ?>
                                    </div>

                                    <?php if ($isSubmitted == 0): ?>
                                        <button type="button" class="btn px-4 text-reg text-md-14 rounded-4 w-75"
                                            style="background-color: var(--primaryColor);" data-bs-toggle="modal"
                                            data-bs-target="#turnInModal">
                                            Turn In
                                        </button>

                                    <?php elseif ($isSubmitted == 1): ?>
                                        <!-- SHOW UNSUBMIT BUTTON WHEN SUBMITTED -->
                                        <button type="button" class="btn btn-sm px-4 py-2 rounded-pill text-reg text-md-14"
                                            style="background-color: var(--primaryColor); margin-top: -25px;"
                                            data-bs-toggle="modal" data-bs-target="#unsubmitModal">
                                            Unsubmit
                                        </button>
                                    <?php endif; ?>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <!-- Button Completely Outside Sticky Card -->
                <div class="mt-3 d-flex justify-content-lg-end justify-content-center w-100">
                    <button type="button"
                        class="btn me-5 text-reg text-12 rounded-4 w-lg-25 w-sm-100 ms-5 d-flex justify-content-center align-items-center"
                        data-bs-toggle="modal" data-bs-target="#guidelinesModal">
                        <span class="material-symbols-outlined me-1"
                            style="font-variation-settings:'FILL' 1;">info</span>
                        View attachment guidelines
                    </button>
                </div>

            </div>
        </div>
    </div>

    <!-- Turn In Modal -->
    <div class="modal fade" id="turnInModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered py-4" style="max-width: 500px;">
            <div class="modal-content">

                <div class="modal-header border-bottom">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <p class="text-bold text-20 align-items-center text-center">Turn in this task?</p>
                    <p class="text-reg text-14 align-items-center text-center justify-content-center">You can still edit
                        it before the deadline — but be careful! Unsubmitting will cost you webstars.</p>
                </div>

                <form id="turnInForm" action="" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="assessmentID" value="<?= $assessmentID ?>">
                    <input type="file" name="fileAttachment[]" id="fileUploadHidden" class="d-none" multiple>
                    <input type="hidden" name="deletedFiles" id="deletedFiles">

                    <div class="modal-footer border-top">
                        <button type="submit" class="btn rounded-5 px-4 text-sbold text-14 me-1"
                            style="background-color: var(--primaryColor); border: 1px solid var(--black);">
                            Submit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Submitted Modal -->
    <div class="modal fade" id="submittedModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered py-4" style="max-width: 500px;">
            <div class="modal-content">

                <div class="modal-header border-bottom">
                    <div class="modal-title text-sbold text-20 ms-3" id="submittedModalLabel">Well Done!
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body px-5 text-center">
                    <!-- Large Image -->
                    <img src="shared/assets/img/wellDone.png" alt="Illustration" class="img-fluid mb-3"
                        style="max-height: 200px; margin-top: -30px;">

                    <!-- Text Below -->
                    <p class="text-med text-12 mb-2 px-5" style="margin-top: -30px;">Your task has been successfully
                        submitted. Keep up the great work and
                        watch out for your instructor’s feedback!</p>

                    <div class="d-flex align-items-center text-center justify-content-center mb-1">
                        <img src="shared/assets/img/xp.png" alt="Image 2" class="" style="width: 20px; height: 20px;">
                        <p class="text-sbold text-14 mb-0">
                            +150 XPs <span class="text-12">+20 Bonus XPs</span>
                        </p>
                    </div>
                    <div class="d-flex align-items-center text-center justify-content-center mb-3">
                        <img src="shared/assets/img/webstar.png" alt="Image 2" class=""
                            style="width: 20px; height: 20px;">
                        <p class="text-sbold text-14 mb-0">
                            +50 Webstars <span class="text-12">+20 Bonus XPs</span>
                        </p>
                    </div>
                </div>

                <div class="modal-footer border-top py-4">
                </div>
            </div>
        </div>
    </div>

    <!-- Unsubmit Modal -->
    <div class="modal fade" id="unsubmitModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered py-4" style="max-width: 500px;">
            <div class="modal-content">

                <div class="modal-header border-bottom">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <p class="text-bold text-20 text-center">Unsubmit this task?</p>
                    <p class="text-reg text-14 text-center">
                        If you unsubmit, your work will return to pending status.<br>
                        Unsubmitting will also cost you <b>50 Webstars</b>.
                    </p>
                    <p class="text-reg text-14 text-center">Are you sure you want to unsubmit this task?</p>
                </div>

                <form id="unsubmitForm" action="" method="POST">
                    <div class="modal-footer border-top d-flex justify-content-center gap-2">
                        <button type="submit" name="unsubmit" class="btn rounded-5 px-4 text-sbold text-14 me-1"
                            style="background-color: var(--primaryColor); border: 1px solid var(--black);">
                            Unsubmit
                        </button>

                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const allSelectedFiles = new DataTransfer();
            const buttons = document.querySelectorAll('[data-bs-toggle="collapse"]');
            const fileUpload = document.getElementById('fileUpload');
            const myWorkContainer = document.querySelector('.myWorkContainer');
            const workName = document.getElementById('workName');

            const uploadBtn = document.querySelector('[onclick="document.getElementById(\'fileUpload\').click();"]');
            const linkBtn = document.querySelector('[data-bs-target="#linkModal"]');
            const turnInBtn = document.querySelector('[data-bs-target="#turnInModal"]');
            let unsubmitBtn = document.querySelector('[data-bs-target="#unsubmitModal"]');

            // --- Collapse toggle logic ---
            buttons.forEach(button => {
                const target = button.getAttribute('data-bs-target');
                const icon = button.querySelector('.material-symbols-rounded');
                const collapse = document.querySelector(target);

                if (collapse && icon) {
                    collapse.addEventListener('show.bs.collapse', () => {
                        buttons.forEach(btn => btn.style.backgroundColor = 'var(--pureWhite)');
                        document.querySelectorAll('.material-symbols-rounded')
                            .forEach(ic => ic.style.transform = 'rotate(0deg)');
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

            // --- My Work visibility + multiple file handling ---
            if (fileUpload && myWorkContainer) {
                const MAX_TOTAL_SIZE = 25 * 1024 * 1024; // 25 MB
                const workList = document.createElement('div');
                workList.className = 'uploadedFiles';
                myWorkContainer.appendChild(workList);

                fileUpload.addEventListener('change', function () {
                    const newFiles = Array.from(this.files);
                    newFiles.forEach(file => {
                        allSelectedFiles.items.add(file); // keep adding to allSelectedFiles
                    });

                    // Reset file input to allow re-selecting same file names later
                    this.value = '';

                    myWorkContainer.style.display = 'block';
                    document.getElementById('myWorkLabel').style.display = 'block';

                    workList.innerHTML = ''; // clear UI before re-rendering
                    Array.from(allSelectedFiles.files).forEach(file => {
                        const card = document.createElement('div');
                        card.className = 'cardFile text-sbold text-16 my-2 d-flex align-items-center justify-content-between';
                        card.dataset.size = file.size;

                        const info = document.createElement('div');
                        info.className = 'd-flex align-items-center';
                        const icon = document.createElement('span');
                        icon.className = 'material-symbols-outlined p-2 pe-2';
                        icon.style.fontVariationSettings = "'FILL' 1";
                        icon.textContent = 'draft';

                        const name = document.createElement('span');
                        name.textContent = file.name;
                        name.className = 'ms-2';
                        info.appendChild(icon);
                        info.appendChild(name);

                        const removeBtn = document.createElement('button');
                        removeBtn.type = 'button';
                        removeBtn.className = 'border-0 bg-transparent mt-2';
                        removeBtn.innerHTML = '<span class="material-symbols-outlined">close</span>';
                        removeBtn.addEventListener('click', function () {
                            // remove from DataTransfer too
                            for (let i = 0; i < allSelectedFiles.items.length; i++) {
                                if (allSelectedFiles.items[i].getAsFile().name === file.name) {
                                    allSelectedFiles.items.remove(i);
                                    break;
                                }
                            }
                            card.remove();
                            if (workList.children.length === 0) {
                                myWorkContainer.style.display = 'none';
                            }
                        });

                        card.appendChild(info);
                        card.appendChild(removeBtn);
                        workList.appendChild(card);
                    });
                });
                // Hide remove button if submission is done
                if (<?= $isSubmitted ?> === 1) {
                    removeBtn.style.display = 'none';
                }
            }

            // --- Adjust sticky margin ---
            function updateStickyMargin() {
                const card = document.getElementById('stickyCard');
                if (card) {
                    card.style.marginLeft = (window.innerWidth >= 992) ? '-30px' : '0';
                }
            }
            updateStickyMargin();
            window.addEventListener('resize', updateStickyMargin);

            // --- Sync file input with hidden modal input ---
            const fileUploadHidden = document.getElementById('fileUploadHidden');
            const turnInModal = document.getElementById('turnInModal');
            if (turnInModal && fileUpload && fileUploadHidden) {
                turnInModal.addEventListener('show.bs.modal', () => {
                    fileUploadHidden.files = allSelectedFiles.files;
                });
            }

            const filesToDelete = [];

            document.querySelectorAll('.remove-existing-file').forEach(btn => {
                btn.addEventListener('click', function () {
                    const fileName = this.dataset.filename;
                    filesToDelete.push(fileName);

                    // Remove file visually from UI
                    this.closest('.cardFile').remove();
                });
            });

            const deletedFilesInput = document.getElementById('deletedFiles');

            document.querySelectorAll('.remove-existing-file').forEach(btn => {
                btn.addEventListener('click', function () {
                    const fileName = this.dataset.filename;
                    filesToDelete.push(fileName);

                    // Update hidden input (for form submit)
                    deletedFilesInput.value = JSON.stringify(filesToDelete);

                    // Remove file visually from UI
                    this.closest('.cardFile').remove();
                });
            });

            // --- Turn In Form ---
            const turnInForm = document.getElementById('turnInForm');
            if (turnInForm) {
                turnInForm.addEventListener('submit', function (e) {
                    e.preventDefault(); // prevent default form submission

                    const formData = new FormData(turnInForm);

                    // Submit via fetch to server
                    fetch(turnInForm.action || '', {
                        method: 'POST',
                        body: formData
                    })
                        .then(response => response.text()) // or response.json() if server returns JSON
                        .then(data => {
                            // Hide Turn In Modal
                            const turnInModalInstance = bootstrap.Modal.getInstance(turnInModal);
                            if (turnInModalInstance) turnInModalInstance.hide();

                            // Show Submitted Modal
                            const submittedModalEl = document.getElementById('submittedModal');
                            const submittedModal = new bootstrap.Modal(submittedModalEl);
                            submittedModal.show();

                            // Hide original buttons
                            if (uploadBtn) uploadBtn.style.display = 'none';
                            if (linkBtn) linkBtn.style.display = 'none';
                            if (turnInBtn) turnInBtn.style.display = 'none';

                            // Dynamically create unsubmit button if it doesn't exist
                            if (!unsubmitBtn) {
                                const stickyDiv = document.querySelector('.mt-0.d-flex.flex-column.align-items-center');
                                unsubmitBtn = document.createElement('button');
                                unsubmitBtn.type = 'button';
                                unsubmitBtn.className = 'btn btn-sm px-4 py-2 rounded-pill text-reg text-md-14';
                                unsubmitBtn.style.backgroundColor = 'var(--primaryColor)';
                                unsubmitBtn.style.marginTop = '-25px';
                                unsubmitBtn.dataset.bsToggle = 'modal';
                                unsubmitBtn.dataset.bsTarget = '#unsubmitModal';
                                unsubmitBtn.textContent = 'Unsubmit';
                                stickyDiv.appendChild(unsubmitBtn);
                            }
                            unsubmitBtn.style.display = 'block';
                        })
                    // Hide remove buttons when submitted
                    document.querySelectorAll('.remove-existing-file').forEach(btn => {
                        btn.style.display = 'none';
                    });
                });
            }

            // --- Unsubmit Modal Submit Logic ---
            const unsubmitForm = document.getElementById('unsubmitForm');
            if (unsubmitForm) {
                unsubmitForm.addEventListener('submit', function () {
                    setTimeout(() => {
                        var unsubmitModalEl = document.getElementById('unsubmitModal');
                        var unsubmitModal = bootstrap.Modal.getInstance(unsubmitModalEl);
                        if (unsubmitModal) unsubmitModal.hide();
                    }, 500);
                });
            }
        });

        // --- Remove My Work (hide again) ---
        function removeMyWork(button) {
            const container = button.closest('.myWorkContainer');
            const fileUpload = document.getElementById('fileUpload');
            const workName = document.getElementById('workName');
            if (container) container.style.display = 'none';
            if (fileUpload) fileUpload.value = '';
            if (workName) workName.textContent = 'Submission';
        }

        // --- Add link handler ---
        function addLinkWork(linkText) {
            const myWorkContainer = document.querySelector('.myWorkContainer');
            const workName = document.getElementById('workName');
            if (myWorkContainer && workName) {
                workName.textContent = linkText;
                myWorkContainer.style.display = 'block';
            }
        }
    </script>
</body>

</html>