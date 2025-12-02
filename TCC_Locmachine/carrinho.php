<?php
require 'conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_SESSION['carrinho'])) { $_SESSION['carrinho'] = []; }

$msg_sucesso = false;

if (isset($_GET['remover'])) {
    $id_rem = $_GET['remover'];
    if (($key = array_search($id_rem, $_SESSION['carrinho'])) !== false) {
        unset($_SESSION['carrinho'][$key]);
    }
    header("Location: carrinho.php");
    exit;
}

if (isset($_POST['finalizar']) && !empty($_SESSION['carrinho'])) {
    $id_user = $_SESSION['usuario_id'];
    
    $datas_inicio = $_POST['inicio'];
    $datas_fim = $_POST['fim'];
    $local_digitado = $_POST['endereco_entrega'];
    $pagamento = $_POST['pagamento'];

    foreach ($_SESSION['carrinho'] as $id_maq) {
        $inicio = $datas_inicio[$id_maq];
        $fim = $datas_fim[$id_maq];

        if(empty($inicio) || empty($fim)){
            $inicio = date('Y-m-d');
            $fim = date('Y-m-d', strtotime('+1 day'));
        }

        $d1 = new DateTime($inicio);
        $d2 = new DateTime($fim);
        $diff = $d1->diff($d2);
        $qtd_dias = $diff->days;
        
        if ($qtd_dias < 1 || $d1 > $d2) { $qtd_dias = 1; }

        $stmt = $pdo->prepare("SELECT preco_diaria FROM maquinas WHERE id = ?");
        $stmt->execute([$id_maq]);
        $maq = $stmt->fetch();
        
        $total_item = $maq['preco_diaria'] * $qtd_dias;

        $sql = "INSERT INTO alugueis (id_usuario, id_maquina, data_inicio, data_fim, valor_total, local_entrega, metodo_pagamento) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $pdo->prepare($sql)->execute([$id_user, $id_maq, $inicio, $fim, $total_item, $local_digitado, $pagamento]);
    }
    
    $_SESSION['carrinho'] = [];
    $msg_sucesso = true;
}

$itens = [];
if (!empty($_SESSION['carrinho'])) {
    $ids_str = implode(',', $_SESSION['carrinho']);
    $itens = $pdo->query("SELECT * FROM maquinas WHERE id IN ($ids_str)")->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Carrinho - LOCMACHINE</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="container nav-flex">
            <div class="logo"><span class="logo-box">L</span> LOCMACHINE</div>
            <div class="menu-icones">
                <?php if(!$msg_sucesso): ?>
                    <a href="index.php">Voltar</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <div class="container" style="margin-top: 40px;">
        
        <?php if($msg_sucesso): ?>
            <div class="card" style="text-align: center; padding: 50px;">
                <div style="font-size: 4rem; color: #28a745; margin-bottom: 20px;">
                    <i class="fa fa-check-circle"></i>
                </div>
                <h2 style="color: #28a745;">Aluguel Concluído com Sucesso!</h2>
                <p style="font-size: 1.1rem; color: #666; margin-top: 10px;">
                    Seu pedido foi processado. Você pode acompanhar o status no seu perfil.
                </p>
                <div style="margin-top: 30px;">
                    <a href="perfil.php" class="btn btn-laranja">Ver Histórico</a>
                    <a href="index.php" class="btn" style="border: 1px solid #ddd; margin-left: 10px;">Voltar à Home</a>
                </div>
            </div>

        <?php else: ?>

            <h2><i class="fa fa-shopping-cart"></i> Finalizar Aluguel</h2>
            
            <?php if(empty($itens)): ?>
                <div class="card" style="text-align: center; margin-top: 20px;">
                    <p>Seu carrinho está vazio.</p>
                    <a href="index.php" class="btn btn-laranja" style="margin-top: 15px;">Ver Máquinas</a>
                </div>
            <?php else: ?>
                
                <form method="POST">
                    <table class="tabela-estilizada">
                        <thead>
                            <tr>
                                <th>Máquina</th>
                                <th>Preço Diária</th>
                                <th>Período de Aluguel</th>
                                <th>Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($itens as $item): ?>
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 15px;">
                                        <img src="<?php echo $item['imagem_url']; ?>" style="width: 80px; height: 60px; object-fit: cover; border-radius: 5px;">
                                        <strong><?php echo $item['nome']; ?></strong>
                                    </div>
                                </td>
                                
                                <td style="color: var(--cor-laranja); font-weight: bold;">
                                    R$ <?php echo number_format($item['preco_diaria'], 2, ',', '.'); ?>
                                </td>
                                
                                <td>
                                    <div style="display: flex; gap: 10px; align-items: center;">
                                        <div>
                                            <small>De:</small>
                                            <input type="date" name="inicio[<?php echo $item['id']; ?>]" required 
                                                   class="input-data" value="<?php echo date('Y-m-d'); ?>">
                                        </div>
                                        <div>
                                            <small>Até:</small>
                                            <input type="date" name="fim[<?php echo $item['id']; ?>]" required 
                                                   class="input-data" value="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
                                        </div>
                                    </div>
                                </td>
                                
                                <td>
                                    <a href="?remover=<?php echo $item['id']; ?>" class="btn btn-vermelho" style="padding: 5px 10px;">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div class="card" style="margin-top: 30px; max-width: 600px; margin-left: auto;">
                        <h4 style="margin-bottom: 20px;">Detalhes da Entrega e Pagamento</h4>
                        
                        <div class="input-group">
                            <label><i class="fa fa-map-marker-alt"></i> Endereço Completo para Entrega</label>
                            <input type="text" name="endereco_entrega" required 
                                   placeholder="Rua, Número, Bairro, Cidade - Estado"
                                   style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px;">
                        </div>

                        <div class="input-group">
                            <label><i class="fa fa-credit-card"></i> Forma de Pagamento</label>
                            <select name="pagamento" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px;">
                                <option value="">Selecione...</option>
                                <option value="Cartão de Crédito">Cartão de Crédito</option>
                                <option value="Boleto Bancário">Boleto Bancário</option>
                                <option value="PIX">PIX (Aprovação Imediata)</option>
                                <option value="Faturamento (PJ)">Faturamento 28 dias (Apenas PJ)</option>
                            </select>
                        </div>

                        <div style="margin-top: 25px; text-align: right;">
                            <p style="font-size: 0.85rem; color: #666; margin-bottom: 15px;">
                                *O valor total será calculado com base nas datas selecionadas.
                            </p>
                            <button type="submit" name="finalizar" class="btn btn-laranja" style="width: 100%; font-size: 1.1rem;">
                                Confirmar e Alugar <i class="fa fa-check"></i>
                            </button>
                        </div>
                    </div>
                </form>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>