<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webstar | Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="shared/assets/css/global-styles.css">
    <style>
        body {
            background-color: var(--lightBlue);
        }

        .login-container {
            height: 628px;
            width: 595px;
            background-color: var(--white);
        }

        /*Border Color*/
        .border-blue {
            border-style: solid;
            border-width: 0.5px;
            border-color: var(--blue);
        }

        /*Font Color*/
        .text-blue {
            color: var(--blue);
        }

        .btn-login {
            background-color: var(--blue);
            color: var(--white);
            font-size: 22px;
        }

        /*Resopnsiveness*/
        @media screen and (max-width: 767px) {
            .login-container {
                height: 544px;
                width: 506px;
            }
        }

        @media screen and (max-width: 575px) {
            .login-container {
                height: 474px;
                width: 430px;
            }

            .btn-login {
                font-size: 18px;
            }
        }

        @media screen and (max-width: 496px) {
            .login-container {
                height: 435px;
                width: 400px;
            }

            .logo {
                width: 235px;
            }
        }

        @media screen and (max-width: 430px) {
            .login-container {
                width: 360px;
            }
        }

         @media screen and (max-width: 380px) {
            .login-container {
                width: 300px;
            }

            .logo {
                width: 220px;
            }
        }
    </style>
</head>

<body>
    <div class="container min-vh-100 d-flex justify-content-center align-items-center">
        <div class="row h-100">
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
                    <div class="form-floating pt-4 pb-md-4 pb-3">
                        <input type="password" class="form-control rounded-5 border-blue" id="floatingPassword" placeholder="Password">
                        <label for="floatingPassword">
                            <div class="pt-4">Password</div>
                        </label>
                    </div>
                    <div class="forgot-password float-end">
                        <a href="#" class="text-decoration-none text-blue fw-semibold">Forgot Password?</a>
                    </div>
                </div>
                <!--Login Button-->
                <div class="container d-flex justify-content center mt-3">
                    <button class="btn btn-login rounded-5 px-5 my-md-4 my-sm-3 my-3 mx-auto w-sm-50 w-75" type="submit">Log in</button>
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