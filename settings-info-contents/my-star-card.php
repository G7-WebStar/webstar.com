<?php
$selectEnrolledQuery = "SELECT DISTINCT courses.courseID, courses.courseCode
FROM courses
INNER JOIN enrollments ON courses.courseID = enrollments.courseID
WHERE enrollments.userID = '$userID'
ORDER BY courses.courseCode ASC";

$selectEnrolledResult = executeQuery($selectEnrolledQuery);

$starCardQuery = "
    SELECT 
        users.userID,
        users.userName,
        users.role,
        userinfo.firstName,
        userinfo.lastName,
        userinfo.profilePicture,
        userinfo.schoolEmail,
        userinfo.facebookLink,
        userinfo.linkedInLink,
        userinfo.githubLink,
        userinfo.yearLevel,
        userinfo.yearSection,
        userinfo.programID,
        program.programInitial,
        profile.bio,
        profile.webstars,
        (
            SELECT COUNT(*) 
            FROM enrollments AS e 
            WHERE e.userID = users.userID
        ) AS totalEnrollments,
        (
            SELECT COUNT(*) 
            FROM studentbadges AS sb 
            WHERE sb.userID = users.userID
        ) AS totalBadges
    FROM users
    JOIN userinfo ON users.userID = userinfo.userID
    JOIN program ON userinfo.programID = program.programID
    LEFT JOIN profile ON users.userID = profile.userID
    LEFT JOIN studentbadges AS sb ON users.userID = sb.userID
    LEFT JOIN badges AS b ON sb.badgeID = b.badgeID
    WHERE users.userID = '$userID'
";
$result = mysqli_query($conn, $starCardQuery);
$user = mysqli_fetch_assoc($result);

$myItemsQuery = "
    SELECT 
        prof.bio,
        prof.webstars,
        e.emblemName AS emblemTitle,
        e.emblemPath AS emblemImg,
        c.title AS coverTitle,
        c.imagePath AS coverImg,
        t.themeName AS colorTitle,
        t.hexCode AS colorHex
    FROM profile prof
    LEFT JOIN emblem e ON prof.emblemID = e.emblemID
    LEFT JOIN coverimage c ON prof.coverImageID = c.coverImageID
    LEFT JOIN colortheme t ON prof.colorThemeID = t.colorThemeID
    WHERE prof.userID = '$userID'
";
$myItemsResult = mysqli_query($conn, $myItemsQuery);
$profile = mysqli_fetch_assoc($myItemsResult);

$starCardQuery = "
   SELECT 
        profile.starCard,
        courses.courseCode,
        courses.courseTitle
    FROM profile
    LEFT JOIN courses ON profile.starCard = courses.courseID
    WHERE profile.userID = '$userID'
";

$starCardResult = mysqli_query($conn, $starCardQuery);
$starCard = mysqli_fetch_assoc($starCardResult);


?>

