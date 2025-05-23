<?php
require_once 'app/models/Usuario.php';

class UsuarioController
{
    public function registrar()
    {
        // Solo permitimos método POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405); // Método no permitido
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }

        // Recolect"ar datos del formulario
        $nombre_usuario = $_POST['nombre_usuario'];
        $password = $_POST['password'];
        $correo = $_POST['correo'];
        
        echo $nombre_usuario." ".$password. "".$correo;
        // Validación simple
        if (empty($nombre_usuario) ||  empty($password) || empty($correo)) {
            http_response_code(400); // Petición incorrecta
            echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios']);
            return;
        }

        // Hash de contraseña
        $contrasena_hash = password_hash($password, PASSWORD_BCRYPT);

        // Crear usuario
        $usuario = new Usuario();
        $resultado = $usuario->registrar($nombre_usuario, $contrasena_hash, $correo);

        if ($resultado) {
            echo json_encode(['success' => true, 'message' => 'Usuario registrado con éxito']);
        } else {
            http_response_code(500); // Error del servidor
            echo json_encode(['success' => false, 'message' => 'No se pudo registrar el usuario']);
        }
    }
}
?>