<?php
/**
 * Classe Produto
 * Gerencia operações CRUD de produtos
 */

require_once __DIR__ . '/../database/connection.php';

class Produto {
    private $db;
    private $company_id;

    // Propriedades
    public $id;
    public $nome;
    public $descricao;
    public $sku;
    public $categoria;
    public $preco_venda;
    public $preco_custo;
    public $estoque_atual;
    public $estoque_minimo;
    public $unidade;
    public $status;

    /**
     * Construtor
     */
    public function __construct($company_id = null) {
        $this->db = Database::getInstance();
        $this->company_id = $company_id ?? getCompanyId();
    }

    /**
     * Criar novo produto
     */
    public function create() {
        $sql = "INSERT INTO produtos (company_id, nome, descricao, sku, categoria, preco_venda, preco_custo, estoque_atual, estoque_minimo, unidade, status)
                VALUES (:company_id, :nome, :descricao, :sku, :categoria, :preco_venda, :preco_custo, :estoque_atual, :estoque_minimo, :unidade, :status)";

        $params = [
            ':company_id' => $this->company_id,
            ':nome' => $this->nome,
            ':descricao' => $this->descricao,
            ':sku' => $this->sku,
            ':categoria' => $this->categoria,
            ':preco_venda' => $this->preco_venda,
            ':preco_custo' => $this->preco_custo,
            ':estoque_atual' => $this->estoque_atual ?? 0,
            ':estoque_minimo' => $this->estoque_minimo ?? 0,
            ':unidade' => $this->unidade ?? 'UN',
            ':status' => $this->status ?? 'ativo'
        ];

        if ($this->db->execute($sql, $params)) {
            $this->id = $this->db->lastInsertId();
            return true;
        }
        return false;
    }

    /**
     * Atualizar produto existente
     */
    public function update() {
        $sql = "UPDATE produtos SET
                nome = :nome,
                descricao = :descricao,
                sku = :sku,
                categoria = :categoria,
                preco_venda = :preco_venda,
                preco_custo = :preco_custo,
                estoque_minimo = :estoque_minimo,
                unidade = :unidade,
                status = :status
                WHERE id = :id AND company_id = :company_id";

        $params = [
            ':id' => $this->id,
            ':company_id' => $this->company_id,
            ':nome' => $this->nome,
            ':descricao' => $this->descricao,
            ':sku' => $this->sku,
            ':categoria' => $this->categoria,
            ':preco_venda' => $this->preco_venda,
            ':preco_custo' => $this->preco_custo,
            ':estoque_minimo' => $this->estoque_minimo,
            ':unidade' => $this->unidade,
            ':status' => $this->status
        ];

        return $this->db->execute($sql, $params);
    }

    /**
     * Deletar produto
     */
    public function delete($id) {
        $sql = "DELETE FROM produtos WHERE id = :id AND company_id = :company_id";
        $params = [
            ':id' => $id,
            ':company_id' => $this->company_id
        ];
        return $this->db->execute($sql, $params);
    }

    /**
     * Buscar produto por ID
     */
    public function getById($id) {
        $sql = "SELECT * FROM produtos WHERE id = :id AND company_id = :company_id";
        $params = [
            ':id' => $id,
            ':company_id' => $this->company_id
        ];
        return $this->db->queryOne($sql, $params);
    }

