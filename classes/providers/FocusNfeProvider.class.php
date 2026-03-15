<?php
/**
 * FocusNfeProvider
 * Implementação da integração com a API Focus NFe para emissão de NFS-e.
 * Documentação: https://focusnfe.com.br/doc/
 */
class FocusNfeProvider {

    private string $baseUrl;
    private string $token;

    // Mapeamento de status da API → status interno
    private const STATUS_MAP = [
        'autorizado'               => 'emitida',
        'processando_autorizacao'  => 'processando',
        'erro_autorizacao'         => 'erro',
        'cancelado'                => 'cancelada',
        'cancelamento_pendente'    => 'processando',
    ];

    public function __construct(array $config) {
        $ambiente       = $config['nfse_ambiente'] ?? 'homologacao';
        $this->token    = $config['nfse_api_token'] ?? '';
        $this->baseUrl  = $ambiente === 'producao'
            ? 'https://api.focusnfe.com.br'
            : 'https://homologacao.focusnfe.com.br';
    }

    /**
     * Emite uma NFS-e.
     *
     * @param  string $ref     Referência única (ex: PAW1NF7)
     * @param  array  $payload Dados para emissão (formato Focus NFe NFS-e)
     * @return array  ['sucesso' => bool, 'status' => string, 'dados' => array, 'erro' => string]
     */
    public function emitir(string $ref, array $payload): array {
        $response = $this->request('POST', '/v2/nfse?ref=' . urlencode($ref), $payload);
        return $this->parseResponse($response);
    }

    /**
     * Consulta o status de uma NFS-e pelo ref.
     */
    public function consultar(string $ref): array {
        $response = $this->request('GET', '/v2/nfse/' . urlencode($ref));
        return $this->parseResponse($response);
    }

    /**
     * Cancela uma NFS-e.
     */
    public function cancelar(string $ref): array {
        $response = $this->request('DELETE', '/v2/nfse/' . urlencode($ref));
        return $this->parseResponse($response);
    }

    /**
     * Retorna a URL do PDF da NFS-e (link_nfse do response).
     */
    public function getPdfUrl(string $ref): ?string {
        $resultado = $this->consultar($ref);
        return $resultado['dados']['link_nfse'] ?? $resultado['dados']['link_danfse'] ?? null;
    }

    // --------------------------------------------------------
    // Privado: HTTP
    // --------------------------------------------------------

    private function request(string $method, string $endpoint, array $body = []): array {
        if (empty($this->token)) {
            return ['http_code' => 0, 'body' => ['erro' => 'Token da API não configurado.']];
        }

        $url = $this->baseUrl . $endpoint;
        $ch  = curl_init($url);

        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
        ];

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_USERPWD        => $this->token . ':',   // Focus NFe: token como usuário, senha vazia
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        switch (strtoupper($method)) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
                break;
            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
            // GET é padrão
        }

        $raw       = curl_exec($ch);
        $httpCode  = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            error_log("[FocusNfe] cURL error: $curlError");
            return ['http_code' => 0, 'body' => ['erro' => "Erro de comunicação: $curlError"]];
        }

        $decoded = json_decode($raw, true) ?? [];
        return ['http_code' => $httpCode, 'body' => $decoded, 'raw' => $raw];
    }

    private function parseResponse(array $response): array {
        $httpCode = $response['http_code'];
        $body     = $response['body'] ?? [];
        $raw      = $response['raw'] ?? json_encode($body);

        // Mapeia status da API para status interno
        $statusApi = $body['status'] ?? '';
        $statusInterno = self::STATUS_MAP[$statusApi] ?? null;

        if ($httpCode === 0) {
            return [
                'sucesso'       => false,
                'status'        => 'erro',
                'dados'         => $body,
                'erro'          => $body['erro'] ?? 'Falha de comunicação com a API.',
                'resposta_json' => $raw,
            ];
        }

        // 2xx = sucesso ou processando
        if ($httpCode >= 200 && $httpCode < 300) {
            return [
                'sucesso'             => true,
                'status'              => $statusInterno ?? 'processando',
                'numero_nota'         => $body['numero'] ?? $body['numero_nfse'] ?? null,
                'codigo_verificacao'  => $body['codigo_verificacao'] ?? null,
                'pdf_url'             => $body['link_nfse'] ?? $body['link_danfse'] ?? null,
                'dados'               => $body,
                'erro'                => null,
                'resposta_json'       => $raw,
            ];
        }

        // Erros (4xx, 5xx)
        $mensagem = $body['mensagem'] ?? $body['erros'][0]['mensagem'] ?? $body['erro'] ?? "Erro HTTP $httpCode";
        return [
            'sucesso'       => false,
            'status'        => 'erro',
            'dados'         => $body,
            'erro'          => $mensagem,
            'resposta_json' => $raw,
        ];
    }
}
