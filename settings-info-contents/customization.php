<div class="container">
    <form method="POST" enctype="multipart/form-data">
        <div class="row mb-3">
            <div class="col-12 col-md-6 mb-2 d-flex align-items-center">
                <div class="text-bold text-20 me-2"> Logo Emblems </div>
                <button type="submit" name="saveLogo" id="saveLogoBtn" class="btn rounded-5 text-reg text-12"
                    style="background-color: var(--primaryColor); border: 1px solid var(--black); width: fit-content;">
                    Save changes
                </button>
            </div>
            <div class="col-12 text-reg text-14 mb-3" style="text-align: justify;">
                Choose an emblem to display on your profile! You can buy more emblems from the Shop.
            </div>
        </div>

        <!-- Emblem gallery -->
        <div class="row align-items-center">
            <!-- DESKTOP VERSION -->
            <div class="d-none d-md-flex align-items-center w-100">
                <!-- Left Arrow -->
                <div class="col-auto d-flex align-items-center">
                    <button type="button" id="prevBtnEmblem"
                        class="btn p-0 d-flex align-items-center justify-content-center rounded-circle border-0"
                        style="width: 40px; height: 40px; background: transparent; color: var(--black); margin-top: -50px;">
                        <span class="material-symbols-outlined" style="font-size: 30px;">arrow_back_ios</span>
                    </button>
                </div>

                <!-- Gallery -->
                <div class="col position-relative w-100 overflow-hidden" style="height: 200px;">
                    <div id="carouselWrapperEmblem" style="overflow: hidden; width: 100%;">
                        <div id="thumbnailCarouselEmblem" style="display: flex; transition: transform 0.6s ease;">
                            <!-- ITEMS -->
                            <div class="card emblem-item"
                                style="flex: 0 0 240px; margin-right: 12px; border-radius: 15px;  cursor: pointer; border: 1px solid var(--black);">
                                <div class="card-body d-flex align-items-center justify-content-center p-2">
                                    <img src="shared/assets/img/logo-emblems/badge 2.png" class="img-fluid"
                                        style="max-height:140px; object-fit:contain;">
                                </div>
                            </div>

                            <div class="card emblem-item"
                                style="flex: 0 0 240px; margin-right: 12px; border-radius: 15px;  cursor: pointer; border: 1px solid var(--black);">
                                <div class="card-body d-flex align-items-center justify-content-center p-2">
                                    <img src="shared/assets/img/logo-emblems/bsit.png" class="img-fluid"
                                        style="max-height:140px; object-fit:contain;">
                                </div>
                            </div>

                            <div class="card emblem-item"
                                style="flex: 0 0 240px; margin-right: 12px; border-radius: 15px;  cursor: pointer; border: 1px solid var(--black);">
                                <div class="card-body d-flex align-items-center justify-content-center p-2">
                                    <img src="shared/assets/img/logo-emblems/bsitlog 1.png" class="img-fluid"
                                        style="max-height:140px; object-fit:contain;">
                                </div>
                            </div>

                            <div class="card emblem-item"
                                style="flex: 0 0 240px; margin-right: 12px; border-radius: 15px;  cursor: pointer; border: 1px solid var(--black);">
                                <div class="card-body d-flex align-items-center justify-content-center p-2">
                                    <img src="shared/assets/img/logo-emblems/comsoc.png" class="img-fluid"
                                        style="max-height:140px; object-fit:contain;">
                                </div>
                            </div>

                            <div class="card emblem-item"
                                style="flex: 0 0 240px; margin-right: 12px; border-radius: 15px;  cursor: pointer; border: 1px solid var(--black);">
                                <div class="card-body d-flex align-items-center justify-content-center p-2">
                                    <img src="shared/assets/img/logo-emblems/crammer club 1.png" class="img-fluid"
                                        style="max-height:140px; object-fit:contain;">
                                </div>
                            </div>

                            <div class="card emblem-item"
                                style="flex: 0 0 240px; margin-right: 12px; border-radius: 15px;  cursor: pointer; border: 1px solid var(--black);">
                                <div class="card-body d-flex align-items-center justify-content-center p-2">
                                    <img src="shared/assets/img/logo-emblems/crammer club 2.png" class="img-fluid"
                                        style="max-height:140px; object-fit:contain;">
                                </div>
                            </div>

                            <div class="card emblem-item"
                                style="flex: 0 0 240px; margin-right: 12px; border-radius: 15px;  cursor: pointer; border: 1px solid var(--black);">
                                <div class="card-body d-flex align-items-center justify-content-center p-2">
                                    <img src="shared/assets/img/logo-emblems/logo.png" class="img-fluid"
                                        style="max-height:140px; object-fit:contain;">
                                </div>
                            </div>

                            <div class="card emblem-item"
                                style="flex: 0 0 240px; margin-right: 12px; border-radius: 15px;  cursor: pointer; border: 1px solid var(--black);">
                                <div class="card-body d-flex align-items-center justify-content-center p-2">
                                    <img src="shared/assets/img/logo-emblems/webstar univ 1.png" class="img-fluid"
                                        style="max-height:140px; object-fit:contain;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Arrow -->
                <div class="col-auto d-flex align-items-center">
                    <button type="button" id="nextBtnEmblem"
                        class="btn p-0 d-flex align-items-center justify-content-center rounded-circle border-0"
                        style="width: 40px; height: 40px; background: transparent; color: var(--black); margin-top: -50px;">
                        <span class="material-symbols-outlined" style="font-size: 30px;">arrow_forward_ios</span>
                    </button>
                </div>
            </div>


            <!-- MOBILE VERSION -->
            <div class="d-flex d-md-none mb-4"
                style="overflow-x: auto; overflow-y: hidden; scroll-behavior: smooth; gap: 12px; height: 170px; -webkit-overflow-scrolling: touch; padding-bottom: 8px;">

                <!-- CARD 1 -->
                <div class="flex-shrink-0">
                    <div class="card-body cover-item p-2 d-flex align-items-center justify-content-center"
                        style="width: 230px; border: 1px solid var(--black); border-radius: 15px; height: 150px;">
                        <img src="shared/assets/img/logo-emblems/badge 2.png" class="img-fluid" alt="Cover"
                            style="object-fit: contain;">
                    </div>
                </div>

                <!-- CARD 2 -->
                <div class="flex-shrink-0">
                    <div class="card-body cover-item p-2 d-flex align-items-center justify-content-center"
                        style="width: 230px; border: 1px solid var(--black); border-radius: 15px; height: 150px;">
                        <img src="shared/assets/img/logo-emblems/bsit.png" class="img-fluid" alt="Cover"
                            style="object-fit: contain;">
                    </div>
                </div>

                <!-- CARD 3 -->
                <div class="flex-shrink-0">
                    <div class="card-body cover-item p-2 d-flex align-items-center justify-content-center"
                        style="width: 230px; border: 1px solid var(--black); border-radius: 15px; height: 150px;">
                        <img src="shared/assets/img/logo-emblems/comsoc.png" class="img-fluid" alt="Cover"
                            style="object-fit: contain;">
                    </div>
                </div>
            </div>

        </div>
    </form>

    <!-- Cover Photo -->
    <form method="POST" enctype="multipart/form-data">
        <div class="row mb-3">
            <div class="col-12 col-md-6 mb-2 d-flex align-items-center">
                <div class="text-bold text-20 me-2"> Cover Photo </div>
                <button type="submit" name="saveCover" id="saveCoverBtn" class="btn rounded-5 text-reg text-12"
                    style="background-color: var(--primaryColor); border: 1px solid var(--black); width: fit-content;">
                    Save changes
                </button>
            </div>
            <div class="col-12 text-reg text-14 mb-3"
                style="white-space: normal; word-wrap: break-word; text-align: justify;">
                Choose a cover photo that reflects your style. You can unlock more designs from the Shop.
            </div>
        </div>

        <!-- Pictures -->
        <div class="row align-items-center">
            <!-- DESKTOP VERSION -->
            <div class="d-none d-md-flex align-items-center w-100">
                <!-- Left Arrow -->
                <div class="col-auto d-flex align-items-center">
                    <button type="button" id="prevBtnCover"
                        class="btn p-0 d-flex align-items-center justify-content-center rounded-circle border-0"
                        style="width: 40px; height: 40px; background: transparent; color: var(--black); margin-top: -50px;">
                        <span class="material-symbols-outlined" style="font-size: 30px;">arrow_back_ios</span>
                    </button>
                </div>

                <!-- Gallery -->
                <div class="col position-relative w-100 overflow-hidden" style="height: 180px;">
                    <div id="carouselWrapperCover" style="overflow: hidden; width: 100%;">
                        <div id="thumbnailCarouselCover" style="display: flex; transition: transform 0.6s ease;">
                            <!-- ITEMS -->
                            <div class="card cover-item"
                                style="flex: 0 0 240px; margin-right: 12px; border-radius: 15px;  cursor: pointer; border: 1px solid var(--black);">
                                <div class="card-body d-flex align-items-center justify-content-center p-2">
                                    <img src="shared/assets/img/logo-emblems/badge 2.png" class="img-fluid"
                                        style="max-height:140px; object-fit:contain;">
                                </div>
                            </div>

                            <div class="card cover-item"
                                style="flex: 0 0 240px; margin-right: 12px; border-radius: 15px;  cursor: pointer; border: 1px solid var(--black);">
                                <div class="card-body d-flex align-items-center justify-content-center p-2">
                                    <img src="shared/assets/img/logo-emblems/bsit.png" class="img-fluid"
                                        style="max-height:140px; object-fit:contain;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Arrow -->
                <div class="col-auto d-flex align-items-center">
                    <button type="button" id="nextBtnCover"
                        class="btn p-0 d-flex align-items-center justify-content-center rounded-circle border-0"
                        style="width: 40px; height: 40px; background: transparent; color: var(--black); margin-top: -50px;">
                        <span class="material-symbols-outlined" style="font-size: 30px;">arrow_forward_ios</span>
                    </button>
                </div>
            </div>

            <!-- MOBILE VERSION -->
            <div class="d-flex d-md-none mb-4"
                style="overflow-x: auto; overflow-y: hidden; scroll-behavior: smooth; gap: 12px; height: 170px; -webkit-overflow-scrolling: touch; padding-bottom: 8px;">

                <!-- CARD 1 -->
                <div class="flex-shrink-0">
                    <div class="card-body cover-item p-2 d-flex align-items-center justify-content-center"
                        style="width: 220px; border: 1px solid var(--black); border-radius: 15px; height: 150px;">
                        <img src="shared/assets/img/logo-emblems/badge 2.png" class="img-fluid" alt="Cover"
                            style="object-fit: contain;">
                    </div>
                </div>

                <!-- CARD 2 -->
                <div class="flex-shrink-0">
                    <div class="card-body cover-item p-2 d-flex align-items-center justify-content-center"
                        style="width: 220px; border: 1px solid var(--black); border-radius: 15px; height: 150px;">
                        <img src="shared/assets/img/logo-emblems/bsit.png" class="img-fluid" alt="Cover"
                            style="object-fit: contain;">
                    </div>
                </div>
            </div>

        </div>
    </form>

    <!-- Profile Picture -->
    <form method="POST" enctype="multipart/form-data">
        <div class="row mb-3">
            <div
                class="col-12 col-md-6 mb-2 d-flex flex-column flex-sm-row flex-md-row align-items-start align-items-md-center">
                <div class="text-bold text-20 me-0 me-md-2 mb-2 mb-md-0">
                    Moving Profile Picture
                </div>
                <button type="submit" name="saveProfile" id="saveProfileBtn" class="btn rounded-5 text-reg text-12"
                    style="background-color: var(--primaryColor); border: 1px solid var(--black); width: fit-content;">
                    Save changes
                </button>
            </div>
            <div class="col-12 text-reg text-14 mb-3"
                style="white-space: normal; word-wrap: break-word; text-align: justify;">
                Select a moving profile picture to bring your profile to life. More animated styles are available in the
                Shop.
            </div>
        </div>

        <!-- Profile -->
        <div class="row mb-3 justify-content-center align-items-center text-center" style="margin-top: -50px;">
            <!-- DESKTOP VERSION -->
            <div class="d-none d-md-flex align-items-center w-100">
                <!-- Left Arrow -->
                <div class="col-auto d-flex justify-content-center align-items-center">
                    <button type="button" id="prevBtnProfile"
                        class="btn p-0 d-flex align-items-center justify-content-center rounded-circle border-0"
                        style="width: 40px; height: 40px; background: transparent; color: var(--black);">
                        <span class="material-symbols-outlined" style="font-size: 30px;">arrow_back_ios</span>
                    </button>
                </div>

                <!-- Gallery -->
                <div class="col d-flex justify-content-start align-items-center position-relative overflow-hidden"
                    style="height: 200px;">
                    <div id="carouselWrapperProfile" style="overflow: hidden; width: 100%;">
                        <div id="thumbnailCarouselProfile"
                            style="display: flex; justify-content: flex-start; align-items: center; transition: transform 0.6s ease;">
                            <!-- ITEM 1 -->
                            <div class="card profile-item border border-dark rounded-circle bg-white d-flex align-items-center justify-content-center overflow-hidden"
                                style="width: 100px; height: 100px; cursor: pointer; margin-right: 10px; flex-shrink: 0;">
                                <img src="shared/assets/img/logo-emblems/badge 2.png"
                                    class="w-100 h-100 object-fit-cover rounded-circle">
                            </div>
                            <!-- ITEM 2 -->
                            <div class="card profile-item border border-dark rounded-circle bg-white d-flex align-items-center justify-content-center overflow-hidden"
                                style="width: 100px; height: 100px; cursor: pointer; margin-right: 10px; flex-shrink: 0;">
                                <img src="shared/assets/img/logo-emblems/bsit.png"
                                    class="w-100 h-100 object-fit-cover rounded-circle">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Arrow -->
                <div class="col-auto d-flex justify-content-center align-items-center">
                    <button type="button" id="nextBtnProfile"
                        class="btn p-0 d-flex align-items-center justify-content-center rounded-circle border-0"
                        style="width: 40px; height: 40px; background: transparent; color: var(--black);">
                        <span class="material-symbols-outlined" style="font-size: 30px;">arrow_forward_ios</span>
                    </button>
                </div>
            </div>

            <!-- MOBILE VERSION -->
            <div class="d-flex d-md-none align-items-center w-100" style="overflow-x: auto; scroll-behavior: smooth;">
                <div class="d-flex flex-row align-items-center px-2" style="gap: 10px; height: 170px;">

                    <!-- ITEM 1 -->
                    <div class="card border-0 bg-transparent" style="flex: 0 0 auto;">
                        <div class="card-body d-flex align-items-center justify-content-center p-0">
                            <div class="profile-item border border-dark rounded-circle bg-white d-flex align-items-center justify-content-center overflow-hidden"
                                style="width: 100px; height: 100px; cursor: pointer;">
                                <img src="shared/assets/img/logo-emblems/badge 2.png"
                                    class="w-100 h-100 object-fit-cover rounded-circle">
                            </div>
                        </div>
                    </div>

                    <!-- ITEM 2 -->
                    <div class="card border-0 bg-transparent" style="flex: 0 0 auto;">
                        <div class="card-body d-flex align-items-center justify-content-center p-0">
                            <div class="profile-item border border-dark rounded-circle bg-white d-flex align-items-center justify-content-center overflow-hidden"
                                style="width: 100px; height: 100px; cursor: pointer;">
                                <img src="shared/assets/img/logo-emblems/bsit.png"
                                    class="w-100 h-100 object-fit-cover rounded-circle">
                            </div>
                        </div>
                    </div>

                    <!-- ITEM 3 -->
                    <div class="card border-0 bg-transparent" style="flex: 0 0 auto;">
                        <div class="card-body d-flex align-items-center justify-content-center p-0">
                            <div class="profile-item border border-dark rounded-circle bg-white d-flex align-items-center justify-content-center overflow-hidden"
                                style="width: 100px; height: 100px; cursor: pointer;">
                                <img src="shared/assets/img/logo-emblems/comsoc.png"
                                    class="w-100 h-100 object-fit-cover rounded-circle">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>

