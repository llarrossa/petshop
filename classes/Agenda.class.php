<?php
/**
 * Classe Agenda
 * Gerencia operações CRUD de agendamentos
 */

require_once __DIR__ . '/../database/connection.php';

class Agenda {
    private $db;
    private $company_id;

    // Propriedades
    public $id;
    public $pet_id;
    public $tutor_id;
    public $servico_id;
    public $profissional_id;
    public $data;
    public $hora;
    public $status;
    public $observacoes;

    /**
     * Construtor
     */
    public function __construct($company_id = null) {
        $this->db = Database::getInstance();
        $this->company_id = $company_id ?? getCompanyId();
    }

    /**
     * Criar novo agendamento
     */
    public function create() {
        // Verificar disponibilidade
        if (!$this->verificarDisponibilidade($this->data, $this->hora, $this->profissional_id)) {
            return false;
        }

        $sql = "INSERT INTO agenda (company_id, pet_id, tutor_id, servico_id, profissional_id, data, hora, status, observacoes)
                VALUES (:company_id, :pet_id, :tutor_id, :servico_id, :profissional_id, :data, :hora, :status, :observacoes)";

        $params = [
            ':company_id' => $this->company_id,
            ':pet_id' => $this->pet_id,
            ':tutor_id' => $this->tutor_id,
            ':servico_id' => $this->servico_id,
            ':profissional_id' => $this->profissional_id,
            ':data' => $this->data,
            ':hora' => $this->hora,
            ':status' => $this->status ?? 'agendado',
            ':observacoes' => $this->observacoes
        ];

        if ($this->db->execute($sql, $params)) {
            $this->id = $this->db->lastInsertId();
            return true;
        }
        return false;
    }

    /**
     * Atualizar agendamento existente
     */
    public function update() {
        $sql = "UPDATE agenda SET
                pet_id = :pet_id,
                tutor_id = :tutor_id,
                servico_id = :servico_id,
                profissional_id = :profissional_id,
                data = :data,
                hora = :hora,
                status = :status,
                observacoes = :observacoes
                WHERE id = :id AND company_id = :company_id";

        $params = [
            ':id' => $this->id,
            ':company_id' => $this->company_id,
            ':pet_id' => $this->pet_id,
            ':tutor_id' => $this->tutor_id,
            ':servico_id' => $this->servico_id,
            ':profissional_id' => $this->profissional_id,
            ':data' => $this->data,
            ':hora' => $this->hora,
            ':status' => $this->status,
            ':observacoes' => $this->observacoes
        ];

        return $this->db->execute($sql, $params);
    }

    /**
     * Deletar agendamento
     */
    public function delete($id) {
        $sql = "DELETE FROM agenda WHERE id = :id AND company_id = :company_id";
        $params = [
            ':id' => $id,
            ':company_id' => $this->company_id
        ];
        return $this->db->execute($sql, $params);
    }

    /**
     * Alterar status do agendamento
     */
    public function alterarStatus($id, $novo_status) {
        $sql = "UPDATE agenda SET status = :status WHERE id = :id AND company_id = :company_id";
        $params = [
            ':id' => $id,
            ':status' => $novo_status,
            ':company_id' => $this->company_id
        ];
        return $this->db->execute($sql, $params);
    }

    /**
     * Buscar agendamento por ID
     */
    public function getById($id) {
        $sql = "SELECT a.*,
                p.nome as pet_nome, p.especie, p.raca,
                t.nome as tutor_nome, t.telefone as tutor_telefone, t.whatsapp as tutor_whatsapp,
                s.nome as servico_nome, s.preco as servico_preco, s.duracao_media,
                pr.nome as profissional_nome
                FROM agenda a
                LEFT JOIN pets p ON a.pet_id = p.id
                LEFT JOIN tutors t ON a.tutor_id = t.id
                LEFT JOIN servicos s ON a.servico_id = s.id
                LEFT JOIN profissionais pr ON a.profissional_id = pr.id
                WHERE a.id = :id AND a.company_id = :company_id";
        $params = [
            ':id' => $id,
            ':company_id' => $this->company_id
        ];
        return $this->db->queryOne($sql, $params);
    }

