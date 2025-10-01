<?php
?>
<div class="container-fluid">
    <div class="row align-items-center justify-content-center flex-column flex-md-row">
        <div class="col-8 col-sm-6 col-md-12 col-lg-6 d-flex search-container mb-2 mb-lg-0">
            <input type="text" placeholder="Search classmates" class="form-control py-1 text-reg text-lg-12 text-14">
            <button type="button" class="btn-outline-secondary">
                <i class="bi bi-search me-2"></i>
            </button>
        </div>
        <div class="col-6 d-flex justify-content-center justify-content-lg-start align-items-center">
            <span class="dropdown-label me-2">View by:</span>
            <form method="POST">
                <select class="dropdown-custom" name="dateFilter" onchange="this.form.submit()">
                    <option value="Monthly" <?php echo $dateFilter == 'Monthly' ? 'selected' : '';?> class="dropdown-item text-reg">Monthly</option>
                    <option value="Weekly" <?php echo ($dateFilter == 'Weekly' || empty($dateFilter)) ? 'selected' : '';?> class="dropdown-item text-reg">Weekly</option>
                    <option value="Daily" <?php echo $dateFilter == 'Daily' ? 'selected' : '';?> class="dropdown-item text-reg">Daily</option>
                </select>
            </form>
        </div>
    </div>
