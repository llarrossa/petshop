<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Profissional.class.php';

verificarLogin();

if (!moduloDisponivel('profissionais')) {
    $_SESSION['error'] = 'Módulo não disponível no seu plano.';
    header('Location: ?page=dashboard');
    exit;
}

$profissional = new Profissional();
$action       = $_GET['action'] ?? 'list';
$back_url     = '?' . ($_SESSION['profissionais_qs'] ?? 'page=profissionais&action=list');

switch ($action) {
    case 'list':
        $filtros = [];
        if (isset($_GET['nome'])   && $_GET['nome']   !== '') $filtros['nome']   = sanitize($_GET['nome']);
        if (isset($_GET['status']) && $_GET['status'] !== '') $filtros['status'] = sanitize($_GET['status']);

        $filtros['orderby'] = $_GET['orderby'] ?? 'nome';
        $filtros['order']   = $_GET['order']   ?? 'asc';

        // Persiste filtros na sessão
        $qs_params = ['page' => 'profissionais', 'action' => 'list'];
        foreach (['nome', 'status', 'orderby', 'order', 'p'] as $k) {
            if (isset($_GET[$k]) && $_GET[$k] !== '') $qs_params[$k] = $_GET[$k];
        }
        $_SESSION['profissionais_qs'] = http_build_query($qs_params);
        $back_url = '?' . $_SESSION['profissionais_qs'];

        // Paginação
        $por_pagina    = 20;
        $pagina_atual  = max(1, (int)($_GET['p'] ?? 1));
        $total         = $profissional->count($filtros);
        $total_paginas = (int)ceil($total / $por_pagina);
        $filtros['limit']  = $por_pagina;
        $filtros['offset'] = ($pagina_atual - 1) * $por_pagina;

        $profissionais = $profissional->getAll($filtros);
        include __DIR__ . '/../views/profissionais/list.php';
        break;

    case 'create':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            validateCsrfToken();
            $profissional->nome          = sanitize($_POST['nome']);
            $profissional->funcao        = sanitize($_POST['funcao'] ?? '');
            $profissional->telefone      = sanitize($_POST['telefone'] ?? '');
            $profissional->email         = sanitize($_POST['email'] ?? '');
            $profissional->comissao      = (float)($_POST['comissao'] ?? 0);
            $profissional->tipo_comissao = sanitize($_POST['tipo_comissao'] ?? 'percentual');
            $profissional->status        = 'ativo';

            if ($profissional->create()) {
                $_SESSION['success'] = 'Profissional cadastrado com sucesso!';
                header('Location: ' . $back_url);
                exit;
            } else {
                $_SESSION['error'] = 'Erro ao cadastrar profissional.';
            }
        }
        include __DIR__ . '/../views/profissionais/form.php';
        break;

    case 'edit':
        $id    = (int)$_GET['id'];
        $dados = $profissional->getById($id);

        if (!$dados) {
            $_SESSION['error'] = 'Profissional não encontrado.';
            header('Location: ' . $back_url);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            validateCsrfToken();
            $profissional->id            = $id;
            $profissional->nome          = sanitize($_POST['nome']);
            $profissional->funcao        = sanitize($_POST['funcao'] ?? '');
            $profissional->telefone      = sanitize($_POST['telefone'] ?? '');
            $profissional->email         = sanitize($_POST['email'] ?? '');
            $profissional->comissao      = (float)($_POST['comissao'] ?? 0);
            $profissional->tipo_comissao = sanitize($_POST['tipo_comissao'] ?? 'percentual');
            $profissional->status        = sanitize($_POST['status']);

            if ($profissional->update()) {
                $_SESSION['success'] = 'Profissional atualizado com sucesso!';
                header('Location: ' . $back_url);
                exit;
            } else {
                $_SESSION['error'] = 'Erro ao atualizar profissional.';
            }
        }
        include __DIR__ . '/../views/profissionais/form.php';
        break;

    case 'delete':
        $id = (int)$_GET['id'];

        if ($profissional->hasAgendamentosAtivos($id)) {
            $_SESSION['error'] = 'Não é possível excluir este profissional pois ele possui agendamentos ativos ou concluídos vinculados.';
        } elseif ($profissional->delete($id)) {
            $_SESSION['success'] = 'Profissional excluído com sucesso!';
        } else {
            $_SESSION['error'] = 'Erro ao excluir profissional.';
        }

        header('Location: ' . $back_url);
        exit;
        break;

    default:
        header('Location: ?page=profissionais&action=list');
        exit;
}
