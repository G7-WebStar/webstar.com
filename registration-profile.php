<?php $activePage = 'signUp'; ?>
<?php
include("shared/assets/database/connect.php");
include("shared/assets/processes/registration-profile-process.php");
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webstar | Registration | Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="shared/assets/css/registration-profile.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- Material Design Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp" />

</head>

<body>
    <div class="container-fluid min-vh-100 d-flex justify-content-center align-items-center" style="background: var(--black);">
        <div class="registration-container">

            <!-- Header -->
            <div class="w-100 d-none d-md-flex flex-column justify-content-center header-part">
                <p class="text-bold mb-1" style="font-size: 28px; color: var(--black); margin-top: 5px;">Set up your profile</p>
                <p class="text-med text-12 mb-0" style="color: var(--black);">
                    Complete your profile to help others recognize you and <br> connect with you on the platform
                </p>
            </div>

            <div class="d-block d-md-none text-center p-3">
                <!-- Optional logo -->
                <div class="mb-3 mt-5 text-center">
                    <img src="shared/assets/img/webstar-logo-black.png" alt="Logo" class="img-fluid" style="width: 200px;">
                </div>

                <!-- Move the heading & instruction here -->
                <p class="text-bold mb-1 mt-5" style="font-size: 24px; color: var(--black);">Set up your profile</p>
                <p class="text-med text-12" style="color: var(--black);">
                    Complete your profile to help others recognize you and connect with you on the platform
                </p>
            </div>

            <!-- Registration Form -->
            <div class="container p-4 d-flex justify-content-center">
                <form method="POST" action="" class="w-100" id="registrationForm" enctype="multipart/form-data">
                    <div class="row justify-content-center" style="max-width: 900px;">

                        <!-- Left: Registration Form -->
                        <div class="col-12 col-md-auto text-reg custom-forms px-3 px-md-0">
                            <div>
                                <div class="mb-4 mt-2 text-med text-16 text-center text-md-start" style="color: var(--black);">
                                    Basic Information
                                </div>

                                <!-- First & Middle Name -->
                                <div class="row mb-3 gx-3">
                                    <div class="col">
                                        <div class="form-floating">
                                            <input type="text" name="firstName" class="form-control" id="firstName" placeholder="" pattern="[A-Za-z\s]+" oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '')">
                                            <label for="firstName">First Name</label>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-floating">
                                            <input type="text" name="middleName" class="form-control" id="middleName" placeholder="" pattern="[A-Za-z\s]+" oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '')">
                                            <label for="middleName">Middle Name</label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Last Name & Username -->
                                <div class="row mb-3 gx-3">
                                    <div class="col">
                                        <div class="form-floating">
                                            <input type="text" name="lastName" class="form-control" id="lastName" placeholder="Last Name" pattern="[A-Za-z\s]+" oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '')">
                                            <label for="lastName">Last Name</label>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-floating">
                                            <input type="text" name="userName" class="form-control" id="userName" placeholder="Username" maxlength="30" pattern="^[^\s.]+$">
                                            <label for="userName">Username</label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Student No. -->
                                <div class="mb-3">
                                    <div class="form-floating">
                                        <input type="text" name="studentID" class="form-control" id="studentID" placeholder="Student No.">
                                        <label for="studentNo">Student No.</label>
                                    </div>
                                </div>

                                <!-- Program -->
                                <div class="mb-3">
                                    <div class="form-floating">
                                        <select class="form-select" name="program" id="program" required>
                                            <option selected disabled>Program</option>
                                            <?php
                                            if ($programResult && mysqli_num_rows($programResult) > 0) {
                                                while ($row = mysqli_fetch_assoc($programResult)) {
                                                    echo '<option value="' . $row['programID'] . '">' . $row['programName'] . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                        <label for="program">Program</label>
                                    </div>
                                </div>

                                <!-- Gender,Year, Section -->
                                <div class="row gx-2 mb-3 align-dropdowns">
                                    <div class="col-4 col-sm-4 mb-3">
                                        <select class="form-select" name="gender" id="gender">
                                            <option selected disabled>Gender</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                    <div class="col-4 col-sm-4 mb-3">
                                        <select class="form-select" name="yearLevel" id="yearLevel">
                                            <option selected disabled>Year Level</option>
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                            <option value="4">4</option>
                                        </select>
                                    </div>
                                    <div class="col-4 col-sm-4 mb-3">
                                        <select class="form-select" name="yearSection" id="yearSection">
                                            <option selected disabled>Section</option>
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                            <option value="4">4</option>
                                        </select>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- Right: Profile Picture -->
                        <div class="col-md-auto d-flex flex-column align-items-center ps-md-3 px-3">
                            <div class="profile-pic">
                                <img id="profilePreview" src="https://via.placeholder.com/150" alt="Profile Picture">
                            </div>

                            <div style="width: 100%; display: flex; justify-content: center;">
                                <p class="text-med text-12 mt-2" style="color: var(--black); text-align: center; max-width: 150px;">
                                    Upload a JPG, PNG, or GIF file, up to 5 MB in size.
                                </p>
                            </div>

                            <input type="file" id="fileInput" name="fileUpload" class="form-control"
                                accept=".png, .jpg, .jpeg" required style="display:none;">

                            <button type="button" class="text-reg text-14 btn btn-upload mt-1" id="uploadBtn">Upload Photo</button>

                            <div class="d-flex justify-content-start justify-content-md-center w-100 mt-5">
                                <button type="submit" name="nextBtn" class="next-btn d-flex align-items-center gap-2 text-med text-14"
                                    style="background: none; border: none; padding: 0; cursor: pointer; color: var(--black); text-decoration: none;">
                                    <span>Next</span>
                                    <i class="fa-solid fa-arrow-right" style="font-size: 12px; color: var(--black);"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Toast Container -->
    <div id="toastContainer"
        class="position-absolute top-0 start-50 translate-middle-x pt-5 pt-md-1 d-flex flex-column align-items-center text-med text-14"
        style="z-index:1100; pointer-events:none;">
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const fileInput = document.getElementById('fileInput');
        const uploadBtn = document.getElementById('uploadBtn');
        const profilePreview = document.getElementById('profilePreview');
        const usernameInput = document.getElementById('userName');
        const toastContainer = document.getElementById('toastContainer');
        const studentIDInput = document.getElementById('studentID');

        studentIDInput.addEventListener('input', () => {
            studentIDInput.value = studentIDInput.value.toUpperCase();
        });

        // Auto-lowercase username and remove spaces/dots
        usernameInput.addEventListener('input', () => {
            usernameInput.value = usernameInput.value.toLowerCase().replace(/[\s.]/g, '');
        });

        // Trigger file input
        uploadBtn.addEventListener('click', () => {
            fileInput.click();
        });

        // Function to show toast with icon
        function showToast(message, type = 'success') {
            const alert = document.createElement('div');
            alert.className = `alert mb-2 shadow-lg d-flex align-items-center gap-2 px-3 py-2 ${type === 'success' ? 'alert-success' : 'alert-danger'}`;
            alert.style.transition = "opacity 0.5s ease";
            alert.style.opacity = "1";

            // Add icon using Bootstrap icons
            const icon = document.createElement('i');
            icon.className = `bi ${type === 'success' ? 'bi-check-circle-fill' : 'bi-x-circle-fill'} fs-6`;
            icon.setAttribute('role', 'img');
            icon.style.color = 'black';

            const text = document.createElement('div');
            text.className = 'text-med text-12';
            text.innerText = message;

            alert.appendChild(icon);
            alert.appendChild(text);
            toastContainer.appendChild(alert);

            setTimeout(() => {
                alert.style.opacity = "0";
                setTimeout(() => alert.remove(), 500);
            }, 3000);
        }

        // File validation and preview
        fileInput.addEventListener('change', () => {
            const file = fileInput.files[0];
            if (file) {
                const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                const maxSize = 5 * 1024 * 1024; // 5 MB

                if (!allowedTypes.includes(file.type)) {
                    fileInput.value = '';
                    profilePreview.src = 'https://via.placeholder.com/150';
                    showToast('Invalid file type. Only JPG, JPEG, and PNG are allowed.', 'danger');
                    return;
                }

                if (file.size > maxSize) {
                    fileInput.value = '';
                    profilePreview.src = 'https://via.placeholder.com/150';
                    showToast('File is too large. Maximum size is 5 MB.', 'danger');
                    return;
                }

                const reader = new FileReader();
                reader.onload = e => {
                    profilePreview.src = e.target.result;
                    showToast('Image uploaded successfully!', 'success');
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>

</html>