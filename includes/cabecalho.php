<?php
// pega o nome da pagina atual para marcar no menu
$pagina = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $titulo ?> - Sistema de Logística</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="css/estilo.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light mb-4">
    <div class="container">
        <span class="navbar-brand fw-bold"><i class="bi bi-truck"></i> Sistema de Logística</span>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="menu">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link <?php if ($pagina == 'index.php') echo 'active'; ?>" href="index.php">Início</a></li>
                <li class="nav-item"><a class="nav-link <?php if ($pagina == 'produtos.php') echo 'active'; ?>" href="produtos.php">Produtos</a></li>
                <li class="nav-item"><a class="nav-link <?php if ($pagina == 'enderecos.php') echo 'active'; ?>" href="enderecos.php">Endereços</a></li>
                <li class="nav-item"><a class="nav-link <?php if ($pagina == 'transportadores.php') echo 'active'; ?>" href="transportadores.php">Transportadores</a></li>
                <li class="nav-item"><a class="nav-link <?php if ($pagina == 'fretes.php') echo 'active'; ?>" href="fretes.php">Fretes</a></li>
                <li class="nav-item"><a class="nav-link <?php if ($pagina == 'relatorio.php') echo 'active'; ?>" href="relatorio.php">Relatório</a></li>
            </ul>
        </div>
    </div>
</nav>
<div class="container">
    <h3 class="mb-4 fw-bold"><?= $titulo ?></h3>

    <?php if (!empty($erro)) { ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-circle"></i> <?= $erro ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php } ?>

    <?php if (isset($_GET['ok'])) { ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle"></i> Salvo com sucesso!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php } ?>
