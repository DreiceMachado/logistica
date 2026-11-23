// adiciona mais uma linha de produto no frete (copia a primeira linha)
function adicionarProduto() {
    var lista = document.getElementById('lista-produtos');
    var primeira = lista.querySelector('.linha-produto');
    var nova = primeira.cloneNode(true);
    nova.querySelector('input').value = 1;
    lista.appendChild(nova);
}

// remove a linha, mas deixa pelo menos uma
function removerProduto(botao) {
    var linhas = document.querySelectorAll('.linha-produto');
    if (linhas.length > 1) {
        botao.closest('.linha-produto').remove();
    } else {
        alert('O frete precisa ter pelo menos um produto.');
    }
}

// busca o endereco pelo cep usando a api do viacep
var campoCep = document.getElementById('cep');

if (campoCep) {
    campoCep.addEventListener('blur', function () {
        var cep = campoCep.value.replace('-', '');
        if (cep.length != 8) {
            return;
        }

        fetch('https://viacep.com.br/ws/' + cep + '/json/')
            .then(function (resposta) {
                return resposta.json();
            })
            .then(function (dados) {
                if (dados.erro) {
                    alert('CEP não encontrado');
                    return;
                }
                document.getElementById('rua').value = dados.logradouro;
                document.getElementById('bairro').value = dados.bairro;
                document.getElementById('cidade').value = dados.localidade;
                document.getElementById('estado').value = dados.uf;
            });
    });
}
