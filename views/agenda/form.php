<?php
$page_title = isset($dados) ? 'Editar Agendamento' : 'Novo Agendamento';
$isEdit = isset($dados);
ob_start();
?>

<div class="page-header">
    <h2><?= $isEdit ? 'Editar' : 'Novo' ?> Agendamento</h2>
    <a href="?page=agenda&action=list" class="btn btn-secondary">← Voltar</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" class="form" id="form-agenda">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="tutor_id">Cliente *</label>
                    <select id="tutor_id" name="tutor_id" class="form-control" required onchange="carregarPets(this.value)">
                        <option value="">Selecione o cliente...</option>
                        <?php foreach ($tutores as $t): ?>
                        <option value="<?= $t['id'] ?>" <?= (isset($dados['tutor_id']) && $dados['tutor_id'] == $t['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($t['nome']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label for="pet_id">Pet *</label>
                    <select id="pet_id" name="pet_id" class="form-control" required>
                        <option value="">Selecione o cliente primeiro...</option>
                        <?php if ($isEdit && !empty($pets_tutor)): ?>
                            <?php foreach ($pets_tutor as $p): ?>
                            <option value="<?= $p['id'] ?>" <?= ($dados['pet_id'] == $p['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($p['nome']) ?>
                            </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="servico_id">Serviço *</label>
                    <select id="servico_id" name="servico_id" class="form-control" required>
                        <option value="">Selecione...</option>
                        <?php foreach ($servicos as $s): ?>
                        <option value="<?= $s['id'] ?>" <?= (isset($dados['servico_id']) && $dados['servico_id'] == $s['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($s['nome']) ?> - <?= formatarMoeda($s['preco']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label for="profissional_id">Profissional</label>
                    <select id="profissional_id" name="profissional_id" class="form-control">
                        <option value="">Sem preferência</option>
                        <?php foreach ($profissionais as $pr): ?>
                        <option value="<?= $pr['id'] ?>" <?= (isset($dados['profissional_id']) && $dados['profissional_id'] == $pr['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($pr['nome']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="data">Data *</label>
                    <input type="date" id="data" name="data" class="form-control" required value="<?= $dados['data'] ?? date('Y-m-d') ?>">
                </div>
                <div class="form-group col-md-4">
                    <label for="hora">Hora *</label>
                    <input type="time" id="hora" name="hora" class="form-control" required value="<?= isset($dados['hora']) ? substr($dados['hora'], 0, 5) : '' ?>">
                </div>
                <?php if ($isEdit): ?>
                <div class="form-group col-md-4">
                    <label for="status">Status</label>
                    <select id="status" name="status" class="form-control" required>
                        <?php foreach (['agendado', 'confirmado', 'em_atendimento', 'finalizado', 'cancelado', 'faltou'] as $s): ?>
                        <option value="<?= $s ?>" <?= ($dados['status'] == $s) ? 'selected' : '' ?>><?= ucfirst(str_replace('_', ' ', $s)) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="observacoes">Observações</label>
                <textarea id="observacoes" name="observacoes" class="form-control" rows="2"><?= htmlspecialchars($dados['observacoes'] ?? '') ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">💾 Salvar</button>
                <a href="?page=agenda&action=list" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<script>
function carregarPets(tutorId) {
    var select = document.getElementById('pet_id');
    select.innerHTML = '<option value="">Carregando...</option>';
    if (!tutorId) {
        select.innerHTML = '<option value="">Selecione o cliente primeiro...</option>';
        return;
    }
    fetch('?page=pets&action=buscar&termo=&tutor_id=' + tutorId)
        .then(r => r.json())
        .then(pets => {
            select.innerHTML = '<option value="">Selecione o pet...</option>';
            pets.forEach(p => {
                select.innerHTML += '<option value="' + p.id + '">' + p.nome + '</option>';
            });
        })
        .catch(() => {
            select.innerHTML = '<option value="">Erro ao carregar pets</option>';
        });
}
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../template.php';
?>
