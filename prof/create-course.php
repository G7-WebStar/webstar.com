<?php $activePage = 'create-course'; ?>
<?php
include('../shared/assets/database/connect.php');
date_default_timezone_set('Asia/Manila');
include("../shared/assets/processes/prof-session-process.php");

if (isset($_POST['createCourse'])) {
    $courseTitle = $_POST['courseTitle'];
    $courseCode = strtoupper($_POST['courseCode']); // convert to uppercase
    $section = strtoupper($_POST['section']);
    $userID = $_SESSION['userID'];

    // handle image
    $courseImage = '';
    if (isset($_FILES['fileUpload']) && $_FILES['fileUpload']['error'] === 0) {
        $courseImage = $_FILES['fileUpload']['name'];
        $tmpFile = $_FILES['fileUpload']['tmp_name'];
        $folder = "../shared/assets/img/course-images/";
        move_uploaded_file($tmpFile, $folder . $courseImage);
    }

    // Generate unique 6-character access code
    function generateAccessCode($length = 6)
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomCode = '';
        for ($i = 0; $i < $length; $i++) {
            $randomCode .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomCode;
    }

    do {
        $accessCode = generateAccessCode(6); // random 6-character alphanumeric
        $checkCodeQuery = "SELECT * FROM courses WHERE code = '$accessCode'";
        $codeExists = executeQuery($checkCodeQuery);
    } while (mysqli_num_rows($codeExists) > 0);


    $insertCourse = "
        INSERT INTO courses (userID, courseTitle, courseCode, section, courseImage, code)
        VALUES ('$userID', '$courseTitle', '$courseCode', '$section', '$courseImage', '$accessCode')
    ";
    $courseResult = executeQuery($insertCourse);

    if ($courseResult) {
        $courseID = mysqli_insert_id($conn);

        // Insert schedule
        $days = $_POST['selectedDay'] ?? [];
        $startTimes = $_POST['startTime'] ?? [];
        $endTimes = $_POST['endTime'] ?? [];

        for ($i = 0; $i < count($days); $i++) {
            $day = mysqli_real_escape_string($conn, $days[$i]);
            $start = mysqli_real_escape_string($conn, $startTimes[$i]);
            $end = mysqli_real_escape_string($conn, $endTimes[$i]);

            if (!empty($day) && !empty($start) && !empty($end)) {
                $insertSchedule = "
                    INSERT INTO courseschedule (courseID, day, startTime, endTime, createdAt)
                    VALUES ('$courseID', '$day', '$start', '$end', NOW())
                ";
                executeQuery($insertSchedule);
            }
        }

        header("Location: courses.php");
        exit();
    } else {
        echo "<script>alert('Error creating course. Please try again.');</script>";
    }
}

