<div class="form-box" id="login-box">
    <div class="header bg-primary">Sign In</div>
    {{ form('id': 'form_login') }}
        <div class="body bg-gray">
            {{ content() }}
            <div class="form-group">
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                    {{ form.render('email') }}
                </div>
            </div>
            <div class="form-group">
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                    {{ form.render('password') }}
                </div>
            </div>
            <div class="form-group">
                {{ form.render('remember') }} {{ form.label('remember') }}
            </div>
            {{ form.render('csrf', ['value': security.getToken()]) }}
        </div>
        <div class="footer">
            <button type="submit" class="btn bg-green btn-block" data-text-loading="Login..." onclick="user.login(this,'#form_login');return false;">Login</button>
        </div>
    </form>
</div>