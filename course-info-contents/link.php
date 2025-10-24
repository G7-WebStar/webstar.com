<?php
$fileQuery = "SELECT * FROM files WHERE courseID = '$courseID'";
$fileResult = executeQuery($fileQuery);
?>

<?php if (mysqli_num_rows($fileResult) > 0): ?>
    <!-- Sort By Dropdown (Shown only when there are files) -->
    <div class="d-flex align-items-center flex-nowrap mb-1" id="header">
        <div class="d-flex align-items-center flex-nowrap">
            <span class="dropdown-label me-2 text-reg">Sort by</span>
            <div class="custom-dropdown">
                <button class="dropdown-btn text-reg text-14">Newest</button>
                <ul class="dropdown-list text-reg text-14">
                    <li data-value="Newest">Newest</li>
                    <li data-value="Oldest">Oldest</li>
                    <li data-value="Unread">Unread</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Files list -->
    <div class="d-flex flex-column flex-nowrap overflow-y-auto overflow-x-hidden"
        style="max-height: 70vh;">
        <?php while ($file = mysqli_fetch_assoc($fileResult)): ?>
            <?php
            // Get link and title
            $fileLink = $file['fileLink'];
            $fileTitle = $file['fileTitle'];
            ?>
            <div class="row mb-0 mt-2">
                <div class="col">
                    <a href="<?php echo htmlspecialchars($fileLink); ?>"
                        target="_blank" rel="noopener noreferrer"
                        style="text-decoration: none; color: inherit; display: block;">
                        <div class="todo-card d-flex align-items-stretch p-2">
                            <div class="d-flex w-100 align-items-center">

                                <!-- File Info -->
                                <div class="d-flex align-items-center flex-grow-1">
                                    <div class="mx-4 d-flex align-items-center">
                                        <span class="material-symbols-outlined" style="line-height: 1;">
                                            public
                                        </span>
                                    </div>
                                    <div>
                                        <div class="text-sbold text-16 py-1" style="line-height: 1;">
                                            <?php echo htmlspecialchars($fileTitle); ?>
                                        </div>
                                        <div class="text-reg text-12" style="line-height: 1;">
                                            <?php echo htmlspecialchars($fileLink); ?>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </a>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

<?php else: ?>
    <!-- No Files Placeholder -->
    <div class="empty-state text-center">
        <img src="shared/assets/img/courseInfo/bookmark.png"
            alt="No Links"
            class="empty-state-img">
        <div class="empty-state-text text-16 d-flex flex-column align-items-center">
            <p class="text-med mb-0">No links yet.</p>
            <p class="text-reg">Links from announcements and quests appear here.</p>
        </div>
    </div>
<?php endif; ?>
