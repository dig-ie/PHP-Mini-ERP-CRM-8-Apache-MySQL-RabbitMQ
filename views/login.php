<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 520px; margin: 40px auto; }
        form { display: flex; flex-direction: column; gap: 10px; }
        input { padding: 8px; }
        button { padding: 10px; }
        .error { color: #b00; }
    </style>
    </head>
<body>
    <h2>Login</h2>
    <?php if (!empty($_GET['error'])): ?>
        <p class="error">Credenciais inválidas.</p>
    <?php endif; ?>
    <form method="post" action="/login">
        <input type="email" name="email" placeholder="Email" required />
        <input type="password" name="password" placeholder="Senha" required />
        <button type="submit">Entrar</button>
    </form>
</body>
</html>


