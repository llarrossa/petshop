<?php
/**
 * Classe NotaFiscal
 * Gerencia registros de notas fiscais e orquestra a emissão via NfeService.
 */

require_once __DIR__ . '/../database/connection.php';
require_once __DIR__ . '/NfeService.class.php';

class NotaFiscal {

    private $db;
    private $company_id;

    // Propriedades do registro
    public $id;
    public $venda_id;
    public $cliente_id;
    public $ref_externa;
    public $numero_nota;
    public $codigo_verificacao;
    public $status;
    public $valor;
    public $descricao_servico;
    public $data_emissao;
    public $pdf_url;
    public $resposta_api;
    public $mensagem_erro;

    public function __construct($company_id = null) {
        $this->db         = Database::getInstance();
        $this->company_id = $company_id ?? getCompanyId();
    }

    // --------------------------------------------------------
    // CRUD básico
    // --------------------------------------------------------

    public function create(): bool {
        $sql = "INSERT INTO notas_fiscais
                    (company_id, venda_id, cliente_id, ref_externa, status,
                     valor, descricao_servico, data_emissao,
                     numero_nota, codigo_verificacao, pdf_url, resposta_api, mensagem_erro)
                VALUES
                    (:company_id, :venda_id, :cliente_id, :ref_externa, :status,
                     :valor, :descricao_servico, :data_emissao,
                     :numero_nota, :codigo_verificacao, :pdf_url, :resposta_api, :mensagem_erro)";

        $params = [
            ':company_id'         => $this->company_id,
            ':venda_id'           => $this->venda_id          ?: null,
            ':cliente_id'         => $this->cliente_id        ?: null,
            ':ref_externa'        => $this->ref_externa        ?? null,
            ':status'             => $this->status             ?? 'pendente',
            ':valor'              => $this->valor              ?? null,
            ':descricao_servico'  => $this->descricao_servico  ?? null,
            ':data_emissao'       => $this->data_emissao       ?? null,
            ':numero_nota'        => $this->numero_nota        ?? null,
            ':codigo_verificacao' => $this->codigo_verificacao ?? null,
            ':pdf_url'            => $this->pdf_url            ?? null,
            ':resposta_api'       => $this->resposta_api       ?? null,
            ':mensagem_erro'      => $this->mensagem_erro      ?? null,
        ];

        if ($this->db->execute($sql, $params)) {
            $this->id = $this->db->lastInsertId();
            return true;
        }
        return false;
    }

    private function updateFromArray(int $id, array $fields): bool {
        $sets   = [];
        $params = [':id' => $id, ':company_id' => $this->company_id];
        foreach ($fields as $col => $val) {
            $sets[]         = "$col = :$col";
            $params[":$col"] = $val;
        }
        $sql = "UPDATE notas_fiscais SET " . implode(', ', $sets)
             . " WHERE id = :id AND company_id = :company_id";
        return $this->db->execute($sql, $params);
    }

    public function delete(int $id): bool {
        return $this->db->execute(
            "DELETE FROM notas_fiscais WHERE id = :id AND company_id = :company_id",
            [':id' => $id, ':company_id' => $this->company_id]
        );
    }

    // --------------------------------------------------------
    // Consultas
    // --------------------------------------------------------

    public function getById(int $id): ?array {
        $sql = "SELECT nf.*,
                       t.nome    AS cliente_nome,
                       t.cpf     AS cliente_cpf,
                       t.email   AS cliente_email,
                       t.telefone AS cliente_telefone
                FROM notas_fiscais nf
                LEFT JOIN tutors t ON nf.cliente_id = t.id
                WHERE nf.id = :id AND nf.company_id = :company_id";
        return $this->db->queryOne($sql, [':id' => $id, ':company_id' => $this->company_id]) ?: null;
    }

    public function getByVenda(int $venda_id): ?array {
        return $this->db->queryOne(
            "SELECT * FROM notas_fiscais WHERE venda_id = :venda_id AND company_id = :company_id ORDER BY id DESC LIMIT 1",
            [':venda_id' => $venda_id, ':company_id' => $this->company_id]
        ) ?: null;
    }

