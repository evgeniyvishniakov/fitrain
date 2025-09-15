<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Fitrain CRM</title>
</head>
<body>
    <h1>CRM Система Fitrain</h1>
    <p>Это CRM система (crm.fitrain.local)</p>
    <p>Subdomain: {{ request()->attributes->get('subdomain', 'unknown') }}</p>
    <p>Host: {{ request()->getHost() }}</p>
</body>
</html>
