<?php if (mysqli_num_rows($selectAssessmentResult) > 0): ?>
    <div class="d-flex align-items-center flex-nowrap mb-3" id="header">
        <!-- Sort by -->
        <div class="d-flex align-items-center flex-nowrap me-4">
            <span class="dropdown-label me-2 text-reg">Sort by</span>
            <div class="custom-dropdown">
                <button class="dropdown-btn text-reg text-14">Newest</button>
                <ul class="dropdown-list text-reg text-14">
                    <li data-value="Newest">Newest</li>
                    <li data-value="Oldest">Oldest</li>
                    <li data-value="Unread">Unread</li>
                </ul>
            </div>
        </div>

        <!-- Status -->
        <div class="d-flex align-items-center flex-nowrap">
            <span class="dropdown-label me-2 text-reg">Status</span>
            <div class="custom-dropdown">
                <button class="dropdown-btn text-reg text-14">All</button>
                <ul class="dropdown-list text-reg text-14">
                    <li data-value="All">All</li>
                    <li data-value="Pending">Pending</li>
                    <li data-value="Missing">Missing</li>
                    <li data-value="Done">Done</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- To-Do List -->
    <div id="todo-list">
        <?php
        mysqli_data_seek($selectAssessmentResult, 0);
        while ($todo = mysqli_fetch_assoc($selectAssessmentResult)):
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
                                <div class="text-sbold text-16"><?php echo $todo['assessmentTitle']; ?></div>
                                <span class="course-badge rounded-pill px-3 text-reg text-12 mt-2 d-inline d-md-none">
                                    <?php echo $todo['type']; ?>
                                </span>
                            </div>

                            <div class="d-flex align-items-center gap-2 ms-auto">
                                <span class="course-badge rounded-pill px-3 text-reg text-12 d-none d-md-inline">
                                    <?php echo $todo['type']; ?>
                                </span>

                                <?php
                                $type = strtolower(trim($todo['type']));
                                $link = "#";

                                if ($type === 'task') {
                                    $link = "assignment.php?assignmentID=" . $todo['assignmentID'];
                                } elseif ($type === 'test') {
                                    $link = "test.php?testID=" . $todo['testID'];
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

    <!-- Status-Based Empty States  -->
    <div id="empty-assigned" class="empty-state text-center d-none">
        <img src="shared/assets/img/courseInfo/puzzle.png" alt="No Assigned Quests" class="empty-state-img">
        <div class="empty-state-text text-16 d-flex flex-column align-items-center">
            <p class="text-med mb-0">No quests have been assigned yet.</p>
            <p class="text-reg">Your next adventure awaits!</p>
        </div>
    </div>


    <div id="empty-missing" class="empty-state text-center d-none">
        <img src="shared/assets/img/courseInfo/thumbs-up.png" alt="No Missing Quests" class="empty-state-img">
        <div class="empty-state-text text-16 d-flex flex-column align-items-center">
            <p class="text-med mb-0">No missing quests.</p>
            <p class="text-reg">You’re right on track, adventurer!</p>
        </div>
    </div>

    <div id="empty-done" class="empty-state text-center d-none">
        <img src="shared/assets/img/courseInfo/file.png" alt="No Done Quests" class="empty-state-img">
        <div class="empty-state-text text-16 d-flex flex-column align-items-center">
            <p class="text-med mb-0">You haven’t submitted any quests yet.</pp>
            <p class="text-reg">Complete one to earn XPs!</p>
        </div>
    </div>

<?php else: ?>
    <!-- No To-Do Placeholder -->
    <div class="empty-state text-center">
        <img src="shared/assets/img/courseInfo/puzzle.png"
            alt="No Todo"
            class="empty-state-img">
        <div class="empty-state-text text-reg text-16">
            No quests are available at the moment.
        </div>
    </div>
<?php endif; ?>