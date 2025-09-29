<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Novo Pedido</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 720px; margin: 40px auto; }
        form { display: flex; flex-direction: column; gap: 10px; }
        input { padding: 8px; }
        button { padding: 10px; }
        a { text-decoration: none; }
    </style>
    </head>
<body>
    <h2>Novo Pedido</h2>
    <form method="post" action="/orders/store">
        <input type="number" name="client_id" placeholder="ID do Cliente" required />
        <input type="number" step="0.01" name="total_amount" placeholder="Valor Total" required />
        <div>
            <button type="submit">Salvar</button>
            <a href="/dashboard">Cancelar</a>
        </div>
    </form>
</body>
</html>


