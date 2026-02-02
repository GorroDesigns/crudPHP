<?php
if (session_status() === PHP_SESSION_NONE)
    session_start();
require_once("model/recibos.php");
require_once("model/facturas.php");

/**
 * Controlador de recibos.
 *
 * Se encarga de listar, insertar y generar PDF de los recibos de facturas.
 */
class Recibos
{
    /**
     * Muestra los recibos de una factura concreta.
     *
     * @return void
     */
    static function index(): void
    {
        $lineas = new RecibosModelo();
        $lineas->factura_id = $_GET['factura_id'];
        $lineas->Seleccionar();

        $factura = new FacturasModelo();
        $factura->id = $_GET['factura_id'];
        $factura->Seleccionar();

        require_once("view/recibos.php");
    }

    /**
     * Inserta un nuevo recibo.
     *
     * @return void
     */
    static function Insertar(): void
    {
        $linea = new RecibosModelo();
        $linea->factura_id = $_POST['factura_id'];
        $linea->fecha = $_POST['fecha'];
        $linea->importe = $_POST['importe'];

        if ($linea->Insertar() == 1) {
            header("location:" . URLSITE . '?c=recibos&factura_id=' . $linea->factura_id);
        } else {
            $_SESSION["CRUDMVC_ERROR"] = $linea->GetError();
            header("location:" . URLSITE . "view/error.php");
        }
    }

    /**
     * Genera el PDF con los recibos de una factura.
     *
     * @return void
     */
    static function Imprimir(): void
    {
        $factura_id = $_GET['factura_id'] ?? null;

        if (!$factura_id) {
            $_SESSION["CRUDMVC_ERROR"] = "Falta el parámetro factura_id";
            header("location:" . URLSITE . "view/error.php");
            exit;
        }

        $recibos = new RecibosModelo();
        $recibos->factura_id = $factura_id;
        $recibos->Seleccionar();
        $recibos->filas = $recibos->Seleccionar();

        require_once("fpdf/fpdf.php");
        require_once("pdfs/recibo.php");

        $pdf = new RecibosPDF();
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 10);

        $pdf->filas = $recibos->filas;

        $pdf->Imprimir();
        $pdf->Output();
        exit;
    }
}