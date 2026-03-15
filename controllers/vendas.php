<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Venda.class.php';
require_once __DIR__ . '/../classes/Tutor.class.php';
require_once __DIR__ . '/../classes/Pet.class.php';
require_once __DIR__ . '/../classes/Servico.class.php';
require_once __DIR__ . '/../classes/Produto.class.php';
require_once __DIR__ . '/../classes/Profissional.class.php';

verificarLogin();

if (!moduloDisponivel('vendas')) {
    $_SESSION['error'] = 'Módulo não disponível no seu plano.';
    header('Location: ?page=dashboard');
    exit;
}

$venda = new Venda();
$action = $_GET['action'] ?? 'list';

switch ($action) {
    case 'list':
        $filtros = [];
        if (!empty($_GET['data_inicio'])) $filtros['data_inicio'] = sanitize($_GET['data_inicio']);
        if (!empty($_GET['data_fim']))    $filtros['data_fim']    = sanitize($_GET['data_fim']);
        if (!empty($_GET['status']))      $filtros['status']      = sanitize($_GET['status']);
        if (!empty($_GET['cliente']))     $filtros['cliente']     = sanitize($_GET['cliente']);

        if (!isset($filtros['data_inicio'])) {
            $filtros['data_inicio'] = date('Y-m-01');
            $filtros['data_fim']    = date('Y-m-t');
        }

        $total         = $venda->count($filtros);
        $pagina_atual  = max(1, (int)($_GET['pg'] ?? 1));
        $total_paginas = max(1, (int)ceil($total / ITEMS_PER_PAGE));
        $pagina_atual  = min($pagina_atual, $total_paginas);
        $filtros['limit']  = ITEMS_PER_PAGE;
        $filtros['offset'] = ($pagina_atual - 1) * ITEMS_PER_PAGE;

        $vendas = $venda->getAll($filtros);
        $resumo = $venda->getFaturamentoPorPeriodo(
            $filtros['data_inicio'],
            $filtros['data_fim'] ?? date('Y-m-t')
        );
        include __DIR__ . '/../views/vendas/list.php';
        break;

    case 'create':
        $tutores = (new Tutor())->getAll(['status' => 'ativo']);
        $servicos = (new Servico())->getAll(['status' => 'ativo']);
        $produtos = (new Produto())->getAll(['status' => 'ativo']);
        $profissionais = (new Profissional())->getAll(['status' => 'ativo']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            validateCsrfToken();
            $itens = [];
            $tipos = $_POST['tipo_item'] ?? [];
            $item_ids = $_POST['item_id'] ?? [];
            $nomes = $_POST['nome_item'] ?? [];
            $qtds = $_POST['quantidade'] ?? [];
            $precos = $_POST['preco_unitario'] ?? [];
            $prof_ids = $_POST['profissional_item_id'] ?? [];

            foreach ($tipos as $i => $tipo) {
                if (empty($item_ids[$i])) continue;
                $qtd = (float)$qtds[$i];
                $preco_unit = (float)$precos[$i];
                $itens[] = [
                    'tipo_item' => sanitize($tipo),
                    'item_id' => (int)$item_ids[$i],
                    'nome_item' => sanitize($nomes[$i]),
                    'quantidade' => $qtd,
                    'preco_unitario' => $preco_unit,
                    'preco_total' => $qtd * $preco_unit,
                    'profissional_id' => !empty($prof_ids[$i]) ? (int)$prof_ids[$i] : null,
                ];
            }

            if (empty($itens)) {
                $_SESSION['error'] = 'Adicione pelo menos um item à venda.';
                include __DIR__ . '/../views/vendas/form.php';
                break;
            }

            $venda->tutor_id = (int)$_POST['tutor_id'] ?: null;
            $venda->pet_id = (int)($_POST['pet_id'] ?? 0) ?: null;
            $venda->desconto = (float)($_POST['desconto'] ?? 0);
            $venda->forma_pagamento = sanitize($_POST['forma_pagamento']);
            $venda->observacoes = sanitize($_POST['observacoes'] ?? '');
            $venda->valor_total = array_sum(array_column($itens, 'preco_total'));
            $venda->valor_final = $venda->valor_total - $venda->desconto;
            $venda->status = 'finalizada';

            if ($venda->create($itens)) {
                $_SESSION['success'] = 'Venda registrada com sucesso!';
                header('Location: ?page=vendas&action=list');
                exit;
            } else {
                $_SESSION['error'] = 'Erro ao registrar venda.';
            }
        }
        include __DIR__ . '/../views/vendas/form.php';
        break;

    case 'view':
        $id = (int)$_GET['id'];
        $dados = $venda->getById($id);

        if (!$dados) {
            $_SESSION['error'] = 'Venda não encontrada.';
            header('Location: ?page=vendas&action=list');
            exit;
        }

        $itens = $venda->getItens($id);
        include __DIR__ . '/../views/vendas/view.php';
        break;

    case 'cancelar':
        $id = (int)$_GET['id'];
        $return_url = safeReturnUrl($_GET['return_url'] ?? '', '?page=vendas&action=list');

        if ($venda->cancelar($id)) {
            $_SESSION['success'] = 'Venda cancelada com sucesso!';
        } else {
            $_SESSION['error'] = 'Erro ao cancelar venda.';
        }
        header('Location: ' . $return_url);
        exit;
        break;

    default:
        header('Location: ?page=vendas&action=list');
        exit;
}
