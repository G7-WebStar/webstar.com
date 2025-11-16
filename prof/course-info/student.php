<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$query = "
    SELECT 
        users.userID,
        users.userName,
        userinfo.firstName,
        userinfo.lastName,
        userinfo.profilePicture,
        users.role,
        enrollments.enrollmentID
    FROM enrollments
    INNER JOIN users ON enrollments.userID = users.userID
    INNER JOIN userinfo ON users.userID = userinfo.userID
    WHERE users.role = 'student' AND enrollments.courseID = '$courseID'
    ORDER BY userinfo.lastName ASC
";

$result = mysqli_query($conn, $query);
$count = mysqli_num_rows($result);
?>

<div class="container-fluid">
    <!-- Search bar -->
    <div class="row align-items-center justify-content-start flex-column flex-md-row">
        <div class="col-8 col-sm-6 col-md-12 col-lg-6 d-flex search-container mb-2 mb-lg-0 p-0 position-relative">
            <input type="text" id="mysearchinput" placeholder="Search students"
                class="form-control ps-3 py-1 text-reg text-lg-12 text-14"
                style="padding-right: 45px; padding-left: 27px;">
            <span class="material-symbols-outlined" style="position: absolute; right: 20px; top: 50%; transform: translateY(-50%);
                color: #2c2c2c; font-size: 24px;">search</span>
        </div>
    </div>

    <!-- Count -->
    <div class="row p-0">
        <h3 class="text-sbold text-18 my-4 p-0" id="mystudentcount">
            <?= $count . ($count === 1 ? ' student' : ' students'); ?>
        </h3>
    </div>

    <!-- Student List -->
    <div class="row p-0" id="mystudentlist">
        <?php if ($count > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div onclick="window.location='profile.php?user=<?php echo htmlspecialchars($row['userName']) ?>'" class="my-student-item align-items-center text-decoration-none p-2 mb-4 rounded-4"
                    style="padding: 14px 18px; transition: background 0.2s; display:flex">
                    <div class="rounded-circle me-3 flex-shrink-0" style="width: 40px; height: 40px; background-color: #5ba9ff;
               background-image: url('../shared/assets/pfp-uploads/<?= htmlspecialchars($row['profilePicture']) ?>');
               background-size: cover; background-position: center;">
                    </div>
                    <div class="d-flex flex-row justify-content-between w-100">
                        <div class="d-flex flex-column justify-content-center">
                            <span class="text-sbold my-student-name">
                                <?= htmlspecialchars($row['firstName'] . ' ' . $row['lastName']) ?>
                            </span>
                            <small class="text-reg my-student-username">@<?= htmlspecialchars($row['userName']) ?></small>
                        </div>
                        <div class="dropdown position-relative d-flex justify-content-end align-items-center" onclick="event.stopPropagation();">
                            <button class="btn btn-sm" type="button" style="background-color:transparent!important; border:0px;transform: none !important; box-shadow: none !important" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton" onclick="event.stopPropagation();">
                                <li>
                                    <button type="button" name="markArchived" class="dropdown-item text-reg text-14" onclick="event.stopPropagation(); window.location='report.php?enrollmentID=<?= $row['enrollmentID'] ?>'">
                                        View Report
                                    </button>
                                </li>
                                <li>
                                    <button type="button" name="markArchived" class="dropdown-item text-reg text-14 text-danger" onclick="kickoutStudent(<?= $row['enrollmentID'] ?>);">
                                        Kick Out
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-muted ps-3">No results found.</p>
        <?php endif; ?>
        <p id="my-no-results" class="text-muted text-reg text-16 p-0"
            style="display: <?= $count > 0 ? 'none' : 'block' ?>;">
            No classmates match your search. Try different keywords.
        </p>
    </div>
</div>

<script>
    (() => {
        const searchInput = document.getElementById('mysearchinput');
        const students = document.querySelectorAll('#mystudentlist .my-student-item');
        const noResults = document.getElementById('my-no-results');

        searchInput.addEventListener('keyup', () => {
            const searchTerm = searchInput.value.toLowerCase();
            let anyVisible = false;

            students.forEach(student => {
                const name = student.querySelector('.my-student-name').textContent.toLowerCase();
                const username = student.querySelector('.my-student-username').textContent.toLowerCase();

                const isMatch = name.includes(searchTerm) || username.includes(searchTerm);
                student.style.display = isMatch ? 'flex' : 'none';

                if (isMatch) anyVisible = true;
            });

            if (noResults) noResults.style.display = anyVisible ? 'none' : 'block';
        });
    })();

    function kickoutStudent(enrollmentID) {
        fetch('../shared/assets/processes/kick-out-student.php?enrollmentID=' + enrollmentID + '&userID=<?php echo $userID ?>', {
                method: 'POST',
            })
            .then(data => {
                alert("Successfully kicked out student.");
                window.location.reload();
            })
            .catch(error => {
                console.error("There was a problem with your request.");
                window.location.reload();
            });
    }
</script>