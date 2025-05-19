<?php

class FacturaCompraController {

    public function registrarNuevaFactura() {
        // 1. Obtener los datos del cuerpo de la solicitud (payload JSON)
        $data = json_decode(file_get_contents("php://input"));

        // 2. Validación básica de datos (muy simple por ahora)
        if (is_null($data)) { // json_decode devuelve null si el JSON es inválido
            http_response_code(400); // Bad Request
            echo json_encode(['message' => 'Error: El JSON enviado es inválido.']);
            return;
        }

        if (!isset($data->proveedor_id) || !isset($data->numero_factura) || !isset($data->items) || !is_array($data->items)) {
            http_response_code(400); // Bad Request
            echo json_encode([
                'message' => 'Datos incompletos o mal formados. Se requiere proveedor_id, numero_factura e items (array).',
                'received_data_keys' => array_keys(get_object_vars($data)) // Ayuda a depurar qué se recibió
                ]);
            return;
        }

        // Si llegamos aquí, los datos básicos parecen estar presentes.
        // Por ahora, solo devolvemos los datos recibidos para confirmar que el endpoint funciona.
        http_response_code(200); // OK
        echo json_encode([
            'message' => 'Endpoint /api/facturas-compra alcanzado. Datos recibidos (procesamiento pendiente).',
            'received_data' => $data
        ]);
    }
}
?>