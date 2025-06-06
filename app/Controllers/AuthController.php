<?php

require_once __DIR__ . '/../../config/database.php';

class AuthController {
    private $db;
    private $jwtSecretKey = 'tu_clave_secreta_jwt_actualizala_en_produccion';

    public function __construct() {
        // Usamos tu clase Database existente
        $database = new Database();
        $this->db = $database->getConnection();
    }

    /**
     * Maneja el proceso de login de usuarios
     */
    public function login() {
        // Solo permitir método POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['message' => 'Método no permitido']);
            return;
        }

        // Obtener y validar datos JSON
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode(['message' => 'Datos JSON inválidos']);
            return;
        }

        if (!isset($data['email']) || !isset($data['password'])) {
            http_response_code(400);
            echo json_encode(['message' => 'Email y contraseña son requeridos']);
            return;
        }

        $email = trim($data['email']);
        $password = $data['password'];

        try {
            // Consulta preparada para seguridad
            $query = "SELECT id, nombre, email, password, rol FROM usuarios WHERE email = :email AND estado = 'activo' LIMIT 1";
            $stmt = $this->db->prepare($query);
            
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            if ($stmt->rowCount() === 0) {
                http_response_code(401);
                echo json_encode(['message' => 'Credenciales incorrectas o usuario inactivo']);
                return;
            }

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verificación segura de contraseña
            if (!password_verify($password, $user['password'])) {
                http_response_code(401);
                echo json_encode(['message' => 'Credenciales incorrectas']);
                return;
            }

            // Generar token JWT (sin datos sensibles)
            $tokenData = [
                'id' => $user['id'],
                'email' => $user['email'],
                'rol' => $user['rol']
            ];
            
            $token = $this->generateJWT($tokenData);

            // Respuesta exitosa (sin enviar password)
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'Autenticación exitosa',
                'token' => $token,
                'user' => [
                    'id' => $user['id'],
                    'nombre' => $user['nombre'],
                    'email' => $user['email'],
                    'rol' => $user['rol']
                ]
            ]);

        } catch (PDOException $e) {
            error_log('Error en AuthController: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error en el servidor',
                'error' => 'Error interno del servidor' // En producción no mostrar detalles
            ]);
        }
    }

    /**
     * Genera un token JWT seguro
     */
    private function generateJWT($data) {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $payload = json_encode([
            'iss' => 'pawpro_api',       // Emisor
            'aud' => 'pawpro_client',    // Audiencia
            'iat' => time(),             // Emitido en
            'exp' => time() + 86400,     // Expira en 24 horas (86400 segundos)
            'sub' => $data['id'],       // Subject (ID usuario)
            'user' => [                 // Datos del usuario
                'id' => $data['id'],
                'email' => $data['email'],
                'rol' => $data['rol']
            ]
        ]);

        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $this->jwtSecretKey, true);
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }

    /**
     * Middleware para verificar token (opcional)
     */
    public static function authenticate($token) {
        try {
            $tokenParts = explode('.', $token);
            if (count($tokenParts) !== 3) return false;

            $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $tokenParts[1])), true);
            
            // Verificar expiración
            if (isset($payload['exp']) && $payload['exp'] < time()) {
                return false;
            }

            // Verificar firma (deberías usar tu clave secreta real aquí)
            $jwtSecretKey = 'tu_clave_secreta_jwt_actualizala_en_produccion';
            $signature = hash_hmac('sha256', $tokenParts[0] . "." . $tokenParts[1], $jwtSecretKey, true);
            $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

            return ($base64UrlSignature === $tokenParts[2]) ? $payload : false;
            
        } catch (Exception $e) {
            error_log('Error en autenticación: ' . $e->getMessage());
            return false;
        }
    }
}