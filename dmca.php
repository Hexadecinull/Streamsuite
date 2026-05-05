<?php
/*
 * StreamSuite — Free, open-source streaming website
 * Copyright (C) 2026  StreamSuite Contributors
 * (GPL-3.0 license)
 */

$pageTitle       = 'DMCA Policy';
$pageDescription = 'StreamSuite DMCA takedown policy.';
require_once __DIR__ . '/includes/head.php';
require_once __DIR__ . '/includes/nav.php';
?>

<main class="container legal-page">
    <div class="legal-content">
        <a href="javascript:history.back()" class="back-btn">&#8592; Back</a>
        <h1>DMCA Policy</h1>
        <p class="legal-meta">Last updated: <?= date('F j, Y') ?></p>

        <h2>Important Notice</h2>
        <p>StreamSuite is a free, open-source aggregator that indexes embed links from third-party video hosting services. <strong>StreamSuite does not host, store, upload, or transmit any video content.</strong> All video streams originate from independent third-party services outside our control.</p>
        <p>If you believe that content accessible via StreamSuite infringes your copyright, please direct your takedown notice to the hosting provider that is actually serving the content (e.g. vidsrc.cc, embed.su, vidlink.pro, etc.).</p>

        <h2>Filing a DMCA Takedown Notice</h2>
        <p>If, after contacting the source provider, you still wish to submit a notice to StreamSuite to have a title removed from our index, please include the following in your notice:</p>
        <ol>
            <li>Your full legal name, address, telephone number, and email address.</li>
            <li>A description of the copyrighted work you claim has been infringed.</li>
            <li>The specific URL(s) on StreamSuite that you are requesting be removed (e.g. <code>https://streamsuite.ct.ws/detail?id=XXXXX</code>).</li>
            <li>A statement that you have a good faith belief that the use of the material is not authorised by the copyright owner, its agent, or the law.</li>
            <li>A statement, made under penalty of perjury, that the information in your notice is accurate and that you are the copyright owner or authorised to act on behalf of the owner.</li>
            <li>Your physical or electronic signature.</li>
        </ol>
        <p>Submit your notice by opening an issue on <a href="https://github.com/Hexadecinull/Streamsuite/issues" target="_blank" rel="noopener noreferrer">GitHub</a> with the label <strong>DMCA</strong>. We will respond within 10 business days.</p>

        <h2>Counter-Notice</h2>
        <p>If you believe your content was removed as a result of a mistake or misidentification, you may submit a counter-notice. Your counter-notice must include:</p>
        <ol>
            <li>Your contact information (name, address, phone, email).</li>
            <li>Identification of the material and its location before removal.</li>
            <li>A statement under penalty of perjury that you have a good faith belief the material was removed by mistake.</li>
            <li>Consent to the jurisdiction of your local federal court.</li>
            <li>Your physical or electronic signature.</li>
        </ol>

        <h2>Repeat Infringers</h2>
        <p>StreamSuite will terminate user accounts that are repeatedly found to be associated with infringing activity, in appropriate circumstances.</p>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
<script src="/assets/js/api.js"></script>
<script src="/assets/js/ui.js"></script>
<script src="/assets/js/app.js"></script>
</body>
</html>
