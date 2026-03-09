<?php
$page_title = isset($dados['id']) ? 'Editar Pet' : 'Novo Pet';
ob_start();
?>

<div class="page-header">
    <h1><?= isset($dados['id']) ? '✏️ Editar Pet' : '➕ Novo Pet' ?></h1>
    <a href="?page=pets&action=list" class="btn btn-secondary">← Voltar</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" class="needs-validation" novalidate>
            <div class="row">
                <!-- Tutor -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="tutor_id">Tutor (Dono) *</label>
                        <select name="tutor_id" id="tutor_id" class="form-control" required>
                            <option value="">Selecione o tutor</option>
                            <?php foreach ($tutores as $t): ?>
                                <option value="<?= $t['id'] ?>" <?= (isset($dados['tutor_id']) && $dados['tutor_id'] == $t['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($t['nome']) ?> - <?= htmlspecialchars($t['telefone'] ?? '') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Por favor, selecione o tutor.</div>
                    </div>
                </div>

                <!-- Nome do Pet -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nome">Nome do Pet *</label>
                        <input type="text" name="nome" id="nome" class="form-control"
                               value="<?= htmlspecialchars($dados['nome'] ?? '') ?>" required>
                        <div class="invalid-feedback">Por favor, informe o nome do pet.</div>
                    </div>
                </div>

                <!-- Espécie -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="especie">Espécie *</label>
                        <select name="especie" id="especie" class="form-control" required>
                            <option value="">Selecione</option>
                            <option value="cachorro" <?= (isset($dados['especie']) && $dados['especie'] == 'cachorro') ? 'selected' : '' ?>>Cachorro</option>
                            <option value="gato" <?= (isset($dados['especie']) && $dados['especie'] == 'gato') ? 'selected' : '' ?>>Gato</option>
                            <option value="passaro" <?= (isset($dados['especie']) && $dados['especie'] == 'passaro') ? 'selected' : '' ?>>Pássaro</option>
                            <option value="roedor" <?= (isset($dados['especie']) && $dados['especie'] == 'roedor') ? 'selected' : '' ?>>Roedor</option>
                            <option value="reptil" <?= (isset($dados['especie']) && $dados['especie'] == 'reptil') ? 'selected' : '' ?>>Réptil</option>
                            <option value="outro" <?= (isset($dados['especie']) && $dados['especie'] == 'outro') ? 'selected' : '' ?>>Outro</option>
                        </select>
                        <div class="invalid-feedback">Por favor, selecione a espécie.</div>
                    </div>
                </div>

                <!-- Raça -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="raca">Raça</label>
                        <input type="text" name="raca" id="raca" class="form-control"
                               value="<?= htmlspecialchars($dados['raca'] ?? '') ?>" placeholder="Ex: Poodle, SRD, etc.">
                    </div>
                </div>

                <!-- Sexo -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="sexo">Sexo *</label>
                        <select name="sexo" id="sexo" class="form-control" required>
                            <option value="">Selecione</option>
                            <option value="macho" <?= (isset($dados['sexo']) && $dados['sexo'] == 'macho') ? 'selected' : '' ?>>Macho</option>
                            <option value="femea" <?= (isset($dados['sexo']) && $dados['sexo'] == 'femea') ? 'selected' : '' ?>>Fêmea</option>
                        </select>
                        <div class="invalid-feedback">Por favor, selecione o sexo.</div>
                    </div>
                </div>

                <!-- Data de Nascimento -->
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="data_nascimento">Data de Nascimento</label>
                        <input type="date" name="data_nascimento" id="data_nascimento" class="form-control"
                               value="<?= $dados['data_nascimento'] ?? '' ?>">
                    </div>
                </div>

                <!-- Peso -->
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="peso">Peso (kg)</label>
                        <input type="number" name="peso" id="peso" class="form-control" step="0.01" min="0"
                               value="<?= $dados['peso'] ?? '' ?>" placeholder="Ex: 5.5">
                    </div>
                </div>

                <!-- Cor -->
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="cor">Cor</label>
                        <input type="text" name="cor" id="cor" class="form-control"
                               value="<?= htmlspecialchars($dados['cor'] ?? '') ?>" placeholder="Ex: Branco, Preto">
                    </div>
                </div>

                <!-- Porte -->
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="porte">Porte</label>
                        <select name="porte" id="porte" class="form-control">
                            <option value="">Selecione</option>
                            <option value="Mini" <?= (isset($dados['porte']) && $dados['porte'] == 'Mini') ? 'selected' : '' ?>>Mini</option>
                            <option value="Pequeno" <?= (isset($dados['porte']) && $dados['porte'] == 'Pequeno') ? 'selected' : '' ?>>Pequeno</option>
                            <option value="Médio" <?= (isset($dados['porte']) && $dados['porte'] == 'Médio') ? 'selected' : '' ?>>Médio</option>
                            <option value="Grande" <?= (isset($dados['porte']) && $dados['porte'] == 'Grande') ? 'selected' : '' ?>>Grande</option>
                            <option value="Gigante" <?= (isset($dados['porte']) && $dados['porte'] == 'Gigante') ? 'selected' : '' ?>>Gigante</option>
                        </select>
                    </div>
                </div>

                <!-- Observações -->
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="observacoes">Observações</label>
                        <textarea name="observacoes" id="observacoes" class="form-control" rows="3"
                                  placeholder="Informações adicionais sobre o pet..."><?= htmlspecialchars($dados['observacoes'] ?? '') ?></textarea>
                    </div>
                </div>

                <?php if (isset($dados['id'])): ?>
                <!-- Status (somente edição) -->
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="ativo" <?= (isset($dados['status']) && $dados['status'] == 'ativo') ? 'selected' : '' ?>>Ativo</option>
                            <option value="inativo" <?= (isset($dados['status']) && $dados['status'] == 'inativo') ? 'selected' : '' ?>>Inativo</option>
                        </select>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">💾 Salvar</button>
                <a href="?page=pets&action=list" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<script>
// Validação do formulário
(function() {
    'use strict';
    var forms = document.querySelectorAll('.needs-validation');
    Array.prototype.slice.call(forms).forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
})();
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../template.php';
?>
