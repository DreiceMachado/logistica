<?php
// evita que o texto digitado quebre o html
function limpar($texto)
{
    return htmlspecialchars($texto ?? '', ENT_QUOTES, 'UTF-8');
}

function dinheiro($valor)
{
    return 'R$ ' . number_format($valor, 2, ',', '.');
}

function dataBR($data)
{
    return date('d/m/Y', strtotime($data));
}

// busca os fretes com o nome do transportador, cidades e produtos
// se passar uma data, traz so os fretes daquele dia
function buscarFretes($pdo, $data = null)
{
    $sql = "SELECT f.id, f.data_frete, f.valor,
                   t.nome AS transportador,
                   CONCAT(o.cidade, '/', o.estado) AS origem,
                   CONCAT(d.cidade, '/', d.estado) AS destino,
                   GROUP_CONCAT(CONCAT(fp.quantidade, 'x ', p.nome) SEPARATOR ', ') AS produtos
            FROM fretes f
            JOIN transportadores t ON t.id = f.transportador_id
            JOIN enderecos o ON o.id = f.origem_id
            JOIN enderecos d ON d.id = f.destino_id
            LEFT JOIN frete_produtos fp ON fp.frete_id = f.id
            LEFT JOIN produtos p ON p.id = fp.produto_id";

    if ($data) {
        $sql .= " WHERE f.data_frete = ?";
    }

    $sql .= " GROUP BY f.id ORDER BY f.data_frete DESC, f.id DESC";

    $stmt = $pdo->prepare($sql);
    if ($data) {
        $stmt->execute([$data]);
    } else {
        $stmt->execute();
    }
    return $stmt->fetchAll();
}
