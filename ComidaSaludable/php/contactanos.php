<?php
session_start();

$mensaje = '';
$errores = [];

// Mostrar el mensaje almacenado en la sesión si existe
if (isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
    $tipo = $_SESSION['tipo'];
    unset($_SESSION['mensaje']);
    unset($_SESSION['tipo']);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $campos = [
        'nombre' => 'Debe ingresar el nombre',
        'apellido' => 'Debe ingresar el apellido',
        'telefono' => 'Debe ingresar el teléfono',
        'correo' => 'El correo electrónico es obligatorio',
        'mensaje' => 'Debe ingresar un mensaje'
    ];

    foreach ($campos as $campo => $mensajeError) {
        $valor = trim(filter_input(INPUT_POST, $campo, FILTER_SANITIZE_STRING));
        if (empty($valor)) {
            $errores[$campo] = $mensajeError;
        } else {
            ${$campo} = $valor;
        }
    }

    $correo = filter_input(INPUT_POST, 'correo', FILTER_SANITIZE_EMAIL);
    if (empty($correo)) {
        $errores['correo'] = 'El correo electrónico es obligatorio';
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $errores['correo'] = 'El correo electrónico no es válido';
    }

    if (empty($errores)) {
        guardarDatos();
    } else {
        mostrarMensaje('Por favor, corrija los errores en el formulario.', 'danger');
    }
}

function guardarDatos() {
    global $nombre, $apellido, $telefono, $correo, $mensaje;

    $linea = "---------------------------------\n";
    $linea .= "Nombre: $nombre\n";
    $linea .= "Apellido: $apellido\n";
    $linea .= "Teléfono: $telefono\n";
    $linea .= "Correo: $correo\n";
    $linea .= "Mensaje: $mensaje\n";
    $linea .= "---------------------------------\n";

    $archivo = fopen("verContactanos.txt", "a");
    if ($archivo) {
        fwrite($archivo, $linea);
        fclose($archivo);
        $_SESSION['mensaje'] = 'Datos enviados exitosamente.';
        $_SESSION['tipo'] = 'success';
        header("Location: " . $_SERVER['PHP_SELF']); // Redirige al mismo script para limpiar el formulario
        exit;
    } else {
        mostrarMensaje('Error al guardar los datos.', 'danger');
    }
}

function mostrarMensaje($mensaje, $tipo) {
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Contáctenos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #38384c;
            padding: 5px;
        }
        .formulario {
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ddd;
            background-color: #fff;
            border-radius: 10px;
        }
        .titulo {
            text-align: center;
            margin-bottom: 10px;
            text-decoration: none;
        }
        .form-footer {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        .btn {
            width: 45%;
        }
        .links {
            text-align: center;
        }
        .links p {
            padding-top: 12px;
        }
    </style>
</head>
<body>
    <div class="formulario">
        <h2 class="titulo">Contactános</h2>
        <?php if (!empty($mensaje)): ?>
        <div class="alert alert-<?php echo $tipo; ?>" role="alert">
            <?php echo $mensaje; ?>
        </div>
        <?php endif; ?>
        <form id="ContactoFormulario" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre:</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : ''; ?>" required class="form-control">
                <?php if (isset($errores['nombre'])): ?>
                    <div class="text-danger"><?php echo $errores['nombre']; ?></div>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label for="apellido" class="form-label">Apellido:</label>
                <input type="text" id="apellido" name="apellido" value="<?php echo isset($_POST['apellido']) ? htmlspecialchars($_POST['apellido']) : ''; ?>" required class="form-control">
                <?php if (isset($errores['apellido'])): ?>
                    <div class="text-danger"><?php echo $errores['apellido']; ?></div>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label for="correo" class="form-label">Correo:</label>
                <input type="email" id="correo" name="correo" value="<?php echo isset($_POST['correo']) ? htmlspecialchars($_POST['correo']) : ''; ?>" required class="form-control">
                <?php if (isset($errores['correo'])): ?>
                    <div class="text-danger"><?php echo $errores['correo']; ?></div>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label for="telefono" class="form-label">Número de Teléfono:</label>
                <input type="tel" id="telefono" name="telefono" value="<?php echo isset($_POST['telefono']) ? htmlspecialchars($_POST['telefono']) : ''; ?>" required class="form-control">
                <?php if (isset($errores['telefono'])): ?>
                    <div class="text-danger"><?php echo $errores['telefono']; ?></div>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label for="mensaje" class="form-label">Mensaje:</label>
                <textarea id="mensaje" name="mensaje" required class="form-control"><?php echo isset($_POST['mensaje']) ? htmlspecialchars($_POST['mensaje']) : ''; ?></textarea>
                <?php if (isset($errores['mensaje'])): ?>
                    <div class="text-danger"><?php echo $errores['mensaje']; ?></div>
                <?php endif; ?>
            </div>
            <div class="form-footer">
                <button type="submit" class="btn btn-primary btn-submit">Enviar</button>
            </div>
            <div class="links">
                <p><a href="../html/index.html">Regresar a la tienda</a></p>
            </div>
        </form>
    </div>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
}

// Mostrar el formulario cuando no se ha enviado ningún dato
mostrarMensaje($mensaje, isset($tipo) ? $tipo : 'info');
?>
