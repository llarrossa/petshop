<?php
/**
 * Classe Venda
 * Gerencia operações de vendas (PDV)
 */

require_once __DIR__ . '/../database/connection.php';

class Venda {
    private $db;
    private $company_id;

    // Propriedades
    public $id;
    public $tutor_id;
    public $pet_id;
    public $valor_total;
    public $desconto;
    public $valor_final;
    public $forma_pagamento;
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
     * Criar nova venda (com itens)
     */
    public function create($itens = []) {
        try {
            $this->db->beginTransaction();

            // Inserir venda
            $sql = "INSERT INTO vendas (company_id, tutor_id, pet_id, valor_total, desconto, valor_final, forma_pagamento, status, observacoes)
                    VALUES (:company_id, :tutor_id, :pet_id, :valor_total, :desconto, :valor_final, :forma_pagamento, :status, :observacoes)";

            $params = [
                ':company_id' => $this->company_id,
                ':tutor_id' => $this->tutor_id,
                ':pet_id' => $this->pet_id,
                ':valor_total' => $this->valor_total,
                ':desconto' => $this->desconto ?? 0,
                ':valor_final' => $this->valor_final,
                ':forma_pagamento' => $this->forma_pagamento,
                ':status' => $this->status ?? 'finalizada',
                ':observacoes' => $this->observacoes
            ];

            if ($this->db->execute($sql, $params)) {
                $this->id = $this->db->lastInsertId();

                // Inserir itens da venda
                foreach ($itens as $item) {
                    $this->adicionarItem($this->id, $item);
                }

                // Registrar no financeiro
                $this->registrarFinanceiro($this->id);

                $this->db->commit();
                return true;
            }

            $this->db->rollback();
            return false;

        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Erro ao criar venda: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Adicionar item à venda
     */
    private function adicionarItem($venda_id, $item) {
        $sql = "INSERT INTO venda_itens (venda_id, tipo_item, item_id, nome_item, quantidade, preco_unitario, preco_total, profissional_id)
                VALUES (:venda_id, :tipo_item, :item_id, :nome_item, :quantidade, :preco_unitario, :preco_total, :profissional_id)";

        $params = [
            ':venda_id' => $venda_id,
            ':tipo_item' => $item['tipo_item'],
            ':item_id' => $item['item_id'],
            ':nome_item' => $item['nome_item'],
            ':quantidade' => $item['quantidade'],
            ':preco_unitario' => $item['preco_unitario'],
            ':preco_total' => $item['preco_total'],
            ':profissional_id' => $item['profissional_id'] ?? null
        ];

        if ($this->db->execute($sql, $params)) {
            // Se for produto, baixar do estoque
            if ($item['tipo_item'] === 'produto') {
                $this->baixarEstoque($item['item_id'], $item['quantidade']);
            }
            return true;
        }
        return false;
    }

    /**
     * Baixar estoque do produto
     */
    private function baixarEstoque($produto_id, $quantidade) {
        // Atualizar estoque do produto
        $sql = "UPDATE produtos SET estoque_atual = estoque_atual - :quantidade
                WHERE id = :id AND company_id = :company_id";
        $params = [
            ':id' => $produto_id,
            ':quantidade' => $quantidade,
            ':company_id' => $this->company_id
        ];
        $this->db->execute($sql, $params);

        // Registrar movimentação de estoque
        $sql = "INSERT INTO estoque_movimentacoes (company_id, produto_id, tipo, quantidade, motivo)
                VALUES (:company_id, :produto_id, 'saida', :quantidade, 'Venda')";
        $params = [
            ':company_id' => $this->company_id,
            ':produto_id' => $produto_id,
            ':quantidade' => $quantidade
        ];
        $this->db->execute($sql, $params);
    }

    /**
     * Registrar venda no financeiro
     */
    private function registrarFinanceiro($venda_id) {
        $sql = "INSERT INTO financeiro (company_id, tipo, categoria, descricao, valor, forma_pagamento, data_pagamento, status, venda_id)
                VALUES (:company_id, 'receita', 'Venda', :descricao, :valor, :forma_pagamento, NOW(), 'pago', :venda_id)";

        $params = [
            ':company_id' => $this->company_id,
            ':descricao' => 'Venda #' . $venda_id,
            ':valor' => $this->valor_final,
            ':forma_pagamento' => $this->forma_pagamento,
            ':venda_id' => $venda_id
        ];

        return $this->db->execute($sql, $params);
    }

    /**
     * Cancelar venda
     */
    public function cancelar($venda_id) {
        try {
            $this->db->beginTransaction();

            // Buscar itens da venda para estornar estoque
            $itens = $this->getItens($venda_id);

            foreach ($itens as $item) {
                if ($item['tipo_item'] === 'produto') {
                    // Devolver ao estoque
                    $sql = "UPDATE produtos SET estoque_atual = estoque_atual + :quantidade
                            WHERE id = :id AND company_id = :company_id";
                    $params = [
                        ':id' => $item['item_id'],
                        ':quantidade' => $item['quantidade'],
                        ':company_id' => $this->company_id
                    ];
                    $this->db->execute($sql, $params);

                    // Registrar movimentação
                    $sql = "INSERT INTO estoque_movimentacoes (company_id, produto_id, tipo, quantidade, motivo)
                            VALUES (:company_id, :produto_id, 'entrada', :quantidade, 'Cancelamento de venda')";
                    $params = [
                        ':company_id' => $this->company_id,
                        ':produto_id' => $item['item_id'],
                        ':quantidade' => $item['quantidade']
                    ];
                    $this->db->execute($sql, $params);
                }
            }

            // Atualizar status da venda
            $sql = "UPDATE vendas SET status = 'cancelada' WHERE id = :id AND company_id = :company_id";
            $params = [
                ':id' => $venda_id,
                ':company_id' => $this->company_id
            ];
            $this->db->execute($sql, $params);

            // Cancelar lançamento financeiro
            $sql = "UPDATE financeiro SET status = 'cancelado' WHERE venda_id = :venda_id AND company_id = :company_id";
            $params = [
                ':venda_id' => $venda_id,
                ':company_id' => $this->company_id
            ];
            $this->db->execute($sql, $params);

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Erro ao cancelar venda: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Buscar venda por ID
     */
    public function getById($id) {
        $sql = "SELECT v.*,
                t.nome as tutor_nome,
                p.nome as pet_nome
                FROM vendas v
                LEFT JOIN tutors t ON v.tutor_id = t.id
                LEFT JOIN pets p ON v.pet_id = p.id
                WHERE v.id = :id AND v.company_id = :company_id";
        $params = [
            ':id' => $id,
            ':company_id' => $this->company_id
        ];
        return $this->db->queryOne($sql, $params);
    }

    /**
     * Buscar itens da venda
     */
    public function getItens($venda_id) {
        $sql = "SELECT vi.*, pr.nome as profissional_nome
                FROM venda_itens vi
                LEFT JOIN profissionais pr ON vi.profissional_id = pr.id
                WHERE vi.venda_id = :venda_id";
        $params = [':venda_id' => $venda_id];
        return $this->db->query($sql, $params);
    }

    /**
     * Buscar todas as vendas
     */
    public function getAll($filtros = []) {
        $sql = "SELECT v.*,
                t.nome as tutor_nome,
                p.nome as pet_nome
                FROM vendas v
                LEFT JOIN tutors t ON v.tutor_id = t.id
                LEFT JOIN pets p ON v.pet_id = p.id
                WHERE v.company_id = :company_id";
        $params = [':company_id' => $this->company_id];

        // Filtro por status
        if (isset($filtros['status'])) {
            $sql .= " AND v.status = :status";
            $params[':status'] = $filtros['status'];
        }

        // Filtro por período
        if (isset($filtros['data_inicio']) && isset($filtros['data_fim'])) {
            $sql .= " AND DATE(v.data) BETWEEN :data_inicio AND :data_fim";
            $params[':data_inicio'] = $filtros['data_inicio'];
            $params[':data_fim'] = $filtros['data_fim'];
        }

        // Filtro por tutor (ID)
        if (isset($filtros['tutor_id'])) {
            $sql .= " AND v.tutor_id = :tutor_id";
            $params[':tutor_id'] = $filtros['tutor_id'];
        }

        // Filtro por nome do cliente
        if (!empty($filtros['cliente'])) {
            $sql .= " AND t.nome LIKE :cliente";
            $params[':cliente'] = '%' . $filtros['cliente'] . '%';
        }

        $sql .= " ORDER BY v.data DESC";

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
     * Contar total de vendas (respeita os mesmos filtros do getAll)
     */
    public function count($filtros = []) {
        $sql = "SELECT COUNT(*) as total FROM vendas v
                LEFT JOIN tutors t ON v.tutor_id = t.id
                WHERE v.company_id = :company_id";
        $params = [':company_id' => $this->company_id];

        if (isset($filtros['status'])) {
            $sql .= " AND v.status = :status";
            $params[':status'] = $filtros['status'];
        }

        if (isset($filtros['data_inicio']) && isset($filtros['data_fim'])) {
            $sql .= " AND DATE(v.data) BETWEEN :data_inicio AND :data_fim";
            $params[':data_inicio'] = $filtros['data_inicio'];
            $params[':data_fim']    = $filtros['data_fim'];
        }

        if (!empty($filtros['cliente'])) {
            $sql .= " AND t.nome LIKE :cliente";
            $params[':cliente'] = '%' . $filtros['cliente'] . '%';
        }

        $result = $this->db->queryOne($sql, $params);
        return $result['total'] ?? 0;
    }

    /**
     * Vendas do dia
     */
    public function getVendasDoDia($data = null) {
        if (!$data) {
            $data = date('Y-m-d');
        }

        $sql = "SELECT v.*, t.nome as tutor_nome
                FROM vendas v
                LEFT JOIN tutors t ON v.tutor_id = t.id
                WHERE v.company_id = :company_id
                AND DATE(v.data) = :data
                AND v.status = 'finalizada'
                ORDER BY v.data DESC";

        $params = [
            ':company_id' => $this->company_id,
            ':data' => $data
        ];

        return $this->db->query($sql, $params);
    }

    /**
     * Faturamento por período
     */
    public function getFaturamentoPorPeriodo($data_inicio, $data_fim) {
        $sql = "SELECT
                    COUNT(*) as total_vendas,
                    SUM(valor_final) as faturamento_total,
                    AVG(valor_final) as ticket_medio
                FROM vendas
                WHERE company_id = :company_id
                AND DATE(data) BETWEEN :data_inicio AND :data_fim
                AND status = 'finalizada'";

        $params = [
            ':company_id' => $this->company_id,
            ':data_inicio' => $data_inicio,
            ':data_fim' => $data_fim
        ];

        return $this->db->queryOne($sql, $params);
    }

    /**
     * Produtos mais vendidos
     */
    public function getProdutosMaisVendidos($limit = 10, $data_inicio = null, $data_fim = null) {
        $sql = "SELECT
                    vi.nome_item,
                    SUM(vi.quantidade) as quantidade_vendida,
                    SUM(vi.preco_total) as receita_total
                FROM venda_itens vi
                INNER JOIN vendas v ON vi.venda_id = v.id
                WHERE vi.tipo_item = 'produto'
                AND v.company_id = :company_id
                AND v.status = 'finalizada'";

        $params = [':company_id' => $this->company_id];

        if ($data_inicio && $data_fim) {
            $sql .= " AND DATE(v.data) BETWEEN :data_inicio AND :data_fim";
            $params[':data_inicio'] = $data_inicio;
            $params[':data_fim'] = $data_fim;
        }

        $sql .= " GROUP BY vi.item_id, vi.nome_item ORDER BY quantidade_vendida DESC LIMIT :limit";

        $stmt = $this->db->getConnection()->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
