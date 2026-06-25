<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/style.css">
    <title><?php echo SITENAME; ?> - Login</title>
    <style>
        :root {
            --login-gradient-start: #667eea;
            --login-gradient-end: #764ba2;
            --login-card-bg: #fff;
            --login-card-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            --login-footer-bg: #f8f9fa;
            --login-footer-color: #6c757d;
            --login-input-bg: #f8f9fa;
        }
        body {
            background: linear-gradient(135deg, var(--login-gradient-start) 0%, var(--login-gradient-end) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        }
        .login-page {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-card {
            background: var(--login-card-bg);
            border-radius: 15px;
            box-shadow: var(--login-card-shadow);
            overflow: hidden;
            width: 100%;
            max-width: 420px;
        }
        .login-header {
            background: linear-gradient(135deg, var(--login-gradient-start) 0%, var(--login-gradient-end) 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        .login-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }
        .login-header h3 {
            margin: 0;
            font-size: 26px;
            font-weight: 600;
        }
        .login-header p {
            margin: 8px 0 0;
            font-size: 14px;
            opacity: 0.9;
        }
        .login-body {
            padding: 40px 30px;
        }
        .login-footer {
            background: var(--login-footer-bg);
            padding: 20px 30px;
            text-align: center;
            font-size: 13px;
            color: var(--login-footer-color);
        }
        .input-group-text {
            background-color: var(--login-input-bg);
            border-right: none;
        }
        .input-group .form-control {
            border-left: none;
        }
        .input-group .form-control:focus {
            border-left: none;
            box-shadow: none;
        }
        .input-group .btn-outline-secondary {
            border-left: none;
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--login-gradient-start) 0%, var(--login-gradient-end) 100%);
            border: none;
            padding: 12px;
            font-weight: 500;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, color-mix(in srgb, var(--login-gradient-start), black 10%), color-mix(in srgb, var(--login-gradient-end), black 10%));
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
    </style>
</head>
<body>
    <div class="login-page">
        <div class="login-card">
