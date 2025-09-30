<?php
$fileQuery = "SELECT * FROM files WHERE courseID = '$courseID'";
$fileResult = executeQuery($fileQuery);

?>

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

        <div class="row mb-0 mt-3">
            <div class="col">
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
                            <a href="<?php echo $filePath; ?>"
                                <?php if (!preg_match('/^https?:\/\//', $filePath)) : ?>
                                download="<?php echo htmlspecialchars($file['fileAttachment']); ?>"
                                <?php endif; ?>>
                                <img src="shared/assets/img/dl.png"
                                    alt="Download Icon"
                                    style="width: 16px; height: 20px; cursor:pointer;">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
<?php endif; ?>