    public function getAll(array $filtros = []): array {
        [$where, $params] = $this->buildWhere($filtros);

        $sql = "SELECT nf.*,
                       t.nome AS cliente_nome
                FROM notas_fiscais nf
                LEFT JOIN tutors t ON nf.cliente_id = t.id
                WHERE $where
                ORDER BY nf.created_at DESC";

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

    public function count(array $filtros = []): int {
        [$where, $params] = $this->buildWhere($filtros);
        $result = $this->db->queryOne(
            "SELECT COUNT(*) as total FROM notas_fiscais nf WHERE $where",
            $params
        );
        return (int)($result['total'] ?? 0);
    }

    // --------------------------------------------------------
    // Operações de emissão / consulta / cancelamento
    // --------------------------------------------------------

    /**
     * Emite a NFS-e via API para um registro já salvo (id = $this->id).
     * Atualiza o registro com a resposta e retorna resultado.
     */
    public function emitirViaApi(array $config_fiscal, array $tomador, array $servico_dados): array {
        $config_fiscal_arr = (array)$config_fiscal;

        if (empty($config_fiscal_arr['nfse_api_token'])) {
            return ['sucesso' => false, 'erro' => 'Token da API não configurado. Configure em Minha Conta → Dados Fiscais.'];
        }

        $ref     = 'PAW' . $this->company_id . 'NF' . $this->id;
        $payload = NfeService::montarPayload($config_fiscal_arr, $tomador, $servico_dados);

        $service   = new NfeService($config_fiscal_arr);
        $resultado = $service->emitir($ref, $payload);

        // Persiste resultado
        $this->updateFromArray($this->id, [
            'ref_externa'        => $ref,
            'status'             => $resultado['status']              ?? 'erro',
            'numero_nota'        => $resultado['numero_nota']         ?? null,
            'codigo_verificacao' => $resultado['codigo_verificacao']  ?? null,
            'pdf_url'            => $resultado['pdf_url']             ?? null,
            'resposta_api'       => $resultado['resposta_json']       ?? null,
            'mensagem_erro'      => $resultado['erro']                ?? null,
        ]);

        // Marca venda com NF vinculada
        if (!empty($this->venda_id) && ($resultado['status'] ?? '') === 'emitida') {
            $this->db->execute(
                "UPDATE vendas SET tem_nota_fiscal = 1 WHERE id = :id AND company_id = :company_id",
                [':id' => $this->venda_id, ':company_id' => $this->company_id]
            );
        }

        return $resultado;
    }

    /**
     * Consulta status na API e atualiza o registro.
     */
    public function consultarStatus(int $id, array $config_fiscal): array {
        $nf = $this->getById($id);
        if (!$nf || empty($nf['ref_externa'])) {
            return ['sucesso' => false, 'erro' => 'Referência externa não encontrada.'];
        }

        $service   = new NfeService($config_fiscal);
        $resultado = $service->consultar($nf['ref_externa']);

        $this->updateFromArray($id, [
            'status'             => $resultado['status']              ?? $nf['status'],
            'numero_nota'        => $resultado['numero_nota']         ?? $nf['numero_nota'],
            'codigo_verificacao' => $resultado['codigo_verificacao']  ?? $nf['codigo_verificacao'],
            'pdf_url'            => $resultado['pdf_url']             ?? $nf['pdf_url'],
            'resposta_api'       => $resultado['resposta_json']       ?? $nf['resposta_api'],
            'mensagem_erro'      => $resultado['erro']                ?? null,
        ]);

        // Marca venda se acabou de ser autorizada
        if (!empty($nf['venda_id']) && ($resultado['status'] ?? '') === 'emitida') {
            $this->db->execute(
                "UPDATE vendas SET tem_nota_fiscal = 1 WHERE id = :id AND company_id = :company_id",
                [':id' => $nf['venda_id'], ':company_id' => $this->company_id]
            );
        }

        return $resultado;
    }

    /**
     * Cancela a NFS-e na API e atualiza o registro.
     */
    public function cancelarViaApi(int $id, array $config_fiscal): array {
        $nf = $this->getById($id);
        if (!$nf || empty($nf['ref_externa'])) {
            return ['sucesso' => false, 'erro' => 'Referência externa não encontrada.'];
        }

        $service   = new NfeService($config_fiscal);
        $resultado = $service->cancelar($nf['ref_externa']);

        if ($resultado['sucesso'] || ($resultado['status'] ?? '') === 'cancelada') {
            $this->updateFromArray($id, [
                'status'       => 'cancelada',
                'resposta_api' => $resultado['resposta_json'] ?? $nf['resposta_api'],
            ]);
            if (!empty($nf['venda_id'])) {
                $this->db->execute(
                    "UPDATE vendas SET tem_nota_fiscal = 0 WHERE id = :id AND company_id = :company_id",
                    [':id' => $nf['venda_id'], ':company_id' => $this->company_id]
                );
            }
        }

        return $resultado;
    }

    // --------------------------------------------------------
    // Configuração fiscal da empresa
    // --------------------------------------------------------

    public function getConfigFiscal(): array {
        return $this->db->queryOne(
            "SELECT * FROM config_fiscal WHERE company_id = :company_id",
            [':company_id' => $this->company_id]
        ) ?: [];
    }

    public function salvarConfigFiscal(array $dados): bool {
        $existe = $this->getConfigFiscal();

        $campos = [
            'cnpj', 'razao_social', 'inscricao_municipal',
            'logradouro', 'numero_endereco', 'complemento', 'bairro',
            'codigo_municipio', 'municipio', 'uf', 'cep',
            'codigo_servico', 'codigo_tributario_municipio',
            'aliquota_iss', 'nfse_api_token', 'nfse_ambiente', 'nfse_provedor',
        ];

        $filtrado = [];
        foreach ($campos as $c) {
            if (array_key_exists($c, $dados)) {
                $filtrado[$c] = $dados[$c] ?: null;
            }
        }

        if ($existe) {
            $sets   = array_map(fn($c) => "$c = :$c", array_keys($filtrado));
            $params = array_combine(
                array_map(fn($c) => ":$c", array_keys($filtrado)),
                array_values($filtrado)
            );
            $params[':company_id'] = $this->company_id;
            return $this->db->execute(
                "UPDATE config_fiscal SET " . implode(', ', $sets) . " WHERE company_id = :company_id",
                $params
            );
        }

        $filtrado['company_id'] = $this->company_id;
        $cols   = implode(', ', array_keys($filtrado));
        $placeholders = implode(', ', array_map(fn($c) => ":$c", array_keys($filtrado)));
        $params = array_combine(
            array_map(fn($c) => ":$c", array_keys($filtrado)),
            array_values($filtrado)
        );
        return $this->db->execute(
            "INSERT INTO config_fiscal ($cols) VALUES ($placeholders)",
            $params
        );
    }

    // --------------------------------------------------------
    // Privado
    // --------------------------------------------------------

    private function buildWhere(array $filtros): array {
        $where  = "nf.company_id = :company_id";
        $params = [':company_id' => $this->company_id];

        if (!empty($filtros['status'])) {
            $where .= " AND nf.status = :status";
            $params[':status'] = $filtros['status'];
        }
        if (!empty($filtros['cliente_id'])) {
            $where .= " AND nf.cliente_id = :cliente_id";
            $params[':cliente_id'] = $filtros['cliente_id'];
        }
        if (!empty($filtros['data_inicio'])) {
            $where .= " AND DATE(nf.created_at) >= :data_inicio";
            $params[':data_inicio'] = $filtros['data_inicio'];
        }
        if (!empty($filtros['data_fim'])) {
            $where .= " AND DATE(nf.created_at) <= :data_fim";
            $params[':data_fim'] = $filtros['data_fim'];
        }

        return [$where, $params];
    }
}
