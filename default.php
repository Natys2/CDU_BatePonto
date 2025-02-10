
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../img/icon.png" type="image/x-icon">
    <link rel="stylesheet" href="login.css">
    
    <title>Login | Absoluta CDU</title>
</head>
<body>
    <div class="titulo">
        <img src="meta.png" alt="LOGO DA EMPRESA">
    </div>
    <div class="Login">
        <h1>Login</h1>
        <form action="login.php" method="POST">
            <input type="email" id="email" name="email" placeholder="Email" required>
            <br><br>
            <input type="password" id="senha" name="senha"  placeholder="Senha" required>
            <br><br>
            <button type="submit" class="button-send">Entrar</button>
        </form>
    </div>
</body>
</html>
