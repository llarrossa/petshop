<?php
$page_title = 'Detalhes do Pet';
ob_start();
?>

<div class="page-header">
    <h1>🐕 Detalhes do Pet</h1>
    <div>
        <a href="?page=pets&action=edit&id=<?= $dados['id'] ?>" class="btn btn-warning">✏️ Editar</a>
        <a href="<?= htmlspecialchars($back_url) ?>" class="btn btn-secondary">← Voltar</a>
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
                        <td><?= htmlspecialchars(ucfirst($dados['especie'])) ?></td>
                    </tr>
                    <tr>
                        <th>Raça:</th>
                        <td><?= htmlspecialchars($dados['raca'] ?: '-') ?></td>
                    </tr>
                    <tr>
                        <th>Sexo:</th>
                        <td><?= $dados['sexo'] === 'macho' ? 'Macho' : ($dados['sexo'] === 'femea' ? 'Fêmea' : '-') ?></td>
                    </tr>
                    <tr>
                        <th>Data de Nascimento:</th>
                        <td><?= $dados['data_nascimento'] ? date('d/m/Y', strtotime($dados['data_nascimento'])) : '-' ?></td>
                    </tr>
                    <tr>
                        <th>Idade:</th>
                        <td>
                            <?php
                            if ($dados['data_nascimento']) {
                                $nasc  = new DateTime($dados['data_nascimento']);
                                $idade = (new DateTime())->diff($nasc);
                                if ($idade->y > 0)     echo $idade->y . ' ano' . ($idade->y > 1 ? 's' : '');
                                elseif ($idade->m > 0) echo $idade->m . ' ' . ($idade->m > 1 ? 'meses' : 'mês');
                                else                   echo $idade->d . ' dia' . ($idade->d > 1 ? 's' : '');
                            } else { echo '-'; }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Peso:</th>
                        <td><?= $dados['peso'] ? number_format($dados['peso'], 2, ',', '.') . ' kg' : '-' ?></td>
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
                            <span class="badge badge-<?= $dados['status'] === 'ativo' ? 'success' : 'secondary' ?>">
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

    <!-- Informações do Cliente -->
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h3>👤 Cliente Responsável</h3>
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
                </table>
                <a href="?page=clientes&action=view&id=<?= $dados['tutor_id'] ?>" class="btn btn-info btn-sm">
                    Ver Perfil do Cliente
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Agendamentos -->
<div class="card mb-4">
    <div class="card-header">
        <h3>📅 Agendamentos</h3>
    </div>
    <div class="card-body">
        <?php if (empty($agendamentos)): ?>
            <p class="text-center text-muted">Nenhum agendamento registrado para este pet.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Horário</th>
                            <th>Serviço</th>
                            <th>Profissional</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($agendamentos as $ag): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($ag['data'])) ?></td>
                                <td><?= substr($ag['hora'], 0, 5) ?></td>
                                <td><?= htmlspecialchars($ag['servico_nome'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($ag['profissional_nome'] ?? '-') ?></td>
                                <td>
                                    <?php
                                    $badge = match($ag['status'] ?? '') {
                                        'agendado'   => 'primary',
                                        'confirmado' => 'info',
                                        'concluido'  => 'success',
                                        'cancelado'  => 'danger',
                                        default      => 'secondary',
                                    };
                                    ?>
                                    <span class="badge badge-<?= $badge ?>">
                                        <?= ucfirst($ag['status'] ?? '-') ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Prontuário -->
<div class="card">
    <div class="card-header">
        <h3>📋 Prontuário / Histórico</h3>
    </div>
    <div class="card-body">
        <?php if (empty($prontuario)): ?>
            <p class="text-center text-muted">Nenhum registro no prontuário ainda.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Tipo</th>
                            <th>Título</th>
                            <th>Descrição</th>
                            <th>Profissional</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($prontuario as $reg): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($reg['data_atendimento'])) ?></td>
                                <td>
                                    <span class="badge badge-info">
                                        <?= htmlspecialchars(ucfirst($reg['tipo'])) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($reg['titulo'] ?? '-') ?></td>
                                <td><?= nl2br(htmlspecialchars($reg['descricao'])) ?></td>
                                <td><?= htmlspecialchars($reg['veterinario_nome'] ?? '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../template.php';
?>
