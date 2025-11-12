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

// Handle Save Changes
if (isset($_POST['saveChanges'])) {
    $firstName = $_POST['firstName'] ?? '';
    $middleName = $_POST['middleName'] ?? '';
    $lastName = $_POST['lastName'] ?? '';
    $userName = $_POST['userName'] ?? '';
    $studentID = $_POST['studentID'] ?? '';
    $programID = $_POST['program'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $yearLevel = $_POST['yearLevel'] ?? '';
    $yearSection = $_POST['yearSection'] ?? '';
    $schoolEmail = $_POST['schoolEmail'] ?? '';
    $fbLink = $_POST['fbLink'] ?? '';
    $linkedInLink = $_POST['linkedInLink'] ?? '';
    $githubLink = $_POST['githubLink'] ?? '';

    // Handle profile picture upload
    $uploadField = isset($_FILES['fileUpload']) ? 'fileUpload' : (isset($_FILES['fileUploadMobile']) ? 'fileUploadMobile' : null);

    if ($uploadField && isset($_FILES[$uploadField]) && $_FILES[$uploadField]['error'] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES[$uploadField]['tmp_name'];
        $fileName = basename($_FILES[$uploadField]['name']);
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExt = ['jpg', 'jpeg', 'png'];
        if (in_array($fileExt, $allowedExt)) {
            $newFileName = 'profile_' . $userID . '_' . time() . '.' . $fileExt;
            $uploadDir = "shared/assets/pfp-uploads/";
            if (!is_dir($uploadDir))
                mkdir($uploadDir, 0777, true);
            $uploadPath = $uploadDir . $newFileName;
            if (move_uploaded_file($fileTmp, $uploadPath)) {
                $profilePicture = $newFileName;
            }
        }
    }

    // Update users table
    executeQuery("UPDATE users SET username='$userName' WHERE userID='$userID'");

    // Update userinfo table
    $updateInfoQuery = "
        UPDATE userinfo SET
            firstName='$firstName',
            middleName='$middleName',
            lastName='$lastName',
            studentID='$studentID',
            gender='$gender',
            yearLevel='$yearLevel',
            yearSection='$yearSection',
            schoolEmail='$schoolEmail',
            facebookLink='$fbLink',
            linkedInLink='$linkedInLink',
            githubLink='$githubLink',
            programID='$programID'
    ";
    if (!empty($profilePicture))
        $updateInfoQuery .= ", profilePicture='$profilePicture'";
    $updateInfoQuery .= " WHERE userID='$userID'";

    $result = executeQuery($updateInfoQuery);
}
?>
<div class="container">
    <div class="row d-flex justify-content-center">
        <form method="POST" action="" class="w-100" id="registrationForm" enctype="multipart/form-data">
            <!-- Top Row -->
            <div class="row mb-3">
                <div class="col-12 col-md-6 mb-2 d-flex align-items-center">
                    <button type="submit" name="saveChanges" id="saveChangesBtn" class="btn rounded-5 mt-3 text-reg text-12"
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
                                    value="<?php echo $firstName ?? ''; ?>">
                                <label for="firstName" class="text-reg text-16">First Name</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-floating">
                                <input type="text" name="middleName" class="form-control" id="middleName"
                                    placeholder=" " value="<?php echo $middleName ?? ''; ?>">
                                <label for="middleName" class="text-reg text-16">Middle Name</label>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-2 gx-2">
                        <div class="col">
                            <div class="form-floating">
                                <input type="text" name="lastName" class="form-control" id="lastName" placeholder=" "
                                    value="<?php echo $lastName ?? ''; ?>">
                                <label for="lastName" class="text-reg text-16">Last Name</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-floating">
                                <input type="text" name="userName" class="form-control" id="userName" placeholder=" "
                                    value="<?php echo $userName ?? ''; ?>">
                                <label for="userName" class="text-reg text-16">Username</label>
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
                                        placeholder=" " value="<?php echo $schoolEmail ?? ''; ?>">
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
                    <div
                        class="col-12 col-md-auto text-reg custom-forms px-2 px-md-0 text-center d-flex justify-content-center">
                        <form action="reset_password.php" method="post">
                            
                            <button type="submit" class="btn rounded-5 text-med text-12 px-5"
                                style="background-color: var(--primaryColor); border: 1px solid var(--black);">
                                Reset Password
                            </button>
                        </form>
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

<script>
    const fileInput = document.getElementById('fileInput');
    const uploadBtn = document.getElementById('uploadBtn');
    const fileInputMobile = document.getElementById('fileInputMobile');
    const uploadBtnMobile = document.getElementById('uploadBtnMobile');
    const profilePreviewDesktop = document.getElementById('profilePreviewDesktop');
    const profilePreviewMobile = document.getElementById('profilePreviewMobile');
    const saveBtn = document.getElementById('saveChangesBtn');

    function showSaveButton() {
        saveBtn.style.display = 'inline-block';
    }

    // 🖥️ Desktop upload
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

    // 📱 Mobile upload (FIXED)
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
</script>