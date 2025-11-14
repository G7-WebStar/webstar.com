<?php
// Handle Assessment Deletion
if (isset($_POST['deleteAssessmentBtn']) && isset($_POST['deleteAssessmentID']) && isset($_POST['deleteAssessmentType'])) {
    $deleteID = intval($_POST['deleteAssessmentID']);
    $deleteType = mysqli_real_escape_string($conn, $_POST['deleteAssessmentType']);
    $assessmentID = null;
    $testID = null;
    $assignmentID = null;

    // Get assessmentID based on type
    if (strtolower($deleteType) === 'task') {
        $assignmentID = $deleteID;
        $getAssessmentQuery = "SELECT assessmentID FROM assignments WHERE assignmentID = '$assignmentID'";
        $getAssessmentResult = executeQuery($getAssessmentQuery);
        if ($getAssessmentResult && mysqli_num_rows($getAssessmentResult) > 0) {
            $assessmentRow = mysqli_fetch_assoc($getAssessmentResult);
            $assessmentID = $assessmentRow['assessmentID'];
        }
    } elseif (strtolower($deleteType) === 'test') {
        $testID = $deleteID;
        $getAssessmentQuery = "SELECT assessmentID FROM tests WHERE testID = '$testID'";
        $getAssessmentResult = executeQuery($getAssessmentQuery);
        if ($getAssessmentResult && mysqli_num_rows($getAssessmentResult) > 0) {
            $assessmentRow = mysqli_fetch_assoc($getAssessmentResult);
            $assessmentID = $assessmentRow['assessmentID'];
        }
    }

    if ($assessmentID) {
        mysqli_begin_transaction($conn);

        try {
            // If it's a test, delete test-related records first
            if ($testID) {
                // 1. Delete testquestionchoices (needs testquestionID from testquestions)
                $getTestQuestionsQuery = "SELECT testquestionID FROM testquestions WHERE testID = '$testID'";
                $getTestQuestionsResult = executeQuery($getTestQuestionsQuery);
                if ($getTestQuestionsResult) {
                    while ($questionRow = mysqli_fetch_assoc($getTestQuestionsResult)) {
                        $testquestionID = $questionRow['testquestionID'];
                        executeQuery("DELETE FROM testquestionchoices WHERE testquestionID = '$testquestionID'");
                    }
                }

                // 2. Delete testquestions
                executeQuery("DELETE FROM testquestions WHERE testID = '$testID'");

                // 3. Delete testresponses
                executeQuery("DELETE FROM testresponses WHERE testID = '$testID'");

                // 4. Delete scores related to testID
                executeQuery("DELETE FROM scores WHERE testID = '$testID'");

                // 5. Delete tests
                executeQuery("DELETE FROM tests WHERE testID = '$testID'");
            }

            // If it's a task/assignment, delete assignment-related records
            if ($assignmentID) {
                // 1. Delete files
                executeQuery("DELETE FROM files WHERE assignmentID = '$assignmentID'");

                // 2. Delete submissions
                executeQuery("DELETE FROM submissions WHERE assignmentID = '$assignmentID'");

                // 3. Delete scores related to assignmentID
                executeQuery("DELETE FROM scores WHERE assignmentID = '$assignmentID'");

                // 4. Delete assignments
                executeQuery("DELETE FROM assignments WHERE assignmentID = '$assignmentID'");
            }

            // 5. Delete todo records
            executeQuery("DELETE FROM todo WHERE assessmentID = '$assessmentID'");

            // 6. Finally delete assessments
            executeQuery("DELETE FROM assessments WHERE assessmentID = '$assessmentID'");

            // Commit transaction
            mysqli_commit($conn);

            // Set success message in session
            $_SESSION['success'] = 'Assessment deleted successfully!';

            // Use JavaScript redirect since headers already sent
            $redirectCourseID = $_POST['courseID'] ?? $_GET['courseID'] ?? '';
            $redirectActiveTab = $_POST['activeTab'] ?? 'todo';
            echo '<script>window.location.href = "course-info.php?courseID=' . urlencode($redirectCourseID) . '&activeTab=' . urlencode($redirectActiveTab) . '";</script>';
            exit();
        } catch (Exception $e) {
            // Rollback on error
            mysqli_rollback($conn);
            error_log("Delete assessment error: " . $e->getMessage());
        }
    }
}

