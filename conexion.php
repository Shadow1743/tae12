<?php


class Conexion {
    private $host;
    private $usuario;
    private $password;
    private $base_datos;
    private $conexion;
    
    public function __construct() {
      
        $this->host = "localhost";
        $this->usuario = "root"; 
        $this->password = ""; 
        $this->base_datos = "TAE";
    }
    
    /
      @return PDO 
     
    public function conectar() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->base_datos};charset=utf8";
            
            $this->conexion = new PDO($dsn, $this->usuario, $this->password);
            
            $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            return $this->conexion;
            
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }
    
    
    public function desconectar() {
        $this->conexion = null;
    }
   
    public function ejecutarConsulta($sql, $params = []) {
        try {
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            die("Error en la consulta: " . $e->getMessage());
        }
    }
}

function obtenerConexion() {
    $conexion = new Conexion();
    return $conexion->conectar();
}
?>