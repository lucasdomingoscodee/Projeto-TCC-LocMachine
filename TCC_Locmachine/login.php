<?php
require 'conexao.php';
$erro = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && $senha == $usuario['senha']) {
        
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        $_SESSION['is_admin'] = $usuario['is_admin'];
        
        if($usuario['is_admin'] == 1){
            header("Location: admin.php");
        } else {
            header("Location: index.php");
        }
        exit;
    } else {
        $erro = "Email ou senha incorretos!";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container login-page">
        <div class="login-box">
            <h3 style="margin-bottom:20px; text-align:center;">Login</h3>
            <?php if($erro) echo "<p style='color:red; text-align:center'>$erro</p>"; ?>
            <form method="POST">
                <div class="input-group"><label>Email</label><input type="email" name="email" required></div>
                <div class="input-group"><label>Senha</label><input type="password" name="senha" required></div>
                <button class="btn btn-laranja" style="width:100%">Entrar</button>
            </form>
            <p style="text-align:center; margin-top:15px;"><a href="cadastro.php" style="color: #ff6600">Criar conta</a></p>
        </div>
    </div>
</body>
</html>