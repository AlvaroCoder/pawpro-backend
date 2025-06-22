<?php
require_once 'app/models/Usuario.php';

class UsuarioController
{
    private $jwtSecretKey = "pawpro-backend";
    public function listarUsuarios(){
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode([
                'success' => false,
                'message' => 'Metodo no permitido'
            ]);
            return;
        }

        $usuarioModelo = new Usuario();
        $response = $usuarioModelo->listarUsuarios();
        echo json_encode($response);
    }

    public function iniciarSesion(){
        // Validamos otro tipo de conexiones a la API
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode([
                'success' => false,
                'message' => 'Metodo no permitido'
            ]);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Datos no validos'
            ]);
            return;
        }
        if (!isset($data['email']) || !isset($data['password'])) {
            http_response_code(400);
            echo json_encode([
                'success'=> false,
                'message'=> 'Es necesario completar los campos de email y contraseña'
            ]);
            return;
        }
        $email = trim($data['email']);
        $password = $data['password'];

        try {
           $usuarioModelo = new Usuario();
           $user = $usuarioModelo->autenticar($email, $password);
           // validamos si existe usuario
           if (!$user) {
                http_response_code(401);
                echo json_encode([
                    'success'=>false,
                    'message'=>'Credenciales incorrectas'
                ]);
                return;
           }

           // Generamos el token
           $token = $this->generateJWT([
            'id'=>$user['id'],
            'email'=>$user['correo_electronico'],
            'rol'=>$user['rol_id']
           ]);

           http_response_code(200);
           echo json_encode([
                'success' => true,
                'message' => 'Autenticación exitosa',
                'token' => $token,
                'user' => [
                    'id' => $user['id'],
                    'nombre' => $user['nombre_usuario'],
                    'email' => $user['correo_electronico'],
                    'rol' => $user['rol_id']
                ]
            ]);


        } catch (Exception $error) {
            http_response_code(401);
            echo json_encode([
                'success'=>false,
                'message'=>$error->getMessage()
            ]);
        }
    }
    public function registrar()
    {
    
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405); 
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $nombre_usuario = trim($data['username']);
        $password = trim($data['password']);
        $email = $data['email'];
       
        if (empty($nombre_usuario) ||  empty($password) || empty($email)) {
            http_response_code(400); 
            echo json_encode([
                'success' => false, 
                'message' => 'Todos los campos son obligatorios']);
            return;
        }

        $usuario = new Usuario();

        if ($usuario->correoExiste($email)) {
            http_response_code(401);
            echo json_encode([
                'success'=>false,
                'message'=> 'El correo ya existe'
            ]);
            return;
        }

        if ($usuario->usuarioExiste($nombre_usuario)) {
            http_response_code(401);
            echo json_encode([
                'success'=>false,
                'message'=> 'El usuario ya existe'
            ]);
            return;
        }

        $password_hash = password_hash($data['password'], PASSWORD_DEFAULT);
        $resultado = $usuario->registrar($nombre_usuario, $password_hash, $email);

        if ($resultado) {
            
            echo json_encode(['success' => true, 'message' => 'Usuario registrado con éxito']);
        } else {
            http_response_code(500); 
            echo json_encode(['success' => false, 'message' => 'No se pudo registrar el usuario']);
        }
    }
}
?>