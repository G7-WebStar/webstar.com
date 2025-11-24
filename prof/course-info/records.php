<?php

$filter = $_POST['filterRecords'] ?? 'All';
$order = $_POST['orderByRecords'] ?? 'Ascending';
$orderDirection = $order === 'Ascending' ? 'ASC' : 'DESC';

$assessmentQuery = "SELECT assessmentTitle FROM assessments WHERE courseID = '$courseID' ORDER BY assessmentTitle ASC";
$assessmentResult = executeQuery($assessmentQuery);
$assessmentsList = ['All']; // Default option
if ($assessmentResult && mysqli_num_rows($assessmentResult) > 0) {
    while ($row = mysqli_fetch_assoc($assessmentResult)) {
        $assessmentsList[] = $row['assessmentTitle'];
    }
}

$recordQuery = "
SELECT 
    userinfo.userinfoID,
    userinfo.firstName,
    userinfo.middleName,
    userinfo.lastName,
    assessments.assessmentID,
    assessments.assessmentTitle,
    assessments.type AS assessmentType,
    scores.score AS score,
    CASE 
        WHEN assessments.type = 'task' THEN COALESCE(assignments.assignmentPoints, 0)
        WHEN assessments.type = 'test' THEN (
            SELECT COALESCE(SUM(testquestions.testQuestionPoints), 0)
            FROM testquestions
            WHERE testquestions.testID = tests.testID
        )
        ELSE 0
    END AS totalPoints
FROM enrollments
JOIN users ON enrollments.userID = users.userID
JOIN userinfo ON users.userID = userinfo.userID
JOIN assessments ON assessments.courseID = enrollments.courseID
LEFT JOIN assignments ON assessments.assessmentID = assignments.assessmentID
LEFT JOIN tests ON assessments.assessmentID = tests.assessmentID
LEFT JOIN submissions 
    ON submissions.userID = enrollments.userID 
    AND submissions.assessmentID = assignments.assessmentID
LEFT JOIN scores 
    ON (
        scores.submissionID = submissions.submissionID
        OR (
            scores.testID = tests.testID
            AND scores.userID = enrollments.userID
        )
    )

WHERE enrollments.courseID = '$courseID'
";

if ($filter === 'All') {
    $recordQuery .= " ORDER BY userinfo.lastName $orderDirection";
} else {
    $safeFilter = mysqli_real_escape_string($conn, $filter);
    if ($order === 'Ascending') {
        $recordQuery .= "
    ORDER BY
        (assessments.assessmentTitle != '$safeFilter') ASC,
        (scores.score IS NULL) ASC,
        CAST(scores.score AS UNSIGNED) ASC,
        userinfo.lastName ASC
    ";
    } else {
        $recordQuery .= "
    ORDER BY
        (assessments.assessmentTitle != '$safeFilter') ASC,
        CAST(scores.score AS UNSIGNED) DESC,
        userinfo.lastName ASC
    ";
    }
}

$recordResult = executeQuery($recordQuery);

$students = [];
$assessments = [];

if ($recordResult && mysqli_num_rows($recordResult) > 0) {
    while ($row = mysqli_fetch_assoc($recordResult)) {
        $studentName = trim(
            $row['lastName'] .
                (!empty($row['firstName']) ? ', ' . $row['firstName'] : '') .
                ' ' . $row['middleName']
        );

        $assessmentTitle = $row['assessmentTitle'];
        if (!in_array($assessmentTitle, $assessments)) {
            $assessments[] = $assessmentTitle;
        }

        $score = isset($row['score']) && $row['score'] !== null ? $row['score'] : '-';
        $total = !empty($row['totalPoints']) ? $row['totalPoints'] : 0;
        $students[$studentName][$assessmentTitle] = "{$score}/{$total}";
    }
}

$displayAssessments = $filter === 'All' ? $assessments : [$filter];
?>

