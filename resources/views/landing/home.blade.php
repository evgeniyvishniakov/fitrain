<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Fitrain - Landing</title>
</head>
<body>
    <h1>Добро пожаловать на Fitrain!</h1>
    <p>Это лендинг страница (fitrain.local)</p>
    <p>Subdomain: {{ request()->attributes->get('subdomain', 'unknown') }}</p>
    <p>Host: {{ request()->getHost() }}</p>
</body>
</html>
