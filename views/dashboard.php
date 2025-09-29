<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard - Mini ERP/CRM</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1200px; margin: 20px auto; padding: 20px; }
        .header { border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 30px; }
        .actions { margin-bottom: 30px; }
        .actions a { 
            background: #007cba; color: white; padding: 10px 20px; 
            text-decoration: none; margin-right: 10px; border-radius: 4px; 
        }
        .actions a:hover { background: #005a87; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .status-pending { color: #ff9800; font-weight: bold; }
        .status-completed { color: #4caf50; font-weight: bold; }
        .empty { text-align: center; color: #666; font-style: italic; padding: 20px; }
        .logout { float: right; }
        .logout a { color: #d32f2f; text-decoration: none; }
        .logout a:hover { text-decoration: underline; }
        .delete-btn { 
            background: #d32f2f; color: white; padding: 5px 10px; 
            text-decoration: none; border-radius: 3px; font-size: 12px;
        }
        .delete-btn:hover { background: #b71c1c; }
        .edit-btn { 
            background: #ff9800; color: white; padding: 5px 10px; 
            text-decoration: none; border-radius: 3px; font-size: 12px; margin-right: 5px;
        }
        .edit-btn:hover { background: #f57c00; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Mini ERP/CRM — Dashboard</h1>
        <p>Olá, <?= htmlspecialchars($_SESSION['user_name'] ?? 'usuário', ENT_QUOTES, 'UTF-8') ?>!</p>
        <div class="logout">
            <a href="/logout">Sair</a>
        </div>
    </div>

    <?php if (isset($_GET['deleted'])): ?>
        <div class="success">
            <?php if ($_GET['deleted'] === 'client'): ?>
                Cliente excluído com sucesso!
            <?php elseif ($_GET['deleted'] === 'order'): ?>
                Pedido excluído com sucesso!
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['updated'])): ?>
        <div class="success">
            <?php if ($_GET['updated'] === 'client'): ?>
                Cliente atualizado com sucesso!
            <?php elseif ($_GET['updated'] === 'order'): ?>
                Pedido atualizado com sucesso!
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="error">
            <?php if ($_GET['error'] === '1'): ?>
                ID inválido.
            <?php elseif ($_GET['error'] === '2'): ?>
                Erro ao processar operação.
            <?php elseif ($_GET['error'] === '3'): ?>
                Registro não encontrado.
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="actions">
        <a href="/clients/create">+ Novo Cliente</a>
        <a href="/orders/create">+ Novo Pedido</a>
    </div>

    <h2>Clientes Cadastrados</h2>
    <?php if (empty($clients)): ?>
        <div class="empty">Nenhum cliente cadastrado ainda.</div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Telefone</th>
                    <th>Data de Cadastro</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clients as $client): ?>
                <tr>
                    <td><?= htmlspecialchars((string)$client['id']) ?></td>
                    <td><?= htmlspecialchars($client['name']) ?></td>
                    <td><?= htmlspecialchars($client['email'] ?: '-') ?></td>
                    <td><?= htmlspecialchars($client['phone'] ?: '-') ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($client['created_at'])) ?></td>
                    <td>
                        <a href="/clients/edit?id=<?= $client['id'] ?>" class="edit-btn">Editar</a>
                        <a href="/clients/delete?id=<?= $client['id'] ?>" 
                           class="delete-btn" 
                           onclick="return confirm('Tem certeza que deseja excluir este cliente?')">
                            Excluir
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <h2>Pedidos</h2>
    <?php if (empty($orders)): ?>
        <div class="empty">Nenhum pedido cadastrado ainda.</div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Valor Total</th>
                    <th>Status</th>
                    <th>Data de Criação</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?= htmlspecialchars((string)$order['id']) ?></td>
                    <td><?= htmlspecialchars($order['client_name'] ?: 'Cliente #' . $order['client_id']) ?></td>
                    <td>R$ <?= number_format((float)$order['total_amount'], 2, ',', '.') ?></td>
                    <td class="status-<?= $order['status'] ?>"><?= htmlspecialchars($order['status']) ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                    <td>
                        <a href="/orders/edit?id=<?= $order['id'] ?>" class="edit-btn">Editar</a>
                        <a href="/orders/delete?id=<?= $order['id'] ?>" 
                           class="delete-btn" 
                           onclick="return confirm('Tem certeza que deseja excluir este pedido?')">
                            Excluir
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>

