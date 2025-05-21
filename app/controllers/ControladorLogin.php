<?php
require_once 'config/database.php';

class AuthController {
    public function login() {
        // Obtener datos del body (puede venir por POST o JSON)
        $data = json_decode(file_get_contents("php://input"), true);
        $username = $data['username'] ?? null;
        $password = $data['password'] ?? null;

        if (!$username || !$password) {
            http_response_code(400);
            echo json_encode(['error' => 'Faltan credenciales.']);
            return;
        }

        try {
            $pdo = getConnection(); // Desde database.php
            $stmt = $pdo->prepare("SELECT * FROM Usuarios WHERE nombre_usuario = :username");
            $stmt->execute(['username' => $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user || !password_verify($password, $user['password'])) {
                http_response_code(401);
                echo json_encode(['error' => 'Credenciales inválidas.']);
                return;
            }

            // Aquí puedes generar un token de sesión, JWT o iniciar sesión PHP
            echo json_encode([
                'message' => 'Inicio de sesión exitoso.',
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                ]
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error de servidor: ' . $e->getMessage()]);
        }
    }
}
?>