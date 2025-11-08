<?php $activePage = 'shop';

include("shared/assets/database/connect.php");
include("shared/assets/processes/session-process.php");

// Gets which tab is active
$activeTab = $_GET['tab'] ?? 'emblems';

// Stores Toast Message and Variable
$toastMessage = '';
$toastType = '';

if (isset($_SESSION['toast'])) {
    $toastMessage = $_SESSION['toast']['message'];
    $toastType = $_SESSION['toast']['type'];
    unset($_SESSION['toast']);
}

// Get My Items
$myItems = [];
$myItemsQuery = "
SELECT m.myItemID, m.dateAcquired,
       e.emblemID, e.emblemName AS emblemTitle, e.emblemPath AS emblemImg,
       c.coverImageID, c.title AS coverTitle, c.imagePath AS coverImg,
       t.colorThemeID, t.themeName AS colorTitle, t.hexCode
FROM myItems m
LEFT JOIN emblem e ON m.emblemID = e.emblemID
LEFT JOIN coverImage c ON m.coverImageID = c.coverImageID
LEFT JOIN colorTheme t ON m.colorThemeID = t.colorThemeID
WHERE m.userID = $userID
ORDER BY m.dateAcquired DESC
";

$result = $conn->query($myItemsQuery);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $myItems[] = $row;
    }
}

// Get Cover Images
$coverImages = [];
$coverQuery = "SELECT c.coverImageID, c.title, c.imagePath, c.description, c.price,
       CASE WHEN m.coverImageID IS NOT NULL THEN 1 ELSE 0 END AS isBought
FROM coverImage c
LEFT JOIN myItems m
    ON c.coverImageID = m.coverImageID AND m.userID = $userID
ORDER BY isBought ASC, c.price DESC";
$result = $conn->query($coverQuery);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $coverImages[] = $row;
    }
}

// Get Emblems
$emblems = [];
$emblemQuery = "
SELECT e.emblemID, e.emblemName, e.emblemPath, e.description, e.price,
       CASE WHEN m.emblemID IS NOT NULL THEN 1 ELSE 0 END AS isBought
FROM emblem e
LEFT JOIN myItems m
    ON e.emblemID = m.emblemID AND m.userID = $userID
ORDER BY isBought ASC, e.price DESC
";
$result = $conn->query($emblemQuery);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $emblems[] = $row;
    }
}

// Get Color Themes
$colorThemes = [];
$colorQuery = "SELECT t.colorThemeID, t.themeName, t.hexCode, t.description, t.price,
       CASE WHEN m.colorThemeID IS NOT NULL THEN 1 ELSE 0 END AS isBought
FROM colorTheme t
LEFT JOIN myItems m
    ON t.colorThemeID = m.colorThemeID AND m.userID = $userID
ORDER BY isBought ASC, t.price DESC";

$result = $conn->query($colorQuery);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $colorThemes[] = $row;
    }
}

// Get Owned Cover Images
$boughtCovers = [];
$coverQuery = "SELECT coverImageID FROM myItems WHERE userID = $userID AND coverImageID IS NOT NULL";
$result = $conn->query($coverQuery);

while ($row = $result->fetch_assoc()) {
    $boughtCovers[] = $row['coverImageID'];
}

// Get Owned Color Themes
$boughtColors = [];
$colorQuery = "SELECT colorThemeID FROM myItems WHERE userID = $userID AND colorThemeID IS NOT NULL";
$result = $conn->query($colorQuery);
while ($row = $result->fetch_assoc()) {
    $boughtColors[] = $row['colorThemeID'];
}

// Get Owned Emblems
$boughtEmblems = [];
$emblemQuery = "SELECT emblemID FROM myItems WHERE userID = $userID AND emblemID IS NOT NULL";
$result = $conn->query($emblemQuery);
while ($row = $result->fetch_assoc()) {
    $boughtEmblems[] = $row['emblemID'];
}


