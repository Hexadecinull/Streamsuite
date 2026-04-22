<?php
/*
 * StreamSuite — Free, open-source streaming website
 * Copyright (C) 2026  StreamSuite Contributors
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

if (!isset($pageTitle)) $pageTitle = 'StreamSuite';
if (!isset($pageDescription)) $pageDescription = 'Stream everything. Own nothing. Pay nothing.';
?>
<!DOCTYPE html>
<html lang="en" data-theme="obsidian">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> — StreamSuite</title>
    <meta name="description" content="<?= htmlspecialchars($pageDescription) ?>">
    <link rel="icon" type="image/svg+xml" href="/assets/img/logo-mark.svg">

    <!-- CSS -->
    <link rel="stylesheet" href="/assets/css/reset.css">
    <link rel="stylesheet" href="/assets/css/tokens.css">
    <link rel="stylesheet" href="/assets/css/themes.css">
    <link rel="stylesheet" href="/assets/css/layout.css">
    <link rel="stylesheet" href="/assets/css/components.css">
    <link rel="stylesheet" href="/assets/css/nav.css">
    <?php if (isset($extraCss)): foreach ((array)$extraCss as $css): ?>
        <link rel="stylesheet" href="<?= htmlspecialchars($css) ?>">
    <?php endforeach; endif; ?>

    <!-- Preload fonts -->
    <link rel="preload" href="/assets/fonts/Satoshi-Variable.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="/assets/fonts/DMMono-Regular.woff2" as="font" type="font/woff2" crossorigin>
</head>
<body>