<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Editar Cliente</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 720px; margin: 40px auto; }
        form { display: flex; flex-direction: column; gap: 10px; }
        input { padding: 8px; }
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
    <h2>Editar Cliente</h2>
    
    <?php if (isset($_GET['error'])): ?>
        <p class="error">
            <?php if ($_GET['error'] === '1'): ?>
                Dados inválidos. Nome é obrigatório.
            <?php elseif ($_GET['error'] === '2'): ?>
                Erro ao atualizar cliente.
            <?php endif; ?>
        </p>
    <?php endif; ?>

    <form method="post" action="/clients/update">
        <input type="hidden" name="id" value="<?= htmlspecialchars((string)$client['id']) ?>" />
        
        <input type="text" name="name" placeholder="Nome" value="<?= htmlspecialchars($client['name']) ?>" required />
        <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($client['email'] ?: '') ?>" />
        <input type="text" name="phone" placeholder="Telefone" value="<?= htmlspecialchars($client['phone'] ?: '') ?>" />
        
        <div class="actions">
            <button type="submit" class="btn-primary">Salvar</button>
            <a href="/dashboard" class="btn-secondary">Cancelar</a>
        </div>
    </form>
</body>
</html>

