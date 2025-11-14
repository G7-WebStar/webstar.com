<?php

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

$myEmblems = array_filter($myItems, fn($item) => !empty($item['emblemID']));
$myCovers = array_filter($myItems, fn($item) => !empty($item['coverImageID']));
$myThemes = array_filter($myItems, fn($item) => !empty($item['colorThemeID']));

// Get current profile selection
$profileQuery = "SELECT * FROM profile WHERE userID = $userID LIMIT 1";
$profile = $conn->query($profileQuery)->fetch_assoc();

?>

<div class="container">

    <!-- Bio -->
    <form method="POST" id="bioForm">
        <div class="row mt-3 pt-1  mb-3">
            <div class="col-12 col-md-6 mb-2 d-flex align-items-center">
                <div class="text-sbold text-16 me-2 me-md-3">Bio</div>
                <button type="submit" name="saveBio" id="saveBioBtn" class="btn rounded-5 text-reg text-12"
                    style="background-color: var(--primaryColor); border: 1px solid var(--black); width: fit-content; display: none;">
                    Save changes
                </button>
            </div>
            <div class="col-12 text-reg text-14 mb-2" style="text-align: justify;">
                Write a short description about yourself! Your bio appears on your profile page and helps others get to
                know you better.
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12 col-md-6">
                <textarea name="bio" id="bio" class="form-control text-reg text-14 p-3" rows="4" maxlength="300"
                    placeholder="Tell something about yourself..."
                    style="border-radius: 10px; border: 1px solid var(--black); resize: none;"><?= htmlspecialchars($profile['bio'] ?? '') ?></textarea>
            </div>
        </div>
    </form>

    <!-- Logo Emblems -->
    <form method="POST">
        <div class="row mb-4">
            <div class="col-12 col-md-6 mb-2 d-flex align-items-center">
                <div class="text-sbold text-16 me-3"> Logo Emblems </div>
                <button type="submit" name="saveLogo" id="saveLogoBtn" class="btn rounded-5 text-reg text-12"
                    style="background-color: var(--primaryColor); border: 1px solid var(--black); width: fit-content; display:none;">
                    Save changes
                </button>
                <input type="hidden" id="selectedEmblem" name="selectedEmblem"
                    value="<?= $profile['emblemID'] ?? '' ?>">
            </div>
            <div class="col-12 text-reg text-14 mb-3" style="text-align: justify;">
                Choose an emblem to display on your profile! You can buy more emblems from the Shop.
            </div>
        </div>

        <!-- Emblem Gallery -->
        <div class="row align-items-center">
            <!-- DESKTOP -->
            <div class="d-none d-md-flex align-items-center w-100">
                <div class="col-auto d-flex align-items-center">
                    <button type="button" id="prevBtnEmblem"
                        class="btn p-0 d-flex align-items-center justify-content-center rounded-circle border-0"
                        style="width: 40px; height: 40px; background: transparent; color: var(--black); margin-top: -50px;transform:none !important; box-shadow:none !important;">
                        <span class="material-symbols-outlined" style="font-size: 30px;">arrow_back_ios</span>
                    </button>
                </div>
                <div class="col position-relative w-100 overflow-hidden" style="height: 200px;">
                    <div id="carouselWrapperEmblem" style="overflow: hidden; width: 100%;">
                        <div id="thumbnailCarouselEmblem" style="display: flex; transition: transform 0.6s ease;">
                            <?php if (!empty($myEmblems)): ?>
                                <?php
                                usort($myEmblems, fn($a, $b) => ($a['emblemID'] == $profile['emblemID'] ? -1 : 1));
                                foreach ($myEmblems as $emblem):
                                    $selected = ($emblem['emblemID'] == $profile['emblemID']);
                                    ?>
                                    <div class="card emblem-item" data-id="<?= $emblem['emblemID'] ?>"
                                        style="flex: 0 0 240px; margin-right: 12px; border-radius: 15px; cursor: pointer; border: <?= $selected ? '3px solid var(--black)' : '3px solid var(--dirtyWhite)' ?>; overflow: hidden;">
                                        <div class="card-body d-flex align-items-center justify-content-center p-2">
                                            <img src="shared/assets/img/shop/emblems/<?= htmlspecialchars($emblem['emblemImg']) ?>"
                                                alt="<?= htmlspecialchars($emblem['emblemTitle']) ?>" class="img-fluid"
                                                style="max-height:140px; object-fit:contain; user-select: none;">
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-muted text-reg text-center w-100">No emblems owned yet.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-auto d-flex align-items-center">
                    <button type="button" id="nextBtnEmblem"
                        class="btn p-0 d-flex align-items-center justify-content-center rounded-circle border-0"
                        style="width: 40px; height: 40px; background: transparent; color: var(--black); margin-top: -50px;transform:none !important; box-shadow:none !important;">
                        <span class="material-symbols-outlined" style="font-size: 30px;">arrow_forward_ios</span>
                    </button>
                </div>
            </div>

            <!-- MOBILE -->
            <div class="d-flex d-md-none mb-4"
                style="overflow-x: auto; overflow-y: hidden; scroll-behavior: smooth; gap: 12px; height: 170px; -webkit-overflow-scrolling: touch; padding-bottom: 8px;">
                <?php foreach ($myEmblems as $emblem):
                    $selected = ($emblem['emblemID'] == $profile['emblemID']);
                    ?>
                    <div class="flex-shrink-0">
                        <div class="card-body emblem-item p-2 d-flex align-items-center justify-content-center"
                            data-id="<?= $emblem['emblemID'] ?>"
                            style="width: 230px; border: <?= $selected ? '3px solid var(--black)' : '3px solid var(--dirtyWhite)' ?>; border-radius: 15px; height: 150px; background-color:white; cursor: pointer;">
                            <img src="shared/assets/img/shop/emblems/<?= htmlspecialchars($emblem['emblemImg']) ?>"
                                alt="<?= htmlspecialchars($emblem['emblemTitle']) ?>" class="img-fluid"
                                style="object-fit: contain;">
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </form>

    <!-- Cover Photos -->
    <form method="POST">
        <div class="row mb-3">
            <div class="col-12 col-md-6 mb-2 d-flex align-items-center">
                <div class="text-sbold text-16 me-3"> Cover Photo </div>
                <button type="submit" name="saveCover" id="saveCoverBtn" class="btn rounded-5 text-reg text-12"
                    style="background-color: var(--primaryColor); border: 1px solid var(--black); width: fit-content; display:none;">
                    Save changes
                </button>
                <input type="hidden" id="selectedCover" name="selectedCover"
                    value="<?= $profile['coverImageID'] ?? '' ?>">
            </div>
            <div class="col-12 text-reg text-14 mb-3"
                style="white-space: normal; word-wrap: break-word; text-align: justify;">
                Choose a cover photo that reflects your style. You can unlock more designs from the Shop.
            </div>
        </div>

        <div class="row align-items-center">
            <!-- DESKTOP -->
            <div class="d-none d-md-flex align-items-center w-100">
                <div class="col-auto d-flex align-items-center">
                    <button type="button" id="prevBtnCover"
                        class="btn p-0 d-flex align-items-center justify-content-center rounded-circle border-0"
                        style="width: 40px; height: 40px; background: transparent; color: var(--black); margin-top: -50px;transform:none !important; box-shadow:none !important;">
                        <span class="material-symbols-outlined" style="font-size: 30px;">arrow_back_ios</span>
                    </button>
                </div>
                <div class="col position-relative w-100 overflow-hidden" style="height: 130px;">
                    <div id="carouselWrapperCover" style="overflow: hidden; width: 100%;">
                        <div id="thumbnailCarouselCover" style="display: flex; transition: transform 0.6s ease;">
                            <?php if (!empty($myCovers)): ?>
                                <?php
                                usort($myCovers, fn($a, $b) => ($a['coverImageID'] == $profile['coverImageID'] ? -1 : 1));
                                foreach ($myCovers as $cover):
                                    $selected = ($cover['coverImageID'] == $profile['coverImageID']);
                                    ?>
                                    <div class="card cover-item" data-id="<?= $cover['coverImageID'] ?>"
                                        style="flex: 0 0 240px; margin-right: 12px; border-radius: 10px; cursor: pointer; border: <?= $selected ? '3px solid var(--black)' : '3px solid var(--dirtyWhite)' ?>; overflow: hidden;">
                                        <div class="card-body d-flex align-items-center justify-content-center p-0">
                                            <img src="shared/assets/img/shop/cover-images/<?= htmlspecialchars($cover['coverImg']) ?>"
                                                alt="<?= htmlspecialchars($cover['coverTitle']) ?>" class="img-fluid"
                                                style="max-height:240px; object-fit:cover; width:100%; user-select: none;">
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-muted text-reg text-center w-100">No cover photos owned yet.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-auto d-flex align-items-center">
                    <button type="button" id="nextBtnCover"
                        class="btn p-0 d-flex align-items-center justify-content-center rounded-circle border-0"
                        style="width: 40px; height: 40px; background: transparent; color: var(--black); margin-top: -50px;transform:none !important; box-shadow:none !important;">
                        <span class="material-symbols-outlined" style="font-size: 30px;">arrow_forward_ios</span>
                    </button>
                </div>
            </div>

            <!-- MOBILE -->
            <div class="d-flex d-md-none mb-4"
                style="overflow-x: auto; overflow-y: hidden; scroll-behavior: smooth; gap: 12px; height: 110px; -webkit-overflow-scrolling: touch; padding-bottom: 8px;">
                <?php foreach ($myCovers as $cover):
                    $selected = ($cover['coverImageID'] == $profile['coverImageID']);
                    ?>
                    <div class="flex-shrink-0">
                        <div class="card-body cover-item p-0 d-flex align-items-center justify-content-center"
                            data-id="<?= $cover['coverImageID'] ?>"
                            style="width: 220px; border: <?= $selected ? '3px solid var(--black)' : '3px solid var(--dirtyWhite)' ?>; border-radius: 13px; background-color: white; cursor: pointer;">
                            <img src="shared/assets/img/shop/cover-images/<?= htmlspecialchars($cover['coverImg']) ?>"
                                alt="<?= htmlspecialchars($cover['coverTitle']) ?>" class="img-fluid"
                                style="object-fit: contain; border-radius: 10px;">
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </form>

    <!-- Color Themes -->
    <form method="POST">
        <div class="row mb-3">
            <div class="col-12 col-md-6 mb-2 d-flex align-items-center">
                <div class="text-sbold text-16 me-2 me-md-3">Color Theme</div>
                <button type="submit" name="saveProfile" id="saveProfileBtn" class="btn rounded-5 text-reg text-12"
                    style="background-color: var(--primaryColor); border: 1px solid var(--black); width: fit-content; display:none;">
                    Save changes
                </button>
                <input type="hidden" id="selectedTheme" name="selectedTheme"
                    value="<?= $profile['colorThemeID'] ?? '' ?>">
            </div>
            <div class="col-12 text-reg text-14 mb-3"
                style="white-space: normal; word-wrap: break-word; text-align: justify;">
                Select a color theme to give your profile page a vibrant and personalized look. Explore more unique and
                dynamic themes in the Shop.
            </div>
        </div>

        <div class="row mb-3 justify-content-center align-items-center text-center" style="margin-top: -50px;">
            <!-- DESKTOP -->
            <div class="d-none d-md-flex align-items-center w-100">
                <div class="col-auto d-flex justify-content-center align-items-center">
                    <button type="button" id="prevBtnProfile"
                        class="btn p-0 d-flex align-items-center justify-content-center rounded-circle border-0"
                        style="width: 40px; height: 40px; background: transparent; color: var(--black);transform:none !important; box-shadow:none !important;">
                        <span class="material-symbols-outlined" style="font-size: 30px;">arrow_back_ios</span>
                    </button>
                </div>

                <div class="col d-flex justify-content-start align-items-center position-relative overflow-hidden"
                    style="height: 200px;">
                    <div id="carouselWrapperProfile" style="overflow: hidden; width: 100%;">
                        <div id="thumbnailCarouselProfile" style="display: flex; transition: transform 0.6s ease;">
                            <?php if (!empty($myThemes)): ?>
                                <?php
                                usort($myThemes, fn($a, $b) => ($a['colorThemeID'] == $profile['colorThemeID'] ? -1 : 1));
                                foreach ($myThemes as $theme):
                                    $selected = ($theme['colorThemeID'] == $profile['colorThemeID']);
                                    ?>
                                    <div class="card profile-item rounded-circle bg-white d-flex align-items-center justify-content-center overflow-hidden"
                                        data-id="<?= $theme['colorThemeID'] ?>"
                                        style="width: 100px; height: 100px; cursor: pointer; margin-right: 10px; flex-shrink: 0; border: <?= $selected ? '3px solid var(--black)' : '3px solid var(--dirtyWhite)' ?>;">
                                        <div class="rounded-circle"
                                            style="width: 100%; height: 100%; background-color: <?= htmlspecialchars($theme['hexCode']) ?>;">
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-muted text-reg text-center w-100">No color themes owned yet.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-auto d-flex justify-content-center align-items-center">
                    <button type="button" id="nextBtnProfile"
                        class="btn p-0 d-flex align-items-center justify-content-center rounded-circle border-0"
                        style="width: 40px; height: 40px; background: transparent; color: var(--black);transform:none !important; box-shadow:none !important;">
                        <span class="material-symbols-outlined" style="font-size: 30px;">arrow_forward_ios</span>
                    </button>
                </div>
            </div>

            <!-- MOBILE -->
            <div class="d-flex d-md-none align-items-center w-100" style="overflow-x: auto; scroll-behavior: smooth;">
                <div class="d-flex flex-row align-items-center px-2" style="gap: 10px; height: 170px;">
                    <?php foreach ($myThemes as $theme):
                        $selected = ($theme['colorThemeID'] == $profile['colorThemeID']);
                        ?>
                        <div class="card border-0 bg-transparent" style="flex: 0 0 auto;">
                            <div class="card-body d-flex align-items-center justify-content-center p-0">
                                <div class="profile-item rounded-circle bg-white d-flex align-items-center justify-content-center overflow-hidden"
                                    data-id="<?= $theme['colorThemeID'] ?>"
                                    style="width: 100px; height: 100px; cursor: pointer;border: <?= $selected ? '3px solid var(--black)' : '3px solid var(--dirtyWhite)' ?>;">
                                    <div class="rounded-circle"
                                        style="width: 100%; height: 100%; background-color: <?= htmlspecialchars($theme['hexCode']) ?>;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </form>
