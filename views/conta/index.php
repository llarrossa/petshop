<?php
/**
 * View: Minha Conta
 * Abas: Perfil (nome/e-mail) e Senha
 */
?>

<div class="page-header">
    <h2>Minha Conta</h2>
</div>

<?php if (!empty($errors)): ?>
<div class="alert alert-error">
    <ul style="margin:0; padding-left:18px;">
        <?php foreach ($errors as $err): ?>
        <li><?= htmlspecialchars($err) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<!-- Tabs -->
<div class="tabs" style="margin-bottom:24px;">
    <a href="?page=conta&tab=perfil"
       class="tab-link <?= $tab === 'perfil' ? 'active' : '' ?>">
        Perfil
    </a>
    <a href="?page=conta&tab=senha"
       class="tab-link <?= $tab === 'senha' ? 'active' : '' ?>">
        Alterar Senha
    </a>
</div>

<!-- Tab: Perfil -->
<?php if ($tab === 'perfil'): ?>
<div class="card">
    <div class="card-body">
        <form method="POST" class="form">
            <input type="hidden" name="acao" value="perfil">

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="nome">Nome *</label>
                    <input type="text" id="nome" name="nome" class="form-control" required
                           value="<?= htmlspecialchars($usuario['nome'] ?? '') ?>">
                </div>

                <div class="form-group col-md-6">
                    <label for="email">E-mail *</label>
                    <input type="email" id="email" name="email" class="form-control" required
                           value="<?= htmlspecialchars($usuario['email'] ?? '') ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Perfil</label>
                    <input type="text" class="form-control" disabled
                           value="<?= ucfirst(htmlspecialchars($usuario['perfil'] ?? '')) ?>">
                </div>

                <div class="form-group col-md-4">
                    <label>Membro desde</label>
                    <input type="text" class="form-control" disabled
                           value="<?= date('d/m/Y', strtotime($usuario['data_criacao'] ?? 'now')) ?>">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Salvar alterações</button>
            </div>
        </form>
    </div>
</div>

<!-- Tab: Senha -->
<?php else: ?>
<div class="card">
    <div class="card-body">
        <form method="POST" class="form">
            <input type="hidden" name="acao" value="senha">

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="senha_atual">Senha atual *</label>
                    <input type="password" id="senha_atual" name="senha_atual" class="form-control" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="nova_senha">Nova senha * <small>(mínimo 8 caracteres)</small></label>
                    <input type="password" id="nova_senha" name="nova_senha" class="form-control" required minlength="8">
                </div>

                <div class="form-group col-md-6">
                    <label for="confirmar_senha">Confirmar nova senha *</label>
                    <input type="password" id="confirmar_senha" name="confirmar_senha" class="form-control" required minlength="8">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Alterar senha</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<style>
.tabs {
    display: flex;
    gap: 8px;
    border-bottom: 2px solid #e5e7eb;
    padding-bottom: 0;
}
.tab-link {
    padding: 8px 20px;
    text-decoration: none;
    color: #6b7280;
    font-weight: 500;
    border-radius: 6px 6px 0 0;
    border: 1px solid transparent;
    border-bottom: none;
    position: relative;
    bottom: -2px;
    transition: color .15s;
}
.tab-link:hover { color: #2563EB; }
.tab-link.active {
    color: #2563EB;
    background: #fff;
    border-color: #e5e7eb;
    border-bottom-color: #fff;
}
</style>
