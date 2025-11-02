<?php $activePage = 'shop'; ?>
<?php
include("shared/assets/processes/session-process.php");
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webstar | Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="shared/assets/css/sidebar-and-container-styles.css">
    <link rel="stylesheet" href="shared/assets/css/shop.css">
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

                    <!-- Navbar for mobile -->
                    <?php include 'shared/components/navbar-for-mobile.php'; ?>

                    <div class="container-fluid py-3 overflow-y-auto">
                        <div class="row row-padding-top">
                            <div class="col-12">

                                <div class="d-flex align-items-center">
                                    <h1 class="text-bold text-30 mb-0 mt-2 shop-title" style="color: var(--black);">Shop
                                    </h1>
                                    <div class="tab-container">
                                        <div class="tab-scroll-container">
                                            <ul class="nav nav-tabs custom-nav-tabs" id="shopTabs">
                                                <li class="nav-item">
                                                    <a class="nav-link active" href="#" data-label="Frame">Frame</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" href="#" data-label="Color Theme">Color
                                                        Theme</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" href="#" data-label="Fonts">Fonts</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" href="#" data-label="Logo Badge">Logo Badge</a>
                                                </li>
                                            </ul>
                                        </div>
                                        <button class="tab-arrow left" onclick="scrollTabs('left')">
                                            <i class="fa-solid fa-chevron-left"></i>
                                        </button>
                                        <button class="tab-arrow right" onclick="scrollTabs('right')">
                                            <i class="fa-solid fa-chevron-right"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab Content Area -->
                        <div class="row" id="tabContentArea">
                            <!-- JavaScript will inject content here -->
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
                    <!-- Close Button -->
                    <button type="button" class="custom-close px-4 pt-4" data-bs-dismiss="modal"
                        aria-label="Close">✕</button>
                </div>

                <!-- Divider Line Under X -->
                <hr class="modal-divider mt-3 mb-3">
                <div class="modal-body p-0">
                    <div class="container mb-2 px-5">
                        <div class="d-flex flex-column align-items-center gap-4">
                            <!-- Left Box (Image) -->
                            <div class="modal-img"></div>

                            <!-- Right Details -->
                            <div class="text-center">
                                <div class="mb-1 modalTitle" id="modalTitle">Frame 1</div>
                                <div class="mb-1 description">Customize your profile or avatar with stylish borders
                                </div>
                                <div class="title d-flex justify-content-center align-items-center gap-2 mb-3">
                                    <i class="bi bi-star-fill"></i>
                                    <span id="modalPrice">1200 WBSTRS</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Divider Line Before Buy Button (Full Width) -->
                    <hr class="modal-divider mb-2">
                </div>
                <div class="modal-footer border-0 px-2 py-1">
                    <!-- Buy Button Container -->
                    <div class="d-flex justify-content-end pb-2 pe-2 w-100">
                        <button class="modalButton btn px-4 py-1 rounded-pill me-0">Buy</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
    </div>

    <!-- Bootstrap Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Shop Page Script -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const tabs = document.querySelectorAll('#shopTabs .nav-link');
            const contentArea = document.getElementById('tabContentArea');
            const modalElement = document.getElementById('cardModal');
            const bsModal = modalElement ? new bootstrap.Modal(modalElement) : null;

            function createShopCard(title, points) {
                return `
                    <div class="col-lg-3 col-md-4 col-sm-6 col-12 card-wrapper">
                        <div class="customCard">
                            <div class="cardItem">
                                <div class="shopCardImage"></div>
                                <div class="cardContent">
                                    <div class="cardTitle">${title}</div>
                                    <div class="cardPrice">
                                        <i class="bi bi-star-fill"></i>
                                        <span>${points} WBSTRS</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }

            const shopContent = {
                "Frame": [
                    createShopCard("Frame 1", 1200),
                    createShopCard("Frame 2", 1500),
                    createShopCard("Frame 3", 1800),
                    createShopCard("Frame 4", 2000),
                    createShopCard("Frame 5", 2500)
                ].join(''),
                "Color Theme": [
                    createShopCard("Dark Theme", 800),
                    createShopCard("Light Theme", 600),
                    createShopCard("Blue Theme", 900),
                    createShopCard("Green Theme", 750),
                    createShopCard("Purple Theme", 1000)
                ].join(''),
                "Fonts": [
                    createShopCard("Cursive Font", 500),
                    createShopCard("Bold Font", 400),
                    createShopCard("Italic Font", 450),
                    createShopCard("Script Font", 600),
                    createShopCard("Modern Font", 700)
                ].join(''),
                "Logo Badge": [
                    createShopCard("Gold Badge", 1500),
                    createShopCard("Silver Badge", 1200),
                    createShopCard("Bronze Badge", 900),
                    createShopCard("Diamond Badge", 2000),
                    createShopCard("Platinum Badge", 1800)
                ].join('')
            };

            contentArea.innerHTML = shopContent["Frame"];

            tabs.forEach(tab => {
                tab.addEventListener('click', function(e) {
                    e.preventDefault();
                    tabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');

                    const label = this.getAttribute('data-label');
                    contentArea.innerHTML = shopContent[label] || `<p>No items available.</p>`;
                });
            });

            // Open modal when a shop card is clicked
            contentArea.addEventListener('click', function(e) {
                const card = e.target.closest('.customCard');
                if (!card) return;

                const titleEl = card.querySelector('.cardTitle');
                const priceEl = card.querySelector('.cardPrice span');
                const titleText = titleEl ? titleEl.textContent.trim() : '';
                const priceText = priceEl ? priceEl.textContent.trim() : '';
                const priceNumber = priceText.split(' ')[0];

                const modalTitle = document.getElementById('modalTitle');
                const modalPrice = document.getElementById('modalPrice');
                if (modalTitle) modalTitle.textContent = titleText || 'Item';
                if (modalPrice) modalPrice.textContent = (priceNumber || '') + ' WBSTRS';

                if (bsModal) bsModal.show();
            });

            function scrollTabs(direction) {
                const scrollContainer = document.querySelector('.tab-scroll-container');
                const scrollAmount = 200;

                if (direction === 'left') {
                    scrollContainer.scrollBy({
                        left: -scrollAmount,
                        behavior: 'smooth'
                    });
                } else {
                    scrollContainer.scrollBy({
                        left: scrollAmount,
                        behavior: 'smooth'
                    });
                }
            }

            window.scrollTabs = scrollTabs;
        });
    </script>

</body>

</html>