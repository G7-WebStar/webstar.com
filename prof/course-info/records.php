<?php
// Ensure $courseID and $conn are already defined

// Get POST values or set default
$filter = $_POST['filterRecords'] ?? 'All';
$order = $_POST['orderByRecords'] ?? 'Ascending';
$orderDirection = $order === 'Ascending' ? 'ASC' : 'DESC';

// === Fetch assessments for dropdown dynamically ===
$assessmentQuery = "SELECT assessmentTitle FROM assessments WHERE courseID = '$courseID' ORDER BY assessmentTitle ASC";
$assessmentResult = executeQuery($assessmentQuery);
$assessmentsList = ['All']; // Default option
if ($assessmentResult && mysqli_num_rows($assessmentResult) > 0) {
    while ($row = mysqli_fetch_assoc($assessmentResult)) {
        $assessmentsList[] = $row['assessmentTitle'];
    }
}

// === Build main record query dynamically ===
$recordQuery = "
SELECT 
    userInfo.userInfoID,
    userInfo.firstName,
    userInfo.middleName,
    userInfo.lastName,
    assessments.assessmentID,
    assessments.assessmentTitle,
    assessments.type AS assessmentType,
    scores.score AS score,
    CASE 
        WHEN assessments.type = 'task' THEN COALESCE(assignments.assignmentPoints, 0)
        WHEN assessments.type = 'test' THEN (
            SELECT COALESCE(SUM(testQuestions.testQuestionPoints), 0)
            FROM testQuestions
            WHERE testQuestions.testID = tests.testID
        )
        ELSE 0
    END AS totalPoints
FROM enrollments
JOIN users ON enrollments.userID = users.userID
JOIN userInfo ON users.userID = userInfo.userID
JOIN assessments ON assessments.courseID = enrollments.courseID
LEFT JOIN assignments ON assessments.assessmentID = assignments.assessmentID
LEFT JOIN tests ON assessments.assessmentID = tests.assessmentID
LEFT JOIN scores 
    ON scores.userID = enrollments.userID 
    AND (
        (scores.assignmentID = assignments.assignmentID) 
        OR (scores.testID = tests.testID)
    )
WHERE enrollments.courseID = '$courseID'
";

if ($filter === 'All') {
    // Sort by lastName
    $recordQuery .= " ORDER BY userInfo.lastName $orderDirection";
} else {
    // Sort by score of the specific assessment, then lastName
    $safeFilter = mysqli_real_escape_string($conn, $filter);

    if ($order === 'Ascending') {
        $recordQuery .= "
    ORDER BY
        (assessments.assessmentTitle != '$safeFilter') ASC,
        (scores.score IS NULL) ASC,
        CAST(scores.score AS UNSIGNED) ASC,
        userInfo.lastName ASC
    ";
    } else {
        $recordQuery .= "
    ORDER BY
        (assessments.assessmentTitle != '$safeFilter') ASC,
        CAST(scores.score AS UNSIGNED) DESC,
        userInfo.lastName ASC
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

<div class="container-fluid records-controls">
    <div class="row align-items-center justify-content-start flex-wrap">
        <!-- Search -->
        <div class="col-auto d-flex search-container mb-2 mb-lg-0" style="flex: 0 0 250px;">
            <input type="text" placeholder="Search classmates" class="form-control py-1 text-reg text-lg-12 text-12">
            <button type="button" class="btn-outline-secondary ms-1">
                <i class="bi bi-search me-2"></i>
            </button>
        </div>

        <!-- Dropdowns -->
        <div class="col-auto d-flex dropdowns-wrapper mb-2 mb-lg-0">
            <!-- Order dropdown -->
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

            <!-- Filter dropdown -->
            <div class="d-flex align-items-center mt-1" style="margin-left: 12px;">
                <span class="dropdown-label me-1 text-reg text-14">Filter by</span>
                <form method="POST" style="display: inline-block; margin: 0;">
                    <input type="hidden" name="activeTab" value="records">
                    <input type="hidden" name="orderByRecords" value="<?php echo htmlspecialchars($order); ?>">
                    <select class="select-modern text-reg text-14" name="filterRecords" onchange="this.form.submit()">
                        <?php foreach ($assessmentsList as $assessmentTitle): ?>
                            <option value="<?php echo htmlspecialchars($assessmentTitle); ?>" <?php if ($filter === $assessmentTitle) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($assessmentTitle); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="records p-3">
    <div class="row">
        <div class="col-12">
            <div class="card" style="border: 1px solid var(--black); border-radius: 10px; overflow: hidden;">
                <div class="table-responsive" style="overflow-x: auto; overflow-y: visible;">
                    <table class="table mb-0" style="min-width: 100px; border-collapse: collapse; table-layout: fixed;">
                        <thead>
                            <tr class="text-med text-12">
                                <th style="position: sticky; left: 0; z-index: 3; background-color: var(--primaryColor); border-right: 1px solid var(--black); 
                                    border-bottom: 1px solid var(--black); width: 150px; font-weight: normal;">
                                    Name
                                </th>
                                <?php foreach ($displayAssessments as $assessmentTitle): ?>
                                    <th style="background-color: var(--primaryColor); border-right:1px solid var(--black); border-bottom:1px solid var(--black); width:150px; font-weight: normal;">
                                        <?php echo htmlspecialchars($assessmentTitle); ?>
                                    </th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($students)): ?>
                                <?php foreach ($students as $studentName => $scores): ?>
                                    <tr class="text-med text-12">
                                        <td style="position: sticky; left: 0; z-index: 2; background: #fff; border-right:1px solid var(--black);">
                                            <?php echo htmlspecialchars($studentName); ?>
                                        </td>
                                        <?php foreach ($displayAssessments as $assessmentTitle): ?>
                                            <td style="border-right:1px solid var(--black);">
                                                <?php echo isset($scores[$assessmentTitle]) ? $scores[$assessmentTitle] : '-'; ?>
                                            </td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="<?php echo count($displayAssessments) + 1 ?>" class="text-center">No records found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SEARCH FUNCTIONALITY -->
<script>
    const searchInput = document.querySelector('.search-container input');
    const tableRows = document.querySelectorAll('.records tbody tr');

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

        const noRecordsRow = document.querySelector('.records tbody tr td[colspan]');
        if (noRecordsRow) {
            noRecordsRow.parentElement.style.display = anyVisible ? 'none' : '';
        }
    });
</script>