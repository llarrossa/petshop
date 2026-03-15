<?php
$page_title = isset($dados) ? 'Editar Serviço' : 'Novo Serviço';
$isEdit = isset($dados);
ob_start();
?>

<div class="page-header">
    <h2><?= $isEdit ? 'Editar' : 'Novo' ?> Serviço</h2>
    <a href="<?= htmlspecialchars($back_url) ?>" class="btn btn-secondary">← Voltar</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" class="form">
            <input type="hidden" name="csrf_token" value="<?= getCsrfToken() ?>">
            <div class="form-row">
                <div class="form-group col-md-8">
                    <label for="nome">Nome *</label>
                    <input type="text" id="nome" name="nome" class="form-control" required value="<?= htmlspecialchars($dados['nome'] ?? '') ?>">
                </div>

                <div class="form-group col-md-4">
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
                <div class="form-group col-md-4">
                    <label for="preco">Preço *</label>
                    <input type="number" id="preco" name="preco" class="form-control" required step="0.01" min="0" value="<?= $dados['preco'] ?? '' ?>">
                </div>

                <div class="form-group col-md-4">
                    <label for="duracao_media">Duração Média (min)</label>
                    <input type="number" id="duracao_media" name="duracao_media" class="form-control" min="0" value="<?= $dados['duracao_media'] ?? '' ?>">
                </div>

                <?php if ($isEdit): ?>
                <div class="form-group col-md-4">
                    <label for="status">Status</label>
                    <select id="status" name="status" class="form-control" required>
                        <option value="ativo" <?= (isset($dados['status']) && $dados['status'] == 'ativo') ? 'selected' : '' ?>>Ativo</option>
                        <option value="inativo" <?= (isset($dados['status']) && $dados['status'] == 'inativo') ? 'selected' : '' ?>>Inativo</option>
                    </select>
                </div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="descricao">Descrição</label>
                <textarea id="descricao" name="descricao" class="form-control" rows="3"><?= htmlspecialchars($dados['descricao'] ?? '') ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">💾 Salvar</button>
                <a href="<?= htmlspecialchars($back_url) ?>" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../template.php';
?>
