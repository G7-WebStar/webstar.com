<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webstar | Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="shared/assets/css/registration.css">
</head>

<body>
    <div class="container-fluid min-vh-100 d-flex justify-content-center align-items-center" style="background: #dbe8fd;">
        <div class="row w-100 h-100 registration-container" style="max-width: 860px; margin: auto; background: #fff; min-height: 80vh; box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15); border-radius: 1.5rem; overflow: hidden;">
            <!-- Logo -->
            <div class="col-md-6 d-flex flex-column justify-content-center align-items-center p-0 registration-logo-col" style="background: #2866e4; max-width: 350px;">
                <div class="w-100 d-flex justify-content-center align-items-center" style="height: 100%;">
                    <img src="shared/assets/img/webstar-logo-blue.png" class="img-fluid" style="max-width: 275px; filter: brightness(0) invert(1);">
                </div>
            </div>
            <!-- Registration Form -->
            <div class="col-md-7 d-flex flex-column justify-content-center align-items-center p-4 h-100 registration-form-col" style="background: #fff;">
                <div class="w-100" style="max-width: 400px;">
                    <h2 class="text-center mb-3 mt-2 registration-title" style="color: #2866e4; font-family: 'DM Sans SemiBold', sans-serif; font-weight: 600;">Create an account</h2>
                    <form>
                        <input type="text" class="form-control rounded-pill mb-3 border-primary" placeholder="First name">
                        <input type="text" class="form-control rounded-pill mb-3 border-primary" placeholder="Last name">
                        <input type="email" class="form-control rounded-pill mb-3 border-primary" placeholder="Email">
                        <input type="password" class="form-control rounded-pill mb-3 border-primary" placeholder="Password">
                        <input type="password" class="form-control rounded-pill mb-3 border-primary" placeholder="Confirm password">
                        <button type="submit" class="btn btn-primary w-100 rounded-pill fw-semibold mb-4 mt-2">Register</button>
                    </form>
                    <div class="text-center mb-4">
                        <span>Already have an account?</span>
                        <a href="login.php" class="text-decoration-none fw-semibold" style="color: #2866e4;">Log in</a>
                    </div>
                    <div class="text-center" style="font-size: 0.9rem; color: #888;">
                        By clicking register, you agree to our
                        <a href="#" class="fw-bold" style="color: #2866e4;">Terms of Service</a> and
                        <a href="#" class="fw-bold" style="color: #2866e4;">Privacy Policy</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>