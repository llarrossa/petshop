<?php
$page_title = isset($dados) ? 'Editar Cliente' : 'Novo Cliente';
$isEdit = isset($dados);
ob_start();
?>

<div class="page-header">
    <h2><?= $isEdit ? 'Editar' : 'Novo' ?> Cliente</h2>
    <a href="<?= htmlspecialchars($back_url) ?>" class="btn btn-secondary">← Voltar</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" class="form">
            <input type="hidden" name="csrf_token" value="<?= getCsrfToken() ?>">
            <div class="form-row">
                <div class="form-group col-md-8">
                    <label for="nome">Nome Completo *</label>
                    <input type="text" id="nome" name="nome" class="form-control" required value="<?= $dados['nome'] ?? '' ?>">
                </div>

                <div class="form-group col-md-4">
                    <label for="cpf">CPF</label>
                    <input type="text" id="cpf" name="cpf" class="form-control cpf-mask" value="<?= $dados['cpf'] ?? '' ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="telefone">Telefone</label>
                    <input type="text" id="telefone" name="telefone" class="form-control phone-mask" value="<?= $dados['telefone'] ?? '' ?>">
                </div>

                <div class="form-group col-md-4">
                    <label for="whatsapp">WhatsApp</label>
                    <input type="text" id="whatsapp" name="whatsapp" class="form-control phone-mask" value="<?= $dados['whatsapp'] ?? '' ?>">
                </div>

                <div class="form-group col-md-4">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" class="form-control" value="<?= $dados['email'] ?? '' ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-8">
                    <label for="endereco">Endereço</label>
                    <input type="text" id="endereco" name="endereco" class="form-control" value="<?= $dados['endereco'] ?? '' ?>">
                </div>

                <div class="form-group col-md-4">
                    <label for="cep">CEP</label>
                    <input type="text" id="cep" name="cep" class="form-control cep-mask" value="<?= $dados['cep'] ?? '' ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-8">
                    <label for="cidade">Cidade</label>
                    <input type="text" id="cidade" name="cidade" class="form-control" value="<?= $dados['cidade'] ?? '' ?>">
                </div>

                <div class="form-group col-md-4">
                    <label for="estado">Estado</label>
                    <select id="estado" name="estado" class="form-control">
                        <option value="">Selecione...</option>
                        <option value="AC" <?= (isset($dados['estado']) && $dados['estado'] == 'AC') ? 'selected' : '' ?>>AC</option>
                        <option value="AL" <?= (isset($dados['estado']) && $dados['estado'] == 'AL') ? 'selected' : '' ?>>AL</option>
                        <option value="AP" <?= (isset($dados['estado']) && $dados['estado'] == 'AP') ? 'selected' : '' ?>>AP</option>
                        <option value="AM" <?= (isset($dados['estado']) && $dados['estado'] == 'AM') ? 'selected' : '' ?>>AM</option>
                        <option value="BA" <?= (isset($dados['estado']) && $dados['estado'] == 'BA') ? 'selected' : '' ?>>BA</option>
                        <option value="CE" <?= (isset($dados['estado']) && $dados['estado'] == 'CE') ? 'selected' : '' ?>>CE</option>
                        <option value="DF" <?= (isset($dados['estado']) && $dados['estado'] == 'DF') ? 'selected' : '' ?>>DF</option>
                        <option value="ES" <?= (isset($dados['estado']) && $dados['estado'] == 'ES') ? 'selected' : '' ?>>ES</option>
                        <option value="GO" <?= (isset($dados['estado']) && $dados['estado'] == 'GO') ? 'selected' : '' ?>>GO</option>
                        <option value="MA" <?= (isset($dados['estado']) && $dados['estado'] == 'MA') ? 'selected' : '' ?>>MA</option>
                        <option value="MT" <?= (isset($dados['estado']) && $dados['estado'] == 'MT') ? 'selected' : '' ?>>MT</option>
                        <option value="MS" <?= (isset($dados['estado']) && $dados['estado'] == 'MS') ? 'selected' : '' ?>>MS</option>
                        <option value="MG" <?= (isset($dados['estado']) && $dados['estado'] == 'MG') ? 'selected' : '' ?>>MG</option>
                        <option value="PA" <?= (isset($dados['estado']) && $dados['estado'] == 'PA') ? 'selected' : '' ?>>PA</option>
                        <option value="PB" <?= (isset($dados['estado']) && $dados['estado'] == 'PB') ? 'selected' : '' ?>>PB</option>
                        <option value="PR" <?= (isset($dados['estado']) && $dados['estado'] == 'PR') ? 'selected' : '' ?>>PR</option>
                        <option value="PE" <?= (isset($dados['estado']) && $dados['estado'] == 'PE') ? 'selected' : '' ?>>PE</option>
                        <option value="PI" <?= (isset($dados['estado']) && $dados['estado'] == 'PI') ? 'selected' : '' ?>>PI</option>
                        <option value="RJ" <?= (isset($dados['estado']) && $dados['estado'] == 'RJ') ? 'selected' : '' ?>>RJ</option>
                        <option value="RN" <?= (isset($dados['estado']) && $dados['estado'] == 'RN') ? 'selected' : '' ?>>RN</option>
                        <option value="RS" <?= (isset($dados['estado']) && $dados['estado'] == 'RS') ? 'selected' : '' ?>>RS</option>
                        <option value="RO" <?= (isset($dados['estado']) && $dados['estado'] == 'RO') ? 'selected' : '' ?>>RO</option>
                        <option value="RR" <?= (isset($dados['estado']) && $dados['estado'] == 'RR') ? 'selected' : '' ?>>RR</option>
                        <option value="SC" <?= (isset($dados['estado']) && $dados['estado'] == 'SC') ? 'selected' : '' ?>>SC</option>
                        <option value="SP" <?= (isset($dados['estado']) && $dados['estado'] == 'SP') ? 'selected' : '' ?>>SP</option>
                        <option value="SE" <?= (isset($dados['estado']) && $dados['estado'] == 'SE') ? 'selected' : '' ?>>SE</option>
                        <option value="TO" <?= (isset($dados['estado']) && $dados['estado'] == 'TO') ? 'selected' : '' ?>>TO</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="observacoes">Observações</label>
                <textarea id="observacoes" name="observacoes" class="form-control" rows="3"><?= $dados['observacoes'] ?? '' ?></textarea>
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
                <a href="<?= htmlspecialchars($back_url) ?>" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../template.php';
?>
