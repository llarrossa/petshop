<?php
/**
 * Controller de Pets
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Pet.class.php';
require_once __DIR__ . '/../classes/Tutor.class.php';

$action = $_GET['action'] ?? 'list';
$pet = new Pet();
$tutorObj = new Tutor();

switch ($action) {
    case 'list':
        // Filtros
        $busca = $_GET['busca'] ?? '';
        $tutor_id = $_GET['tutor_id'] ?? '';
        $especie = $_GET['especie'] ?? '';

        // Buscar pets
        $where = [];
        $params = [];

        if ($busca) {
            $where[] = "(p.nome LIKE :busca OR p.raca LIKE :busca)";
            $params[':busca'] = "%$busca%";
        }

        if ($tutor_id) {
            $where[] = "p.tutor_id = :tutor_id";
            $params[':tutor_id'] = $tutor_id;
        }

        if ($especie) {
            $where[] = "p.especie = :especie";
            $params[':especie'] = $especie;
        }

        $whereClause = $where ? 'AND ' . implode(' AND ', $where) : '';

        $sort_col_map = [
            'id'         => 'p.id',
            'nome'       => 'p.nome',
            'especie'    => 'p.especie',
            'raca'       => 'p.raca',
            'sexo'       => 'p.sexo',
            'tutor_nome' => 't.nome',
            'status'     => 'p.status',
        ];
        $orderby_key = $_GET['orderby'] ?? 'id';
        $sort_col = $sort_col_map[$orderby_key] ?? 'p.id';
        $sort_dir = (isset($_GET['order']) && strtolower($_GET['order']) === 'desc') ? 'DESC' : 'ASC';

        $sql = "SELECT p.*, t.nome as tutor_nome, t.telefone as tutor_telefone
                FROM pets p
                LEFT JOIN tutors t ON p.tutor_id = t.id
                WHERE p.company_id = :company_id $whereClause
                ORDER BY $sort_col $sort_dir";

        $params[':company_id'] = getCompanyId();
        $db = Database::getInstance();
        $pets = $db->query($sql, $params);

        // Buscar tutores para o filtro
        $tutores = $tutorObj->getAll();

        include __DIR__ . '/../views/pets/list.php';
        break;

    case 'create':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pet->tutor_id = sanitize($_POST['tutor_id']);
            $pet->nome = sanitize($_POST['nome']);
            $pet->especie = sanitize($_POST['especie']);
            $pet->raca = sanitize($_POST['raca']) ?: null;
            $pet->sexo = sanitize($_POST['sexo']);
            $pet->data_nascimento = sanitize($_POST['data_nascimento']) ?: null;
            $pet->peso = sanitize($_POST['peso']) ?: null;
            $pet->cor = sanitize($_POST['cor']) ?: null;
            $pet->porte = sanitize($_POST['porte']) ?: null;
            $pet->observacoes = sanitize($_POST['observacoes']) ?: null;
            $pet->status = 'ativo';

            if ($pet->create()) {
                $_SESSION['success'] = 'Pet cadastrado com sucesso!';
                header('Location: ?page=pets&action=list');
                exit;
            } else {
                $_SESSION['error'] = 'Erro ao cadastrar pet.';
            }
        }

        // Buscar tutores para o select
        $tutores = $tutorObj->getAll();
        $dados = [];
        include __DIR__ . '/../views/pets/form.php';
        break;

    case 'edit':
        $id = (int)$_GET['id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pet->id = $id;
            $pet->tutor_id = sanitize($_POST['tutor_id']);
            $pet->nome = sanitize($_POST['nome']);
            $pet->especie = sanitize($_POST['especie']);
            $pet->raca = sanitize($_POST['raca']) ?: null;
            $pet->sexo = sanitize($_POST['sexo']);
            $pet->data_nascimento = sanitize($_POST['data_nascimento']) ?: null;
            $pet->peso = sanitize($_POST['peso']) ?: null;
            $pet->cor = sanitize($_POST['cor']) ?: null;
            $pet->porte = sanitize($_POST['porte']) ?: null;
            $pet->observacoes = sanitize($_POST['observacoes']) ?: null;
            $pet->status = sanitize($_POST['status']);

            if ($pet->update()) {
                $_SESSION['success'] = 'Pet atualizado com sucesso!';
                header('Location: ?page=pets&action=list');
                exit;
            } else {
                $_SESSION['error'] = 'Erro ao atualizar pet.';
            }
        }

        $dados = $pet->getById($id);
        if (!$dados) {
            $_SESSION['error'] = 'Pet não encontrado.';
            header('Location: ?page=pets&action=list');
            exit;
        }

        // Buscar tutores para o select
        $tutores = $tutorObj->getAll();
        include __DIR__ . '/../views/pets/form.php';
        break;

    case 'view':
        $id = (int)$_GET['id'];
        $dados = $pet->getById($id);

        if (!$dados) {
            $_SESSION['error'] = 'Pet não encontrado.';
            header('Location: ?page=pets&action=list');
            exit;
        }

        // Buscar prontuário
        $prontuario = $pet->getProntuario($id);

        include __DIR__ . '/../views/pets/view.php';
        break;

    case 'delete':
        $id = (int)$_GET['id'];

        if ($pet->delete($id)) {
            $_SESSION['success'] = 'Pet removido com sucesso!';
        } else {
            $_SESSION['error'] = 'Erro ao remover pet. Verifique se não há vínculos.';
        }

        header('Location: ?page=pets&action=list');
        exit;
        break;

    case 'buscar':
        // AJAX - Buscar pets
        $termo = $_GET['termo'] ?? '';
        $tutor_id_busca = $_GET['tutor_id'] ?? '';

        $where_busca = "p.company_id = :company_id AND p.status = 'ativo'";
        $params_busca = [
            ':company_id' => getCompanyId(),
            ':termo' => "%$termo%"
        ];

        if ($tutor_id_busca) {
            $where_busca .= " AND p.tutor_id = :tutor_id";
            $params_busca[':tutor_id'] = (int)$tutor_id_busca;
        }

        if ($termo !== '') {
            $where_busca .= " AND (p.nome LIKE :termo OR t.nome LIKE :termo)";
        } else {
            unset($params_busca[':termo']);
        }

        $sql = "SELECT p.*, t.nome as tutor_nome
                FROM pets p
                LEFT JOIN tutors t ON p.tutor_id = t.id
                WHERE $where_busca
                ORDER BY p.nome ASC
                LIMIT 20";

        $db = Database::getInstance();
        $results = $db->query($sql, $params_busca);

        header('Content-Type: application/json');
        echo json_encode($results);
        exit;
        break;

    default:
        header('Location: ?page=pets&action=list');
        exit;
}