// When bought, insert into myItems table
if (isset($_POST['buyItem'])) {
    $coverImageID = (int)($_POST['coverImageID'] ?? 0);
    $emblemID = (int)($_POST['emblemID'] ?? 0);
    $colorThemeID = (int)($_POST['colorThemeID'] ?? 0);
    $userID = (int)$userID;

    // Determine which item is being bought
    $itemType = '';
    $itemID = 0;
    $price = 0;

    if ($coverImageID) {
        $itemType = 'cover';
        $itemID = $coverImageID;
        $priceQuery = $conn->query("SELECT price FROM coverImage WHERE coverImageID = $coverImageID");
    } elseif ($emblemID) {
        $itemType = 'emblem';
        $itemID = $emblemID;
        $priceQuery = $conn->query("SELECT price FROM emblem WHERE emblemID = $emblemID");
    } elseif ($colorThemeID) {
        $itemType = 'color';
        $itemID = $colorThemeID;
        $priceQuery = $conn->query("SELECT price FROM colorTheme WHERE colorThemeID = $colorThemeID");
    }

    $priceData = $priceQuery->fetch_assoc();
    $price = $priceData['price'] ?? 0;

    // Get user's webstars
    $webstarQuery = $conn->query("SELECT webstars FROM profile WHERE userID = $userID");
    $userWebstars = $webstarQuery->fetch_assoc()['webstars'] ?? 0;

    // Checks if the user has enough webstar
    if ($userWebstars >= $price && $itemID) {
        $conn->begin_transaction();
        try {
            // Deduct webstars
            $conn->query("UPDATE profile SET webstars = webstars - $price WHERE userID = $userID");

            // Insert into myItems 
            $conn->query("INSERT INTO myItems (userID, coverImageID, emblemID, colorThemeID, dateAcquired) 
                          VALUES (
                              $userID,
                              " . ($coverImageID ?: "NULL") . ",
                              " . ($emblemID ?: "NULL") . ",
                              " . ($colorThemeID ?: "NULL") . ",
                              NOW()
                          )");

            // Insert into webstars history
            $conn->query("INSERT INTO webstars (userID, sourceType, pointsChanged, dateEarned) 
                          VALUES ($userID, 'Shop Purchase', -$price, NOW())");

            $conn->commit();

            $_SESSION['toast'] = [
                'message' => 'Item purchased!',
                'type' => 'alert-success'
            ];
        } catch (Exception $e) {
            $conn->rollback();
            $_SESSION['toast'] = [
                'message' => 'Something went wrong. Please try again.',
                'type' => 'alert-danger'
            ];
        }
    } else {
        $_SESSION['toast'] = [
            'message' => 'Purchase failed. Insufficient webstars.',
            'type' => 'alert-danger'
        ];
    }

    // Redirect to the appropriate tab
    $tab = $itemType === 'cover' ? 'cover-images' : ($itemType === 'emblem' ? 'emblems' : 'color-themes');
    header("Location: shop.php?tab=$tab");
    exit;
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Shop ✦ Webstar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="shared/assets/css/sidebar-and-container-styles.css">
    <link rel="stylesheet" href="shared/assets/css/shop.css">
    <link rel="stylesheet" href="shared/assets/css/settings.css">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="icon" type="image/png" href="shared/assets/img/webstar-icon.png">

    <!-- Material Design Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp" />

</head>


<body>
    <div class="container-fluid min-vh-100 d-flex justify-content-center align-items-center p-0 p-md-3"
        style="background-color: var(--black);">

        <div class="row w-100">

            <!-- Sidebar (only shows on mobile) -->
            <?php include 'shared/components/sidebar-for-mobile.php'; ?>

            <!-- Sidebar Column (fixed on desktop) -->
            <?php include 'shared/components/sidebar-for-desktop.php'; ?>

            <!-- Main Container Column-->
            <div class="col main-container m-0 p-0 mx-0 mx-md-2 p-0 p-md-4 overflow-y-auto">
                <div class="card border-0 px-3 pt-3 m-0 h-100 w-100 rounded-0 shadow-none"
                    style="background-color: transparent;">

                    <div id="toastContainer"
                        class="position-absolute top-0 start-50 translate-middle-x pt-5 pt-md-1 d-flex flex-column align-items-center"
                        style="z-index: 1100;"></div>

                    <!-- Navbar for mobile -->
                    <?php include 'shared/components/navbar-for-mobile.php'; ?>

                    <div class="container-fluid py-3 overflow-y-auto row-padding-top shop-container">
                        <!-- Sticky Header: Title + Tabs -->
                        <div class="settings-header-wrapper my-1">
                            <div class="row align-items-center">
                                <!-- Title -->
                                <div class="col-12 col-md-auto shop-title">
                                    <h1 class="text-sbold text-25" style="color: var(--black); ">Shop
                                    </h1>
                                </div>

                                <!-- Tabs -->
                                <div class="col-12 col-md-auto">
                                    <div class="tab-carousel-wrapper">
                                        <div class="d-flex align-items-center" style="width: 100%;">

                                            <!-- Left Arrow -->
                                            <button id="desktopScrollLeftBtn" class="scroll-arrow-btn d-none"
                                                aria-label="Scroll Left"
                                                style="background: none; border: none; color: var(--black); flex-shrink: 0; margin-top:-2px;">
                                                <i class="fa-solid fa-chevron-left"></i>
                                            </button>

                                            <!-- Scrollable Tabs -->
                                            <div class="tab-scroll flex-grow-1 overflow-auto nav-tabs"
                                                style="scroll-behavior: smooth; white-space: nowrap;">
                                                <ul class="nav custom-nav-tabs flex-nowrap d-flex justify-content-between justify-content-md-start"
                                                    id="shopTabs" role="tablist"
                                                    style="display: inline-flex; white-space: nowrap;">
                                                    <li class="nav-item">
                                                        <a class="nav-link text-14 fade <?php echo ($activeTab == 'emblems') ? 'active' : ''; ?>"
                                                            data-bs-toggle="tab" data-label="emblems"
                                                            href="#emblems">Emblems</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link text-14 <?php echo ($activeTab == 'cover-images') ? 'active' : ''; ?>"
                                                            data-bs-toggle="tab" data-label="cover-images"
                                                            href="#cover-images">Cover
                                                            Images</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link text-14 <?php echo ($activeTab == 'color-themes') ? 'active' : ''; ?>"
                                                            data-bs-toggle="tab" data-label="color-themes"
                                                            href="#color-themes">Color
                                                            Themes</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link text-14 <?php echo ($activeTab == 'my-items') ? 'active' : ''; ?>"
                                                            data-bs-toggle="tab" data-label="my-items"
                                                            href="#my-items">My Items</a>
                                                    </li>
                                                </ul>
                                            </div>
                                            <!-- Right Arrow -->
                                            <button id="desktopScrollRightBtn" class="scroll-arrow-btn"
                                                aria-label="Scroll Right"
                                                style="background: none; border: none; color: var(--black); flex-shrink: 0; margin-top:-2px;">
                                                <i class="fa-solid fa-chevron-right"></i>
                                            </button>

                                        </div>
                                    </div>



                                </div>
                            </div>
                        </div>

                        <!-- Tab Content Area -->
                        <div class="row" id="tabContentArea">
                            <div class="tab-content m-0 p-0" id="shopTabContent">
                                <!-- Emblems -->
                                <div class="tab-pane fade <?php echo ($activeTab == 'emblems') ? 'show active' : ''; ?>"
                                    id="emblems" role="tabpanel">
                                    <div class="row m-0 p-0">
                                        <?php if (!empty($emblems)): ?>
                                            <?php foreach ($emblems as $emblem): ?>
                                                <div class="col-lg-3 col-md-6 col-sm-6 col-6 card-wrapper">
                                                    <div class="customCard shop-card" data-bs-toggle="modal"
                                                        data-bs-target="#cardModal" data-id="<?php echo $emblem['emblemID']; ?>"
                                                        data-title="<?php echo htmlspecialchars($emblem['emblemName']); ?>"
                                                        data-price="<?php echo htmlspecialchars($emblem['price']); ?>"
                                                        data-img="shared/assets/img/shop/emblems/<?php echo htmlspecialchars($emblem['emblemPath']); ?>"
                                                        data-desc="<?php echo htmlspecialchars($emblem['description']); ?>"
                                                        data-isbought="<?php echo $emblem['isBought'] ? '1' : '0'; ?>">

                                                        <div class="cardItem">
                                                            <div class="shopCardImage"
                                                                style="padding:10px; display:flex; justify-content:center; align-items:center;">
                                                                <img src="shared/assets/img/shop/emblems/<?php echo htmlspecialchars($emblem['emblemPath']); ?>"
                                                                    alt="<?php echo htmlspecialchars($emblem['emblemName']); ?>"
                                                                    style="max-width:100%; max-height:100%; object-fit:contain;">
                                                            </div>

                                                            <div class="cardContent">
                                                                <div class="cardTitle text-med text-18 text-truncate">
                                                                    <?php echo htmlspecialchars($emblem['emblemName']); ?>
                                                                </div>

                                                                <?php if ($emblem['isBought']): ?>
                                                                    <div class="badge text-reg text-14 mt-1"
                                                                        style="background-color: rgba(108, 117, 125, 0.2);">Owned
                                                                    </div>
                                                                <?php else: ?>
                                                                    <div
                                                                        class="cardPrice text-med text-18 d-flex align-items-center gap-1 mt-1">
                                                                        <img class="me-1" src="shared/assets/img/webstar.png"
                                                                            width="20">
                                                                        <span><?php echo htmlspecialchars($emblem['price']); ?></span>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <p class="text-center text-14 text-med mt-1">No emblems available.</p>
                                        <?php endif; ?>
                                    </div>

                                </div>

                                <!-- Cover Images -->
                                <div class="tab-pane fade <?php echo ($activeTab == 'cover-images') ? 'show active' : ''; ?>"
                                    id="cover-images" role="tabpanel">
                                    <div class="row m-0 p-0">
                                        <?php if (!empty($coverImages)): ?>
                                            <?php foreach ($coverImages as $cover):
                                                $isBought = in_array($cover['coverImageID'], $boughtCovers); ?>
                                                <div class="col-lg-3 col-md-6 col-sm-6 col-6 card-wrapper">
                                                    <div class="customCard shop-card" data-bs-toggle="modal"
                                                        data-bs-target="#cardModal"
                                                        data-id="<?php echo $cover['coverImageID']; ?>"
                                                        data-title="<?php echo htmlspecialchars($cover['title']); ?>"
                                                        data-price="<?php echo htmlspecialchars($cover['price']); ?>"
                                                        data-img="shared/assets/img/shop/cover-images/<?php echo htmlspecialchars($cover['imagePath']); ?>"
                                                        data-desc="<?php echo htmlspecialchars($cover['description']); ?>"
                                                        data-isbought="<?php echo in_array($cover['coverImageID'], $boughtCovers) ? '1' : '0'; ?>">

                                                        <div class="cardItem">
                                                            <div class="shopCardImage"
                                                                style="background: url('shared/assets/img/shop/cover-images/<?php echo htmlspecialchars($cover['imagePath']); ?>'); background-size: cover; background-position: center;">
                                                            </div>
                                                            <div class="cardContent">
                                                                <div class="cardTitle text-med text-18 text-truncate">
                                                                    <?php echo htmlspecialchars($cover['title']); ?>
                                                                </div>

                                                                <?php if ($isBought): ?>
                                                                    <div class="badge text-reg text-14 mt-1"
                                                                        style="background-color: rgba(108, 117, 125, 0.2);">Owned
                                                                    </div>
                                                                <?php else: ?>
                                                                    <div
                                                                        class="cardPrice text-med text-18 d-flex align-items-center gap-1 mt-1">
                                                                        <img class="me-1" src="shared/assets/img/webstar.png"
                                                                            width="20">
                                                                        <span><?php echo htmlspecialchars($cover['price']); ?></span>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <p class="text-center text-14 text-med mt-1">No cover images available.</p>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Color Themes -->
                                <div class="tab-pane fade <?php echo ($activeTab == 'color-themes') ? 'show active' : ''; ?>"
                                    id="color-themes" role="tabpanel">
                                    <div class="row m-0 p-0">
                                        <?php if (!empty($colorThemes)): ?>
                                            <?php foreach ($colorThemes as $color):
                                                $isBought = in_array($color['colorThemeID'], $boughtColors); ?>
                                                <div class="col-lg-3 col-md-6 col-sm-6 col-6 card-wrapper">
                                                    <div class="customCard shop-card" data-bs-toggle="modal"
                                                        data-bs-target="#cardModal"
                                                        data-id="<?php echo $color['colorThemeID']; ?>"
                                                        data-title="<?php echo htmlspecialchars($color['themeName']); ?>"
                                                        data-price="<?php echo htmlspecialchars($color['price']); ?>"
                                                        data-img=""
                                                        data-desc="<?php echo htmlspecialchars($color['description']); ?>"
                                                        data-isbought="<?php echo $isBought ? '1' : '0'; ?>"
                                                        data-hex="<?php echo htmlspecialchars($color['hexCode']); ?>">

                                                        <div class="cardItem">
                                                            <div class="shopCardImage"
                                                                style="background-color: <?php echo htmlspecialchars($color['hexCode']); ?>;">
                                                            </div>
                                                            <div class="cardContent">
                                                                <div class="cardTitle text-med text-18 text-truncate">
                                                                    <?php echo htmlspecialchars($color['themeName']); ?>
                                                                </div>

                                                                <?php if ($isBought): ?>
                                                                    <div class="badge text-reg text-14 mt-1"
                                                                        style="background-color: rgba(108, 117, 125, 0.2);">Owned
                                                                    </div>
                                                                <?php else: ?>
                                                                    <div
                                                                        class="cardPrice text-med text-18 d-flex align-items-center gap-1 mt-1">
                                                                        <img class="me-1" src="shared/assets/img/webstar.png"
                                                                            width="20">
                                                                        <span><?php echo htmlspecialchars($color['price']); ?></span>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <p class="text-center text-14 text-med mt-1">No color themes available.</p>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- My Items -->
                                <div class="tab-pane fade <?php echo ($activeTab == 'my-items') ? 'show active' : ''; ?>"
                                    id="my-items" role="tabpanel">
                                    <div class="row m-0 p-0" style="min-height:60vh!important">
                                        <?php if (!empty($myItems)): ?>
                                            <div class="d-flex flex-row justify-content-center justify-content-md-start align-items-center pt-4 pt-md-3 pb-4 pb-md-3"
                                                style="margin-bottom:-20px">
                                                <div class="text-center text-md-start text-14 text-med w-75 w-md-100">Want
                                                    to use your new items? Head over to your settings to apply them!
                                                </div>
                                            </div> <?php foreach ($myItems as $item): ?>
                                                <div class="col-lg-3 col-md-6 col-sm-6 col-6 card-wrapper">
                                                    <div class="customCard">
                                                        <div class="cardItem">
                                                            <?php if ($item['emblemID']): ?>
                                                                <div class="shopCardImage"
                                                                    style="padding:10px; display:flex; justify-content:center; align-items:center;">
                                                                    <img src="shared/assets/img/shop/emblems/<?php echo htmlspecialchars($item['emblemImg']); ?>"
                                                                        style="max-width:100%; max-height:100%; object-fit:contain;">
                                                                </div>

                                                                <div class="cardContent">
                                                                    <div class="cardTitle text-med text-18 text-truncate">
                                                                        <?php echo htmlspecialchars($item['emblemTitle']); ?>
                                                                    </div>
                                                                    <div class="badge text-reg text-14 mt-1 text-truncate"
                                                                        style="background-color: rgba(108, 117, 125, 0.2);">
                                                                        Emblem
                                                                    </div>
                                                                </div>
                                                            <?php elseif ($item['coverImageID']): ?>
                                                                <div class="shopCardImage"
                                                                    style="background: url('shared/assets/img/shop/cover-images/<?php echo htmlspecialchars($item['coverImg']); ?>'); background-size: cover; background-position: center;">
                                                                </div>
                                                                <div class="cardContent">
                                                                    <div class="cardTitle text-med text-18 text-truncate">
                                                                        <?php echo htmlspecialchars($item['coverTitle']); ?>
                                                                    </div>
                                                                    <div class="badge text-reg text-14 mt-1 text-truncate"
                                                                        style="background-color: rgba(108, 117, 125, 0.2);">
                                                                        Cover
                                                                        Image
                                                                    </div>
                                                                </div>
                                                            <?php elseif ($item['colorThemeID']): ?>
                                                                <div class="shopCardImage"
                                                                    style="background-color: <?php echo htmlspecialchars($item['hexCode']); ?>; height: 150px;">
                                                                </div>
                                                                <div class="cardContent">
                                                                    <div class="cardTitle text-med text-18 text-truncate">
                                                                        <?php echo htmlspecialchars($item['colorTitle']); ?>
                                                                    </div>
                                                                    <div class="badge text-reg text-14 mt-1 text-truncate"
                                                                        style="background-color: rgba(108, 117, 125, 0.2);">
                                                                        Color
                                                                        Theme
                                                                    </div>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <!-- Empty State -->
                                            <div class="d-flex flex-column justify-content-center align-items-center">
                                                <img src="shared/assets/img/empty/shop.png" width="100">
                                                <div class="text-center text-14 text-med mt-1">No items here.</div>
                                                <div class="text-center text-14 text-reg mt-1">Grab something from the
                                                    shop!
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="cardModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content custom-modal position-relative rounded-4 overflow-hidden">
                    <div class="modal-header border-0 p-0 d-flex justify-content-end">
                        <button type="button" class="custom-close px-4 pt-4" data-bs-dismiss="modal"
                            aria-label="Close">✕</button>
                    </div>
                    <hr class="modal-divider mt-3 mb-3">
                    <div class="modal-body p-0">
                        <div class="container mb-2 px-5">
                            <div class="d-flex flex-column align-items-center gap-4">
                                <div class="modal-img"
                                    style="width:100%; height:200px; background-color: #<?php echo htmlspecialchars($item['hexCode'] ?? 'ffffff'); ?>; background-size:cover; background-position:center;">
                                </div>
                                <div class="text-center">
                                    <div class="mb-1 modalTitle text-sbold text-18" id="modalTitle"></div>
                                    <div class="mb-1 mt-2 description text-med text-14">
                                    </div>
                                    <div
                                        class="title mt-3 d-flex justify-content-center align-items-center gap-1 gap-md-2 mb-3">
                                        <img class="modal-coin" src="shared/assets/img/webstar.png" width="25">
                                        <span class="text-med modal-price text-18" id="modalPrice"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr class="modal-divider mb-2">
                    </div>
                    <div class="modal-footer border-0 px-2 py-1">
                        <form method="POST" class="d-flex justify-content-end pb-2 pe-2 w-100 text-med">
                            <input type="hidden" name="coverImageID" id="coverImageID">
                            <input type="hidden" name="emblemID" id="emblemID">
                            <input type="hidden" name="colorThemeID" id="colorThemeID">

                            <button type="submit" name="buyItem"
                                class="modalButton btn px-4 py-1 rounded-pill me-0 btn-primary">
                                Buy
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
    </div>
    </div>

    <!-- Bootstrap Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Nav Script -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const tabContainer = document.getElementById('mobileTabScroll');
            const scrollLeftBtn = document.getElementById('scrollLeftBtn');
            const scrollRightBtn = document.getElementById('scrollRightBtn');

            function updateArrowVisibility() {
                if (!tabContainer) return;

                scrollLeftBtn.classList.toggle('d-none', tabContainer.scrollLeft === 0);
                scrollRightBtn.classList.toggle('d-none', tabContainer.scrollLeft + tabContainer.clientWidth >= tabContainer.scrollWidth);
            }

            scrollLeftBtn.addEventListener('click', () => {
                tabContainer.scrollBy({
                    left: -100,
                    behavior: 'smooth'
                });
            });

            scrollRightBtn.addEventListener('click', () => {
                tabContainer.scrollBy({
                    left: 100,
                    behavior: 'smooth'
                });
            });

            tabContainer.addEventListener('scroll', updateArrowVisibility);

            updateArrowVisibility();
        });
    </script>

    <!-- JS for Desktop Scroll Buttons -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const desktopTabScroll = document.querySelector(".tab-scroll");
            const desktopScrollLeftBtn = document.getElementById("desktopScrollLeftBtn");
            const desktopScrollRightBtn = document.getElementById("desktopScrollRightBtn");

            function updateDesktopArrowVisibility() {
                if (!desktopTabScroll) return;
                desktopScrollLeftBtn.classList.toggle("d-none", desktopTabScroll.scrollLeft === 0);
                desktopScrollRightBtn.classList.toggle(
                    "d-none",
                    desktopTabScroll.scrollLeft + desktopTabScroll.clientWidth >= desktopTabScroll.scrollWidth
                );
            }

            desktopScrollLeftBtn.addEventListener("click", () => {
                desktopTabScroll.scrollBy({
                    left: -150,
                    behavior: "smooth"
                });
            });

            desktopScrollRightBtn.addEventListener("click", () => {
                desktopTabScroll.scrollBy({
                    left: 150,
                    behavior: "smooth"
                });
            });

            desktopTabScroll.addEventListener("scroll", updateDesktopArrowVisibility);

            updateDesktopArrowVisibility();
        });
    </script>

    <!-- Shop Script -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const modalElement = document.getElementById('cardModal');
            const bsModal = modalElement ? new bootstrap.Modal(modalElement) : null;

            document.body.addEventListener('click', function (e) {
                const card = e.target.closest('.shop-card');
                if (!card) return;
                if (!card.dataset.id) return;

                const modalTitle = document.getElementById('modalTitle');
                const modalPrice = document.getElementById('modalPrice');
                const modalImg = modalElement.querySelector('.modal-img');
                const modalDesc = modalElement.querySelector('.description');
                const modalButton = modalElement.querySelector('.modalButton');
                document.getElementById('coverImageID').value = '';
                document.getElementById('emblemID').value = '';
                document.getElementById('colorThemeID').value = '';

                if (card.dataset.hex) {
                    // Color Theme
                    document.getElementById('colorThemeID').value = card.dataset.id;
                } else if (card.dataset.img) {
                    if (card.dataset.img.includes('emblems')) {
                        document.getElementById('emblemID').value = card.dataset.id;
                    } else {
                        document.getElementById('coverImageID').value = card.dataset.id;
                    }
                }

                if (modalTitle) modalTitle.textContent = card.dataset.title || 'Item';
                if (modalPrice) modalPrice.textContent = (card.dataset.price || '');
                if (modalDesc) modalDesc.textContent = card.dataset.desc || '';

                // Reset styles
                modalImg.style.padding = '';
                modalImg.style.background = '';
                modalImg.style.display = 'flex';
                modalImg.style.justifyContent = 'center';
                modalImg.style.alignItems = 'center';
                modalImg.style.height = '200px';
                modalImg.innerHTML = '';

                // Determine type
                if (card.dataset.hex) {
                    // Color Theme
                    modalImg.style.backgroundColor = card.dataset.hex.startsWith('#') ? card.dataset.hex : `#${card.dataset.hex}`;
                } else if (card.dataset.img) {
                    // Emblem
                    if (card.dataset.title && card.dataset.img.includes('emblems')) {
                        modalImg.style.padding = '10px';
                        const imgEl = document.createElement('img');
                        imgEl.src = card.dataset.img;
                        imgEl.alt = card.dataset.title;
                        imgEl.style.maxWidth = '100%';
                        imgEl.style.maxHeight = '100%';
                        imgEl.style.objectFit = 'contain';
                        modalImg.appendChild(imgEl);
                    } else {
                        // Cover image
                        modalImg.style.background = `url('${card.dataset.img}') center/cover no-repeat`;
                    }
                }

                // Handle bought state
                if (card.dataset.isbought === '1') {
                    modalButton.textContent = 'Bought';
                    modalButton.disabled = true;
                    modalButton.classList.add('btn-secondary');
                } else {
                    modalButton.textContent = 'Buy';
                    modalButton.disabled = false;
                    modalButton.classList.remove('btn-secondary');
                }

                if (bsModal) bsModal.show();
            });
        });
    </script>

    <!-- Nav Link Handling -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const activeTab = new URLSearchParams(window.location.search).get('tab') || 'emblems';
            document.querySelectorAll('.nav-link').forEach(link => {
                const label = link.getAttribute('data-label');
                if (label === activeTab) {
                    link.classList.add('active');
                } else {
                    link.classList.remove('active');
                }
            });
        });
    </script>

    <!-- Toast Handling -->
    <?php if (!empty($toastMessage)): ?>
        <script>
            window.addEventListener('DOMContentLoaded', () => {
                const container = document.getElementById("toastContainer");
                const alert = document.createElement("div");
                alert.className = `alert mb-2 shadow-lg text-med text-12 d-flex align-items-center justify-content-center gap-2 px-3 py-2 <?php echo $toastType; ?>`;
                alert.role = "alert";
                alert.innerHTML = `
        <i class="bi <?php echo ($toastType === 'alert-success') ? 'bi-check-circle-fill' : 'bi-x-circle-fill'; ?> fs-6"></i>
        <span><?php echo $toastMessage; ?></span>
    `;
                container.appendChild(alert);
                setTimeout(() => alert.remove(), 3000);
            });
        </script>
    <?php endif; ?>

</body>

</html>