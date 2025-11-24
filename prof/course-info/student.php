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
        enrollments.enrollmentID,
        courses.courseTitle,
        courses.courseCode
    FROM enrollments
    INNER JOIN users ON enrollments.userID = users.userID
    INNER JOIN userinfo ON users.userID = userinfo.userID
    INNER JOIN courses ON enrollments.courseID = courses.courseID
    WHERE users.role = 'student' AND enrollments.courseID = '$courseID'
    ORDER BY userinfo.lastName ASC
";

$result = mysqli_query($conn, $query);
$count = mysqli_num_rows($result);
?>
<?php if ($count > 0): ?>
    <div class="container-fluid">
        <!-- Search bar -->
        <div class="row align-items-center justify-content-start flex-column flex-md-row">
            <div class="col-8 col-sm-6 col-md-12 col-lg-6 d-flex search-container mb-2 mb-lg-0 p-0 position-relative">
                <input type="text" id="mysearchinput" placeholder="Search students"
                    class="form-control ps-3 py-1 text-reg text-lg-12 text-14"
                    style="font-size:13px!important; padding:6px 14px!important">
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
            <?php if ($count > 0):
                $i = 1;
                ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <div onclick="window.location='profile.php?user=<?php echo htmlspecialchars($row['userName']) ?>'"
                        class="my-student-item align-items-center text-decoration-none px-2 py-0 mb-4 rounded-4"
                        style="padding: 14px 18px; transition: background 0.2s; transform: none !important; border-radius:0px !important;box-shadow: none !important; display:flex">
                        <div class="rounded-circle me-3 flex-shrink-0" style="width: 40px; height: 40px; background-color: #5ba9ff;
               background-image: url('../shared/assets/pfp-uploads/<?= htmlspecialchars($row['profilePicture']) ?>');
               background-size: cover; background-position: center;">
                        </div>
                        <div class="d-flex flex-row justify-content-between w-100" style="min-width:0;">
                            <div class="d-flex flex-column justify-content-center"  style="min-width:0;">
                               <span class="text-sbold my-student-name"
                                style="display:block; min-width:0; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                <?= htmlspecialchars($row['firstName'] . ' ' . $row['lastName']) ?>
                            </span>
                                <small class="text-reg my-student-username">@<?= htmlspecialchars($row['userName']) ?></small>
                            </div>
                            <div class="dropdown position-relative d-flex justify-content-end align-items-center z-3"
                                onclick="event.stopPropagation();">
                                <button class="btn btn-sm" id="dot<?php echo $i; ?>" type="button"
                                    style="background-color:transparent!important; border:0px;transform: none !important; box-shadow: none !important"
                                    id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu me-5" aria-labelledby="dropdownMenuButton"
                                    onclick="event.stopPropagation();">
                                    <li>
                                        <button type="button" name="markArchived" class="dropdown-item text-reg text-14"
                                            onclick="event.stopPropagation(); window.location='report.php?enrollmentID=<?= $row['enrollmentID'] ?>'">
                                            View Report
                                        </button>
                                    </li>
                                    <li>
                                        <button type="button" name="markArchived" class="dropdown-item text-reg text-14 text-danger"
                                            data-bs-toggle="modal" data-bs-target="#kick<?php echo $i; ?>">
                                            Kick Out
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <!-- Kick Student Modal -->
                    <div class="modal fade" id="kick<?php echo $i; ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered py-4" style="max-width: 700px;  height: 25px;">
                            <div class="modal-content">

                                <!-- HEADER -->
                                <div class="modal-header border-bottom">
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>


                                <div class="modal-body">
                                    <div class="container">
                                        <div class="row justify-content-center">
                                            <div class="col-12 d-flex justify-content-center flex-column text-center text-30">
                                                <p class="text-bold">Kick Out Student</p>
                                            </div>
                                            <div class="row">
                                                <div class="mx-auto col-8 text-center mb-2">
                                                    <div class="mx-auto rounded-circle" style="width: 100px; height: 100px; background-color: #5ba9ff;
                                                            background-image: url('../shared/assets/pfp-uploads/<?= htmlspecialchars($row['profilePicture']) ?>');
                                                            background-size: cover; background-position: center;">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12 mb-3">
                                                    <p class="confirm-text text-16 text-sm-14 text-center text-reg">
                                                        Are you sure you want to kick
                                                        <span
                                                            class="text-sbold"><?= htmlspecialchars($row['firstName'] . ' ' . $row['lastName']) ?></span>
                                                        from
                                                        <br>
                                                        <span
                                                            class="text-sbold"><?= htmlspecialchars($row['courseTitle'] . " | " . $row['courseCode']) ?></span>?
                                                    </p>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                                <!-- FOOTER -->
                                <div class="modal-footer justify-content-center">
                                    <button id="kickBtn<?php echo $i; ?>"
                                        class="my-auto text-sbold btn d-flex align-items-center justify-content-center rounded-5 px-lg-4 py-lg-2"
                                        style="background-color: rgba(248, 142, 142, 1); border: 1px solid var(--black);"
                                        data-bs-toggle="modal" data-bs-target="#kick<?php echo $i; ?>"
                                        onclick="kickoutStudent(<?= $row['enrollmentID'] ?>, 'dot<?php echo $i; ?>');">
                                        <span class="m-0 fs-sm-6 text-16 text-sbold">Kick Student</span>
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>
                    <?php $i++; ?>
                <?php endwhile; ?>
            <?php else: ?>

            <?php endif; ?>
            <p id="my-no-results" class="text-muted text-reg text-16 p-0"
                style="display: <?= $count > 0 ? 'none' : 'block' ?>;">
                No students match your search.
            </p>
        </div>
    </div>
