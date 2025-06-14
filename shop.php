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
</head>

<body>
    <div class="container-fluid min-vh-100 d-flex justify-content-center align-items-center p-3">
        <div class="row w-100">


            <!-- Sidebar (only shows on mobile) -->
            <?php include 'shared/components/sidebar-for-mobile.php'; ?>

            <!-- Sidebar Column (fixed on desktop) -->
            <?php include 'shared/components/sidebar-for-desktop.php'; ?>

            <!-- Main Container Column-->
            <div class="col main-container m-0 p-0 mx-2 p-4 ">
                <div class="card border-0 p-3 h-100 w-100 rounded-0 shadow-none">

                    <!-- Navbar for mobile -->
                    <?php include 'shared/components/navbar-for-mobile.php'; ?>

                    <!-- PUT CONTENT HERE -->
                    <!-- Main scrollable content area -->
                    <div class="scrollable-content" id="scrollableContent">
                        <div class="container-fluid">
                            <div class="row align-items-center g-0">
                                <!-- Shop icon and name (desktop only) -->
                                <div class="col-auto d-none d-md-block">
                                    <img src="shared/assets/img/shop/shop.svg" alt="Shop" class="shopIcon">
                                </div>
                                <div class="col-auto px-3 d-none d-md-block">
                                    <div class="shopName">Shop</div>
                                </div>

                                <!-- Dropdown for filter buttons (mobile only) -->
                                <div class="col-auto mt-2">
                                    <!-- Horizontal filter buttons (desktop only) -->
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
                                <!-- Dropdown for small devices only -->
                                <div class="col-12 mt-2 d-flex justify-content-center d-md-none mt-3">
                                    <div class="dropdown fixed-dropdown">
                                        <button class="btn btn-outline-primary dropdown-toggle w-100" type="button"
                                            id="shopDropdownButton" data-bs-toggle="dropdown" aria-expanded="false">
                                            Frame
                                        </button>
                                        <ul class="dropdown-menu w-100" aria-labelledby="shopDropdownButton">
                                            <li><button class="dropdown-item" type="button"
                                                    data-value="Frame">Frame</button></li>
                                            <li><button class="dropdown-item" type="button"
                                                    data-value="Color Theme">Color Theme</button></li>
                                            <li><button class="dropdown-item" type="button"
                                                    data-value="Fonts">Fonts</button></li>
                                            <li><button class="dropdown-item" type="button" data-value="Logo Badge">Logo
                                                    Badge</button></li>
                                        </ul>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <!-- Area where shop cards are shown -->
                        <div class="container mt-4" id="cardDisplay">
                            <div class="row g-3 px-4" id="cardGrid">
                                <!-- JavaScript dynamically injects card items here -->
                            </div>
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
                    <div class="container mb-2 mt-2 px-4">
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

        // Dynamically generate card items based on selected filter
        function generateCards(label) {
            cardGrid.innerHTML = '';

            for (let i = 1; i <= 8; i++) {
                const col = document.createElement('div');
                col.className = 'col-md-3';

                const card = document.createElement('div');
                card.className = 'cardItem h-100 p-3 text-center rounded-4';

                const img = document.createElement('img');
                img.src = 'shared/assets/img/shop/empty.svg';
                img.alt = `${label} ${i}`;
                img.className = 'img-fluid mb-1';
                img.style.maxHeight = '125px';

                const title = document.createElement('div');
                title.textContent = `${label} ${i}`;
                title.className = 'fw-bold mb-1 title';

                const priceRow = document.createElement('div');
                priceRow.className = 'd-flex justify-content-center align-items-center gap-1';

                const coinIcon = document.createElement('img');
                coinIcon.src = 'shared/assets/img/shop/coins.svg';
                coinIcon.alt = 'Coin';
                coinIcon.style.width = '20px';

                const price = document.createElement('span');
                price.textContent = '250';
                price.className = 'title';

                priceRow.appendChild(coinIcon);
                priceRow.appendChild(price);

                card.appendChild(img);
                card.appendChild(title);
                card.appendChild(priceRow);

                card.addEventListener('click', () => {
                    document.getElementById('modalTitle').textContent = title.textContent;
                    document.getElementById('modalPrice').textContent = price.textContent;
                    const modal = new bootstrap.Modal(document.getElementById('cardModal'));
                    modal.show();
                });

                col.appendChild(card);
                cardGrid.appendChild(col);
            }
        }

        // Fix for "aria-hidden on focused element" warning
        document.addEventListener('DOMContentLoaded', function () {
            const closeBtn = document.querySelector('#cardModal .custom-close');
            const cardModal = document.getElementById('cardModal');

            closeBtn?.addEventListener('click', function () {
                this.blur();
            });

            cardModal.addEventListener('hidden.bs.modal', function () {
                document.body.focus();
            });
        });

        // Setup event listeners on page load
        window.addEventListener('DOMContentLoaded', () => {
            generateCards('Frame');

            // Set initial active for "Frame"
            document.querySelectorAll('.plainButton').forEach(btn => {
                if ((btn.getAttribute('data-label') || btn.textContent.trim()) === 'Frame') {
                    btn.classList.add('active');
                }
            });

            // Setup desktop button click behavior
            document.querySelectorAll('.plainButton').forEach(btn => {
                btn.addEventListener('click', function () {
                    const value = this.getAttribute('data-label') || this.textContent.trim();
                    generateCards(value);

                    // Update active button
                    document.querySelectorAll('.plainButton').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                });
            });

            // Setup mobile dropdown behavior
            const dropdownButton = document.getElementById('shopDropdownButton');
            const dropdownItems = document.querySelectorAll('.dropdown-menu .dropdown-item');

            dropdownItems.forEach(item => {
                item.addEventListener('click', function () {
                    const value = this.getAttribute('data-value');
                    dropdownButton.textContent = value;
                    generateCards(value);

                    // Update active buttons
                    document.querySelectorAll('.plainButton').forEach(b => {
                        if ((b.getAttribute('data-label') || b.textContent.trim()) === value) {
                            b.classList.add('active');
                        } else {
                            b.classList.remove('active');
                        }
                    });
                });
            });
        });

        // Adjust scroll behavior for mobile
        function handleScrollStyle() {
            const scrollable = document.getElementById("scrollableContent");
            if (window.innerWidth < 768) {
                scrollable.style.maxHeight = "calc(100vh - 100px)";
                scrollable.style.overflowY = "auto";
                scrollable.style.paddingRight = "10px";
            } else {
                scrollable.style.maxHeight = "none";
                scrollable.style.overflowY = "visible";
                scrollable.style.paddingRight = "0";
            }
        }

        window.addEventListener("resize", handleScrollStyle);
        window.addEventListener("load", handleScrollStyle);
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>