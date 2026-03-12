<?php
/**
 * Classe Profissional
 * Gerencia operações CRUD de profissionais/funcionários
 */

require_once __DIR__ . '/../database/connection.php';

class Profissional {
    private $db;
    private $company_id;

    // Propriedades
    public $id;
    public $nome;
    public $funcao;
    public $telefone;
    public $email;
    public $comissao;
    public $tipo_comissao;
    public $status;

    /**
     * Construtor
     */
    public function __construct($company_id = null) {
        $this->db = Database::getInstance();
        $this->company_id = $company_id ?? getCompanyId();
    }

    /**
     * Criar novo profissional
     */
    public function create() {
        $sql = "INSERT INTO profissionais (company_id, nome, funcao, telefone, email, comissao, tipo_comissao, status)
                VALUES (:company_id, :nome, :funcao, :telefone, :email, :comissao, :tipo_comissao, :status)";

        $params = [
            ':company_id' => $this->company_id,
            ':nome' => $this->nome,
            ':funcao' => $this->funcao,
            ':telefone' => $this->telefone,
            ':email' => $this->email,
            ':comissao' => $this->comissao ?? 0,
            ':tipo_comissao' => $this->tipo_comissao ?? 'percentual',
            ':status' => $this->status ?? 'ativo'
        ];

        if ($this->db->execute($sql, $params)) {
            $this->id = $this->db->lastInsertId();
            return true;
        }
        return false;
    }

    /**
     * Atualizar profissional existente
     */
    public function update() {
        $sql = "UPDATE profissionais SET
                nome = :nome,
                funcao = :funcao,
                telefone = :telefone,
                email = :email,
                comissao = :comissao,
                tipo_comissao = :tipo_comissao,
                status = :status
                WHERE id = :id AND company_id = :company_id";

        $params = [
            ':id' => $this->id,
            ':company_id' => $this->company_id,
            ':nome' => $this->nome,
            ':funcao' => $this->funcao,
            ':telefone' => $this->telefone,
            ':email' => $this->email,
            ':comissao' => $this->comissao,
            ':tipo_comissao' => $this->tipo_comissao,
            ':status' => $this->status
        ];

        return $this->db->execute($sql, $params);
    }

    /**
     * Verifica se o profissional tem agendamentos não-cancelados
     */
    public function hasAgendamentosAtivos($id) {
        $sql = "SELECT COUNT(*) as total FROM agenda
                WHERE profissional_id = :id
                  AND company_id = :company_id
                  AND status != 'cancelado'";
        $params = [':id' => $id, ':company_id' => $this->company_id];
        $result = $this->db->queryOne($sql, $params);
        return ((int)($result['total'] ?? 0)) > 0;
    }

    /**
     * Deletar profissional
     */
    public function delete($id) {
        $sql = "DELETE FROM profissionais WHERE id = :id AND company_id = :company_id";
        $params = [
            ':id' => $id,
            ':company_id' => $this->company_id
        ];
        return $this->db->execute($sql, $params);
    }

    /**
     * Buscar profissional por ID
     */
    public function getById($id) {
        $sql = "SELECT * FROM profissionais WHERE id = :id AND company_id = :company_id";
        $params = [
            ':id' => $id,
            ':company_id' => $this->company_id
        ];
        return $this->db->queryOne($sql, $params);
    }

