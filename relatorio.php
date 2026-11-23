<?php
require 'config/conexao.php';

// se nao escolher data usa a de hoje
if (isset($_GET['data'])) {
    $data = $_GET['data'];
} else {
    $data = date('Y-m-d');
}

$fretes = buscarFretes($pdo, $data);

// gera o arquivo csv para baixar (abre no excel)
if (isset($_GET['baixar'])) {
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="relatorio_fretes_' . $data . '.csv"');

    $arquivo = fopen('php://output', 'w');
    fwrite($arquivo, "\xEF\xBB\xBF"); // para o excel reconhecer os acentos
    fputcsv($arquivo, ['Frete', 'Data', 'Transportador', 'Origem', 'Destino', 'Produtos', 'Valor'], ';');

    $total = 0;
    foreach ($fretes as $f) {
        fputcsv($arquivo, [
            $f['id'],
            dataBR($f['data_frete']),
            $f['transportador'],
            $f['origem'],
            $f['destino'],
            $f['produtos'],
            number_format($f['valor'], 2, ',', '.'),
        ], ';');
        $total = $total + $f['valor'];
    }

    fputcsv($arquivo, ['', '', '', '', '', 'Total', number_format($total, 2, ',', '.')], ';');
    fclose($arquivo);
    exit;
}

$total = 0;
foreach ($fretes as $f) {
    $total = $total + $f['valor'];
}

$titulo = 'Relatório';
require 'includes/cabecalho.php';
?>

<div class="card">
    <div class="card-body">
        <form method="get" class="d-flex flex-wrap align-items-center gap-2">
            <label class="fw-semibold me-1">Data:</label>
            <input type="date" name="data" class="form-control w-auto" value="<?= limpar($data) ?>">
            <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Buscar</button>
            <button type="submit" name="baixar" value="1" class="btn btn-success"><i class="bi bi-download"></i> Baixar relatório</button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">Fretes do dia <?= dataBR($data) ?></div>
    <div class="card-body">
        <?php if (count($fretes) == 0) { ?>
            <p class="text-center text-muted py-4 mb-0">Nenhum frete nesse dia.</p>
        <?php } else { ?>
            <table class="table table-hover align-middle">
                <tr>
                    <th>Nº</th>
                    <th>Transportador</th>
                    <th>Origem</th>
                    <th>Destino</th>
                    <th>Produtos</th>
                    <th>Valor</th>
                </tr>
                <?php foreach ($fretes as $f) { ?>
                    <tr>
                        <td><?= $f['id'] ?></td>
                        <td><?= limpar($f['transportador']) ?></td>
                        <td><?= limpar($f['origem']) ?></td>
                        <td><?= limpar($f['destino']) ?></td>
                        <td><?= limpar($f['produtos']) ?></td>
                        <td><?= dinheiro($f['valor']) ?></td>
                    </tr>
                <?php } ?>
                <tr>
                    <td colspan="5"><strong>Total</strong></td>
                    <td><strong><?= dinheiro($total) ?></strong></td>
                </tr>
            </table>
        <?php } ?>
    </div>
</div>

<?php require 'includes/rodape.php'; ?>
