<?php
require 'config/conexao.php';

// escolher ou tirar um endereco do frete (o primeiro e a origem, o segundo o destino)
if (isset($_GET['usar']) && isset($_SESSION['frete'])) {
    $pos = array_search($_GET['usar'], $_SESSION['frete']['enderecos']);
    if ($pos === false) {
        if (count($_SESSION['frete']['enderecos']) < 2) {
            $_SESSION['frete']['enderecos'][] = $_GET['usar'];
        }
    } else {
        unset($_SESSION['frete']['enderecos'][$pos]);
        $_SESSION['frete']['enderecos'] = array_values($_SESSION['frete']['enderecos']);
    }
    header('Location: enderecos.php');
    exit;
}

// salvar (se tiver id edita, se nao tiver cadastra)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cep = $_POST['cep'];
    $rua = $_POST['rua'];
    $numero = $_POST['numero'];
    $bairro = $_POST['bairro'];
    $cidade = $_POST['cidade'];
    $estado = strtoupper($_POST['estado']);

    if ($_POST['id'] != '') {
        $stmt = $pdo->prepare('UPDATE enderecos SET cep = ?, rua = ?, numero = ?, bairro = ?, cidade = ?, estado = ? WHERE id = ?');
        $stmt->execute([$cep, $rua, $numero, $bairro, $cidade, $estado, $_POST['id']]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO enderecos (cep, rua, numero, bairro, cidade, estado) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$cep, $rua, $numero, $bairro, $cidade, $estado]);

        if (isset($_SESSION['frete']) && count($_SESSION['frete']['enderecos']) < 2) {
            $_SESSION['frete']['enderecos'][] = $pdo->lastInsertId();
        }
    }

    header('Location: enderecos.php?ok=1');
    exit;
}

// excluir
if (isset($_GET['excluir'])) {
    try {
        $stmt = $pdo->prepare('DELETE FROM enderecos WHERE id = ?');
        $stmt->execute([$_GET['excluir']]);
        header('Location: enderecos.php?ok=1');
        exit;
    } catch (PDOException $ex) {
        $erro = 'Não dá pra excluir esse endereço porque ele está em um frete.';
    }
}

$endereco = ['id' => '', 'cep' => '', 'rua' => '', 'numero' => '', 'bairro' => '', 'cidade' => '', 'estado' => ''];

if (isset($_GET['editar'])) {
    $stmt = $pdo->prepare('SELECT * FROM enderecos WHERE id = ?');
    $stmt->execute([$_GET['editar']]);
    $endereco = $stmt->fetch();
}

$lista = $pdo->query('SELECT * FROM enderecos ORDER BY cidade')->fetchAll();

$titulo = 'Endereços';
require 'includes/cabecalho.php';

if (isset($_SESSION['frete'])) {
    // monta o texto na ordem que foi escolhido
    $escolhidos = [];
    foreach ($_SESSION['frete']['enderecos'] as $id) {
        foreach ($lista as $item) {
            if ($item['id'] == $id) {
                $escolhidos[] = limpar($item['rua'] . ', ' . $item['numero'] . ' - ' . $item['cidade'] . '/' . $item['estado']);
            }
        }
    }

    $etapa = 2;
    if (count($escolhidos) == 0) {
        $textoAssistente = 'Escolha o endereço de <strong>origem</strong> (cadastre um novo ou clique em Escolher).';
    } elseif (count($escolhidos) == 1) {
        $textoAssistente = 'Origem: <strong>' . $escolhidos[0] . '</strong><br>Agora escolha o endereço de <strong>destino</strong>.';
    } else {
        $textoAssistente = 'Origem: <strong>' . $escolhidos[0] . '</strong><br>Destino: <strong>' . $escolhidos[1] . '</strong>';
    }
    $podeAvancar = count($escolhidos) == 2;
    $voltar = 'produtos.php';
    $proximo = 'transportadores.php';
    $textoBotao = 'Próximo';
    require 'includes/assistente.php';
}
?>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <?php if ($endereco['id'] != '') { ?>
                    Editar endereço
                <?php } else { ?>
                    Novo endereço
                <?php } ?>
            </div>
            <div class="card-body">
                <form method="post">
                    <input type="hidden" name="id" value="<?= $endereco['id'] ?>">

                    <div class="mb-3">
                        <label class="form-label">CEP</label>
                        <input type="text" name="cep" id="cep" class="form-control" maxlength="9" value="<?= limpar($endereco['cep']) ?>" required>
                        <small class="text-muted">Ao sair do campo o endereço é preenchido pelo ViaCEP</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rua</label>
                        <input type="text" name="rua" id="rua" class="form-control" value="<?= limpar($endereco['rua']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Número</label>
                        <input type="text" name="numero" id="numero" class="form-control" value="<?= limpar($endereco['numero']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bairro</label>
                        <input type="text" name="bairro" id="bairro" class="form-control" value="<?= limpar($endereco['bairro']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Cidade</label>
                        <input type="text" name="cidade" id="cidade" class="form-control" value="<?= limpar($endereco['cidade']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Estado (UF)</label>
                        <input type="text" name="estado" id="estado" class="form-control" maxlength="2" value="<?= limpar($endereco['estado']) ?>" required>
                    </div>

                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Salvar</button>
                    <a href="enderecos.php" class="btn btn-light">Cancelar</a>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Endereços cadastrados</div>
            <div class="card-body">
                <table class="table table-hover align-middle">
                    <tr>
                        <th>ID</th>
                        <th>Endereço</th>
                        <th>Cidade</th>
                        <th>CEP</th>
                        <th>Ações</th>
                    </tr>
                    <?php foreach ($lista as $item) { ?>
                        <tr>
                            <td><?= $item['id'] ?></td>
                            <td><?= limpar($item['rua']) ?>, <?= limpar($item['numero']) ?> - <?= limpar($item['bairro']) ?></td>
                            <td><?= limpar($item['cidade']) ?>/<?= limpar($item['estado']) ?></td>
                            <td><?= limpar($item['cep']) ?></td>
                            <td>
                                <?php if (isset($_SESSION['frete'])) { ?>
                                    <?php if (in_array($item['id'], $_SESSION['frete']['enderecos'])) { ?>
                                        <a href="enderecos.php?usar=<?= $item['id'] ?>" class="btn btn-sm btn-success">
                                            <i class="bi bi-check-lg"></i> <?= $_SESSION['frete']['enderecos'][0] == $item['id'] ? 'Origem' : 'Destino' ?>
                                        </a>
                                    <?php } elseif (count($_SESSION['frete']['enderecos']) < 2) { ?>
                                        <a href="enderecos.php?usar=<?= $item['id'] ?>" class="btn btn-sm btn-outline-success">Escolher</a>
                                    <?php } ?>
                                <?php } ?>
                                <a href="enderecos.php?editar=<?= $item['id'] ?>" class="btn btn-sm btn-outline-primary" title="Editar"><i class="bi bi-pencil"></i></a>
                                <a href="enderecos.php?excluir=<?= $item['id'] ?>" class="btn btn-sm btn-outline-danger" title="Excluir" onclick="return confirm('Deseja excluir?')"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>
                    <?php } ?>
                    <?php if (count($lista) == 0) { ?>
                        <tr><td colspan="5" class="text-center text-muted py-4">Nenhum endereço cadastrado.</td></tr>
                    <?php } ?>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require 'includes/rodape.php'; ?>
