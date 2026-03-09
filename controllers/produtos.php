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
        if (isset($_GET['nome'])) {
            $filtros['nome'] = sanitize($_GET['nome']);
        }
        if (isset($_GET['categoria'])) {
            $filtros['categoria'] = sanitize($_GET['categoria']);
        }
        if (isset($_GET['estoque_baixo']) && $_GET['estoque_baixo'] == '1') {
            $filtros['estoque_baixo'] = true;
        }

        $filtros['orderby'] = $_GET['orderby'] ?? 'id';
        $filtros['order']   = $_GET['order'] ?? 'asc';

        $produtos = $produto->getAll($filtros);
        $categorias = $produto->getCategorias();
        include __DIR__ . '/../views/produtos/list.php';
        break;

    case 'create':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $produto->nome = sanitize($_POST['nome']);
            $produto->descricao = sanitize($_POST['descricao'] ?? '');
            $produto->sku = sanitize($_POST['sku'] ?? '');
            $produto->categoria = sanitize($_POST['categoria'] ?? '');
            $produto->preco_venda = (float)$_POST['preco_venda'];
            $produto->preco_custo = (float)($_POST['preco_custo'] ?? 0);
            $produto->estoque_atual = (int)($_POST['estoque_atual'] ?? 0);
            $produto->estoque_minimo = (int)($_POST['estoque_minimo'] ?? 0);
            $produto->unidade = sanitize($_POST['unidade'] ?? 'UN');
            $produto->status = 'ativo';

            if ($produto->create()) {
                // Registrar movimentação inicial de estoque se houver
                if ($produto->estoque_atual > 0) {
                    $produto->registrarMovimentacao(
                        $produto->id,
                        'entrada',
                        $produto->estoque_atual,
                        'Estoque inicial',
                        $_SESSION['user_id']
                    );
                }

                $_SESSION['success'] = 'Produto cadastrado com sucesso!';
                header('Location: ?page=produtos&action=list');
                exit;
            } else {
                $_SESSION['error'] = 'Erro ao cadastrar produto.';
            }
        }

        $categorias = $produto->getCategorias();
        include __DIR__ . '/../views/produtos/form.php';
        break;

    case 'edit':
        $id = (int)$_GET['id'];
        $dados = $produto->getById($id);

        if (!$dados) {
            $_SESSION['error'] = 'Produto não encontrado.';
            header('Location: ?page=produtos&action=list');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $produto->id = $id;
            $produto->nome = sanitize($_POST['nome']);
            $produto->descricao = sanitize($_POST['descricao'] ?? '');
            $produto->sku = sanitize($_POST['sku'] ?? '');
            $produto->categoria = sanitize($_POST['categoria'] ?? '');
            $produto->preco_venda = (float)$_POST['preco_venda'];
            $produto->preco_custo = (float)($_POST['preco_custo'] ?? 0);
            $produto->estoque_minimo = (int)($_POST['estoque_minimo'] ?? 0);
            $produto->unidade = sanitize($_POST['unidade'] ?? 'UN');
            $produto->status = sanitize($_POST['status']);

            if ($produto->update()) {
                $_SESSION['success'] = 'Produto atualizado com sucesso!';
                header('Location: ?page=produtos&action=list');
                exit;
            } else {
                $_SESSION['error'] = 'Erro ao atualizar produto.';
            }
        }

        $categorias = $produto->getCategorias();
        include __DIR__ . '/../views/produtos/form.php';
        break;

    case 'view':
        $id = (int)$_GET['id'];
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

        if ($produto->delete($id)) {
            $_SESSION['success'] = 'Produto excluído com sucesso!';
        } else {
            $_SESSION['error'] = 'Erro ao excluir produto. Verifique se existem registros vinculados.';
        }

        header('Location: ?page=produtos&action=list');
        exit;
        break;

    case 'movimentacao':
        $id = (int)$_GET['id'];
        $dados = $produto->getById($id);

        if (!$dados) {
            $_SESSION['error'] = 'Produto não encontrado.';
            header('Location: ?page=produtos&action=list');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tipo = sanitize($_POST['tipo']);
            $quantidade = (int)$_POST['quantidade'];
            $motivo = sanitize($_POST['motivo']);

            if ($produto->registrarMovimentacao($id, $tipo, $quantidade, $motivo, $_SESSION['user_id'])) {
                $_SESSION['success'] = 'Movimentação registrada com sucesso!';
                header('Location: ?page=produtos&action=view&id=' . $id);
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
