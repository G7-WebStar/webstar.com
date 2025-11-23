<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Webstar | Where Education Meets Exploration</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="shared/assets/css/global-styles.css">
  <link rel="stylesheet" href="shared/assets/css/landing-page.css">
  <!-- <link rel="stylesheet" href="shared/assets/css/sidebar-and-container-styles.css"> -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:FILL@1" />
  <link rel="icon" type="image/png" href="shared/assets/img/webstar-icon.png">
</head>

<body>
  <div class="text-reg">
    <!-- Navbar -->
    <div class="navbar navbar-expand-lg">
      <?php include 'shared/components/landing-page-for-mobile.php'; ?>
      <?php include 'shared/components/landing-page-for-desktop.php'; ?>
      <?php include 'shared/components/landing-page-for-navbar.php'; ?>
    </div>


    <!-- Hero Section -->
    <!-- Desktop Hero (hidden on mobile) -->
    <section class="hero-section desktop-hero">
      <img src="shared/assets/img/folder1.png" style="width:450px" class="hero-img-left" alt="Folder Icon">
      <div class="intro"><span style="font-size:80px!important;">where</span><br>education<br><span style="font-size:80px!important;">meets</span><br>exploration</div>
    </section>
    <section class="hero-section desktop-hero">
      <img src="shared/assets/img/rocket.png" style="width:450px" class="hero-img-right" alt="Rocket Icon">
      <div class="text-18 mt-5 text-med" style="line-height:1.1">Turn every lesson into a quest and every <br> achievement into progress with Webstar!</div>
    </section>

    <!-- Mobile Hero (Only visible on mobile) -->
    <section class="mobile-hero">
      <h1><span>where</span><br>education<br><span>meets</span><br>exploration</h1>
      <p>Turn every lesson into a quest and every achievement into progress with Webstar!</p>
      <div class="hero-images">
        <img src="shared/assets/img/folder1.png" alt="Folder Icon" class="folder-img">
        <img src="shared/assets/img/rocket.png" alt="Rocket Icon" class="rocket-img">
      </div>
    </section>


    <!-- About Section for Desktop -->
    <section id="about" class="about-section d-none d-md-flex align-items-center justify-content-between px-5 py-5 mt-5">
      <!-- Left Side Image -->
      <div class="about-img-left">
        <img src="shared/assets/img/time.png" alt="Time Icon" class="img-fluid mt-5" style="max-width: 350px;">
      </div>

      <!-- Right Side Text -->
      <div class="about-text text-end mt-5">
        <div class="about-image mb-4 mt-5">
          <div class="text-sbold mt-5" style="transform: translateY(70%) translateX(-25%); font-size: 50px;">about</div>
          <img src="shared/assets/img/webstar-logo-black.png" class="img-fluid" alt="Webstar Logo"
            style="max-width: 700px;">
        </div>

        <div class="text-reg text-22">
          <div><strong>Webstar</strong> is an innovative <strong>Learning Management
              System</strong> designed to make
            education
            more engaging, interactive, and rewarding. It transforms traditional online learning into an immersive
            experience
            where students can complete quests, earn badges, and track their academic growth like stars rising in a
            galaxy
            of
            knowledge.</div>

          <div class="mt-3">For professors, <strong>Webstar</strong> offers powerful tools to create courses, manage
            class
            activities, and monitor student progress efficiently. The platform combines the structure of an LMS with the
            motivation of gamification—encouraging learners to stay active, curious, and driven.</div>

          <div class="mt-3">At <strong>Webstar</strong>, we believe that learning should be both productive and
            inspiring.
            Our
            mission is to help educators and students reach their full potential through creativity, collaboration, and
            technology.</div>
        </div>
      </div>
    </section>


    <!-- About Section for Mobile -->
    <section id="about" class="about-section-mobile d-block d-md-none text-center py-4 px-3 mt-5">
      <div class="text-sbold text-30">
        about
      </div>

      <div class="about-image mb-3">
        <img src="shared/assets/img/webstar-logo-black.png" class="img-fluid" alt="Time Icon" style="max-width: 250px;">
      </div>

      <div class="about-image mb-3">
        <img src="shared/assets/img/time.png" class="img-fluid" alt="Time Icon" style="max-width: 150px;">
      </div>

      <div class="text-reg text-18">
        <div><strong>Webstar</strong> is an innovative <strong>Learning Management System</strong> designed to make
          education
          more engaging, interactive, and rewarding. It transforms traditional online learning into an immersive
          experience
          where students can complete quests, earn badges, and track their academic growth like stars rising in a galaxy
          of
          knowledge.</div>

        <div class="mt-3">For professors, <strong>Webstar</strong> offers powerful tools to create courses, manage class
          activities, and monitor student progress efficiently. The platform combines the structure of an LMS with the
          motivation of gamification—encouraging learners to stay active, curious, and driven.</div>

        <div class="mt-3">At <strong>Webstar</strong>, we believe that learning should be both productive and inspiring.
          Our
          mission is to help educators and students reach their full potential through creativity, collaboration, and
          technology.</div>
      </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features-section">
      <h2>our <span style="font-style:italic;">best</span> features</h2>
      <p class="subtitle">Discover how Webstar turns learning into an engaging, rewarding experience through features
        designed to motivate and inspire every learner.</p>
      <div class="overflow-hidden">
        <div class="carousel-wrapper">
          <div class="d-flex">
            <div class="feature-card" style="border: 1px solid var(--black);">
              <img src="shared/assets/img/best-feature/roll-brush.png" alt="roll-brush" class="mt-2 mb-5">
              <h5>Customizable Profiles and Shop</h5>
              <p>Students can personalize their profiles and express their achievements through in-app shop items,
                making learning more fun and rewarding.</p>
            </div>
            <div class="feature-card" style="border: 1px solid var(--black);">
              <img src="shared/assets/img/best-feature/bulb.png" alt="Analytics" class="mt-2 mb-5">
              <h5>Analytics and Insights</h5>
              <p>Webstar provides analytics that help instructors track student progress and make informed learning
                decisions.</p>
            </div>
            <div class="feature-card" style="border: 1px solid var(--black);">
              <img src="shared/assets/img/best-feature/puzzle.png" alt="Analytics" class="mt-2 mb-4">
              <h5>Gamified Learning Experience</h5>
              <p>Turn lessons into quests! Students earn points, badges, and achievements as they progress.</p>
            </div>
            <div class="feature-card" style="border: 1px solid var(--black);">
              <img src="shared/assets/img/best-feature/notebook.png" alt="Professor Courses" class="mt-2 mb-5">
              <h5>Professor-Controlled Courses</h5>
              <p>Professors can easily manage lessons, assignments, and performance tracking all in one place.</p>
            </div>
            <div class="feature-card" style="border: 1px solid var(--black);">
              <img src="shared/assets/img/best-feature/trophy.png" alt="Leaderboard" class="mt-2 mb-5">
              <h5>Badges and Leaderboards</h5>
              <p>Students earn badges for milestones, while leaderboards inspire friendly competition.</p>
            </div>
            <div class="feature-card" style="border: 1px solid var(--black);">
              <img src="shared/assets/img/best-feature/brush.png" alt="Clean Interface" class="mt-2 mb-5">
              <h5>Clean & Modern User Interface</h5>
              <p>Built for simplicity, Webstar’s UI focuses on learning — not complicated navigation.</p>
            </div>
          </div>

          <!-- DUPLICATE for infinite loop -->
          <div class="d-flex">
            <div class="feature-card" style="border: 1px solid var(--black);">
              <img src="shared/assets/img/best-feature/roll-brush.png" alt="Profiles" class="mt-2 mb-5">
              <h5>Customizable Profiles and Shop</h5>
              <p>Students can personalize their profiles and express their achievements through in-app shop items,
                making learning more fun and rewarding.</p>
            </div>
            <div class="feature-card" style="border: 1px solid var(--black);">
              <img src="shared/assets/img/best-feature/bulb.png" alt="Analytics" class="mt-2 mb-5">
              <h5>Analytics and Insights</h5>
              <p>Webstar provides analytics that help instructors track student progress and make informed learning
                decisions.</p>
            </div>
            <div class="feature-card" style="border: 1px solid var(--black);">
              <img src="shared/assets/img/best-feature/puzzle.png" alt="Gamified Learning" class="mt-2 mb-4">
              <h5>Gamified Learning Experience</h5>
              <p>Turn lessons into quests! Students earn points, badges, and achievements as they progress.</p>
            </div>
            <div class="feature-card" style="border: 1px solid var(--black);">
              <img src="shared/assets/img/best-feature/notebook.png" alt="Professor Courses" class="mt-2 mb-5">
              <h5>Professor-Controlled Courses</h5>
              <p>Professors can easily manage lessons, assignments, and performance tracking all in one place.</p>
            </div>
            <div class="feature-card" style="border: 1px solid var(--black);">
              <img src="shared/assets/img/best-feature/trophy.png" alt="Leaderboard" class="mt-2 mb-5">
              <h5>Badges and Leaderboards</h5>
              <p>Students earn badges for milestones, while leaderboards inspire friendly competition.</p>
            </div>
            <div class="feature-card" style="border: 1px solid var(--black);">
              <img src="shared/assets/img/best-feature/-brush.png" alt="Clean Interface" class="mt-2 mb-5">
              <h5>Clean & Modern User Interface</h5>
              <p>Built for simplicity, Webstar’s UI focuses on learning — not complicated navigation.</p>
            </div>
          </div>
        </div>


      </div>
    </section>

    <!-- Team Section -->
    <section id="team" class="team-section">
      <h2>our <span style="font-style:italic;">best</span> team</h2>
      <p>Meet the passionate minds behind Webstar — information technology students united by one mission: to make
        learning engaging and rewarding for everyone.</p>

      <div class="container">
        <div class="row g-4 justify-content-center">

          <div class="col-md-6 col-lg-4">
            <div class="team-card" style="border: 1px solid var(--black);">
              <span class="role-badge">UI/UX Designer & Project Manager</span>
              <div class="team-overlay"></div>
              <img src="shared/assets/pfp-uploads/torillo.jpg" alt="Christian James D. Torrillo">
              <div class="team-info text-start text-18">
                <p class="text-bold">TORRILLO,</p>
                <p>Christian James D.</p>
              </div>
            </div>

          </div>

          <div class="col-md-6 col-lg-4">
            <div class="team-card" style="border: 1px solid var(--black);">
              <span class="role-badge">Full Stack Dev & Database Administrator</span>
              <div class="team-overlay"></div>
              <img src="shared/assets/pfp-uploads/silverio.jpg" alt="Shane Rhyder Silverio">
              <div class="team-info text-start text-18">
                <p class="text-bold">SILVERIO,</p>
                <p>Shane Rhyder</p>
              </div>
            </div>

          </div>

          <div class="col-md-6 col-lg-4">
            <div class="team-card" style="border: 1px solid var(--black);">
              <span class="role-badge">Full Stack Dev</span>
              <div class="team-overlay"></div>
              <img src="shared/assets/pfp-uploads/cato.jpg" alt="Marielle Alyssa L. Cato">
              <div class="team-info text-start text-18">
                <p class="text-bold">CATO,</p>
                <p>Marielle Alyssa L.</p>
              </div>
            </div>

          </div>

          <div class="col-md-6 col-lg-4">
            <div class="team-card" style="border: 1px solid var(--black);">
              <span class="role-badge">Full Stack Dev</span>
              <div class="team-overlay"></div>
              <img src="shared/assets/pfp-uploads/estoque.jpg" alt="Ayisha Sofhia D. Estoque">
              <div class="team-info text-start text-18">
                <p class="text-bold">ESTOQUE,</p>
                <p>Ayisha Sofhia D.</p>
              </div>
            </div>

          </div>

          <div class="col-md-6 col-lg-4">
            <div class="team-card" style="border: 1px solid var(--black);">
              <span class="role-badge">Full Stack Dev</span>
              <div class="team-overlay"></div>
              <img src="shared/assets/pfp-uploads/palla.jpg" alt="Kimberly Joan N. Palla">
              <div class="team-info text-start text-18">
                <p class="text-bold">PALLA,</p>
                <p>Kimberly Joan N.</p>
              </div>
            </div>

          </div>

          <div class="col-md-6 col-lg-4">
            <div class="team-card" style="border: 1px solid var(--black);">
              <span class="role-badge">Full Stack Dev</span>
              <div class="team-overlay"></div>
              <img src="shared/assets/pfp-uploads/vergara.jpg" alt="Neil Jeferson A. Vergara">
              <div class="team-info text-start text-18">
                <p class="text-bold">VERGARA,</p>
                <p>Neil Jeferson A.</p>
              </div>
            </div>

          </div>

        </div>
      </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="contact-section d-flex align-items-center justify-content-center py-5 px-5">
      <div class="container">
        <div class="row align-items-center justify-content-center flex-column-reverse flex-md-row">

          <!-- Left Side Image (will move below on mobile) -->
          <div class="col-md-5 d-flex justify-content-center mt-4 mt-md-0">
            <img src="shared/assets/img/phone.png" alt="Phone Icon" class="img-fluid" style="max-width: 280px;">
          </div>

          <!-- Right Side Text -->
          <div class="col-md-6 text-center text-md-start">
            <span class="text-bold fst-italic text-30">contact us!</span>
            <div class="d-flex align-items-center justify-content-center justify-content-md-start mt-3">
              <span class="material-symbols-outlined me-2">mail</span>
              <span>learn.webstar@gmail.com</span>
            </div>
            <form action="#">
              <button type="submit" class="btn contact-btn px-3 py-1 flex-fill rounded-pill text-reg text-md-14 mt-4">
                Send us an email
              </button>
            </form>
          </div>

        </div>
      </div>
    </section>



    <!-- Navbar -->
    <div class="navbar navbar-expand-lg desktop-only">
      <?php include 'shared/components/landing-page-for-desktop.php'; ?>
    </div>



    <!-- Footer Contact Section -->
    <section class="contact-info text-center py-5">
      <div class="container">
        <button class="btn button px-3 py-1 flex-fill rounded-pill text-reg text-md-14 mb-2"
          style="background-color: var(--primaryColor); color: var(--black); border: 1px solid var(--black);">
          Contact us:
        </button>

        <div class="mb-1">
          <strong>Email:</strong> learn.webstar@gmail.com
        </div>
        <div class="mb-0">
          <strong>Address:</strong> Polytechnic University of the Philippines – Sto. Tomas Campus<br>
          445V+GCG, A. Bonifacio St, Matandang Balará, Santo Tomas, 4234 Batangas
        </div>
      </div>
    </section>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  </div>
</body>

</html>