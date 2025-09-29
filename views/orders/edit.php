<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Editar Pedido</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 720px; margin: 40px auto; }
        form { display: flex; flex-direction: column; gap: 10px; }
        input, select { padding: 8px; }
        button { padding: 10px; }
        a { text-decoration: none; }
        .error { color: #b00; }
        .actions { display: flex; gap: 10px; }
        .btn-primary { background: #007cba; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        .btn-secondary { background: #6c757d; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn-primary:hover { background: #005a87; }
        .btn-secondary:hover { background: #545b62; }
    </style>
</head>
<body>
    <h2>Editar Pedido</h2>
    
    <?php if (isset($_GET['error'])): ?>
        <p class="error">
            <?php if ($_GET['error'] === '1'): ?>
                Dados inválidos. Cliente e valor são obrigatórios.
            <?php elseif ($_GET['error'] === '2'): ?>
                Erro ao atualizar pedido.
            <?php endif; ?>
        </p>
    <?php endif; ?>

    <form method="post" action="/orders/update">
        <input type="hidden" name="id" value="<?= htmlspecialchars((string)$order['id']) ?>" />
        
        <select name="client_id" required>
            <option value="">Selecione um cliente</option>
            <?php foreach ($clients as $client): ?>
                <option value="<?= $client['id'] ?>" 
                        <?= $client['id'] == $order['client_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($client['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <input type="number" step="0.01" name="total_amount" placeholder="Valor Total" 
               value="<?= htmlspecialchars($order['total_amount']) ?>" required />
        
        <select name="status">
            <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Pendente</option>
            <option value="processing" <?= $order['status'] === 'processing' ? 'selected' : '' ?>>Processando</option>
            <option value="completed" <?= $order['status'] === 'completed' ? 'selected' : '' ?>>Concluído</option>
            <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelado</option>
        </select>
        
        <div class="actions">
            <button type="submit" class="btn-primary">Salvar</button>
            <a href="/dashboard" class="btn-secondary">Cancelar</a>
        </div>
    </form>
</body>
</html>

