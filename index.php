<?php
require 'config/conexao.php';

$qtdProdutos = $pdo->query('SELECT COUNT(*) FROM produtos')->fetchColumn();
$qtdEnderecos = $pdo->query('SELECT COUNT(*) FROM enderecos')->fetchColumn();
$qtdTransportadores = $pdo->query('SELECT COUNT(*) FROM transportadores')->fetchColumn();
$qtdFretes = $pdo->query('SELECT COUNT(*) FROM fretes')->fetchColumn();

// cancelar o passo a passo do frete
if (isset($_GET['cancelar'])) {
    unset($_SESSION['frete']);
    header('Location: index.php');
    exit;
}

$titulo = 'Início';
require 'includes/cabecalho.php';
?>

<p class="text-muted">Cadastre os produtos, endereços e transportadores e depois gere os fretes.</p>

<div class="row">
    <div class="col-md-3">
        <a href="produtos.php" class="text-decoration-none text-dark">
            <div class="card card-azul">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="icone"><i class="bi bi-box-seam"></i></div>
                    <div>
                        <h4 class="mb-0 fw-bold"><?= $qtdProdutos ?></h4>
                        <span>Produtos</span>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="enderecos.php" class="text-decoration-none text-dark">
            <div class="card card-verde">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="icone"><i class="bi bi-geo-alt"></i></div>
                    <div>
                        <h4 class="mb-0 fw-bold"><?= $qtdEnderecos ?></h4>
                        <span>Endereços</span>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="transportadores.php" class="text-decoration-none text-dark">
            <div class="card card-amarelo">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="icone"><i class="bi bi-truck"></i></div>
                    <div>
                        <h4 class="mb-0 fw-bold"><?= $qtdTransportadores ?></h4>
                        <span>Transportadores</span>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="fretes.php" class="text-decoration-none text-dark">
            <div class="card card-vermelho">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="icone"><i class="bi bi-receipt"></i></div>
                    <div>
                        <h4 class="mb-0 fw-bold"><?= $qtdFretes ?></h4>
                        <span>Fretes</span>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

<a href="produtos.php?iniciar=1" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Gerar frete</a>
<a href="relatorio.php" class="btn btn-dark"><i class="bi bi-file-earmark-text"></i> Relatório</a>

<?php require 'includes/rodape.php'; ?>
