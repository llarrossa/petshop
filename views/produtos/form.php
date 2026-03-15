<?php
$isEdit = !empty($dados['id']);
$page_title = $isEdit ? 'Editar Produto' : 'Novo Produto';
ob_start();
?>

<div class="page-header">
    <h2><?= $isEdit ? 'Editar' : 'Novo' ?> Produto</h2>
    <a href="<?= htmlspecialchars($_GET['return_url'] ?? '?page=produtos&action=list') ?>" class="btn btn-secondary">← Voltar</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" class="form">
            <input type="hidden" name="csrf_token" value="<?= getCsrfToken() ?>">
            <input type="hidden" name="return_url" value="<?= htmlspecialchars($_GET['return_url'] ?? '?page=produtos&action=list') ?>">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="nome">Nome *</label>
                    <input type="text" id="nome" name="nome" class="form-control" required value="<?= htmlspecialchars($dados['nome'] ?? '') ?>">
                </div>

                <div class="form-group col-md-3">
                    <label for="sku">SKU</label>
                    <input type="text" id="sku" name="sku" class="form-control" value="<?= htmlspecialchars($dados['sku'] ?? '') ?>">
                </div>

                <div class="form-group col-md-3">
                    <label for="categoria">Categoria</label>
                    <input type="text" id="categoria" name="categoria" class="form-control" list="categorias-list" value="<?= htmlspecialchars($dados['categoria'] ?? '') ?>">
                    <datalist id="categorias-list">
                        <?php foreach ($categorias as $cat): ?>
                        <option value="<?= htmlspecialchars($cat['categoria']) ?>">
                        <?php endforeach; ?>
                    </datalist>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="preco_venda">Preço de Venda *</label>
                    <input type="number" id="preco_venda" name="preco_venda" class="form-control" required step="0.01" min="0" value="<?= $dados['preco_venda'] ?? '' ?>">
                </div>

                <div class="form-group col-md-3">
                    <label for="preco_custo">Preço de Custo</label>
                    <input type="number" id="preco_custo" name="preco_custo" class="form-control" step="0.01" min="0" value="<?= $dados['preco_custo'] ?? '' ?>">
                </div>

                <div class="form-group col-md-2">
                    <label for="unidade">Unidade</label>
                    <select id="unidade" name="unidade" class="form-control">
                        <?php foreach (['UN', 'KG', 'G', 'L', 'ML', 'CX', 'PCT'] as $un): ?>
                        <option value="<?= $un ?>" <?= (isset($dados['unidade']) && $dados['unidade'] == $un) ? 'selected' : '' ?>><?= $un ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <?php if (!$isEdit): ?>
                <div class="form-group col-md-2">
                    <label for="estoque_atual">Estoque Inicial</label>
                    <input type="number" id="estoque_atual" name="estoque_atual" class="form-control" min="0" value="0">
                </div>
                <?php endif; ?>

                <div class="form-group col-md-2">
                    <label for="estoque_minimo">Estoque Mínimo</label>
                    <input type="number" id="estoque_minimo" name="estoque_minimo" class="form-control" min="0" value="<?= $dados['estoque_minimo'] ?? 0 ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="descricao">Descrição</label>
                <textarea id="descricao" name="descricao" class="form-control" rows="3"><?= htmlspecialchars($dados['descricao'] ?? '') ?></textarea>
            </div>

            <?php if ($isEdit): ?>
            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status" class="form-control" required>
                    <option value="ativo" <?= (isset($dados['status']) && $dados['status'] == 'ativo') ? 'selected' : '' ?>>Ativo</option>
                    <option value="inativo" <?= (isset($dados['status']) && $dados['status'] == 'inativo') ? 'selected' : '' ?>>Inativo</option>
                </select>
            </div>
            <?php endif; ?>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">💾 Salvar</button>
                <a href="<?= htmlspecialchars($_GET['return_url'] ?? '?page=produtos&action=list') ?>" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../template.php';
?>
