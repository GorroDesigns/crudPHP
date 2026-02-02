<?php

/**
 * Clase Crypt.
 *
 * Se usa para encriptar y desencriptar datos.
 */
class Crypt
{
    /**
     * Encripta una cadena de texto.
     *
     * @param string $string Texto a encriptar.
     * @return string Texto encriptado.
     */
    public static function Encriptar(string $string): string
    {
        $key = hash("sha256", SECRET_KEY);
        $iv  = substr(hash("sha256", SECRET_IV), 0, 16);

        $output = openssl_encrypt($string, ENCRYPT_METHOD, $key, 0, $iv);
        return base64_encode($output);
    }

    /**
     * Desencripta una cadena de texto.
     *
     * @param string $string Texto encriptado.
     * @return string Texto desencriptado.
     */
    public static function Desencriptar(string $string): string
    {
        $key = hash("sha256", SECRET_KEY);
        $iv  = substr(hash("sha256", SECRET_IV), 0, 16);

        $output = base64_decode($string);
        return openssl_decrypt($output, ENCRYPT_METHOD, $key, 0, $iv);
    }
}