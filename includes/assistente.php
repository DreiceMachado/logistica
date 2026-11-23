<?php
// barra do passo a passo do frete
// cada pagina define $etapa, $textoAssistente, $podeAvancar, $voltar, $proximo e $textoBotao
$etapas = [1 => 'Produtos', 2 => 'Endereços', 3 => 'Transportador', 4 => 'Frete'];
?>
<div class="card assistente">
    <div class="card-body">
        <div class="d-flex flex-wrap gap-2 mb-3">
            <?php foreach ($etapas as $numero => $nome) { ?>
                <?php if ($numero == $etapa) { ?>
                    <span class="etapa atual"><?= $numero ?>. <?= $nome ?></span>
                <?php } elseif ($numero < $etapa) { ?>
                    <span class="etapa feita"><i class="bi bi-check-lg"></i> <?= $nome ?></span>
                <?php } else { ?>
                    <span class="etapa"><?= $numero ?>. <?= $nome ?></span>
                <?php } ?>
            <?php } ?>
        </div>

        <p class="mb-3"><?= $textoAssistente ?></p>

        <div class="d-flex flex-wrap gap-2">
            <?php if ($voltar != '') { ?>
                <a href="<?= $voltar ?>" class="btn btn-light"><i class="bi bi-arrow-left"></i> Voltar</a>
            <?php } ?>

            <?php if ($proximo != '') { ?>
                <?php if ($podeAvancar) { ?>
                    <a href="<?= $proximo ?>" class="btn btn-primary"><?= $textoBotao ?> <i class="bi bi-arrow-right"></i></a>
                <?php } else { ?>
                    <button class="btn btn-primary" disabled><?= $textoBotao ?> <i class="bi bi-arrow-right"></i></button>
                <?php } ?>
            <?php } ?>

            <a href="index.php?cancelar=1" class="btn btn-outline-danger ms-auto">Cancelar frete</a>
        </div>
    </div>
</div>
