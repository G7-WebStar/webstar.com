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
    <div class="row mb-3">
        <div class="col-12 col-md-6 mb-2 d-flex flex-column flex-md-row align-items-start align-items-md-center">
            <div class="text-bold text-20">
                Send Feedback
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 text-reg text-14 mb-3"
            style="white-space: normal; word-wrap: break-word; text-align: justify;">
            We’d love to hear from you! Tell us what you like, what could be better, or any <br> ideas you have to make
            Webstar more fun and effective.
        </div>
    </div>
    <div class="row">
        <form action="" method="POST" class="col-12 d-flex flex-column align-items-start" style="max-width:500px;">
            <textarea name="feedback" class="form-control mb-3" placeholder="Feedback"
                style="width:100%; height:200px; border-radius:15px; border:1px solid var(--black);"></textarea>

            <div class="d-flex justify-content-end w-100">
                <button type="submit" class="btn rounded-5 px-4 text-reg text-12"
                    style="background-color: var(--primaryColor); border: 1px solid var(--black);">
                    Send
                </button>
            </div>
        </form>
    </div>
</div>