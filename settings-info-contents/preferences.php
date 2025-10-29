<?php
$result = executeQuery("SELECT * FROM settings WHERE userID = '$userID'");
$settings = mysqli_fetch_assoc($result);

if (isset($_POST['save'])) {
    $courseUpdateEnabled = isset($_POST['courseUpdateEnabled']) ? 1 : 0;
    $questDeadlineEnabled = isset($_POST['questDeadlineEnabled']) ? 1 : 0;
    $announcementEnabled = isset($_POST['announcementEnabled']) ? 1 : 0;

    executeQuery("
        UPDATE settings SET 
            courseUpdateEnabled = '$courseUpdateEnabled',
            questDeadlineEnabled = '$questDeadlineEnabled',
            announcementEnabled = '$announcementEnabled'
        WHERE userID = '$userID'
    ");

    $result = executeQuery("SELECT * FROM settings WHERE userID = '$userID'");
    $settings = mysqli_fetch_assoc($result);
}
?>

<div class="container mt-3">
    <form method="POST">
        <input type="hidden" name="activeTab" value="preferences">
        <div class="row mb-3">
            <div class="col-12 col-md-6 mb-2 d-flex align-items-center">
                <div class="text-bold text-20 mb-0">Email Notification</div>
                <button type="submit" name="save" class="btn rounded-5 text-reg text-12 ms-3" style="background-color: var(--primaryColor); border: 1px solid var(--black);">
                    Save changes
                </button>
            </div>
            <div class="col-12 text-reg text-14 mb-1" style="white-space: normal; text-align: justify;">
                Manage your email notifications and stay updated on your courses, quests, and announcements.
            </div>
        </div>

        <!-- Course Updates -->
        <div class="row">
            <div class="col d-flex flex-column gap-3">
                <div class="d-flex flex-column">
                    <div class="d-flex align-items-center mb-1 flex-wrap">
                        <div class="form-check form-switch m-0 me-2 d-flex align-items-center">
                            <input class="form-check-input" type="checkbox" name="courseUpdateEnabled" id="courseUpdatesToggle" style="margin-top: 0;"
                                <?php if ($settings['courseUpdateEnabled']) echo 'checked'; ?>>
                        </div>
                        <label for="courseUpdatesToggle" class="toggle text-sbold text-16 mb-0">Course Updates</label>
                    </div>
                    <div class="text-reg text-14" style="text-align: justify;">
                        Get notified when new lessons or materials are added to your enrolled courses.
                    </div>
                </div>
            </div>
        </div>

        <!-- Quests and Deadlines -->
        <div class="row mt-3">
            <div class="col d-flex flex-column">
                <div class="d-flex align-items-center mb-1 flex-wrap">
                    <div class="form-check form-switch m-0 me-2 d-flex align-items-center">
                        <input class="form-check-input" type="checkbox" name="questDeadlineEnabled" id="questsToggle" style="margin-top: 0;"
                            <?php if ($settings['questDeadlineEnabled']) echo 'checked'; ?>>
                    </div>
                    <label for="questsToggle" class="toggle text-sbold text-16 mb-0">Quests and Deadlines</label>
                </div>
                <div class="text-reg text-14" style="text-align: justify;">
                    Receive reminders for upcoming or missing quests.
                </div>
            </div>
        </div>

        <!-- Announcements -->
        <div class="row mt-3">
            <div class="col d-flex flex-column">
                <div class="d-flex align-items-center mb-1 flex-wrap">
                    <div class="form-check form-switch m-0 me-2 d-flex align-items-center">
                        <input class="form-check-input" type="checkbox" name="announcementEnabled" id="announcementsToggle" style="margin-top: 0;"
                            <?php if ($settings['announcementEnabled']) echo 'checked'; ?>>
                    </div>
                    <label for="announcementsToggle" class="toggle text-sbold text-16 mb-0">Announcements</label>
                </div>
                <div class="text-reg text-14" style="text-align: justify;">
                    Stay informed about important updates and announcements from your courses.
                </div>
            </div>
        </div>
    </form>
</div>
