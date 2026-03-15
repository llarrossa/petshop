<?php
$page_title = isset($dados) ? 'Editar Profissional' : 'Novo Profissional';
$isEdit = isset($dados);
ob_start();
?>

<div class="page-header">
    <h2><?= $isEdit ? 'Editar' : 'Novo' ?> Profissional</h2>
    <a href="<?= htmlspecialchars($back_url) ?>" class="btn btn-secondary">← Voltar</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" class="form">
            <input type="hidden" name="csrf_token" value="<?= getCsrfToken() ?>">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="nome">Nome *</label>
                    <input type="text" id="nome" name="nome" class="form-control" required value="<?= htmlspecialchars($dados['nome'] ?? '') ?>">
                </div>
                <div class="form-group col-md-6">
                    <label for="funcao">Função</label>
                    <input type="text" id="funcao" name="funcao" class="form-control" placeholder="Ex: Tosador, Banhista, Veterinário..." value="<?= htmlspecialchars($dados['funcao'] ?? '') ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="telefone">Telefone</label>
                    <input type="text" id="telefone" name="telefone" class="form-control" value="<?= htmlspecialchars($dados['telefone'] ?? '') ?>">
                </div>
                <div class="form-group col-md-4">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($dados['email'] ?? '') ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="tipo_comissao">Tipo de Comissão</label>
                    <select id="tipo_comissao" name="tipo_comissao" class="form-control">
                        <option value="percentual" <?= (isset($dados['tipo_comissao']) && $dados['tipo_comissao'] == 'percentual') ? 'selected' : '' ?>>Percentual (%)</option>
                        <option value="valor_fixo" <?= (isset($dados['tipo_comissao']) && $dados['tipo_comissao'] == 'valor_fixo') ? 'selected' : '' ?>>Fixo por serviço (R$)</option>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label for="comissao">Valor da Comissão</label>
                    <input type="number" id="comissao" name="comissao" class="form-control" step="0.01" min="0" value="<?= $dados['comissao'] ?? 0 ?>">
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
