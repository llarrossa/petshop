<?php
$page_title = 'Nova Venda (PDV)';
ob_start();
?>

<div class="page-header">
    <h2>Nova Venda (PDV)</h2>
    <a href="?page=vendas&action=list" class="btn btn-secondary">← Voltar</a>
</div>

<form method="POST" class="form" id="form-venda">
    <input type="hidden" name="csrf_token" value="<?= getCsrfToken() ?>">
    <div class="form-row">
        <div class="form-group col-md-5">
            <label for="tutor_id">Cliente</label>
            <select id="tutor_id" name="tutor_id" class="form-control">
                <option value="">Cliente avulso</option>
                <?php foreach ($tutores as $t): ?>
                <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['nome']) ?> - <?= htmlspecialchars($t['telefone'] ?? '') ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group col-md-4">
            <label for="forma_pagamento">Forma de Pagamento *</label>
            <select id="forma_pagamento" name="forma_pagamento" class="form-control" required>
                <option value="dinheiro">Dinheiro</option>
                <option value="debito">Cartão Débito</option>
                <option value="credito">Cartão Crédito</option>
                <option value="pix">PIX</option>
                <option value="boleto">Boleto</option>
                <option value="transferencia">Transferência</option>
                <option value="outros">Outros</option>
            </select>
        </div>
        <div class="form-group col-md-3">
            <label for="desconto">Desconto (R$)</label>
            <input type="number" id="desconto" name="desconto" class="form-control" step="0.01" min="0" value="0" onchange="calcularTotal()">
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3>Itens da Venda</h3>
        </div>
        <div class="card-body">
            <div class="form-row" style="font-weight:bold; margin-bottom:.5rem;">
                <div class="col-md-2">Tipo</div>
                <div class="col-md-4">Item</div>
                <div class="col-md-1">Qtd</div>
                <div class="col-md-2">Preço Unit.</div>
                <div class="col-md-2">Subtotal</div>
                <div class="col-md-1"></div>
            </div>
            <div id="itens-lista"></div>
            <button type="button" class="btn btn-secondary" onclick="adicionarItem()">+ Adicionar Item</button>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="form-row">
                <div class="form-group col-md-8">
                    <label for="observacoes">Observações</label>
                    <input type="text" id="observacoes" name="observacoes" class="form-control">
                </div>
                <div class="form-group col-md-4 text-right">
                    <p>Subtotal: <strong id="exib-subtotal">R$ 0,00</strong></p>
                    <p>Desconto: <strong id="exib-desconto">R$ 0,00</strong></p>
                    <h3>Total: <strong id="exib-total">R$ 0,00</strong></h3>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary btn-lg">💰 Finalizar Venda</button>
                <a href="?page=vendas&action=list" class="btn btn-secondary">Cancelar</a>
            </div>
        </div>
    </div>
</form>

