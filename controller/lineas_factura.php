<?php
if (session_status() === PHP_SESSION_NONE)
    session_start();
require_once("model/lineas_factura.php");
require_once("model/facturas.php");

/**
 * Controlador de líneas de factura.
 *
 * Se encarga del CRUD de las líneas y también de generar recibos según la forma de pago.
 */
class LineasControlador
{
    /**
     * Muestra las líneas de una factura concreta.
     *
     * @return void
     */
    static function index(): void
    {
        $lineas = new LineasModelo();
        $lineas->factura_id = $_GET['factura_id'];
        $lineas->Seleccionar();

        $factura = new FacturasModelo();
        $factura->id = $_GET['factura_id'];
        $factura->Seleccionar();

        require_once("view/lineas_factura.php");
    }

    /**
     * Muestra el formulario para añadir una línea nueva.
     *
     * @return void
     */
    static function Nuevo(): void
    {
        $articulos = new ArticulosModelo();
        $articulos->Seleccionar();

        $facturas = new FacturasModelo();
        $facturas->Seleccionar();

        $opcion = 'NUEVO';
        require_once("view/lineasfacturasmantenimiento.php");
    }

    /**
     * Inserta una nueva línea en la factura.
     *
     * @return void
     */
    static function Insertar(): void
    {
        $linea = new LineasModelo();
        $linea->factura_id = $_POST['factura_id'];
        $linea->referencia = $_POST['referencia'];
        $linea->descripcion = $_POST['descripcion'];
        $linea->cantidad = $_POST['cantidad'];
        $linea->precio = $_POST['precio'];
        $linea->iva = $_POST['iva'];

        if ($linea->Insertar() == 1) {
            header("location:" . URLSITE . '?c=lineas_factura');
        } else {
            $_SESSION["CRUDMVC_ERROR"] = $linea->GetError();
            header("location:" . URLSITE . "view/error.php");
        }
    }

    /**
     * Muestra el formulario para editar una línea existente.
     *
     * @return void
     */
    static function Editar(): void
    {
        $linea = new LineasModelo();
        $linea->id = $_GET['id'];
        $opcion = 'EDITAR';

        if ($linea->Seleccionar()) {
            $articulos = new ArticulosModelo();
            $articulos->Seleccionar();
            $facturas = new FacturasModelo();
            $facturas->Seleccionar();
            require_once("view/lineasfacturasmantenimiento.php");
        } else {
            $_SESSION["CRUDMVC_ERROR"] = $linea->GetError();
            header("location:" . URLSITE . "view/error.php");
        }
    }

    /**
     * Guarda los cambios de una línea existente.
     *
     * @return void
     */
    static function Modificar(): void
    {
        $linea = new LineasModelo();
        $linea->id = $_GET['id'];
        $linea->factura_id = $_POST['factura_id'];
        $linea->referencia = $_POST['referencia'];
        $linea->descripcion = $_POST['descripcion'];
        $linea->cantidad = $_POST['cantidad'];
        $linea->precio = $_POST['precio'];
        $linea->iva = $_POST['iva'];

        if (($linea->Modificar() == 1) || ($linea->GetError() == '')) {
            header("location:" . URLSITE . '?c=lineas_factura');
        } else {
            $_SESSION["CRUDMVC_ERROR"] = $linea->GetError();
            header("location:" . URLSITE . "view/error.php");
        }
    }

    /**
     * Borra una línea de factura.
     *
     * @return void
     */
    static function Borrar(): void
    {
        $linea = new LineasModelo();
        $linea->id = $_GET['id'];
        if ($linea->Borrar() == 1) {
            header("location:" . URLSITE . '?c=lineas_factura');
        } else {
            $_SESSION["CRUDMVC_ERROR"] = $linea->GetError();
            header("location:" . URLSITE . "view/error.php");
        }
    }

    /**
     * Genera los recibos de una factura según la forma de pago del cliente.
     *
     * @param int $factura_id ID de la factura.
     * @return void
     */
    static function recibos(int $factura_id): void
    {
        $factura = new FacturasModelo();
        $factura->id = $factura_id;

        if (!$factura->Seleccionar()) {
            $_SESSION["CRUDMVC_ERROR"] = "Factura no encontrada";
            header("location:" . URLSITE . "view/error.php");
            exit;
        }

        $cliente = new ClientesModelo();
        $cliente->id = $factura->cliente_id;
        $cliente->Seleccionar();

        $formapago = $cliente->formapago;
        $importe_total = $factura->importe;
        $fecha_factura = $factura->fecha;

        $recibo = new RecibosModelo();

        switch ($formapago) {
            case 1: // Contado
                $recibo->factura_id = $factura_id;
                $recibo->fecha = $fecha_factura;
                $recibo->importe = $importe_total;
                $recibo->Insertar();
                break;

            case 2: // A 30 días
                $fecha = date("Y-m-d", strtotime($fecha_factura . " +30 days"));
                $recibo->factura_id = $factura_id;
                $recibo->fecha = $fecha;
                $recibo->importe = $importe_total;
                $recibo->Insertar();
                break;

            case 3: // A 30 y 60 días
                $importe_parcial = $importe_total / 2;

                $fecha1 = date("Y-m-d", strtotime($fecha_factura . " +30 days"));
                $recibo->factura_id = $factura_id;
                $recibo->fecha = $fecha1;
                $recibo->importe = $importe_parcial;
                $recibo->Insertar();

                $fecha2 = date("Y-m-d", strtotime($fecha_factura . " +60 days"));
                $recibo->factura_id = $factura_id;
                $recibo->fecha = $fecha2;
                $recibo->importe = $importe_parcial;
                $recibo->Insertar();
                break;
        }

        header("location:" . URLSITE . "?c=recibos&factura_id=" . $factura_id);
        exit;
    }
}