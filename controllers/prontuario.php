<?php
/**
 * Controller de Prontuário
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Prontuario.class.php';
require_once __DIR__ . '/../classes/Pet.class.php';
require_once __DIR__ . '/../classes/Tutor.class.php';

$action        = $_GET['action'] ?? 'list';
$prontuarioObj = new Prontuario();
$petObj        = new Pet();
$tutorObj      = new Tutor();
$db            = Database::getInstance();

$back_url = '?' . ($_SESSION['prontuario_qs'] ?? 'page=prontuario&action=list');

switch ($action) {

    // --------------------------------------------------------
    // LISTAGEM
    // --------------------------------------------------------
    case 'list':
        $pet_id      = $_GET['pet_id']      ?? '';
        $cliente_id  = $_GET['cliente_id']  ?? '';
        $data_inicio = $_GET['data_inicio'] ?? '';
        $data_fim    = $_GET['data_fim']    ?? '';

        $qs_params = ['page' => 'prontuario', 'action' => 'list'];
        foreach (['pet_id', 'cliente_id', 'data_inicio', 'data_fim', 'p'] as $k) {
            if (isset($_GET[$k]) && $_GET[$k] !== '') $qs_params[$k] = $_GET[$k];
        }
        $_SESSION['prontuario_qs'] = http_build_query($qs_params);
        $back_url = '?' . $_SESSION['prontuario_qs'];

        $filtros = array_filter([
            'pet_id'      => $pet_id,
            'cliente_id'  => $cliente_id,
            'data_inicio' => $data_inicio,
            'data_fim'    => $data_fim,
        ]);

        $por_pagina   = 20;
        $pagina_atual = max(1, (int)($_GET['p'] ?? 1));
        $total        = $prontuarioObj->count($filtros);
        $total_paginas = (int)ceil($total / $por_pagina);
        $offset       = ($pagina_atual - 1) * $por_pagina;

        $prontuarios = $prontuarioObj->getAll(array_merge($filtros, [
            'limit'  => $por_pagina,
            'offset' => $offset,
        ]));

        $pets    = $petObj->getAll();
        $tutores = $tutorObj->getAll();

        $qs_filtros = http_build_query(array_filter([
            'page'        => 'prontuario',
            'action'      => 'list',
            'pet_id'      => $pet_id,
            'cliente_id'  => $cliente_id,
            'data_inicio' => $data_inicio,
            'data_fim'    => $data_fim,
        ]));

        include __DIR__ . '/../views/prontuario/list.php';
        break;

    // --------------------------------------------------------
    // CADASTRO
    // --------------------------------------------------------
    case 'create':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $prontuarioObj->pet_id          = (int)$_POST['pet_id'];
            $prontuarioObj->cliente_id      = (int)$_POST['cliente_id'];
            $prontuarioObj->profissional_id = !empty($_POST['profissional_id']) ? (int)$_POST['profissional_id'] : null;
            $prontuarioObj->data_atendimento = sanitize($_POST['data_atendimento']);
            $prontuarioObj->peso            = !empty($_POST['peso']) ? (float)$_POST['peso'] : null;
            $prontuarioObj->observacoes     = sanitize($_POST['observacoes']) ?: null;
            $prontuarioObj->recomendacoes   = sanitize($_POST['recomendacoes']) ?: null;

            if ($prontuarioObj->create()) {
                $_SESSION['success'] = 'Prontuário registrado com sucesso!';
                // Redireciona para o prontuário do pet se veio de lá
                $redirect = !empty($_POST['redirect_pet'])
                    ? '?page=pets&action=view&id=' . (int)$_POST['redirect_pet']
                    : $back_url;
                header('Location: ' . $redirect);
                exit;
            } else {
                $_SESSION['error'] = 'Erro ao registrar prontuário.';
            }
        }

        $dados         = [];
        $pets          = $petObj->getAll();
        $profissionais = $db->query(
            "SELECT id, nome FROM profissionais WHERE company_id = :company_id ORDER BY nome",
            [':company_id' => getCompanyId()]
        );

        // Pré-selecionar pet se veio de ?pet_id=X
        $pet_pre = null;
        if (!empty($_GET['pet_id'])) {
            $pet_pre = $petObj->getById((int)$_GET['pet_id']);
        }

        include __DIR__ . '/../views/prontuario/form.php';
        break;

    // --------------------------------------------------------
    // EDIÇÃO
    // --------------------------------------------------------
    case 'edit':
        $id = (int)$_GET['id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $prontuarioObj->id              = $id;
            $prontuarioObj->pet_id          = (int)$_POST['pet_id'];
            $prontuarioObj->cliente_id      = (int)$_POST['cliente_id'];
            $prontuarioObj->profissional_id = !empty($_POST['profissional_id']) ? (int)$_POST['profissional_id'] : null;
            $prontuarioObj->data_atendimento = sanitize($_POST['data_atendimento']);
            $prontuarioObj->peso            = !empty($_POST['peso']) ? (float)$_POST['peso'] : null;
            $prontuarioObj->observacoes     = sanitize($_POST['observacoes']) ?: null;
            $prontuarioObj->recomendacoes   = sanitize($_POST['recomendacoes']) ?: null;

            if ($prontuarioObj->update()) {
                $_SESSION['success'] = 'Prontuário atualizado com sucesso!';
                header('Location: ' . $back_url);
                exit;
            } else {
                $_SESSION['error'] = 'Erro ao atualizar prontuário.';
            }
        }

        $dados = $prontuarioObj->getById($id);
        if (!$dados) {
            $_SESSION['error'] = 'Registro não encontrado.';
            header('Location: ' . $back_url);
            exit;
        }

        $pets          = $petObj->getAll();
        $profissionais = $db->query(
            "SELECT id, nome FROM profissionais WHERE company_id = :company_id ORDER BY nome",
            [':company_id' => getCompanyId()]
        );
        $pet_pre = null;

        include __DIR__ . '/../views/prontuario/form.php';
        break;

    // --------------------------------------------------------
    // EXCLUSÃO
    // --------------------------------------------------------
    case 'delete':
        $id = (int)$_GET['id'];

        if ($prontuarioObj->delete($id)) {
            $_SESSION['success'] = 'Registro excluído com sucesso!';
        } else {
            $_SESSION['error'] = 'Erro ao excluir registro.';
        }

        header('Location: ' . $back_url);
        exit;

    default:
        header('Location: ?page=prontuario&action=list');
        exit;
}
