<?php
$page_title = 'Movimentação de Estoque';
ob_start();
?>

<div class="page-header">
    <h2>Movimentação de Estoque</h2>
    <a href="?page=produtos&action=view&id=<?= $dados['id'] ?>" class="btn btn-secondary">← Voltar</a>
</div>

<div class="card">
    <div class="card-header">
        <h3><?= htmlspecialchars($dados['nome']) ?></h3>
    </div>
    <div class="card-body">
        <p>Estoque atual: <strong><?= $dados['estoque_atual'] ?> <?= htmlspecialchars($dados['unidade']) ?></strong></p>
        <p>Estoque mínimo: <strong><?= $dados['estoque_minimo'] ?> <?= htmlspecialchars($dados['unidade']) ?></strong></p>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>Registrar Movimentação</h3>
    </div>
    <div class="card-body">
        <form method="POST" class="form">
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="tipo">Tipo *</label>
                    <select id="tipo" name="tipo" class="form-control" required>
                        <option value="entrada">Entrada</option>
                        <option value="saida">Saída</option>
                        <option value="ajuste">Ajuste</option>
                    </select>
                </div>

                <div class="form-group col-md-4">
                    <label for="quantidade">Quantidade *</label>
                    <input type="number" id="quantidade" name="quantidade" class="form-control" required min="1" value="">
                </div>
            </div>

            <div class="form-group">
                <label for="motivo">Motivo *</label>
                <input type="text" id="motivo" name="motivo" class="form-control" required placeholder="Ex: Compra de fornecedor, venda avulsa, inventário...">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">💾 Registrar</button>
                <a href="?page=produtos&action=view&id=<?= $dados['id'] ?>" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../template.php';
?>
