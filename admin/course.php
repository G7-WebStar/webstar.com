<?php $activePage = 'course'; ?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webstar | admin | Course</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="shared/assets/css/sidebar-and-container-styles.css">
    <link rel="stylesheet" href="shared/assets/css/course.css">
</head>
<<<<<<< Updated upstream
=======
<style>
    .header {
        font-family: var(--Bold);
        color: var(--blue);
        font-size: 2.5em;
    }

    .btn {
        font-family: var(--Regular);
        color: var(--white);
    }

    .courseTitle {
        font-family: var(--Bold);
        font-size: 2.5em;
        color: var(--blue);
    }

    .custom-thead {
        background-color: var(--blue);
        color: var(--white);
        border-bottom: 5px solid var(--white);
    }

    .custom-thead th {
        background-color: var(--blue);
        color: var(--white);
        height: 50px;
    }

    .custom-body td {
        background-color: var(--blue);
        color: var(--white);
        height: 50px;
    }
</style>
>>>>>>> Stashed changes

<body>
    <div class="container-fluid min-vh-100 d-flex justify-content-center align-items-center p-3">

        <div class="row w-100">

            <!-- Sidebar (only shows on mobile) -->
            <?php include 'shared/components/sidebar-for-mobile.php'; ?>

            <!-- Sidebar Column (fixed on desktop) -->
            <div class="col-auto d-none d-md-block">
                <?php include 'shared/components/sidebar-for-desktop.php'; ?>
            </div>

            <!-- Main Container Column-->
<<<<<<< Updated upstream
            <div class="col main-container m-0 p-0 mx-2 p-4">
=======
            <div class="col main-container m-0 p-0 mx-2 p-4 overflow-y-auto">
>>>>>>> Stashed changes
                <div class="card border-0 p-3 h-100 w-100 rounded-0 shadow-none">

                    <!-- Navbar for mobile -->
                    <?php include 'shared/components/navbar-for-mobile.php'; ?>

                    <!-- PUT CONTENT HERE -->
<<<<<<< Updated upstream

=======
                    <div class="container-fluid py-3 overflow-y-auto">
                        <div class="row align-items-center mb-3">
                            <div class="col d-flex justify-content-between align-items-center">
                                <div class="header">Courses</div>
                                <button type="button" class="btn btn-primary p-2">Create Course</button>
                            </div>
                        </div>

                        <!-- Course: HTML -->
                        <div class="row align-items-center">
                            <div class="col d-flex justify-content-between align-items-center">
                                <div class="courseTitle my-2">HTML</div>
                                <button type="button" class="btn btn-primary d-flex align-items-center">
                                    <img src="shared/assets/img/add.svg" alt="Add">
                                    New Lesson
                                </button>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col">
                                <table class="table">
                                    <thead class="custom-thead text-center">
                                        <tr>
                                            <th>Lesson No.</th>
                                            <th>Title</th>
                                            <th>Description</th>
                                            <th>Page Name</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="html-lessons" class="custom-body text-center">
                                        <!-- HTML lessons go here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Course: CSS -->
                        <div class="row align-items-center">
                            <div class="col d-flex justify-content-between align-items-center">
                                <div class="courseTitle my-2">CSS</div>
                                <button type="button" class="btn btn-primary d-flex align-items-center">
                                    <img src="shared/assets/img/add.svg" alt="Add">
                                    New Lesson
                                </button>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col">
                                <table class="table">
                                    <thead class="custom-thead text-center">
                                        <tr>
                                            <th>Lesson No.</th>
                                            <th>Title</th>
                                            <th>Description</th>
                                            <th>Page Name</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="custom-body text-center">
                                        <tr>
                                            <td colspan="5">No lessons yet.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
>>>>>>> Stashed changes
                </div>
            </div>
        </div>
    </div>
<<<<<<< Updated upstream

=======
    <script>
        const htmlLessons = [
            { title: "HTML 1", desc: "HTML is the standard .......", page: "lesson1.html" },
            { title: "HTML 2", desc: "HTML is the standard .......", page: "lesson2.html" },
            { title: "HTML 3", desc: "HTML is the standard .......", page: "lesson3.html" },
            { title: "HTML 4", desc: "HTML is the standard .......", page: "lesson4.html" },
            { title: "HTML 5", desc: "HTML is the standard .......", page: "lesson5.html" },
            { title: "HTML 6", desc: "HTML is the standard .......", page: "lesson6.html" }
        ];

        let rows = "";

        for (let i = 0; i < htmlLessons.length; i++) {
            const lesson = htmlLessons[i];
            rows += `
            <tr>
                <td>${i + 1}</td>
                <td>${lesson.title}</td>
                <td>${lesson.desc}</td>
                <td>${lesson.page}</td>
                <td>
                    <div class="d-flex justify-content-center gap-2">
                        <button class="btn p-0 border-0 bg-transparent">
                            <img src="shared/assets/img/edit.svg" alt="Edit">
                        </button>
                        <button class="btn p-0 border-0 bg-transparent">
                            <img src="shared/assets/img/delete.svg" alt="Delete">
                        </button>
                    </div>
                </td>
            </tr>
        `;
        }

        document.getElementById("html-lessons").innerHTML = rows;
    </script>
>>>>>>> Stashed changes
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

</body>


</html>