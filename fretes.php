<?php
require 'config/conexao.php';

// gerar o frete
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $produtos = $_POST['produtos'];
    $quantidades = $_POST['quantidades'];

    if ($_POST['origem_id'] == $_POST['destino_id']) {
        $erro = 'A origem e o destino não podem ser iguais.';
    } else {
        // primeiro salva o frete
        $stmt = $pdo->prepare('INSERT INTO fretes (transportador_id, origem_id, destino_id, data_frete, valor) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$_POST['transportador_id'], $_POST['origem_id'], $_POST['destino_id'], $_POST['data_frete'], $_POST['valor']]);
        $freteId = $pdo->lastInsertId();

        // depois salva cada produto ligado ao frete
        $stmt = $pdo->prepare('INSERT INTO frete_produtos (frete_id, produto_id, quantidade) VALUES (?, ?, ?)');
        for ($i = 0; $i < count($produtos); $i++) {
            $stmt->execute([$freteId, $produtos[$i], $quantidades[$i]]);
        }

        header('Location: fretes.php?ok=1');
        exit;
    }
}

// excluir
if (isset($_GET['excluir'])) {
    $stmt = $pdo->prepare('DELETE FROM fretes WHERE id = ?');
    $stmt->execute([$_GET['excluir']]);
    header('Location: fretes.php?ok=1');
    exit;
}

$transportadores = $pdo->query('SELECT * FROM transportadores ORDER BY nome')->fetchAll();
$enderecos = $pdo->query('SELECT * FROM enderecos ORDER BY cidade')->fetchAll();
$listaProdutos = $pdo->query('SELECT * FROM produtos ORDER BY nome')->fetchAll();
$fretes = buscarFretes($pdo);

$titulo = 'Fretes';
require 'includes/cabecalho.php';
?>

<?php if (count($transportadores) == 0 || count($enderecos) < 2 || count($listaProdutos) == 0) { ?>
    <div class="alert alert-warning">
        Para gerar um frete precisa ter pelo menos 1 transportador, 2 endereços (origem e destino) e 1 produto cadastrados.<br>
        Hoje tem: <?= count($transportadores) ?> transportador(es), <?= count($enderecos) ?> endereço(s) e <?= count($listaProdutos) ?> produto(s).
        <?php if (count($enderecos) < 2) { ?>
            <a href="enderecos.php">Cadastrar endereço</a>
        <?php } ?>
    </div>
<?php } else { ?>
    <div class="card">
        <div class="card-header">Novo frete</div>
        <div class="card-body">
            <form method="post">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Transportador</label>
                        <select name="transportador_id" class="form-select">
                            <?php foreach ($transportadores as $t) { ?>
                                <option value="<?= $t['id'] ?>"><?= limpar($t['nome']) ?> (<?= limpar($t['veiculo']) ?>)</option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Origem</label>
                        <select name="origem_id" class="form-select">
                            <?php foreach ($enderecos as $end) { ?>
                                <option value="<?= $end['id'] ?>"><?= limpar($end['rua']) ?>, <?= limpar($end['numero']) ?> - <?= limpar($end['cidade']) ?>/<?= limpar($end['estado']) ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Destino</label>
                        <select name="destino_id" class="form-select">
                            <?php foreach ($enderecos as $end) { ?>
                                <option value="<?= $end['id'] ?>"><?= limpar($end['rua']) ?>, <?= limpar($end['numero']) ?> - <?= limpar($end['cidade']) ?>/<?= limpar($end['estado']) ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Data</label>
                        <input type="date" name="data_frete" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Valor do frete (R$)</label>
                        <input type="number" step="0.01" name="valor" class="form-control" required>
                    </div>
                </div>

                <h6 class="fw-bold mt-2">Produtos do frete</h6>
                <div id="lista-produtos">
                    <div class="row linha-produto">
                        <div class="col-7">
                            <select name="produtos[]" class="form-select">
                                <?php foreach ($listaProdutos as $p) { ?>
                                    <option value="<?= $p['id'] ?>"><?= limpar($p['nome']) ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-3">
                            <input type="number" name="quantidades[]" class="form-control" min="1" value="1">
                        </div>
                        <div class="col-2">
                            <button type="button" class="btn btn-outline-danger" title="Remover" onclick="removerProduto(this)"><i class="bi bi-x-lg"></i></button>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-outline-success btn-sm mb-4" onclick="adicionarProduto()"><i class="bi bi-plus-lg"></i> Adicionar produto</button>

                <br>
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Gerar frete</button>
            </form>
        </div>
    </div>
<?php } ?>

<div class="card">
    <div class="card-header">Fretes gerados</div>
    <div class="card-body">
        <table class="table table-hover align-middle">
            <tr>
                <th>Nº</th>
                <th>Data</th>
                <th>Transportador</th>
                <th>Origem</th>
                <th>Destino</th>
                <th>Produtos</th>
                <th>Valor</th>
                <th>Ações</th>
            </tr>
            <?php foreach ($fretes as $f) { ?>
                <tr>
                    <td><?= $f['id'] ?></td>
                    <td><?= dataBR($f['data_frete']) ?></td>
                    <td><?= limpar($f['transportador']) ?></td>
                    <td><?= limpar($f['origem']) ?></td>
                    <td><?= limpar($f['destino']) ?></td>
                    <td><?= limpar($f['produtos']) ?></td>
                    <td class="fw-semibold"><?= dinheiro($f['valor']) ?></td>
                    <td>
                        <a href="fretes.php?excluir=<?= $f['id'] ?>" class="btn btn-sm btn-outline-danger" title="Excluir" onclick="return confirm('Deseja excluir?')"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
            <?php } ?>
            <?php if (count($fretes) == 0) { ?>
                <tr><td colspan="8" class="text-center text-muted py-4">Nenhum frete gerado.</td></tr>
            <?php } ?>
        </table>
    </div>
</div>

<?php require 'includes/rodape.php'; ?>
