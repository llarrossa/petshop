<?php
/**
 * Controller de Serviços
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Servico.class.php';

verificarLogin();

if (!moduloDisponivel('servicos')) {
    $_SESSION['error'] = 'Módulo não disponível no seu plano.';
    header('Location: ?page=dashboard');
    exit;
}

$servico  = new Servico();
$action   = $_GET['action'] ?? 'list';
$back_url = '?' . ($_SESSION['servicos_qs'] ?? 'page=servicos&action=list');

switch ($action) {
    case 'list':
        // Filtros — usar trim(strip_tags) para não causar duplo escape com htmlspecialchars na view
        $filtros = [];
        if (isset($_GET['nome'])      && $_GET['nome']      !== '') $filtros['nome']      = trim(strip_tags($_GET['nome']));
        if (isset($_GET['categoria']) && $_GET['categoria'] !== '') $filtros['categoria'] = trim(strip_tags($_GET['categoria']));
        if (isset($_GET['status'])    && $_GET['status']    !== '') $filtros['status']    = trim(strip_tags($_GET['status']));

        $filtros['orderby'] = $_GET['orderby'] ?? 'id';
        $filtros['order']   = $_GET['order']   ?? 'asc';

        // Persiste filtros na sessão
        $qs_params = ['page' => 'servicos', 'action' => 'list'];
        foreach (['nome', 'categoria', 'status', 'orderby', 'order', 'p'] as $k) {
            if (isset($_GET[$k]) && $_GET[$k] !== '') $qs_params[$k] = $_GET[$k];
        }
        $_SESSION['servicos_qs'] = http_build_query($qs_params);
        $back_url = '?' . $_SESSION['servicos_qs'];

        // Paginação
        $por_pagina    = 20;
        $pagina_atual  = max(1, (int)($_GET['p'] ?? 1));
        $total         = $servico->count($filtros);
        $total_paginas = (int)ceil($total / $por_pagina);
        $filtros['limit']  = $por_pagina;
        $filtros['offset'] = ($pagina_atual - 1) * $por_pagina;

        $servicos   = $servico->getAll($filtros);
        $categorias = $servico->getCategorias();
        include __DIR__ . '/../views/servicos/list.php';
        break;

    case 'create':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $servico->nome          = trim(strip_tags($_POST['nome']));
            $servico->descricao     = trim(strip_tags($_POST['descricao'] ?? ''));
            $servico->preco         = (float)$_POST['preco'];
            $servico->duracao_media = (int)($_POST['duracao_media'] ?? 0) ?: null;
            $servico->categoria     = trim(strip_tags($_POST['categoria'] ?? ''));
            $servico->status        = 'ativo';

            if ($servico->create()) {
                $_SESSION['success'] = 'Serviço cadastrado com sucesso!';
                header('Location: ' . $back_url);
                exit;
            } else {
                $_SESSION['error'] = 'Erro ao cadastrar serviço.';
            }
        }

        $categorias = $servico->getCategorias();
        include __DIR__ . '/../views/servicos/form.php';
        break;

    case 'edit':
        $id    = (int)$_GET['id'];
        $dados = $servico->getById($id);

        if (!$dados) {
            $_SESSION['error'] = 'Serviço não encontrado.';
            header('Location: ' . $back_url);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $servico->id            = $id;
            $servico->nome          = trim(strip_tags($_POST['nome']));
            $servico->descricao     = trim(strip_tags($_POST['descricao'] ?? ''));
            $servico->preco         = (float)$_POST['preco'];
            $servico->duracao_media = (int)($_POST['duracao_media'] ?? 0) ?: null;
            $servico->categoria     = trim(strip_tags($_POST['categoria'] ?? ''));
            $servico->status        = trim(strip_tags($_POST['status']));

            if ($servico->update()) {
                $_SESSION['success'] = 'Serviço atualizado com sucesso!';
                header('Location: ' . $back_url);
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

        header('Location: ' . $back_url);
        exit;
        break;

    case 'buscar':
        header('Content-Type: application/json');
        $termo      = trim(strip_tags($_GET['termo'] ?? ''));
        $filtros    = ['nome' => $termo, 'status' => 'ativo'];
        $resultados = $servico->getAll($filtros);
        echo json_encode($resultados);
        exit;
        break;

    default:
        header('Location: ?page=servicos&action=list');
        exit;
}
