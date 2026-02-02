<?php

/**
 * Controlador de clientes.
 *
 * Gestiona el CRUD de clientes y algunas acciones extra
 * como exportar o imprimir.
 */
class ClientesControlador
{
    /**
     * Muestra el listado de clientes.
     *
     * @return void
     */
    public static function index(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        require_once("model/clientes.php");

        $clientes = new ClientesModelo();
        $clientes->Seleccionar();

        require_once("view/clientes.php");
    }

    /**
     * Muestra el formulario para crear un cliente.
     *
     * @return void
     */
    public static function Nuevo(): void
    {
        $opcion = 'NUEVO';
        require_once("view/clientesmantenimiento.php");
    }

    /**
     * Inserta un nuevo cliente.
     *
     * @return void
     */
    public static function Insertar(): void
    {
        $cliente = new ClientesModelo();
        $cliente->nombre = $_POST['nombre'];
        $cliente->apellidos = $_POST['apellidos'];
        $cliente->fechanacimiento = $_POST['fechanacimiento'];
        $cliente->email = $_POST['email'];
        $cliente->contrasenya = Crypt::Encriptar($_POST['contrasenya']);
        $cliente->direccion = $_POST['direccion'];
        $cliente->cp = $_POST['cp'];
        $cliente->poblacion = $_POST['poblacion'];
        $cliente->provincia = $_POST['provincia'];
        $cliente->formapago = $_POST['formapago'];

        if ($cliente->Insertar() == 1) {
            header("location:" . URLSITE . '?c=clientes');
        } else {
            $_SESSION["CRUDMVC_ERROR"] = $cliente->GetError();
            header("location:" . URLSITE . "view/error.php");
        }
    }

    /**
     * Muestra el formulario para editar un cliente.
     *
     * @return void
     */
    public static function Editar(): void
    {
        $cliente = new ClientesModelo();
        $cliente->id = $_GET['id'];
        $opcion = 'EDITAR';

        if ($cliente->Seleccionar()) {
            require_once("view/clientesmantenimiento.php");
        } else {
            $_SESSION["CRUDMVC_ERROR"] = $cliente->GetError();
            header("location:" . URLSITE . "view/error.php");
        }
    }

    /**
     * Actualiza un cliente existente.
     *
     * @return void
     */
    public static function Modificar(): void
    {
        $cliente = new ClientesModelo();
        $cliente->id = $_GET['id'];
        $cliente->nombre = $_POST['nombre'];
        $cliente->apellidos = $_POST['apellidos'];
        $cliente->fechanacimiento = $_POST['fechanacimiento'];
        $cliente->email = $_POST['email'];
        $cliente->contrasenya = $_POST['contrasenya'];
        $cliente->direccion = $_POST['direccion'];
        $cliente->cp = $_POST['cp'];
        $cliente->poblacion = $_POST['poblacion'];
        $cliente->provincia = $_POST['provincia'];
        $cliente->formapago = $_POST['formapago'];

        if (($cliente->Modificar() == 1) || ($cliente->GetError() == '')) {
            header("location:" . URLSITE . '?c=clientes');
        } else {
            $_SESSION["CRUDMVC_ERROR"] = $cliente->GetError();
            header("location:" . URLSITE . "view/error.php");
        }
    }

    /**
     * Exporta los clientes a un archivo CSV.
     *
     * @return void
     */
    public static function Exportar(): void
    {
        $clientes = new ClientesModelo();
        $clientes->Seleccionar();

        try {
            $fichero = fopen("clientes.csv", "w");
            foreach ($clientes->filas as $fila) {
                $cadena = "$fila->id#$fila->nombre#$fila->apellidos\n";
                fputs($fichero, $cadena);
            }
        } finally {
            fclose($fichero);
        }

        $rutaFichero = 'clientes.csv';
        $fichero = basename($rutaFichero);

        header("Content-Type: application/octet-stream");
        header("Content-Length: " . filesize($rutaFichero));
        header("Content-Disposition: attachment; filename=$fichero");
        readfile($rutaFichero);
    }

    /**
     * Genera un PDF con el listado de clientes.
     *
     * @return void
     */
    public static function Imprimir(): void
    {
        $clientes = new ClientesModelo();
        $clientes->Seleccionar();

        $pdf = new ClientesPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetWidths(array(20, 20, 21, 44, 40, 15, 15, 15));
        $pdf->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));

        $pdf->filas = $clientes->filas;
        $pdf->Imprimir();
        $pdf->Output();
    }

    /**
     * Borra un cliente.
     *
     * @return void
     */
    public static function Borrar(): void
    {
        $cliente = new ClientesModelo();
        $cliente->id = $_GET['id'];

        if ($cliente->Borrar() == 1) {
            header("location:" . URLSITE . '?c=clientes');
        } else {
            $_SESSION["CRUDMVC_ERROR"] = $cliente->GetError();
            header("location:" . URLSITE . "view/error.php");
        }
    }
}