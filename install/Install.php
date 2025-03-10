<?php

namespace Install;

class Install
{
    public static function crearEstructuraDirectorios()
    {
        // Define la estructura de directorios que quieres crear
        $directorios = [
            'config',
            'logs'
        ];

        // Define los archivos que quieres copiar desde las plantillas
        $archivos = [
            'config/config.php' => 'config.php',
            'config/config-db.php' => 'config-db.php',
            'config/verification_email.json' => '../src/config/verification_email.json',
            'config/forgot_password_email.json' => '../src/config/forgot_password_email.json',
            'logs/log_file.log' =>  '../src/logs/log_file.log',
        ];

        // Crear directorios
        foreach ($directorios as $directorio) {
            $ruta = dirname(__DIR__) . '/' . $directorio;

            if (!is_dir($ruta)) {
                mkdir($ruta, 0755, true);
                echo "Directorio creado: <strong>$ruta</strong><br>";
            } else {
                echo "El directorio ya existe: $ruta\n";
            }
        }

        // Copiar archivos desde las plantillas
        foreach ($archivos as $destino => $origen) {
            $rutaDestino = dirname(__DIR__) . '/' . $destino;

            if (!file_exists($rutaDestino)) {
                copy($origen, $rutaDestino);
                echo "Archivo copiado: <strong>$rutaDestino</strong><br>";
            } else {
                echo "El archivo ya existe: $rutaDestino\n";
            }
        }

        // Eliminar el directorio actual y su contenido
        $rutaActual = __DIR__;
        self::eliminarDirectorio($rutaActual);
    }

    /**
     * Elimina recursivamente un directorio y su contenido.
     *
     * @param string $ruta La ruta del directorio a eliminar.
     */
    private static function eliminarDirectorio($ruta)
    {
        if (!is_dir($ruta)) {
            echo "El directorio no existe: $ruta\n";
            return;
        }

        $archivos = array_diff(scandir($ruta), ['.', '..']);

        foreach ($archivos as $archivo) {
            $rutaArchivo = $ruta . DIRECTORY_SEPARATOR . $archivo;

            if (is_dir($rutaArchivo)) {
                self::eliminarDirectorio($rutaArchivo); // Eliminar subdirectorios recursivamente
            } else {
                unlink($rutaArchivo); // Eliminar archivo
                echo "Archivo eliminado: $rutaArchivo\n";
            }
        }

        rmdir($ruta); // Eliminar el directorio en sí
        echo "Directorio eliminado: $ruta\n";
    }
}

//Install::crearEstructuraDirectorios();
