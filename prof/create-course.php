<?php $activePage = 'create-course'; ?>
<?php
include('../shared/assets/database/connect.php');
date_default_timezone_set('Asia/Manila');
include("../shared/assets/processes/prof-session-process.php");
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

        /* Default: Mobile (xs to sm) */
        .create-prof-row {
            width: 100%;
        }

        /* Medium screens (md: ≥768px and <1200px) */
        @media (min-width: 1160px) and (max-width: 1599px) {
            .create-prof-row {
                width: 90%;
            }
        }

        /* Large screens (lg: ≥1600px and <2000px) */
        @media (min-width: 1600px) {
            .create-prof-row {
                width: 70%;
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
                                <div class="row mb-3 align-items-center">

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

                                    <!-- Assign Existing Task Button -->
                                    <div
                                        class="col-12 col-md-auto text-center d-flex d-md-block justify-content-center justify-content-md-end mt-3 mt-md-0">
                                        <button type="button"
                                            class="btn btn-sm me-4 px-3 py-1 rounded-pill text-reg text-md-14 mt-1 d-flex align-items-center gap-2"
                                            style="background-color: var(--primaryColor); border: 1px solid var(--black); color: var(--black);"
                                            data-bs-toggle="modal" data-bs-target="#reuseTaskModal">
                                            <span class="material-symbols-rounded" style="font-size:16px">folder</span>
                                            <span>Create an existing course</span>
                                        </button>
                                    </div>
                                </div>


                                <!-- Form starts -->
                                <form action="" method="POST" enctype="multipart/form-data">
                                    <div class="row">
                                        <div class="col-12 col-md-8 pt-3">
                                            <label for="taskInfo" class="form-label text-med text-16">Course
                                                Information</label>
                                            <input type="text"
                                                class="form-control textbox mb-2 px-3 py-2 text-reg text-16"
                                                id="taskInfo" name="assignmentTitle" placeholder="Course Title *"
                                                value="<?php echo isset($reusedData) ? htmlspecialchars($reusedData['assessmentTitle']) : ''; ?>"
                                                required>
                                            <input type="text"
                                                class="form-control textbox mb-2 px-3 py-2 text-reg text-16"
                                                id="taskInfo" name="assignmentTitle" placeholder="Course Code *"
                                                value="<?php echo isset($reusedData) ? htmlspecialchars($reusedData['assessmentTitle']) : ''; ?>"
                                                required>
                                            <input type="text"
                                                class="form-control textbox mb-2 px-3 py-2 text-reg text-16"
                                                id="taskInfo" name="assignmentTitle" placeholder="Section *"
                                                value="<?php echo isset($reusedData) ? htmlspecialchars($reusedData['assessmentTitle']) : ''; ?>"
                                                required>
                                        </div>
                                        <div class="col-12 col-md-4 pt-3">
                                            <div class="text-med text-16">
                                                <div style="margin-bottom:.6rem">Course Image</div>
                                                <div class="course-image-upload pe-4">
                                                    <div class="image-preview-wrapper rounded-4">
                                                        <img id="imagePreview" />
                                                    </div>
                                                    <input type="file" id="fileInput" name="fileUpload"
                                                        class="form-control" accept=".png, .jpg, .jpeg" required
                                                        style="display:none;">
                                                    <div class="text-med text-12 mt-2 w-100 text-center"
                                                        style="color: var(--black); text-align: center;">
                                                        Upload a JPG, JPEG, or PNG file, <br>up to 10 MB in size.
                                                    </div>
                                                    <div class="w-100 d-flex justify-content-center">
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
                                </form>
                                <div class="row p-0 m-0 mt-5 c">
                                    <div class="col-12 col-md-3">
                                        <label for="dropdown-btn" class="form-label text-med d-none d-md-block">Class
                                            Schedule</label>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label for="timeInput" class="form-label text-med d-none d-md-block">Start
                                            Time</label>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label for="timeInput" class="form-label text-med d-none d-md-block">End
                                            Time</label>
                                    </div>
                                </div>
                                <div id="schedule-wrapper">
                                    <div class="row text-16 text-reg schedule-row">
                                        <div class="col-12 col-md-3">
                                            <label for="dropdown-btn"
                                                class="form-label text-med d-block d-md-none">Class
                                                Schedule</label>
                                            <div class="custom-dropdown day-select mb-3">
                                                <div class="dropdown-btn" id="dayDropdownBtn">Select Day</div>
                                                <ul class="dropdown-list" id="dayDropdownList">
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
                                        <div class="col-12 col-md-4">
                                            <div class="mb-3">
                                                <label for="timeInput"
                                                    class="form-label text-med d-block d-md-none">Start
                                                    Time</label>
                                                <input type="time" class="form-control start-time" id="timeInput">
                                            </div>

                                        </div>
                                        <div class="col-12 col-md-4">
                                            <div class="mb-3">
                                                <label for="timeInput" class="form-label text-med d-block d-md-none">End
                                                    Time</label>
                                                <input type="time" class="form-control end-time" id="timeInput">
                                            </div>

                                        </div>
                                        <div class="col-12 col-md-1">
                                            <span class="material-symbols-rounded remove-row"
                                                style="font-size:24px; cursor:pointer; margin-top:5px">
                                                close
                                            </span>
                                        </div>


                                    </div>
                                </div>

                                <div class="row">
                                    <div class="w-100 d-flex justify-content-start">
                                        <button type="button"
                                            class="btn btn-sm px-3 py-1 rounded-pill text-reg text-md-14"
                                            style="background-color: var(--primaryColor); border: 1px solid var(--black);">
                                            <div style="display: flex; align-items: center; gap: 5px;"
                                                id="add-schedule">
                                                <span class="material-symbols-rounded" style="font-size:16px"></span>
                                                <span>+ Add Schedule Date</span>
                                            </div>
                                        </button>
                                    </div>
                                </div>
                                <div class="row mt-5">
                                    <div class="col-12 col-md-auto mt-3 mt-md-0 text-center w-100">
                                        <button type="submit" name="saveAssignment"
                                            class="px-4 py-2 rounded-pill text-sbold text-md-14 mt-4 mt-md-0"
                                            style="background-color: var(--primaryColor); border: 1px solid var(--black);">
                                            <?php echo isset($reusedData) ? 'Recreate Course' : 'Create'; ?>
                                        </button>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const dropdownBtn = document.getElementById('dayDropdownBtn');
        const dropdownList = document.getElementById('dayDropdownList');

        // Toggle dropdown
        dropdownBtn.addEventListener('click', () => {
            dropdownList.style.display = dropdownList.style.display === 'block' ? 'none' : 'block';
        });

        // Select item
        dropdownList.querySelectorAll('li').forEach(item => {
            item.addEventListener('click', () => {
                dropdownBtn.textContent = item.textContent;
                dropdownList.style.display = 'none';
            });
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!dropdownBtn.contains(e.target) && !dropdownList.contains(e.target)) {
                dropdownList.style.display = 'none';
            }
        });
    </script>

    <script>
        const wrapper = document.getElementById('schedule-wrapper');
        const addBtn = document.getElementById('add-schedule');
        const maxRows = 3;

        addBtn.addEventListener('click', () => {
            const currentRows = wrapper.querySelectorAll('.schedule-row').length;
            if (currentRows >= maxRows) return; // Stop if already 3

            // Clone the first row
            const firstRow = wrapper.querySelector('.schedule-row');
            const newRow = firstRow.cloneNode(true);

            // Clear inputs
            newRow.querySelector('.dropdown-btn').textContent = 'Select Day';
            newRow.querySelector('.start-time').value = '';
            newRow.querySelector('.end-time').value = '';

            wrapper.appendChild(newRow);

            // Make the dropdown in the new row work
            const dropdownBtn = newRow.querySelector('.dropdown-btn');
            const dropdownList = newRow.querySelector('.dropdown-list');

            dropdownBtn.addEventListener('click', () => {
                dropdownList.style.display = dropdownList.style.display === 'block' ? 'none' : 'block';
            });

            dropdownList.querySelectorAll('li').forEach(item => {
                item.addEventListener('click', () => {
                    dropdownBtn.textContent = item.textContent;
                    dropdownList.style.display = 'none';
                });
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', (e) => {
                if (!dropdownBtn.contains(e.target) && !dropdownList.contains(e.target)) {
                    dropdownList.style.display = 'none';
                }
            });

            function attachRemoveBtn(row) {
                const btn = row.querySelector('.remove-row');
                if (btn) {
                    btn.addEventListener('click', () => {
                        row.remove();
                    });
                }
            }

            // Initialize for existing rows
            wrapper.querySelectorAll('.schedule-row').forEach(attachRemoveBtn);

        });
    </script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>