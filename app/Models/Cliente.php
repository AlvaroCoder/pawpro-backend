<?php
require_once 'config/database.php';

class ClienteModel {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function obtenerClientes() {
        $query = "SELECT * FROM clientes";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crearCliente($data) {
        $query = "INSERT INTO clientes (dni, nombre_cliente, direccion, telefono, correo_electronico, fecha_creacion, fecha_modificacion)
                  VALUES (:dni, :nombre, :direccion, :telefono, :correo, NOW(), NOW())";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':dni' => $data['dni'],
            ':nombre' => $data['nombre_cliente'],
            ':direccion' => $data['direccion'],
            ':telefono' => $data['telefono'],
            ':correo' => $data['correo_electronico']
        ]);
    }

    public function actualizarCliente($id, $data) {
        $query = "UPDATE clientes SET 
                    dni = :dni,
                    nombre_cliente = :nombre,
                    direccion = :direccion,
                    telefono = :telefono,
                    correo_electronico = :correo,
                    fecha_modificacion = NOW()
                  WHERE cliente_id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':dni' => $data['dni'],
            ':nombre' => $data['nombre_cliente'],
            ':direccion' => $data['direccion'],
            ':telefono' => $data['telefono'],
            ':correo' => $data['correo_electronico'],
            ':id' => $id
        ]);
    }
}