<!doctype html>
<html lang="pl">
  <head>
    <meta charset="utf-8">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/jpg" href="{IMG}favicon.png">
    <title>{$title}</title>

    <!-- https://daneden.github.io/animate.css/  Animacje CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.0/animate.min.css">

    <!-- Font Awsome -->
    <link rel="stylesheet" href="{FONTAWESOME}css/all.css">

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="{BOOTSTRAP}css/bootstrap.min.css">

    <!-- jQuery -->
    <script src="{JQUERY}jquery.min.js"></script>

    <!-- Custom styles -->
    {foreach item=value from=$cssFilesName}
      <link href="{STYLES}{$value}.css" rel="stylesheet">
    {/foreach}

    <!-- Custom script -->
    <script src="{JS}main.js"></script>
  </head>
  <body>
    <!-- Preloader element adres: www.icons8.com/preloaders -->
    <div class="preloader-wrapper">
      <div class="preloader">
        <img src="{IMG}preloader.gif" alt="preloader">
      </div>
    </div>
    <header>
      {include file="./menu.tpl"}
    </header>
    <section>
      <div class="container">
        {if $debug}
          <article>
            {include file="./debug.tpl"}
          </article>
        {/if}
        {if $exception}
          <article>
            <div class="card mb-2">
              {foreach item=value from=$exception}
                <div class="alert mb-0 alert-danger text-center border-light">
                  {*<a href="" class="close" data-dismiss="alert" aria-label="close">&times;</a>*}
                  {$value}
                </div>
              {/foreach}
            </div>
          </article>
        {/if}
        {*Here include file with view of Model *}
        {if $view}
          <article>
            {include file=$view}
          </article>
        {/if}
      </div>
    </section>
    <aside>

    </aside>
    <footer>
			{if $resultForm}
				<div class="alert-bottom alert alert-info">
					<a href="" class="close" data-dismiss="alert" aria-label="close">&times;</a>
					<center>{$resultForm}.</center>
				</div>
			{/if}
			<div class="alert-bottom alert alert-info">
				<a href="" class="close" data-dismiss="alert" aria-label="close">&times;</a>
				<strong>Info: </strong>Strona wygenerowana w {$timeGenerate} sekund.<br>
			</div>
    </footer>
		<!-- Bootstrap bundle JS -->
		<script src="{BOOTSTRAP}js/bootstrap.bundle.min.js"></script>
	</body>
</html>