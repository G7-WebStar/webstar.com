<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteLessonBtn'])) {
    $deleteLessonID = intval($_POST['deleteLessonID']);

    if ($deleteLessonID > 0) {
        // Delete files
        $deleteFilesQuery = "DELETE FROM files WHERE lessonID = '$deleteLessonID'";
        executeQuery($deleteFilesQuery);

        // Delete lesson
        $deleteLessonQuery = "DELETE FROM lessons WHERE lessonID = '$deleteLessonID'";
        executeQuery($deleteLessonQuery);

        // Set session for toast
        $_SESSION['success'] = "Lesson deleted successfully!";

        // Signal for JS reload
        echo "<script>window.lessonDeleted = true;</script>";
    }
}




// LESSON SORTING BACKEND (NO UNREAD — only createdAt)
$sortLesson = $_POST['sortLesson'] ?? 'Newest';

switch ($sortLesson) {
    case 'Oldest':
        $lessonOrderBy = "createdAt ASC";
        break;

    default: // Newest
        $lessonOrderBy = "createdAt DESC";
        break;
}


$lessonQuery = "SELECT * FROM lessons WHERE courseID = '$courseID' ORDER BY $lessonOrderBy";
$lessonResult = executeQuery($lessonQuery);

// Check if there is at least one lesson with a valid title
$hasLessons = false;
while ($lesson = mysqli_fetch_assoc($lessonResult)) {
    if (!empty($lesson['lessonTitle'])) {
        $hasLessons = true;
        break;
    }
}

// Reset pointer to start of result set
mysqli_data_seek($lessonResult, 0);
?>

<?php if (isset($_SESSION['success'])): ?>
    <div class="position-absolute top-0 start-50 translate-middle-x pt-5 pt-md-1 d-flex flex-column align-items-center"
        style="z-index:1100; pointer-events:none;">
        <div class="alert alert-success mb-2 shadow-lg text-med text-12
                d-flex align-items-center justify-content-center gap-2 px-3 py-2" role="alert"
            style="border-radius:8px; display:flex; align-items:center; gap:8px; padding:0.5rem 0.75rem; text-align:center; background-color:#d1e7dd; color:#0f5132;">
            <i class="bi bi-check-circle-fill fs-6" style="color: var(--black);"></i>
            <span style="color: var(--black);"><?= $_SESSION['success']; ?></span>
        </div>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>


