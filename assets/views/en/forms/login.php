<?php /* @var Response $this */ ?>
<form action="/index.php?action=login" method="post" enctype="multipart/form-data"
      id="login-form-content" class="form-content col-12 px-0 <?= $active_tab !== 'login' ? 'd-none' : ''; ?>"
>
    <input type="hidden" name="form_token" value="<?= Request::getFormToken(); ?>">
    <div class="form-group">
        <label class="mb-1 font-weight-bold" for="login-username">Login</label>
        <input type="text" name="login[username]" value="<?= Request::postInput('login', 'username') ?: ''; ?>"
               id="login-username" class="form-control form-control-sm <?= $this->e('login-username') ? 'is-invalid' : ''; ?>" placeholder="Login"
        >
        <div id="login-username-empty" class="invalid-feedback <?= $this->e('login-username', 'empty') ? '' : 'd-none'; ?>">
            <?= Validator::msg('empty'); ?>
        </div>
        <div id="login-username-login" class="invalid-feedback <?= $this->e('login-username', 'login') ? '' : 'd-none'; ?>">
            <?= Validator::msg('login'); ?>
        </div>
        <div id="login-username-blocked" class="invalid-feedback <?= $this->e('login-username', 'blocked') ? '' : 'd-none'; ?>">
            <?= Validator::msg('blocked'); ?>
        </div>
    </div>
    <div class="form-group">
        <label class="mb-1 font-weight-bold" for="login-password">Password</label>
        <input type="password" name="login[password]"
               id="login-password" class="form-control form-control-sm <?= $this->e('login-username') ? 'is-invalid' : ''; ?>" placeholder="Password"
        >
        <div id="login-password-empty" class="invalid-feedback <?= $this->e('login-password', 'empty') ? '' : 'd-none'; ?>">
            <?= Validator::msg('empty'); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <button id="login-submit" type="submit" class="btn btn-sm btn-primary px-3">Sign in</button>
        </div>
    </div>
</form>