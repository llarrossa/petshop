<?php
/**
 * Funções Auxiliares do Sistema
 * Pet Shop SaaS
 */

/**
 * Formatar valor monetário para exibição
 */
function formatarMoedaBR($valor) {
    return 'R$ ' . number_format($valor, 2, ',', '.');
}

/**
 * Converter valor monetário BR para float
 */
function moedaBRparaFloat($valor) {
    $valor = str_replace('R$', '', $valor);
    $valor = str_replace('.', '', $valor);
    $valor = str_replace(',', '.', $valor);
    return (float)trim($valor);
}

/**
 * Formatar data para padrão brasileiro
 */
function formatarDataBR($data, $com_hora = false) {
    if (empty($data)) return '';

    try {
        $dt = new DateTime($data);
        if ($com_hora) {
            return $dt->format('d/m/Y H:i');
        }
        return $dt->format('d/m/Y');
    } catch (Exception $e) {
        return '';
    }
}

/**
 * Converter data BR para formato MySQL
 */
function dataBRparaMYSQL($data) {
    if (empty($data)) return null;

    $partes = explode('/', $data);
    if (count($partes) == 3) {
        return $partes[2] . '-' . $partes[1] . '-' . $partes[0];
    }
    return null;
}

/**
 * Calcular idade a partir da data de nascimento
 */
function calcularIdade($data_nascimento) {
    if (empty($data_nascimento)) return null;

    try {
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
    } catch (Exception $e) {
        return null;
    }
}

/**
 * Validar CPF
 */
function validarCPF($cpf) {
    $cpf = preg_replace('/[^0-9]/', '', $cpf);

    if (strlen($cpf) != 11 || preg_match('/(\d)\1{10}/', $cpf)) {
        return false;
    }

    for ($t = 9; $t < 11; $t++) {
        for ($d = 0, $c = 0; $c < $t; $c++) {
            $d += $cpf[$c] * (($t + 1) - $c);
        }
        $d = ((10 * $d) % 11) % 10;
        if ($cpf[$c] != $d) {
            return false;
        }
    }
    return true;
}

/**
 * Validar CNPJ
 */
function validarCNPJ($cnpj) {
    $cnpj = preg_replace('/[^0-9]/', '', $cnpj);

    if (strlen($cnpj) != 14) {
        return false;
    }

    if (preg_match('/(\d)\1{13}/', $cnpj)) {
        return false;
    }

    $soma = 0;
    $multiplicador = 5;

    for ($i = 0; $i < 12; $i++) {
        $soma += $cnpj[$i] * $multiplicador;
        $multiplicador = ($multiplicador == 2) ? 9 : $multiplicador - 1;
    }

    $resto = $soma % 11;
    $digito1 = ($resto < 2) ? 0 : 11 - $resto;

    if ($cnpj[12] != $digito1) {
        return false;
    }

    $soma = 0;
    $multiplicador = 6;

    for ($i = 0; $i < 13; $i++) {
        $soma += $cnpj[$i] * $multiplicador;
        $multiplicador = ($multiplicador == 2) ? 9 : $multiplicador - 1;
    }

    $resto = $soma % 11;
    $digito2 = ($resto < 2) ? 0 : 11 - $resto;

    return ($cnpj[13] == $digito2);
}

/**
 * Validar e-mail
 */
function validarEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Formatar telefone
 */
function formatarTelefone($telefone) {
    $telefone = preg_replace('/[^0-9]/', '', $telefone);

    if (strlen($telefone) == 11) {
        return '(' . substr($telefone, 0, 2) . ') ' . substr($telefone, 2, 5) . '-' . substr($telefone, 7);
    } elseif (strlen($telefone) == 10) {
        return '(' . substr($telefone, 0, 2) . ') ' . substr($telefone, 2, 4) . '-' . substr($telefone, 6);
    }

    return $telefone;
}

/**
 * Formatar CEP
 */
function formatarCEP($cep) {
    $cep = preg_replace('/[^0-9]/', '', $cep);

    if (strlen($cep) == 8) {
        return substr($cep, 0, 5) . '-' . substr($cep, 5);
    }

    return $cep;
}

/**
 * Gerar senha aleatória
 */
function gerarSenhaAleatoria($tamanho = 8) {
    $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%';
    $senha = '';

    for ($i = 0; $i < $tamanho; $i++) {
        $senha .= $caracteres[rand(0, strlen($caracteres) - 1)];
    }

    return $senha;
}

/**
 * Upload de arquivo
 */
