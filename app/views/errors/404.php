<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Página no encontrada</title>
    <style>
        :root {
            --error-bg: #f5f5f5;
            --error-text: #666;
            --error-primary: #e74c3c;
            --error-link: #3498db;
        }
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: var(--error-bg);
        }
        .error-container {
            text-align: center;
            padding: 40px;
        }
        h1 {
            font-size: 72px;
            color: var(--error-primary);
            margin: 0;
        }
        p {
            font-size: 24px;
            color: var(--error-text);
        }
        a {
            color: var(--error-link);
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <h1>404</h1>
        <p>Página no encontrada</p>
        <p><a href="<?= URLROOT ?>">Volver al inicio</a></p>
    </div>
</body>
</html>
