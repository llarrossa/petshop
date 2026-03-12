<?php
/**
 * Classe Servico
 * Gerencia operações CRUD de serviços
 */

require_once __DIR__ . '/../database/connection.php';

class Servico {
    private $db;
    private $company_id;

    // Propriedades
    public $id;
    public $nome;
    public $descricao;
    public $preco;
    public $duracao_media;
    public $categoria;
    public $status;

    /**
     * Construtor
     */
    public function __construct($company_id = null) {
        $this->db = Database::getInstance();
        $this->company_id = $company_id ?? getCompanyId();
    }

    /**
     * Criar novo serviço
     */
    public function create() {
        $sql = "INSERT INTO servicos (company_id, nome, descricao, preco, duracao_media, categoria, status)
                VALUES (:company_id, :nome, :descricao, :preco, :duracao_media, :categoria, :status)";

        $params = [
            ':company_id' => $this->company_id,
            ':nome' => $this->nome,
            ':descricao' => $this->descricao,
            ':preco' => $this->preco,
            ':duracao_media' => $this->duracao_media,
            ':categoria' => $this->categoria,
            ':status' => $this->status ?? 'ativo'
        ];

        if ($this->db->execute($sql, $params)) {
            $this->id = $this->db->lastInsertId();
            return true;
        }
        return false;
    }

    /**
     * Atualizar serviço existente
     */
    public function update() {
        $sql = "UPDATE servicos SET
                nome = :nome,
                descricao = :descricao,
                preco = :preco,
                duracao_media = :duracao_media,
                categoria = :categoria,
                status = :status
                WHERE id = :id AND company_id = :company_id";

        $params = [
            ':id' => $this->id,
            ':company_id' => $this->company_id,
            ':nome' => $this->nome,
            ':descricao' => $this->descricao,
            ':preco' => $this->preco,
            ':duracao_media' => $this->duracao_media,
            ':categoria' => $this->categoria,
            ':status' => $this->status
        ];

        return $this->db->execute($sql, $params);
    }

    /**
     * Deletar serviço
     */
    public function delete($id) {
        $sql = "DELETE FROM servicos WHERE id = :id AND company_id = :company_id";
        $params = [
            ':id' => $id,
            ':company_id' => $this->company_id
        ];
        return $this->db->execute($sql, $params);
    }

    /**
     * Buscar serviço por ID
     */
    public function getById($id) {
        $sql = "SELECT * FROM servicos WHERE id = :id AND company_id = :company_id";
        $params = [
            ':id' => $id,
            ':company_id' => $this->company_id
        ];
        return $this->db->queryOne($sql, $params);
    }

    /**
     * Buscar todos os serviços
     */
    public function getAll($filtros = []) {
        $sql = "SELECT * FROM servicos WHERE company_id = :company_id";
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

        // Filtro por categoria
        if (isset($filtros['categoria']) && !empty($filtros['categoria'])) {
            $sql .= " AND categoria = :categoria";
            $params[':categoria'] = $filtros['categoria'];
        }

        $allowed_sort = ['id', 'nome', 'categoria', 'preco', 'duracao_media', 'status'];
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
     * Contar total de serviços
     */
    public function count($filtros = []) {
        $sql = "SELECT COUNT(*) as total FROM servicos WHERE company_id = :company_id";
        $params = [':company_id' => $this->company_id];

        if (isset($filtros['status']) && $filtros['status'] !== '') {
            $sql .= " AND status = :status";
            $params[':status'] = $filtros['status'];
        }

        if (isset($filtros['nome']) && $filtros['nome'] !== '') {
            $sql .= " AND nome LIKE :nome";
            $params[':nome'] = '%' . $filtros['nome'] . '%';
        }

        if (isset($filtros['categoria']) && $filtros['categoria'] !== '') {
            $sql .= " AND categoria = :categoria";
            $params[':categoria'] = $filtros['categoria'];
        }

        $result = $this->db->queryOne($sql, $params);
        return (int)($result['total'] ?? 0);
    }

    /**
     * Buscar categorias únicas
     */
    public function getCategorias() {
        $sql = "SELECT DISTINCT categoria FROM servicos
                WHERE company_id = :company_id
                AND categoria IS NOT NULL
                AND categoria != ''
                ORDER BY categoria";
        $params = [':company_id' => $this->company_id];
        return $this->db->query($sql, $params);
    }

    /**
     * Serviços mais vendidos
     */
    public function getMaisVendidos($limit = 10, $data_inicio = null, $data_fim = null) {
        $sql = "SELECT s.*, COUNT(vi.id) as total_vendas, SUM(vi.preco_total) as receita_total
                FROM servicos s
                INNER JOIN venda_itens vi ON s.id = vi.item_id AND vi.tipo_item = 'servico'
                INNER JOIN vendas v ON vi.venda_id = v.id
                WHERE s.company_id = :company_id";

        $params = [':company_id' => $this->company_id];

        if ($data_inicio) {
            $sql .= " AND v.data >= :data_inicio";
            $params[':data_inicio'] = $data_inicio;
        }

        if ($data_fim) {
            $sql .= " AND v.data <= :data_fim";
            $params[':data_fim'] = $data_fim;
        }

        $sql .= " GROUP BY s.id ORDER BY total_vendas DESC LIMIT :limit";

        $stmt = $this->db->getConnection()->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Receita total por período
     */
    public function getReceitaPorPeriodo($data_inicio, $data_fim) {
        $sql = "SELECT SUM(vi.preco_total) as receita_total
                FROM venda_itens vi
                INNER JOIN vendas v ON vi.venda_id = v.id
                WHERE vi.tipo_item = 'servico'
                AND v.company_id = :company_id
                AND v.data >= :data_inicio
                AND v.data <= :data_fim
                AND v.status = 'finalizada'";

        $params = [
            ':company_id' => $this->company_id,
            ':data_inicio' => $data_inicio,
            ':data_fim' => $data_fim
        ];

        $result = $this->db->queryOne($sql, $params);
        return $result['receita_total'] ?? 0;
    }
}
