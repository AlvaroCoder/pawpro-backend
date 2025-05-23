<?php
    require_once 'config/database.php';

    class Usuario{
        public function registrar(
            $nombre_usuario,
            $contrasena_hash,
            $correo
        ){
            $database = new Database();
            echo $nombre_usuario;
            $db = $database->getConnection();
            
            $rol_id = 4; // administrador

            $sql = "INSERT INTO usuarios (nombre_usuario, contraseña_hash, correo_electronico, estado, rol_id)
                    VALUES (:nombre_usuario, :contrasena_hash, :correo, 'activo', :rol_id)";
            
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':nombre_usuario', $nombre_usuario);
            $stmt->bindParam(':contrasena_hash', $contrasena_hash);
            $stmt->bindParam(':correo', $correo);
            $stmt->bindParam(':rol_id', $rol_id);
        
            return $stmt->execute();
        }
    }

?>