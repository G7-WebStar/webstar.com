<?php $activePage = 'shop'; ?>


<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webstar | Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="shared/assets/css/global-styles.css">
    <link rel="stylesheet" href="shared/assets/css/sidebar-and-container-styles.css">
    <link rel="stylesheet" href="shared/assets/css/shop.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>

<body>
    <div class="container-fluid min-vh-100 d-flex justify-content-center align-items-center p-3">
        <div class="row w-100">


            <!-- Sidebar (only shows on mobile) -->
            <?php include 'shared/components/sidebar-for-mobile.php'; ?>

            <!-- Sidebar Column (fixed on desktop) -->
            <?php include 'shared/components/sidebar-for-desktop.php'; ?>

            <!-- Main Container Column-->
            <div class="col main-container m-0 p-0 mx-2 p-4 overflow-y-auto">
                <div class="card border-0 p-3 h-100 w-100 rounded-0 shadow-none">

                    <!-- Navbar for mobile -->
                    <?php include 'shared/components/navbar-for-mobile.php'; ?>

                    <!-- PUT CONTENT HERE -->
                    <!-- Main scrollable content area -->
                    <div class="container-fluid py-3 overflow-y-auto">
                        <div class="row align-items-center g-0">
                            <!-- Shop icon and name (desktop only) -->
                            <div class="col-auto d-none d-md-block">
                                <img src="shared/assets/img/shop/shop.svg" alt="Shop" class="shopIcon">
                            </div>
                            <div class="col-auto px-3 d-none d-md-block">
                                <div class="shopName">Shop</div>
                            </div>

                            <!-- Filter buttons (desktop only) -->
                            <div class="col-auto mt-2">
                                <div class="customCard p-1 d-none d-md-flex">
                                    <div class="buttonGroup">
                                        <button type="button" class="plainButton" data-label="Frame">Frame</button>
                                        <button type="button" class="plainButton" data-label="Color Theme">Color
                                            Theme</button>
                                        <button type="button" class="plainButton" data-label="Fonts">Fonts</button>
                                        <button type="button" class="plainButton" data-label="Logo Badge">Logo
                                            Badge</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Dropdown (mobile only) -->
                            <div class="col-12 mt-2 d-flex justify-content-center d-md-none mt-3">
                                <div class="dropdown fixed-dropdown w-100 px-3">
                                    <button class="btn btn-outline-primary dropdown-toggle w-100" type="button"
                                        id="shopDropdownButton" data-bs-toggle="dropdown" aria-expanded="false">
                                        Frame
                                    </button>
                                    <ul class="dropdown-menu w-100" aria-labelledby="shopDropdownButton">
                                        <li><button class="dropdown-item" type="button"
                                                data-value="Frame">Frame</button></li>
                                        <li><button class="dropdown-item" type="button" data-value="Color Theme">Color
                                                Theme</button></li>
                                        <li><button class="dropdown-item" type="button"
                                                data-value="Fonts">Fonts</button></li>
                                        <li><button class="dropdown-item" type="button" data-value="Logo Badge">Logo
                                                Badge</button></li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Shop cards display area -->
                        <div class="row g-3 px-4 mt-3" id="cardGrid">
                            <!-- JavaScript dynamically injects card items here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="container">
        <div class="modal fade" id="cardModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content custom-modal position-relative rounded-4 overflow-hidden">

                    <!-- Close Button -->
                    <div class="d-flex justify-content-end px-4 pt-4">
                        <button type="button" class="custom-close" data-bs-dismiss="modal" aria-label="Close">✕</button>
                    </div>

                    <!-- Divider Line Under X -->
                    <hr class="modal-divider mb-3">

                    <!-- Modal Body -->
                    <div class="container mb-2 px-5">
                        <div class="d-flex flex-column flex-md-row align-items-center gap-4">
                            <!-- Left Box (Image) -->
                            <div class="modal-img"></div>

                            <!-- Right Details -->
                            <div class="text-center text-md-start">
                                <div class="mb-1 modalTitle" id="modalTitle">
                                    Frame 1
                                </div>
                                <div class="mb-1 description">Customize your profile or avatar with stylish borders

                                </div>
                                <div
                                    class="title d-flex justify-content-center justify-content-md-start align-items-center gap-2 mb-3">
                                    <img src="shared/assets/img/shop/coins.svg" alt="Coin" style="width: 20px;">
                                    <span id="modalPrice">250</span>
                                </div>
                                <button class="modalButton btn px-4 py-1 text-white rounded-pill">BUY</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript to handle filter buttons and card generation -->
    <script>
        const cardGrid = document.getElementById('cardGrid');

        const fontNames = [
            "Helvetica", "Arial", "Montserrat", "Roboto",
            "Serif", "Gotham", "Poppins Bold", "Courier New"
        ];

        const fontMap = {
            "Helvetica": "Helvetica, sans-serif",
            "Arial": "Arial, sans-serif",
            "Montserrat": "'Montserrat', sans-serif",
            "Roboto": "'Roboto', sans-serif",
            "Serif": "serif",
            "Gotham": "'Gotham', sans-serif",
            "Poppins Bold": "'Poppins', sans-serif",
            "Courier New": "'Courier New', monospace"
        };

        const colorNames = [
            "Rose Red", "Orange", "Green", "Gray",
            "Brown", "Cyan", "Amber", "Purple"
        ];

        const badgeNames = [
            "Code Cadet", "HTML Hero", "CSS Styler", "Webstar Scholar",
            "Syntax Star", "Frontend Explorer", "Tag Master", "Pixel Pro"
        ];

        function getCardTitle(label, i) {
            if (label === 'Fonts') return fontNames[i - 1];
            if (label === 'Color Theme') return colorNames[i - 1];
            if (label === 'Logo Badge') return badgeNames[i - 1];
            return `${label} ${i}`;
        }

        function generateCards(label) {
            cardGrid.innerHTML = '';

            for (let i = 1; i <= 8; i++) {
                const col = document.createElement('div');
                col.className = 'col-md-3';

                const card = document.createElement('div');
                card.className = 'cardItem h-100 p-3 text-center rounded-4';

                const img = document.createElement('img');
                img.src = `shared/assets/img/shop/${label.toLowerCase().replace(/\s/g, '-')}-${i}.png`;
                img.alt = `${label} ${i}`;
                img.className = 'shopCardImage mb-1 rounded-4';
                img.style.maxHeight = '125px';

                const imgWrapper = document.createElement('div');
                imgWrapper.className = 'overflow-hidden rounded-4 mb-2';
                imgWrapper.appendChild(img);
                card.appendChild(imgWrapper);

                const title = document.createElement('div');
                const name = getCardTitle(label, i);
                title.textContent = name;
                title.className = 'fw-bold mb-1 title';

                if (label === 'Fonts') {
                    title.style.fontFamily = fontMap[name] || 'sans-serif';
                }

                const priceRow = document.createElement('div');
                priceRow.className = 'd-flex justify-content-center align-items-center gap-1';

                const coinIcon = document.createElement('img');
                coinIcon.src = 'shared/assets/img/shop/coins.svg';
                coinIcon.alt = 'Coin';
                coinIcon.style.width = '20px';

                const price = document.createElement('span');
                price.textContent = '250';
                price.className = 'title';

                priceRow.append(coinIcon, price);

                card.append(title, priceRow);
                col.appendChild(card);
                cardGrid.appendChild(col);

                card.addEventListener('click', () => {
                    document.getElementById('modalTitle').textContent = name;
                    document.getElementById('modalPrice').textContent = price.textContent;

                    const modalImg = document.querySelector('.modal-img');
                    modalImg.innerHTML = '';
                    const modalImage = document.createElement('img');
                    modalImage.src = img.src;
                    modalImage.alt = img.alt;
                    modalImage.className = 'img-fluid rounded-4';
                    modalImage.style.maxHeight = '200px';
                    modalImg.appendChild(modalImage);

                    new bootstrap.Modal(document.getElementById('cardModal')).show();
                });
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            // Modal accessibility
            const modal = document.getElementById('cardModal');
            modal?.addEventListener('hidden.bs.modal', () => document.body.focus());
            document.querySelector('#cardModal .custom-close')?.addEventListener('click', function () {
                this.blur();
            });

            // Initial load
            generateCards('Frame');
            setActiveButton('Frame');

            // Desktop buttons
            document.querySelectorAll('.plainButton').forEach(btn => {
                btn.addEventListener('click', function () {
                    const label = this.getAttribute('data-label') || this.textContent.trim();
                    generateCards(label);
                    setActiveButton(label);
                });
            });

            // Mobile dropdown
            const dropdownButton = document.getElementById('shopDropdownButton');
            document.querySelectorAll('.dropdown-menu .dropdown-item').forEach(item => {
                item.addEventListener('click', function () {
                    const value = this.getAttribute('data-value');
                    dropdownButton.textContent = value;
                    generateCards(value);
                    setActiveButton(value);
                });
            });

        });

        function setActiveButton(label) {
            document.querySelectorAll('.plainButton').forEach(btn => {
                const btnLabel = btn.getAttribute('data-label') || btn.textContent.trim();
                btn.classList.toggle('active', btnLabel === label);
            });
        }

        const buttons = document.querySelectorAll('.plainButton');

        buttons.forEach(button => {
            button.addEventListener('click', () => {
                buttons.forEach(btn => btn.classList.remove('active')); // Remove from all
                button.classList.add('active'); // Add to clicked
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>