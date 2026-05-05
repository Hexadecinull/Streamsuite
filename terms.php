<?php
/*
 * StreamSuite — Free, open-source streaming website
 * Copyright (C) 2026  StreamSuite Contributors
 * (GPL-3.0 license)
 */

$pageTitle       = 'Terms of Service';
$pageDescription = 'StreamSuite Terms of Service.';
require_once __DIR__ . '/includes/head.php';
require_once __DIR__ . '/includes/nav.php';
?>

<main class="container legal-page">
    <div class="legal-content">
        <a href="javascript:history.back()" class="back-btn">&#8592; Back</a>
        <h1>Terms of Service</h1>
        <p class="legal-meta">Last updated: <?= date('F j, Y') ?> &mdash; Effective immediately</p>

        <h2>1. Acceptance of Terms</h2>
        <p>By accessing or using StreamSuite ("the Service"), you agree to be bound by these Terms of Service. If you do not agree, please do not use the Service.</p>

        <h2>2. Description of Service</h2>
        <p>StreamSuite is a free, open-source web application that aggregates publicly available third-party video embed links. StreamSuite does not host, store, upload, or distribute any video content. All streams are provided by independent third-party embed services entirely outside StreamSuite's control.</p>

        <h2>3. Use of the Service</h2>
        <p>You may use the Service only for lawful purposes and in accordance with these Terms. You agree not to:</p>
        <ul>
            <li>Use the Service in any way that violates applicable local, national, or international law or regulation.</li>
            <li>Attempt to gain unauthorised access to any part of the Service or its related systems.</li>
            <li>Transmit any unsolicited or unauthorised advertising or promotional material.</li>
            <li>Reverse engineer, decompile, or disassemble any part of the Service.</li>
            <li>Use automated tools to scrape or overload the Service.</li>
        </ul>

        <h2>4. Intellectual Property</h2>
        <p>StreamSuite is released under the <a href="https://www.gnu.org/licenses/gpl-3.0.html" target="_blank" rel="noopener noreferrer">GNU General Public License v3.0</a>. The source code is available at <a href="https://github.com/Hexadecinull/Streamsuite" target="_blank" rel="noopener noreferrer">github.com/Hexadecinull/Streamsuite</a>. Movie and TV metadata is provided by <a href="https://www.themoviedb.org" target="_blank" rel="noopener noreferrer">TMDB</a> under their terms.</p>
        <p>StreamSuite does not claim ownership of any video content accessible through the Service. All content remains the property of its respective owners.</p>

        <h2>5. Disclaimer of Warranties</h2>
        <p>The Service is provided "as is" and "as available" without warranties of any kind, express or implied. StreamSuite makes no warranty that the Service will be uninterrupted, error-free, or free of harmful components.</p>

        <h2>6. Limitation of Liability</h2>
        <p>To the fullest extent permitted by law, StreamSuite and its contributors shall not be liable for any indirect, incidental, special, consequential, or punitive damages arising from your use of the Service.</p>

        <h2>7. Third-Party Services</h2>
        <p>The Service embeds content from third-party providers. These providers have their own terms of service and privacy policies, and StreamSuite has no control over their practices. Use of those services is at your own risk.</p>

        <h2>8. Modifications</h2>
        <p>We reserve the right to modify these Terms at any time. Continued use of the Service after changes constitutes acceptance of the new Terms.</p>

        <h2>9. Governing Law</h2>
        <p>These Terms shall be governed by the laws of the jurisdiction in which the primary contributor resides, without regard to conflict of law principles.</p>

        <h2>10. Contact</h2>
        <p>For questions about these Terms, please open an issue on <a href="https://github.com/Hexadecinull/Streamsuite" target="_blank" rel="noopener noreferrer">GitHub</a>.</p>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
<script src="/assets/js/api.js"></script>
<script src="/assets/js/ui.js"></script>
<script src="/assets/js/app.js"></script>
</body>
</html>
