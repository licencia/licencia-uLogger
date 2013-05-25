<div class="navbar navbar-inverse navbar-fixed-top">
  <div class="navbar-inner">
    <div class="container">
      <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="brand" href="/">Licencia uLogger</a>
      <div class="nav-collapse collapse" id="main-menu">
        <ul class="nav">
          <li><a href="/"><i class="icon-home icon-white"></i> Hem</a></li>
          <?php if ($logged_in): ?>
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-th-large icon-white"></i> Funktioner <b class="caret"></b></a>
            <ul class="dropdown-menu">
              <li><a href="/siplogg.php">SIP-logg</a></li>
              <li class="divider"></li>
              <li class="nav-header">Övrigt</li>
              <li><a href="/settings.php">Inställningar</a></li>
              <li><a href="/hardware.php">Systemstatus</a></li>
            </ul>
          </li>
          <?php endif; ?>
          <li><a href="http://www.licencia.se/ulogger"><i class="icon-question-sign icon-white"></i> Hjälp</a></li>
          <li><a href="http://www.licencia.se/kontakt"><i class="icon-envelope icon-white"></i> Kontakt</a></li>
        </ul>
        <div class="navbar-form pull-right">
        <?php if ($logged_in): ?>
          <button class="btn btn-link logout-button" id="logout"><i class="icon-user icon-white"></i> Logga ut</button>
        <?php else: ?>
          <div class="form-inline">
            <input class="input-small" type="text" id="user" placeholder="Användare">
            <input class="input-small" type="password" id="password" placeholder="Lösenord">            
            <label class="checkbox">
            <?php if (isset($_COOKIE['remember_me'])) {
              $checked = 'checked="checked"';
              }
              else {
                $checked =  '';
              }
            ?>          
            <input type="checkbox" value="remember-me" id="remember_me" <?php echo $checked; ?>> Kom ihåg mig
            </label>
            <button class="btn login-button btn-success btn-small" id="login">Logga in</button>
          </div>
        <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>
