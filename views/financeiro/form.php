<?php
$page_title = 'Novo Lançamento';
ob_start();
?>

<div class="page-header">
    <h2>Novo Lançamento</h2>
    <a href="?page=financeiro&action=list" class="btn btn-secondary">← Voltar</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" class="form">
            <input type="hidden" name="csrf_token" value="<?= getCsrfToken() ?>">
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="tipo">Tipo *</label>
                    <select id="tipo" name="tipo" class="form-control" required>
                        <option value="receita">Receita</option>
                        <option value="despesa">Despesa</option>
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="categoria">Categoria</label>
                    <input type="text" id="categoria" name="categoria" class="form-control" placeholder="Ex: Aluguel, Salário, Venda...">
                </div>
                <div class="form-group col-md-3">
                    <label for="valor">Valor *</label>
                    <input type="number" id="valor" name="valor" class="form-control" required step="0.01" min="0.01">
                </div>
                <div class="form-group col-md-3">
                    <label for="data_pagamento">Data *</label>
                    <input type="date" id="data_pagamento" name="data_pagamento" class="form-control" required value="<?= date('Y-m-d') ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-5">
                    <label for="descricao">Descrição *</label>
                    <input type="text" id="descricao" name="descricao" class="form-control" required>
                </div>
                <div class="form-group col-md-4">
                    <label for="forma_pagamento">Forma de Pagamento</label>
                    <select id="forma_pagamento" name="forma_pagamento" class="form-control">
                        <option value="">Não informado</option>
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
                    <label for="status">Status *</label>
                    <select id="status" name="status" class="form-control" required>
                        <option value="pago">Pago</option>
                        <option value="pendente">Pendente</option>
                    </select>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">💾 Salvar</button>
                <a href="?page=financeiro&action=list" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../template.php';
?>
