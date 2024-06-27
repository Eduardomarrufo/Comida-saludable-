<?php
session_start();

$errores = array();

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    include("conexion.php");

    $email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : null;
    $password = isset($_POST['password']) ? $_POST['password'] : null;

    if (empty($email)) {
        $errores['email'] = "El email es obligatorio";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores['email'] = 'El email no es válido';
    }

    if (empty($password)) {
        $errores['password'] = "La contraseña es obligatoria";
    }

    if (empty($errores)) {
        try {
            $pdo = new PDO('mysql:host=' . $direccionservidor . ';dbname=' . $baseDatos, $usarioBD, $contraseniaBD);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Buscar en la tabla `cuenta`
            $sql = "SELECT cuenta.*, usuario.nombres FROM cuenta JOIN usuario ON cuenta.usuario_id = usuario.id WHERE cuenta.email=:email";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['usuario_id'] = $user['usuario_id'];
                $_SESSION['usuario_nombres'] = $user['nombres'];

                header("Location: ../html/index.html");
                exit();
            } else {
                $errores['general'] = "Usuario o contraseña incorrectos";
            }

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
    <title>Iniciar sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #38384c;
            padding: 20px;
        }
        .login-form {
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
        .links {
            text-align: center;
        }
        .links p {
            padding-top: 12px;
        }
        .form-footer {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        .btn {
            width: 45%;
        }
    </style>
</head>
<body>
<main>
    <div class="login-form">
        <div class="titulo">
            <h2>Iniciar Sesión</h2>
        </div>
        <?php
        if (!empty($errores)) {
            foreach ($errores as $error) {
                echo "<div id='alerta' class='alert alert-danger'>$error</div>";
            }
        }
        ?>
        <form id="loginForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="mb-3">
                <label for="email" class="form-label">Correo:</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php if(isset($_POST['email'])) echo htmlspecialchars($_POST['email']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="form-footer">
                <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
            </div>
            <div class="links">
                <p>¿No tienes una cuenta? <a href="registro.php">Regístrate aquí</a></p>
            </div>
        </form>
    </div>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
