
<?php
    /**
     * PDO es un manejador de objetos de PHP, consitente necesario para acceder a las bases de datos 
     * usando PHP. Cada base de datos puede implementar PDO. Y agregar alguna fucnionalidad 
     * como una extension de alguna función.
     * https://www.php.net/manual/en/intro.pdo.php
     */
    class Database{
        private $host = "localhost";
        private $db_name = "registro_productos";
        private $username = "root";
        private $password = "123456";
        public $conn;

        public function getConnection(){
            $this->conn = null;

            try {
                $this->conn = new PDO(
                    "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                    $this->username,
                    $this->password
                );
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $exception) {
                echo "Error de conexion: ".$exception->getMessage(); 
            }
            return $this->conn;
        }
        
    }
?>