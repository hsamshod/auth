<!DOCTYPE html>
<html>
<head>
    <title>User Auth</title>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="/assets/css/bs.css">
    <link rel="stylesheet" type="text/css" href="/assets/css/style.css">
</head>
<body>
<noscript><div class="text-center alert alert-danger mb-0" role="alert">Пожалуйста, включите Javascript в браузере</div></noscript>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="#">AuthBoard</a>
        <ul id="locales" class="nav justify-content-end">
            <?php foreach (app()::LOCALES as $locale) : ?>
                <li class="ml-auto nav-item">
                    <a class="nav-link text-white px-1 <?= app()->getLocale() === $locale ? 'active' : ''; ?>"
                       href="/?action=locale&lang=<?= $locale; ?>"
                    >
                        <?= ucfirst($locale); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</nav>
<div class="container">
    <?= $content; ?>
</div>
<script src="/assets/js/jq.js"></script>
<script src="/assets/js/script.js"></script>
</body>
</html>