<?php else: ?>
    <!-- EMPTY STATE -->
    <div class="empty-state text-center">
        <img src="../shared/assets/img/empty/student.png" alt="No Stars" class="empty-state-img">
        <div class="empty-state-text text-14 d-flex flex-column align-items-center">
            <p class="text-med mb-1">No students enrolled yet.</p>
            <p class="text-reg">Wait for students to join to see profiles here.</p>
        </div>
    </div>
<?php endif; ?>
<script>
    //Function to show toast with icon
    function showToast(message, type = 'success') {
        const alert = document.createElement('div');
        alert.className = `alert mb-2 shadow-lg d-flex align-items-center gap-2 px-3 py-2 
                       ${type === 'success' ? 'alert-success' : 'alert-danger'}`;
        alert.style.opacity = "0";
        alert.style.transition = "opacity 0.3s ease";
        alert.style.pointerEvents = "none";

        alert.innerHTML = `
                <i class="fa-solid ${type === 'success' ? 'fa-circle-check' : 'fa-circle-xmark'}"></i>
                <span>${message}</span>
            `;

        document.getElementById('toastContainer').appendChild(alert);

        //Fade in
        setTimeout(() => alert.style.opacity = "1", 10);

        //Fade out & remove after 3s
        setTimeout(() => {
            alert.style.opacity = "0";
            setTimeout(() => alert.remove(), 300);
        }, 3000);
    }
</script>
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

    function kickoutStudent(enrollmentID, btnID) {
        document.getElementById(btnID).disabled = true;
        fetch('../shared/assets/processes/kick-out-student.php?enrollmentID=' + enrollmentID + '&userID=<?php echo $userID ?>'+ '&courseID=<?php echo $courseID ?>', {
            method: 'POST',
        })
            .then(data => {
                window.location.reload();
            })
            .catch(error => {
                window.location.reload();
            });
    }
</script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        //Get all dropdown elements
        const dropdowns = document.querySelectorAll('.dropdown');

        dropdowns.forEach(dropdown => {
            dropdown.addEventListener('show.bs.dropdown', function () {
                //Close all other dropdowns
                dropdowns.forEach(d => {
                    if (d !== dropdown) {
                        const bsDropdown = bootstrap.Dropdown.getInstance(d.querySelector('button'));
                        if (bsDropdown) bsDropdown.hide();
                    }
                });
            });
        });
    });
</script>