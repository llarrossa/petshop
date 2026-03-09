<?php
/**
 * Classe Tutor
 * Gerencia operações CRUD de tutores/clientes
 */

require_once __DIR__ . '/../database/connection.php';

class Tutor {
    private $db;
    private $company_id;

    // Propriedades
    public $id;
    public $nome;
    public $cpf;
    public $telefone;
    public $whatsapp;
    public $email;
    public $endereco;
    public $cidade;
    public $estado;
    public $cep;
    public $observacoes;
    public $status;

    /**
     * Construtor
     */
    public function __construct($company_id = null) {
        $this->db = Database::getInstance();
        $this->company_id = $company_id ?? getCompanyId();
    }

    /**
     * Criar novo tutor
     */
    public function create() {
        $sql = "INSERT INTO tutors (company_id, nome, cpf, telefone, whatsapp, email, endereco, cidade, estado, cep, observacoes, status)
                VALUES (:company_id, :nome, :cpf, :telefone, :whatsapp, :email, :endereco, :cidade, :estado, :cep, :observacoes, :status)";

        $params = [
            ':company_id' => $this->company_id,
            ':nome' => $this->nome,
            ':cpf' => $this->cpf,
            ':telefone' => $this->telefone,
            ':whatsapp' => $this->whatsapp,
            ':email' => $this->email,
            ':endereco' => $this->endereco,
            ':cidade' => $this->cidade,
            ':estado' => $this->estado,
            ':cep' => $this->cep,
            ':observacoes' => $this->observacoes,
            ':status' => $this->status ?? 'ativo'
        ];

        if ($this->db->execute($sql, $params)) {
            $this->id = $this->db->lastInsertId();
            return true;
        }
        return false;
    }

    /**
     * Atualizar tutor existente
     */
    public function update() {
        $sql = "UPDATE tutors SET
                nome = :nome,
                cpf = :cpf,
                telefone = :telefone,
                whatsapp = :whatsapp,
                email = :email,
                endereco = :endereco,
                cidade = :cidade,
                estado = :estado,
                cep = :cep,
                observacoes = :observacoes,
                status = :status
                WHERE id = :id AND company_id = :company_id";

        $params = [
            ':id' => $this->id,
            ':company_id' => $this->company_id,
            ':nome' => $this->nome,
            ':cpf' => $this->cpf,
            ':telefone' => $this->telefone,
            ':whatsapp' => $this->whatsapp,
            ':email' => $this->email,
            ':endereco' => $this->endereco,
            ':cidade' => $this->cidade,
            ':estado' => $this->estado,
            ':cep' => $this->cep,
            ':observacoes' => $this->observacoes,
            ':status' => $this->status
        ];

        return $this->db->execute($sql, $params);
    }

    /**
     * Deletar tutor
     */
    public function delete($id) {
        $sql = "DELETE FROM tutors WHERE id = :id AND company_id = :company_id";
        $params = [
            ':id' => $id,
            ':company_id' => $this->company_id
        ];
        return $this->db->execute($sql, $params);
    }

    /**
     * Buscar tutor por ID
     */
    public function getById($id) {
        $sql = "SELECT * FROM tutors WHERE id = :id AND company_id = :company_id";
        $params = [
            ':id' => $id,
            ':company_id' => $this->company_id
        ];
        return $this->db->queryOne($sql, $params);
    }

    /**
     * Buscar todos os tutores
     */
    public function getAll($filtros = []) {
        $sql = "SELECT * FROM tutors WHERE company_id = :company_id";
        $params = [':company_id' => $this->company_id];

        // Filtro por status
        if (isset($filtros['status'])) {
            $sql .= " AND status = :status";
            $params[':status'] = $filtros['status'];
        }

        // Filtro por nome
        if (isset($filtros['nome']) && !empty($filtros['nome'])) {
            $sql .= " AND nome LIKE :nome";
            $params[':nome'] = '%' . $filtros['nome'] . '%';
        }

        // Filtro por telefone
        if (isset($filtros['telefone']) && !empty($filtros['telefone'])) {
            $sql .= " AND telefone LIKE :telefone";
            $params[':telefone'] = '%' . $filtros['telefone'] . '%';
        }

        $allowed_sort = ['id', 'nome', 'telefone', 'email', 'status'];
        $sort_col = (isset($filtros['orderby']) && in_array($filtros['orderby'], $allowed_sort)) ? $filtros['orderby'] : 'id';
        $sort_dir = (isset($filtros['order']) && strtolower($filtros['order']) === 'desc') ? 'DESC' : 'ASC';
        $sql .= " ORDER BY $sort_col $sort_dir";

        // Paginação
        if (isset($filtros['limit']) && isset($filtros['offset'])) {
            $sql .= " LIMIT :limit OFFSET :offset";
            $stmt = $this->db->getConnection()->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindValue(':limit', (int)$filtros['limit'], PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$filtros['offset'], PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        }

        return $this->db->query($sql, $params);
    }

    /**
     * Contar total de tutores
     */
    public function count($filtros = []) {
        $sql = "SELECT COUNT(*) as total FROM tutors WHERE company_id = :company_id";
        $params = [':company_id' => $this->company_id];

        if (isset($filtros['status'])) {
            $sql .= " AND status = :status";
            $params[':status'] = $filtros['status'];
        }

        if (isset($filtros['nome']) && !empty($filtros['nome'])) {
            $sql .= " AND nome LIKE :nome";
            $params[':nome'] = '%' . $filtros['nome'] . '%';
        }

        $result = $this->db->queryOne($sql, $params);
        return $result['total'] ?? 0;
    }

    /**
     * Buscar pets de um tutor
     */
    public function getPets($tutor_id) {
        $sql = "SELECT * FROM pets WHERE tutor_id = :tutor_id AND company_id = :company_id ORDER BY nome";
        $params = [
            ':tutor_id' => $tutor_id,
            ':company_id' => $this->company_id
        ];
        return $this->db->query($sql, $params);
    }

    /**
     * Buscar histórico de vendas do tutor
     */
    public function getHistoricoVendas($tutor_id, $limit = 10) {
        $sql = "SELECT v.*, p.nome as pet_nome
                FROM vendas v
                LEFT JOIN pets p ON v.pet_id = p.id
                WHERE v.tutor_id = :tutor_id AND v.company_id = :company_id
                ORDER BY v.data DESC
                LIMIT :limit";

        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->bindValue(':tutor_id', $tutor_id, PDO::PARAM_INT);
        $stmt->bindValue(':company_id', $this->company_id, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Buscar agendamentos do tutor
     */
    public function getAgendamentos($tutor_id, $filtros = []) {
        $sql = "SELECT a.*, p.nome as pet_nome, s.nome as servico_nome, pr.nome as profissional_nome
                FROM agenda a
                LEFT JOIN pets p ON a.pet_id = p.id
                LEFT JOIN servicos s ON a.servico_id = s.id
                LEFT JOIN profissionais pr ON a.profissional_id = pr.id
                WHERE a.tutor_id = :tutor_id AND a.company_id = :company_id";

        $params = [
            ':tutor_id' => $tutor_id,
            ':company_id' => $this->company_id
        ];

        if (isset($filtros['status'])) {
            $sql .= " AND a.status = :status";
            $params[':status'] = $filtros['status'];
        }

        $sql .= " ORDER BY a.data DESC, a.hora DESC";

        return $this->db->query($sql, $params);
    }

    /**
     * Validar CPF
     */
    public static function validarCPF($cpf) {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);

        if (strlen($cpf) != 11 || preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }
        return true;
    }
}
