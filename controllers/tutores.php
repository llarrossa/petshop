<?php
/**
 * Controller de Tutores
 * Processa requisições HTTP para operações de tutores
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Tutor.class.php';

verificarLogin();

$tutor = new Tutor();
$action = $_GET['action'] ?? 'list';

switch ($action) {
    case 'list':
        $filtros = [];
        if (isset($_GET['nome']))        $filtros['nome']        = sanitize($_GET['nome']);
        if (isset($_GET['status']))      $filtros['status']      = sanitize($_GET['status']);
        if (isset($_GET['com_vinculo'])) $filtros['com_vinculo'] = sanitize($_GET['com_vinculo']);
        $filtros['orderby'] = $_GET['orderby'] ?? 'id';
        $filtros['order']   = $_GET['order']   ?? 'asc';

        // Persiste os filtros na sessão para restaurar ao voltar de view/edit
        $qs_params = ['page' => 'clientes', 'action' => 'list'];
        foreach (['nome', 'status', 'com_vinculo', 'orderby', 'order', 'p'] as $k) {
            if (isset($_GET[$k]) && $_GET[$k] !== '') $qs_params[$k] = $_GET[$k];
        }
        $_SESSION['clientes_qs'] = http_build_query($qs_params);

        $por_pagina   = 20;
        $pagina_atual = max(1, (int)($_GET['p'] ?? 1));
        $total        = $tutor->count($filtros);
        $total_paginas = (int)ceil($total / $por_pagina);
        $filtros['limit']  = $por_pagina;
        $filtros['offset'] = ($pagina_atual - 1) * $por_pagina;

        $tutores = $tutor->getAll($filtros);
        include __DIR__ . '/../views/tutores/list.php';
        break;

    case 'create':
        $back_url = '?' . ($_SESSION['clientes_qs'] ?? 'page=clientes&action=list');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            validateCsrfToken();
            $tutor->nome = sanitize($_POST['nome']);
            $tutor->cpf = sanitize($_POST['cpf'] ?? '');
            $tutor->telefone = sanitize($_POST['telefone'] ?? '');
            $tutor->whatsapp = sanitize($_POST['whatsapp'] ?? '');
            $tutor->email = sanitize($_POST['email'] ?? '');
            $tutor->endereco = sanitize($_POST['endereco'] ?? '');
            $tutor->cidade = sanitize($_POST['cidade'] ?? '');
            $tutor->estado = sanitize($_POST['estado'] ?? '');
            $tutor->cep = sanitize($_POST['cep'] ?? '');
            $tutor->observacoes = sanitize($_POST['observacoes'] ?? '');
            $tutor->status = 'ativo';

            if ($tutor->create()) {
                $_SESSION['success'] = 'Cliente cadastrado com sucesso!';
                header('Location: ' . $back_url);
                exit;
            } else {
                $_SESSION['error'] = 'Erro ao cadastrar cliente.';
            }
        }
        include __DIR__ . '/../views/tutores/form.php';
        break;

    case 'edit':
        $back_url = '?' . ($_SESSION['clientes_qs'] ?? 'page=clientes&action=list');
        $id = (int)$_GET['id'];
        $dados = $tutor->getById($id);

        if (!$dados) {
            $_SESSION['error'] = 'Cliente não encontrado.';
            header('Location: ' . $back_url);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            validateCsrfToken();
            $tutor->id = $id;
            $tutor->nome = sanitize($_POST['nome']);
            $tutor->cpf = sanitize($_POST['cpf'] ?? '');
            $tutor->telefone = sanitize($_POST['telefone'] ?? '');
            $tutor->whatsapp = sanitize($_POST['whatsapp'] ?? '');
            $tutor->email = sanitize($_POST['email'] ?? '');
            $tutor->endereco = sanitize($_POST['endereco'] ?? '');
            $tutor->cidade = sanitize($_POST['cidade'] ?? '');
            $tutor->estado = sanitize($_POST['estado'] ?? '');
            $tutor->cep = sanitize($_POST['cep'] ?? '');
            $tutor->observacoes = sanitize($_POST['observacoes'] ?? '');
            $tutor->status = sanitize($_POST['status']);

            if ($tutor->update()) {
                $_SESSION['success'] = 'Cliente atualizado com sucesso!';
                header('Location: ' . $back_url);
                exit;
            } else {
                $_SESSION['error'] = 'Erro ao atualizar cliente.';
            }
        }
        include __DIR__ . '/../views/tutores/form.php';
        break;

    case 'view':
        $back_url = '?' . ($_SESSION['clientes_qs'] ?? 'page=clientes&action=list');
        $id = (int)$_GET['id'];
        $dados = $tutor->getById($id);

        if (!$dados) {
            $_SESSION['error'] = 'Cliente não encontrado.';
            header('Location: ' . $back_url);
            exit;
        }

        $pets = $tutor->getPets($id);
        $historico_vendas = $tutor->getHistoricoVendas($id);
        $agendamentos = $tutor->getAgendamentos($id);

        include __DIR__ . '/../views/tutores/view.php';
        break;

    case 'delete':
        $back_url = '?' . ($_SESSION['clientes_qs'] ?? 'page=clientes&action=list');
        $id = (int)$_GET['id'];

        if ($tutor->hasVinculos($id)) {
            $_SESSION['error'] = 'Não é possível excluir este cliente pois ele possui pets, agendamentos ou compras vinculados.';
        } elseif ($tutor->delete($id)) {
            $_SESSION['success'] = 'Cliente excluído com sucesso!';
        } else {
            $_SESSION['error'] = 'Erro ao excluir cliente.';
        }

        header('Location: ' . $back_url);
        exit;
        break;

    case 'buscar':
        // API para buscar tutores (AJAX)
        header('Content-Type: application/json');
        $termo = sanitize($_GET['termo'] ?? '');
        $filtros = ['nome' => $termo, 'status' => 'ativo'];
        $resultados = $tutor->getAll($filtros);
        echo json_encode($resultados);
        exit;
        break;

    default:
        header('Location: ?page=clientes&action=list');
        exit;
}
