<?php
// Check for empty state
$hasLeaderboardResults = mysqli_num_rows($selectTopOneResult) > 0
    || mysqli_num_rows($selectTopTwoToThreeResult) > 0
    || mysqli_num_rows($selectTopFourToTenResult) > 0
    || mysqli_num_rows($selectPlacementResult) > 0;

?>
<?php if ($hasLeaderboardResults): ?>
    <div class="container-fluid">
        <div class="row align-items-center justify-content-start flex-column flex-md-row">
            <div class="col-8 col-sm-6 col-md-12 col-lg-6 d-flex search-container mb-2 mb-lg-0">
                <input type="text" id="leaderboardSearch" placeholder="Search classmates" class="form-control py-1 text-reg text-lg-12 text-14">
                <button type="button" class="btn-outline-secondary">
                    <i class="bi bi-search me-2"></i>
                </button>
            </div>
        </div>
    </div>
    <div class="customCard text-sbold p-3">
        <div class="row" id="topThree">
            <?php
            if (mysqli_num_rows($selectTopOneResult) > 0) {
                while ($topOne = mysqli_fetch_assoc($selectTopOneResult)) {
                    $previousRank = ($topOne['previousRank'] == null) ? '1' : $topOne['currentRank'];
                    $currentRank = '1';
                    $leaderboardID = $topOne['leaderboardID'];
                    if ($topOne['currentRank'] != $currentRank) {
                        $updatePreviousRankQuery = "UPDATE leaderboard SET previousRank = '$previousRank', currentRank = '$currentRank'
                                                WHERE leaderboardID = '$leaderboardID'";
                        $updatePreviousRankResult = executeQuery($updatePreviousRankQuery);
                    }
            ?>
                    <div class="col-12 col-xl-4 mt-3 px-0 mx-auto mx-md-0 d-flex d-md-block justify-content-center justify-content-md-auto px-1 leaderboard-item">
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
                                            style="background-color: <?php
                                                                        $getPreviousRankQuery = "SELECT previousRank FROM leaderboard WHERE leaderboardID = '$leaderboardID'";
                                                                        $getPreviousRankResult = executeQuery($getPreviousRankQuery);
                                                                        $getPreviousRankRow = (mysqli_num_rows($getPreviousRankResult) > 0) ?  mysqli_fetch_assoc($getPreviousRankResult) : null;
                                                                        $getPreviousRank = ($getPreviousRankRow == null) ? null : $getPreviousRankRow['previousRank'];

                                                                        echo ($getPreviousRank > 1) ? '#C8ECC1' : '#e2e2e2ff'; ?>;">
                                            <i class="<?php if ($getPreviousRank > 1) {
                                                            echo 'bi bi-caret-up-fill';
                                                        } else if ($getPreviousRank == 1) {
                                                            echo '';
                                                        } ?>"></i>
                                            <?php echo ($getPreviousRank > 1) ? ($getPreviousRank - 1) : '-';
                                            ?>
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
                            <p id="leaderboard-no-results" class="text-muted text-center mt-3" style="display:none;">
                                No matches found.
                            </p>

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
                    $previousRank = ($topTwoToThree['previousRank'] == null) ? $i : $topTwoToThree['currentRank'];
                    $currentRank = $i;
                    $leaderboardID = $topTwoToThree['leaderboardID'];
                    if ($topTwoToThree['currentRank'] != $currentRank) {
                        $updatePreviousRankQuery = "UPDATE leaderboard SET previousRank = '$previousRank', currentRank = '$currentRank'
                                                WHERE leaderboardID = '$leaderboardID'";
                        $updatePreviousRankResult = executeQuery($updatePreviousRankQuery);
                    }
            ?>
                    <div class="col-6 col-md-12 col-xl-4 mt-3 px-0 px-1 leaderboard-item">
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
                                            style="background-color: <?php
                                                                        $getPreviousRankQuery = "SELECT previousRank FROM leaderboard WHERE leaderboardID = '$leaderboardID'";
                                                                        $getPreviousRankResult = executeQuery($getPreviousRankQuery);
                                                                        $getPreviousRankRow = (mysqli_num_rows($getPreviousRankResult) > 0) ?  mysqli_fetch_assoc($getPreviousRankResult) : null;
                                                                        $getPreviousRank = ($getPreviousRankRow == null) ? null : $getPreviousRankRow['previousRank'];

                                                                        if ($getPreviousRank > $i) {
                                                                            echo '#C8ECC1';
                                                                        } else if ($getPreviousRank == $i) {
                                                                            echo '#e2e2e2ff';
                                                                        } else if ($getPreviousRank < $i) {
                                                                            echo '#ecc1c1ff';
                                                                        } ?>;">
                                            <i class="<?php if ($getPreviousRank > $i) {
                                                            echo 'bi bi-caret-up-fill';
                                                        } else if ($getPreviousRank == $i) {
                                                            echo '';
                                                        } else if ($getPreviousRank < $i) {
                                                            echo 'bi bi-caret-down-fill';
                                                        } ?>"></i>
                                            <?php if ($getPreviousRank > $i) {
                                                echo $getPreviousRank - $i;
                                            } else if ($getPreviousRank == $i) {
                                                echo '-';
                                            } else if ($getPreviousRank < $i) {
                                                echo $i - $getPreviousRank;
                                            }
                                            ?>
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
                            <p id="leaderboard-no-results" class="text-muted text-center mt-3" style="display:none;">
                                No matches found.
                            </p>

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
                    $previousRank = ($topFourToTen['previousRank'] == null) ? $i : $topFourToTen['currentRank'];
                    $currentRank = $i;
                    $leaderboardID = $topFourToTen['leaderboardID'];
                    if ($topFourToTen['currentRank'] != $currentRank) {
                        $updatePreviousRankQuery = "UPDATE leaderboard SET previousRank = '$previousRank', currentRank = '$currentRank'
                                                WHERE leaderboardID = '$leaderboardID'";
                        $updatePreviousRankResult = executeQuery($updatePreviousRankQuery);
                    }
            ?>
                    <div class="container-fluid">
                        <div class="row px-1">
                            <div class="col-12 border border-black mx-auto mt-3 rounded-4 px-4 py-2 bg-white leaderboard-item">
                                <div class="row">
                                    <div class="col-3 d-flex align-items-center justify-content-around">
                                        <span class="text-xl-36 text-xs-28 text-30">
                                            <?php echo $i; ?>
                                        </span>
                                        <span
                                            class="badge rounded-pill text-dark text-reg float-end d-flex flex-row d-xs-none d-md-none d-lg-flex"
                                            style="background-color: <?php
                                                                        $getPreviousRankQuery = "SELECT previousRank FROM leaderboard WHERE leaderboardID = '$leaderboardID'";
                                                                        $getPreviousRankResult = executeQuery($getPreviousRankQuery);
                                                                        $getPreviousRankRow = (mysqli_num_rows($getPreviousRankResult) > 0) ?  mysqli_fetch_assoc($getPreviousRankResult) : null;
                                                                        $getPreviousRank = ($getPreviousRankRow == null) ? null : $getPreviousRankRow['previousRank'];

                                                                        if ($getPreviousRank > $i) {
                                                                            echo '#C8ECC1';
                                                                        } else if ($getPreviousRank == $i) {
                                                                            echo '#e2e2e2ff';
                                                                        } else if ($getPreviousRank < $i) {
                                                                            echo '#ecc1c1ff';
                                                                        } ?>;">
                                            <i class="<?php if ($getPreviousRank > $i) {
                                                            echo 'bi bi-caret-up-fill';
                                                        } else if ($getPreviousRank == $i) {
                                                            echo '';
                                                        } else if ($getPreviousRank < $i) {
                                                            echo 'bi bi-caret-down-fill';
                                                        } ?>"></i>
                                            <?php if ($getPreviousRank > $i) {
                                                echo $getPreviousRank - $i;
                                            } else if ($getPreviousRank == $i) {
                                                echo '-';
                                            } else if ($getPreviousRank < $i) {
                                                echo $i - $getPreviousRank;
                                            }
                                            ?>
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
                            <p id="leaderboard-no-results" class="text-muted text-center mt-3" style="display:none;">
                                No matches found.
                            </p>

                        </div>
                    </div>
            <?php
                    $i++;
                }
            }
            ?>
        </div>
    </div>
<?php else: ?>
    <!-- EMPTY STATE -->
    <div class="empty-state text-center">
        <img src="shared/assets/img/empty/leaderboard.png" alt="No Stars" class="empty-state-img">
        <div class="empty-state-text text-14 d-flex flex-column align-items-center">
            <p class="text-med mb-1">No stars shining yet!</p>
            <p class="text-reg">Earn XP from quests to climb the leaderboard</p>
        </div>
    </div>
<?php endif; ?>