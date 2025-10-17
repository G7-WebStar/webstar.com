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
            // Decide if link is external or local
            $filePath = $file['fileLink'];

            // If it’s not starting with http, treat it as local upload
            if (!preg_match('/^https?:\/\//', $filePath)) {
                $filePath = $file['fileLink']; // already like uploads/STC Merge.pdf
            }
            ?>

            <div class="row mb-0 mt-2">
                <div class="col">
                    <a href="<?php echo $filePath; ?>"
                        <?php if (!preg_match('/^https?:\/\//', $filePath)) : ?>
                        download="<?php echo htmlspecialchars($file['fileAttachment']); ?>"
                        <?php endif; ?>
                        style="text-decoration: none; color: inherit; display: block;">
                        <div class="todo-card d-flex align-items-stretch p-2">
                            <div class="d-flex w-100 align-items-center justify-content-between">

                                <!-- File Info -->
                                <div class="d-flex align-items-center flex-grow-1">
                                    <div class="mx-4">
                                        <img src="shared/assets/img/doc.png" alt="File Icon" style="width: 16px; height: 20px;">
                                    </div>
                                    <div>
                                        <div class="text-sbold text-16 py-1" style="line-height: 1;">
                                            <?php echo htmlspecialchars($file['fileAttachment']); ?>
                                        </div>
                                        <div class="text-reg text-12" style="line-height: 1;">
                                            Uploaded <?php echo date("F d, Y", strtotime($file['uploadedAt'])); ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Download Button -->
                                <div class="mx-4">
                                    <img src="shared/assets/img/dl.png"
                                        alt="Download Icon"
                                        style="width: 16px; height: 20px; cursor:pointer;">
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>
</div>