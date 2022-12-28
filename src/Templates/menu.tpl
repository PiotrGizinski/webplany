<!-- MENU -->
<nav class="navbar navbar-expand-lg fixed-top navbar-dark bg-dark" id="main_navbar">
  <div class="container">
    <img src="{IMG}favicon.png" alt="Icon" height="35" width="35">
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
      <ul class="navbar-nav mr-auto">
        {* Dynamic Menu *}
        {foreach item=$record key=$key from=$menu}
          {if $record|is_array}
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle text-light" href="#" id="{$key}" data-bs-toggle="dropdown">
                {$key}
              </a>
              <ul class="dropdown-menu" aria-labelledby="{$key}">
                {foreach item=$subRecord key=$subKey from=$record}
                  {if $subRecord|is_array}
                    <li class="nav-item dropdown">
                      <a class="nav-link dropdown-toggle" href="#" id="{$subKey}" data-bs-toggle="dropdown">
                        {$subKey}
                      </a>
                      <ul class="dropdown-menu" aria-labelledby="{$subKey}">
                        {foreach item=$subSubRecord key=$subSubKey from=$subRecord}
                          <li><a class="dropdown-item" href="{$subSubRecord}">{$subSubKey}</a></li>
                        {/foreach}
                      </ul>
                    </li>
                  {else}
                    <li><a class="dropdown-item" href="{$subRecord}">{$subKey}</a></li>
                  {/if}
                {/foreach}
              </ul>
            </li>
          {else}
            <li class="nav-item">
              <a class="nav-link text-light" href="{$record}">{$key}</a>
            </li>
          {/if}
        {/foreach}
        {* END Dynamic menu *}
      </ul>
      {*<button class="btn text-light">Zalogowany jako:??</button>*}
      {if $debug}
        <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDebug">
          Debug
        </button>
      {/if}
      <button id="fullscreen" class="btn btn-warning"><i class="fas fa-arrows-alt"></i></button>
    </div>
  </div>
</nav>
