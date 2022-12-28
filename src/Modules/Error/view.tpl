<div class="card text-center border-danger">
	<div class="card-header">
		BŁĄD ŁADOWANIA STRONY
	</div>
	<div class="card-body">
		{$errorCode}
		<h5 class="card-title">Błąd {$errorCode}</h5>
		<p class="card-text">
		{if $errorCode == 404}
			Nie odnaleziono strony pod podanym adresie.
		{else if $errorCode == 101}
			Nie posiadasz odpowienich uprawnień do strony.
		{else} Nie znany błąd aplikacji
		{/if}
		</p>
		{if $debug && $info}
			<p class="card-text">{$info[0]}</p>
		{/if}
	</div>
</div>