</div>

<script>

    function setupSelectableItems(itemClass, hiddenInputId, saveBtnId) {
        const items = document.querySelectorAll(`.${itemClass}`);
        const hiddenInput = document.getElementById(hiddenInputId);
        const saveBtn = document.getElementById(saveBtnId);

        items.forEach(item => {
            item.addEventListener("click", () => {
                items.forEach(i => i.style.setProperty("border", "3px solid var(--dirtyWhite)", "important"));
                item.style.setProperty("border", "3px solid var(--black)", "important");
                hiddenInput.value = item.dataset.id;
                saveBtn.style.display = "inline-block";
            });
        });
    }

    setupSelectableItems("emblem-item", "selectedEmblem", "saveLogoBtn");
    setupSelectableItems("cover-item", "selectedCover", "saveCoverBtn");
    setupSelectableItems("profile-item", "selectedTheme", "saveProfileBtn");

    const bioTextarea = document.getElementById('bio');
    const bioSaveBtn = document.getElementById('saveBioBtn');
    const originalBio = bioTextarea.value.trim();

    bioTextarea.addEventListener('input', () => {
        const currentBio = bioTextarea.value.trim();
        if (currentBio !== originalBio) {
            bioSaveBtn.style.display = "inline-block";
        } else {
            bioSaveBtn.style.display = "none";
        }
    });