?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webstar | Create Course</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="../shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="../shared/assets/css/post-announcement.css">
    <link rel="stylesheet" href="../shared/assets/css/add-lesson.css">
    <link rel="stylesheet" href="../shared/assets/css/sidebar-and-container-styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../shared/assets/img/webstar-icon.png">

    <!-- Material Design Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,1,0"
        rel="stylesheet" />


    <style>
        .btn-upload:hover {
            background-color: var(--primaryColor);
        }

        .course-image-upload {
            width: 100%;
        }

        .image-preview-wrapper {
            position: relative;
            width: 100%;
            padding-top: 56.25%;
            border: 1px solid var(--black);
            background-color: var(--primaryColor);
            overflow: hidden;
            cursor: pointer;
        }

        .image-preview-wrapper img {
            position: absolute;
            top: 0;
            left: 0;
            justify-content: center;
            width: 105%;
            height: 105%;
            margin: -5px;
            object-fit: cover;
        }

        /* Default Create Existing Course Button Styles */
        .create-ex-course {
            width: 100% !important;
            margin-top: 10px !important;
        }

        /* Create Existing Course Button Styles for bigger screens*/
        @media (min-width: 1090px) {
            .create-ex-course {
                width: auto !important;
                margin-top: 0px !important;
            }
        }

        .sched-desktop {
            display: none;
        }

        .sched-mobile {
            display: block
        }

        /* Close Button Default Styles */
        .remove-row {
            margin-top: 38px !important;
        }

        /* Medium Screen */
        @media (min-width: 768px) {
            .schedule-col {
                width: 100%;
            }

            .sched-mobile {
                display: block;
            }

            .sched-desktop {
                display: none;
            }

            .class-sched-col {
                width: 80%;
            }

            .start-time-col {
                width: 80%;
            }

            .end-time-col {
                width: 70%;
            }

            .remove-row {
                margin-top: 38px !important;
            }
        }

        /* Medium - Larger Screen */
        @media (min-width: 1135px) {
            .sched-mobile {
                display: none;
            }

            .sched-desktop {
                display: block;
            }

            .class-sched-col {
                width: 25%;
            }

            .start-time-col {
                width: 33.33333333%;
            }

            .end-time-col {
                width: 33.33333333%;
            }

            .remove-row {
                margin-top: 7.5px !important;
            }
        }

        /* Create Prof Row Default Styles */
        .create-prof-row {
            width: 100% !important;
        }

        /* Medium screens */
        @media (min-width: 768px) and (max-width: 1159px) {
            .create-prof-row {
                width: 100% !important;
            }
        }

        /* Medium - Larger screens */
        @media (min-width: 1160px) {
            .create-prof-row {
                width: 80% !important;
            }
        }

        .custom-dropdown {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        .dropdown-btn {
            background-color: #fff;
            border: 1px solid var(--black);
            border-radius: 10px;
            padding: 0.375rem 0.75rem;
            cursor: pointer;
            min-width: 120px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: calc(2.25rem + 2px);
        }

        .dropdown-btn::after {
            content: '';
            border-left: 5px solid transparent;
            border-right: 5px solid transparent;
            border-top: 5px solid var(--black);
            display: inline-block;
            margin-left: 8px;
        }

        .dropdown-list {
            position: absolute;
            top: 100%;
            left: 0;
            margin-top: 4px;
            padding: 0;
            list-style: none;
            border: 1px solid var(--black);
            border-radius: 0.375rem;
            background-color: #fff;
            width: 100%;
            display: none;
            z-index: 100;
            overflow-y: auto;
        }

        .dropdown-list li {
            padding: 0.375rem 0.75rem;
            cursor: pointer;
        }

        .form-control {
            border: 1px solid var(--black);
            border-radius: 10px;
        }

        .remove-row {
            user-select: none;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="container-fluid min-vh-100 d-flex justify-content-center align-items-center p-0 p-md-3"
        style="background-color: var(--black);">

        <div class="row w-100">

            <!-- Sidebar (mobile) -->
            <?php include '../shared/components/prof-sidebar-for-mobile.php'; ?>

            <!-- Sidebar (desktop) -->
            <?php include '../shared/components/prof-sidebar-for-desktop.php'; ?>

            <!-- Main Container -->
            <div class="col main-container m-0 p-0 mx-0 mx-md-2 p-md-4 overflow-y-auto">
                <div class="card border-0 px-3 pt-3 m-0 h-100 w-100 rounded-0 shadow-none"
                    style="background-color: transparent;">

                    <!-- Navbar (mobile) -->
                    <?php include '../shared/components/prof-navbar-for-mobile.php'; ?>

                    <div class="container-fluid py-3 overflow-y-auto row-padding-top">
                        <div class="create-prof-row" style="margin-bottom: 100px;">
                            <div class="col-12">
                                <!-- Header -->
                                <div
                                    class="row mb-3 justify-content-center justify-content-md-start align-items-center">
                                    <div class="col-8 m-0 p-0 create-prof-row">
                                        <div class="row m-0 p-0 justify-content-center align-items-center">
                                            <!-- Back Arrow -->
                                            <div class="col-auto d-none d-md-block">
                                                <a href="javascript:history.back()" class="text-decoration-none">
                                                    <i class="fa-solid fa-arrow-left text-reg text-16"
                                                        style="color: var(--black);"></i>
                                                </a>
                                            </div>

                                            <!-- Page Title -->
                                            <div class="col text-center text-md-start">
                                                <span class="text-sbold text-20">Create Course</span>
                                            </div>

                                            <!-- Create an existing course Button -->
                                            <div
                                                class="col-12 col-md-auto create-ex-course text-center d-flex d-md-block justify-content-center justify-content-md-end mt-3 mt-md-0">
                                                <button type="button"
                                                    class="btn btn-sm px-3 py-1 rounded-pill text-reg text-md-14 my-1 d-flex align-items-center gap-2"
                                                    style="background-color: var(--primaryColor); border: 1px solid var(--black); color: var(--black);"
                                                    data-bs-toggle="modal" data-bs-target="#reuseTaskModal">
                                                    <span class="material-symbols-rounded"
                                                        style="font-size:16px">folder</span>
                                                    <span>Create an existing course</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Form starts -->
                                <form action="" method="POST" enctype="multipart/form-data">
                                    <div class="row">
                                        <!-- Course Information -->
                                        <div class="col-12 col-md-8 pt-3 create-prof-row">
                                            <label for="taskInfo" class="form-label text-med text-16">Course
                                                Information</label>
                                            <input type="text"
                                                class="form-control textbox mb-2 px-3 py-2 text-reg text-16"
                                                id="taskInfo" name="courseTitle" placeholder="Course Title *"
                                                value="<?php echo isset($reusedData) ? htmlspecialchars($reusedData['assessmentTitle']) : ''; ?>"
                                                required>
                                            <input type="text"
                                                class="form-control textbox mb-2 px-3 py-2 text-reg text-16"
                                                id="taskInfo" name="courseCode" placeholder="Course Code *"
                                                value="<?php echo isset($reusedData) ? htmlspecialchars($reusedData['assessmentTitle']) : ''; ?>"
                                                required>
                                            <input type="text"
                                                class="form-control textbox mb-2 px-3 py-2 text-reg text-16"
                                                id="taskInfo" name="section" placeholder="Section *"
                                                value="<?php echo isset($reusedData) ? htmlspecialchars($reusedData['assessmentTitle']) : ''; ?>"
                                                required>
                                        </div>
                                        <!-- Course Image -->
                                        <div class="col-8 pt-3 create-prof-row">
                                            <div class="text-med text-16">
                                                <div style="margin-bottom:.5rem">Course Image</div>
                                                <div class="course-image-upload">
                                                    <div class="image-preview-wrapper rounded-4 mb-3">
                                                        <img id="profilePreview" />
                                                    </div>
                                                    <input type="file" id="fileInput" name="fileUpload"
                                                        class="form-control" accept=".png, .jpg, .jpeg"
                                                        style="display:none;">
                                                    <div class="text-med text-12 mt-3"
                                                        style="color: var(--black); text-align: start;">
                                                        Upload a JPG, JPEG, or PNG file, up to 10 MB in size.
                                                    </div>
                                                    <div class="d-flex justify-content-start">
                                                        <button type="button"
                                                            class="btn btn-sm px-3 py-1 rounded-pill text-reg text-md-14 mt-3"
                                                            style="background-color: var(--primaryColor); border: 1px solid var(--black);"
                                                            id="uploadBtn">
                                                            <div style="display: flex; align-items: center; gap: 5px;">
                                                                <span class="material-symbols-rounded"
                                                                    style="font-size:16px"></span>
                                                                <span>Upload Photo</span>
                                                            </div>
                                                        </button>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        <!-- Class Schedule Labels for Desktop-->
                                        <div class="row p-0 m-0 mt-5">
                                            <div class="col-12 col-md-3">
                                                <label for="dropdown-btn"
                                                    class="form-label text-med sched-desktop">Class
                                                    Schedule *</label>
                                            </div>
                                            <div class="col-12 col-md-4">
                                                <label for="timeInput" class="form-label text-med sched-desktop">Start
                                                    Time *</label>
                                            </div>
                                            <div class="col-12 col-md-4">
                                                <label for="timeInput" class="form-label text-med sched-desktop">End
                                                    Time *</label>
                                            </div>
                                        </div>
                                        <!-- Class Schedule Input -->
                                        <div id="schedule-wrapper">
                                            <div class="row text-16 text-reg schedule-row">
                                                <div class="col-12 col-md-3 class-sched-col">
                                                    <label for="dropdown-btn"
                                                        class="form-label text-med sched-mobile">Class Schedule
                                                        *</label>
                                                    <div class="custom-dropdown day-select mb-3">
                                                        <div class="dropdown-btn" id="dayDropdownBtn"
                                                            data-value="Monday">Monday</div>
                                                        <ul class="dropdown-list py-1" id="dayDropdownList">
                                                            <li data-value="Monday">Monday</li>
                                                            <li data-value="Tuesday">Tuesday</li>
                                                            <li data-value="Wednesday">Wednesday</li>
                                                            <li data-value="Thursday">Thursday</li>
                                                            <li data-value="Friday">Friday</li>
                                                            <li data-value="Saturday">Saturday</li>
                                                            <li data-value="Sunday">Sunday</li>
                                                        </ul>
                                                    </div>
                                                </div>

                                                <!-- Start Time -->
                                                <div class="col-12 col-md-4 start-time-col">
                                                    <div class="mb-3">
                                                        <label for="timeInput"
                                                            class="form-label text-med sched-mobile">Start Time
                                                            *</label>
                                                        <input type="time" class="form-control start-time"
                                                            id="timeInput" name="startTime[]" required>
                                                    </div>
                                                </div>

                                                <!-- End Time -->
                                                <div class="col-10 col-md-4 end-time-col">
                                                    <div class="mb-3">
                                                        <label for="timeInput"
                                                            class="form-label text-med sched-mobile">End Time
                                                            *</label>
                                                        <input type="time" class="form-control end-time" id="timeInput"
                                                            name="endTime[]" required>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="text-med text-12 mt-1 mb-3"
                                                style="color: var(--black); text-align: start;">
                                                Maximum of 3 schedule dates allowed.
                                            </div>
                                        </div>
                                        <!-- Add Schedule Date -->
                                        <div class="row">
                                            <div class="w-100 d-flex justify-content-start">
                                                <button type="button"
                                                    class="btn btn-sm px-3 py-1 rounded-pill text-reg text-md-14"
                                                    style="background-color: var(--primaryColor); border: 1px solid var(--black);">
                                                    <div style="display: flex; align-items: center; gap: 5px;"
                                                        id="add-schedule">
                                                        <span class="material-symbols-rounded"
                                                            style="font-size:16px"></span>
                                                        <span>+ Add Schedule Date</span>
                                                    </div>
                                                </button>
                                            </div>
                                        </div>
                                        <!-- Create Course Button -->
                                        <div class="col-8 mt-5 w-100 d-flex justify-content-center">
                                            <div class="col-12 col-md-auto mt-3 mt-md-0 text-center w-100">
                                                <button type="submit" name="createCourse"
                                                    class="px-4 py-2 rounded-5 text-sbold text-md-14 mt-4 mt-md-0"
                                                    style="background-color: var(--primaryColor); border: 1px solid var(--black);">
                                                    <?php echo isset($reusedData) ? 'Recreate Course' : 'Create'; ?>
                                                </button>
                                            </div>
                                        </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const wrapper = document.getElementById('schedule-wrapper');
            const addBtn = document.getElementById('add-schedule');
            const maxRows = 3;

            function initDropdown(dropdown) {
                const dropdownBtn = dropdown.querySelector('.dropdown-btn');

                // Ensure a hidden input exists for this dropdown
                let hiddenInput = dropdown.querySelector('input[name="selectedDay[]"]');
                if (!hiddenInput) {
                    hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'selectedDay[]';
                    hiddenInput.value = dropdownBtn.textContent.trim(); // default value
                    dropdown.appendChild(hiddenInput);
                }

                dropdownBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const list = dropdown.querySelector('.dropdown-list');
                    list.style.display = list.style.display === 'block' ? 'none' : 'block';
                });

                dropdown.querySelectorAll('.dropdown-list li').forEach(item => {
                    item.addEventListener('click', () => {
                        const selectedValue = item.getAttribute('data-value');
                        dropdownBtn.textContent = selectedValue;
                        hiddenInput.value = selectedValue; // update hidden input
                        dropdown.querySelector('.dropdown-list').style.display = 'none';
                    });
                });

                document.addEventListener('click', (e) => {
                    if (!dropdown.contains(e.target)) {
                        dropdown.querySelector('.dropdown-list').style.display = 'none';
                    }
                });
            }

            function attachRemove(row) {
                const removeIcon = row.querySelector('.remove-row');
                if (removeIcon) {
                    removeIcon.addEventListener('click', () => {
                        row.remove();
                    });
                }
            }

            // Initialize existing dropdowns
            document.querySelectorAll('.custom-dropdown').forEach(initDropdown);

            // Add new schedule row
            addBtn.addEventListener('click', () => {
                const currentRows = wrapper.querySelectorAll('.schedule-row').length;
                if (currentRows >= maxRows) return;

                const firstRow = wrapper.querySelector('.schedule-row');
                const newRow = firstRow.cloneNode(true);

                const dropdownBtn = newRow.querySelector('.dropdown-btn');
                dropdownBtn.textContent = 'Monday';

                // Remove old input if exists
                const oldInput = newRow.querySelector('input[name="selectedDay[]"]');
                if (oldInput) oldInput.remove();

                // Add new hidden input with default value
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'selectedDay[]';
                input.value = 'Monday';
                newRow.querySelector('.custom-dropdown').appendChild(input);

                newRow.querySelector('.start-time').value = '';
                newRow.querySelector('.end-time').value = '';

                // Remove old remove button if exists
                const oldRemove = newRow.querySelector('.remove-row');
                if (oldRemove) oldRemove.parentElement.remove();

                const removeCol = document.createElement('div');
                removeCol.className = 'col-2 col-md-1 text-end';
                const removeIcon = document.createElement('span');
                removeIcon.className = 'material-symbols-rounded remove-row';
                removeIcon.textContent = 'close';
                removeIcon.style.cssText = 'font-size:24px; cursor:pointer; margin-top:5px; user-select:none !important;';
                removeCol.appendChild(removeIcon);
                newRow.appendChild(removeCol);

                wrapper.appendChild(newRow);

                initDropdown(newRow.querySelector('.custom-dropdown'));
                attachRemove(newRow);
            });
        });

    </script>

    <script>
        // File validation and preview
        const fileInput = document.getElementById('fileInput');
        const uploadBtn = document.getElementById('uploadBtn');
        const profilePreview = document.getElementById('profilePreview');

        // Trigger file input when button is clicked
        uploadBtn.addEventListener('click', () => {
            fileInput.click();
        });

        fileInput.addEventListener('change', () => {
            const file = fileInput.files[0];
            if (file) {
                const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                const maxSize = 10 * 1024 * 1024; // 10 MB

                if (!allowedTypes.includes(file.type)) {
                    alert('Invalid file type. Only JPG, JPEG, and PNG are allowed.');
                    fileInput.value = '';
                    profilePreview.src = 'https://via.placeholder.com/150';
                    return;
                }

                if (file.size > maxSize) {
                    alert('File is too large. Maximum size is 10 MB.');
                    fileInput.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = e => {
                    // Append timestamp to force browser to reload
                    profilePreview.src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });

    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>