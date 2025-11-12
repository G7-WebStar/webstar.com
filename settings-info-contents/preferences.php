<?php
// --- Ensure all students have default settings ---
executeQuery("
    INSERT INTO settings (userID, courseUpdateEnabled, questDeadlineEnabled, announcementEnabled)
    SELECT userID, 0, 0, 0
    FROM users
    WHERE role = 'student'
    AND userID NOT IN (SELECT userID FROM settings)
");

// --- Fetch user settings ---
$result = executeQuery("SELECT * FROM settings WHERE userID = '$userID'");
$settings = mysqli_fetch_assoc($result);

// --- If the user has no settings yet (failsafe for non-students) ---
if (!$settings) {
    executeQuery("
        INSERT INTO settings (userID, courseUpdateEnabled, questDeadlineEnabled, announcementEnabled)
        VALUES ('$userID', 0, 0, 0)
    ");
    $result = executeQuery("SELECT * FROM settings WHERE userID = '$userID'");
    $settings = mysqli_fetch_assoc($result);
}

// --- Handle save action ---
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

    // Refresh settings after update
    $result = executeQuery("SELECT * FROM settings WHERE userID = '$userID'");
    $settings = mysqli_fetch_assoc($result);
}
?>


<div class="container">
    <form id="notificationForm" method="POST">
        <input type="hidden" name="activeTab" value="preferences">
        <div class="row mb-3 mt-2">
            <div class="col-12 col-md-6 mt-2 mb-4 d-flex align-items-center">
                <button type="submit" name="save" id="saveBtn" class="btn rounded-5 text-reg text-12"
                    style="background-color: var(--primaryColor); border: 1px solid var(--black); display:none;">
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
                            <input class="form-check-input" type="checkbox" name="courseUpdateEnabled"
                                id="courseUpdatesToggle"
                                style="margin-top: 0; background-color: <?php echo $settings['courseUpdateEnabled'] ? 'var(--black)' : 'var(--gray)'; ?>; border-color: <?php echo $settings['courseUpdateEnabled'] ? 'var(--black)' : 'var(--gray)'; ?>;"
                                onchange="this.style.backgroundColor = this.checked ? 'var(--black)' : 'var(--gray)'; this.style.borderColor = this.checked ? 'var(--black)' : 'var(--gray)';"
                                <?php if ($settings['courseUpdateEnabled'])
                                    echo 'checked'; ?>>
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
                        <input class="form-check-input" type="checkbox" name="questDeadlineEnabled" id="questsToggle"
                            style="margin-top: 0; background-color: <?php echo $settings['questDeadlineEnabled'] ? 'var(--black)' : 'var(--gray)'; ?>; border-color: <?php echo $settings['questDeadlineEnabled'] ? 'var(--black)' : 'var(--gray)'; ?>;"
                            onchange="this.style.backgroundColor = this.checked ? 'var(--black)' : 'var(--gray)'; this.style.borderColor = this.checked ? 'var(--black)' : 'var(--gray)';"
                            <?php if ($settings['questDeadlineEnabled'])
                                echo 'checked'; ?>>
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
                        <input class="form-check-input" type="checkbox" name="announcementEnabled"
                            id="announcementsToggle"
                            style="margin-top: 0; background-color: <?php echo $settings['announcementEnabled'] ? 'var(--black)' : 'var(--gray)'; ?>; border-color: <?php echo $settings['announcementEnabled'] ? 'var(--black)' : 'var(--gray)'; ?>;"
                            onchange="this.style.backgroundColor = this.checked ? 'var(--black)' : 'var(--gray)'; this.style.borderColor = this.checked ? 'var(--black)' : 'var(--gray)';"
                            <?php if ($settings['announcementEnabled'])
                                echo 'checked'; ?>>
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
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('notificationForm');
        const saveBtn = document.getElementById('saveBtn');
        const toggles = form.querySelectorAll('.form-check-input');

        // Save initial states
        const initialStates = {};
        toggles.forEach(t => initialStates[t.name] = t.checked);

        // Check for changes
        toggles.forEach(t => {
            t.addEventListener('change', () => {
                const changed = Array.from(toggles).some(x => x.checked !== initialStates[x.name]);
                saveBtn.style.display = changed ? 'inline-block' : 'none';
            });
        });
    });
</script>