<div class="navbar navbar-inverse navbar-fixed-top">
  <div class="navbar-inner">
    <div class="container">
      <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="brand" href="/siplogg/">Licencia uLogger</a>
      <div class="nav-collapse collapse" id="main-menu">              
        <ul class="nav">
          <li><a href="/siplogg/">Hem</a></li>
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Funktioner <b class="caret"></b></a>
            <ul class="dropdown-menu">
              <li><a href="/siplogg/siplogg.php">SIP-logg</a></li>
              <li class="divider"></li>
              <li class="nav-header">Övrigt</li>
              <li><a href="/siplogg/settings.php">Inställningar</a></li>
            </ul>
          </li>
          <li><a href="http://www.licencia.se/kontakt">Kontakt</a></li>          
        </ul>
        <div class="navbar-form pull-right">        
        <?php if ($logged_in): ?>
          <button class="btn btn-link logout-button" id="logout">Logga ut</button>
        <?php else: ?>
          <input class="span2" type="text" id="user" placeholder="Email">
          <input class="span2" type="password" id="password" placeholder="Password">
          <button class="btn login-button" id="login">Logga in</button>                          
        <?php endif; ?>          
        </div>
      </div>
    </div>
  </div>
</div>
