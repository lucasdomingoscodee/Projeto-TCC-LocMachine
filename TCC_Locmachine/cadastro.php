<?php
require 'conexao.php';

$erro = "";
$sucesso = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $telefone = $_POST['telefone'];
    $endereco = $_POST['endereco'];
    
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email");
    $stmt->execute(['email' => $email]);
    
    if ($stmt->rowCount() > 0) {
        $erro = "Este e-mail já está cadastrado!";
    } else {
        $sql = "INSERT INTO usuarios (nome, email, senha, telefone, endereco, tipo_cliente) 
                VALUES (:nome, :email, :senha, :telefone, :endereco, 'Cliente Comum')";
        
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([
            'nome' => $nome,
            'email' => $email,
            'senha' => $senha,
            'telefone' => $telefone,
            'endereco' => $endereco
        ])) {
            $sucesso = "Conta criada! <a href='login.php'>Faça login aqui</a>";
        } else {
            $erro = "Erro ao criar conta.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Criar Conta</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container login-page">
        <div class="card login-box cadastro-box">
            <h3 style="margin-bottom: 20px;">Criar nova conta</h3>
            
            <?php if($erro): ?><div class="alerta erro"><?php echo $erro; ?></div><?php endif; ?>
            <?php if($sucesso): ?><div class="alerta sucesso"><?php echo $sucesso; ?></div><?php endif; ?>

            <?php if(empty($sucesso)): ?>
            <form method="POST">
                <div class="input-group"><label>Nome</label><input type="text" name="nome" required></div>
                <div class="input-group"><label>Email</label><input type="email" name="email" required></div>
                <div class="input-group"><label>Telefone</label><input type="text" name="telefone"></div>
                <div class="input-group"><label>Endereço</label><input type="text" name="endereco"></div>
                <div class="input-group"><label>Senha</label><input type="password" name="senha" required></div>
                <button type="submit" class="btn btn-laranja">Cadastrar</button>
            </form>
            <?php endif; ?>
            <p style="text-align:center; margin-top:15px;"><a href="login.php" style="color:#ff6600">Voltar ao Login</a></p>
        </div>
    </div>
</body>
</html>