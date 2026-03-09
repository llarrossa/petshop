<?php
$page_title = 'Detalhes do Produto';
ob_start();
?>

<div class="page-header">
    <h2>Produto: <?= htmlspecialchars($dados['nome']) ?></h2>
    <div>
        <a href="?page=produtos&action=edit&id=<?= $dados['id'] ?>" class="btn btn-warning">✏️ Editar</a>
        <a href="?page=produtos&action=movimentacao&id=<?= $dados['id'] ?>" class="btn btn-secondary">📦 Movimentação</a>
        <a href="?page=produtos&action=list" class="btn btn-secondary">← Voltar</a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>Informações do Produto</h3>
    </div>
    <div class="card-body">
        <div class="form-row">
            <div class="form-group col-md-6">
                <label>Nome</label>
                <p><?= htmlspecialchars($dados['nome']) ?></p>
            </div>
            <div class="form-group col-md-3">
                <label>SKU</label>
                <p><?= htmlspecialchars($dados['sku'] ?? '-') ?></p>
            </div>
            <div class="form-group col-md-3">
                <label>Categoria</label>
                <p><?= htmlspecialchars($dados['categoria'] ?? '-') ?></p>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-3">
                <label>Preço de Venda</label>
                <p><?= formatarMoeda($dados['preco_venda']) ?></p>
            </div>
            <div class="form-group col-md-3">
                <label>Preço de Custo</label>
                <p><?= formatarMoeda($dados['preco_custo'] ?? 0) ?></p>
            </div>
            <div class="form-group col-md-2">
                <label>Unidade</label>
                <p><?= htmlspecialchars($dados['unidade']) ?></p>
            </div>
            <div class="form-group col-md-2">
                <label>Estoque Atual</label>
                <p class="<?= $dados['estoque_atual'] <= $dados['estoque_minimo'] ? 'text-danger' : '' ?>">
                    <strong><?= $dados['estoque_atual'] ?></strong>
                </p>
            </div>
            <div class="form-group col-md-2">
                <label>Estoque Mínimo</label>
                <p><?= $dados['estoque_minimo'] ?></p>
            </div>
        </div>
        <?php if (!empty($dados['descricao'])): ?>
        <div class="form-group">
            <label>Descrição</label>
            <p><?= nl2br(htmlspecialchars($dados['descricao'])) ?></p>
        </div>
        <?php endif; ?>
        <div class="form-group">
            <label>Status</label>
            <p><span class="badge badge-<?= $dados['status'] == 'ativo' ? 'success' : 'danger' ?>"><?= ucfirst($dados['status']) ?></span></p>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>Histórico de Movimentações</h3>
    </div>
    <div class="card-body">
        <?php if (empty($historico)): ?>
            <p class="text-muted text-center">Nenhuma movimentação registrada.</p>
        <?php else: ?>
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Tipo</th>
                    <th>Quantidade</th>
                    <th>Motivo</th>
                    <th>Usuário</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($historico as $mov): ?>
                <tr>
                    <td><?= formatarData($mov['data']) ?></td>
                    <td>
                        <span class="badge badge-<?= $mov['tipo'] == 'entrada' ? 'success' : 'danger' ?>">
                            <?= ucfirst($mov['tipo']) ?>
                        </span>
                    </td>
                    <td><?= $mov['quantidade'] ?></td>
                    <td><?= htmlspecialchars($mov['motivo']) ?></td>
                    <td><?= htmlspecialchars($mov['usuario_nome'] ?? '-') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../template.php';
?>
