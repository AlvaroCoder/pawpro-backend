<?php
    require_once 'config/database.php';

    class Categoria {
        // Funcion de traer todas las categorias de la tabla Categoria
        public static function getAll(){
            $database = new Database;
            $db = $database->getConnection();
            $sql = "SELECT * FROM Categoria;";
            $stament = $db->query($sql);
            return $stament->fetchAll(PDO::FETCH_ASSOC);
        }
    }
?>