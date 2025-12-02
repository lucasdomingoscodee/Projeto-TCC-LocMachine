<?php
$host = 'localhost';
$banco = 'locadora_maquinas';
$usuario_mysql = 'root';
$senha_mysql = ''; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$banco;charset=utf8", $usuario_mysql, $senha_mysql);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    if(!isset($_SESSION)) {
        session_start();
    }
} catch (PDOException $erro) {
    die("Erro ao conectar: " . $erro->getMessage());
}
?>