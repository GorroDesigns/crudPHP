<?php
if (session_status() === PHP_SESSION_NONE)
    session_start();
require_once("controller/facturas.php");
require_once("controller/app.php");
require_once("controller/clientes.php");
require_once("controller/lineas_factura.php");

require_once("config.php");
require_once("controller/articulos.php");
require_once("controller/recibo.php");

$controlador = '';

if (isset($_GET['c'])) :
    $controlador = $_GET['c'];
    $metodo = '';
    if (isset($_GET['m']))
        $metodo = $_GET['m'];

    switch ($controlador) :
        case 'clientes' :
            if (method_exists('ClientesControlador', $metodo)) :
                ClientesControlador::{$metodo}();
            else :
                ClientesControlador::index();
            endif;
            break;

            /**
             * Cambio de Juan 1
             */

        case 'facturas' :
            if (method_exists('FacturasControlador', $metodo)) :
                FacturasControlador::{$metodo}();
            else :
                FacturasControlador::index();
            endif;
            break;

        case 'lineas_factura' :
            if (method_exists('LineasControlador', $metodo)) :
                LineasControlador::{$metodo}();
            else :
                LineasControlador::index();
            endif;
            break;

        case 'articulos' :
            if (method_exists('ArticulosControlador', $metodo)) :
                ArticulosControlador::{$metodo}();
            else :
                ArticulosControlador::index();
            endif;
            break;

        case 'recibos' :
            if (method_exists('Recibos', $metodo)) :
                Recibos::{$metodo}();
            else :
                Recibos::index();
            endif;
            break;

        default:
            AppControlador::index();
    endswitch;
else :
    AppControlador::index();
endif;
?>
