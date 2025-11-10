<?php
$assessmentCount = mysqli_num_rows($selectAssessmentResult);

// Determine if dropdowns should be visible
$showDropdowns = $assessmentCount > 0
    || (!empty($statusFilter) && $statusFilter != 'All')
    || (!empty($sortTodo) && $sortTodo == 'Missing');
?>
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
                    <option value="Missing" <?php echo ($sortTodo == 'Missing') ? 'selected' : ''; ?>>Missing</option>
                </select>
            </form>
        </div>
        <!-- Status -->
        <div class="d-flex align-items-center flex-nowrap">
            <span class="dropdown-label me-2 text-reg text-14">Status</span>
            <form method="POST">
                <input type="hidden" name="activeTab" value="todo">
                <select class="select-modern text-reg text-14" name="statusFilter" onchange="this.form.submit()">
                    <option value="All" <?php echo ($statusFilter == 'All') ? 'selected' : ''; ?>>All</option>
                    <option value="Pending" <?php echo ($statusFilter == 'Pending') ? 'selected' : ''; ?>>Assigned</option>
                    <option value="Missing" <?php echo ($statusFilter == 'Missing') ? 'selected' : ''; ?>>Missing</option>
                    <option value="Done" <?php echo ($statusFilter == 'Graded') ? 'selected' : ''; ?>>Done</option>
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
            if (empty($todo['assessmentTitle'])) continue; // skip invalid assessments
        ?>
            <div class="row mb-0 mt-3">
                <div class="col-12 col-md-10">
                    <div class="todo-card d-flex align-items-stretch">
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

                                if ($type === 'task') {
                                    $link = "task-info.php?assignmentID=" . $todo['assignmentID'];
                                } elseif ($type === 'test') {
                                    $link = "test-info.php?testID=" . $todo['testID'];
                                }
                                ?>

                                <a href="<?php echo $link; ?>" class="text-decoration-none">
                                    <i class="fa-solid fa-arrow-right text-reg text-12 pe-2" style="color: var(--black);"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

<?php elseif (!empty($statusFilter) && $statusFilter != 'All' || $sortTodo == 'Missing'): ?>
    <div class="empty-state text-center">
        <?php if ($statusFilter == 'Pending'): ?>
            <img src="../shared/assets/img/courseInfo/puzzle.png" alt="No Pending Quests" class="empty-state-img">
            <div class="empty-state-text text-reg text-16 d-flex flex-column align-items-center">
                <p class="text-med mb-0">No quests have been assigned yet.</p>
                <p class="text-reg">Your next adventure awaits!</p>
            </div>

        <?php elseif ($statusFilter == 'Missing' || $sortTodo == 'Missing'): ?>
            <img src="../shared/assets/img/courseInfo/thumbs-up.png" alt="No Missing Quests" class="empty-state-img">
            <div class="empty-state-text text-reg text-16 d-flex flex-column align-items-center">
                <p class="text-med mb-0">No missing quests.</p>
                <p class="text-reg">You’re right on track, adventurer!</p>
            </div>

        <?php elseif ($statusFilter == 'Done'): ?>
            <img src="../shared/assets/img/courseInfo/file.png" alt="No Done Quests" class="empty-state-img">
            <div class="empty-state-text text-reg text-16 d-flex flex-column align-items-center">
                <p class="text-med mb-0">You haven’t submitted any quests yet.</p>
                <p class="text-reg">Complete one to earn XPs!</p>
            </div>
        <?php endif; ?>
    </div>

<?php else: ?>
    <div class="empty-state text-center">
        <img src="../shared/assets/img/courseInfo/puzzle.png" alt="No Quests" class="empty-state-img">
        <div class="empty-state-text text-reg text-16 d-flex flex-column align-items-center">
            <p class="text-med mb-0">No quests are available at the moment.</p>
        </div>
    </div>
<?php endif; ?>