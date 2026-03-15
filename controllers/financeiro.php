<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../database/connection.php';

verificarLogin();

if (!moduloDisponivel('financeiro')) {
    $_SESSION['error'] = 'Módulo não disponível no seu plano.';
    header('Location: ?page=dashboard');
    exit;
}

$db = Database::getInstance();
$company_id = getCompanyId();
$action = $_GET['action'] ?? 'list';

switch ($action) {
    case 'list':
        $data_inicio = sanitize($_GET['data_inicio'] ?? date('Y-m-01'));
        $data_fim = sanitize($_GET['data_fim'] ?? date('Y-m-t'));
        $tipo = sanitize($_GET['tipo'] ?? '');
        $status = sanitize($_GET['status'] ?? '');

        $sql = "SELECT * FROM financeiro WHERE company_id = :company_id
                AND DATE(data_pagamento) BETWEEN :data_inicio AND :data_fim";
        $params = [
            ':company_id' => $company_id,
            ':data_inicio' => $data_inicio,
            ':data_fim' => $data_fim,
        ];

        if ($tipo !== '') {
            $sql .= " AND tipo = :tipo";
            $params[':tipo'] = $tipo;
        }
        if ($status !== '') {
            $sql .= " AND status = :status";
            $params[':status'] = $status;
        }

        $sql .= " ORDER BY data_pagamento DESC";
        $lancamentos = $db->query($sql, $params);

        // Totais do período
        $sql_totais = "SELECT
            SUM(CASE WHEN tipo = 'receita' AND status = 'pago' THEN valor ELSE 0 END) as total_receitas,
            SUM(CASE WHEN tipo = 'despesa' AND status = 'pago' THEN valor ELSE 0 END) as total_despesas
            FROM financeiro
            WHERE company_id = :company_id
            AND DATE(data_pagamento) BETWEEN :data_inicio AND :data_fim";
        $totais = $db->queryOne($sql_totais, [
            ':company_id' => $company_id,
            ':data_inicio' => $data_inicio,
            ':data_fim' => $data_fim,
        ]);

        include __DIR__ . '/../views/financeiro/list.php';
        break;

    case 'create':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            validateCsrfToken();
            $sql = "INSERT INTO financeiro (company_id, tipo, categoria, descricao, valor, forma_pagamento, data_pagamento, status)
                    VALUES (:company_id, :tipo, :categoria, :descricao, :valor, :forma_pagamento, :data_pagamento, :status)";
            $params = [
                ':company_id' => $company_id,
                ':tipo' => sanitize($_POST['tipo']),
                ':categoria' => sanitize($_POST['categoria'] ?? ''),
                ':descricao' => sanitize($_POST['descricao']),
                ':valor' => (float)$_POST['valor'],
                ':forma_pagamento' => sanitize($_POST['forma_pagamento'] ?? '') ?: null,
                ':data_pagamento' => sanitize($_POST['data_pagamento']),
                ':status' => sanitize($_POST['status']),
            ];

            if ($db->execute($sql, $params)) {
                $_SESSION['success'] = 'Lançamento registrado com sucesso!';
                header('Location: ?page=financeiro&action=list');
                exit;
            } else {
                $_SESSION['error'] = 'Erro ao registrar lançamento.';
            }
        }
        include __DIR__ . '/../views/financeiro/form.php';
        break;

    case 'delete':
        $id = (int)$_GET['id'];
        $sql = "DELETE FROM financeiro WHERE id = :id AND company_id = :company_id";
        if ($db->execute($sql, [':id' => $id, ':company_id' => $company_id])) {
            $_SESSION['success'] = 'Lançamento excluído com sucesso!';
        } else {
            $_SESSION['error'] = 'Erro ao excluir lançamento.';
        }
        header('Location: ?page=financeiro&action=list');
        exit;
        break;

    default:
        header('Location: ?page=financeiro&action=list');
        exit;
}
