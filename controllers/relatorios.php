<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Venda.class.php';
require_once __DIR__ . '/../classes/Agenda.class.php';
require_once __DIR__ . '/../classes/Produto.class.php';
require_once __DIR__ . '/../classes/Servico.class.php';
require_once __DIR__ . '/../classes/Profissional.class.php';
require_once __DIR__ . '/../classes/Tutor.class.php';

verificarLogin();

if (!moduloDisponivel('relatorios')) {
    $_SESSION['error'] = 'Módulo não disponível no seu plano.';
    header('Location: ?page=dashboard');
    exit;
}

$relatorio = sanitize($_GET['relatorio'] ?? 'faturamento');
$data_inicio = sanitize($_GET['data_inicio'] ?? date('Y-m-01'));
$data_fim = sanitize($_GET['data_fim'] ?? date('Y-m-t'));

$dados_relatorio = [];

switch ($relatorio) {
    case 'faturamento':
        $venda = new Venda();
        $dados_relatorio['resumo'] = $venda->getFaturamentoPorPeriodo($data_inicio, $data_fim);
        $dados_relatorio['vendas'] = $venda->getAll([
            'data_inicio' => $data_inicio,
            'data_fim' => $data_fim,
            'status' => 'finalizada',
        ]);
        break;

    case 'servicos':
        $servico = new Servico();
        $dados_relatorio['mais_vendidos'] = $servico->getMaisVendidos(20, $data_inicio, $data_fim);
        $dados_relatorio['receita'] = $servico->getReceitaPorPeriodo($data_inicio, $data_fim);
        break;

    case 'produtos':
        $venda = new Venda();
        $dados_relatorio['mais_vendidos'] = $venda->getProdutosMaisVendidos(20, $data_inicio, $data_fim);
        $produto = new Produto();
        $dados_relatorio['estoque_baixo'] = $produto->getProdutosEstoqueBaixo();
        $dados_relatorio['valor_estoque'] = $produto->getValorTotalEstoque();
        break;

    case 'profissionais':
        $profissional = new Profissional();
        $lista = $profissional->getAll(['status' => 'ativo']);
        foreach ($lista as &$p) {
            $perf = $profissional->getPerformance($p['id'], $data_inicio, $data_fim);
            $p['total_servicos'] = $perf['total_servicos'];
            $p['receita_total'] = $perf['receita_total'];
            $p['comissao'] = $perf['comissao'];
        }
        $dados_relatorio['profissionais'] = $lista;
        break;

    case 'agendamentos':
        $agenda = new Agenda();
        $dados_relatorio['agendamentos'] = $agenda->getAll([
            'data_inicio' => $data_inicio,
            'data_fim' => $data_fim,
        ]);
        break;
}

include __DIR__ . '/../views/relatorios/index.php';
