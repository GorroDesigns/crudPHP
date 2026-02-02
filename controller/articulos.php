<?php

/**
 * Controlador de artículos.
 *
 * Maneja las acciones del CRUD de artículos y carga
 * las vistas correspondientes.
 */
class ArticulosControlador
{
    /**
     * Muestra el listado de artículos.
     *
     * @return void
     */
    public static function index(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        require_once("model/articulos.php");

        $articulos = new ArticulosModelo();
        $articulos->Seleccionar();

        require_once("view/articulos.php");
    }

    /**
     * Muestra el formulario para crear un artículo nuevo.
     *
     * @return void
     */
    public static function Nuevo(): void
    {
        $opcion = 'NUEVO';
        require_once("view/articulosmantenimiento.php");
    }

    /**
     * Inserta un nuevo artículo.
     *
     * @return void
     */
    public static function Insertar(): void
    {
        $articulo = new ArticulosModelo();
        $articulo->referencia  = $_POST['referencia'];
        $articulo->descripcion = $_POST['descripcion'];
        $articulo->precio      = $_POST['precio'];
        $articulo->iva         = $_POST['iva'];

        if ($articulo->Insertar() == 1) {
            header("location:" . URLSITE . '?c=articulos');
        } else {
            $_SESSION["CRUDMVC_ERROR"] = $articulo->GetError();
            header("location:" . URLSITE . "view/error.php");
        }
    }

    /**
     * Muestra el formulario para editar un artículo.
     *
     * @return void
     */
    public static function Editar(): void
    {
        $articulo = new ArticulosModelo();
        $articulo->id = $_GET['id'];
        $opcion = 'EDITAR';

        if ($articulo->Seleccionar()) {
            require_once("view/articulosmantenimiento.php");
        } else {
            $_SESSION["CRUDMVC_ERROR"] = $articulo->GetError();
            header("location:" . URLSITE . "view/error.php");
        }
    }

    /**
     * Actualiza un artículo existente.
     *
     * @return void
     */
    public static function Modificar(): void
    {
        $articulo = new ArticulosModelo();
        $articulo->id          = $_GET['id'];
        $articulo->referencia  = $_POST['referencia'];
        $articulo->descripcion = $_POST['descripcion'];
        $articulo->precio      = $_POST['precio'];
        $articulo->iva         = $_POST['iva'];

        if (($articulo->Modificar() == 1) || ($articulo->GetError() == '')) {
            header("location:" . URLSITE . '?c=articulos');
        } else {
            $_SESSION["CRUDMVC_ERROR"] = $articulo->GetError();
            header("location:" . URLSITE . "view/error.php");
        }
    }

    /**
     * Borra un artículo.
     *
     * @return void
     */
    public static function Borrar(): void
    {
        $articulo = new ArticulosModelo();
        $articulo->id = $_GET['id'];

        if ($articulo->Borrar() == 1) {
            header("location:" . URLSITE . '?c=articulos');
        } else {
            $_SESSION["CRUDMVC_ERROR"] = $articulo->GetError();
            header("location:" . URLSITE . "view/error.php");
        }
    }
}