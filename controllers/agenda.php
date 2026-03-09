<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Agenda.class.php';
require_once __DIR__ . '/../classes/Tutor.class.php';
require_once __DIR__ . '/../classes/Pet.class.php';
require_once __DIR__ . '/../classes/Servico.class.php';
require_once __DIR__ . '/../classes/Profissional.class.php';

verificarLogin();

if (!moduloDisponivel('agenda')) {
    $_SESSION['error'] = 'Módulo não disponível no seu plano.';
    header('Location: ?page=dashboard');
    exit;
}

$agenda = new Agenda();
$action = $_GET['action'] ?? 'list';

switch ($action) {
    case 'list':
        $filtros = [];
        if (isset($_GET['data']) && $_GET['data'] !== '') {
            $filtros['data'] = sanitize($_GET['data']);
        }
        if (isset($_GET['status']) && $_GET['status'] !== '') {
            $filtros['status'] = sanitize($_GET['status']);
        }
        if (isset($_GET['profissional_id']) && $_GET['profissional_id'] !== '') {
            $filtros['profissional_id'] = (int)$_GET['profissional_id'];
        }
        $agendamentos = $agenda->getAll($filtros);
        $profissionais = (new Profissional())->getAll(['status' => 'ativo']);
        include __DIR__ . '/../views/agenda/list.php';
        break;

    case 'create':
        $tutores = (new Tutor())->getAll(['status' => 'ativo']);
        $servicos = (new Servico())->getAll(['status' => 'ativo']);
        $profissionais = (new Profissional())->getAll(['status' => 'ativo']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $agenda->pet_id = (int)$_POST['pet_id'];
            $agenda->tutor_id = (int)$_POST['tutor_id'];
            $agenda->servico_id = (int)$_POST['servico_id'];
            $agenda->profissional_id = (int)($_POST['profissional_id'] ?? 0) ?: null;
            $agenda->data = sanitize($_POST['data']);
            $agenda->hora = sanitize($_POST['hora']);
            $agenda->observacoes = sanitize($_POST['observacoes'] ?? '');
            $agenda->status = 'agendado';

            if ($agenda->create()) {
                $_SESSION['success'] = 'Agendamento criado com sucesso!';
                header('Location: ?page=agenda&action=list');
                exit;
            } else {
                $_SESSION['error'] = 'Erro ao criar agendamento. Verifique a disponibilidade do horário.';
            }
        }
        include __DIR__ . '/../views/agenda/form.php';
        break;

    case 'edit':
        $id = (int)$_GET['id'];
        $dados = $agenda->getById($id);

        if (!$dados) {
            $_SESSION['error'] = 'Agendamento não encontrado.';
            header('Location: ?page=agenda&action=list');
            exit;
        }

        $tutores = (new Tutor())->getAll(['status' => 'ativo']);
        $servicos = (new Servico())->getAll(['status' => 'ativo']);
        $profissionais = (new Profissional())->getAll(['status' => 'ativo']);
        $pets_tutor = (new Pet())->getAll(['tutor_id' => $dados['tutor_id']]);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $agenda->id = $id;
            $agenda->pet_id = (int)$_POST['pet_id'];
            $agenda->tutor_id = (int)$_POST['tutor_id'];
            $agenda->servico_id = (int)$_POST['servico_id'];
            $agenda->profissional_id = (int)($_POST['profissional_id'] ?? 0) ?: null;
            $agenda->data = sanitize($_POST['data']);
            $agenda->hora = sanitize($_POST['hora']);
            $agenda->observacoes = sanitize($_POST['observacoes'] ?? '');
            $agenda->status = sanitize($_POST['status']);

            if ($agenda->update()) {
                $_SESSION['success'] = 'Agendamento atualizado com sucesso!';
                header('Location: ?page=agenda&action=list');
                exit;
            } else {
                $_SESSION['error'] = 'Erro ao atualizar agendamento.';
            }
        }
        include __DIR__ . '/../views/agenda/form.php';
        break;

    case 'status':
        $id = (int)$_GET['id'];
        $novo_status = sanitize($_GET['novo_status'] ?? '');
        $status_validos = ['agendado', 'confirmado', 'em_atendimento', 'finalizado', 'cancelado', 'faltou'];

        if (in_array($novo_status, $status_validos) && $agenda->alterarStatus($id, $novo_status)) {
            $_SESSION['success'] = 'Status atualizado com sucesso!';
        } else {
            $_SESSION['error'] = 'Erro ao atualizar status.';
        }
        header('Location: ?page=agenda&action=list');
        exit;
        break;

    case 'delete':
        $id = (int)$_GET['id'];
        if ($agenda->delete($id)) {
            $_SESSION['success'] = 'Agendamento excluído com sucesso!';
        } else {
            $_SESSION['error'] = 'Erro ao excluir agendamento.';
        }
        header('Location: ?page=agenda&action=list');
        exit;
        break;

    default:
        header('Location: ?page=agenda&action=list');
        exit;
}
