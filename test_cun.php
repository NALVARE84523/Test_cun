<?php

class PicasYFijas {
    private $secreto;

    public function __construct() {
        session_start();
        if (isset($_SESSION['secreto'])) {
            $this->secreto = $_SESSION['secreto'];
        } else {
            $this->generarSecreto();
        }
    }

    private function generarSecreto() {
        // Generar un número secreto de cuatro dígitos sin repeticiones
        $this->secreto = strval(rand(1000, 9999));
        while (count(array_unique(str_split($this->secreto))) < 4) {
            $this->secreto = strval(rand(1000, 9999));
        }
        $_SESSION['secreto'] = $this->secreto;
    }

    public function verificarIntento($numero) {
        // Verificar si el número ingresado tiene 4 dígitos
        if (!is_numeric($numero) || strlen($numero) != 4) {
            return "Por favor ingresa un número de cuatro dígitos.";
        }

        $picas = 0;
        $fijas = 0;
        $fijasNumbers = [];

        // Verificar picas y fijas
        for ($i = 0; $i < 4; $i++) {
            $digit = $numero[$i];
            if ($digit == $this->secreto[$i]) {
                $fijas++;
                $fijasNumbers[] = $digit;
            } elseif (strpos($this->secreto, $digit) !== false) {
                $picas++;
            }
        }

        // Verificar si el jugador adivinó el número secreto
        if ($fijas == 4) {
            session_destroy(); // Limpiar la sesión una vez que se ha adivinado el número
            return "¡Felicidades! Adivinaste el número secreto: $numero";
        }

        // Construir mensaje de picas y fijas
        $mensaje = "Número ingresado: $numero - Picas: $picas - Fijas: $fijas";
        if ($fijas > 0) {
            $mensaje .= " (Fijas: " . implode(", ", $fijasNumbers) . ")";
        }

        return $mensaje;
    }
}

// Ejemplo de uso
$juego = new PicasYFijas();
$resultado = "";

// Verificar si se ha enviado un número
if (isset($_POST['numero'])) {
    $numero = $_POST['numero'];
    $resultado = $juego->verificarIntento($numero);
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Juego de Picas y Fijas</title>
</head>
<body>
    <h1>¡Bienvenido al juego de Picas y Fijas!</h1>
    <p>Intenta adivinar el número secreto de cuatro dígitos.</p>
    <form method="post">
        <label for="numero">Ingresa tu intento:</label>
        <input type="text" id="numero" name="numero" maxlength="4">
        <button type="submit">Adivinar</button>
    </form>
    <?php if (!empty($resultado)) : ?>
        <p><?php echo $resultado; ?></p>
    <?php endif; ?>
</body>
</html>
