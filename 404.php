<!DOCTYPE html>
<html lang="=ru">
<head>
    <title>404 Not Found</title>
    <style>
        body, html {
            margin: 0;
            height: 100%;
            padding: 0;
        }

        body {
            background: #362b36;
            color: #ffef8f;
            font-size: 42px;
        }

        body {
            display: table;
            height: 100%;
            width: 100%;
        }

        h1 {
            display: table-cell;
            text-align: center;
            vertical-align: middle;
        }

        p {
            font-size: 23px;
        }

        .error {
            font-size: 23px;
        }
    </style>
</head>
<body>
<h1>
    404 Not Found<br>
    <p>Документ <?= $_SERVER['REQUEST_URI'] ?> не найден на сервере</p>
    <p><a style="color: #ffef8f" href="/">вернуться на главную</a></p>
    <?php if (\common\Application::$debugMode == \common\types\DebugMode::true): ?>
        <div class="error">
            <?= $e->getMessage() ?>
            <?= $e->getLine() ?>
            <?= $e->getFile() ?>
        </div>
    <?php endif ?>
</h1>
</body>
</html>