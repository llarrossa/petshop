<?php
/**
 * Controller de Produtos
 * Processa requisições HTTP para operações de produtos
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Produto.class.php';

verificarLogin();

if (!moduloDisponivel('produtos')) {
    $_SESSION['error'] = 'Módulo não disponível no seu plano.';
    header('Location: ../public/dashboard.php');
    exit;
}

$produto = new Produto();
$action = $_GET['action'] ?? 'list';

switch ($action) {
    case 'list':
        $filtros = [];
        if (!empty($_GET['nome']))       $filtros['nome']       = sanitize($_GET['nome']);
        if (!empty($_GET['sku']))        $filtros['sku']        = sanitize($_GET['sku']);
        if (!empty($_GET['categoria']))  $filtros['categoria']  = sanitize($_GET['categoria']);
        if (!empty($_GET['status']))     $filtros['status']     = sanitize($_GET['status']);
        if (!empty($_GET['estoque_baixo']) && $_GET['estoque_baixo'] == '1') {
            $filtros['estoque_baixo'] = true;
        }

        $filtros['orderby'] = $_GET['orderby'] ?? 'id';
        $filtros['order']   = $_GET['order']   ?? 'asc';

        $total         = $produto->count($filtros);
        $pagina_atual  = max(1, (int)($_GET['pg'] ?? 1));
        $total_paginas = max(1, (int)ceil($total / ITEMS_PER_PAGE));
        $pagina_atual  = min($pagina_atual, $total_paginas);
        $filtros['limit']  = ITEMS_PER_PAGE;
        $filtros['offset'] = ($pagina_atual - 1) * ITEMS_PER_PAGE;

        $produtos = $produto->getAll($filtros);
        $categorias = $produto->getCategorias();
        include __DIR__ . '/../views/produtos/list.php';
        break;

    case 'create':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            validateCsrfToken();
            $sku = sanitize($_POST['sku'] ?? '');

            if (!empty($sku) && $produto->skuExiste($sku)) {
                $_SESSION['error'] = 'Já existe um produto cadastrado com este SKU.';
                // Preserva os dados preenchidos para reexibir o formulário
                $dados = [
                    'nome'           => $_POST['nome']           ?? '',
                    'sku'            => $_POST['sku']            ?? '',
                    'categoria'      => $_POST['categoria']      ?? '',
                    'preco_venda'    => $_POST['preco_venda']    ?? '',
                    'preco_custo'    => $_POST['preco_custo']    ?? '',
                    'estoque_atual'  => $_POST['estoque_atual']  ?? 0,
                    'estoque_minimo' => $_POST['estoque_minimo'] ?? 0,
                    'unidade'        => $_POST['unidade']        ?? 'UN',
                    'descricao'      => $_POST['descricao']      ?? '',
                ];
            } else {
                $produto->nome         = sanitize($_POST['nome']);
                $produto->descricao    = sanitize($_POST['descricao'] ?? '');
                $produto->sku          = $sku;
                $produto->categoria    = sanitize($_POST['categoria'] ?? '');
                $produto->preco_venda  = (float)$_POST['preco_venda'];
                $produto->preco_custo  = (float)($_POST['preco_custo'] ?? 0);
                $produto->estoque_atual  = (int)($_POST['estoque_atual'] ?? 0);
                $produto->estoque_minimo = (int)($_POST['estoque_minimo'] ?? 0);
                $produto->unidade = sanitize($_POST['unidade'] ?? 'UN');
                $produto->status  = 'ativo';

                if ($produto->create()) {
                    if ($produto->estoque_atual > 0) {
                        $produto->registrarMovimentacao(
                            $produto->id, 'entrada', $produto->estoque_atual,
                            'Estoque inicial', $_SESSION['user_id']
                        );
                    }
                    $_SESSION['success'] = 'Produto cadastrado com sucesso!';
                    $return_url = safeReturnUrl($_POST['return_url'] ?? '', '?page=produtos&action=list');
                    header('Location: ' . $return_url);
                    exit;
                } else {
                    $_SESSION['error'] = 'Erro ao cadastrar produto.';
                }
            }
        }

        $categorias = $produto->getCategorias();
        include __DIR__ . '/../views/produtos/form.php';
        break;

    case 'edit':
        $id = (int)$_GET['id'];
        if ($id <= 0) { header('Location: ?page=produtos&action=list'); exit; }
        $dados = $produto->getById($id);

        if (!$dados) {
            $_SESSION['error'] = 'Produto não encontrado.';
            header('Location: ?page=produtos&action=list');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            validateCsrfToken();
            $sku = sanitize($_POST['sku'] ?? '');

            if (!empty($sku) && $produto->skuExiste($sku, $id)) {
                $_SESSION['error'] = 'Já existe outro produto cadastrado com este SKU.';
            } else {
                $produto->id           = $id;
                $produto->nome         = sanitize($_POST['nome']);
                $produto->descricao    = sanitize($_POST['descricao'] ?? '');
                $produto->sku          = $sku;
                $produto->categoria    = sanitize($_POST['categoria'] ?? '');
                $produto->preco_venda  = (float)$_POST['preco_venda'];
                $produto->preco_custo  = (float)($_POST['preco_custo'] ?? 0);
                $produto->estoque_minimo = (int)($_POST['estoque_minimo'] ?? 0);
                $produto->unidade      = sanitize($_POST['unidade'] ?? 'UN');
                $produto->status       = sanitize($_POST['status']);

                if ($produto->update()) {
                    $_SESSION['success'] = 'Produto atualizado com sucesso!';
                    $return_url = safeReturnUrl($_POST['return_url'] ?? '', '?page=produtos&action=list');
                    header('Location: ' . $return_url);
                    exit;
                } else {
                    $_SESSION['error'] = 'Erro ao atualizar produto.';
                }
            }
        }

        $categorias = $produto->getCategorias();
        include __DIR__ . '/../views/produtos/form.php';
        break;

    case 'view':
        $id = (int)$_GET['id'];
        if ($id <= 0) { header('Location: ?page=produtos&action=list'); exit; }
        $dados = $produto->getById($id);

        if (!$dados) {
            $_SESSION['error'] = 'Produto não encontrado.';
            header('Location: ?page=produtos&action=list');
            exit;
        }

        $historico = $produto->getHistoricoMovimentacoes($id);

        include __DIR__ . '/../views/produtos/view.php';
        break;

    case 'delete':
        $id = (int)$_GET['id'];
        if ($id <= 0) { header('Location: ?page=produtos&action=list'); exit; }
        $return_url = safeReturnUrl($_GET['return_url'] ?? '', '?page=produtos&action=list');

        if ($produto->delete($id)) {
            $_SESSION['success'] = 'Produto excluído com sucesso!';
        } else {
            $_SESSION['error'] = 'Erro ao excluir produto. Verifique se existem registros vinculados.';
        }

        header('Location: ' . $return_url);
        exit;
        break;

    case 'movimentacao':
        $id = (int)$_GET['id'];
        if ($id <= 0) { header('Location: ?page=produtos&action=list'); exit; }
        $dados = $produto->getById($id);

        if (!$dados) {
            $_SESSION['error'] = 'Produto não encontrado.';
            header('Location: ?page=produtos&action=list');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            validateCsrfToken();
            $tipo      = sanitize($_POST['tipo']);
            $quantidade = max(1, (int)$_POST['quantidade']);
            $motivo    = sanitize($_POST['motivo']);

            // Bloquear saída que tornaria estoque negativo sem confirmação explícita
            if ($tipo === 'saida' && $quantidade > $dados['estoque_atual'] && empty($_POST['confirmar_negativo'])) {
                $_SESSION['error'] = 'Operação cancelada: o estoque ficaria negativo. Confirme a operação no formulário.';
            } elseif ($produto->registrarMovimentacao($id, $tipo, $quantidade, $motivo, $_SESSION['user_id'])) {
                $_SESSION['success'] = 'Movimentação registrada com sucesso!';
                $return_url = safeReturnUrl($_POST['return_url'] ?? '', '?page=produtos&action=list');
                header('Location: ?page=produtos&action=view&id=' . $id . '&return_url=' . urlencode($return_url));
                exit;
            } else {
                $_SESSION['error'] = 'Erro ao registrar movimentação.';
            }
        }

        include __DIR__ . '/../views/produtos/movimentacao.php';
        break;

    case 'buscar':
        // API para buscar produtos (AJAX)
        header('Content-Type: application/json');
        $termo = sanitize($_GET['termo'] ?? '');
        $filtros = ['nome' => $termo, 'status' => 'ativo'];
        $resultados = $produto->getAll($filtros);
        echo json_encode($resultados);
        exit;
        break;

    default:
        header('Location: ?page=produtos&action=list');
        exit;
}
