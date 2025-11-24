<?php
$userQuery = "
    SELECT 
        users.username,
        userinfo.firstName,
        userinfo.middleName,
        userinfo.lastName,
        userinfo.studentID,
        userinfo.gender,
        userinfo.yearLevel,
        userinfo.yearSection,
        userinfo.schoolEmail,
        userinfo.facebookLink,
        userinfo.linkedInLink,
        userinfo.githubLink,
        userinfo.profilePicture,
        program.programID,
        program.programName
    FROM users
    INNER JOIN userinfo ON users.userID = userinfo.userID
    INNER JOIN program ON userinfo.programID = program.programID
    WHERE users.userID = '$userID'
";
$userResult = executeQuery($userQuery);

if ($userResult && mysqli_num_rows($userResult) > 0) {
    $userData = mysqli_fetch_assoc($userResult);
    $firstName = $userData['firstName'];
    $middleName = $userData['middleName'];
    $lastName = $userData['lastName'];
    $userName = $userData['username'];
    $studentID = $userData['studentID'];
    $programID = $userData['programID'];
    $programName = $userData['programName'];
    $gender = $userData['gender'];
    $yearLevel = $userData['yearLevel'];
    $yearSection = $userData['yearSection'];
    $schoolEmail = $userData['schoolEmail'];
    $fbLink = $userData['facebookLink'];
    $linkedInLink = $userData['linkedInLink'];
    $githubLink = $userData['githubLink'];
    $profilePicture = $userData['profilePicture'];
}

// Initialize username check message
$usernameTaken = false;
$usernameTakenMessage = '';


if (isset($_POST['deleteAccount'])) {
    $userID = $_SESSION['userID'];

    // --- Delete dependent tables first ---
    $tables = [
        'activities' => 'userID',
        'announcementnotes' => 'userID',
        'enrollments' => 'userID',
        'feedback' => ['senderID', 'receiverID'],
        'files' => 'userID',
        'inbox' => 'enrollmentID',
        'leaderboard' => 'enrollmentID',
        'myitems' => 'userID',
        'profile' => 'userID',
        'report' => 'enrollmentID',
        'scores' => 'userID',
        'selectedlevels' => 'submissionID',
        'settings' => 'userID',
        'studentbadges' => 'userID',
        'submissions' => 'userID',
        'testresponses' => 'userID',
        'todo' => 'userID',
        'webstars' => 'userID'
    ];

    // --- Start deletion ---
    // 1. Fetch enrollmentIDs for this user
    $enrollmentResult = executeQuery("SELECT enrollmentID FROM enrollments WHERE userID='$userID'");
    $enrollmentIDs = [];
    while ($row = mysqli_fetch_assoc($enrollmentResult)) {
        $enrollmentIDs[] = $row['enrollmentID'];
    }
    $enrollmentIDsStr = !empty($enrollmentIDs) ? implode(',', $enrollmentIDs) : '0';

    // 2. Fetch submissionIDs
    $submissionResult = executeQuery("SELECT submissionID FROM submissions WHERE userID='$userID'");
    $submissionIDs = [];
    while ($row = mysqli_fetch_assoc($submissionResult)) {
        $submissionIDs[] = $row['submissionID'];
    }
    $submissionIDsStr = !empty($submissionIDs) ? implode(',', $submissionIDs) : '0';

    // 3. Delete from tables
    executeQuery("DELETE FROM activities WHERE userID='$userID'");
    executeQuery("DELETE FROM announcementnotes WHERE userID='$userID'");
    executeQuery("DELETE FROM feedback WHERE senderID='$userID' OR receiverID='$userID'");
    executeQuery("DELETE FROM submissions WHERE userID='$userID'");
    executeQuery("DELETE FROM selectedlevels WHERE submissionID IN ($submissionIDsStr)");
    executeQuery("DELETE FROM scores WHERE userID='$userID'");
    executeQuery("DELETE FROM testresponses WHERE userID='$userID'");
    executeQuery("DELETE FROM todo WHERE userID='$userID'");
    executeQuery("DELETE FROM webstars WHERE userID='$userID'");
    executeQuery("DELETE FROM studentbadges WHERE userID='$userID'");
    executeQuery("DELETE FROM myitems WHERE userID='$userID'");
    executeQuery("DELETE FROM profile WHERE userID='$userID'");
    executeQuery("DELETE FROM settings WHERE userID='$userID'");

    // Delete enrollments related data
    if (!empty($enrollmentIDs)) {
        executeQuery("DELETE FROM leaderboard WHERE enrollmentID IN ($enrollmentIDsStr)");
        executeQuery("DELETE FROM report WHERE enrollmentID IN ($enrollmentIDsStr)");
        executeQuery("DELETE FROM inbox WHERE enrollmentID IN ($enrollmentIDsStr)");
        executeQuery("DELETE FROM enrollments WHERE userID='$userID'");
    }

    // Finally delete user info and account
    executeQuery("DELETE FROM userinfo WHERE userID='$userID'");
    executeQuery("DELETE FROM users WHERE userID='$userID'");

    // Destroy session and redirect
    session_destroy();
    header("Location: login.php?accountDeleted=1");
    exit();
}
?>

