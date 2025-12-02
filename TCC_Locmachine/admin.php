<?php
require 'conexao.php';

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    die("Acesso Negado.");
}

$msg = "";

if (isset($_GET['deletar'])) {
    $id_del = $_GET['deletar'];
    $pdo->prepare("DELETE FROM maquinas WHERE id = ?")->execute([$id_del]);
    header("Location: admin.php");
    exit;
}

if (isset($_POST['add_maquina'])) {
    $nome = $_POST['nome'];
    $cat = $_POST['categoria'];
    $desc = $_POST['descricao'];
    $img = $_POST['imagem'];
    $preco = $_POST['preco'];
    
    $sql = "INSERT INTO maquinas (nome, categoria, descricao, imagem_url, preco_diaria) VALUES (?, ?, ?, ?, ?)";
    $pdo->prepare($sql)->execute([$nome, $cat, $desc, $img, $preco]);
    $msg = "Máquina adicionada!";
}

if (isset($_POST['add_local'])) {
    $cidade = $_POST['cidade'];
    $estado = $_POST['estado'];
    $sql = "INSERT INTO localidades (cidade, estado) VALUES (?, ?)";
    $pdo->prepare($sql)->execute([$cidade, $estado]);
    $msg = "Localidade adicionada!";
}

$lista_maquinas = $pdo->query("SELECT * FROM maquinas ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel Admin</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="container nav-flex">
            <div class="logo"><span class="logo-box">ADM</span> Gerenciamento</div>
            <div class="menu-icones">
                <a href="index.php">Ir para o Site</a>
                <a href="login.php">Sair</a>
            </div>
        </div>
    </header>

    <div class="container" style="margin-top: 30px;">
        <h2 style="margin-bottom: 20px;">Painel de Controle</h2>
        <?php if($msg) echo "<p class='alerta sucesso'>$msg</p>"; ?>

        <div style="display: flex; gap: 20px; flex-wrap: wrap;">
            
            <div class="admin-section" style="flex: 2;">
                <h3><i class="fa fa-plus-circle"></i> Nova Máquina</h3>
                <form method="POST">
                    <div class="input-group"><label>Nome</label><input type="text" name="nome" required></div>
                    <div style="display:flex; gap:10px;">
                        <div class="input-group" style="flex:1"><label>Categoria</label><input type="text" name="categoria" required></div>
                        <div class="input-group" style="flex:1"><label>Preço</label><input type="number" step="0.01" name="preco" required></div>
                    </div>
                    <div class="input-group"><label>Descrição</label><textarea name="descricao" rows="2" required></textarea></div>
                    <div class="input-group"><label>URL Imagem</label><input type="text" name="imagem" required></div>
                    <button type="submit" name="add_maquina" class="btn btn-laranja">Cadastrar</button>
                </form>
            </div>

            <div class="admin-section" style="flex: 1;">
                <h3><i class="fa fa-map-marker-alt"></i> Nova Localidade</h3>
                <form method="POST">
                    <div class="input-group"><label>Cidade</label><input type="text" name="cidade" required></div>
                    <div class="input-group"><label>Estado</label><input type="text" name="estado" maxlength="2" required></div>
                    <button type="submit" name="add_local" class="btn btn-verde" style="width:100%">Salvar</button>
                </form>
            </div>
        </div>

        <h3 style="margin-top: 30px;">Gerenciar Máquinas Existentes</h3>
        <table class="tabela-estilizada">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Foto</th>
                    <th>Nome</th>
                    <th>Preço Diária</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($lista_maquinas as $item): ?>
                <tr>
                    <td>#<?php echo $item['id']; ?></td>
                    <td><img src="<?php echo $item['imagem_url']; ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;"></td>
                    <td><?php echo $item['nome']; ?></td>
                    <td>R$ <?php echo $item['preco_diaria']; ?></td>
                    <td>
                        <a href="editar_maquina.php?id=<?php echo $item['id']; ?>" class="btn" style="background:#ddd; padding:5px 10px; font-size:0.8rem;">
                            <i class="fa fa-pen"></i>
                        </a>
                        <a href="?deletar=<?php echo $item['id']; ?>" class="btn btn-vermelho" style="padding:5px 10px; font-size:0.8rem;" onclick="return confirm('Tem certeza que deseja apagar?');">
                            <i class="fa fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <br><br>
    </div>
</body>
</html>