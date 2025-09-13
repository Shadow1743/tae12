<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Establecer variables de sesión como si el usuario hubiera iniciado sesión correctamente
    $_SESSION['usuario'] = [
        'correo' => $_POST['correo'] ?? 'usuario@ejemplo.com',
        'nombre' => 'Usuario Demo'
    ];
    $_SESSION['loggedin'] = true;
    $_SESSION['mensaje'] = 'Has iniciado sesión correctamente';
    $_SESSION['tipo_mensaje'] = 'success';

    // Redirigir a ia.php
    header('Location: ia.php');
    exit;
}

$errores = $_SESSION['errores_login'] ?? [];
$datos = $_SESSION['datos_login'] ?? [];
unset($_SESSION['errores_login'], $_SESSION['datos_login']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Iniciar Sesión</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #e6f4ea;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .card {
      border-radius: 1rem;
    }
    .alert-custom {
      padding: 10px;
      margin-bottom: 15px;
      border-radius: 5px;
    }
    .password-toggle {
      cursor: pointer;
      position: absolute;
      right: 10px;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
    }
    .password-container {
      position: relative;
    }
  </style>
</head>
<body>
  <div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card shadow p-4" style="max-width: 400px; width: 100%;">
      <!-- Logo -->
      <div class="text-center mb-4">
        <img src="Logo.jpeg" alt="Logo" class="rounded-circle border border-success border-2" style="width: 100px; height: 100px; object-fit: cover;">
      </div>

      <!-- Título -->
      <h4 class="text-center mb-3 text-success">Iniciar Sesión</h4>
      
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
            <div><?= $error ?></div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <!-- Formulario -->
      <form method="POST" id="loginForm">
        <div class="mb-3">
          <label for="correo" class="form-label text-success">Correo electrónico</label>
          <input type="email" class="form-control border-success <?= isset($errores) && count($errores) > 0 ? 'is-invalid' : '' ?>" 
                 id="correo" name="correo" placeholder="tu.correo@ejemplo.com" 
                 value="<?= htmlspecialchars($datos['correo'] ?? '') ?>" required>
        </div>

        <div class="mb-3 password-container">
          <label for="contrasena" class="form-label text-success">Contraseña</label>
          <input type="password" class="form-control border-success <?= isset($errores) && count($errores) > 0 ? 'is-invalid' : '' ?>" 
                 id="contrasena" name="contrasena" placeholder="*******" required>
          <button type="button" class="password-toggle" id="togglePassword">
            <i class="bi bi-eye"></i>
          </button>
        </div>

        <div class="d-grid">
          <button type="submit" class="btn btn-success">Entrar</button>
        </div>
      </form>

      <!-- Pasos extras -->
      <div class="text-center mt-3">
        <a href="#" class="small text-muted">¿Olvidaste tu contraseña?</a><br>
        <small class="text-muted">¿No tienes cuenta? <a href="registro.php" class="text-success">Regístrate</a></small>
      </div>
    </div>
  </div>

  <!-- Agregar iconos de Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Función para mostrar/ocultar contraseña
    document.getElementById('togglePassword').addEventListener('click', function() {
      const passwordInput = document.getElementById('contrasena');
      const icon = this.querySelector('i');
      
      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
      } else {
        passwordInput.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
      }
    });
  </script>
</body>
</html>