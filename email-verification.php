<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>WebStar | Email Verification</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="shared/assets/css/email-verification.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="shared/assets/img/webstar-icon.png">

    <!-- Material Design Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp" />

</head>


<body>
    <div class="container min-vh-100 d-flex justify-content-center align-items-center">
        <div class="row">
            <div class="col-12 col-md-6 col-lg-4 p-3 rounded-4 login-container border-blue mx-auto">
                <div class="container d-flex justify-content-center py-sm-4 py-3">
                    <img src="shared/assets/img/webstar-logo-black.png" class="img-fluid px-3 my-4 logo" width="275px">
                </div>


                <!-- Check Your Email Heading -->
                <div class="container text-center mb-4">
                    <h1 class="check-email-heading">Check your email</h1>
                </div>

                <!-- Check Your Email Description -->
                <div class="container text-center mb-4">
                    <p class="check-email-description">Input the code that was sent to your email.</p>
                </div>

                <!-- Code Input Form -->
                <form method="POST" action="">
                    <div class="container login-form py-md-2">
                        <div class="form-floating">
                            <input type="text" name="code" class="input-style form-control rounded-4 border-blue"
                                id="floatingInput" placeholder="0 0 0 0 0 0" maxlength="6" required>
                        </div>
                    </div>
                    <br class="mobile-only">

                    <!-- Bootstrap Icons (only include once in your layout) 
                    <svg xmlns="http://www.w3.org/2000/svg" class="d-none">
                        <symbol id="exclamation-triangle-fill" viewBox="0 0 16 16">
                            <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 
                                     1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 
                                     1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 
                                     0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 
                                     5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
                        </symbol>
                    </svg>-->

                    <!-- Error Alert 
                    <div class="alert alert-danger d-flex align-items-center alert-dismissible fade show custom-alert"
                        role="alert" id="errorAlert" style="display: none;">
                        <svg class="bi flex-shrink-0 me-2" width="20" height="20" role="img" aria-label="Danger:">
                            <use xlink:href="#exclamation-triangle-fill" />
                        </svg>
                        <div class="alert-message mx-1">
                        Invalid code. Please try again.
                        </div>
                    </div> -->

                    <!-- Code Button -->
                    <div class="container d-flex justify-content-center">
                        <button type="submit" name="login"
                            class="btn btn-code text-dark rounded-4 px-4 my-md-4 my-3 mx-auto border-blue"
                            style="width: 73%;">
                            Continue
                        </button>
                    </div>


                </form>

                <!-- Back to Login Redirect -->
                <div class="container text-center text-small">
                    <a href="" class="text-decoration-none">
                        <span class="back-to-login">
                            <span class="material-symbols-outlined arrow-back-icon">arrow_back</span>
                            Back to login
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function closeAlert() {
            document.getElementById('errorModal').style.display = 'none';
        }
        function closeAlert() {
            document.getElementById('errorModal').style.display = 'none';
        }

        // modal and button animation
        // function closeAlert() {
        //     const modal = document.getElementById('errorModal');
        //     modal.style.display = 'none';
        //     document.querySelector('.btn-login').classList.remove('modal-shift');
        // }

        // Code input functionality
        document.addEventListener('DOMContentLoaded', function() {
            const codeInput = document.getElementById('floatingInput');
            
            // Handle input - only allow numbers and limit to 6 digits
            codeInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, ''); // Remove non-digits
                value = value.slice(0, 6); // Limit to 6 digits
                e.target.value = value;
            });
            
            // Handle paste
            codeInput.addEventListener('paste', function(e) {
                e.preventDefault();
                const pastedData = e.clipboardData.getData('text');
                const digits = pastedData.replace(/\D/g, '').slice(0, 6);
                e.target.value = digits;
            });
        });
    </script>
</body>

</html>