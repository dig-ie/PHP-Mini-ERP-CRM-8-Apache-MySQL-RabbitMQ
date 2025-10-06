<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Cobranças - <?= htmlspecialchars($client['name']) ?></title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1200px; margin: 20px auto; padding: 20px; }
        .header { border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 30px; }
        .actions { margin-bottom: 30px; }
        .actions a { 
            background: #007cba; color: white; padding: 10px 20px; 
            text-decoration: none; margin-right: 10px; border-radius: 4px; 
        }
        .actions a:hover { background: #005a87; }
        .back-btn { 
            background: #6c757d; color: white; padding: 10px 20px; 
            text-decoration: none; margin-right: 10px; border-radius: 4px; 
        }
        .back-btn:hover { background: #5a6268; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .status-pending { color: #ff9800; font-weight: bold; }
        .status-completed { color: #4caf50; font-weight: bold; }
        .status-cancelled { color: #f44336; font-weight: bold; }
        .empty { text-align: center; color: #666; font-style: italic; padding: 20px; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Cobranças - <?= htmlspecialchars($client['name']) ?></h1>
        <p>Cliente: <?= htmlspecialchars($client['name']) ?> | Email: <?= htmlspecialchars($client['email'] ?: 'Não informado') ?></p>
    </div>

    <?php if (isset($_GET['error'])): ?>
        <div class="error">
            <?php if ($_GET['error'] === '1'): ?>
                ID inválido.
            <?php elseif ($_GET['error'] === '3'): ?>
                Cliente não encontrado.
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="actions">
        <a href="/dashboard" class="back-btn">← Voltar ao Dashboard</a>
        <a href="/payments/create?client_id=<?= $client['id'] ?>">+ Nova Cobrança</a>
    </div>

    <h2>Cobranças Geradas</h2>
    <?php if (empty($payments)): ?>
        <div class="empty">Nenhuma cobrança gerada para este cliente ainda.</div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tipo de Cobrança</th>
                    <th>Valor</th>
                    <th>Vencimento</th>
                    <th>Status</th>
                    <th>Descrição</th>
                    <th>Data de Criação</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($payments as $payment): ?>
                <tr>
                    <td><?= htmlspecialchars((string)$payment['id']) ?></td>
                    <td><?= htmlspecialchars($payment['billing_type']) ?></td>
                    <td>R$ <?= number_format((float)$payment['value'], 2, ',', '.') ?></td>
                    <td><?= date('d/m/Y', strtotime($payment['due_date'])) ?></td>
                    <td class="status-<?= strtolower($payment['status']) ?>"><?= htmlspecialchars($payment['status']) ?></td>
                    <td><?= htmlspecialchars($payment['description'] ?: '-') ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($payment['created_at'])) ?></td>
                    <td>
                        <?php if (!empty($payment['invoice_url'])): ?>
                            <a href="<?= htmlspecialchars($payment['invoice_url']) ?>" target="_blank" class="edit-btn">Ver Fatura</a>
                        <?php endif; ?>
                        <?php if (!empty($payment['bank_slip_url'])): ?>
                            <a href="<?= htmlspecialchars($payment['bank_slip_url']) ?>" target="_blank" class="edit-btn">Ver Boleto</a>
                        <?php endif; ?>
                        <?php if (!empty($payment['pix_qr_code'])): ?>
                            <a href="<?= htmlspecialchars($payment['pix_qr_code']) ?>" target="_blank" class="edit-btn">Ver PIX</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
