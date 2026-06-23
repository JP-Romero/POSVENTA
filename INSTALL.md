# Instrucciones de Instalación (XAMPP)

Para ejecutar este sistema en un entorno local con XAMPP, siga estos pasos:

## 1. Preparación de carpetas
- Clone o descargue este repositorio.
- Copie la carpeta del proyecto en el directorio `htdocs` de su instalación de XAMPP (por lo general `C:\xampp\htdocs\libreria`).

## 2. Configuración de Base de Datos
- Inicie los módulos **Apache** y **MySQL** desde el Panel de Control de XAMPP.
- Acceda a `phpMyAdmin` (http://localhost/phpmyadmin).
- Importe el archivo `sql/create_db.sql` o cree manualmente una base de datos llamada `libreria_db`.

## 3. Configuración del Proyecto
- Abra el archivo `app/config/config.php`.
- Verifique que las credenciales de la base de datos sean correctas:
  ```php
  define('DB_HOST', 'localhost');
  define('DB_USER', 'root');
  define('DB_PASS', '');
  define('DB_NAME', 'libreria_db');
  ```
- Asegúrese de que `URLROOT` coincida con la ruta de su proyecto:
  ```php
  define('URLROOT', 'http://localhost/libreria');
  ```

## 4. Acceso al sistema
- Abra su navegador y acceda a: `http://localhost/libreria`

---
**Nota:** El sistema utiliza `.htaccess` para URLs amigables. Asegúrese de que el módulo `mod_rewrite` esté habilitado en su configuración de Apache (habilitado por defecto en XAMPP).
