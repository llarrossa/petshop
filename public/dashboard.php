<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Venda.class.php';
require_once __DIR__ . '/../classes/Agenda.class.php';
require_once __DIR__ . '/../classes/Produto.class.php';
require_once __DIR__ . '/../classes/Tutor.class.php';
require_once __DIR__ . '/../classes/Pet.class.php';

verificarLogin();

$page_title = 'Dashboard';

// Instanciar classes
$venda = new Venda();
$agenda = new Agenda();
$produto = new Produto();
$tutor = new Tutor();
$pet = new Pet();

// Dados do dashboard
$hoje = date('Y-m-d');
$mes_atual_inicio = date('Y-m-01');
$mes_atual_fim = date('Y-m-t');

// Faturamento do dia
$vendas_hoje = $venda->getVendasDoDia($hoje);
$faturamento_hoje = 0;
foreach ($vendas_hoje as $v) {
    $faturamento_hoje += $v['valor_final'];
}

// Faturamento do mês
$faturamento_mes = $venda->getFaturamentoPorPeriodo($mes_atual_inicio, $mes_atual_fim);

// Agendamentos do dia
$agendamentos_hoje = $agenda->getAgendamentosDoDia($hoje);
$total_agendamentos_hoje = count($agendamentos_hoje);

// Próximos agendamentos
$proximos_agendamentos = $agenda->getProximosAgendamentos(5);

// Produtos com estoque baixo
$produtos_estoque_baixo = $produto->getProdutosEstoqueBaixo();

// Totalizadores
$total_tutores = $tutor->count(['status' => 'ativo']);
$total_pets = $pet->count(['status' => 'ativo']);
$total_produtos = $produto->count(['status' => 'ativo']);

ob_start();
?>

<div class="dashboard">
    <!-- Cards de resumo -->
    <div class="dashboard-cards">
        <div class="card card-primary">
            <div class="card-icon">💰</div>
            <div class="card-content">
                <h3>Faturamento Hoje</h3>
                <p class="value"><?= formatarMoeda($faturamento_hoje) ?></p>
                <small><?= count($vendas_hoje) ?> vendas</small>
            </div>
        </div>

        <div class="card card-success">
            <div class="card-icon">📈</div>
            <div class="card-content">
                <h3>Faturamento do Mês</h3>
                <p class="value"><?= formatarMoeda($faturamento_mes['faturamento_total'] ?? 0) ?></p>
                <small><?= $faturamento_mes['total_vendas'] ?? 0 ?> vendas</small>
            </div>
        </div>

        <div class="card card-info">
            <div class="card-icon">📅</div>
            <div class="card-content">
                <h3>Agendamentos Hoje</h3>
                <p class="value"><?= $total_agendamentos_hoje ?></p>
                <small>Atendimentos do dia</small>
            </div>
        </div>

        <div class="card card-warning">
            <div class="card-icon">⚠️</div>
            <div class="card-content">
                <h3>Estoque Baixo</h3>
                <p class="value"><?= count($produtos_estoque_baixo) ?></p>
                <small>Produtos</small>
            </div>
        </div>
    </div>

    <div class="dashboard-grid">
        <!-- Próximos agendamentos -->
        <?php if (moduloDisponivel('agenda')): ?>
        <div class="card">
            <div class="card-header">
                <h3>📅 Próximos Agendamentos</h3>
                <a href="?page=agenda" class="btn btn-sm">Ver todos</a>
            </div>
            <div class="card-body">
                <?php if (empty($proximos_agendamentos)): ?>
                    <p class="text-muted">Nenhum agendamento próximo.</p>
                <?php else: ?>
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Data/Hora</th>
                            <th>Pet</th>
                            <th>Tutor</th>
                            <th>Serviço</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($proximos_agendamentos as $ag): ?>
                        <tr>
                            <td><?= formatarData($ag['data']) ?> <?= substr($ag['hora'], 0, 5) ?></td>
                            <td><?= htmlspecialchars($ag['pet_nome']) ?></td>
                            <td><?= htmlspecialchars($ag['tutor_nome']) ?></td>
                            <td><?= htmlspecialchars($ag['servico_nome']) ?></td>
                            <td><span class="badge badge-<?= $ag['status'] ?>"><?= ucfirst($ag['status']) ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Produtos com estoque baixo -->
        <?php if (moduloDisponivel('produtos')): ?>
        <div class="card">
            <div class="card-header">
                <h3>📦 Produtos com Estoque Baixo</h3>
                <a href="?page=produtos&estoque_baixo=1" class="btn btn-sm">Ver todos</a>
            </div>
            <div class="card-body">
                <?php if (empty($produtos_estoque_baixo)): ?>
                    <p class="text-muted">Todos os produtos com estoque adequado.</p>
                <?php else: ?>
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Estoque Atual</th>
                            <th>Estoque Mínimo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($produtos_estoque_baixo, 0, 5) as $prod): ?>
                        <tr>
                            <td><?= htmlspecialchars($prod['nome']) ?></td>
                            <td class="text-danger"><strong><?= $prod['estoque_atual'] ?></strong></td>
                            <td><?= $prod['estoque_minimo'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Estatísticas gerais -->
        <div class="card">
            <div class="card-header">
                <h3>📊 Estatísticas Gerais</h3>
            </div>
            <div class="card-body">
                <div class="stats-list">
                    <div class="stat-item">
                        <span class="stat-icon">👥</span>
                        <div>
                            <strong><?= $total_tutores ?></strong>
                            <small>Tutores Ativos</small>
                        </div>
                    </div>
                    <div class="stat-item">
                        <span class="stat-icon">🐕</span>
                        <div>
                            <strong><?= $total_pets ?></strong>
                            <small>Pets Cadastrados</small>
                        </div>
                    </div>
                    <?php if (moduloDisponivel('produtos')): ?>
                    <div class="stat-item">
                        <span class="stat-icon">📦</span>
                        <div>
                            <strong><?= $total_produtos ?></strong>
                            <small>Produtos Ativos</small>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Vendas recentes -->
        <?php if (moduloDisponivel('vendas')): ?>
        <div class="card">
            <div class="card-header">
                <h3>💰 Últimas Vendas</h3>
                <a href="?page=vendas" class="btn btn-sm">Ver todas</a>
            </div>
            <div class="card-body">
                <?php if (empty($vendas_hoje)): ?>
                    <p class="text-muted">Nenhuma venda hoje.</p>
                <?php else: ?>
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tutor</th>
                            <th>Valor</th>
                            <th>Forma Pgto</th>
                            <th>Hora</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($vendas_hoje, 0, 5) as $vend): ?>
                        <tr>
                            <td>#<?= $vend['id'] ?></td>
                            <td><?= htmlspecialchars($vend['tutor_nome']) ?></td>
                            <td><?= formatarMoeda($vend['valor_final']) ?></td>
                            <td><?= ucfirst($vend['forma_pagamento']) ?></td>
                            <td><?= date('H:i', strtotime($vend['data'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../views/template.php';
?>
