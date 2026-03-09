<?php
$page_title = 'Detalhes do Pet';
ob_start();
?>

<div class="page-header">
    <h1>🐕 Detalhes do Pet</h1>
    <div>
        <a href="?page=pets&action=edit&id=<?= $dados['id'] ?>" class="btn btn-warning">✏️ Editar</a>
        <a href="?page=pets&action=list" class="btn btn-secondary">← Voltar</a>
    </div>
</div>

<div class="row">
    <!-- Informações do Pet -->
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h3>📋 Informações do Pet</h3>
            </div>
            <div class="card-body">
                <table class="table table-detail">
                    <tr>
                        <th width="40%">Nome:</th>
                        <td><strong><?= htmlspecialchars($dados['nome']) ?></strong></td>
                    </tr>
                    <tr>
                        <th>Espécie:</th>
                        <td><?= htmlspecialchars($dados['especie']) ?></td>
                    </tr>
                    <tr>
                        <th>Raça:</th>
                        <td><?= htmlspecialchars($dados['raca'] ?: '-') ?></td>
                    </tr>
                    <tr>
                        <th>Sexo:</th>
                        <td><?= $dados['sexo'] == 'macho' ? 'Macho' : ($dados['sexo'] == 'femea' ? 'Fêmea' : '-') ?></td>
                    </tr>
                    <tr>
                        <th>Data de Nascimento:</th>
                        <td>
                            <?php
                            if ($dados['data_nascimento']) {
                                echo date('d/m/Y', strtotime($dados['data_nascimento']));
                            } else {
                                echo '-';
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Idade:</th>
                        <td>
                            <?php
                            if ($dados['data_nascimento']) {
                                $nascimento = new DateTime($dados['data_nascimento']);
                                $hoje = new DateTime();
                                $idade = $hoje->diff($nascimento);

                                if ($idade->y > 0) {
                                    echo $idade->y . ' ano' . ($idade->y > 1 ? 's' : '');
                                } elseif ($idade->m > 0) {
                                    echo $idade->m . ' ' . ($idade->m > 1 ? 'meses' : 'mês');
                                } else {
                                    echo $idade->d . ' dia' . ($idade->d > 1 ? 's' : '');
                                }
                            } else {
                                echo '-';
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Peso:</th>
                        <td><?= $dados['peso'] ? $dados['peso'] . ' kg' : '-' ?></td>
                    </tr>
                    <tr>
                        <th>Cor:</th>
                        <td><?= htmlspecialchars($dados['cor'] ?: '-') ?></td>
                    </tr>
                    <tr>
                        <th>Porte:</th>
                        <td><?= htmlspecialchars($dados['porte'] ?: '-') ?></td>
                    </tr>
                    <tr>
                        <th>Status:</th>
                        <td>
                            <span class="badge badge-<?= $dados['status'] == 'ativo' ? 'success' : 'secondary' ?>">
                                <?= ucfirst($dados['status']) ?>
                            </span>
                        </td>
                    </tr>
                </table>

                <?php if ($dados['observacoes']): ?>
                <div class="mt-3">
                    <strong>Observações:</strong>
                    <p class="text-muted"><?= nl2br(htmlspecialchars($dados['observacoes'])) ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Informações do Tutor -->
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h3>👤 Tutor Responsável</h3>
            </div>
            <div class="card-body">
                <table class="table table-detail">
                    <tr>
                        <th width="40%">Nome:</th>
                        <td><?= htmlspecialchars($dados['tutor_nome'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <th>Telefone:</th>
                        <td><?= htmlspecialchars($dados['tutor_telefone'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <th>WhatsApp:</th>
                        <td><?= htmlspecialchars($dados['tutor_whatsapp'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <th>E-mail:</th>
                        <td><?= htmlspecialchars($dados['tutor_email'] ?? '-') ?></td>
                    </tr>
                </table>
                <a href="?page=tutores&action=view&id=<?= $dados['tutor_id'] ?>" class="btn btn-info btn-sm">
                    Ver Perfil do Tutor
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Prontuário -->
<?php if (!empty($prontuario)): ?>
<div class="card">
    <div class="card-header">
        <h3>📋 Prontuário / Histórico</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Tipo</th>
                        <th>Descrição</th>
                        <th>Profissional</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($prontuario as $registro): ?>
                        <tr>
                            <td><?= date('d/m/Y H:i', strtotime($registro['data_registro'])) ?></td>
                            <td>
                                <span class="badge badge-info">
                                    <?= htmlspecialchars($registro['tipo']) ?>
                                </span>
                            </td>
                            <td><?= nl2br(htmlspecialchars($registro['descricao'])) ?></td>
                            <td><?= htmlspecialchars($registro['profissional_nome'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php else: ?>
<div class="card">
    <div class="card-body text-center text-muted">
        <p>Nenhum registro no prontuário ainda.</p>
    </div>
</div>
<?php endif; ?>

<?php
$content = ob_get_clean();
include __DIR__ . '/../template.php';
?>
