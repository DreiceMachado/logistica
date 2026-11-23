<?php
require 'config/conexao.php';

// salvar (se tiver id edita, se nao tiver cadastra)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $cnpj = $_POST['cnpj'];
    $telefone = $_POST['telefone'];
    $veiculo = $_POST['veiculo'];
    $placa = strtoupper($_POST['placa']);

    if ($_POST['id'] != '') {
        $stmt = $pdo->prepare('UPDATE transportadores SET nome = ?, cnpj = ?, telefone = ?, veiculo = ?, placa = ? WHERE id = ?');
        $stmt->execute([$nome, $cnpj, $telefone, $veiculo, $placa, $_POST['id']]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO transportadores (nome, cnpj, telefone, veiculo, placa) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$nome, $cnpj, $telefone, $veiculo, $placa]);
    }

    header('Location: transportadores.php?ok=1');
    exit;
}

// excluir
if (isset($_GET['excluir'])) {
    try {
        $stmt = $pdo->prepare('DELETE FROM transportadores WHERE id = ?');
        $stmt->execute([$_GET['excluir']]);
        header('Location: transportadores.php?ok=1');
        exit;
    } catch (PDOException $ex) {
        $erro = 'Não dá pra excluir esse transportador porque ele está em um frete.';
    }
}

$transportador = ['id' => '', 'nome' => '', 'cnpj' => '', 'telefone' => '', 'veiculo' => '', 'placa' => ''];

if (isset($_GET['editar'])) {
    $stmt = $pdo->prepare('SELECT * FROM transportadores WHERE id = ?');
    $stmt->execute([$_GET['editar']]);
    $transportador = $stmt->fetch();
}

$lista = $pdo->query('SELECT * FROM transportadores ORDER BY nome')->fetchAll();
$veiculos = ['Moto', 'Carro', 'Van', 'Caminhão'];

$titulo = 'Transportadores';
require 'includes/cabecalho.php';
?>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <?php if ($transportador['id'] != '') { ?>
                    Editar transportador
                <?php } else { ?>
                    Novo transportador
                <?php } ?>
            </div>
            <div class="card-body">
                <form method="post">
                    <input type="hidden" name="id" value="<?= $transportador['id'] ?>">

                    <div class="mb-3">
                        <label class="form-label">Nome</label>
                        <input type="text" name="nome" class="form-control" value="<?= limpar($transportador['nome']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">CNPJ</label>
                        <input type="text" name="cnpj" class="form-control" maxlength="18" value="<?= limpar($transportador['cnpj']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Telefone</label>
                        <input type="text" name="telefone" class="form-control" value="<?= limpar($transportador['telefone']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Veículo</label>
                        <select name="veiculo" class="form-select">
                            <?php foreach ($veiculos as $v) { ?>
                                <option <?php if ($transportador['veiculo'] == $v) echo 'selected'; ?>><?= $v ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Placa</label>
                        <input type="text" name="placa" class="form-control" maxlength="8" value="<?= limpar($transportador['placa']) ?>" required>
                    </div>

                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Salvar</button>
                    <a href="transportadores.php" class="btn btn-light">Cancelar</a>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Transportadores cadastrados</div>
            <div class="card-body">
                <table class="table table-hover align-middle">
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>CNPJ</th>
                        <th>Telefone</th>
                        <th>Veículo</th>
                        <th>Ações</th>
                    </tr>
                    <?php foreach ($lista as $item) { ?>
                        <tr>
                            <td><?= $item['id'] ?></td>
                            <td><?= limpar($item['nome']) ?></td>
                            <td><?= limpar($item['cnpj']) ?></td>
                            <td><?= limpar($item['telefone']) ?></td>
                            <td><?= limpar($item['veiculo']) ?> (<?= limpar($item['placa']) ?>)</td>
                            <td>
                                <a href="transportadores.php?editar=<?= $item['id'] ?>" class="btn btn-sm btn-outline-primary" title="Editar"><i class="bi bi-pencil"></i></a>
                                <a href="transportadores.php?excluir=<?= $item['id'] ?>" class="btn btn-sm btn-outline-danger" title="Excluir" onclick="return confirm('Deseja excluir?')"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>
                    <?php } ?>
                    <?php if (count($lista) == 0) { ?>
                        <tr><td colspan="6" class="text-center text-muted py-4">Nenhum transportador cadastrado.</td></tr>
                    <?php } ?>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require 'includes/rodape.php'; ?>
