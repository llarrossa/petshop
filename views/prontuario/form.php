<?php
$editando   = !empty($dados['id']);
$page_title = $editando ? 'Editar Prontuário' : 'Novo Registro de Prontuário';
ob_start();

// Monta mapa pet_id => [cliente_id, cliente_nome] para o JS de auto-preenchimento
$pets_map = [];
foreach ($pets as $p) {
    $pets_map[$p['id']] = [
        'cliente_id'   => $p['tutor_id']   ?? '',
        'cliente_nome' => $p['tutor_nome'] ?? '',
    ];
}
?>

<div class="page-header">
    <h2><?= $editando ? '✏️ Editar Prontuário' : '📋 Novo Registro de Prontuário' ?></h2>
    <a href="<?= htmlspecialchars($back_url) ?>" class="btn btn-secondary">← Voltar</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" class="needs-validation" novalidate>

            <?php
            // Se veio da página do pet, preservar redirect
            $redirect_pet = $_GET['pet_id'] ?? ($dados['pet_id'] ?? '');
            if ($redirect_pet && !$editando):
            ?>
                <input type="hidden" name="redirect_pet" value="<?= (int)$redirect_pet ?>">
            <?php endif; ?>

            <div class="row">

                <!-- Pet -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="pet_id">Pet *</label>
                        <select name="pet_id" id="pet_id" class="form-control" required
                            <?= $editando ? 'disabled' : '' ?>>
                            <option value="">Selecione o pet</option>
                            <?php foreach ($pets as $p): ?>
                                <?php
                                $selecionado = false;
                                if ($editando && $dados['pet_id'] == $p['id']) $selecionado = true;
                                if (!$editando && $pet_pre && $pet_pre['id'] == $p['id']) $selecionado = true;
                                ?>
                                <option value="<?= $p['id'] ?>"
                                        data-cliente-id="<?= $p['tutor_id'] ?? '' ?>"
                                        data-cliente-nome="<?= htmlspecialchars($p['tutor_nome'] ?? '') ?>"
                                    <?= $selecionado ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($p['nome']) ?>
                                    <?php if (!empty($p['tutor_nome'])): ?>
                                        — <?= htmlspecialchars($p['tutor_nome']) ?>
                                    <?php endif; ?>
                                    <?php if (!empty($p['especie'])): ?>
                                        (<?= htmlspecialchars(ucfirst($p['especie'])) ?>)
                                    <?php endif; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if ($editando): ?>
                            <input type="hidden" name="pet_id" value="<?= (int)$dados['pet_id'] ?>">
                        <?php endif; ?>
                        <div class="invalid-feedback">Selecione o pet.</div>
                    </div>
                </div>

                <!-- Cliente (preenchido automaticamente) -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Cliente (preenchido automaticamente)</label>
                        <input type="text" id="cliente_nome_display" class="form-control"
                               value="<?= htmlspecialchars($dados['cliente_nome'] ?? ($pet_pre['tutor_nome'] ?? '')) ?>"
                               readonly placeholder="Selecione um pet acima">
                        <input type="hidden" name="cliente_id" id="cliente_id"
                               value="<?= (int)($dados['cliente_id'] ?? ($pet_pre['tutor_id'] ?? 0)) ?>">
                    </div>
                </div>

                <!-- Data do atendimento -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="data_atendimento">Data do Atendimento *</label>
                        <input type="date" name="data_atendimento" id="data_atendimento"
                               class="form-control" required
                               value="<?= htmlspecialchars($dados['data_atendimento'] ?? date('Y-m-d')) ?>">
                        <div class="invalid-feedback">Informe a data do atendimento.</div>
                    </div>
                </div>

                <!-- Peso -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="peso">Peso do Pet (kg)</label>
                        <input type="number" name="peso" id="peso" class="form-control"
                               step="0.01" min="0" max="999"
                               value="<?= htmlspecialchars($dados['peso'] ?? '') ?>"
                               placeholder="Ex: 5.80">
                    </div>
                </div>

                <!-- Profissional -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="profissional_id">Profissional Responsável</label>
                        <select name="profissional_id" id="profissional_id" class="form-control">
                            <option value="">Nenhum / Não informado</option>
                            <?php foreach ($profissionais as $prof): ?>
                                <option value="<?= $prof['id'] ?>"
                                    <?= (isset($dados['profissional_id']) && $dados['profissional_id'] == $prof['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($prof['nome']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Observações -->
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="observacoes">Observações / Descrição do Atendimento</label>
                        <textarea name="observacoes" id="observacoes" class="form-control" rows="5"
                                  placeholder="Descreva o atendimento, condição do animal, procedimentos realizados..."><?= htmlspecialchars($dados['observacoes'] ?? '') ?></textarea>
                    </div>
                </div>

                <!-- Recomendações -->
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="recomendacoes">Recomendações</label>
                        <textarea name="recomendacoes" id="recomendacoes" class="form-control" rows="4"
                                  placeholder="Medicamentos, cuidados pós-atendimento, próximo retorno..."><?= htmlspecialchars($dados['recomendacoes'] ?? '') ?></textarea>
                    </div>
                </div>

            </div><!-- .row -->

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">💾 Salvar Registro</button>
                <a href="<?= htmlspecialchars($back_url) ?>" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<script>
// Mapa de pets para preenchimento automático do cliente
var petsMap = <?= json_encode($pets_map) ?>;

document.getElementById('pet_id').addEventListener('change', function () {
    var selectedOption = this.options[this.selectedIndex];
    var clienteId   = selectedOption.getAttribute('data-cliente-id')   || '';
    var clienteNome = selectedOption.getAttribute('data-cliente-nome')  || '';

    document.getElementById('cliente_id').value           = clienteId;
    document.getElementById('cliente_nome_display').value = clienteNome || '—';
});

// Validação do formulário
(function () {
    'use strict';
    document.querySelectorAll('.needs-validation').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            // Garantir que cliente_id está preenchido
            var petSel    = document.getElementById('pet_id').value;
            var clienteId = document.getElementById('cliente_id').value;
            if (petSel && !clienteId) {
                alert('O pet selecionado não possui cliente associado. Verifique o cadastro do pet.');
                event.preventDefault();
                return;
            }
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
