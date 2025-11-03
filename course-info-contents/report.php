<?php
if (isset($userID)) {
    $studentQuery = "
    SELECT 
        users.userName,
        userInfo.firstName,
        userInfo.middleName,
        userInfo.lastName,
        userInfo.studentID,
        userInfo.yearLevel,
        userInfo.yearSection,
        userInfo.profilePicture,
        program.programInitial,
        enrollments.enrollmentID
    FROM users
    INNER JOIN userInfo ON users.userID = userInfo.userID
    INNER JOIN program ON userInfo.programID = program.programID
    INNER JOIN enrollments ON enrollments.userID = users.userID
    WHERE users.userID = '$userID' 
      AND enrollments.courseID = '$courseID'
";

    $studentQueryResult = executeQuery($studentQuery);
    if ($studentQueryResult && mysqli_num_rows($studentQueryResult) > 0) {
        $student = mysqli_fetch_assoc($studentQueryResult);
        $enrollmentID = $student['enrollmentID'];
    }

    // LEADERBOARD + REPORT LOGIC
    if (!empty($enrollmentID)) {
        $leaderboardQuery = "
            SELECT leaderboard.enrollmentID, SUM(leaderboard.xpPoints) AS totalXP
            FROM leaderboard
            INNER JOIN enrollments
                ON leaderboard.enrollmentID = enrollments.enrollmentID
            WHERE enrollments.courseID = '$courseID'
            GROUP BY leaderboard.enrollmentID
            ORDER BY totalXP DESC
        ";
        $leaderboardResult = executeQuery($leaderboardQuery);

        $rank = 1;
        while ($row = mysqli_fetch_assoc($leaderboardResult)) {
            $id = $row['enrollmentID'];
            $totalXP = $row['totalXP'];

            $checkQuery = "SELECT * FROM report WHERE enrollmentID = '$id'";
            $checkResult = executeQuery($checkQuery);

            if (mysqli_num_rows($checkResult) > 0) {
                $updateReport = "
                    UPDATE report 
                    SET totalXP = '$totalXP',
                        allTimeRank = '$rank',
                        generatedAt = NOW()
                    WHERE enrollmentID = '$id'
                ";
                executeQuery($updateReport);
            } else {
                $insertReport = "
                    INSERT INTO report (enrollmentID, totalXP, allTimeRank, generatedAt)
                    VALUES ('$id', '$totalXP', '$rank', NOW())
                ";
                executeQuery($insertReport);
            }

            if ($id == $enrollmentID) {
                $studentTotalXP = $totalXP;
                $studentRank = $rank;
            }

            $rank++;
        }
    }

    // for submission record
    $recordQuery = "
    SELECT 
        submissions.submittedAt,
        assessments.assessmentID,
        assessments.assessmentTitle,
        assessments.type,
        scores.score,
        assignments.assignmentID,
        assignments.assignmentPoints
    FROM submissions
    INNER JOIN scores 
        ON submissions.scoreID = scores.scoreID
    INNER JOIN assessments 
        ON submissions.assessmentID = assessments.assessmentID
    INNER JOIN assignments 
        ON assignments.assessmentID = assessments.assessmentID
    WHERE scores.userID = '$userID'
      AND assessments.courseID = '$courseID'
    ORDER BY submissions.submittedAt DESC
";
    $recordResult = executeQuery($recordQuery);
}
?>
<div class="row mt-3">
    <div class="mx-1 mb-3">
        <div class="col-12 card p-2 d-flex flex-column flex-sm-row align-items-center justify-content-between"
            style="
        border: 1px solid var(--black); border-radius: 20px; background: linear-gradient(180deg, #FDDF94 0%, #FFFFFF 100%);">
            <!-- Left Side: Profile + Info -->
            <div class="d-flex flex-column flex-sm-row align-items-center text-center text-sm-start ">
                <div class="card d-flex justify-content-center align-items-center"
                    style="width:83px; height:85px; border-radius:15px; overflow:hidden; border:1px solid var(--black); background-color:var(--black); margin:12px;">
                    <img src="<?php
                    if (!empty($student['profilePicture'])) {
                        echo 'shared/assets/pfp-uploads/' . $student['profilePicture'];
                    } else {
                        echo 'shared/assets/img/default-profile.png';
                    }
                    ?>" alt="Profile" style="object-fit:cover; border-radius:15px; width:100%; height:100%;">
                </div>

                <div>
                    <?php if (!empty($student)): ?>
                        <div class="text-bold text-18">
                            <?php
                            $middleInitial = !empty($student['middleName'])
                                ? strtoupper(substr($student['middleName'], 0, 1)) . '.'
                                : '';
                            echo $student['firstName'] . ' ' . $middleInitial . ' ' . $student['lastName'];
                            ?>
                        </div>
                        <div class="text-med text-muted text-12">@<?php echo $student['userName']; ?></div>
                        <div class="text-med text-12"><?php echo $student['studentID']; ?></div>
                        <div class="text-med text-12">
                            <?php echo $student['programInitial'] . ' ' . $student['yearLevel'] . '-' . $student['yearSection']; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-bold text-18">No student data found.</div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right Side: Rank + XP -->
            <div class="text-center align-items-center justify-content-center text-sm-end mt-3 mt-sm-0 mb-3 mb-sm-0"
                style="line-height: 1.2; margin-right: 15px;">
                <?php if (!empty($studentRank) && !empty($studentTotalXP)): ?>
                    <div class="text-bold text-25">
                        <?php
                        // ordinal suffix
                        $suffix = 'th';
                        if (!in_array(($studentRank % 100), [11, 12, 13])) {
                            switch ($studentRank % 10) {
                                case 1:
                                    $suffix = 'st';
                                    break;
                                case 2:
                                    $suffix = 'nd';
                                    break;
                                case 3:
                                    $suffix = 'rd';
                                    break;
                            }
                        }
                        echo $studentRank . $suffix;
                        ?>
                    </div>
                    <div class="text-bold text-22"><?php echo $studentTotalXP; ?> XPs</div>
                    <div class="text-med text-18">All-time Rank</div>
                <?php else: ?>
                    <div class="text-bold text-30">-</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="row ms-2 justify-content-center align-items-center mt-3 text-center">
        <div class="col-12 col-sm-6 mb-3 mb-sm-0 d-flex flex-column align-items-center">
            <div class="percent d-flex justify-content-center align-items-center text-sbold text-20">
                <span id="status-icon" class="material-symbols-outlined" style="margin-right:6px; font-size:23px;">
                    assignment_turned_in
                </span>
                <span id="status-text">100%</span>
            </div>
            <div class="title text-sbold text-16">On-Time Submissions</div>
            <div class="sub text-reg text-12">Percentage of tasks and <br> exams submitted on time.</div>
        </div>

        <div class="col-12 col-sm-6 d-flex flex-column align-items-center">
            <div class="percent d-flex justify-content-center align-items-center text-sbold text-20">
                <span id="status-icon" class="material-symbols-outlined" style="margin-right:6px; font-size:23px;">
                    assignment
                </span>
                <span id="status-text">100%</span>
            </div>
            <div class="title text-sbold text-16">Overall Performance</div>
            <div class="sub text-reg text-12">Based on your submission rate and <br> scores in tasks and
                exams.</div>
        </div>
    </div>

    <!-- Record Table -->
    <div class="row mt-2">
        <div class="col-12">
            <div class="text-med text-20 my-3">Record</div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card d-none d-md-block"
                style="border: 1px solid var(--black); border-radius: 10px; overflow: hidden; width: 620px;">
                <div class="table-responsive d-none d-md-block">
                    <table class="table mb-0" style="width: 618px;">
                        <thead>
                            <tr class="text-med text-12">
                                <th
                                    style="background-color: var(--primaryColor); border-right:1px solid var(--black); border-bottom:1px solid var(--black); width:25%;">
                                    <span class="material-symbols-outlined"
                                        style="font-size:13px; vertical-align:middle; margin-right:4px;">calendar_today</span>Date
                                </th>
                                <th
                                    style="background-color: var(--primaryColor); border-right:1px solid var(--black); border-bottom:1px solid var(--black); width:25%;">
                                    <span class="material-symbols-outlined"
                                        style="font-size:13px; vertical-align:middle; margin-right:4px;">notes</span>Title
                                </th>
                                <th
                                    style="background-color: var(--primaryColor); border-right:1px solid var(--black); border-bottom:1px solid var(--black); width:25%;">
                                    <span class="material-symbols-outlined"
                                        style="font-size:13px; vertical-align:middle; margin-right:4px;">list</span>Type
                                </th>
                                <th
                                    style="background-color: var(--primaryColor); border-bottom:1px solid var(--black); width:25%;">
                                    <span class="material-symbols-outlined"
                                        style="font-size:13px; vertical-align:middle; margin-right:4px;">task_alt</span>Grade
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($recordResult && mysqli_num_rows($recordResult) > 0): ?>
                                <?php while ($record = mysqli_fetch_assoc($recordResult)): ?>
                                    <tr class="text-med text-12">
                                        <td style="border-right:1px solid var(--black);">
                                            <?php echo date('F d, Y', strtotime($record['submittedAt'])); ?>
                                        </td>
                                        <td style="border-right:1px solid var(--black);">
                                            <a href="assignment.php?assignmentID=<?php echo $record['assignmentID']; ?>"
                                                style="text-decoration:none; color:var(--black); font-weight:500;">
                                                <?php echo $record['assessmentTitle']; ?>
                                            </a>
                                        </td>
                                        <td style="border-right:1px solid var(--black); text-align:center;">
                                            <span class="course-badge rounded-pill px-3 text-reg text-12">
                                                <?php echo ucfirst($record['type']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo $record['score']; ?>/<?php echo $record['assignmentPoints']; ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center text-reg text-12">No submission records found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Cards -->
    <div class="row">
        <div class="col d-block d-md-none">
            <?php
            $recordResultMobile = executeQuery($recordQuery);
            if ($recordResultMobile && mysqli_num_rows($recordResultMobile) > 0):
                while ($record = mysqli_fetch_assoc($recordResultMobile)):
                    ?>
                    <div class="card p-3 ms-2 mb-2" style="border: 1px solid var(--black); border-radius: 15px; width: 290px;">
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="assignment.php?assignmentID=<?php echo $record['assignmentID']; ?>"
                                style="text-decoration:none; color:var(--black); font-weight:500;">
                                <?php echo $record['assessmentTitle']; ?>
                            </a>
                            <span class="text-sbold text-16"><?php echo $record['score']; ?>/100</span>
                        </div>
                        <div class="justify-content-start align-items-start">
                            <div class="text-reg text-14 mb-2"><?php echo date('F d, Y', strtotime($record['submittedAt'])); ?>
                            </div>
                            <div class="course-badge rounded-pill px-3 text-reg text-12" style="width: 60px;">
                                <?php echo ucfirst($record['type']); ?>
                            </div>
                        </div>
                    </div>
                    <?php
                endwhile;
            else:
                ?>
                <div class="text-center text-muted">No submission records found.</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Badges -->
    <div class="row">
        <div class="col">
            <div class="text-med text-20 my-3">
                Badges
            </div>
        </div>
    </div>
    <div class="row mx-1">
        <div class="card py-2 py-sm-0" style="border: 1px solid var(--black); border-radius: 15px; width: 100%;">
            <div class="col-12 d-flex flex-row align-items-center justify-content-between flex-nowrap">

                <!-- Left Side -->
                <div class="d-flex flex-row align-items-center flex-nowrap" style="min-width: 0;">

                    <!-- Badge -->
                    <div class="d-flex justify-content-center align-items-center flex-shrink-0">
                        <img src="shared/assets/img/badge/Badge.png"
                            style="width:60px; height:62px; object-fit:contain;">
                    </div>

                    <!-- Details -->
                    <div class="ms-2" style="min-width: 0; word-break: break-word; white-space: normal;">
                        <div class="text-bold text-16">Ahead of the Curve</div>
                        <div class="text-med text-12">You submitted before the deadline—way to stay ahead!</div>
                    </div>

                </div>

                <!-- Right Side -->
                <div class="text-end flex-shrink-0" style="line-height: 1.2; margin-right: 15px;">
                    <div class="text-bold text-22 justify-content-end">x3</div>
                </div>
            </div>
        </div>
    </div>
</div>