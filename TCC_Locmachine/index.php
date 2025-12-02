<?php
require 'conexao.php';

if (!isset($_SESSION['usuario_id'])) { header("Location: login.php"); exit; }

if (!isset($_SESSION['carrinho'])) { $_SESSION['carrinho'] = []; }

if (isset($_GET['add_carrinho'])) {
    $id_add = $_GET['add_carrinho'];
    if(!in_array($id_add, $_SESSION['carrinho'])){
        $_SESSION['carrinho'][] = $id_add;
    }
    header("Location: index.php?status=adicionado");
    exit;
}

if (isset($_GET['comprar_agora'])) {
    $id_buy = $_GET['comprar_agora'];
    if(!in_array($id_buy, $_SESSION['carrinho'])){
        $_SESSION['carrinho'][] = $id_buy;
    }
    header("Location: carrinho.php");
    exit;
}

$termo_busca = "";
$sql_busca = "SELECT * FROM maquinas";

if (isset($_GET['busca']) && !empty($_GET['busca'])) {
    $termo_busca = $_GET['busca'];
    $sql_busca = "SELECT * FROM maquinas WHERE nome LIKE :termo";
}

if ($termo_busca) {
    $stmt = $pdo->prepare($sql_busca);
    $stmt->execute(['termo' => "%$termo_busca%"]);
    $maquinas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $maquinas = $pdo->query($sql_busca)->fetchAll(PDO::FETCH_ASSOC);
}

$qtd_carrinho = count($_SESSION['carrinho']);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Home - LOCMACHINE</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <header>
        <div class="container nav-flex">
            <div class="logo">
                <span class="logo-box">L</span> LOCMACHINE
            </div>
            <div class="menu-icones">
                <?php if(isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
                    <a href="admin.php" style="color: red;">ADMIN</a>
                <?php endif; ?>

                <a href="index.php">Home</a>
                <a href="perfil.php">Histórico</a>
                
                <a href="carrinho.php" style="margin-left: 20px;">
                    <i class="fa fa-shopping-cart"></i>
                    <?php if($qtd_carrinho > 0): ?>
                        <span class="badge-cart"><?php echo $qtd_carrinho; ?></span>
                    <?php endif; ?>
                </a>
                
                <a href="perfil.php"><i class="fa fa-user-circle"></i></a>
            </div>
        </div>
    </header>

    <div class="hero-section">
        <div class="container reveal">
            <h1 style="font-size: 3rem; margin-bottom: 15px;">Potência para sua Obra</h1>
            
            <form method="GET" class="form-busca-home">
                <input type="text" name="busca" class="input-busca-moderno" 
                       placeholder="O que você procura? (Ex: Trator)" 
                       value="<?php echo htmlspecialchars($termo_busca); ?>">
                <button type="submit" class="btn btn-laranja" style="border-radius: 50px; padding: 10px 30px;">
                    <i class="fa fa-search"></i>
                </button>
            </form>

            <?php if($termo_busca): ?>
                <p style="margin-top:10px;">Exibindo resultados para: <strong><?php echo $termo_busca; ?></strong> 
                <a href="index.php" style="color:#ffae42; margin-left:5px;">(Limpar Filtros)</a></p>
            <?php else: ?>
                <p style="font-size: 1.2rem; opacity: 0.8; max-width: 600px; margin: 20px auto 0;">
                    A frota mais moderna de máquinas pesadas ao seu alcance.
                </p>
            <?php endif; ?>
        </div>
    </div>

    <div class="container" id="catalogo">
        <div class="reveal delay-1" style="margin-top: 60px; text-align: center;">
            <h3 style="font-size: 2rem;">Nossa Frota Disponível</h3>
            <p style="color:#777;">Clique na máquina para ver detalhes e avaliações</p>
        </div>

        <div class="grid-maquinas">
            <?php if(count($maquinas) == 0): ?>
                <div class="card" style="width: 100%; text-align:center; padding: 40px;">
                    <h3>Nenhuma máquina encontrada.</h3>
                    <a href="index.php" class="btn btn-laranja" style="margin-top:10px;">Ver Todas</a>
                </div>
            <?php endif; ?>

            <?php foreach($maquinas as $index => $item): 
                $delay_class = ($index % 3 == 0) ? 'reveal' : (($index % 3 == 1) ? 'reveal delay-1' : 'reveal delay-2');
            ?>
            <div class="maquina-card <?php echo $delay_class; ?>">
                <div style="overflow: hidden;">
                    <a href="detalhes.php?id=<?php echo $item['id']; ?>">
                        <img src="<?php echo $item['imagem_url']; ?>" alt="Foto">
                    </a>
                </div>
                <div class="maquina-info">
                    <a href="detalhes.php?id=<?php echo $item['id']; ?>">
                        <h4 style="font-size: 1.2rem;"><?php echo $item['nome']; ?></h4>
                    </a>
                    
                    <p style="font-size: 0.9rem; color: #666; margin: 10px 0; min-height: 40px;">
                        <?php echo substr($item['descricao'], 0, 80) . '...'; ?>
                    </p>
                    <span class="preco">R$ <?php echo number_format($item['preco_diaria'], 2, ',', '.'); ?> <small style="color:#888; font-weight:normal;">/dia</small></span>
                    
                    <div class="btn-group">
                        <a href="?add_carrinho=<?php echo $item['id']; ?>" class="btn btn-outline">
                            <i class="fa fa-cart-plus"></i> Add
                        </a>
                        
                        <a href="?comprar_agora=<?php echo $item['id']; ?>" class="btn btn-laranja">
                            Comprar
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
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

    <div class="toast-container">
        <div id="toast" class="toast">
            <i class="fa fa-check-circle" style="color: #4caf50; font-size: 1.5rem;"></i>
            <div>
                <h4 style="margin:0; font-size: 1rem;">Sucesso!</h4>
                <p style="margin:0; font-size: 0.9rem; color: #ccc;">Máquina adicionada ao carrinho.</p>
            </div>
        </div>
    </div>

    <script>
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('status') === 'adicionado') {
            const toast = document.getElementById('toast');
            toast.classList.add('show');
            
            window.history.replaceState(null, null, window.location.pathname);

            setTimeout(() => {
                toast.classList.remove('show');
            }, 3000);
        }

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animationPlayState = 'running';
                    entry.target.classList.add('visible');
                }
            });
        });

        document.querySelectorAll('.reveal').forEach((el) => {
            observer.observe(el);
        });
    </script>

</body>
</html>