<?php
$page_title = 'Detalhes da Venda #' . $dados['id'];
ob_start();
?>

<div class="page-header">
    <h2>Venda #<?= $dados['id'] ?></h2>
    <div>
        <?php
        $return_url = htmlspecialchars(safeReturnUrl($_GET['return_url'] ?? '', '?page=vendas&action=list'));
        $return_url_enc = urlencode(safeReturnUrl($_GET['return_url'] ?? '', '?page=vendas&action=list'));
        ?>
        <?php if ($dados['status'] == 'finalizada' && moduloDisponivel('nota_fiscal')): ?>
            <?php if (empty($dados['tem_nota_fiscal'])): ?>
            <a href="?page=notas_fiscais&action=emitir&venda_id=<?= $dados['id'] ?>"
               class="btn btn-primary">🧾 Emitir Nota Fiscal</a>
            <?php else: ?>
            <span class="badge badge-success" style="padding:8px 14px;font-size:.85em;">✅ NF Emitida</span>
            <?php endif; ?>
        <?php endif; ?>
        <?php if ($dados['status'] == 'finalizada'): ?>
        <a href="?page=vendas&action=cancelar&id=<?= $dados['id'] ?>&return_url=<?= $return_url_enc ?>" class="btn btn-danger" onclick="return confirm('Cancelar esta venda? O estoque será revertido.')">✖️ Cancelar Venda</a>
        <?php endif; ?>
        <a href="<?= $return_url ?>" class="btn btn-secondary">← Voltar</a>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Informações da Venda</h3></div>
    <div class="card-body">
        <div class="form-row">
            <div class="form-group col-md-3">
                <label>Data/Hora</label>
                <p><?= date('d/m/Y H:i', strtotime($dados['data'])) ?></p>
            </div>
            <div class="form-group col-md-5">
                <label>Cliente</label>
                <p><?= htmlspecialchars($dados['tutor_nome'] ?? 'Cliente avulso') ?></p>
            </div>
            <div class="form-group col-md-2">
                <label>Status</label>
                <p><span class="badge badge-<?= $dados['status'] == 'finalizada' ? 'success' : 'danger' ?>"><?= ucfirst($dados['status']) ?></span></p>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-3">
                <label>Forma de Pagamento</label>
                <p><?= ucfirst($dados['forma_pagamento']) ?></p>
            </div>
            <div class="form-group col-md-3">
                <label>Valor Total</label>
                <p><?= formatarMoeda($dados['valor_total']) ?></p>
            </div>
            <div class="form-group col-md-3">
                <label>Desconto</label>
                <p><?= formatarMoeda($dados['desconto']) ?></p>
            </div>
            <div class="form-group col-md-3">
                <label>Valor Final</label>
                <p><strong><?= formatarMoeda($dados['valor_final']) ?></strong></p>
            </div>
        </div>
        <?php if (!empty($dados['observacoes'])): ?>
        <div class="form-group">
            <label>Observações</label>
            <p><?= htmlspecialchars($dados['observacoes']) ?></p>
        </div>
        <?php endif; ?>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Itens</h3></div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr><th>Tipo</th><th>Item</th><th>Qtd</th><th>Preço Unit.</th><th>Subtotal</th><th>Profissional</th></tr>
            </thead>
            <tbody>
                <?php foreach ($itens as $item): ?>
                <tr>
                    <td><span class="badge badge-<?= $item['tipo_item'] == 'servico' ? 'info' : 'secondary' ?>"><?= ucfirst($item['tipo_item']) ?></span></td>
                    <td><?= htmlspecialchars($item['nome_item']) ?></td>
                    <td><?= $item['quantidade'] ?></td>
                    <td><?= formatarMoeda($item['preco_unitario']) ?></td>
                    <td><?= formatarMoeda($item['preco_total']) ?></td>
                    <td><?= htmlspecialchars($item['profissional_nome'] ?? '-') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="text-right"><strong>Total:</strong></td>
                    <td><strong><?= formatarMoeda($dados['valor_final']) ?></strong></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../template.php';
?>
