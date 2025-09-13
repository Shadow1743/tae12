<?php
// registro.php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Inicializar variables
$errores = [];
$datos = [
    'nombre_completo' => '',
    'correo' => '',
    'contrasena' => '',
    'con_contrasena' => ''
];

// Configuración de la base de datos para XAMPP
$host = 'localhost';
$dbname = 'TAE'; // Nombre de tu base de datos
$username = 'root'; // Usuario por defecto de XAMPP
$password = ''; // Contraseña por defecto de XAMPP (vacía)

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger y sanitizar datos del formulario
    $datos['nombre_completo'] = trim($_POST['nombre_completo'] ?? '');
    $datos['correo'] = trim($_POST['correo'] ?? '');
    $datos['contrasena'] = $_POST['contrasena'] ?? '';
    $datos['con_contrasena'] = $_POST['con_contrasena'] ?? '';
    
    // Validaciones
    if (empty($datos['nombre_completo'])) {
        $errores[] = 'El nombre completo es obligatorio';
    } elseif (strlen($datos['nombre_completo']) > 50) {
        $errores[] = 'El nombre no puede tener más de 50 caracteres';
    }
    
    if (empty($datos['correo'])) {
        $errores[] = 'El correo electrónico es obligatorio';
    } elseif (!filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) {
        $errores[] = 'El formato del correo electrónico no es válido';
    } elseif (strlen($datos['correo']) > 50) {
        $errores[] = 'El correo no puede tener más de 50 caracteres';
    }
    
    if (empty($datos['contrasena'])) {
        $errores[] = 'La contraseña es obligatoria';
    } elseif (strlen($datos['contrasena']) < 6) {
        $errores[] = 'La contraseña debe tener al menos 6 caracteres';
    } elseif (strlen($datos['contrasena']) > 10) {
        $errores[] = 'La contraseña no puede tener más de 10 caracteres';
    }
    
    if ($datos['contrasena'] !== $datos['con_contrasena']) {
        $errores[] = 'Las contraseñas no coinciden';
    }
    
    // Si no hay errores, proceder con la inserción en la base de datos
    if (empty($errores)) {
        try {
            // Conexión a la base de datos
            $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Verificar si el correo ya existe
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM registro WHERE correo = :correo");
            $stmt->bindParam(':correo', $datos['correo']);
            $stmt->execute();
            
            if ($stmt->fetchColumn() > 0) {
                $errores[] = 'Este correo electrónico ya está registrado';
            } else {
                // Encriptar la contraseña (usando hash simple por limitación de 10 caracteres)
                $contrasenaHash = password_hash($datos['contrasena'], PASSWORD_DEFAULT);
                
                // Si el hash es demasiado largo, usar un método alternativo
                if (strlen($contrasenaHash) > 10) {
                    $contrasenaHash = substr(md5($datos['contrasena']), 0, 10);
                }
                
                // Insertar nuevo usuario en la tabla "registro"
                // NOTA: Ahora la columna se llama con_contrasena (sin eñe)
                $stmt = $pdo->prepare("INSERT INTO registro (nombre_completo, correo, contrasena, con_contrasena) 
                                      VALUES (:nombre_completo, :correo, :contrasena, :con_contrasena)");
                
                // Corregimos los nombres de los parámetros para que coincidan con la consulta
                $stmt->bindParam(':nombre_completo', $datos['nombre_completo']);
                $stmt->bindParam(':correo', $datos['correo']);
                $stmt->bindParam(':contrasena', $contrasenaHash);
                $stmt->bindParam(':con_contrasena', $contrasenaHash);
                
                if ($stmt->execute()) {
                    $_SESSION['mensaje'] = 'Registro exitoso. Ahora puedes iniciar sesión.';
                    $_SESSION['tipo_mensaje'] = 'success';
                    header('Location: ' . $_SERVER['PHP_SELF']);
                    exit;
                } else {
                    $errores[] = 'Error al registrar el usuario. Intenta nuevamente.';
                }
            }
        } catch (PDOException $e) {
            $errores[] = 'Error de conexión: ' . $e->getMessage();
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #e6f4ea;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .card {
            margin-top: 2rem;
            margin-bottom: 2rem;
        }
        .btn-green {
            background-color: #28a745;
            border-color: #28a745;
            color: white;
            transition: all 0.3s ease;
        }
        .btn-green:hover {
            background-color: #218838;
            border-color: #1e7e34;
            transform: translateY(-2px);
        }
        .form-control:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25);
        }
        .logo-container {
            margin: 0 auto;
            width: 100px;
            height: 100px;
        }
        .alert-custom {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="d-flex justify-content-center align-items-center min-vh-100">
            <div class="card shadow p-4" style="max-width: 450px; width: 100%; border-radius: 1rem;">
                <!-- Logo -->
                <div class="text-center mb-4">
                    <div class="logo-container">
                        <img src="Logo.jpeg" alt="Logo" class="img-fluid rounded-circle border border-success border-2" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                </div>

                <!-- Título -->
                <h4 class="text-center mb-3 text-success">Crear cuenta</h4>
                
                <!-- Mensajes de error/éxito -->
                <?php if (isset($_SESSION['mensaje'])): ?>
                    <div class="alert-custom <?= $_SESSION['tipo_mensaje'] === 'success' ? 'alert alert-success' : 'alert alert-danger' ?>">
                        <?= $_SESSION['mensaje'] ?>
                    </div>
                    <?php unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']); ?>
                <?php endif; ?>
                
                <?php if (!empty($errores)): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errores as $error): ?>
                            <div><?= htmlspecialchars($error) ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <!-- Formulario -->
                <form action="registro.php" method="POST" id="registroForm" novalidate>
                    <div class="mb-3">
                        <label for="nombre_completo" class="form-label">Nombre completo</label>
                        <input type="text" class="form-control <?= !empty($errores) && empty($datos['nombre_completo']) ? 'is-invalid' : '' ?>" 
                               id="nombre_completo" name="nombre_completo" 
                               value="<?= htmlspecialchars($datos['nombre_completo']) ?>" required>
                        <div class="invalid-feedback">Por favor ingresa tu nombre completo</div>
                    </div>

                    <div class="mb-3">
                        <label for="correo" class="form-label">Correo electrónico</label>
                        <input type="email" class="form-control <?= !empty($errores) && empty($datos['correo']) ? 'is-invalid' : '' ?>" 
                               id="correo" name="correo" 
                               value="<?= htmlspecialchars($datos['correo']) ?>" required>
                        <div class="invalid-feedback">Por favor ingresa un correo válido</div>
                    </div>

                    <div class="mb-3">
                        <label for="contrasena" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="contrasena" name="contrasena" required>
                        <div class="invalid-feedback">La contraseña debe tener al menos 6 caracteres</div>
                    </div>

                    <div class="mb-3">
                        <label for="con_contrasena" class="form-label">Confirmar contraseña</label>
                        <input type="password" class="form-control" id="con_contrasena" name="con_contrasena" required>
                        <div class="invalid-feedback">Las contraseñas no coinciden</div>
                    </div>

                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-green btn-lg">Registrarse</button>
                    </div>

                    <div class="text-center">
                        <small class="text-muted">¿Ya tienes una cuenta? <a href="ingresar.php" class="text-success">Inicia sesión</a></small>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('registroForm').addEventListener('submit', function(event) {
            event.preventDefault();
            
            // Validación de formulario
            const form = event.target;
            const password = document.getElementById('contrasena').value;
            const confirmPassword = document.getElementById('con_contrasena').value;
            
            if (!form.checkValidity()) {
                event.stopPropagation();
                form.classList.add('was-validated');
                return;
            }
            
            if (password !== confirmPassword) {
                document.getElementById('con_contrasena').classList.add('is-invalid');
                event.stopPropagation();
                return;
            }
            
            // Si todo está bien, enviar el formulario
            form.submit();
        });

        // Validación en tiempo real para confirmar contraseña
        document.getElementById('con_contrasena').addEventListener('input', function() {
            const password = document.getElementById('contrasena').value;
            if (this.value !== password) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });
    </script>
</body>
</html>