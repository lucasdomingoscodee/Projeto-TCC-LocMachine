<?php
require 'conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['usuario_id'];

if (isset($_GET['cancelar_id'])) {
    $id_aluguel = $_GET['cancelar_id'];
    
    $sql_delete = "DELETE FROM alugueis WHERE id = ? AND id_usuario = ?";
    $stmt_del = $pdo->prepare($sql_delete);
    
    if($stmt_del->execute([$id_aluguel, $id_user])) {
        header("Location: perfil.php");
        exit;
    }
}

$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$id_user]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

$sql = "SELECT a.*, m.nome as nome_maquina, m.imagem_url 
        FROM alugueis a 
        JOIN maquinas m ON a.id_maquina = m.id 
        WHERE a.id_usuario = ? 
        ORDER BY a.data_inicio DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_user]);
$historico = $stmt->fetchAll(PDO::FETCH_ASSOC);

$qtd_alugueis = count($historico);
$total_investido = 0;
foreach($historico as $h) {
    $total_investido += $h['valor_total'];
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Meu Perfil - LOCMACHINE</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <header>
        <div class="container nav-flex">
            <div class="logo"><span class="logo-box">L</span> LOCMACHINE</div>
            <div class="menu-icones">
                <?php if(isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
                    <a href="admin.php" style="color: red; font-weight: bold; font-size: 0.9rem;">ADMIN</a>
                <?php endif; ?>

                <a href="index.php"><i class="fa fa-home"></i></a>
                <a href="carrinho.php"><i class="fa fa-shopping-cart"></i></a>
                <a href="perfil.php"><i class="fa fa-user" style="color: var(--cor-laranja);"></i></a>
                <a href="login.php" style="font-size: 0.9rem; margin-left: 10px;">Sair</a>
            </div>
        </div>
    </header>

    <div class="container">
        <div style="margin-top: 40px;">
            <h2>Meu Perfil</h2>
            <p style="color: #666;">Gerencie suas informações e histórico</p>
        </div>

        <div class="perfil-grid">
            <div class="card perfil-sidebar">
                <div class="avatar"><i class="fa fa-user"></i></div>
                <h3><?php echo $usuario['nome']; ?></h3>
                <span class="tipo-cliente"><?php echo $usuario['tipo_cliente']; ?></span>
                
                <div class="dados-contato">
                    <p><i class="fa fa-envelope"></i> <?php echo $usuario['email']; ?></p>
                    <p><i class="fa fa-phone"></i> <?php echo $usuario['telefone']; ?></p>
                    <p><i class="fa fa-map-marker-alt"></i> <?php echo $usuario['endereco']; ?></p>
                </div>
                
                <a href="editar_perfil.php" class="btn btn-laranja" style="width: 100%; margin-top: 20px; display:block;"> Editar Perfil </a>
            </div>

            <div>
                <div class="card">
                    <h4 style="margin-bottom: 20px;"><i class="fa fa-calendar"></i> Histórico de Aluguéis</h4>
                    
                    <?php if(count($historico) == 0): ?>
                        <p style="text-align:center; padding: 20px; color:#999;">Você ainda não fez nenhum aluguel.</p>
                        <center><a href="index.php" class="btn btn-laranja" style="width:auto;">Ver Máquinas</a></center>
                    <?php endif; ?>

                    <?php foreach($historico as $item): ?>
                    <div class="historico-item">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <img src="<?php echo $item['imagem_url']; ?>" style="width: 60px; height: 60px; object-fit: cover; border-radius: 5px;">
                            
                            <div>
                                <h4><?php echo $item['nome_maquina']; ?></h4>
                                <span style="font-size: 0.85rem; color: #777;">
                                    <?php echo date('d/m/Y', strtotime($item['data_inicio'])); ?> até 
                                    <?php echo date('d/m/Y', strtotime($item['data_fim'])); ?>
                                </span>
                                <?php if(!empty($item['local_entrega'])): ?>
                                    <br><span style="font-size: 0.8rem; color: #555;"><i class="fa fa-map-pin"></i> <?php echo $item['local_entrega']; ?></span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div style="text-align: right; display:flex; flex-direction:column; align-items:flex-end; gap:5px;">
                            <span class="tag-status" style="margin-bottom:5px;"><?php echo isset($item['status']) ? $item['status'] : 'Confirmado'; ?></span>
                            <span style="font-weight: bold; font-size:1.1rem; color: var(--cor-laranja);">
                                R$ <?php echo number_format($item['valor_total'], 2, ',', '.'); ?>
                            </span>
                            
                            <a href="?cancelar_id=<?php echo $item['id']; ?>" 
                               class="btn btn-vermelho" 
                               style="padding: 5px 10px; font-size: 0.75rem; margin-top: 5px;"
                               onclick="return confirm('Tem certeza que deseja cancelar/apagar este histórico?');">
                                <i class="fa fa-trash"></i> Cancelar
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="stats-box">
                    <div class="stat-card">
                        <span class="stat-valor"><?php echo $qtd_alugueis; ?></span>
                        <span style="color:#666; font-size:0.9rem">Aluguéis</span>
                    </div>
                    <div class="stat-card">
                        <span class="stat-valor">--</span> 
                        <span style="color:#666; font-size:0.9rem">Dias Total</span>
                    </div>
                    <div class="stat-card">
                        <span class="stat-valor">R$ <?php echo number_format($total_investido, 2, ',', '.'); ?></span>
                        <span style="color:#666; font-size:0.9rem">Investido</span>
                    </div>
                </div>
            </div>
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