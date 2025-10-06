<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Criar Cobrança</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 720px; margin: 40px auto; }
        form { display: flex; flex-direction: column; gap: 10px; }
        input, select, textarea { padding: 8px; }
        button { padding: 10px; }
        a { text-decoration: none; }
        .error { color: #b00; }
    </style>
    <script>
        function confirmCustomerCreationIfNeeded() {
            var hasCustomer = <?= isset($client) && !empty($client['asaas_customer_id']) ? 'true' : 'false' ?>;
            if (!hasCustomer) {
                return confirm('Não existe cliente Asaas para este cliente. Ao confirmar, um cliente será criado na Asaas para gerar a cobrança. Deseja continuar?');
            }
            return true;
        }
    </script>
</head>
<body>
    <h2>Criar Cobrança</h2>

    <?php if (isset($_GET['error'])): ?>
        <p class="error">
            <?php if ($_GET['error'] === '1'): ?>
                Dados obrigatórios ausentes (cliente, valor, vencimento).
            <?php elseif ($_GET['error'] === '2'): ?>
                Para criar o cliente na Asaas é necessário informar CPF/CNPJ no cadastro do cliente.
            <?php elseif ($_GET['error'] === '3'): ?>
                Erro ao criar cliente na Asaas.
            <?php endif; ?>
        </p>
    <?php endif; ?>

    <form method="post" action="/payments/store" onsubmit="return confirmCustomerCreationIfNeeded()">
        <input type="hidden" name="client_id" value="<?= htmlspecialchars((string)($client['id'] ?? (string)($_GET['client_id'] ?? ''))) ?>" />

        <label>
            Forma de pagamento
            <select name="billing_type" required>
                <option value="PIX">Pix</option>
                <option value="BOLETO">Boleto</option>
                <option value="CREDIT_CARD">Cartão de Crédito</option>
            </select>
        </label>

        <input type="number" step="0.01" name="value" placeholder="Valor (R$)" required />
        <input type="date" name="due_date" placeholder="Data de vencimento" required />
        <textarea name="description" placeholder="Descrição (opcional)"></textarea>

        <div>
            <button type="submit">Criar cobrança</button>
            <a href="/dashboard">Cancelar</a>
        </div>
    </form>
</body>
</html>


