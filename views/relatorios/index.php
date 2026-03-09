<?php
$page_title = 'Relatórios';
ob_start();
?>

<div class="page-header">
    <h2>Relatórios</h2>
</div>

<div class="card">
    <div class="card-header"><h3>Filtros</h3></div>
    <div class="card-body">
        <form method="GET" class="form-inline">
            <input type="hidden" name="page" value="relatorios">
            <select name="relatorio" class="form-control">
                <option value="faturamento" <?= ($relatorio == 'faturamento') ? 'selected' : '' ?>>Faturamento</option>
                <option value="servicos" <?= ($relatorio == 'servicos') ? 'selected' : '' ?>>Serviços</option>
                <option value="produtos" <?= ($relatorio == 'produtos') ? 'selected' : '' ?>>Produtos / Estoque</option>
                <option value="profissionais" <?= ($relatorio == 'profissionais') ? 'selected' : '' ?>>Profissionais</option>
                <option value="agendamentos" <?= ($relatorio == 'agendamentos') ? 'selected' : '' ?>>Agendamentos</option>
            </select>
            <label>De: <input type="date" name="data_inicio" value="<?= htmlspecialchars($data_inicio) ?>" class="form-control"></label>
            <label>Até: <input type="date" name="data_fim" value="<?= htmlspecialchars($data_fim) ?>" class="form-control"></label>
            <button type="submit" class="btn btn-secondary">Gerar</button>
        </form>
    </div>
</div>

