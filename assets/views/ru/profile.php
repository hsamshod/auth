<div class="row mt-3">
    <div class="col-12 col-sm-6 col-md-5 col-lg-3">
        <div class="card mb-3">
            <img src="/store/<?= app()->user()->photo ?: 'default.png' ?>" class="card-img-top" alt="<?= app()->user()->fio ?>">
            <div class="card-body text-center">
                <h4 class="card-title"><?= app()->user()->fio ?: app()->user()->username; ?></h4>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">Логин: <?= app()->user()->username; ?></li>
                <li class="list-group-item">День рождения: <?= (new DateTime(app()->user()->birthday))->format('d.m.Y'); ?></li>
                <li class="list-group-item">Пол: <?= app()->user()->sex === 'm' ? 'мужской' : 'женский'; ?></li>
            </ul>
            <div class="card-body">
                <form class="text-center" action="/index.php?action=logout" method="post">
                    <input type="hidden" name="form_token" value="<?= Request::getFormToken(); ?>">
                    <button class="btn btn-link card-link mx-auto" type="submit">Выйти</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-7 col-lg-9 text-center text-muted mb-3 pt-3">
        Добро пожаловать!
    </div>
</div>