<div class="container">
    <div class="row d-flex justify-content-center">
        <form method="POST" action="" class="w-100" id="registrationForm" enctype="multipart/form-data">
            <!-- Top Row -->
            <div class="row mb-3">
                <div class="col-12 col-md-6 mb-2 d-flex align-items-center">
                    <button type="submit" name="saveChanges" id="saveChangesBtn"
                        class="btn rounded-5 mt-3 text-reg text-12"
                        style="background-color: var(--primaryColor); border:1px solid var(--black); display:none;">Save
                        changes</button>
                </div>
            </div>

            <!-- Upload Profile Picture for mobile only -->
            <div class="d-block d-md-none mb-3 d-flex flex-column align-items-center text-center mb-5">
                <div class="profile-pic mb-2">
                    <img id="profilePreviewMobile"
                        src="<?php echo !empty($profilePicture) ? 'shared/assets/pfp-uploads/' . $profilePicture : 'https://via.placeholder.com/150'; ?>"
                        alt="Profile Picture" class="img-fluid">
                </div>

                <p class="text-med text-12 mt-2" style="color: var(--black); max-width: 150px;">
                    Upload a JPG, PNG, or GIF file, up to 5 MB in size.
                </p>

                <input type="file" id="fileInputMobile" name="fileUploadMobile" class="form-control"
                    accept=".png, .jpg, .jpeg" style="display:none;">
                <button type="button" class="text-med text-12 btn btn-upload mt-2" id="uploadBtnMobile">
                    Upload Photo
                </button>
            </div>
            <!-- FORM LEFT COLUMN -->
            <div class="row p-0 m-0 d-flex justify-content-center" style="margin-left:2px;">
                <div class="col-12 col-md-8 text-reg custom-forms px-2 px-md-0">
                    <!-- Basic Info -->
                    <div class="col mb-3 text-med text-16 text-center text-md-start" style="color:var(--black);">Basic
                        Information</div>

                    <div class="row mb-2 gx-2">
                        <div class="col">
                            <div class="form-floating">
                                <input type="text" name="firstName" class="form-control" id="firstName" placeholder=" "
                                    pattern="[A-Za-z\s]+" oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '')"
                                    value="<?php echo $firstName ?? ''; ?>" required>
                                <label for="firstName" class="text-reg text-16">First Name</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-floating">
                                <input type="text" name="middleName" class="form-control" id="middleName"
                                    placeholder=" " pattern="[A-Za-z\s]+"
                                    oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '')"
                                    value="<?php echo $middleName ?? ''; ?>">
                                <label for="middleName" class="text-reg text-16">Middle Name</label>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-2 gx-2">
                        <div class="col">
                            <div class="form-floating">
                                <input type="text" name="lastName" class="form-control" id="lastName" placeholder=" "
                                    pattern="[A-Za-z\s]+" oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '')"
                                    value="<?php echo $lastName ?? ''; ?>" required>
                                <label for="lastName" class="text-reg text-16">Last Name</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-floating">
                                <input type="text" name="userName" class="form-control" id="userName" placeholder=" "
                                    maxlength="30" pattern="^[^\s.]+$" value="<?php echo $userName ?? ''; ?>" required>
                                <label for="userName" class="text-reg text-16">Username</label>
                                <small id="usernameMsg" class="text-danger text-reg ms-1"
                                    style="font-size:11px; display:<?= !empty($usernameTaken) ? 'block' : 'none'; ?>;">
                                    <?= $usernameTakenMessage ?? '' ?>
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-2 gx-2">
                        <div class="col">
                            <!-- Student ID -->
                            <div class="form-floating">
                                <input type="text" name="studentID" class="form-control mb-2" id="studentID"
                                    placeholder=" " value="<?php echo $studentID ?? ''; ?>">
                                <label for="studentID" class="text-reg text-16">Student No.</label>
                            </div>
                            <!-- Program -->
                            <div class="form-floating">
                                <select class="form-select" name="program" id="program" required>
                                    <option disabled>Select Program</option>
                                    <option value="7" <?= ($programID == 7) ? 'selected' : ''; ?>>Bachelor of Public
                                        Administration with specialization in Fiscal Administration</option>
                                    <option value="2" <?= ($programID == 2) ? 'selected' : ''; ?>>Bachelor of Science in
                                        Electrical Engineering</option>
                                    <option value="3" <?= ($programID == 3) ? 'selected' : ''; ?>>Bachelor of Science in
                                        Electronics Engineering</option>
                                    <option value="4" <?= ($programID == 4) ? 'selected' : ''; ?>>Bachelor of Science in
                                        Entrepreneurship</option>
                                    <option value="5" <?= ($programID == 5) ? 'selected' : ''; ?>>Bachelor of Science in
                                        Industrial Engineering</option>
                                    <option value="6" <?= ($programID == 6) ? 'selected' : ''; ?>>Bachelor of Science in
                                        Information Technology</option>
                                    <option value="8" <?= ($programID == 8) ? 'selected' : ''; ?>>Bachelor of Science in
                                        Psychology</option>
                                    <option value="1" <?= ($programID == 1) ? 'selected' : ''; ?>>Bachelor of Technology
                                        and
                                        Livelihood Education ICT</option>
                                    <option value="9" <?= ($programID == 9) ? 'selected' : ''; ?>>Diploma in Information
                                        Technology</option>
                                    <option value="10" <?= ($programID == 10) ? 'selected' : ''; ?>>Diploma in Office
                                        Management Technology - Legal Office Management</option>
                                </select>
                                <label for="program" class="text-reg text-16">Program</label>
                            </div>
                        </div>
                    </div>
                    <!-- Gender -->
                    <div class="row gx-2 mb-2 align-dropdowns">
                        <div class="col-md-4 col-12 mb-3 mb-md-0">
                            <div class="form-floating">
                                <select class="form-select" name="gender" id="gender">
                                    <option value="Male" <?= ($gender == 'Male') ? 'selected' : ''; ?>>Male</option>
                                    <option value="Female" <?= ($gender == 'Female') ? 'selected' : ''; ?>>Female
                                    </option>
                                    <option value="Other" <?= ($gender == 'Other') ? 'selected' : ''; ?>>Other</option>
                                </select>
                                <label for="gender" class="text-reg text-16">Gender</label>
                            </div>
                        </div>
                        <!-- Year Level -->
                        <div class="col-md-4 col-12 mb-3 mb-md-0">
                            <div class="form-floating">
                                <select class="form-select" name="yearLevel" id="yearLevel">
                                    <option value="1" <?php echo ($yearLevel == '1') ? 'selected' : ''; ?>>1st Year
                                    </option>
                                    <option value="2" <?php echo ($yearLevel == '2') ? 'selected' : ''; ?>>2nd Year
                                    </option>
                                    <option value="3" <?php echo ($yearLevel == '3') ? 'selected' : ''; ?>>3rd Year
                                    </option>
                                    <option value="4" <?php echo ($yearLevel == '4') ? 'selected' : ''; ?>>4th Year
                                    </option>
                                </select>
                                <label for="yearLevel" class="text-reg text-16">Year Level</label>
                            </div>
                        </div>
                        <!-- Section -->
                        <div class="col-md-4 col-12 mb-3 mb-md-0">
                            <div class="form-floating">
                                <select class="form-select" name="yearSection" id="yearSection">
                                    <option value="1" <?php echo ($yearSection == '1') ? 'selected' : ''; ?>>1
                                    </option>
                                    <option value="2" <?php echo ($yearSection == '2') ? 'selected' : ''; ?>>2
                                    </option>
                                </select>
                                <label for="yearSection" class="text-reg text-16">Section</label>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="col-12 col-md-auto text-reg custom-forms px-2 px-md-0">
                        <div class="col mb-3 mt-5 text-med text-16 text-center text-md-start"
                            style="color: var(--black);">
                            Contact Information
                        </div>

                        <div class="row gx-3 flex-column">
                            <div class="col-12 ">

                                <div class="form-floating">
                                    <input type="email" name="schoolEmail" class="form-control" id="schoolEmail"
                                        placeholder=" " required value="<?php echo $schoolEmail ?? ''; ?>">
                                    <label for="schoolEmail" class="text-reg text-16">School Email</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Social Media -->
                    <div class="col-12 col-md-auto text-reg custom-forms px-2 px-md-0">
                        <div class="col mb-3 mt-5 text-med text-16 text-center text-md-start"
                            style="color: var(--black);">
                            Social Media <i>(optional)</i>
                        </div>

                        <div class="row mb-3 gx-3 flex-column">
                            <div class="col-12 mb-3">

                                <div class="form-floating mb-3">
                                    <input type="text" name="fbLink" class="form-control" id="fbLink" placeholder=" "
                                        value="<?php echo $fbLink ?? ''; ?>">
                                    <label for="fbLink" class="text-reg text-16">Facebook</label>
                                </div>

                                <div class="form-floating mb-3">
                                    <input type="text" name="linkedInLink" class="form-control" id="linkedInLink"
                                        placeholder=" " value="<?php echo $linkedInLink ?? ''; ?>">
                                    <label for="linkedInLink" class="text-reg text-16">LinkedIn</label>
                                </div>

                                <div class="form-floating">
                                    <input type="text" name="githubLink" class="form-control" id="githubLink"
                                        placeholder=" " value="<?php echo $githubLink ?? ''; ?>">
                                    <label for="githubLink" class="text-reg text-16">GitHub</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-auto text-center">

                            <a class="btn rounded-5 text-med text-12 px-5"
                                href="login-auth/forgot-password.php"
                                style="background-color: var(--primaryColor); border: 1px solid var(--black);">
                                Reset Password
                            </a>


                        </div>

                        <div class="col-auto text-center">
                            <form action="" method="post">
                                <button type="button" class="btn rounded-5 text-med text-12 px-5 mt-2 mt-md-0"
                                    style="background-color: rgba(248, 142, 142, 1); border: 1px solid var(--black);"
                                    data-bs-toggle="modal" data-bs-target="#deleteModal">
                                    Delete Account
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- RIGHT COLUMN: DESKTOP PROFILE -->
                <div
                    class="col-12 col-md-4 mt-4 pt-1 d-none d-md-flex flex-column align-items-center ps-md-3 px-3 mb-4 mx-auto">
                    <div class="profile-pic">
                        <img id="profilePreviewDesktop"
                            src="<?php echo !empty($profilePicture) ? 'shared/assets/pfp-uploads/' . $profilePicture : 'https://via.placeholder.com/150'; ?>"
                            alt="Profile Picture">
                    </div>
                    <p class="text-med text-12 mt-2" style="color:var(--black); text-align:center; max-width:150px;">
                        Upload a JPG, PNG, or GIF file, up to 5 MB in size.</p>
                    <input type="file" id="fileInput" name="fileUpload" class="form-control" accept=".png,.jpg,.jpeg"
                        style="display:none;">
                    <button type="button" class="btn btn-upload text-med text-12 mt-1" id="uploadBtn">Upload
                        Photo</button>
                </div>
            </div>
        </form>

    </div>
