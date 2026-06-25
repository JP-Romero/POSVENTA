Actúa como un Arquitecto de Software Senior, Desarrollador Full Stack Senior, Diseñador UX/UI y Analista de Sistemas con más de 15 años de experiencia.

Necesito desarrollar un Sistema de Ventas e Inventario para una librería/papelería utilizando:

- PHP 8+
- MySQL
- HTML5
- CSS3
- JavaScript
- Bootstrap 5
- XAMPP
- Arquitectura MVC
- PDO para consultas seguras
- AJAX para operaciones dinámicas

El sistema debe ser moderno, rápido, intuitivo, profesional, responsive y fácil de utilizar tanto para administradores como para cajeros.

# OBJETIVO

Crear un sistema web completo para la gestión de una librería que permita controlar ventas, inventario, compras, clientes, proveedores, facturación y reportes.

El sistema debe ser escalable, mantenible y estar preparado para futuras funcionalidades.

# REQUISITOS GENERALES

- Código limpio y comentado.
- Arquitectura MVC.
- Separación adecuada entre vistas, controladores y modelos.
- Uso de Bootstrap 5 para diseño moderno.
- Diseño responsive.
- Protección contra SQL Injection mediante PDO.
- Manejo de sesiones seguras.
- Validación tanto frontend como backend.
- Uso de AJAX para mejorar experiencia de usuario.
- Interfaz profesional similar a sistemas POS modernos.

# ROLES

## Administrador

Puede:

- Gestionar usuarios.
- Gestionar productos.
- Gestionar categorías.
- Gestionar proveedores.
- Gestionar clientes.
- Gestionar compras.
- Gestionar ventas.
- Ver reportes.
- Configurar sistema.

## Cajero

Puede:

- Realizar ventas.
- Consultar inventario.
- Consultar clientes.
- Imprimir facturas.

No puede:

- Eliminar información crítica.
- Acceder a configuración.

# MÓDULOS

## Login

Características:

- Inicio de sesión.
- Recuperación de contraseña.
- Control de sesiones.
- Roles y permisos.

Campos:

- Usuario
- Contraseña

--------------------------------------------------

## Dashboard

Mostrar:

- Ventas del día.
- Ventas del mes.
- Total de productos.
- Productos con bajo stock.
- Productos agotados.
- Últimas ventas.
- Gráficos estadísticos.

--------------------------------------------------

## Usuarios

Campos:

- Nombre
- Usuario
- Contraseña
- Rol
- Estado

Funciones:

- Crear
- Editar
- Desactivar
- Buscar

--------------------------------------------------

## Categorías

Campos:

- Nombre
- Descripción

Funciones:

- Crear
- Editar
- Eliminar
- Buscar

--------------------------------------------------

## Productos

Campos:

- Código interno
- Código de barras
- Nombre
- Descripción
- Categoría
- Proveedor
- Precio compra
- Precio venta
- Stock
- Stock mínimo
- Imagen
- Estado

Funciones:

- Crear
- Editar
- Eliminar
- Buscar
- Filtrar
- Importar Excel
- Exportar Excel

--------------------------------------------------

## Proveedores

Campos:

- Nombre
- Contacto
- Teléfono
- Correo
- Dirección

Funciones:

- Crear
- Editar
- Buscar
- Historial de compras

--------------------------------------------------

## Clientes

Campos:

- Nombre
- Teléfono
- Correo
- Dirección

Funciones:

- Crear
- Editar
- Buscar
- Historial de compras

--------------------------------------------------

## Compras

Funciones:

- Registrar compra.
- Seleccionar proveedor.
- Agregar productos.
- Actualizar inventario automáticamente.
- Generar comprobante.

Campos:

- Fecha
- Proveedor
- Productos
- Cantidad
- Precio compra
- Total

--------------------------------------------------

## Inventario

Funciones:

- Ver existencias.
- Entradas.
- Salidas.
- Kardex.
- Productos bajos en stock.
- Productos agotados.

--------------------------------------------------

## Punto de Venta (POS)

Debe ser el módulo principal.