</div>
<div class="customCard text-sbold p-3">
    <div class="row">
        <?php
        if (mysqli_num_rows($selectTopOneResult) > 0) {
            while ($topOne = mysqli_fetch_assoc($selectTopOneResult)) {
        ?>
                <div class="col-12 col-xl-4 mt-3 px-0 mx-auto mx-md-0 d-flex d-md-block justify-content-center justify-content-md-auto px-1">
                    <div class="card rounded-4 col-6 col-md-12">
                        <div class="card-body border border-black rounded-4">
                            <div class="row">
                                <div class="col-6 d-flex align-items-center">
                                    <img src="shared/assets/pfp-uploads/<?php echo $topOne['profilePicture']; ?>" alt="" width="90"
                                        height="90" class="rounded-circle float-start leaderboard-img">
                                </div>
                                <div class="col-6">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="float-end text-xl-36 text-xs-28 text-40">1</div>
                                        </div>
                                    </div>
                                    <div class="badge rounded-pill text-dark text-reg float-end d-flex flex-row d-xxs-none"
                                        style="background-color: #C8ECC1;">
                                        <i class="bi bi-caret-up-fill me-1"></i>
                                        <div class="me-1">2</div>
                                    </div>
                                </div>
                                <div class="col-12 mt-3 text-xl-12 text-lg-16 text-xs-12 text-wrap">
                                    <?php echo $topOne['firstName'] . " " . $topOne['middleName'] . " " . $topOne['lastName']; ?>
                                </div>
                                <div class="col-12 text-reg text-xl-12 text-lg-16 text-xs-12">
                                    <?php echo $topOne['totalPoints']; ?> XPs
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        <?php
            }
        }
        ?>
        <?php
        if (mysqli_num_rows($selectTopTwoToThreeResult) > 0) {
            $i = 2;
            while ($topTwoToThree = mysqli_fetch_assoc($selectTopTwoToThreeResult)) {
        ?>
                <div class="col-6 col-md-12 col-xl-4 mt-3 px-0 px-1">
                    <div class="card rounded-4">
                        <div class="card-body border border-black rounded-4">
                            <div class="row">
                                <div class="col-6 d-flex align-items-center">
                                    <img src="shared/assets/pfp-uploads/<?php echo $topTwoToThree['profilePicture']; ?>" alt="" width="90"
                                        height="90" class="rounded-circle float-start leaderboard-img">
                                </div>
                                <div class="col-6">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="float-end text-xl-36 text-xs-28 text-40"><?php echo $i; ?></div>
                                        </div>
                                    </div>
                                    <div class="badge rounded-pill text-dark text-reg float-end d-flex flex-row d-xxs-none"
                                        style="background-color: #ECC1C1;">
                                        <i class="bi bi-caret-down-fill me-1"></i>
                                        <div class="me-1">1</div>
                                    </div>
                                </div>
                                <div class="col-12 mt-3 text-xl-12 text-lg-16 text-xs-12 text-wrap">
                                    <?php echo $topTwoToThree['firstName'] . " " . $topTwoToThree['middleName'] . " " . $topTwoToThree['lastName']; ?>
                                </div>
                                <div class="col-12 text-reg text-xl-12 text-lg-16 text-xs-12">
                                    <?php echo $topTwoToThree['totalPoints']; ?> XPs
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        <?php
                $i++;
            }
        }
        ?>
        <?php
        if (mysqli_num_rows($selectTopFourToTenResult) > 0) {
            $i = 4;
            while ($topFourToTen = mysqli_fetch_assoc($selectTopFourToTenResult)) {
        ?>
                <div class="container-fluid">
                    <div class="row px-1">
                        <div class="col-12 border border-black mx-auto mt-3 rounded-4 px-4 py-2 bg-white">
                            <div class="row">
                                <div class="col-3 d-flex align-items-center justify-content-around">
                                    <span class="text-xl-36 text-xs-28 text-30">
                                        <?php echo $i; ?>
                                    </span>
                                    <span
                                        class="badge rounded-pill text-dark text-reg float-end d-flex flex-row d-xs-none d-md-none d-lg-flex"
                                        style="background-color: #C8ECC1;">
                                        <i class="bi bi-caret-up-fill me-1"></i>
                                        <div class="me-1">2</div>
                                    </span>
                                </div>
                                <div class="col-9 d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="shared/assets/pfp-uploads/<?php echo $topFourToTen['profilePicture']; ?>" alt="" width="40"
                                            height="40" class="rounded-circle me-2 d-xxs-none">
                                        <span class="text-xl-12 text-wrap">
                                            <?php echo $topFourToTen['firstName'] . " " . $topFourToTen['middleName'] . " " . $topFourToTen['lastName']; ?>
                                        </span>
                                    </div>
                                    <div class="text-reg text-xl-12 d-block d-md-none d-lg-block">
                                        <?php echo $topFourToTen['totalPoints']; ?> XPs
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        <?php
                $i++;
            }
        }
        ?>
        <?php
        if (mysqli_num_rows($selectPlacementResult) > 0) {
            while ($placement = mysqli_fetch_assoc($selectPlacementResult)) {
        ?>
                <div class="container-fluid">
                    <div class="row px-1">
                        <div class="col-12 mx-auto mt-3 rounded-4 px-4 py-2 bg-transparent text-center">
                            ...
                        </div>
                    </div>
                </div>

                <div class="container-fluid">
                    <div class="row px-1">
                        <div class="col-12 border border-black mx-auto mt-3 rounded-4 px-4 py-2 bg-white">
                            <div class="row">
                                <div class="col-3 d-flex align-items-center justify-content-around">
                                    <span class="text-xl-36 text-xs-28 text-30">
                                        <?php echo $placement['rank']; ?>
                                    </span>
                                    <span
                                        class="badge rounded-pill text-dark text-reg float-end d-flex flex-row d-xs-none d-md-none d-lg-flex"
                                        style="background-color: #C8ECC1;">
                                        <i class="bi bi-caret-up-fill me-1"></i>
                                        <div class="me-1">2</div>
                                    </span>
                                </div>
                                <div class="col-9 d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="shared/assets/pfp-uploads/<?php echo $placement['profilePicture']; ?>" alt="" width="40"
                                            height="40" class="rounded-circle me-2 d-xxs-none">
                                        <span class="text-xl-12 text-wrap">
                                            <?php echo $placement['firstName'] . " " . $placement['middleName'] . " " . $placement['lastName']; ?>
                                        </span>
                                    </div>
                                    <div class="text-reg text-xl-12 d-block d-md-none d-lg-block">
                                        <?php echo $placement['totalPoints']; ?> XPs
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        <?php
            }
        }
        ?>
    </div>
</div>