    /**
     * Buscar todos os agendamentos
     */
    public function getAll($filtros = []) {
        $sql = "SELECT a.*,
                p.nome as pet_nome,
                t.nome as tutor_nome, t.telefone as tutor_telefone,
                s.nome as servico_nome,
                pr.nome as profissional_nome
                FROM agenda a
                LEFT JOIN pets p ON a.pet_id = p.id
                LEFT JOIN tutors t ON a.tutor_id = t.id
                LEFT JOIN servicos s ON a.servico_id = s.id
                LEFT JOIN profissionais pr ON a.profissional_id = pr.id
                WHERE a.company_id = :company_id";
        $params = [':company_id' => $this->company_id];

        // Filtro por status
        if (isset($filtros['status'])) {
            $sql .= " AND a.status = :status";
            $params[':status'] = $filtros['status'];
        }

        // Filtro por data
        if (isset($filtros['data'])) {
            $sql .= " AND a.data = :data";
            $params[':data'] = $filtros['data'];
        }

        // Filtro por período
        if (isset($filtros['data_inicio']) && isset($filtros['data_fim'])) {
            $sql .= " AND a.data BETWEEN :data_inicio AND :data_fim";
            $params[':data_inicio'] = $filtros['data_inicio'];
            $params[':data_fim'] = $filtros['data_fim'];
        }

        // Filtro por profissional
        if (isset($filtros['profissional_id'])) {
            $sql .= " AND a.profissional_id = :profissional_id";
            $params[':profissional_id'] = $filtros['profissional_id'];
        }

        // Filtro por pet
        if (isset($filtros['pet_id'])) {
            $sql .= " AND a.pet_id = :pet_id";
            $params[':pet_id'] = $filtros['pet_id'];
        }

        $sql .= " ORDER BY a.data DESC, a.hora DESC";

        return $this->db->query($sql, $params);
    }

    /**
     * Agendamentos do dia
     */
    public function getAgendamentosDoDia($data = null) {
        if (!$data) {
            $data = date('Y-m-d');
        }

        $sql = "SELECT a.*,
                p.nome as pet_nome, p.especie,
                t.nome as tutor_nome, t.telefone as tutor_telefone,
                s.nome as servico_nome, s.duracao_media,
                pr.nome as profissional_nome
                FROM agenda a
                LEFT JOIN pets p ON a.pet_id = p.id
                LEFT JOIN tutors t ON a.tutor_id = t.id
                LEFT JOIN servicos s ON a.servico_id = s.id
                LEFT JOIN profissionais pr ON a.profissional_id = pr.id
                WHERE a.company_id = :company_id
                AND a.data = :data
                AND a.status NOT IN ('cancelado', 'faltou')
                ORDER BY a.hora ASC";

        $params = [
            ':company_id' => $this->company_id,
            ':data' => $data
        ];

        return $this->db->query($sql, $params);
    }

    /**
     * Verificar disponibilidade de horário
     */
    public function verificarDisponibilidade($data, $hora, $profissional_id, $agendamento_id = null) {
        $sql = "SELECT COUNT(*) as total FROM agenda
                WHERE company_id = :company_id
                AND data = :data
                AND hora = :hora
                AND profissional_id = :profissional_id
                AND status NOT IN ('cancelado', 'faltou')";

        $params = [
            ':company_id' => $this->company_id,
            ':data' => $data,
            ':hora' => $hora,
            ':profissional_id' => $profissional_id
        ];

        // Excluir o próprio agendamento se estiver atualizando
        if ($agendamento_id) {
            $sql .= " AND id != :id";
            $params[':id'] = $agendamento_id;
        }

        $result = $this->db->queryOne($sql, $params);
        return ($result['total'] == 0);
    }

    /**
     * Buscar horários disponíveis
     */
    public function getHorariosDisponiveis($data, $profissional_id) {
        // Horários de funcionamento (08:00 às 18:00, intervalos de 30 minutos)
        $horarios = [];
        $hora_inicio = strtotime('08:00');
        $hora_fim = strtotime('18:00');

        while ($hora_inicio <= $hora_fim) {
            $horario = date('H:i', $hora_inicio);

            if ($this->verificarDisponibilidade($data, $horario, $profissional_id)) {
                $horarios[] = $horario;
            }

            $hora_inicio = strtotime('+30 minutes', $hora_inicio);
        }

        return $horarios;
    }

    /**
     * Contar agendamentos
     */
    public function count($filtros = []) {
        $sql = "SELECT COUNT(*) as total FROM agenda WHERE company_id = :company_id";
        $params = [':company_id' => $this->company_id];

        if (isset($filtros['status'])) {
            $sql .= " AND status = :status";
            $params[':status'] = $filtros['status'];
        }

        if (isset($filtros['data'])) {
            $sql .= " AND data = :data";
            $params[':data'] = $filtros['data'];
        }

        $result = $this->db->queryOne($sql, $params);
        return $result['total'] ?? 0;
    }

    /**
     * Próximos agendamentos
     */
    public function getProximosAgendamentos($limit = 10) {
        $sql = "SELECT a.*,
                p.nome as pet_nome,
                t.nome as tutor_nome, t.telefone as tutor_telefone,
                s.nome as servico_nome,
                pr.nome as profissional_nome
                FROM agenda a
                LEFT JOIN pets p ON a.pet_id = p.id
                LEFT JOIN tutors t ON a.tutor_id = t.id
                LEFT JOIN servicos s ON a.servico_id = s.id
                LEFT JOIN profissionais pr ON a.profissional_id = pr.id
                WHERE a.company_id = :company_id
                AND CONCAT(a.data, ' ', a.hora) >= NOW()
                AND a.status NOT IN ('cancelado', 'finalizado', 'faltou')
                ORDER BY a.data ASC, a.hora ASC
                LIMIT :limit";

        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->bindValue(':company_id', $this->company_id, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
