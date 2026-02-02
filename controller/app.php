<?php

/**
 * Controlador principal de la aplicación.
 *
 * Se encarga de cargar la vista inicial del sistema y de
 * inicializar la sesión si aún no ha sido iniciada.
 */
class AppControlador
{
    /**
     * Muestra la página principal de la aplicación.
     *
     * Este metodo comprueba SI existe una sesión activa y,
     * si no existe, la inicia antes de cargar la vista.
     *
     * @return void
     */
    public static function index(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        require_once("view/app.php");
    }
}