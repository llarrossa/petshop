<?php
/**
 * Controller: Notas Fiscais (NFS-e)
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/NotaFiscal.class.php';
require_once __DIR__ . '/../classes/Venda.class.php';
require_once __DIR__ . '/../classes/Tutor.class.php';

verificarLogin();

if (!moduloDisponivel('nota_fiscal')) {
    $_SESSION['error'] = 'Módulo não disponível no seu plano.';
    header('Location: ?page=dashboard');
    exit;
}

$action    = $_GET['action'] ?? 'list';
$nfObj     = new NotaFiscal();
$vendaObj  = new Venda();
$tutorObj  = new Tutor();

$back_url  = '?' . ($_SESSION['nf_qs'] ?? 'page=notas_fiscais&action=list');

switch ($action) {

    // --------------------------------------------------------
    // LISTAGEM
    // --------------------------------------------------------
    case 'list':
        $status      = $_GET['status']      ?? '';
        $data_inicio = $_GET['data_inicio'] ?? '';
        $data_fim    = $_GET['data_fim']    ?? '';
        $cliente_id  = $_GET['cliente_id']  ?? '';

        if (!$data_inicio) { $data_inicio = date('Y-m-01'); $data_fim = date('Y-m-t'); }

        $qs_params = array_filter(['page' => 'notas_fiscais', 'action' => 'list',
            'status' => $status, 'data_inicio' => $data_inicio,
            'data_fim' => $data_fim, 'cliente_id' => $cliente_id]);
        $_SESSION['nf_qs'] = http_build_query($qs_params);
        $back_url = '?' . $_SESSION['nf_qs'];

        $filtros       = array_filter(compact('status', 'data_inicio', 'data_fim', 'cliente_id'));
        $por_pagina    = 20;
        $pagina_atual  = max(1, (int)($_GET['p'] ?? 1));
        $total         = $nfObj->count($filtros);
        $total_paginas = max(1, (int)ceil($total / $por_pagina));
        $offset        = ($pagina_atual - 1) * $por_pagina;

        $notas   = $nfObj->getAll(array_merge($filtros, ['limit' => $por_pagina, 'offset' => $offset]));
        $tutores = $tutorObj->getAll();
        $qs_filtros = http_build_query(array_filter([
            'page' => 'notas_fiscais', 'action' => 'list',
            'status' => $status, 'data_inicio' => $data_inicio,
            'data_fim' => $data_fim, 'cliente_id' => $cliente_id,
        ]));

        include __DIR__ . '/../views/notas_fiscais/list.php';
        break;

    // --------------------------------------------------------
    // EMITIR NOVA NF (form + processamento)
    // --------------------------------------------------------
    case 'emitir':
        $venda_id = (int)($_GET['venda_id'] ?? 0);
        $venda    = $venda_id ? $vendaObj->getById($venda_id) : null;

        // Verifica se já existe NF para esta venda
        if ($venda_id && $venda) {
            $nf_existente = $nfObj->getByVenda($venda_id);
            if ($nf_existente && in_array($nf_existente['status'], ['emitida', 'processando'])) {
                $_SESSION['error'] = 'Já existe uma nota fiscal ' . $nf_existente['status'] . ' para esta venda.';
                header('Location: ?page=notas_fiscais&action=view&id=' . $nf_existente['id']);
                exit;
            }
        }

        $config_fiscal = $nfObj->getConfigFiscal();
        $itens_venda   = $venda_id ? $vendaObj->getItens($venda_id) : [];
        $tutor         = ($venda && $venda['tutor_id']) ? $tutorObj->getById($venda['tutor_id']) : null;

        // Descrição automática dos itens da venda
        $descricao_auto = '';
        if (!empty($itens_venda)) {
            $partes = array_map(fn($i) => $i['nome_item'] . ' (x' . $i['quantidade'] . ')', $itens_venda);
            $descricao_auto = implode(', ', $partes);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            validateCsrfToken();
            $erros = [];

            if (empty($config_fiscal)) {
                $erros[] = 'Configure os dados fiscais em Minha Conta → Dados Fiscais antes de emitir.';
            }
            if (empty(sanitize($_POST['cpf_cnpj_tomador'] ?? ''))) {
                $erros[] = 'CPF ou CNPJ do tomador é obrigatório.';
            }
            if (empty(sanitize($_POST['descricao_servico'] ?? ''))) {
                $erros[] = 'Descrição do serviço é obrigatória.';
            }
            if (empty(sanitize($_POST['data_emissao'] ?? ''))) {
                $erros[] = 'Data da emissão é obrigatória.';
            }

            if (empty($erros)) {
                $cpf_cnpj = preg_replace('/\D/', '', sanitize($_POST['cpf_cnpj_tomador']));
                $tomador  = [
                    'nome'       => sanitize($_POST['nome_tomador']),
                    'email'      => sanitize($_POST['email_tomador']),
                    'cpf'        => strlen($cpf_cnpj) <= 11 ? $cpf_cnpj : null,
                    'cnpj'       => strlen($cpf_cnpj) > 11  ? $cpf_cnpj : null,
                    'endereco'   => sanitize($_POST['endereco_tomador'] ?? ''),
                    'estado'     => sanitize($_POST['uf_tomador'] ?? ''),
                    'cep'        => sanitize($_POST['cep_tomador'] ?? ''),
                ];

                $servico_dados = [
                    'descricao'    => sanitize($_POST['descricao_servico']),
                    'valor'        => (float)str_replace(',', '.', $_POST['valor']),
                    'data_emissao' => sanitize($_POST['data_emissao']),
                ];

                // Cria registro com status pendente
                $nfObj->venda_id         = $venda_id ?: null;
                $nfObj->cliente_id       = $venda['tutor_id'] ?? null;
                $nfObj->status           = 'pendente';
                $nfObj->valor            = $servico_dados['valor'];
                $nfObj->descricao_servico = $servico_dados['descricao'];
                $nfObj->data_emissao      = $servico_dados['data_emissao'];

                if ($nfObj->create()) {
                    $resultado = $nfObj->emitirViaApi($config_fiscal, $tomador, $servico_dados);

                    if ($resultado['sucesso'] || in_array($resultado['status'] ?? '', ['emitida', 'processando'])) {
                        $_SESSION['success'] = 'Nota fiscal enviada! Status: ' . ($resultado['status'] ?? 'processando') . '.';
                    } else {
                        $_SESSION['error'] = 'Erro na emissão: ' . ($resultado['erro'] ?? 'Verifique os dados fiscais.');
                    }
                    header('Location: ?page=notas_fiscais&action=view&id=' . $nfObj->id);
                    exit;
                } else {
                    $erros[] = 'Erro interno ao salvar registro. Tente novamente.';
                }
            }
        }

        include __DIR__ . '/../views/notas_fiscais/emitir.php';
        break;

    // --------------------------------------------------------
    // VISUALIZAR DETALHES
    // --------------------------------------------------------
    case 'view':
        $id   = (int)$_GET['id'];
        $nota = $nfObj->getById($id);

        if (!$nota) {
            $_SESSION['error'] = 'Nota fiscal não encontrada.';
            header('Location: ' . $back_url);
            exit;
        }

        $venda         = $nota['venda_id'] ? $vendaObj->getById($nota['venda_id']) : null;
        $itens_venda   = $nota['venda_id'] ? $vendaObj->getItens($nota['venda_id']) : [];
        $config_fiscal = $nfObj->getConfigFiscal();

        include __DIR__ . '/../views/notas_fiscais/view.php';
        break;

    // --------------------------------------------------------
    // CONSULTAR STATUS NA API
    // --------------------------------------------------------
    case 'consultar':
        $id            = (int)$_GET['id'];
        $config_fiscal = $nfObj->getConfigFiscal();

        if (empty($config_fiscal['nfse_api_token'])) {
            $_SESSION['error'] = 'Token da API não configurado.';
            header('Location: ?page=notas_fiscais&action=view&id=' . $id);
            exit;
        }

        $resultado = $nfObj->consultarStatus($id, $config_fiscal);

        if ($resultado['sucesso'] || !empty($resultado['status'])) {
            $_SESSION['success'] = 'Status atualizado: ' . ($resultado['status'] ?? 'sem status') . '.';
        } else {
            $_SESSION['error'] = 'Erro ao consultar: ' . ($resultado['erro'] ?? 'Falha de comunicação.');
        }

        header('Location: ?page=notas_fiscais&action=view&id=' . $id);
        exit;

    // --------------------------------------------------------
    // CANCELAR NF
    // --------------------------------------------------------
    case 'cancelar':
        $id            = (int)$_GET['id'];
        $config_fiscal = $nfObj->getConfigFiscal();

        if (empty($config_fiscal['nfse_api_token'])) {
            $_SESSION['error'] = 'Token da API não configurado.';
            header('Location: ?page=notas_fiscais&action=view&id=' . $id);
            exit;
        }

        $resultado = $nfObj->cancelarViaApi($id, $config_fiscal);

        if ($resultado['sucesso'] || ($resultado['status'] ?? '') === 'cancelada') {
            $_SESSION['success'] = 'Nota fiscal cancelada.';
        } else {
            $_SESSION['error'] = 'Erro ao cancelar: ' . ($resultado['erro'] ?? 'Verifique na prefeitura.');
        }

        header('Location: ?page=notas_fiscais&action=view&id=' . $id);
        exit;

    default:
        header('Location: ?page=notas_fiscais&action=list');
        exit;
}
