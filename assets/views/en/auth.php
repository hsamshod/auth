<div id="auth-page" class="row">
    <div id="welcome-message" class="col-sm-6 col-md-5 offset-lg-1 text-center mt-100px">
        <h2 class="">AuthBoard</h2>
        <h4 class="">Please, sign in or sign up</h4>
    </div>
    <div id="forms" class="col-sm-6 col-lg-3 col-md-4 offset-md-1 offset-lg-2 px-5 pt-3 px-sm-0">
        <div class="row text-center">
            <div id="login-form"
                 class="auth-form-tab col-6 p-2 button-like border-bottom border-bottom-bold border-dark
                 <?= $active_tab === 'login' ? ' active bg-dark text-light' : ''; ?>"
            >Sign in</div>
            <div id="register-form"
                 class="auth-form-tab col-6 p-2 button-like border-bottom border-bottom-bold border-dark
                 <?= $active_tab === 'register' ? ' active bg-dark text-light' : ''; ?>"
            >Sign up</div>
        </div>
        <div class="row pt-3">
            <?php /* @var Response $this */ ?>
            <?= $this->renderPartial('login', 'forms'); ?>
            <?= $this->renderPartial('register', 'forms'); ?>
        </div>
    </div>
</div>
