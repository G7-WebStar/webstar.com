<?php
$fileQuery = "SELECT * FROM files WHERE courseID = '$courseID'";
$fileResult = executeQuery($fileQuery);
?>
<!-- Sort By Dropdown (Front-End Only, above announcements) -->
<div class="d-flex align-items-center flex-nowrap mb-3" id="header">
    <span class="dropdown-label me-2">Sort by:</span>
    <button class="btn dropdown-toggle dropdown-custom" type="button"
        data-bs-toggle="dropdown" aria-expanded="false">
        <span>Newest</span>
    </button>
    <ul class="dropdown-menu">
        <li><a class="dropdown-item text-reg" href="#">Newest</a></li>
        <li><a class="dropdown-item text-reg" href="#">Oldest</a></li>
        <li><a class="dropdown-item text-reg" href="#">Unread first</a></li>
    </ul>
</div>

<div class="d-flex flex-column flex-nowrap overflow-y-auto overflow-x-hidden"
    style="max-height: 70vh;">
    <?php if (mysqli_num_rows($fileResult) > 0): ?>
        <?php while ($file = mysqli_fetch_assoc($fileResult)): ?>
            <?php
            // Get the link and title from database
            $fileLink = $file['fileLink'];
            $fileTitle = $file['fileTitle'];
            ?>

            <div class="row mb-0 mt-2">
                <div class="col">
                    <a href="<?php echo $fileLink; ?>" target="_blank" rel="noopener noreferrer"
                        style="text-decoration: none; color: inherit; display: block;">
                        <div class="todo-card d-flex align-items-stretch p-2">
                            <div class="d-flex w-100 align-items-center">

                                <!-- File Info -->
                                <div class="d-flex align-items-center flex-grow-1">
                                    <div class="mx-4">
                                        <i class="fa-solid fa-link" style="font-size: 16px;"></i>
                                    </div>
                                    <div>
                                        <div class="text-sbold text-16 py-1" style="line-height: 1;">
                                            <?php echo htmlspecialchars($fileTitle); ?>
                                        </div>
                                        <div class="text-reg text-12" style="line-height: 1;">
                                            <?php echo $fileLink; ?>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </a>
                </div>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>
</div>