<style>
    /* Sort dropdown */
    .custom-dropdown {
        position: relative;
        display: inline-block;
    }

    /* Dropdown Button */
    .dropdown-btn {
        background-color: var(--pureWhite);
        border: 1px solid var(--black);
        border-radius: 25px;
        padding: 3px 14px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: relative;
    }

    .dropdown-btn::after {
        content: '';
        display: inline-block;
        width: 0;
        height: 0;
        border-left: 5px solid transparent;
        border-right: 5px solid transparent;
        border-top: 5px solid var(--black);
        margin-left: 8px;
    }

    /* Dropdown list */
    .dropdown-list {
        position: absolute;
        top: 100%;
        left: 0;
        margin-top: 8px;
        padding: 0;
        list-style: none;
        border: 1px solid var(--black);
        border-radius: 15px;
        background-color: #fff;
        display: none;
        z-index: 100;
        overflow: hidden;
        width: max-content;
        min-width: 100%;
        white-space: nowrap;
        padding: 10px 0px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    /* Mobile dropdown */
    @media (max-width: 500px) {
        .mobile-dropdown {
            width: 100% !important;
            display: flex;
            justify-content: center;
        }
    }

    @media (max-width: 768px) {
        .mobile-dropdown-course-prof {
            width: 100% !important;
            display: flex;
            justify-content: center;
        }
    }

    /* Items */
    .dropdown-list li {
        padding: 8px 16px;
        cursor: pointer;
        border: none;
        text-align: start;
    }

    .star-card {
        width: 100% !important;
        aspect-ratio: 1 / 1 !important;
        align-content: center;
    }
</style>

<div class="container">
    <form id="starCardForm" method="POST">
        <input type="hidden" name="activeTab" id="activeTab" value="my-star-card">
        <input type="hidden" name="selectedCourse" id="selectedCourseInput" value="">
        <div class="row mb-2">
            <div class="col-12 col-md-6 mb-4 d-flex align-items-center">
                <button type="submit" class="btn rounded-5 text-reg text-12 mt-3"
                    style="background-color: var(--primaryColor); border: 1px solid var(--black); display:none;">
                    Save changes
                </button>
            </div>
            <div class="col-12 text-reg text-14 mb-1" style="white-space: normal; text-align: justify; width:700px">
                Your Star Card is a shareable card that showcases your weekly rank, level, and XP earned in Webstar.
                Choose which course’s Star Card to display on your profile. You can change it anytime!
            </div>
        </div>

        <div class="row d-flex justify-content-center">
            <!-- Course -->
            <div class="col-auto mobile-dropdown p-0">
                <div class="d-flex align-items-center flex-nowrap my-2">
                    <span class="dropdown-label me-2 text-reg">Courses</span>

                    <div class="custom-dropdown">
                        <button type="button" class="dropdown-btn text-reg text-14" id="dropdownBtn">
                            <?php
                            echo ($starCard['starCard'] == 0)
                                ? 'Choose a Course'
                                : htmlspecialchars($starCard['courseCode']);
                            ?>
                        </button>

                        <ul class="dropdown-list text-reg text-14">
                            <?php
                            if ($selectEnrolledResult && mysqli_num_rows($selectEnrolledResult) > 0) {
                                mysqli_data_seek($selectEnrolledResult, 0);
                                while ($course = mysqli_fetch_assoc($selectEnrolledResult)) {
                                    $courseID = $course['courseID'];
                                    $courseCode = $course['courseCode'];
                                    ?>
                                    <li data-id="<?= $courseID ?>"><?= htmlspecialchars($courseCode) ?></li>
                                    <?php
                                }
                            }
                            ?>
                        </ul>
                    </div>

                </div>
            </div>

        </div>
        <?php if ($starCard['starCard'] !== null && $starCard['starCard'] !== '0'): ?>
            <div class="row">
                <!-- My Star Card Content -->

                <div class="w-100 d-flex justify-content-center m-0 p-0 mb-1">
                    <div class="mt-3 rounded-4 p-0"
                        style="border: 1px solid var(--black); width: 250px; aspect-ratio: 1 / 1 !important;">
                        <div class="px-4 rounded-4 star-card"
                            style="background: linear-gradient(to bottom,<?= htmlspecialchars($profile['colorHex']) ?>, #FFFFFF); max-width: 350px;">
                            <div class="text-center text-12 text-sbold mb-4" style="margin-top: 30px;">
                                <span class="me-1">My Week on </span>
                                <img src="shared/assets/img/webstar-logo-black.png"
                                    style="width: 80px; height: 100%; object-fit: cover; margin-top:-5px"
                                    alt="Profile Picture">
                            </div>

                            <div class="d-flex justify-content-center text-decoration-none pb-2">
                                <div class="rounded-circle flex-shrink-0 me-2 overflow-hidden d-flex justify-content-center align-items-center"
                                    style="width: 40px; height: 40px; border: 1px solid var(--black); box-shadow: inset 0 0 0 2px rgba(0, 0, 0, 0.8);">
                                    <img src="shared/assets/pfp-uploads/<?= htmlspecialchars($user['profilePicture']) ?>"
                                        style="width: 100%; height: 100%; object-fit: cover;" alt="Profile Picture">
                                </div>
                                <div class="d-flex flex-column justify-content-center text-12">
                                    <span
                                        class="text-sbold"><?= htmlspecialchars($user['firstName'] . ' ' . $user['lastName']) ?></span>
                                    <small class="text-reg">@<?= htmlspecialchars($user['userName']) ?></small>
                                </div>
                            </div>

                            <div class="d-flex flex-column justify-content-center text-14 mt-1">
                                <span class="text-bold text-center"><?= htmlspecialchars($starCard['courseCode']) ?></span>
                                <small
                                    class="text-reg text-center"><?= htmlspecialchars($starCard['courseTitle']) ?></small>
                            </div>

                            <div class="stats mt-3 mb-1">
                                <div class="d-flex justify-content-between align-items-center text-center">
                                    <div class="flex-fill text-center mx-1 text-14">
                                        <div class="text-bold">2</div>
                                        <small class="text-med text-muted text-12">level</small>
                                    </div>
                                    <div class="flex-fill text-center mx-1 text-14">
                                        <div class="text-bold">3</div>
                                        <small class="text-med text-muted text-12"> rank</small>
                                    </div>
                                    <div class="flex-fill text-center mx-1 text-14">
                                        <div class="text-bold">340</div>
                                        <small class="text-med text-muted text-12">XPs</small>
                                    </div>
                                </div>
                            </div>

                            <div class="emblem">
                                <div class="h-100 d-flex justify-content-center align-items-center py-2">
                                    <img src="shared/assets/img/shop/emblems/<?= htmlspecialchars($profile['emblemImg']) ?>"
                                        class="img-fluid"
                                        style="max-height: 250px; width: 100%; height: auto; object-fit: contain;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="d-flex justify-content-center mt-3">
                <button type="button" class="btn btn-sm px-3 rounded-pill text-med text-14"
                    style="background-color: <?= htmlspecialchars($profile['colorHex']) ?>; border: 1px solid var(--black);"
                    onclick="exportCardAsJPG()" title="Download your Star Card and share it with your friends!"
                    data-bs-toggle="tooltip" data-bs-placement="left">
                    <div style="display: flex; align-items: center; gap: 5px;">
                        <i class="fa-solid fa-share"></i>
                        <span>Share</span>
                    </div>
                </button>
            </div>
        <?php endif; ?>
    </form>
</div>


<!-- Dropdown js -->


<script>
    const dropdownBtn = document.getElementById('dropdownBtn');
    const dropdownList = dropdownBtn.nextElementSibling;
    const selectedCourseInput = document.getElementById('selectedCourseInput');

    dropdownBtn.addEventListener('click', () => {
        dropdownList.style.display = dropdownList.style.display === 'block' ? 'none' : 'block';
    });

    dropdownList.querySelectorAll('li').forEach(item => {
        item.addEventListener('click', () => {
            const courseID = item.dataset.id;
            const courseCode = item.textContent;

            dropdownBtn.textContent = courseCode; // update button text
            selectedCourseInput.value = courseID; // set hidden input
            dropdownList.style.display = 'none';

            // submit form immediately
            item.closest('form').submit();
        });
    });

    document.addEventListener('click', (e) => {
        if (!dropdownBtn.parentElement.contains(e.target)) {
            dropdownList.style.display = 'none';
        }
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
<script>
    function exportCardAsJPG() {
        const card = document.querySelector('.star-card');
        html2canvas(card, {
            scale: window.devicePixelRatio * 3,
            useCORS: true,
            backgroundColor: null,
            logging: false,
            onclone: (clonedDoc) => {
                const clonedCard = clonedDoc.querySelector('.star-card');
                clonedCard.classList.remove('rounded-4');
                clonedCard.querySelectorAll('.rounded-4').forEach(el => el.classList.remove('rounded-4'));
            }
        }).then(canvas => {
            const link = document.createElement('a');
            link.download = 'star-card-highres.png';
            link.href = canvas.toDataURL('image/png', 1.0);
            link.click();
        });
    }
</script>