Características:

- Escaneo de código de barras.
- Búsqueda rápida de productos.
- Carrito de compras.
- Cálculo automático.
- Descuentos.
- IVA configurable.
- Múltiples métodos de pago.
- Impresión de factura.

Métodos de pago:

- Efectivo
- Transferencia
- Tarjeta

--------------------------------------------------

## Facturación

Características:

- Numeración automática.
- Generación PDF.
- Reimpresión.
- Historial.

Formato:

FAC-000001

FAC-000002

FAC-000003

--------------------------------------------------

## Reportes

Ventas:

- Diario
- Semanal
- Mensual
- Anual

Inventario:

- Bajo stock
- Agotados
- Más vendidos
- Menos vendidos

Compras:

- Por proveedor
- Por fecha

Exportación:

- PDF
- Excel

--------------------------------------------------

## Configuración

Campos:

- Nombre del negocio
- RUC
- Dirección
- Teléfono
- Correo
- Logo

--------------------------------------------------

# BASE DE DATOS

Diseña una base de datos profesional normalizada incluyendo:

- usuarios
- roles
- categorias
- productos
- proveedores
- clientes
- compras
- detalle_compras
- ventas
- detalle_ventas
- movimientos_inventario
- configuracion

Genera:

1. Modelo entidad relación.
2. Script SQL completo.
3. Relaciones y llaves foráneas.

--------------------------------------------------

# INTERFAZ

Diseñar una interfaz moderna tipo POS profesional.

Características:

- Sidebar colapsable.
- Dashboard visual.
- Tablas con DataTables.
- Modales Bootstrap.
- Modo oscuro.
- Responsive.
- Colores corporativos elegantes.

--------------------------------------------------

# FORMA DE TRABAJO

NO generes todo de una sola vez.

Trabaja por fases.

FASE 1:
Arquitectura completa del proyecto.

FASE 2:
Base de datos SQL completa.

FASE 3:
Sistema de autenticación.

FASE 4:
CRUD de categorías.

FASE 5:
CRUD de productos.

FASE 6:
CRUD de proveedores.

FASE 7:
CRUD de clientes.

FASE 8:
Compras.

FASE 9:
Inventario.

FASE 10:
POS.

FASE 11:
Facturación PDF.

FASE 12:
Reportes.

FASE 13:
Configuración.

Para cada fase entrega:

- Estructura de carpetas.
- Código completo.
- Explicación.
- Buenas prácticas.
- Archivos involucrados.
- SQL necesario.
- Instrucciones de instalación en XAMPP.

No avances a la siguiente fase hasta finalizar completamente la actual.

# NOTAS DE ARQUITECTURA

## Layout

El sistema POSVENTA usa un layout **sin sidebar**, basado en **topbar + main-content full width**. 

- El archivo `sidebar.php` fue eliminado (previamente en `app/views/inc/`).
- El archivo `navbar.php` (navbar Bootstrap clásico) fue eliminado.
- La topbar (`topbar.php`) usa la clase `no-sidebar`.
- Todo el contenido se renderiza dentro de `<main id="mainContent">` sin márgenes laterales.
- No implementar un sidebar ni wrapper basado en sidebar a futuro sin actualizar toda la capa de estilos y scripts asociados.

Esta decisión mantiene la UI consistente con el diseño original, código más ligero y fácil de mantener, y evita riesgo de activar un sidebar no deseado.

## Modo Oscuro

El sistema POSVENTA **no soporta modo oscuro**. Se eliminó completamente:

- `public/css/dark-mode.css` - eliminado
- `public/js/dark-mode.js` - eliminado
- Botón de alternancia en `topbar.php` - eliminado
- Referencias a variables `--dark-*` en vistas PHP - reemplazadas por valores fijos
- Todos los bloques `html.dark-mode` en `style.css` - eliminados

El layout es **fijo en light mode** usando Bootstrap 5 y CSS personalizado con el sistema de elevación semántica (`--elev-*`). Cualquier implementación futura de modo oscuro debe partir desde cero, sin afectar la capa de estilos actual.
