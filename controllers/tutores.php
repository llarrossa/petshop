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
        if (isset($_GET['nome'])) {
            $filtros['nome'] = sanitize($_GET['nome']);
        }
        if (isset($_GET['status'])) {
            $filtros['status'] = sanitize($_GET['status']);
        }
        $filtros['orderby'] = $_GET['orderby'] ?? 'id';
        $filtros['order']   = $_GET['order'] ?? 'asc';

        $tutores = $tutor->getAll($filtros);
        include __DIR__ . '/../views/tutores/list.php';
        break;

    case 'create':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
                $_SESSION['success'] = 'Tutor cadastrado com sucesso!';
                header('Location: ?page=tutores&action=list');
                exit;
            } else {
                $_SESSION['error'] = 'Erro ao cadastrar tutor.';
            }
        }
        include __DIR__ . '/../views/tutores/form.php';
        break;

    case 'edit':
        $id = (int)$_GET['id'];
        $dados = $tutor->getById($id);

        if (!$dados) {
            $_SESSION['error'] = 'Tutor não encontrado.';
            header('Location: ?page=tutores&action=list');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
                $_SESSION['success'] = 'Tutor atualizado com sucesso!';
                header('Location: ?page=tutores&action=list');
                exit;
            } else {
                $_SESSION['error'] = 'Erro ao atualizar tutor.';
            }
        }
        include __DIR__ . '/../views/tutores/form.php';
        break;

    case 'view':
        $id = (int)$_GET['id'];
        $dados = $tutor->getById($id);

        if (!$dados) {
            $_SESSION['error'] = 'Tutor não encontrado.';
            header('Location: ?page=tutores&action=list');
            exit;
        }

        $pets = $tutor->getPets($id);
        $historico_vendas = $tutor->getHistoricoVendas($id);
        $agendamentos = $tutor->getAgendamentos($id);

        include __DIR__ . '/../views/tutores/view.php';
        break;

    case 'delete':
        $id = (int)$_GET['id'];

        if ($tutor->delete($id)) {
            $_SESSION['success'] = 'Tutor excluído com sucesso!';
        } else {
            $_SESSION['error'] = 'Erro ao excluir tutor. Verifique se existem registros vinculados.';
        }

        header('Location: ?page=tutores&action=list');
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
        header('Location: ?page=tutores&action=list');
        exit;
}
