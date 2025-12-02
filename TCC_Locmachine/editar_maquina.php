<?php
require 'conexao.php';

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];
$msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $cat = $_POST['categoria'];
    $desc = $_POST['descricao'];
    $img = $_POST['imagem'];
    $preco = $_POST['preco'];

    $sql = "UPDATE maquinas SET nome=?, categoria=?, descricao=?, imagem_url=?, preco_diaria=? WHERE id=?";
    $stmt = $pdo->prepare($sql);
    if($stmt->execute([$nome, $cat, $desc, $img, $preco, $id])) {
        $msg = "Máquina atualizada com sucesso!";
    } else {
        $msg = "Erro ao atualizar.";
    }
}

$stmt = $pdo->prepare("SELECT * FROM maquinas WHERE id = ?");
$stmt->execute([$id]);
$maquina = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Máquina</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="container nav-flex">
            <div class="logo"><span class="logo-box">ADM</span> Editar</div>
            <div class="menu-icones"><a href="admin.php">Voltar</a></div>
        </div>
    </header>

    <div class="container" style="margin-top: 30px;">
        <div class="admin-section">
            <h3>Editando: <?php echo $maquina['nome']; ?></h3>
            
            <?php if($msg): ?>
                <div class="alerta sucesso"><?php echo $msg; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="input-group">
                    <label>Nome</label>
                    <input type="text" name="nome" value="<?php echo $maquina['nome']; ?>" required>
                </div>
                <div class="input-group">
                    <label>Categoria</label>
                    <input type="text" name="categoria" value="<?php echo $maquina['categoria']; ?>" required>
                </div>
                <div class="input-group">
                    <label>Descrição</label>
                    <textarea name="descricao" required><?php echo $maquina['descricao']; ?></textarea>
                </div>
                <div class="input-group">
                    <label>URL Imagem</label>
                    <input type="text" name="imagem" value="<?php echo $maquina['imagem_url']; ?>" required>
                </div>
                <div class="input-group">
                    <label>Preço Diária</label>
                    <input type="text" name="preco" value="<?php echo $maquina['preco_diaria']; ?>" required>
                </div>
                <button type="submit" class="btn btn-laranja">Salvar Alterações</button>
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