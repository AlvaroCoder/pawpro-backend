<?php
require_once 'app/models/Usuario.php';

class UsuarioController
{
    public function registrar()
    {
    
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405); 
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }

       
        $nombre_usuario = $_POST['nombre_usuario'];
        $password = $_POST['password'];
        $correo = $_POST['correo'];
        
        echo $nombre_usuario." ".$password. "".$correo;
       
        if (empty($nombre_usuario) ||  empty($password) || empty($correo)) {
            http_response_code(400); 
            echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios']);
            return;
        }

      
        $contrasena_hash = password_hash($password, PASSWORD_BCRYPT);

        
        $usuario = new Usuario();
        $resultado = $usuario->registrar($nombre_usuario, $contrasena_hash, $correo);

        if ($resultado) {
            echo json_encode(['success' => true, 'message' => 'Usuario registrado con éxito']);
        } else {
            http_response_code(500); 
            echo json_encode(['success' => false, 'message' => 'No se pudo registrar el usuario']);
        }
    }
}
?>