<!-- Controls -->
<?php if (!empty($students)): ?>
    <div class="container-fluid records-controls mb-3">
        <div class="row align-items-center justify-content-start flex-wrap">
            <!-- Search -->
            <div class="col-auto d-flex search-container mb-2 mb-lg-0" style="flex: 0 0 210px;">
                <input type="text" placeholder="Search classmates" class="form-control py-1 text-reg text-lg-12 text-12">
                <button type="button" class="btn-outline-secondary ms-1">
                    <span class="material-symbols-outlined me-2 py-3">search</span>
                </button>
            </div>

            <!-- Dropdowns -->
            <div class="col-auto d-flex dropdowns-wrapper mb-2 mb-lg-0">
                <!-- Order -->
                <div class="d-flex align-items-center mt-1">
                    <span class="dropdown-label me-1 text-reg text-14">Order by</span>
                    <form method="POST" style="display: inline-block; margin: 0;">
                        <input type="hidden" name="activeTab" value="records">
                        <input type="hidden" name="filterRecords" value="<?php echo htmlspecialchars($filter); ?>">
                        <select class="select-modern text-reg text-14" name="orderByRecords" onchange="this.form.submit()">
                            <option value="Ascending" <?php if ($order === 'Ascending') echo 'selected'; ?>>Ascending</option>
                            <option value="Descending" <?php if ($order === 'Descending') echo 'selected'; ?>>Descending</option>
                        </select>
                    </form>
                </div>

                <!-- Filter -->
                <div class="d-flex align-items-center mt-1 ms-3">
                    <span class="dropdown-label me-1 text-reg text-14">Filter by</span>
                    <form method="POST" style="display: inline-block; margin: 0;">
                        <input type="hidden" name="activeTab" value="records">
                        <input type="hidden" name="orderByRecords" value="<?php echo htmlspecialchars($order); ?>">
                        <select class="select-modern text-reg text-14" name="filterRecords" style="width:120px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" onchange="this.form.submit()">
                            <?php foreach ($assessmentsList as $assessmentTitle): ?>
                                <option value="<?php echo htmlspecialchars($assessmentTitle); ?>" title="<?php echo htmlspecialchars($assessmentTitle); ?>"
                                    <?php if ($filter === $assessmentTitle) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars(strlen($assessmentTitle) > 20 ? substr($assessmentTitle, 0, 20) . '…' : $assessmentTitle); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Table or Empty State -->
    <div class="records p-3">
        <div class="row">
            <div class="col-12">
                <div class="card" style="border: 1px solid var(--black); border-radius: 10px; overflow: hidden;">
                    <div class="table-responsive" style="overflow-x: auto; overflow-y: visible;">
                        <table class="table mb-0" style="min-width: 100px; border-collapse: collapse; table-layout: fixed;">
                            <thead>
                                <tr class="text-med text-12">
                                    <th style="position: sticky; left: 0; z-index: 3; background-color: var(--primaryColor); border-right: 1px solid var(--black); border-bottom: 1px solid var(--black); width: 150px; font-weight: normal;">Name</th>
                                    <?php foreach ($displayAssessments as $assessmentTitle): ?>
                                        <th style="background-color: var(--primaryColor); border-right:1px solid var(--black); border-bottom:1px solid var(--black); width:150px; font-weight: normal; 
                                                overflow: hidden; white-space: nowrap; text-overflow: ellipsis;"
                                            title="<?php echo htmlspecialchars($assessmentTitle); ?>">
                                            <?php echo htmlspecialchars($assessmentTitle); ?>
                                        </th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($students as $studentName => $scores): ?>
                                    <tr class="text-med text-12">
                                        <td class="text-truncate" style="position: sticky; left: 0; z-index: 2; background: #fff; border-right:1px solid var(--black);"
                                            title="<?php echo htmlspecialchars($studentName); ?>">
                                            <?php echo htmlspecialchars($studentName); ?>
                                        </td>
                                        <?php foreach ($displayAssessments as $assessmentTitle): ?>
                                            <td style="border-right:1px solid var(--black);">
                                                <?php echo isset($scores[$assessmentTitle]) ? $scores[$assessmentTitle] : '-'; ?>
                                            </td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="empty-state text-center mt-3">
        <img src="../shared/assets/img/empty/records.png" alt="No Records" class="empty-state-img">
        <div class="empty-state-text text-14 d-flex flex-column align-items-center">
            <p class="text-med mt-1 mb-0">No records yet.</p>
            <p class="text-reg mt-1">Grades will show up here as you assess their work.</p>
        </div>
    </div>
<?php endif; ?>

<!-- SEARCH FUNCTIONALITY -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.querySelector('.search-container input');
        const tableRows = document.querySelectorAll('.records tbody tr');

        let searchEmptyState = document.querySelector('.search-empty-state');
        if (!searchEmptyState) {
            searchEmptyState = document.createElement('div');
            searchEmptyState.className = 'search-empty-state text-center mt-3 text-14 d-none';
            searchEmptyState.innerHTML = `
            <img src="../shared/assets/img/empty/records.png" alt="No Records" class="empty-state-img">
            <div class="empty-state-text d-flex flex-column align-items-center">
                <p class="text-med mt-1 mb-0">No matching records found.</p>
            </div>
        `;
            document.querySelector('.records').appendChild(searchEmptyState);
        }

        searchInput.addEventListener('input', () => {
            const query = searchInput.value.toLowerCase().trim();
            let anyVisible = false;

            tableRows.forEach(row => {
                const studentCell = row.querySelector('td:first-child');
                if (!studentCell) return;
                const studentName = studentCell.textContent.toLowerCase();
                if (studentName.includes(query)) {
                    row.style.display = '';
                    anyVisible = true;
                } else {
                    row.style.display = 'none';
                }
            });

            if (!anyVisible) {
                searchEmptyState.classList.remove('d-none');
            } else {
                searchEmptyState.classList.add('d-none');
            }
        });
    });
</script>