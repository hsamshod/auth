<?php /* @var Response $this */ ?>
<form action="/index.php?action=register" method="post" enctype="multipart/form-data"
      id="register-form-content" class="form-content col-12 px-0 mb-4 <?= $active_tab !== 'register' ? 'd-none' : ''; ?>"
>
    <input type="hidden" name="form_token" value="<?= Request::getFormToken(); ?>">
    <div class="form-group">
        <label class="mb-1 font-weight-bold" for="register-login">Логин</label>
        <input type="text" name="register[username]" value="<?= Request::postInput('register', 'username') ?: ''; ?>"
               id="register-login" class="form-control form-control-sm <?= $this->e('register-username') ? 'is-invalid' : ''; ?>" placeholder="Логин"
        >
        <div id="register-login-empty" class="invalid-feedback <?= $this->e('register-username', 'empty') ? '' : 'd-none'; ?>">
            <?= Validator::msg('empty'); ?>
        </div>
        <div id="register-login-unique" class="invalid-feedback <?= $this->e('register-username', 'unique') ? '' : 'd-none'; ?>">
            <?= Validator::msg('unique'); ?>
        </div>
        <div id="register-login-alphanum" class="invalid-feedback <?= $this->e('register-username', 'alphanum') ? '' : 'd-none'; ?>">
            <?= Validator::msg('alphanum'); ?>
        </div>
    </div>
    <div class="form-group">
        <label class="mb-1 font-weight-bold" for="register-password">Придумайте пароль</label>
        <input type="password" name="register[password]"
               id="register-password" class="form-control form-control-sm" placeholder="Пароль"
        >
        <div id="register-password-empty" class="invalid-feedback <?= $this->e('register-password', 'empty') ? '' : 'd-none'; ?>">
            <?= Validator::msg('empty'); ?>
        </div>
        <div id="register-password-length" class="invalid-feedback <?= $this->e('register-password', 'length') ? '' : 'd-none'; ?>">
            <?= Validator::msg('length'); ?>
        </div>
    </div>
    <div class="form-group">
        <label class="mb-1 font-weight-bold" for="register-password-repeat">Повторите пароль</label>
        <input type="password" name="register[password_repeat]"
               id="register-password-repeat" class="form-control form-control-sm" placeholder="Повторите пароль"
        >
        <div id="register-password-same" class="invalid-feedback <?= $this->e('register-password', 'same') ? '' : 'd-none'; ?>">
            <?= Validator::msg('same'); ?>
        </div>
    </div>

    <div class="form-group mb-0">
        <label class="mb-1 font-weight-bold" for="sex-label">Укажите пол</label>
        <div id="sex-label">
            <div class="custom-control custom-radio custom-control-inline">
                <input type="radio" name="register[sex]" value="m" <?= Request::postInput('register', 'sex') != 'f' ? 'checked' : ''; ?>
                       id="sex-male" class="custom-control-input" required
                >
                <label class="custom-control-label" for="sex-male">Мужской</label>
            </div>
            <div class="custom-control custom-radio custom-control-inline mb-3">
                <input type="radio" name="register[sex]" value="f" <?= Request::postInput('register', 'sex') === 'f' ? 'checked' : ''; ?>
                       id="sex-female" class="custom-control-input" required
                >
                <label class="custom-control-label" for="sex-female">Женский</label>
            </div>
        </div>
    </div>

    <div class="mb-3">
        <label class="mb-1 font-weight-bold" for="sex-label">Укажите дату рождения</label>
        <div class="row">
            <div class="col-4 form-group mb-0">
                <label for="bday-day">День</label>
                <select name="register[day]"
                        id="bday-day" class="form-control form-control-sm"
                >
                    <?php for($i = 1; $i <= 31; $i++) : ?>
                        <option <?= Request::postInput('register', 'day') == $i ? 'selected' : ''; ?> id="day-<?= $i; ?>" class="calendar-day"><?= $i; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-4 form-group mb-0 px-0">
                <label for="bday-month">Месяц</label>
                <select name="register[month]"
                        id="bday-month" class="form-control form-control-sm"
                >
                    <?php for($i = 1; $i <= 12; $i++) : ?>
                        <option <?= Request::postInput('register', 'month') == $i ? 'selected' : ''; ?>><?= $i; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-4 form-group mb-0">
                <label for="bday-year">Год</label>
                <select name="register[year]"
                        id="bday-year" class="form-control form-control-sm"
                >
                    <?php for($i = intval(date('Y')) - 16; $i >= 1940; $i--) : ?>
                        <option <?= Request::postInput('register', 'year') == $i ? 'selected' : ''; ?>><?= $i; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div id="register-date" class="col-12 invalid-feedback <?= $this->e('register-date', 'date') ? '' : 'd-none'; ?>">
                <?= Validator::msg('date'); ?>
            </div>
        </div>
    </div>

    <div class="mb-3">
        <div class="w-100">
            <label class="mb-1 font-weight-bold" for="sex-label">Загрузите фото<sup class="text-danger font-weight-bold">*</sup></label>
        </div>
        <div class="input-group input-group-sm custom-file">
            <input type="file" name="register[photo]"
                   id="user-photo" class="custom-file-input form-control-sm"
            >
            <label class="custom-file-label form-control-sm" for="user-photo">Выберите фото</label>
            <div id="image-format" class="invalid-feedback <?= $this->e('register-photo', 'image') ? '' : 'd-none'; ?>">
                <?= Validator::msg('image'); ?>
            </div>
        </div>
        <div class="valid-feedback d-inline-block text-muted">
            <sup class="text-danger font-weight-bold">*</sup> - необязательные поля
        </div>
    </div>

    <div class="row">
        <div class="col-6 text-right">
            <button id="register-submit" type="submit" class="btn btn-sm btn-primary px-3">Зарегистрироваться</button>
        </div>
    </div>
</form>