    /**
     * Buscar todos os profissionais
     */
    public function getAll($filtros = []) {
        $sql = "SELECT * FROM profissionais WHERE company_id = :company_id";
        $params = [':company_id' => $this->company_id];

        if (isset($filtros['status']) && $filtros['status'] !== '') {
            $sql .= " AND status = :status";
            $params[':status'] = $filtros['status'];
        }

        if (isset($filtros['nome']) && $filtros['nome'] !== '') {
            $sql .= " AND nome LIKE :nome";
            $params[':nome'] = '%' . $filtros['nome'] . '%';
        }

        if (isset($filtros['funcao']) && $filtros['funcao'] !== '') {
            $sql .= " AND funcao = :funcao";
            $params[':funcao'] = $filtros['funcao'];
        }

        $allowed_sort = ['id', 'nome', 'funcao', 'comissao', 'status'];
        $sort_col = (isset($filtros['orderby']) && in_array($filtros['orderby'], $allowed_sort))
            ? $filtros['orderby'] : 'nome';
        $sort_dir = (isset($filtros['order']) && strtolower($filtros['order']) === 'desc') ? 'DESC' : 'ASC';
        $sql .= " ORDER BY $sort_col $sort_dir";

        if (isset($filtros['limit'], $filtros['offset'])) {
            $sql .= " LIMIT :limit OFFSET :offset";
            $stmt = $this->db->getConnection()->prepare($sql);
            foreach ($params as $k => $v) $stmt->bindValue($k, $v);
            $stmt->bindValue(':limit',  (int)$filtros['limit'],  PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$filtros['offset'], PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return $this->db->query($sql, $params);
    }

    /**
     * Contar total de profissionais
     */
    public function count($filtros = []) {
        $sql = "SELECT COUNT(*) as total FROM profissionais WHERE company_id = :company_id";
        $params = [':company_id' => $this->company_id];

        if (isset($filtros['status']) && $filtros['status'] !== '') {
            $sql .= " AND status = :status";
            $params[':status'] = $filtros['status'];
        }

        if (isset($filtros['nome']) && $filtros['nome'] !== '') {
            $sql .= " AND nome LIKE :nome";
            $params[':nome'] = '%' . $filtros['nome'] . '%';
        }

        $result = $this->db->queryOne($sql, $params);
        return (int)($result['total'] ?? 0);
    }

    /**
     * Calcular comissão do profissional
     */
    public function calcularComissao($profissional_id, $data_inicio, $data_fim) {
        // Buscar dados do profissional
        $profissional = $this->getById($profissional_id);
        if (!$profissional) return 0;

        // Buscar total de serviços realizados no período
        $sql = "SELECT SUM(vi.preco_total) as total
                FROM venda_itens vi
                INNER JOIN vendas v ON vi.venda_id = v.id
                WHERE vi.profissional_id = :profissional_id
                AND vi.tipo_item = 'servico'
                AND v.company_id = :company_id
                AND v.data >= :data_inicio
                AND v.data <= :data_fim
                AND v.status = 'finalizada'";

        $params = [
            ':profissional_id' => $profissional_id,
            ':company_id' => $this->company_id,
            ':data_inicio' => $data_inicio,
            ':data_fim' => $data_fim
        ];

        $result = $this->db->queryOne($sql, $params);
        $total_servicos = $result['total'] ?? 0;

        // Calcular comissão
        if ($profissional['tipo_comissao'] === 'percentual') {
            return ($total_servicos * $profissional['comissao']) / 100;
        } else {
            // Contar quantidade de serviços
            $sql = "SELECT COUNT(*) as qtd
                    FROM venda_itens vi
                    INNER JOIN vendas v ON vi.venda_id = v.id
                    WHERE vi.profissional_id = :profissional_id
                    AND vi.tipo_item = 'servico'
                    AND v.company_id = :company_id
                    AND v.data >= :data_inicio
                    AND v.data <= :data_fim
                    AND v.status = 'finalizada'";

            $result = $this->db->queryOne($sql, $params);
            $qtd_servicos = $result['qtd'] ?? 0;

            return $qtd_servicos * $profissional['comissao'];
        }
    }

    /**
     * Agendamentos do profissional
     */
    public function getAgendamentos($profissional_id, $data = null) {
        $sql = "SELECT a.*, p.nome as pet_nome, t.nome as tutor_nome, t.telefone as tutor_telefone, s.nome as servico_nome
                FROM agenda a
                LEFT JOIN pets p ON a.pet_id = p.id
                LEFT JOIN tutors t ON a.tutor_id = t.id
                LEFT JOIN servicos s ON a.servico_id = s.id
                WHERE a.profissional_id = :profissional_id
                AND a.company_id = :company_id";

        $params = [
            ':profissional_id' => $profissional_id,
            ':company_id' => $this->company_id
        ];

        if ($data) {
            $sql .= " AND a.data = :data";
            $params[':data'] = $data;
        }

        $sql .= " ORDER BY a.data, a.hora";

        return $this->db->query($sql, $params);
    }

    /**
     * Performance do profissional (quantidade de serviços realizados)
     */
    public function getPerformance($profissional_id, $data_inicio, $data_fim) {
        $sql = "SELECT
                    COUNT(vi.id) as total_servicos,
                    SUM(vi.preco_total) as receita_total
                FROM venda_itens vi
                INNER JOIN vendas v ON vi.venda_id = v.id
                WHERE vi.profissional_id = :profissional_id
                AND vi.tipo_item = 'servico'
                AND v.company_id = :company_id
                AND v.data >= :data_inicio
                AND v.data <= :data_fim
                AND v.status = 'finalizada'";

        $params = [
            ':profissional_id' => $profissional_id,
            ':company_id' => $this->company_id,
            ':data_inicio' => $data_inicio,
            ':data_fim' => $data_fim
        ];

        $result = $this->db->queryOne($sql, $params);

        return [
            'total_servicos' => $result['total_servicos'] ?? 0,
            'receita_total' => $result['receita_total'] ?? 0,
            'comissao' => $this->calcularComissao($profissional_id, $data_inicio, $data_fim)
        ];
    }
}
