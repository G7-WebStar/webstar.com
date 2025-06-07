<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webstar | Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="shared/assets/css/login.css">
</head>

<body>
    <div class="container min-vh-100 d-flex justify-content-center align-items-center">
        <div class="row">
            <div class="col-4 p-3 rounded-4 login-container border-blue">
                <!--Logo-->
                <div class="container d-flex justify-content-center py-sm-4 py-3">
                    <img src="shared/assets/img/webstar-logo-blue.png" class="img-fluid px-3 my-4 logo" width="275px">
                </div>
                <!--Login Form-->
                <div class="container w-75 py-md-4">
                    <div class="form-floating">
                        <input type="email" class="form-control rounded-5 border-blue" id="floatingInput" placeholder="name@example.com">
                        <label for="floatingInput">Email</label>
                    </div>
                    <div class="form-floating pt-2 pt-md-4 pb-md-4 pb-3">
                        <input type="password" class="form-control rounded-5 border-blue" id="floatingPassword" placeholder="Password">
                        <label for="floatingPassword">
                            <div class="pt-2 pt-md-4">Password</div>
                        </label>
                    </div>
                    <div class="forgot-password float-end">
                        <a href="#" class="text-decoration-none text-blue fw-semibold">Forgot Password?</a>
                    </div>
                </div>
                <!--Login Button-->
                <div class="container d-flex justify-content-center mt-3">
                    <button class="btn btn-primary btn-login rounded-5 px-5 my-md-4 my-3 mx-auto w-sm-50 w-75" type="submit">Log in</button>
                </div>

                <!--Registration Redirect-->
                <div class="container mt-md-4 mt-sm-3 text-center">
                    <a href="#" class="text-decoration-none"><span class="text-blue">Don't have an account? </span><span class="fw-bold text-blue">Sign up</span></a>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>


</html>