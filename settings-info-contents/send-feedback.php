<?php
// Handle feedback form submission
if (isset($_POST['feedback'])) {
    $feedback = trim($_POST['feedback']);

    if ($userID && !empty($feedback)) {
        // Find the admin (receiver)
        $adminResult = executeQuery("SELECT userID FROM users WHERE role = 'admin' LIMIT 1");
        $adminData = mysqli_fetch_assoc($adminResult);

        if ($adminData) {
            $receiverID = $adminData['userID'];

            // Insert feedback directly (simple version)
            executeQuery("
                INSERT INTO feedback (senderID, receiverID, message)
                VALUES ('$userID', '$receiverID', '$feedback')
            ");
        }
    }
}
?>

<div class="container">
    <div class="row mt-4">
        <div class="col-12 text-reg text-14 mb-3"
            style="white-space: normal; word-wrap: break-word; text-align: justify;">
            We’d love to hear from you! Tell us what you like, what could be better, or any <br> ideas you have to make
            Webstar more fun and effective.
        </div>
    </div>
    <div class="row">
        <form action="" method="POST" class="col-12 d-flex flex-column align-items-start" style="max-width:600px;">
            <textarea name="feedback" class="form-control mb-3 text-reg p-3" placeholder="Feedback"
                style="width:100%; height:200px; border-radius:10px; border:1px solid var(--black);"></textarea>

            <div class="d-flex justify-content-end w-100">
                <button type="submit" class="btn rounded-5 px-4 text-med text-12"
                    style="background-color: var(--primaryColor); border: 1px solid var(--black);">
                    Send
                </button>
            </div>
        </form>
    </div>
</div>