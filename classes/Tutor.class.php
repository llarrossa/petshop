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
     * Verificar se tutor possui vínculos (pets, agendamentos ou vendas)
     */
    public function hasVinculos($id) {
        $sql = "SELECT
                    (SELECT COUNT(*) FROM pets     WHERE tutor_id = :id1 AND company_id = :company_id1) +
                    (SELECT COUNT(*) FROM agenda   WHERE tutor_id = :id2 AND company_id = :company_id2) +
                    (SELECT COUNT(*) FROM vendas   WHERE tutor_id = :id3 AND company_id = :company_id3)
                AS total";
        $params = [
            ':id1' => $id, ':company_id1' => $this->company_id,
            ':id2' => $id, ':company_id2' => $this->company_id,
            ':id3' => $id, ':company_id3' => $this->company_id,
        ];
        $result = $this->db->queryOne($sql, $params);
        return ($result['total'] ?? 0) > 0;
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
     * Monta cláusulas WHERE/AND compartilhadas entre getAll() e count()
     */
    private function buildWhere($filtros, &$params) {
        $where = "WHERE company_id = :company_id";

        if (isset($filtros['status']) && $filtros['status'] !== '') {
            $where .= " AND status = :status";
            $params[':status'] = $filtros['status'];
        }

        if (isset($filtros['nome']) && $filtros['nome'] !== '') {
            $where .= " AND nome LIKE :nome";
            $params[':nome'] = '%' . $filtros['nome'] . '%';
        }

        if (isset($filtros['telefone']) && $filtros['telefone'] !== '') {
            $where .= " AND telefone LIKE :telefone";
            $params[':telefone'] = '%' . $filtros['telefone'] . '%';
        }

        if (isset($filtros['com_vinculo']) && $filtros['com_vinculo'] !== '') {
            // Usa tutors.company_id para evitar placeholder duplicado (:company_id)
            // com PDO::ATTR_EMULATE_PREPARES = false
            $exists = "EXISTS (SELECT 1 FROM pets   WHERE tutor_id = tutors.id AND company_id = tutors.company_id)
                    OR EXISTS (SELECT 1 FROM agenda WHERE tutor_id = tutors.id AND company_id = tutors.company_id)
                    OR EXISTS (SELECT 1 FROM vendas WHERE tutor_id = tutors.id AND company_id = tutors.company_id)";
            if ($filtros['com_vinculo'] === '1') {
                $where .= " AND ($exists)";
            } else {
                $where .= " AND NOT ($exists)";
            }
        }

        return $where;
    }

    /**
     * Buscar todos os tutores
     */
    public function getAll($filtros = []) {
        $params = [':company_id' => $this->company_id];
        $where  = $this->buildWhere($filtros, $params);

        $allowed_sort = ['id', 'nome', 'telefone', 'email', 'status'];
        $sort_col = (isset($filtros['orderby']) && in_array($filtros['orderby'], $allowed_sort)) ? $filtros['orderby'] : 'id';
        $sort_dir = (isset($filtros['order']) && strtolower($filtros['order']) === 'desc') ? 'DESC' : 'ASC';

        $sql = "SELECT * FROM tutors $where ORDER BY $sort_col $sort_dir";

        if (isset($filtros['limit']) && isset($filtros['offset'])) {
            $sql .= " LIMIT :limit OFFSET :offset";
            $stmt = $this->db->getConnection()->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindValue(':limit',  (int)$filtros['limit'],  PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$filtros['offset'], PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return $this->db->query($sql, $params);
    }

    /**
     * Contar total de tutores (respeita os mesmos filtros de getAll)
     */
    public function count($filtros = []) {
        $params = [':company_id' => $this->company_id];
        $where  = $this->buildWhere($filtros, $params);

        $sql    = "SELECT COUNT(*) as total FROM tutors $where";
        $result = $this->db->queryOne($sql, $params);
        return (int)($result['total'] ?? 0);
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