<script>
var servicos = <?= json_encode(array_map(function($s) {
    return ['id' => $s['id'], 'nome' => $s['nome'], 'preco' => $s['preco']];
}, $servicos), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

var produtos = <?= json_encode(array_map(function($p) {
    return ['id' => $p['id'], 'nome' => $p['nome'], 'preco' => $p['preco_venda']];
}, $produtos), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

var profissionais = <?= json_encode(array_map(function($p) {
    return ['id' => $p['id'], 'nome' => $p['nome']];
}, $profissionais), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

var itemCount = 0;

function adicionarItem() {
    var idx = itemCount++;
    var profOptions = '<option value="">Nenhum</option>' + profissionais.map(p => '<option value="'+p.id+'">'+p.nome+'</option>').join('');
    var html = '<div class="form-row item-row" id="item-row-'+idx+'" style="margin-bottom:.5rem; align-items:center;">' +
        '<div class="col-md-2">' +
            '<select name="tipo_item[]" class="form-control" onchange="atualizarSelectItem(this, '+idx+')">' +
                '<option value="servico">Serviço</option>' +
                '<option value="produto">Produto</option>' +
            '</select>' +
        '</div>' +
        '<div class="col-md-4">' +
            '<select name="item_id[]" class="form-control item-select-'+idx+'" onchange="preencherPreco(this, '+idx+')">' +
                servicos.map(s => '<option value="'+s.id+'" data-preco="'+s.preco+'">'+s.nome+'</option>').join('') +
            '</select>' +
            '<input type="hidden" name="nome_item[]" class="item-nome-'+idx+'">' +
        '</div>' +
        '<div class="col-md-1">' +
            '<input type="number" name="quantidade[]" class="form-control item-qtd-'+idx+'" value="1" min="1" step="0.01" onchange="calcularLinhaETotal('+idx+')">' +
        '</div>' +
        '<div class="col-md-2">' +
            '<input type="number" name="preco_unitario[]" class="form-control item-preco-'+idx+'" step="0.01" min="0" onchange="calcularLinhaETotal('+idx+')">' +
        '</div>' +
        '<div class="col-md-2">' +
            '<input type="text" class="form-control item-subtotal-'+idx+'" readonly>' +
        '</div>' +
        '<div class="col-md-1">' +
            '<button type="button" class="btn btn-sm btn-danger" onclick="removerItem('+idx+')">✖</button>' +
        '</div>' +
        '</div>' +
        '<div class="form-row" style="margin-bottom:.5rem;" id="item-prof-'+idx+'">' +
        '<div class="col-md-2"><small>Profissional:</small></div>' +
        '<div class="col-md-4"><select name="profissional_item_id[]" class="form-control">' + profOptions + '</select></div>' +
        '</div>';
    document.getElementById('itens-lista').insertAdjacentHTML('beforeend', html);
    preencherPreco(document.querySelector('.item-select-'+idx), idx);
}

function atualizarSelectItem(tipoSelect, idx) {
    var tipo = tipoSelect.value;
    var lista = tipo === 'servico' ? servicos : produtos;
    var select = document.querySelector('.item-select-'+idx);
    select.innerHTML = lista.map(i => '<option value="'+i.id+'" data-preco="'+i.preco+'">'+i.nome+'</option>').join('');
    document.getElementById('item-prof-'+idx).style.display = (tipo === 'servico') ? '' : 'none';
    preencherPreco(select, idx);
}

function preencherPreco(select, idx) {
    var opt = select.options[select.selectedIndex];
    if (!opt) return;
    document.querySelector('.item-preco-'+idx).value = opt.getAttribute('data-preco') || 0;
    document.querySelector('.item-nome-'+idx).value = opt.text;
    calcularLinhaETotal(idx);
}

function calcularLinhaETotal(idx) {
    var qtd = parseFloat(document.querySelector('.item-qtd-'+idx).value) || 0;
    var preco = parseFloat(document.querySelector('.item-preco-'+idx).value) || 0;
    var sub = qtd * preco;
    document.querySelector('.item-subtotal-'+idx).value = 'R$ ' + sub.toFixed(2).replace('.', ',');
    calcularTotal();
}

function removerItem(idx) {
    var row = document.getElementById('item-row-'+idx);
    var prof = document.getElementById('item-prof-'+idx);
    if (row) row.remove();
    if (prof) prof.remove();
    calcularTotal();
}

function calcularTotal() {
    var subtotal = 0;
    document.querySelectorAll('[name="quantidade[]"]').forEach(function(el, i) {
        var idx = el.className.replace('form-control item-qtd-', '');
        var qtd = parseFloat(el.value) || 0;
        var preco = parseFloat(document.querySelector('.item-preco-'+idx)?.value) || 0;
        subtotal += qtd * preco;
    });
    var desconto = parseFloat(document.getElementById('desconto').value) || 0;
    var total = subtotal - desconto;
    document.getElementById('exib-subtotal').textContent = 'R$ ' + subtotal.toFixed(2).replace('.', ',');
    document.getElementById('exib-desconto').textContent = 'R$ ' + desconto.toFixed(2).replace('.', ',');
    document.getElementById('exib-total').textContent = 'R$ ' + total.toFixed(2).replace('.', ',');
}

// Carrinho inicia vazio — o usuário adiciona itens manualmente
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../template.php';
?>
