<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Material Design Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp" />
    <link rel="stylesheet" href="shared/assets/css/global-styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <title>Webstar | 404</title>

    <style>
        .center-visually {
            margin-top: 10vh !important;
            /* pushes content down a bit */
            margin-bottom: 10vh !important;
        }

        .bg-main-color {
            background-color: var(--dirtyWhite);
        }

        @media screen and (max-width: 1399px) {
            .text-xl-20 {
                font-size: 20px !important;
            }

            .text-xl-14 {
                font-size: 14px !important;
            }
        }

        @media screen and (max-width: 767px) {
            .text-sm-18 {
                font-size: 18px !important;
            }

            .text-sm-12 {
                font-size: 12px !important;
            }
        }
    </style>
</head>

<body class="bg-main-color">
    <div class="container text-center row-padding-top my-auto center-visually">
        <div class="row">
            <div class="col-6 mx-auto my-auto">
                <div class="mt-5">
                    <img class="img-fluid object-fit-contain" width="200px" src="shared/assets/img/time.png"
                        alt="image">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-md-8 mx-auto my-auto">
                <div class="text-bold text-25 text-sm-18 text-xl-20 col-12">
                    You seem to have wandered off the quest path.
                </div>
                <div class="text-reg text-16 text-sm-12 text-xl-14 col-12">
                    The page you’re looking for doesn’t exist — or it might’ve been moved to another world.
                </div>
            </div>
        </div>
        <div class="row mt-5">
            <div class="col-12 mx-auto text-bold d-flex justify-content-center">
                <a href="index.php" class="text-decoration-none text-dark">
                    <button
                        class="btn d-flex align-items-center justify-content-center border border-black rounded-5 px-sm-4 py-sm-2 interactable"
                        style="background-color: var(--primaryColor);">
                        <span class="m-0 fs-sm-6 text-sm-12 text-med">Return to Home</span>
                    </button>
                </a>
            </div>
        </div>
    </div>
</body>

</html>