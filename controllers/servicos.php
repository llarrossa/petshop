<?php
/**
 * Controller de Serviços
 * Processa requisições HTTP para operações de serviços
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Servico.class.php';

verificarLogin();

if (!moduloDisponivel('servicos')) {
    $_SESSION['error'] = 'Módulo não disponível no seu plano.';
    header('Location: ?page=dashboard');
    exit;
}

$servico = new Servico();
$action = $_GET['action'] ?? 'list';

switch ($action) {
    case 'list':
        $filtros = [];
        if (isset($_GET['nome']) && $_GET['nome'] !== '') {
            $filtros['nome'] = sanitize($_GET['nome']);
        }
        if (isset($_GET['categoria']) && $_GET['categoria'] !== '') {
            $filtros['categoria'] = sanitize($_GET['categoria']);
        }
        if (isset($_GET['status']) && $_GET['status'] !== '') {
            $filtros['status'] = sanitize($_GET['status']);
        }

        $filtros['orderby'] = $_GET['orderby'] ?? 'id';
        $filtros['order']   = $_GET['order'] ?? 'asc';

        $servicos = $servico->getAll($filtros);
        $categorias = $servico->getCategorias();
        include __DIR__ . '/../views/servicos/list.php';
        break;

    case 'create':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $servico->nome = sanitize($_POST['nome']);
            $servico->descricao = sanitize($_POST['descricao'] ?? '');
            $servico->preco = (float)$_POST['preco'];
            $servico->duracao_media = (int)($_POST['duracao_media'] ?? 0);
            $servico->categoria = sanitize($_POST['categoria'] ?? '');
            $servico->status = 'ativo';

            if ($servico->create()) {
                $_SESSION['success'] = 'Serviço cadastrado com sucesso!';
                header('Location: ?page=servicos&action=list');
                exit;
            } else {
                $_SESSION['error'] = 'Erro ao cadastrar serviço.';
            }
        }

        $categorias = $servico->getCategorias();
        include __DIR__ . '/../views/servicos/form.php';
        break;

    case 'edit':
        $id = (int)$_GET['id'];
        $dados = $servico->getById($id);

        if (!$dados) {
            $_SESSION['error'] = 'Serviço não encontrado.';
            header('Location: ?page=servicos&action=list');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $servico->id = $id;
            $servico->nome = sanitize($_POST['nome']);
            $servico->descricao = sanitize($_POST['descricao'] ?? '');
            $servico->preco = (float)$_POST['preco'];
            $servico->duracao_media = (int)($_POST['duracao_media'] ?? 0);
            $servico->categoria = sanitize($_POST['categoria'] ?? '');
            $servico->status = sanitize($_POST['status']);

            if ($servico->update()) {
                $_SESSION['success'] = 'Serviço atualizado com sucesso!';
                header('Location: ?page=servicos&action=list');
                exit;
            } else {
                $_SESSION['error'] = 'Erro ao atualizar serviço.';
            }
        }

        $categorias = $servico->getCategorias();
        include __DIR__ . '/../views/servicos/form.php';
        break;

    case 'delete':
        $id = (int)$_GET['id'];

        if ($servico->delete($id)) {
            $_SESSION['success'] = 'Serviço excluído com sucesso!';
        } else {
            $_SESSION['error'] = 'Erro ao excluir serviço. Verifique se existem registros vinculados.';
        }

        header('Location: ?page=servicos&action=list');
        exit;
        break;

    case 'buscar':
        header('Content-Type: application/json');
        $termo = sanitize($_GET['termo'] ?? '');
        $filtros = ['nome' => $termo, 'status' => 'ativo'];
        $resultados = $servico->getAll($filtros);
        echo json_encode($resultados);
        exit;
        break;

    default:
        header('Location: ?page=servicos&action=list');
        exit;
}