</div>
<!-- Delete Modal -->
<div class="modal" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        style="transform: scale(0.8); border:none !important; box-shadow:none !important;"></button>
                </div>
                <div class="modal-body d-flex flex-column justify-content-center align-items-center text-center">
                    <span class="mt-4 text-bold text-22">This action cannot be undone.</span>
                    <span class="mb-4 text-reg text-14">Are you sure you want to delete this course?</span>
                    <input type="hidden" name="courseID" value="<?php echo $courseID; ?>">
                </div>
                <div class="modal-footer text-sbold text-18">
                    <button type="button" class="btn rounded-pill px-4"
                        style="background-color: var(--primaryColor); border: 1px solid var(--black);"
                        data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="deleteAccount" class="btn rounded-pill px-4"
                        style="background-color: rgba(248, 142, 142, 1); border: 1px solid var(--black); color: var(--black);">
                        Delete
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    const fileInput = document.getElementById('fileInput');
    const uploadBtn = document.getElementById('uploadBtn');
    const fileInputMobile = document.getElementById('fileInputMobile');
    const uploadBtnMobile = document.getElementById('uploadBtnMobile');
    const profilePreviewDesktop = document.getElementById('profilePreviewDesktop');
    const profilePreviewMobile = document.getElementById('profilePreviewMobile');
    const saveBtn = document.getElementById('saveChangesBtn');
    const usernameInput = document.getElementById('userName');
    const studentIDInput = document.getElementById('studentID');
    const toastContainer = document.getElementById('toastContainer');

    function showSaveButton() {
        saveBtn.style.display = 'inline-block';
    }

    studentIDInput.addEventListener('input', () => {
        studentIDInput.value = studentIDInput.value.toUpperCase();
    });

    // Auto-lowercase username and remove spaces
    usernameInput.addEventListener('input', () => {
        usernameInput.value = usernameInput.value.toLowerCase().replace(/[^a-z0-9._]/g, '');
    });

    // Desktop upload
    uploadBtn.addEventListener('click', () => fileInput.click());
    fileInput.addEventListener('change', () => {
        const file = fileInput.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = e => (profilePreviewDesktop.src = e.target.result);
            reader.readAsDataURL(file);
            showSaveButton();
        }
    });

    // Mobile upload (FIXED)
    uploadBtnMobile.addEventListener('click', () => {
        // Make file upload required ONLY when user actually chooses a file
        fileInputMobile.required = false; // ensure it won't block the form
        fileInputMobile.click();
    });

    fileInputMobile.addEventListener('change', () => {
        const file = fileInputMobile.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = e => (profilePreviewMobile.src = e.target.result);
            reader.readAsDataURL(file);
            showSaveButton();
        }
    });

    // Show save button on any change in inputs/selects
    document.querySelectorAll('#registrationForm input, #registrationForm select').forEach(el => {
        el.addEventListener('input', showSaveButton);
        el.addEventListener('change', showSaveButton);
    });

    function showToast(message, type = 'success') {
        const toastContainer = document.getElementById('toastContainer');

        // Create a unique ID for the toast
        const toastID = 'toast_' + Date.now();

        // Prepare toast HTML
        const toastHTML = `
            <div id="${toastID}" class="alert mb-2 shadow-lg d-flex align-items-center gap-2 px-3 py-2 ${type === 'success' ? 'alert-success' : 'alert-danger'}" style="transition: opacity 0.5s ease; opacity:1;">
                <i class="bi ${type === 'success' ? 'bi-check-circle-fill' : 'bi-x-circle-fill'} fs-6" role="img" style="color:black;"></i>
                <div class="text-med text-12">${message}</div>
            </div>
        `;

        // Inject toast into container
        toastContainer.innerHTML += toastHTML;

        // Remove toast after 3s
        setTimeout(() => {
            const toast = document.getElementById(toastID);
            if (toast) {
                toast.style.opacity = '0';
                setTimeout(() => {
                    if (toast.parentNode) toast.parentNode.removeChild(toast);
                }, 500);
            }
        }, 3000);
    }

    // File validation and preview
    fileInput.addEventListener('change', () => {
        const file = fileInput.files[0];
        if (!file) return;

        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        const maxSize = 5 * 1024 * 1024; // 5 MB

        if (!allowedTypes.includes(file.type)) {
            fileInput.value = '';
            profilePreviewDesktop.src = 'https://via.placeholder.com/150';
            showToast('Invalid file type. Only JPG, JPEG, and PNG are allowed.', 'danger');
            return;
        }

        if (file.size > maxSize) {
            fileInput.value = '';
            profilePreviewDesktop.src = 'https://via.placeholder.com/150';
            showToast('File is too large. Maximum size is 5 MB.', 'danger');
            return;
        }

        const reader = new FileReader();
        reader.onload = e => {
            profilePreviewDesktop.src = e.target.result;
            showToast('Image uploaded successfully!', 'success');
        };
        reader.readAsDataURL(file);
    });
</script>