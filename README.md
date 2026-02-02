CRUD Tienda — Reto despliegue
Este proyecto es una aplicación web hecha en PHP donde he desarrollado un sistema CRUD usando el modelo MVC o Modelo–Vista–Controlador.

La aplicación simula la gestión de una tienda y permite trabajar con clientes, artículos, facturas, líneas de factura y recibos.

Es un desarrollo para clase de DAW.

Funcionalidades
Con esta aplicación se puede:

Crear, ver, modificar y borrar clientes
Gestionar artículos
Crear y modificar facturas
Añadir líneas de facturación
Registrar recibos
Exportar datos a CSV
Generar algunos documentos en PDF
Navegar entre secciones desde un menú principal
Estructura del proyecto
El proyecto está organizado en carpetas siguiendo el modelo MVC:

/controller → Controladores
/model → Modelos de la base de datos
/view → Vistas (HTML)
/pdfs → Archivos para generar PDFs
/docs → Documentación generada con phpDocumentor
El archivo principal es:

index.php → Se encarga de cargar los controladores según la sección que se quiera abrir

Tecnologías
PHP
MySQL
PDO
Bootstrap
phpDocumentor
Configuración
Para poder usar el proyecto hay que:

Crear una base de datos en MySQL
Crear las tablas necesarias (clientes, artículos, facturas, líneas y recibos)
Editar el archivo config.php con los datos de tu base de datos:
