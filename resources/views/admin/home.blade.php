<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Fitrain Admin</title>
</head>
<body>
    <h1>Админ панель Fitrain</h1>
    <p>Это админ панель (panel.fitrain.local)</p>
    <p>Subdomain: {{ request()->attributes->get('subdomain', 'unknown') }}</p>
    <p>Host: {{ request()->getHost() }}</p>
</body>
</html>