<?php if ($relatorio == 'faturamento'): ?>
<div class="card">
    <div class="card-header"><h3>Faturamento — <?= date('d/m/Y', strtotime($data_inicio)) ?> a <?= date('d/m/Y', strtotime($data_fim)) ?></h3></div>
    <div class="card-body">
        <?php $r = $dados_relatorio['resumo'] ?? []; ?>
        <div class="stats-list">
            <div class="stat-item"><span class="stat-icon">💰</span><div><strong><?= formatarMoeda($r['faturamento_total'] ?? 0) ?></strong><small>Faturamento Total</small></div></div>
            <div class="stat-item"><span class="stat-icon">🛒</span><div><strong><?= $r['total_vendas'] ?? 0 ?></strong><small>Total de Vendas</small></div></div>
            <div class="stat-item"><span class="stat-icon">📊</span><div><strong><?= formatarMoeda($r['ticket_medio'] ?? 0) ?></strong><small>Ticket Médio</small></div></div>
        </div>
        <?php if (!empty($dados_relatorio['vendas'])): ?>
        <table class="table table-sm" style="margin-top:1rem;">
            <thead><tr><th>#</th><th>Data</th><th>Tutor</th><th>Valor</th><th>Forma Pgto</th></tr></thead>
            <tbody>
                <?php foreach ($dados_relatorio['vendas'] as $v): ?>
                <tr>
                    <td>#<?= $v['id'] ?></td>
                    <td><?= date('d/m/Y', strtotime($v['data'])) ?></td>
                    <td><?= htmlspecialchars($v['tutor_nome'] ?? '-') ?></td>
                    <td><?= formatarMoeda($v['valor_final']) ?></td>
                    <td><?= ucfirst($v['forma_pagamento']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<?php elseif ($relatorio == 'servicos'): ?>
<div class="card">
    <div class="card-header"><h3>Serviços Mais Realizados</h3></div>
    <div class="card-body">
        <p>Receita total no período: <strong><?= formatarMoeda($dados_relatorio['receita'] ?? 0) ?></strong></p>
        <?php if (!empty($dados_relatorio['mais_vendidos'])): ?>
        <table class="table table-sm">
            <thead><tr><th>Serviço</th><th>Qtd. Realizados</th><th>Receita</th></tr></thead>
            <tbody>
                <?php foreach ($dados_relatorio['mais_vendidos'] as $s): ?>
                <tr>
                    <td><?= htmlspecialchars($s['nome']) ?></td>
                    <td><?= $s['total_vendas'] ?></td>
                    <td><?= formatarMoeda($s['receita_total']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p class="text-muted text-center">Nenhum serviço realizado no período.</p>
        <?php endif; ?>
    </div>
</div>

<?php elseif ($relatorio == 'produtos'): ?>
<div class="card">
    <div class="card-header"><h3>Produtos Mais Vendidos</h3></div>
    <div class="card-body">
        <p>Valor total do estoque: <strong><?= formatarMoeda($dados_relatorio['valor_estoque'] ?? 0) ?></strong></p>
        <?php if (!empty($dados_relatorio['mais_vendidos'])): ?>
        <table class="table table-sm">
            <thead><tr><th>Produto</th><th>Qtd. Vendida</th><th>Receita</th></tr></thead>
            <tbody>
                <?php foreach ($dados_relatorio['mais_vendidos'] as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['nome_item']) ?></td>
                    <td><?= $p['quantidade_vendida'] ?></td>
                    <td><?= formatarMoeda($p['receita_total']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
        <?php if (!empty($dados_relatorio['estoque_baixo'])): ?>
        <h4 style="margin-top:1rem;">Produtos com Estoque Baixo</h4>
        <table class="table table-sm">
            <thead><tr><th>Produto</th><th>Estoque Atual</th><th>Estoque Mínimo</th></tr></thead>
            <tbody>
                <?php foreach ($dados_relatorio['estoque_baixo'] as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['nome']) ?></td>
                    <td class="text-danger"><strong><?= $p['estoque_atual'] ?></strong></td>
                    <td><?= $p['estoque_minimo'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<?php elseif ($relatorio == 'profissionais'): ?>
<div class="card">
    <div class="card-header"><h3>Performance dos Profissionais — <?= date('d/m/Y', strtotime($data_inicio)) ?> a <?= date('d/m/Y', strtotime($data_fim)) ?></h3></div>
    <div class="card-body">
        <?php if (!empty($dados_relatorio['profissionais'])): ?>
        <table class="table">
            <thead><tr><th>Profissional</th><th>Função</th><th>Serviços Realizados</th><th>Receita Gerada</th><th>Comissão</th></tr></thead>
            <tbody>
                <?php foreach ($dados_relatorio['profissionais'] as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['nome']) ?></td>
                    <td><?= htmlspecialchars($p['funcao'] ?? '-') ?></td>
                    <td><?= $p['total_servicos'] ?></td>
                    <td><?= formatarMoeda($p['receita_total']) ?></td>
                    <td><?= formatarMoeda($p['comissao']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p class="text-muted text-center">Nenhum profissional ativo.</p>
        <?php endif; ?>
    </div>
</div>

<?php elseif ($relatorio == 'agendamentos'): ?>
<div class="card">
    <div class="card-header"><h3>Agendamentos — <?= date('d/m/Y', strtotime($data_inicio)) ?> a <?= date('d/m/Y', strtotime($data_fim)) ?></h3></div>
    <div class="card-body">
        <?php if (!empty($dados_relatorio['agendamentos'])): ?>
        <table class="table table-sm">
            <thead><tr><th>Data</th><th>Hora</th><th>Pet</th><th>Tutor</th><th>Serviço</th><th>Profissional</th><th>Status</th></tr></thead>
            <tbody>
                <?php foreach ($dados_relatorio['agendamentos'] as $ag): ?>
                <tr>
                    <td><?= formatarData($ag['data']) ?></td>
                    <td><?= substr($ag['hora'], 0, 5) ?></td>
                    <td><?= htmlspecialchars($ag['pet_nome']) ?></td>
                    <td><?= htmlspecialchars($ag['tutor_nome']) ?></td>
                    <td><?= htmlspecialchars($ag['servico_nome']) ?></td>
                    <td><?= htmlspecialchars($ag['profissional_nome'] ?? '-') ?></td>
                    <td><span class="badge badge-secondary"><?= ucfirst(str_replace('_', ' ', $ag['status'])) ?></span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p class="text-muted text-center">Nenhum agendamento no período.</p>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<?php
$content = ob_get_clean();
include __DIR__ . '/../template.php';
?>
