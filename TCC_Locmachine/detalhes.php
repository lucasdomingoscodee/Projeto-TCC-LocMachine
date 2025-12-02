<?php
require 'conexao.php';

if (!isset($_GET['id'])) { header("Location: index.php"); exit; }
$id_maq = $_GET['id'];

$msg_review = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['avaliar'])) {
    if (!isset($_SESSION['usuario_id'])) {
        header("Location: login.php"); exit;
    }
    
    $nota = $_POST['nota'];
    $comentario = $_POST['comentario'];
    $hoje = date('Y-m-d');
    $id_user = $_SESSION['usuario_id'];

    $sql = "INSERT INTO avaliacoes (id_usuario, id_maquina, nota, comentario, data_avaliacao) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    if($stmt->execute([$id_user, $id_maq, $nota, $comentario, $hoje])) {
        $msg_review = "Avaliação enviada com sucesso!";
    }
}

$stmt = $pdo->prepare("SELECT * FROM maquinas WHERE id = ?");
$stmt->execute([$id_maq]);
$maquina = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$maquina) { echo "Máquina não encontrada."; exit; }

$sql_reviews = "SELECT a.*, u.nome as nome_user FROM avaliacoes a 
                JOIN usuarios u ON a.id_usuario = u.id 
                WHERE a.id_maquina = ? ORDER BY a.id DESC";
$stmt = $pdo->prepare($sql_reviews);
$stmt->execute([$id_maq]);
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

$media = 0;
$total_reviews = count($reviews);
if ($total_reviews > 0) {
    $soma = 0;
    foreach($reviews as $r) { $soma += $r['nota']; }
    $media = round($soma / $total_reviews, 1);
}

$fone_empresa = "NUMERO DA EMPRESA AQUI";
$msg_zap = urlencode("Olá! Tenho interesse na máquina: " . $maquina['nome']);
$link_zap = "https://wa.me/$fone_empresa?text=$msg_zap";
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title><?php echo $maquina['nome']; ?> - Detalhes</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="container nav-flex">
            <div class="logo"><span class="logo-box">L</span> LOCMACHINE</div>
            <div class="menu-icones"><a href="index.php">Voltar</a></div>
        </div>
    </header>

    <div class="container" style="margin-top: 40px;">
        
        <div class="detalhes-grid">
            <div class="img-box">
                <img src="<?php echo $maquina['imagem_url']; ?>" alt="Foto" class="img-detalhe">
            </div>
            
            <div class="info-box">
                <span class="categoria-tag"><?php echo $maquina['categoria']; ?></span>
                <h1><?php echo $maquina['nome']; ?></h1>
                
                <div class="rating-display">
                    <span style="color: gold; font-size: 1.2rem;">
                        <?php 
                        for($i=0; $i<5; $i++) {
                            echo ($i < round($media)) ? '<i class="fa fa-star"></i>' : '<i class="far fa-star"></i>';
                        } 
                        ?>
                    </span>
                    <span style="color:#666; margin-left:10px;">(<?php echo $media; ?>/5 - <?php echo $total_reviews; ?> avaliações)</span>
                </div>

                <p class="desc-longa"><?php echo $maquina['descricao']; ?></p>
                
                <h2 class="preco-destaque">R$ <?php echo number_format($maquina['preco_diaria'], 2, ',', '.'); ?> <small>/dia</small></h2>

                <div class="btn-group-vertical">
                    <a href="index.php?add_carrinho=<?php echo $maquina['id']; ?>" class="btn btn-laranja">
                        <i class="fa fa-cart-plus"></i> Adicionar ao Carrinho
                    </a>

                    <a href="<?php echo $link_zap; ?>" target="_blank" class="btn btn-verde">
                        <i class="fab fa-whatsapp"></i> Falar com Especialista
                    </a>
                </div>
            </div>
        </div>

        <div class="reviews-section">
            <h3>Avaliações de Clientes</h3>
            
            <div class="card form-review">
                <h4>Deixe sua opinião</h4>
                <?php if($msg_review) echo "<p class='alerta sucesso'>$msg_review</p>"; ?>
                
                <form method="POST">
                    <div class="input-group">
                        <label>Nota:</label>
                        <select name="nota" required style="max-width:150px;">
                            <option value="5">★★★★★ (5)</option>
                            <option value="4">★★★★☆ (4)</option>
                            <option value="3">★★★☆☆ (3)</option>
                            <option value="2">★★☆☆☆ (2)</option>
                            <option value="1">★☆☆☆☆ (1)</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <label>Seu Comentário:</label>
                        <textarea name="comentario" required placeholder="O que achou da máquina?"></textarea>
                    </div>
                    <button type="submit" name="avaliar" class="btn btn-outline" style="width:auto;">Enviar Avaliação</button>
                </form>
            </div>

            <div class="lista-reviews">
                <?php if(count($reviews) == 0): ?>
                    <p style="color:#777; font-style:italic;">Nenhuma avaliação ainda. Seja o primeiro!</p>
                <?php endif; ?>

                <?php foreach($reviews as $rev): ?>
                <div class="review-item">
                    <div class="review-header">
                        <strong><?php echo $rev['nome_user']; ?></strong>
                        <span class="stars-mini">
                            <?php for($k=0; $k<$rev['nota']; $k++) echo '★'; ?>
                        </span>
                    </div>
                    <p class="review-txt"><?php echo $rev['comentario']; ?></p>
                    <small class="review-date"><?php echo date('d/m/Y', strtotime($rev['data_avaliacao'])); ?></small>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>
</body>
</html>