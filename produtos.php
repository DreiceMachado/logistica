<?php
require 'config/conexao.php';

// comeca o passo a passo do frete (botao gerar frete do inicio)
if (isset($_GET['iniciar'])) {
    $_SESSION['frete'] = ['produtos' => [], 'enderecos' => [], 'transportador' => ''];
    header('Location: produtos.php');
    exit;
}

// escolher ou tirar um produto do frete
if (isset($_GET['usar']) && isset($_SESSION['frete'])) {
    $pos = array_search($_GET['usar'], $_SESSION['frete']['produtos']);
    if ($pos === false) {
        $_SESSION['frete']['produtos'][] = $_GET['usar'];
    } else {
        unset($_SESSION['frete']['produtos'][$pos]);
        $_SESSION['frete']['produtos'] = array_values($_SESSION['frete']['produtos']);
    }
    header('Location: produtos.php');
    exit;
}

// salvar (se tiver id edita, se nao tiver cadastra)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $peso = $_POST['peso'];
    $valor = $_POST['valor'];

    if ($_POST['id'] != '') {
        $stmt = $pdo->prepare('UPDATE produtos SET nome = ?, descricao = ?, peso = ?, valor = ? WHERE id = ?');
        $stmt->execute([$nome, $descricao, $peso, $valor, $_POST['id']]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO produtos (nome, descricao, peso, valor) VALUES (?, ?, ?, ?)');
        $stmt->execute([$nome, $descricao, $peso, $valor]);

        // se estiver gerando frete, o produto novo ja fica escolhido
        if (isset($_SESSION['frete'])) {
            $_SESSION['frete']['produtos'][] = $pdo->lastInsertId();
        }
    }

    header('Location: produtos.php?ok=1');
    exit;
}

// excluir
if (isset($_GET['excluir'])) {
    try {
        $stmt = $pdo->prepare('DELETE FROM produtos WHERE id = ?');
        $stmt->execute([$_GET['excluir']]);
        header('Location: produtos.php?ok=1');
        exit;
    } catch (PDOException $ex) {
        $erro = 'Não dá pra excluir esse produto porque ele está em um frete.';
    }
}

$produto = ['id' => '', 'nome' => '', 'descricao' => '', 'peso' => '', 'valor' => ''];

if (isset($_GET['editar'])) {
    $stmt = $pdo->prepare('SELECT * FROM produtos WHERE id = ?');
    $stmt->execute([$_GET['editar']]);
    $produto = $stmt->fetch();
}

$lista = $pdo->query('SELECT * FROM produtos ORDER BY nome')->fetchAll();

$titulo = 'Produtos';
require 'includes/cabecalho.php';

if (isset($_SESSION['frete'])) {
    $escolhidos = [];
    foreach ($lista as $item) {
        if (in_array($item['id'], $_SESSION['frete']['produtos'])) {
            $escolhidos[] = limpar($item['nome']);
        }
    }

    $etapa = 1;
    if (count($escolhidos) > 0) {
        $textoAssistente = 'Produtos escolhidos: <strong>' . implode(', ', $escolhidos) . '</strong>';
    } else {
        $textoAssistente = 'Cadastre um produto ou clique em <strong>Escolher</strong> nos que já existem.';
    }
    $podeAvancar = count($escolhidos) > 0;
    $voltar = '';
    $proximo = 'enderecos.php';
    $textoBotao = 'Próximo';
    require 'includes/assistente.php';
}
?>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <?php if ($produto['id'] != '') { ?>
                    Editar produto
                <?php } else { ?>
                    Novo produto
                <?php } ?>
            </div>
            <div class="card-body">
                <form method="post">
                    <input type="hidden" name="id" value="<?= $produto['id'] ?>">

                    <div class="mb-3">
                        <label class="form-label">Nome</label>
                        <input type="text" name="nome" class="form-control" value="<?= limpar($produto['nome']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descrição</label>
                        <input type="text" name="descricao" class="form-control" value="<?= limpar($produto['descricao']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Peso (kg)</label>
                        <input type="number" step="0.01" name="peso" class="form-control" value="<?= $produto['peso'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Valor (R$)</label>
                        <input type="number" step="0.01" name="valor" class="form-control" value="<?= $produto['valor'] ?>" required>
                    </div>

                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Salvar</button>
                    <a href="produtos.php" class="btn btn-light">Cancelar</a>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Produtos cadastrados</div>
            <div class="card-body">
                <table class="table table-hover align-middle">
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Descrição</th>
                        <th>Peso</th>
                        <th>Valor</th>
                        <th>Ações</th>
                    </tr>
                    <?php foreach ($lista as $item) { ?>
                        <tr>
                            <td><?= $item['id'] ?></td>
                            <td><?= limpar($item['nome']) ?></td>
                            <td><?= limpar($item['descricao']) ?></td>
                            <td><?= $item['peso'] ?> kg</td>
                            <td><?= dinheiro($item['valor']) ?></td>
                            <td>
                                <?php if (isset($_SESSION['frete'])) { ?>
                                    <?php if (in_array($item['id'], $_SESSION['frete']['produtos'])) { ?>
                                        <a href="produtos.php?usar=<?= $item['id'] ?>" class="btn btn-sm btn-success"><i class="bi bi-check-lg"></i> Escolhido</a>
                                    <?php } else { ?>
                                        <a href="produtos.php?usar=<?= $item['id'] ?>" class="btn btn-sm btn-outline-success">Escolher</a>
                                    <?php } ?>
                                <?php } ?>
                                <a href="produtos.php?editar=<?= $item['id'] ?>" class="btn btn-sm btn-outline-primary" title="Editar"><i class="bi bi-pencil"></i></a>
                                <a href="produtos.php?excluir=<?= $item['id'] ?>" class="btn btn-sm btn-outline-danger" title="Excluir" onclick="return confirm('Deseja excluir?')"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>
                    <?php } ?>
                    <?php if (count($lista) == 0) { ?>
                        <tr><td colspan="6" class="text-center text-muted py-4">Nenhum produto cadastrado.</td></tr>
                    <?php } ?>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require 'includes/rodape.php'; ?>
