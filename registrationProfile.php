<?php $activePage = 'signUp'; ?>
<?php
include("shared/assets/database/connect.php");
include("shared/assets/processes/registrationProfile-process.php");
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
    <link rel="stylesheet" href="shared/assets/css/registrationProfile.css">
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
                                            <input type="text" name="firstName" class="form-control" id="firstName" placeholder=" ">
                                            <label for="firstName">First Name</label>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-floating">
                                            <input type="text" name="middleName" class="form-control" id="middleName" placeholder=" ">
                                            <label for="middleName">Middle Name</label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Last Name & Username -->
                                <div class="row mb-3 gx-3">
                                    <div class="col">
                                        <div class="form-floating">
                                            <input type="text" name="lastName" class="form-control" id="lastName" placeholder="Last Name">
                                            <label for="lastName">Last Name</label>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-floating">
                                            <input type="text" name="userName" class="form-control" id="userName" placeholder="Username">
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

                                <!-- Gender & Year -->
                                <div class="row gx-3 mb-3">
                                    <div class="col-6 col-sm-6 mb-3" style="max-width: 150px; width: 100%;">
                                        <select class="form-select" name="gender" id="gender" style="height: 48px; line-height: 48px; text-align: center; padding: 0 3.5rem 0 0.5rem;">
                                            <option selected disabled style="color: gray;">Gender</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                    <div class="col-6 col-sm-6 mb-3" style="max-width: 150px; width: 100%;">
                                        <select class="form-select" name="yearLevel" id="yearLevel"
                                            style="height: 48px; line-height: 48px; text-align: center; padding: 0 2.5rem 0 0.5rem;">
                                            <option selected disabled style="color: gray;">Year Level</option>
                                            <option value="1st">1st Year</option>
                                            <option value="2nd">2nd Year</option>
                                            <option value="3rd">3rd Year</option>
                                            <option value="4th">4th Year</option>
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
                                accept=".png, .jpg, .svg, .jpeg" required style="display:none;">

                            <button type="button" class="text-med text-12 btn btn-upload mt-1" id="uploadBtn">Upload Photo</button>

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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const fileInput = document.getElementById('fileInput');
        const uploadBtn = document.getElementById('uploadBtn');
        const profilePreview = document.getElementById('profilePreview');

        // Trigger file input when button is clicked
        uploadBtn.addEventListener('click', () => {
            fileInput.click();
        });

        // Show preview when a file is selected
        fileInput.addEventListener('change', () => {
            const file = fileInput.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = e => {
                    profilePreview.src = e.target.result; // update image src
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>

</html>