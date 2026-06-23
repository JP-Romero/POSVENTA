<?php
// instalar.php — Instalador web para POSVENTA
$msg = '';
$error = '';
$done = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $host = preg_replace('/[^a-zA-Z0-9._-]/', '', $_POST['host'] ?? 'localhost');
    $user = preg_replace('/[^a-zA-Z0-9_]/', '', $_POST['user'] ?? 'root');
    $pass = $_POST['pass'] ?? '';
    $port = intval($_POST['port'] ?? 3306);
    $dbname = preg_replace('/[^a-zA-Z0-9_]/', '', $_POST['dbname'] ?? 'libreria_db');
    $urlroot = trim($_POST['urlroot'] ?? 'http://localhost/POSVENTA');

    $conn = new mysqli($host, $user, $pass, '', $port);
    if ($conn->connect_error) {
        $error = 'No se pudo conectar a MariaDB: ' . $conn->connect_error;
    } else {
        // Leer y ejecutar libreria_db.sql
        $sqlFile = __DIR__ . '/sql/libreria_db.sql';
        if (!file_exists($sqlFile)) {
            $error = 'No se encontró el archivo sql/libreria_db.sql';
        } else {
            $sql = file_get_contents($sqlFile);
            $statements = array_filter(array_map('trim', explode(';', $sql)));
            $errores = [];
            foreach ($statements as $stmt) {
                if (!$stmt || str_starts_with($stmt, '--')) continue;
                if (!$conn->query($stmt)) {
                    $e = $conn->error;
                    if (strpos($e, 'already exists') === false && strpos($e, 'Duplicate') === false) {
                        $errores[] = $e;
                    }
                }
            }

            // Actualizar config.php con los datos reales
            $h = addslashes($host);
            $u = addslashes($user);
            $p = addslashes($pass);
            $d = addslashes($dbname);
            $url = addslashes($urlroot);

            $configPhp = "<?php\n" .
                "// Database params\n" .
                "define('DB_HOST', '$h');\n" .
                "define('DB_USER', '$u');\n" .
                "define('DB_PASS', '$p');\n" .
                "define('DB_NAME', '$d');\n\n" .
                "// App Root\n" .
                "define('APPROOT', dirname(dirname(__FILE__)));\n" .
                "// URL Root\n" .
                "define('URLROOT', '$url');\n" .
                "// Site Name\n" .
                "define('SITENAME', 'POSVENTA');\n" .
                "// App Version\n" .
                "define('APPVERSION', '1.0.0');\n";

            $configPath = __DIR__ . '/app/config/config.php';
            if (file_put_contents($configPath, $configPhp) === false) {
                $errores[] = 'No se pudo escribir en app/config/config.php (verifique permisos)';
            }

            $msg = empty($errores) ? 'Instalación completada correctamente.' : 'Instalado con advertencias menores (algunos datos ya existían).';
            $done = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Instalador — POSVENTA</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:sans-serif;background:#f0f4f8;display:flex;align-items:center;justify-content:center;min-height:100vh;padding:1rem}
.box{background:#fff;border-radius:12px;padding:2rem;width:460px;max-width:100%;box-shadow:0 4px 20px rgba(0,0,0,.1)}
h1{font-size:18px;margin-bottom:.25rem;color:#1a1a18}
p{font-size:13px;color:#666;margin-bottom:1.5rem}
label{display:block;font-size:11px;font-weight:700;color:#555;margin-bottom:4px;margin-top:12px;text-transform:uppercase;letter-spacing:.5px}
input{width:100%;height:38px;padding:0 10px;border:1.5px solid #ddd;border-radius:6px;font-size:14px}
button{margin-top:1.5rem;width:100%;height:42px;background:#2563eb;color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer}
button:hover{background:#1d4ed8}
.ok{background:#ecfdf5;color:#065f46;border:1px solid #a7f3d0;border-radius:6px;padding:12px;font-size:13px;margin-bottom:1rem}
.err{background:#fef2f2;color:#991b1b;border:1px solid #fecaca;border-radius:6px;padding:12px;font-size:13px;margin-bottom:1rem}
.link{display:block;text-align:center;margin-top:1rem;color:#2563eb;font-size:14px;font-weight:600;text-decoration:none}
.usr{background:#f5f5f4;border-radius:6px;padding:10px 12px;font-size:12.5px;margin-top:.5rem;line-height:1.8}
.warn{color:#991b1b;font-size:12px;margin-top:1rem;text-align:center}
</style>
</head>
<body>
<div class="box">
  <h1>🛒 Instalador POSVENTA</h1>
  <p>Sistema de Ventas e Inventario — Configuración inicial</p>

  <?php if ($error): ?>
    <div class="err"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <?php if ($done): ?>
    <div class="ok">✅ <?= htmlspecialchars($msg) ?></div>
    <div class="usr">
      <strong>Usuario por defecto:</strong><br/>
      👤 <strong>admin</strong> / admin123 — Administrador
    </div>
    <a href="<?= htmlspecialchars($urlroot ?? 'http://localhost/POSVENTA') ?>" class="link">→ Ir al sistema</a>
    <p class="warn">⚠️ Elimina el archivo <strong>instalar.php</strong> por seguridad.</p>
  <?php else: ?>
  <form method="POST">
    <label>Host de MariaDB/MySQL</label>
    <input name="host" value="localhost"/>
    <label>Puerto</label>
    <input name="port" value="3306" type="number"/>
    <label>Usuario de MariaDB/MySQL</label>
    <input name="user" value="root"/>
    <label>Contraseña de MariaDB/MySQL</label>
    <input name="pass" type="password" placeholder="Vacía por defecto en XAMPP"/>
    <label>Nombre de la base de datos</label>
    <input name="dbname" value="libreria_db"/>
    <label>URL del sistema (URLROOT)</label>
    <input name="urlroot" value="http://localhost/POSVENTA"/>
    <button type="submit">Instalar POSVENTA</button>
  </form>
  <?php endif; ?>
</div>
</body>
</html>