<?php if ($hasLessons): ?>

    <!-- Sort By Dropdown (only shown if there are lessons) -->
    <div class="d-flex align-items-center flex-nowrap mb-2">
        <div class="d-flex align-items-center flex-nowrap">
            <span class="dropdown-label me-2 text-reg text-12">Sort by</span>
            <form method="POST">
                <input type="hidden" name="activeTab" value="lessons">
                <select class="select-modern text-reg text-12" name="sortLesson" onchange="this.form.submit()">
                    <option value="Newest" <?php echo ($sortLesson == 'Newest') ? 'selected' : ''; ?>>Newest</option>
                    <option value="Oldest" <?php echo ($sortLesson == 'Oldest') ? 'selected' : ''; ?>>Oldest</option>
                </select>
            </form>

        </div>
    </div>

    <!-- Lessons List -->
    <div class="d-flex flex-column flex-nowrap overflow-x-hidden">
        <?php
        while ($lesson = mysqli_fetch_assoc($lessonResult)) {
            $lessonID = $lesson['lessonID'];
            $lessonTitle = $lesson['lessonTitle'];

            if (empty($lessonTitle))
                continue; // skip lessons with empty title
    
            $fileQuery = "SELECT * FROM files WHERE lessonID = '$lessonID'";
            $fileResult = executeQuery($fileQuery);

            $attachmentsArray = [];
            $linksArray = [];

            while ($file = mysqli_fetch_assoc($fileResult)) {
                if (!empty($file['fileAttachment'])) {
                    $attachments = array_map('trim', explode(',', $file['fileAttachment']));
                    $attachmentsArray = array_merge($attachmentsArray, $attachments);
                }

                if (!empty($file['fileLink'])) {
                    $links = array_map('trim', explode(',', $file['fileLink']));
                    $linksArray = array_merge($linksArray, $links);
                }
            }

            $fileCount = count($attachmentsArray);
            $linkCount = count($linksArray);
            ?>
            <div class="row mb-0 mt-2">
                <div class="col">
                    <a href="lessons-info.php?lessonID=<?php echo $lessonID; ?>"
                        style="text-decoration: none; color: inherit; display: block;">
                        <div class="todo-card d-flex align-items-stretch px-2 py-3" data-lesson-id="<?php echo $lessonID; ?>">
                            <div class="d-flex w-100 align-items-center justify-content-between">
                                <div class="d-flex align-items-center" style="flex-grow:1;">
                                    <div class="mx-4 d-flex align-items-center">
                                        <span class="material-symbols-outlined" style="line-height: 1;">menu_book</span>
                                    </div>
                                    <div>
                                        <div class="text-sbold text-16" style="line-height: 1;">
                                            <?php echo htmlspecialchars($lessonTitle); ?>
                                        </div>
                                        <div class="text-reg text-12 mt-1" style="line-height: 1;">
                                            <?php echo $fileCount . " file" . ($fileCount != 1 ? "s" : "") . " · " . $linkCount . " link" . ($linkCount != 1 ? "s" : ""); ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center ms-2 position-relative">
                                    <a href="lessons-info.php?lessonID=<?php echo $lessonID; ?>" class="text-dark mx-2">
                                        <i class="fas fa-arrow-right"></i>
                                    </a>
                                    <div class="dropdown" style="position: relative;">
                                        <button class="btn btn-light btn-sm p-1 px-2 border-0 bg-transparent" type="button"
                                            id="lessonDropdown<?php echo $lessonID; ?>" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end dropdown-delete"
                                            aria-labelledby="lessonDropdown<?php echo $lessonID; ?>">
                                            <li><a class="dropdown-item text-reg text-14"
                                                    href="add-lesson.php?edit=<?php echo $lessonID; ?>">Edit</a></li>
                                            <li>
                                                <button type="button" class="dropdown-item text-reg text-14 text-danger"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#deleteLessonModal<?php echo $lessonID; ?>">
                                                    Delete
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </a>
                </div>
                <!-- Delete Modal -->
                <div class="modal" id="deleteLessonModal<?php echo $lessonID; ?>" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                                    style="transform: scale(0.8);"></button>
                            </div>
                            <div class="modal-body d-flex flex-column justify-content-center align-items-center text-center">
                                <span class="mt-4 text-bold text-22">This action cannot be undone.</span>
                                <span class="mb-4 text-reg text-14">Are you sure you want to delete this lesson?</span>
                            </div>
                            <div class="modal-footer text-sbold text-18">
                                <button type="button" class="btn rounded-pill px-4"
                                    style="background-color: var(--primaryColor); border: 1px solid var(--black);"
                                    data-bs-dismiss="modal">Cancel</button>

                                <form method="POST" class="m-0 delete-lesson-form" data-lesson-id="<?php echo $lessonID; ?>">
                                    <input type="hidden" name="deleteLessonID" value="<?php echo $lessonID; ?>">
                                    <button type="submit" name="deleteLessonBtn" class="btn rounded-pill px-4"
                                        style="background-color: rgba(248, 142, 142, 1); border: 1px solid var(--black);"
                                        data-lesson-id="<?php echo $lessonID; ?>">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        } // end while
        ?>
    </div>
    <!-- Toast Container -->
    <div id="toastContainer"
        class="position-absolute top-0 start-50 translate-middle-x pt-5 pt-md-1 d-flex flex-column align-items-center"
        style="z-index:1100; pointer-events:none;">
    </div>


<?php else: ?>
    <!-- No Lessons Placeholder -->
    <div class="empty-state text-center">
        <img src="../shared/assets/img/empty/lessons.png" alt="No Lessons" class="empty-state-img">
        <div class="empty-state-text text-reg text-14">
            No lessons have been posted yet.
        </div>
    </div>
<?php endif; ?>

<style>
    .todo-card {
        overflow: visible;
        cursor: pointer;
    }

    .dropdown-delete {
    position: fixed !important;
    inset: auto !important;
    z-index: 5000 !important;
    transform: none !important;
}

</style>

<script>
    // Navigate on card click
    document.querySelectorAll('.todo-card').forEach(card => {
        card.addEventListener('click', (e) => {
            if (!e.target.closest('.dropdown')) {
                const lessonID = card.getAttribute('data-lesson-id');
                window.location.href = `lessons-info.php?lessonID=${lessonID}`;
            }
        });
    });

    document.addEventListener('DOMContentLoaded', () => {
        // Auto fade toast
        const alertEl = document.querySelector('.alert.alert-success');
        if (alertEl) {
            setTimeout(() => {
                alertEl.style.transition = "opacity 0.5s ease-out";
                alertEl.style.opacity = 0;
                setTimeout(() => alertEl.remove(), 500);
            }, 3000);
        }

        // Hard reload after deletion and stay on lessons tab
        document.querySelectorAll('.delete-lesson-form').forEach(form => {
            form.addEventListener('submit', (e) => {
                // Allow normal POST to run first
                setTimeout(() => {
                    const url = new URL(window.location.href);
                    url.searchParams.set('activeTab', 'lessons'); // force Lessons tab
                    window.location.href = url.toString(); // reload with tab
                }, 100); // small delay to ensure PHP processed POST
            });
        });
    });

    document.querySelectorAll('[data-bs-toggle="dropdown"]').forEach(btn => {
    btn.addEventListener('show.bs.dropdown', function () {
        const menu = this.parentElement.querySelector('.dropdown-menu');
        const rect = this.getBoundingClientRect();

        menu.style.position = "fixed";
        menu.style.top = rect.bottom + "px";
        menu.style.left = (rect.right - menu.offsetWidth) + "px";
    });
});



</script>