<?php
/**
 * NfeService
 * Façade para integração com provedores de NFS-e.
 * Troca de provedor: altere nfse_provedor na config_fiscal da empresa.
 */

require_once __DIR__ . '/providers/FocusNfeProvider.class.php';

class NfeService {

    private $provider;

    public function __construct(array $config) {
        $provedor = $config['nfse_provedor'] ?? 'focusnfe';

        $this->provider = match ($provedor) {
            'focusnfe' => new FocusNfeProvider($config),
            default    => new FocusNfeProvider($config),  // fallback
        };
    }

    /** Emite NFS-e. Retorna array normalizado do provider. */
    public function emitir(string $ref, array $payload): array {
        return $this->provider->emitir($ref, $payload);
    }

    /** Consulta status pelo ref externo. */
    public function consultar(string $ref): array {
        return $this->provider->consultar($ref);
    }

    /** Cancela a NFS-e pelo ref externo. */
    public function cancelar(string $ref): array {
        return $this->provider->cancelar($ref);
    }

    /** Monta o payload NFS-e no formato Focus NFe a partir dos dados do sistema. */
    public static function montarPayload(array $config_fiscal, array $tomador, array $servico): array {
        return [
            'data_emissao' => $servico['data_emissao'],

            'prestador' => [
                'cnpj'                => preg_replace('/\D/', '', $config_fiscal['cnpj'] ?? ''),
                'inscricao_municipal' => $config_fiscal['inscricao_municipal'] ?? '',
                'codigo_municipio'    => $config_fiscal['codigo_municipio'] ?? '',
            ],

            'tomador' => [
                'cpf'          => !empty($tomador['cpf'])  ? preg_replace('/\D/', '', $tomador['cpf'])  : null,
                'cnpj'         => !empty($tomador['cnpj']) ? preg_replace('/\D/', '', $tomador['cnpj']) : null,
                'razao_social' => $tomador['nome'] ?? '',
                'email'        => $tomador['email'] ?? null,
                'endereco'     => array_filter([
                    'logradouro'       => $tomador['endereco'] ?? null,
                    'numero'           => $tomador['numero']   ?? 'S/N',
                    'complemento'      => $tomador['complemento'] ?? null,
                    'bairro'           => $tomador['bairro']   ?? null,
                    'codigo_municipio' => $tomador['codigo_municipio'] ?? $config_fiscal['codigo_municipio'] ?? null,
                    'uf'               => $tomador['uf'] ?? $tomador['estado'] ?? null,
                    'cep'              => preg_replace('/\D/', '', $tomador['cep'] ?? ''),
                ]),
            ],

            'servicos' => [
                [
                    'descricao'                   => $servico['descricao'],
                    'aliquota'                    => (float)($config_fiscal['aliquota_iss'] ?? 0.05),
                    'base_calculo'                => (float)$servico['valor'],
                    'valor_liquido'               => (float)$servico['valor'],
                    'item_lista_servico'          => $config_fiscal['codigo_servico'] ?? '',
                    'codigo_tributario_municipio' => $config_fiscal['codigo_tributario_municipio'] ?? '',
                ],
            ],
        ];
    }
}