    /**
     * Buscar todos os produtos
     */
    public function getAll($filtros = []) {
        $sql = "SELECT * FROM produtos WHERE company_id = :company_id";
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

        // Filtro por SKU
        if (isset($filtros['sku']) && !empty($filtros['sku'])) {
            $sql .= " AND sku LIKE :sku";
            $params[':sku'] = '%' . $filtros['sku'] . '%';
        }

        // Filtro por estoque baixo
        if (isset($filtros['estoque_baixo']) && $filtros['estoque_baixo'] === true) {
            $sql .= " AND estoque_atual <= estoque_minimo";
        }

        $allowed_sort = ['id', 'nome', 'sku', 'categoria', 'preco_venda', 'estoque_atual', 'status'];
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
     * Contar total de produtos (respeita os mesmos filtros do getAll)
     */
    public function count($filtros = []) {
        $sql = "SELECT COUNT(*) as total FROM produtos WHERE company_id = :company_id";
        $params = [':company_id' => $this->company_id];

        if (isset($filtros['status'])) {
            $sql .= " AND status = :status";
            $params[':status'] = $filtros['status'];
        }

        if (isset($filtros['nome']) && !empty($filtros['nome'])) {
            $sql .= " AND nome LIKE :nome";
            $params[':nome'] = '%' . $filtros['nome'] . '%';
        }

        if (isset($filtros['categoria']) && !empty($filtros['categoria'])) {
            $sql .= " AND categoria = :categoria";
            $params[':categoria'] = $filtros['categoria'];
        }

        if (isset($filtros['sku']) && !empty($filtros['sku'])) {
            $sql .= " AND sku LIKE :sku";
            $params[':sku'] = '%' . $filtros['sku'] . '%';
        }

        if (isset($filtros['estoque_baixo']) && $filtros['estoque_baixo'] === true) {
            $sql .= " AND estoque_atual <= estoque_minimo";
        }

        $result = $this->db->queryOne($sql, $params);
        return $result['total'] ?? 0;
    }

    /**
     * Verificar se SKU já existe (para outro produto da mesma empresa)
     */
    public function skuExiste($sku, $exclude_id = null) {
        if (empty($sku)) return false;
        $sql = "SELECT id FROM produtos WHERE company_id = :company_id AND sku = :sku";
        $params = [':company_id' => $this->company_id, ':sku' => $sku];
        if ($exclude_id !== null) {
            $sql .= " AND id != :exclude_id";
            $params[':exclude_id'] = $exclude_id;
        }
        return (bool) $this->db->queryOne($sql, $params);
    }

    /**
     * Atualizar estoque do produto
     */
    public function atualizarEstoque($produto_id, $quantidade, $operacao = 'adicionar') {
        if ($operacao === 'adicionar') {
            $sql = "UPDATE produtos SET estoque_atual = estoque_atual + :quantidade WHERE id = :id AND company_id = :company_id";
        } else {
            $sql = "UPDATE produtos SET estoque_atual = estoque_atual - :quantidade WHERE id = :id AND company_id = :company_id";
        }

        $params = [
            ':id' => $produto_id,
            ':quantidade' => abs($quantidade),
            ':company_id' => $this->company_id
        ];

        return $this->db->execute($sql, $params);
    }

    /**
     * Registrar movimentação de estoque
     */
    public function registrarMovimentacao($produto_id, $tipo, $quantidade, $motivo, $usuario_id = null) {
        $sql = "INSERT INTO estoque_movimentacoes (company_id, produto_id, tipo, quantidade, motivo, usuario_id)
                VALUES (:company_id, :produto_id, :tipo, :quantidade, :motivo, :usuario_id)";

        $params = [
            ':company_id' => $this->company_id,
            ':produto_id' => $produto_id,
            ':tipo' => $tipo,
            ':quantidade' => $quantidade,
            ':motivo' => $motivo,
            ':usuario_id' => $usuario_id
        ];

        if ($this->db->execute($sql, $params)) {
            // Atualizar estoque do produto
            if ($tipo === 'entrada') {
                $this->atualizarEstoque($produto_id, $quantidade, 'adicionar');
            } elseif ($tipo === 'saida') {
                $this->atualizarEstoque($produto_id, $quantidade, 'subtrair');
            }
            return true;
        }
        return false;
    }

    /**
     * Buscar produtos com estoque baixo
     */
    public function getProdutosEstoqueBaixo() {
        $sql = "SELECT * FROM produtos
                WHERE company_id = :company_id
                AND status = 'ativo'
                AND estoque_atual <= estoque_minimo
                ORDER BY estoque_atual ASC";
        $params = [':company_id' => $this->company_id];
        return $this->db->query($sql, $params);
    }

    /**
     * Buscar histórico de movimentações
     */
    public function getHistoricoMovimentacoes($produto_id, $limit = 20) {
        $sql = "SELECT em.*, u.nome as usuario_nome
                FROM estoque_movimentacoes em
                LEFT JOIN users u ON em.usuario_id = u.id
                WHERE em.produto_id = :produto_id AND em.company_id = :company_id
                ORDER BY em.data DESC
                LIMIT :limit";

        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->bindValue(':produto_id', $produto_id, PDO::PARAM_INT);
        $stmt->bindValue(':company_id', $this->company_id, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Calcular margem de lucro
     */
    public function calcularMargem($preco_custo, $preco_venda) {
        if ($preco_custo == 0) return 0;
        return (($preco_venda - $preco_custo) / $preco_custo) * 100;
    }

    /**
     * Buscar categorias únicas
     */
    public function getCategorias() {
        $sql = "SELECT DISTINCT categoria FROM produtos
                WHERE company_id = :company_id
                AND categoria IS NOT NULL
                AND categoria != ''
                ORDER BY categoria";
        $params = [':company_id' => $this->company_id];
        return $this->db->query($sql, $params);
    }

    /**
     * Valor total do estoque
     */
    public function getValorTotalEstoque() {
        $sql = "SELECT SUM(estoque_atual * preco_custo) as total
                FROM produtos
                WHERE company_id = :company_id AND status = 'ativo'";
        $params = [':company_id' => $this->company_id];
        $result = $this->db->queryOne($sql, $params);
        return $result['total'] ?? 0;
    }
}
