<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Novo Cliente</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 720px; margin: 40px auto; }
        form { display: flex; flex-direction: column; gap: 10px; }
        input { padding: 8px; }
        button { padding: 10px; }
        a { text-decoration: none; }
    </style>
    </head>
<body>
    <h2>Novo Cliente</h2>
    <form method="post" action="/clients/store">
        <input type="text" name="name" placeholder="Nome" required />
        <input type="email" name="email" placeholder="Email" />
        <input type="text" name="phone" placeholder="Telefone" />
        <div>
            <button type="submit">Salvar</button>
            <a href="/dashboard">Cancelar</a>
        </div>
    </form>
</body>
</html>


