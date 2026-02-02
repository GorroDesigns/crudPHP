<?php

/**
 * Controlador de facturas.
 *
 * Se encarga del CRUD de facturas y de acciones como
 * exportar o imprimir recibos.
 */
class FacturasControlador
{
    /**
     * Muestra el listado de facturas.
     *
     * @return void
     */
    public static function index(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        require_once("model/facturas.php");

        $factura = new FacturasModelo();
        $factura->Seleccionar();

        require_once("view/facturas.php");
    }

    /**
     * Muestra el formulario para crear una factura.
     *
     * @return void
     */
    public static function Nuevo(): void
    {
        $clientes = new ClientesModelo();
        $clientes->Seleccionar();

        $opcion = 'NUEVO';
        require_once("view/facturasmantenimiento.php");
    }

    /**
     * Inserta una nueva factura.
     *
     * @return void
     */
    public static function Insertar(): void
    {
        $factura = new FacturasModelo();
        $factura->cliente_id = $_POST['cliente_id'];
        $factura->numero = $_POST['numero'];
        $factura->fecha = $_POST['fecha'];

        if ($factura->Insertar() == 1) {
            $cliente = new ClientesModelo();
            $cliente->id = $factura->cliente_id;
            $cliente->Seleccionar();

            $importe_total = $_POST['importe_total'];

            self::GenerarRecibos(
                $factura->id,
                $cliente->formapago,
                $importe_total,
                $factura->fecha
            );

            header("location:" . URLSITE . '?c=facturas');
        } else {
            $_SESSION["CRUDMVC_ERROR"] = $factura->GetError();
            header("location:" . URLSITE . "view/error.php");
        }
    }

    /**
     * Muestra el formulario para editar una factura.
     *
     * @return void
     */
    public static function Editar(): void
    {
        $factura = new FacturasModelo();
        $factura->id = $_GET['id'];
        $opcion = 'EDITAR';

        if ($factura->Seleccionar()) {
            $clientes = new ClientesModelo();
            $clientes->Seleccionar();

            require_once("view/facturasmantenimiento.php");
        } else {
            $_SESSION["CRUDMVC_ERROR"] = $factura->GetError();
            header("location:" . URLSITE . "view/error.php");
        }
    }

    /**
     * Modifica una factura existente.
     *
     * @return void
     */
    public static function Modificar(): void
    {
        $factura = new FacturasModelo();
        $factura->id = $_GET['id'];
        $factura->cliente_id = $_POST['cliente_id'];
        $factura->numero = $_POST['numero'];
        $factura->fecha = $_POST['fecha'];

        if (($factura->Modificar() == 1) || ($factura->GetError() == '')) {
            header("location:" . URLSITE . '?c=facturas');
        } else {
            $_SESSION["CRUDMVC_ERROR"] = $factura->GetError();
            header("location:" . URLSITE . "view/error.php");
        }
    }

    /**
     * Exporta las facturas a un archivo CSV.
     *
     * @return void
     */
    public static function Exportar(): void
    {
        $facturas = new FacturasModelo();
        $facturas->Seleccionar();

        try {
            $fichero = fopen("facturas.csv", "w");
            foreach ($facturas->filas as $fila) {
                $cadena = "$fila->id#$fila->numero#$fila->fecha\n";
                fputs($fichero, $cadena);
            }
        } finally {
            fclose($fichero);
        }

        $rutaFichero = 'facturas.csv';
        $fichero = basename($rutaFichero);

        header("Content-Type: application/octet-stream");
        header("Content-Length: " . filesize($rutaFichero));
        header("Content-Disposition: attachment; filename=$fichero");
        readfile($rutaFichero);
    }

    /**
     * Borra una factura.
     *
     * @return void
     */
    public static function Borrar(): void
    {
        $factura = new FacturasModelo();
        $factura->id = $_GET['id'];

        if ($factura->Borrar() == 1) {
            header("location:" . URLSITE . '?c=facturas');
        } else {
            $_SESSION["CRUDMVC_ERROR"] = $factura->GetError();
            header("location:" . URLSITE . "view/error.php");
        }
    }

    /**
     * Genera un PDF con la factura y sus líneas.
     *
     * @return void
     */
    public static function Imprimir(): void
    {
        $factura = new FacturasModelo();
        $factura->id = $_GET['id'];

        if (!$factura->Seleccionar()) {
            $_SESSION["CRUDMVC_ERROR"] = $factura->GetError();
            header("location:" . URLSITE . "view/error.php");
            exit;
        }

        $lineas = new LineasModelo();
        $lineas->factura_id = $factura->id;
        $lineas->Seleccionar();

        require_once("fpdf/fpdf.php");
        require_once("pdfs/recibo.php");

        $pdf = new Recibo();
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 10);

        $pdf->factura = $factura;
        $pdf->lineas = $lineas->filas ?? [];

        $pdf->Imprimir();
        $pdf->Output();
        exit;
    }
}