$assessmentCount = mysqli_num_rows($selectAssessmentResult);

// Determine if dropdowns should be visible
$showDropdowns = $assessmentCount > 0
    || (!empty($statusFilter) && $statusFilter != 'All')
    || (!empty($sortTodo) && $sortTodo == 'Missing');
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

<?php if ($showDropdowns): ?>
    <div class="d-flex align-items-center flex-nowrap mb-3" id="header">
        <!-- Sort by -->
        <div class="d-flex align-items-center flex-nowrap me-4">
            <span class="dropdown-label me-2 text-reg text-14">Sort by</span>
            <form method="POST">
                <input type="hidden" name="activeTab" value="todo">
                <select class="select-modern text-reg text-14" name="sortTodo" onchange="this.form.submit()">
                    <option value="Newest" <?php echo ($sortTodo == 'Newest') ? 'selected' : ''; ?>>Newest</option>
                    <option value="Oldest" <?php echo ($sortTodo == 'Oldest') ? 'selected' : ''; ?>>Oldest</option>
                </select>
            </form>
        </div>
        <!-- Status -->
        <div class="d-flex align-items-center flex-nowrap">
            <span class="dropdown-label me-2 text-reg text-14">Status</span>
            <form method="POST">
                <input type="hidden" name="activeTab" value="todo">
                <select class="select-modern text-reg text-14" name="statusFilter" onchange="this.form.submit()">
                    <option value="Pending" <?php echo ($statusFilter == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                    <option value="Missing" <?php echo ($statusFilter == 'Missing') ? 'selected' : ''; ?>>Missing</option>
                    <option value="Done" <?php echo ($statusFilter == 'Done') ? 'selected' : ''; ?>>Done</option>
                </select>
            </form>
        </div>
    </div>
<?php endif; ?>

<?php if ($assessmentCount > 0): ?>
    <!-- To-Do List -->
    <div class="d-flex flex-column flex-nowrap overflow-x-hidden" id="todo-list">
        <?php
        mysqli_data_seek($selectAssessmentResult, 0);
        while ($todo = mysqli_fetch_assoc($selectAssessmentResult)):
            if (empty($todo['assessmentTitle']))
                continue; // skip invalid assessments
            ?>
            <div class="row mb-0 mt-3">
                <div class="col-12 col-md-10">
                    <div class="todo-card d-flex align-items-stretch position-relative">
                        <a href="<?php
                        $type = strtolower(trim($todo['type']));
                        if ($type === 'task')
                            echo 'task-info.php?assignmentID=' . $todo['assignmentID'];
                        elseif ($type === 'test')
                            echo 'test-info.php?testID=' . $todo['testID'];
                        else
                            echo '#';
                        ?>" class="stretched-link">
                        </a>
                        <!-- Date -->
                        <div class="date d-flex align-items-center justify-content-center text-sbold text-20">
                            <?php echo $todo['assessmentDeadline']; ?>
                        </div>

                        <!-- Main content -->
                        <div class="d-flex flex-grow-1 flex-wrap justify-content-between p-2 w-100">
                            <div class="px-3 py-0">
                                <div class="text-sbold text-16"><?php echo htmlspecialchars($todo['assessmentTitle']); ?></div>
                                <span class="course-badge rounded-pill px-3 text-reg text-12 mt-2 d-inline d-md-none">
                                    <?php echo htmlspecialchars($todo['type']); ?>
                                </span>
                            </div>

                            <div class="d-flex align-items-center gap-2 ms-auto">
                                <span class="course-badge rounded-pill px-3 text-reg text-12 d-none d-md-inline">
                                    <?php echo htmlspecialchars($todo['type']); ?>
                                </span>

                                <?php
                                $type = strtolower(trim($todo['type']));
                                $link = "#";
                                $todoID = "";

                                if ($type === 'task') {
                                    $link = "task-info.php?assignmentID=" . $todo['assignmentID'];
                                    $todoID = $todo['assignmentID'];
                                } elseif ($type === 'test') {
                                    $link = "test-info.php?testID=" . $todo['testID'];
                                    $todoID = $todo['testID'];
                                }
                                ?>

                                <!-- DROPDOWN MENU -->
                                <div class="d-flex align-items-center flex-nowrap">
                                    <button class="btn btn-light btn-sm p-1 px-2 border-0 bg-transparent" type="button"
                                        id="dropdownMenuButton<?php echo $todoID; ?>" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>

                                    <ul class="dropdown-menu dropdown-menu-end"
                                        aria-labelledby="dropdownMenuButton<?php echo $todoID; ?>">
                                        <li><a class="dropdown-item text-reg text-14" href="<?php echo $link; ?>">Edit</a></li>
                                        <li>
                                            <button type="button" class="dropdown-item text-reg text-14 text-danger"
                                                data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $todoID; ?>">
                                                Delete
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php if (!empty($todoID)): ?>
                <div class="modal" id="deleteModal<?php echo $todoID; ?>" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                                    style="transform: scale(0.8);"></button>
                            </div>
                            <div class="modal-body d-flex flex-column justify-content-center align-items-center text-center">
                                <span class="mt-4 text-bold text-22">This action cannot be undone.</span>
                                <span class="mb-4 text-reg text-14">Are you sure you want to delete this assessment?</span>
                            </div>
                            <div class="modal-footer text-sbold text-18">
                                <button type="button" class="btn rounded-pill px-4"
                                    style="background-color: var(--primaryColor); border: 1px solid var(--black);"
                                    data-bs-dismiss="modal">Cancel</button>

                                <form method="POST" class="m-0">
                                    <input type="hidden" name="deleteAssessmentID" value="<?php echo $todoID; ?>">
                                    <input type="hidden" name="deleteAssessmentType"
                                        value="<?php echo htmlspecialchars($todo['type']); ?>">
                                    <input type="hidden" name="activeTab" value="todo">
                                    <?php if (isset($courseID)): ?>
                                        <input type="hidden" name="courseID" value="<?php echo $courseID; ?>">
                                    <?php endif; ?>
                                    <button type="submit" name="deleteAssessmentBtn" class="btn rounded-pill px-4"
                                        style="background-color: rgba(255, 80, 80, 1); border: 1px solid var(--black);">
                                        Delete
                                    </button>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endwhile; ?>
    </div>

<?php elseif (!empty($statusFilter) && $statusFilter != 'All' || $sortTodo == 'Missing'): ?>
    <div class="empty-state text-center">
        <?php if ($statusFilter == 'Pending'): ?>
            <img src="../shared/assets/img/empty/todo.png" alt="No Pending Quests" class="empty-state-img">
            <div class="empty-state-text text-reg text-14 d-flex flex-column align-items-center">
                <p class="text-med mt-1 mb-0">No quests have been assigned yet.</p>
                <p class="text-reg mt-1">Your next adventure awaits!</p>
            </div>

        <?php elseif ($statusFilter == 'Missing' || $sortTodo == 'Missing'): ?>
            <img src="../shared/assets/img/empty/quest.png" alt="No Missing Quests" class="empty-state-img">
            <div class="empty-state-text text-reg text-14 d-flex flex-column align-items-center">
                <p class="text-med mt-1 mb-0">No missing quests.</p>
                <p class="text-reg mt-1">You’re right on track, adventurer!</p>
            </div>

        <?php elseif ($statusFilter == 'Done'): ?>
            <img src="../shared/assets/img/empty/folder.png" alt="No Done Quests" class="empty-state-img">
            <div class="empty-state-text text-reg text-14 d-flex flex-column align-items-center">
                <p class="text-med mt-1 mb-0">You haven’t submitted any quests yet.</p>
                <p class="text-reg mt-1">Complete one to earn XPs!</p>
            </div>
        <?php endif; ?>
    </div>

<?php else: ?>
    <div class="empty-state text-center">
        <img src="../shared/assets/img/empty/todo.png" alt="No Quests" class="empty-state-img">
        <div class="empty-state-text text-reg text-14 d-flex flex-column align-items-center">
            <p class="text-med mb-0">No quests are available at the moment.</p>
        </div>
    </div>
<?php endif; ?>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const alertEl = document.querySelector('.alert.alert-success');
        if (alertEl) {
            setTimeout(() => {
                alertEl.style.transition = "opacity 0.5s ease-out";
                alertEl.style.opacity = 0;
                setTimeout(() => alertEl.remove(), 500);
            }, 3000);
        }
    });
</script>