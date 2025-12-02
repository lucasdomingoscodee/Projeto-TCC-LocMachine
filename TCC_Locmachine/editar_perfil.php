<?php
require 'conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['usuario_id'];
$msg = "";
$tipo_msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];
    $endereco = $_POST['endereco'];
    $senha = $_POST['senha'];

    $sql = "UPDATE usuarios SET nome=?, email=?, telefone=?, endereco=?, senha=? WHERE id=?";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$nome, $email, $telefone, $endereco, $senha, $id_user])) {
        $_SESSION['usuario_nome'] = $nome;
        $msg = "Perfil atualizado com sucesso!";
        $tipo_msg = "sucesso";
    } else {
        $msg = "Erro ao atualizar perfil.";
        $tipo_msg = "erro";
    }
}

$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$id_user]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Perfil - LOCMACHINE</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <header>
        <div class="container nav-flex">
            <div class="logo"><span class="logo-box">L</span> LOCMACHINE</div>
            <div class="menu-icones">
                <a href="perfil.php">Voltar ao Perfil</a>
            </div>
        </div>
    </header>

    <div class="container" style="margin-top: 40px; max-width: 600px;">
        <div class="card">
            <h3 style="margin-bottom: 20px;"><i class="fa fa-user-edit"></i> Editar Meus Dados</h3>
            
            <?php if($msg): ?>
                <div class="alerta <?php echo $tipo_msg; ?>"><?php echo $msg; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="input-group">
                    <label>Nome Completo</label>
                    <input type="text" name="nome" value="<?php echo $usuario['nome']; ?>" required>
                </div>
                
                <div class="input-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?php echo $usuario['email']; ?>" required>
                </div>

                <div class="input-group">
                    <label>Telefone</label>
                    <input type="text" name="telefone" value="<?php echo $usuario['telefone']; ?>">
                </div>

                <div class="input-group">
                    <label>Endereço Completo</label>
                    <input type="text" name="endereco" value="<?php echo $usuario['endereco']; ?>">
                </div>

                <div class="input-group">
                    <label>Senha (Mantenha a atual ou digite uma nova)</label>
                    <input type="text" name="senha" value="<?php echo $usuario['senha']; ?>" required>
                </div>

                <div style="display:flex; gap:10px; margin-top:20px;">
                    <button type="submit" class="btn btn-laranja">Salvar Alterações</button>
                    <a href="perfil.php" class="btn" style="background:#ddd; color:#333;">Cancelar</a>
                </div>
            </form>
        </div>
    </div>

    <footer class="footer-zao">
        <div class="container footer-content">
            <div class="footer-left">
                <div class="logo" style="color: white; margin-bottom: 10px;">
                    <span class="logo-box">L</span> LOCMACHINE
                </div>
            </div>
            <div class="footer-right">
                <h4>Fale Conosco</h4>
                <p><i class="fa fa-envelope"></i> contato@locmachine.com</p>
                <p><i class="fa fa-phone"></i> (31) 99999-8888</p>
                <p><i class="fa fa-map-marker-alt"></i> Belo Horizonte, MG</p>
            </div>
        </div>
    </footer>

</body>
</html>