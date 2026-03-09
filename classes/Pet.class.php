<?php
/**
 * Classe Pet
 * Gerencia operações CRUD de pets/animais
 */

require_once __DIR__ . '/../database/connection.php';

class Pet {
    private $db;
    private $company_id;

    // Propriedades
    public $id;
    public $tutor_id;
    public $nome;
    public $especie;
    public $raca;
    public $sexo;
    public $data_nascimento;
    public $peso;
    public $cor;
    public $porte;
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
     * Criar novo pet
     */
    public function create() {
        $sql = "INSERT INTO pets (company_id, tutor_id, nome, especie, raca, sexo, data_nascimento, peso, cor, porte, observacoes, status)
                VALUES (:company_id, :tutor_id, :nome, :especie, :raca, :sexo, :data_nascimento, :peso, :cor, :porte, :observacoes, :status)";

        $params = [
            ':company_id' => $this->company_id,
            ':tutor_id' => $this->tutor_id,
            ':nome' => $this->nome,
            ':especie' => $this->especie,
            ':raca' => $this->raca,
            ':sexo' => $this->sexo,
            ':data_nascimento' => $this->data_nascimento,
            ':peso' => $this->peso,
            ':cor' => $this->cor,
            ':porte' => $this->porte,
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
     * Atualizar pet existente
     */
    public function update() {
        $sql = "UPDATE pets SET
                tutor_id = :tutor_id,
                nome = :nome,
                especie = :especie,
                raca = :raca,
                sexo = :sexo,
                data_nascimento = :data_nascimento,
                peso = :peso,
                cor = :cor,
                porte = :porte,
                observacoes = :observacoes,
                status = :status
                WHERE id = :id AND company_id = :company_id";

        $params = [
            ':id' => $this->id,
            ':company_id' => $this->company_id,
            ':tutor_id' => $this->tutor_id,
            ':nome' => $this->nome,
            ':especie' => $this->especie,
            ':raca' => $this->raca,
            ':sexo' => $this->sexo,
            ':data_nascimento' => $this->data_nascimento,
            ':peso' => $this->peso,
            ':cor' => $this->cor,
            ':porte' => $this->porte,
            ':observacoes' => $this->observacoes,
            ':status' => $this->status
        ];

        return $this->db->execute($sql, $params);
    }

    /**
     * Deletar pet
     */
    public function delete($id) {
        $sql = "DELETE FROM pets WHERE id = :id AND company_id = :company_id";
        $params = [
            ':id' => $id,
            ':company_id' => $this->company_id
        ];
        return $this->db->execute($sql, $params);
    }

    /**
     * Buscar pet por ID
     */
    public function getById($id) {
        $sql = "SELECT p.*, t.nome as tutor_nome, t.telefone as tutor_telefone
                FROM pets p
                LEFT JOIN tutors t ON p.tutor_id = t.id
                WHERE p.id = :id AND p.company_id = :company_id";
        $params = [
            ':id' => $id,
            ':company_id' => $this->company_id
        ];
        return $this->db->queryOne($sql, $params);
    }

    /**
     * Buscar todos os pets
     */
    public function getAll($filtros = []) {
        $sql = "SELECT p.*, t.nome as tutor_nome, t.telefone as tutor_telefone
                FROM pets p
                LEFT JOIN tutors t ON p.tutor_id = t.id
                WHERE p.company_id = :company_id";
        $params = [':company_id' => $this->company_id];

        // Filtro por status
        if (isset($filtros['status'])) {
            $sql .= " AND p.status = :status";
            $params[':status'] = $filtros['status'];
        }

        // Filtro por nome
        if (isset($filtros['nome']) && !empty($filtros['nome'])) {
            $sql .= " AND p.nome LIKE :nome";
            $params[':nome'] = '%' . $filtros['nome'] . '%';
        }

        // Filtro por espécie
        if (isset($filtros['especie']) && !empty($filtros['especie'])) {
            $sql .= " AND p.especie = :especie";
            $params[':especie'] = $filtros['especie'];
        }

        // Filtro por tutor
        if (isset($filtros['tutor_id']) && !empty($filtros['tutor_id'])) {
            $sql .= " AND p.tutor_id = :tutor_id";
            $params[':tutor_id'] = $filtros['tutor_id'];
        }

        $sql .= " ORDER BY p.nome ASC";

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
     * Contar total de pets
     */
    public function count($filtros = []) {
        $sql = "SELECT COUNT(*) as total FROM pets WHERE company_id = :company_id";
        $params = [':company_id' => $this->company_id];

        if (isset($filtros['status'])) {
            $sql .= " AND status = :status";
            $params[':status'] = $filtros['status'];
        }

        if (isset($filtros['especie'])) {
            $sql .= " AND especie = :especie";
            $params[':especie'] = $filtros['especie'];
        }

        $result = $this->db->queryOne($sql, $params);
        return $result['total'] ?? 0;
    }

    /**
     * Calcular idade do pet
     */
    public function calcularIdade($data_nascimento) {
        if (empty($data_nascimento)) return null;

        $nascimento = new DateTime($data_nascimento);
        $hoje = new DateTime();
        $idade = $hoje->diff($nascimento);

        if ($idade->y > 0) {
            return $idade->y . ' ano' . ($idade->y > 1 ? 's' : '');
        } elseif ($idade->m > 0) {
            return $idade->m . ' mes' . ($idade->m > 1 ? 'es' : '');
        } else {
            return $idade->d . ' dia' . ($idade->d > 1 ? 's' : '');
        }
    }

    /**
     * Buscar prontuário do pet
     */
    public function getProntuario($pet_id, $limit = 10) {
        $sql = "SELECT pr.*, u.nome as veterinario_nome
                FROM prontuario pr
                LEFT JOIN users u ON pr.veterinario_id = u.id
                WHERE pr.pet_id = :pet_id AND pr.company_id = :company_id
                ORDER BY pr.data_atendimento DESC
                LIMIT :limit";

        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->bindValue(':pet_id', $pet_id, PDO::PARAM_INT);
        $stmt->bindValue(':company_id', $this->company_id, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Buscar histórico de agendamentos do pet
     */
    public function getHistoricoAgendamentos($pet_id, $limit = 10) {
        $sql = "SELECT a.*, s.nome as servico_nome, pr.nome as profissional_nome
                FROM agenda a
                LEFT JOIN servicos s ON a.servico_id = s.id
                LEFT JOIN profissionais pr ON a.profissional_id = pr.id
                WHERE a.pet_id = :pet_id AND a.company_id = :company_id
                ORDER BY a.data DESC, a.hora DESC
                LIMIT :limit";

        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->bindValue(':pet_id', $pet_id, PDO::PARAM_INT);
        $stmt->bindValue(':company_id', $this->company_id, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Buscar pets por tutor
     */
    public function getByTutor($tutor_id) {
        $sql = "SELECT * FROM pets WHERE tutor_id = :tutor_id AND company_id = :company_id AND status = 'ativo' ORDER BY nome";
        $params = [
            ':tutor_id' => $tutor_id,
            ':company_id' => $this->company_id
        ];
        return $this->db->query($sql, $params);
    }

    /**
     * Atualizar peso do pet
     */
    public function atualizarPeso($pet_id, $novo_peso) {
        $sql = "UPDATE pets SET peso = :peso WHERE id = :id AND company_id = :company_id";
        $params = [
            ':id' => $pet_id,
            ':peso' => $novo_peso,
            ':company_id' => $this->company_id
        ];
        return $this->db->execute($sql, $params);
    }

    /**
     * Estatísticas de pets por espécie
     */
    public function estatisticasPorEspecie() {
        $sql = "SELECT especie, COUNT(*) as total
                FROM pets
                WHERE company_id = :company_id AND status = 'ativo'
                GROUP BY especie
                ORDER BY total DESC";
        $params = [':company_id' => $this->company_id];
        return $this->db->query($sql, $params);
    }
}
