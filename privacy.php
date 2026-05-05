<?php
/*
 * StreamSuite — Free, open-source streaming website
 * Copyright (C) 2026  StreamSuite Contributors
 * (GPL-3.0 license)
 */

$pageTitle       = 'Privacy Policy';
$pageDescription = 'StreamSuite Privacy Policy.';
require_once __DIR__ . '/includes/head.php';
require_once __DIR__ . '/includes/nav.php';
?>

<main class="container legal-page">
    <div class="legal-content">
        <a href="javascript:history.back()" class="back-btn">&#8592; Back</a>
        <h1>Privacy Policy</h1>
        <p class="legal-meta">Last updated: <?= date('F j, Y') ?></p>

        <h2>1. Information We Collect</h2>
        <p><strong>Unauthenticated users:</strong> We assign a randomly generated guest token (stored in your browser's local storage) to associate your watch history and preferences with your session. No personal information is required.</p>
        <p><strong>Registered users:</strong> If you create an account, we collect your display name, email address, and a hashed (bcrypt) password. We never store passwords in plain text.</p>
        <p><strong>Automatically collected:</strong> Standard server logs may include your IP address, browser user agent, and pages visited. These are used solely for security and abuse prevention and are not sold or shared.</p>

        <h2>2. How We Use Your Information</h2>
        <ul>
            <li>To provide and maintain the Service (watch history, continue watching, favorites).</li>
            <li>To authenticate registered users via secure session tokens.</li>
            <li>To improve the Service based on aggregate, anonymised usage patterns.</li>
        </ul>
        <p>We do not sell, rent, or share your personal information with third parties for marketing purposes.</p>

        <h2>3. Cookies and Local Storage</h2>
        <p>StreamSuite uses browser local storage (not cookies) to store your preferences (theme, font, settings) and guest token. No third-party tracking cookies are set by StreamSuite itself. Third-party embed providers may set their own cookies; we have no control over this.</p>

        <h2>4. Third-Party Services</h2>
        <p>Movie and TV metadata is fetched from <a href="https://www.themoviedb.org" target="_blank" rel="noopener noreferrer">TMDB</a>. Video content is embedded from third-party providers (e.g. vidsrc.cc). These providers have their own privacy policies and may collect data independently.</p>

        <h2>5. Data Retention</h2>
        <p>Watch history and favorites are retained until you clear them or delete your account. Server logs are retained for up to 30 days. Registered accounts can be deleted on request.</p>

        <h2>6. Security</h2>
        <p>We use industry-standard measures to protect your data, including HTTPS, hashed passwords, and parameterised database queries. No system is 100% secure; use the Service at your own risk.</p>

        <h2>7. Children's Privacy</h2>
        <p>StreamSuite is not directed at children under 13. We do not knowingly collect personal information from children under 13.</p>

        <h2>8. Your Rights</h2>
        <p>You may request access to, correction of, or deletion of your personal data by opening an issue on <a href="https://github.com/Hexadecinull/Streamsuite" target="_blank" rel="noopener noreferrer">GitHub</a> or contacting us directly.</p>

        <h2>9. Changes to This Policy</h2>
        <p>We may update this Privacy Policy from time to time. We will note the date of last update at the top of this page. Continued use of the Service after changes constitutes acceptance.</p>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
<script src="/assets/js/api.js"></script>
<script src="/assets/js/ui.js"></script>
<script src="/assets/js/app.js"></script>
</body>
</html>
