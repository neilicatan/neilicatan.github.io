<?php require_once(realpath('../vendor') . DIRECTORY_SEPARATOR . 'autoload.php'); ?>
<?php

use app\src\UserProfile;

$userDetails = new UserProfile();
?>
<?php
// Check if the page was accessed by an authenticated user
if (!isset($_SESSION['loggedUser'])) {
    header("Location: ../");
}

if (!isset($_SESSION['id'])) {
    header("Location: ../");
}

if (!isset($_SESSION['user'])) {
    header("Location: ../");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle : "EasyBoard | Admin Dashboard" ?></title>
    <link rel="icon" href="../assets/img/logo-light.png">

    <!-- Preload stylesheets and JavaScript files -->
    <link rel="preload" href="./assets/css/tailwind-output.css" as="style"> 
    <link rel="preload" href="<?= realpath('../assets/css/style.css') ?>" as="style">
    <link rel="preload" href="../assets/fonts/fonts.min.css" as="style">
    <link rel="preload" href="../assets/icons/uicons-brands/css/uicons-brands.min.css" as="style">
    <link rel="preload" href="../assets/icons/uicons-regular-rounded/css/uicons-regular-rounded.min.css" as="style">
    <link rel="preload" href="../assets/js/main..min.js" as="script">
    <link rel="preload" href="../assets/js/chart.min.js" as="script">

    <!-- Important stylesheets -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
    <link rel="stylesheet" href="../assets/css/tailwind-output.css?v=1.0.2">
    <link rel="stylesheet" href="../assets/css/style.css?v=1.0.2">
    <link rel="stylesheet" href="../assets/fonts/fonts.min.css">
    <link rel="stylesheet" href="../assets/icons/uicons-brands/css/uicons-brands.min.css">
    <link rel="stylesheet" href="../assets/icons/uicons-regular-rounded/css/uicons-regular-rounded.min.css">
</head>

<body>

    <div class="lg:grid lg:grid-cols-12">
        <header class="lg:flex-col lg:drop-shadow-none lg:col-span-3 lg:border-r lg:border-r-slate-200 lg:dark:border-r-slate-700 lg:p-0">
            <a class="lg:hidden lg:not-sr-only" href="/admin">
            </a>

            <nav class="scale-0 lg:scale-100 lg:w-full lg:sticky lg:top-0 lg:bottom-full lg:min-h-screen lg:space-y-2 lg:pt-2.5 z-50">
                <a class="hidden not-sr-only lg:block" href="/admin" aria-label="HousingQuest logo">
                </a>

                <ul class="flex flex-col gap-1">
                    
                    <li>
                        <a class="py-3 px-4 w-full hover:bg-admin-nav hover:border-l-4 hover:border-slate-900 hover:dark:border-slate-700 hover:font-bold ?> block border-l-4 border-transparent" href="/admin">
                            <i class="fr fi-rr-apps pr-1.5"></i>
                            Overview
                        </a>
                    </li>
                    <li>
                        <a class="py-3 px-4 w-full hover:bg-admin-nav hover:border-l-4 hover:border-slate-900 hover:dark:border-slate-700 hover:font-bold ?> block border-l-4 border-transparent" href="/admin/properties">
                            <i class="fr fi-rr-home pr-1.5"></i>
                            Properties
                        </a>
                    </li>
                    <li>
                        <a class="py-3 px-4 w-full hover:bg-admin-nav hover:border-l-4 hover:border-slate-900 hover:dark:border-slate-700 hover:font-bold ?> block border-l-4 border-transparent" href="/admin/payment-history">
                            <i class="fr fi-rr-document-signed pr-1.5"></i>
                            Payment History
                        </a>
                    </li>
                    <li>
                        <a class="py-3 px-4 w-full hover:bg-admin-nav hover:border-l-4 hover:border-slate-900 hover:dark:border-slate-700 hover:font-bold ?> block border-l-4 border-transparent" href="/admin/tenants">
                            <i class="fr fi-rr-users pr-1.5"></i>
                            Tenants
                        </a>
                    </li>
                    <li class="hidden">
                        <a class="py-3 px-4 w-full hover:bg-admin-nav hover:border-l-4 hover:border-slate-900 hover:dark:border-slate-700 hover:font-bold ?> block border-l-4 border-transparent" href="/admin/messages">
                            <i class="fr fi-rr-envelope pr-1.5"></i>
                            Messages
                        </a>
                    </li>
                    <li>
                        <a class="py-3 px-4 w-full hover:bg-admin-nav hover:border-l-4 hover:border-slate-900 hover:dark:border-slate-700 hover:font-bold ?> block border-l-4 border-transparent" href="/admin/settings">
                            <i class="fr fi-rr-settings pr-1.5"></i>
                            Settings
                        </a>
                    </li>
                    <li>
                        <a class="py-3 px-4 w-full hover:bg-admin-nav hover:border-l-4 hover:border-slate-900 hover:dark:border-slate-700 hover:font-bold ?> block border-l-4 border-transparent" href="/admin/logout">
                            <i class="fr fi-rr-exit pr-1.5"></i>
                            Logout
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="flex items-center gap-4 lg:hidden">
                <a class="text-xl relative before:content-['1'] before:absolute before:text-xs before:bg-rose-600 before:text-white before:rounded-full before:py-[0.1rem] before:px-1.5 before:z-50 before:-top-[30%] before:-right-[50%] hidden" href="/admin/messages" aria-label="Messages" title="View messages">
                    <i class="fr fi-rr-envelope"></i>
                </a>

                <button class="mode-toggle text-xl" type="button" aria-label="Theme toggle button">
                    <i class="fr fi-rr-moon"></i>
                </button>

                <button class="menu-toggle text-xl lg:hidden" type="button" aria-label="Mobile menu toggle button">
                    <i class="fr fi-rr-apps"></i>
                </button>
            </div>
        </header>

        <main class="lg:col-span-9 bg-slate-100 dark:bg-slate-800 dark:text-slate-100">
            <div class="flex items-center justify-between gap-x-8 gap-y-8 flex-wrap p-4 lg:px-[2.5%] lg:py-2.5 lg:sticky lg:top-0 bg-white dark:bg-slate-900 dark:text-slate-100 border-b border-slate-200 dark:border-slate-700 z-[1024]">
                <div>
                    <h4 class="header text-lg">
                        Welcome back, <?= $_SESSION['user'] ?>!
                    </h4>
                    <p class="dark:text-slate-300">
                        Here is an overview of your properties
                    </p>
                </div>

                <div class="flex items-center flex-wrap gap-x-6 gap-y-2">
                    <button class="mode-toggle hidden not-sr-only lg:block text-xl" type="button" aria-label="Theme toggle button">
                        <i class="fr fi-rr-moon"></i>
                    </button>

                    <a class="hidden text-xl relative before:content-['1'] before:absolute before:text-xs before:bg-rose-600 before:text-white before:rounded-full before:py-[0.1rem] before:px-1.5 before:z-50 before:-top-[30%] before:-right-[50%]" href="/admin/messages" aria-label="Messages" title="View messages">
                        <i class="fr fi-rr-envelope"></i>
                    </a>

                    <div class="flex flex-wrap items-center gap-x-2 gap-y-2">
                        <img class="rounded-full w-10 h-10" src="../<?= (!file_exists('../admin/assets/$userDetails->getUserDetails()->fetch_object()->profile_pic')) ? 'admin/' : '' ?>assets/img/<?= $userDetails->getUserDetails()->fetch_object()->profile_pic ?>" alt="<?= $userDetails->getUserDetails()->fetch_object()->name; ?>" width="40" height="40" />
                        <span class="-space-y-1">
                            <h4 class="header">
                                <?= $_SESSION['user'] ?>
                            </h4>
                            <p class="text-green-500 tracking-wider dark:text-green-400">
                                Online
                            </p>
                        </span>
                    </div>
                </div>
            </div>

            <div class="px-4 space-y-12 lg:px-[2.5%] py-8 relative">