<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            content: ["./app/views/**/*.php"],
            theme: {
                extend: {
                    colors: {
                        primary: '#2563eb',
                        'primary-hover': '#1d4ed8',
                        'primary-light': '#dbeafe',
                        success: '#059669',
                        'success-light': '#d1fae5',
                        danger: '#dc2626',
                        'danger-light': '#fee2e2',
                        warning: '#d97706',
                        'warning-light': '#fef3c7',
                        info: '#0891b2',
                        'info-light': '#cffafe',
                        dark: '#1e293b',
                        'dark-hover': '#0f172a',
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/style.css">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <title><?php echo SITENAME; ?></title>
</head>
<body>
    <?php require APPROOT . '/views/inc/topbar.php'; ?>
    <main class="main-content w-100" id="mainContent" role="main" style="width: 100%; margin-left: 0;">
        <div class="page-content">