</script>
<script>
    function setupCarousel(wrapperId, carouselId, prevBtnId, nextBtnId, itemClass) {
        const wrapper = document.getElementById(wrapperId);
        const carousel = document.getElementById(carouselId);
        const prevBtn = document.getElementById(prevBtnId);
        const nextBtn = document.getElementById(nextBtnId);

        if (!wrapper || !carousel || !prevBtn || !nextBtn) return;

        const items = Array.from(carousel.querySelectorAll(`.${itemClass}`));
        if (!items.length) return;

        let currentIndex = 0;

        function updateCarousel() {
            // Fix for circle themes that have flex gaps and wrapper paddings
            const style = getComputedStyle(items[0]);
            const marginRight = parseInt(style.marginRight) || 12;
            const itemWidth = items[0].offsetWidth + marginRight;

            // Use wrapper.clientWidth instead of offsetWidth (more consistent)
            const visibleCount = Math.max(1, Math.floor(wrapper.clientWidth / itemWidth));
            const maxIndex = Math.max(0, items.length - visibleCount);

            currentIndex = Math.min(Math.max(0, currentIndex), maxIndex);
            const offset = currentIndex * itemWidth;

            carousel.style.transform = `translateX(-${offset}px)`;
            prevBtn.disabled = currentIndex === 0;
            nextBtn.disabled = currentIndex === maxIndex;
            prevBtn.style.opacity = currentIndex === 0 ? "0.4" : "1";
            nextBtn.style.opacity = currentIndex === maxIndex ? "0.4" : "1";
        }

        prevBtn.addEventListener("click", () => {
            currentIndex--;
            updateCarousel();
        });

        nextBtn.addEventListener("click", () => {
            currentIndex++;
            updateCarousel();
        });

        window.addEventListener("resize", updateCarousel);
        updateCarousel();
    }

    // Initialize
    setupCarousel("carouselWrapperEmblem", "thumbnailCarouselEmblem", "prevBtnEmblem", "nextBtnEmblem", "emblem-item");
    setupCarousel("carouselWrapperCover", "thumbnailCarouselCover", "prevBtnCover", "nextBtnCover", "cover-item");
    setupCarousel("carouselWrapperProfile", "thumbnailCarouselProfile", "prevBtnProfile", "nextBtnProfile", "profile-item");

</script>