<!-- Sort By Dropdown (Front-End Only, above announcements) -->
<div class="d-flex align-items-center flex-nowrap mb-3" id="header">
    <span class="dropdown-label me-2">Sort by:</span>
    <button class="btn dropdown-toggle dropdown-custom" type="button"
        data-bs-toggle="dropdown" aria-expanded="false">
        <span>Newest</span>
    </button>
    <ul class="dropdown-menu">
        <li><a class="dropdown-item text-reg" href="#">Newest</a></li>
        <li><a class="dropdown-item text-reg" href="#">Oldest</a></li>
        <li><a class="dropdown-item text-reg" href="#">Unread first</a></li>
    </ul>
</div>

<?php if (mysqli_num_rows($selectAssessmentResult) > 0) {
    mysqli_data_seek($selectAssessmentResult, 0);
    while ($todo = mysqli_fetch_assoc($selectAssessmentResult)) {
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
                        <!-- For small screen of main content -->
                        <div class="px-3 py-0">
                            <div class="text-sbold text-16"><?php echo $todo['assessmentTitle']; ?></div>
                            <span class="course-badge rounded-pill px-3 text-reg text-12 mt-2 d-inline d-md-none"><?php echo $todo['type']; ?></span>
                        </div>
                        <!-- Pill and Arrow on Large screen-->
                        <div class="d-flex align-items-center gap-2 ms-auto">
                            <span class="course-badge rounded-pill px-3 text-reg text-12 d-none d-md-inline"><?php echo $todo['type']; ?></span>

                            <?php
                            $type = strtolower(trim($todo['type']));
                            $link = "#";

                            if ($type === 'task') {
                                $link = "assignment.php?assignmentID=" . $todo['assignmentID'];
                            } elseif ($type === 'test') {
                                $link = "test.php?testID=" . $todo['assignmentID'];
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
<?php
    }
}
?>