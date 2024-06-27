<?php
session_start();

$errores = array();

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    include("conexion.php");

    $nombres = isset($_POST['nombres']) ? htmlspecialchars($_POST['nombres']) : null;
    $email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : null;
    $password = isset($_POST['password']) ? $_POST['password'] : null;
    $confirmarPassword = isset($_POST['confirmarPassword']) ? $_POST['confirmarPassword'] : null;

    // Validaciones
    if (empty($nombres)) {
        $errores['nombres'] = "Debe ingresar el nombre";
    }

    if (empty($email)) {
        $errores['email'] = "El correo electrónico es obligatorio";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores['email'] = 'El correo electrónico no es válido';
    } else {
        try {
            $pdo = new PDO('mysql:host=' . $direccionservidor . ';dbname=' . $baseDatos, $usarioBD, $contraseniaBD);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql_check_email = "SELECT COUNT(*) AS count FROM cuenta WHERE email = :email";
            $stmt_check_email = $pdo->prepare($sql_check_email);
            $stmt_check_email->execute([':email' => $email]);
            $result = $stmt_check_email->fetch(PDO::FETCH_ASSOC);

            if ($result['count'] > 0) {
                $errores['email'] = "El correo electrónico ya está registrado";
            }

        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    if (empty($password)) {
        $errores['password'] = "La contraseña es obligatoria";
    }

    if (empty($confirmarPassword)) {
        $errores['confirmarPassword'] = "Debe confirmar la contraseña";
    } elseif ($password != $confirmarPassword) {
        $errores['confirmarPassword'] = "Las contraseñas no coinciden";
    }

    if (empty($errores)) {
        try {
            $nuevoPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insertar en tabla `usuario`
            $sql_insert_usuario = "INSERT INTO `usuario` (`nombres`) VALUES (:nombres)";
            $stmt_insert_usuario = $pdo->prepare($sql_insert_usuario);
            $stmt_insert_usuario->execute([':nombres' => $nombres]);

            $usuario_id = $pdo->lastInsertId(); 

            // Insertar en tabla `cuenta`
            $sql_insert_cuenta = "INSERT INTO `cuenta` (`usuario_id`, `email`, `password`) VALUES (:usuario_id, :email, :password)";
            $stmt_insert_cuenta = $pdo->prepare($sql_insert_cuenta);
            $stmt_insert_cuenta->execute([
                ':usuario_id' => $usuario_id,
                ':email' => $email,
                ':password' => $nuevoPassword
            ]);

            $_SESSION['usuario_id'] = $usuario_id; // Guardar el ID del usuario en sesión

            header("Location: login.php");
            exit();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #38384c;
            padding: 20px;
        }
        .registro-form {
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ddd;
            background-color: #fff;
            border-radius: 10px;
        }
        .titulo {
            text-align: center;
            margin-bottom: 20px; 
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
<main>
    <div class="registro-form">
        <div class="titulo">
            <h2>Registro</h2>
        </div>
        <?php
        if (!empty($errores)) {
            foreach ($errores as $error) {
                echo "<div id='alerta' class='alert alert-danger'>$error</div>";
            }
        }
        ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="mb-3">
                <label for="nombres" class="form-label">Nombres:</label>
                <input type="text" class="form-control" id="nombres" name="nombres" value="<?php if(isset($_POST['nombres'])) echo htmlspecialchars($_POST['nombres']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Correo:</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php if(isset($_POST['email'])) echo htmlspecialchars($_POST['email']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
                <label for="confirmarPassword" class="form-label">Confirmar Contraseña:</label>
                <input type="password" class="form-control" id="confirmarPassword" name="confirmarPassword" required>
            </div>
            <div class="form-footer">
                <button type="submit" class="btn btn-primary">Registrarse</button>
            </div>
            <div class="links">
                <p>¿Ya tienes una cuenta? <a href="login.php">Inicia sesión aquí</a></p>
            </div>
        </form>
    </div>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