<script>
    // --- DESKTOP CAROUSELS ---
    function setupCarousel(wrapperId, carouselId, prevBtnId, nextBtnId, itemClass, isCircle = false) {
        const wrapper = document.getElementById(wrapperId);
        const carousel = document.getElementById(carouselId);
        const prevBtn = document.getElementById(prevBtnId);
        const nextBtn = document.getElementById(nextBtnId);

        // 🔧 FIX: Scope items only to this carousel
        const allItems = Array.from(carousel.querySelectorAll(`.${itemClass}`));
        const images = allItems.map(item => item.querySelector("img")).filter(Boolean);
        let imagesLoaded = 0;

        function initCarousel() {
            const items = allItems.filter(item => item.querySelector("img"));
            if (items.length === 0) return;

            let currentIndex = 0;

            function updateCarousel() {
                const style = getComputedStyle(items[0]);
                const marginRight = parseInt(style.marginRight) || 0;
                const itemWidth = items[0].offsetWidth + marginRight;
                const visibleCount = Math.max(1, Math.floor(wrapper.offsetWidth / itemWidth));
                const maxIndex = Math.max(0, items.length - visibleCount);

                currentIndex = Math.max(0, Math.min(currentIndex, maxIndex));
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

            // --- Item click effect ---
            items.forEach(item => {
                item.addEventListener("click", () => {
                    // Reset all items
                    items.forEach(i => {
                        i.style.setProperty("border", "1px solid var(--black)", "important");
                        i.style.background = "white";
                    });

                    // Apply active style
                    if (isCircle) {
                        item.style.setProperty("border", "1px solid var(--primaryColor)", "important");
                    } else {
                        item.style.setProperty("border", "1px solid var(--primaryColor)", "important");
                        item.style.background = "var(--primaryColor)";
                    }
                });
            });

            window.addEventListener("resize", updateCarousel);
            updateCarousel();
        }

        // Wait for images to load before initializing carousel
        images.forEach(img => {
            if (img.complete && img.naturalHeight > 0) {
                imagesLoaded++;
            } else {
                img.addEventListener("load", () => {
                    if (++imagesLoaded === images.length) initCarousel();
                });
                img.addEventListener("error", () => {
                    if (++imagesLoaded === images.length) initCarousel();
                });
            }
        });

        if (imagesLoaded === images.length) initCarousel();
    }

    // --- MOBILE CLICK EFFECTS (MATCH DESKTOP BEHAVIOR) ---
    function applyClickEffectForMobile(itemClass, isCircle = false) {
        const items = document.querySelectorAll(`.${itemClass}`);

        items.forEach(item => {
            item.addEventListener("click", () => {
                // Only affect items inside the same mobile section
                const section = item.closest(".d-flex.d-md-none");
                if (!section) return;

                const sectionItems = section.querySelectorAll(`.${itemClass}`);

                // Reset all items in that section
                sectionItems.forEach(i => {
                    i.style.setProperty("border", "1px solid var(--black)", "important");
                    i.style.background = "white";
                    i.style.borderRadius = isCircle ? "50%" : "15px";
                });

                // Apply active color (selected item)
                item.style.setProperty("border", "1px solid var(--primaryColor)", "important");
                item.style.background = "var(--primaryColor)";
                item.style.borderRadius = isCircle ? "50%" : "15px";
            });
        });
    }


    // --- Initialize Desktop ---
    setupCarousel("carouselWrapperEmblem", "thumbnailCarouselEmblem", "prevBtnEmblem", "nextBtnEmblem", "emblem-item");
    setupCarousel("carouselWrapperCover", "thumbnailCarouselCover", "prevBtnCover", "nextBtnCover", "cover-item");
    setupCarousel("carouselWrapperProfile", "thumbnailCarouselProfile", "prevBtnProfile", "nextBtnProfile", "profile-item", true);

    // --- Initialize Mobile ---
    applyClickEffectForMobile("emblem-item");
    applyClickEffectForMobile("cover-item");
    applyClickEffectForMobile("profile-item", true);
</script>