function uploadArquivo($arquivo, $pasta_destino = 'uploads') {
    $extensoes_permitidas = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'];
    $mimes_permitidos = [
        'image/jpeg', 'image/png', 'image/gif',
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    ];
    $tamanho_maximo = 5 * 1024 * 1024; // 5MB

    if (!isset($arquivo['error']) || is_array($arquivo['error'])) {
        return ['success' => false, 'message' => 'Erro no upload do arquivo'];
    }

    if ($arquivo['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Erro ao fazer upload'];
    }

    if ($arquivo['size'] > $tamanho_maximo) {
        return ['success' => false, 'message' => 'Arquivo muito grande (máximo 5MB)'];
    }

    $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));

    if (!in_array($extensao, $extensoes_permitidas)) {
        return ['success' => false, 'message' => 'Tipo de arquivo não permitido'];
    }

    // Validar MIME type real do arquivo (previne upload disfarçado)
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($arquivo['tmp_name']);
    if (!in_array($mime, $mimes_permitidos)) {
        return ['success' => false, 'message' => 'Tipo de arquivo não permitido'];
    }

    // Sanitizar pasta de destino (previne path traversal)
    $pasta_destino = preg_replace('/[^a-zA-Z0-9_\-]/', '', $pasta_destino);
    if (empty($pasta_destino)) {
        $pasta_destino = 'uploads';
    }

    $nome_arquivo = uniqid() . '.' . $extensao;
    $caminho_completo = __DIR__ . '/../public/' . $pasta_destino . '/' . $nome_arquivo;

    if (!is_dir(dirname($caminho_completo))) {
        mkdir(dirname($caminho_completo), 0777, true);
    }

    if (move_uploaded_file($arquivo['tmp_name'], $caminho_completo)) {
        return ['success' => true, 'file' => $nome_arquivo, 'path' => $pasta_destino . '/' . $nome_arquivo];
    }

    return ['success' => false, 'message' => 'Erro ao salvar arquivo'];
}

/**
 * Gerar log de atividade
 */
function registrarLog($tipo, $mensagem, $user_id = null) {
    $arquivo_log = __DIR__ . '/../logs/' . date('Y-m-d') . '.log';

    if (!is_dir(dirname($arquivo_log))) {
        mkdir(dirname($arquivo_log), 0777, true);
    }

    $linha = date('Y-m-d H:i:s') . " | {$tipo} | User: {$user_id} | {$mensagem}\n";
    file_put_contents($arquivo_log, $linha, FILE_APPEND);
}

/**
 * Enviar e-mail (básico)
 */
function enviarEmail($para, $assunto, $mensagem) {
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: " . APP_NAME . " <noreply@petshop.com>\r\n";

    return mail($para, $assunto, $mensagem, $headers);
}

/**
 * Calcular percentual
 */
function calcularPercentual($valor, $total) {
    if ($total == 0) return 0;
    return ($valor / $total) * 100;
}

/**
 * Calcular desconto
 */
function calcularDesconto($valor, $percentual) {
    return $valor * ($percentual / 100);
}

/**
 * Gerar código único
 */
function gerarCodigoUnico($prefixo = '') {
    return $prefixo . strtoupper(uniqid());
}

/**
 * Limitar texto
 */
function limitarTexto($texto, $limite = 100, $complemento = '...') {
    if (strlen($texto) <= $limite) {
        return $texto;
    }

    return substr($texto, 0, $limite) . $complemento;
}

/**
 * Verificar se é data válida
 */
function dataValida($data, $formato = 'Y-m-d') {
    $d = DateTime::createFromFormat($formato, $data);
    return $d && $d->format($formato) === $data;
}

/**
 * Obter primeiro dia do mês
 */
function primeiroDiaMes($data = null) {
    if (!$data) $data = date('Y-m-d');
    return date('Y-m-01', strtotime($data));
}

/**
 * Obter último dia do mês
 */
function ultimoDiaMes($data = null) {
    if (!$data) $data = date('Y-m-d');
    return date('Y-m-t', strtotime($data));
}

/**
 * Remover acentos
 */
function removerAcentos($string) {
    $acentos = [
        'á' => 'a', 'à' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a',
        'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
        'í' => 'i', 'ì' => 'i', 'î' => 'i', 'ï' => 'i',
        'ó' => 'o', 'ò' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o',
        'ú' => 'u', 'ù' => 'u', 'û' => 'u', 'ü' => 'u',
        'ç' => 'c',
        'Á' => 'A', 'À' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A',
        'É' => 'E', 'È' => 'E', 'Ê' => 'E', 'Ë' => 'E',
        'Í' => 'I', 'Ì' => 'I', 'Î' => 'I', 'Ï' => 'I',
        'Ó' => 'O', 'Ò' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O',
        'Ú' => 'U', 'Ù' => 'U', 'Û' => 'U', 'Ü' => 'U',
        'Ç' => 'C'
    ];

    return strtr($string, $acentos);
}

/**
 * Slug para URL
 */
function gerarSlug($string) {
    $string = removerAcentos($string);
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9]+/', '-', $string);
    $string = trim($string, '-');
    return $string;
}
