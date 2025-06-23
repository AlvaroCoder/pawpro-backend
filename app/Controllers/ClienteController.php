<?php
require_once 'app/models/Cliente.php';

class ClienteController {
    private $model;

    public function __construct() {
        $this->model = new ClienteModel();
    }

    public function listarClientes() {
        $clientes = $this->model->obtenerClientes();
        echo json_encode($clientes);
    }

    public function crearCliente() {
        $input = json_decode(file_get_contents("php://input"), true);
        if ($this->model->crearCliente($input)) {
            echo json_encode(["message" => "Cliente creado exitosamente"]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Error al crear cliente"]);
        }
    }

    public function actualizarCliente($id) {
        $input = json_decode(file_get_contents("php://input"), true);
        if ($this->model->actualizarCliente($id, $input)) {
            echo json_encode(["message" => "Cliente actualizado exitosamente"]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Error al actualizar cliente"]);
        }
    }
}
?>