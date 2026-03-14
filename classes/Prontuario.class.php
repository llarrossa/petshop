<?php
/**
 * Classe Prontuario
 * Gerencia registros de prontuário / histórico de atendimento dos pets
 */

require_once __DIR__ . '/../database/connection.php';

class Prontuario {
    private $db;
    private $company_id;

    public $id;
    public $pet_id;
    public $cliente_id;
    public $profissional_id;
    public $data_atendimento;
    public $peso;
    public $observacoes;
    public $recomendacoes;

    public function __construct($company_id = null) {
        $this->db         = Database::getInstance();
        $this->company_id = $company_id ?? getCompanyId();
    }

    /**
     * Criar novo registro de prontuário
     */
    public function create() {
        $sql = "INSERT INTO prontuarios
                    (company_id, pet_id, cliente_id, profissional_id,
                     data_atendimento, peso, observacoes, recomendacoes)
                VALUES
                    (:company_id, :pet_id, :cliente_id, :profissional_id,
                     :data_atendimento, :peso, :observacoes, :recomendacoes)";

        $params = [
            ':company_id'       => $this->company_id,
            ':pet_id'           => $this->pet_id,
            ':cliente_id'       => $this->cliente_id,
            ':profissional_id'  => $this->profissional_id ?: null,
            ':data_atendimento' => $this->data_atendimento,
            ':peso'             => $this->peso ?: null,
            ':observacoes'      => $this->observacoes ?: null,
            ':recomendacoes'    => $this->recomendacoes ?: null,
        ];

        if ($this->db->execute($sql, $params)) {
            $this->id = $this->db->lastInsertId();
            return true;
        }
        return false;
    }

    /**
     * Atualizar registro existente
     */
    public function update() {
        $sql = "UPDATE prontuarios SET
                    pet_id           = :pet_id,
                    cliente_id       = :cliente_id,
                    profissional_id  = :profissional_id,
                    data_atendimento = :data_atendimento,
                    peso             = :peso,
                    observacoes      = :observacoes,
                    recomendacoes    = :recomendacoes
                WHERE id = :id AND company_id = :company_id";

        $params = [
            ':id'               => $this->id,
            ':company_id'       => $this->company_id,
            ':pet_id'           => $this->pet_id,
            ':cliente_id'       => $this->cliente_id,
            ':profissional_id'  => $this->profissional_id ?: null,
            ':data_atendimento' => $this->data_atendimento,
            ':peso'             => $this->peso ?: null,
            ':observacoes'      => $this->observacoes ?: null,
            ':recomendacoes'    => $this->recomendacoes ?: null,
        ];

        return $this->db->execute($sql, $params);
    }

    /**
     * Excluir registro
     */
    public function delete($id) {
        $sql    = "DELETE FROM prontuarios WHERE id = :id AND company_id = :company_id";
        $params = [':id' => $id, ':company_id' => $this->company_id];
        return $this->db->execute($sql, $params);
    }

    /**
     * Buscar registro por ID (com joins de pet, cliente e profissional)
     */
    public function getById($id) {
        $sql = "SELECT pr.*,
                       p.nome    AS pet_nome,
                       p.especie AS pet_especie,
                       t.nome    AS cliente_nome,
                       t.telefone AS cliente_telefone,
                       prof.nome AS profissional_nome
                FROM prontuarios pr
                LEFT JOIN pets          p    ON pr.pet_id          = p.id
                LEFT JOIN tutors        t    ON pr.cliente_id       = t.id
                LEFT JOIN profissionais prof ON pr.profissional_id  = prof.id
                WHERE pr.id = :id AND pr.company_id = :company_id";

        return $this->db->queryOne($sql, [
            ':id'         => $id,
            ':company_id' => $this->company_id,
        ]);
    }

    /**
     * Listar prontuários com filtros opcionais e paginação
     * Filtros aceitos: pet_id, cliente_id, data_inicio, data_fim, limit, offset
     */
    public function getAll($filtros = []) {
        [$where, $params] = $this->buildWhere($filtros);

        $sql = "SELECT pr.*,
                       p.nome    AS pet_nome,
                       p.especie AS pet_especie,
                       t.nome    AS cliente_nome,
                       prof.nome AS profissional_nome
                FROM prontuarios pr
                LEFT JOIN pets          p    ON pr.pet_id          = p.id
                LEFT JOIN tutors        t    ON pr.cliente_id       = t.id
                LEFT JOIN profissionais prof ON pr.profissional_id  = prof.id
                WHERE $where
                ORDER BY pr.data_atendimento DESC, pr.id DESC";

        if (isset($filtros['limit']) && isset($filtros['offset'])) {
            $sql .= " LIMIT :limit OFFSET :offset";
            $stmt = $this->db->getConnection()->prepare($sql);
            foreach ($params as $k => $v) {
                $stmt->bindValue($k, $v);
            }
            $stmt->bindValue(':limit',  (int)$filtros['limit'],  PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$filtros['offset'], PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return $this->db->query($sql, $params);
    }

    /**
     * Contar total de registros (mesmos filtros de getAll)
     */
    public function count($filtros = []) {
        [$where, $params] = $this->buildWhere($filtros);
        $sql    = "SELECT COUNT(*) as total FROM prontuarios pr WHERE $where";
        $result = $this->db->queryOne($sql, $params);
        return (int)($result['total'] ?? 0);
    }

    /**
     * Buscar todos os registros de um pet específico
     */
    public function getByPet($pet_id, $limit = null) {
        $sql = "SELECT pr.*,
                       prof.nome AS profissional_nome
                FROM prontuarios pr
                LEFT JOIN profissionais prof ON pr.profissional_id = prof.id
                WHERE pr.pet_id = :pet_id AND pr.company_id = :company_id
                ORDER BY pr.data_atendimento DESC, pr.id DESC";

        $params = [':pet_id' => $pet_id, ':company_id' => $this->company_id];

        if ($limit !== null) {
            $sql .= " LIMIT :limit";
            $stmt = $this->db->getConnection()->prepare($sql);
            foreach ($params as $k => $v) {
                $stmt->bindValue($k, $v);
            }
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return $this->db->query($sql, $params);
    }

    /**
     * Buscar registros por período de datas
     */
    public function getByPeriodo($data_inicio, $data_fim) {
        return $this->getAll([
            'data_inicio' => $data_inicio,
            'data_fim'    => $data_fim,
        ]);
    }

    // --------------------------------------------------------
    // Privado
    // --------------------------------------------------------

    private function buildWhere($filtros) {
        $where  = "pr.company_id = :company_id";
        $params = [':company_id' => $this->company_id];

        if (!empty($filtros['pet_id'])) {
            $where .= " AND pr.pet_id = :pet_id";
            $params[':pet_id'] = $filtros['pet_id'];
        }
        if (!empty($filtros['cliente_id'])) {
            $where .= " AND pr.cliente_id = :cliente_id";
            $params[':cliente_id'] = $filtros['cliente_id'];
        }
        if (!empty($filtros['data_inicio'])) {
            $where .= " AND pr.data_atendimento >= :data_inicio";
            $params[':data_inicio'] = $filtros['data_inicio'];
        }
        if (!empty($filtros['data_fim'])) {
            $where .= " AND pr.data_atendimento <= :data_fim";
            $params[':data_fim'] = $filtros['data_fim'];
        }

        return [$where, $params];
    }
}
