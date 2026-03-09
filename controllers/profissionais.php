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
$action = $_GET['action'] ?? 'list';

switch ($action) {
    case 'list':
        $filtros = [];
        if (isset($_GET['nome']) && $_GET['nome'] !== '') {
            $filtros['nome'] = sanitize($_GET['nome']);
        }
        if (isset($_GET['status']) && $_GET['status'] !== '') {
            $filtros['status'] = sanitize($_GET['status']);
        }
        $profissionais = $profissional->getAll($filtros);
        include __DIR__ . '/../views/profissionais/list.php';
        break;

    case 'create':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $profissional->nome = sanitize($_POST['nome']);
            $profissional->funcao = sanitize($_POST['funcao'] ?? '');
            $profissional->telefone = sanitize($_POST['telefone'] ?? '');
            $profissional->email = sanitize($_POST['email'] ?? '');
            $profissional->comissao = (float)($_POST['comissao'] ?? 0);
            $profissional->tipo_comissao = sanitize($_POST['tipo_comissao'] ?? 'percentual');
            $profissional->status = 'ativo';

            if ($profissional->create()) {
                $_SESSION['success'] = 'Profissional cadastrado com sucesso!';
                header('Location: ?page=profissionais&action=list');
                exit;
            } else {
                $_SESSION['error'] = 'Erro ao cadastrar profissional.';
            }
        }
        include __DIR__ . '/../views/profissionais/form.php';
        break;

    case 'edit':
        $id = (int)$_GET['id'];
        $dados = $profissional->getById($id);

        if (!$dados) {
            $_SESSION['error'] = 'Profissional não encontrado.';
            header('Location: ?page=profissionais&action=list');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $profissional->id = $id;
            $profissional->nome = sanitize($_POST['nome']);
            $profissional->funcao = sanitize($_POST['funcao'] ?? '');
            $profissional->telefone = sanitize($_POST['telefone'] ?? '');
            $profissional->email = sanitize($_POST['email'] ?? '');
            $profissional->comissao = (float)($_POST['comissao'] ?? 0);
            $profissional->tipo_comissao = sanitize($_POST['tipo_comissao'] ?? 'percentual');
            $profissional->status = sanitize($_POST['status']);

            if ($profissional->update()) {
                $_SESSION['success'] = 'Profissional atualizado com sucesso!';
                header('Location: ?page=profissionais&action=list');
                exit;
            } else {
                $_SESSION['error'] = 'Erro ao atualizar profissional.';
            }
        }
        include __DIR__ . '/../views/profissionais/form.php';
        break;

    case 'delete':
        $id = (int)$_GET['id'];
        if ($profissional->delete($id)) {
            $_SESSION['success'] = 'Profissional excluído com sucesso!';
        } else {
            $_SESSION['error'] = 'Erro ao excluir profissional. Verifique se existem registros vinculados.';
        }
        header('Location: ?page=profissionais&action=list');
        exit;
        break;

    default:
        header('Location: ?page=profissionais&action=list');
        exit;
}
