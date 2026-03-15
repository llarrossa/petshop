<?php
$page_title = 'Movimentação de Estoque';
$return_url = $_GET['return_url'] ?? '?page=produtos&action=list';
ob_start();
?>

<div class="page-header">
    <h2>Movimentação de Estoque</h2>
    <a href="?page=produtos&action=view&id=<?= $dados['id'] ?>&return_url=<?= urlencode($return_url) ?>" class="btn btn-secondary">← Voltar</a>
</div>

<div class="card">
    <div class="card-header">
        <h3><?= htmlspecialchars($dados['nome']) ?></h3>
    </div>
    <div class="card-body">
        <p>Estoque atual: <strong><?= $dados['estoque_atual'] ?> <?= htmlspecialchars($dados['unidade']) ?></strong></p>
        <p>Estoque mínimo: <strong><?= $dados['estoque_minimo'] ?> <?= htmlspecialchars($dados['unidade']) ?></strong></p>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>Registrar Movimentação</h3>
    </div>
    <div class="card-body">
        <form method="POST" id="form-movimentacao" class="form">
            <input type="hidden" name="csrf_token" value="<?= getCsrfToken() ?>">
            <input type="hidden" name="return_url"        value="<?= htmlspecialchars($return_url) ?>">
            <input type="hidden" name="confirmar_negativo" id="confirmar_negativo" value="">

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="tipo">Tipo *</label>
                    <select id="tipo" name="tipo" class="form-control" required>
                        <option value="entrada">Entrada</option>
                        <option value="saida">Saída</option>
                        <option value="ajuste">Ajuste</option>
                    </select>
                </div>

                <div class="form-group col-md-4">
                    <label for="quantidade">Quantidade *</label>
                    <input type="number" id="quantidade" name="quantidade" class="form-control" required min="1" value="">
                </div>
            </div>

            <div class="form-group">
                <label for="motivo">Motivo *</label>
                <input type="text" id="motivo" name="motivo" class="form-control" required placeholder="Ex: Compra de fornecedor, venda avulsa, inventário...">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">💾 Registrar</button>
                <a href="?page=produtos&action=view&id=<?= $dados['id'] ?>&return_url=<?= urlencode($return_url) ?>" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<script>
(function () {
    var estoqueAtual = <?= (int)$dados['estoque_atual'] ?>;
    var unidade      = <?= json_encode($dados['unidade'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    var form         = document.getElementById('form-movimentacao');

    form.addEventListener('submit', function (e) {
        var tipo      = document.getElementById('tipo').value;
        var quantidade = parseInt(document.getElementById('quantidade').value, 10) || 0;
        var confirmar  = document.getElementById('confirmar_negativo');

        // Só intercepta saída que gera estoque negativo e ainda não foi confirmada
        if (tipo === 'saida' && quantidade > estoqueAtual && confirmar.value !== '1') {
            e.preventDefault();
            var estoqueApos = estoqueAtual - quantidade;
            var msg = 'O estoque desse produto ficará negativo ('
                    + estoqueApos + ' ' + unidade + '). Deseja continuar?';

            if (window.confirm(msg)) {
                confirmar.value = '1';
                form.submit();
            }
        }
    });
})();
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../template.php';
?>
