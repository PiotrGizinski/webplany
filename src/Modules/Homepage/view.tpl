<!-- Header -->
<div class="card mb-3">
	<header class="masthead">
    <div class="container d-flex h-100 align-items-center">
      <div class="mx-auto text-center">
        <h1 class="mx-auto my-0 text-uppercase"></h1>
        <h2 class="text-black-50 mx-auto mt-2 mb-5"></h2>
      </div>
    </div>
  </header>
</div>
<div class="card mb-3">
	<div class="card-body text-center">
		{if isset($role)}
			Jesteś zalogowany z uprawnieniami:<b><p>
			{foreach from=$role item=$record}
				{$record}<br>
			{/foreach}
		{else}
			<b><p>Aby otrzymać dostęp do pozostałych stron musisz się zalogować.
		{/if}
		</p></b>
	</div>
</div>
<div class="card-group">
  <div class="card">
    <img class="card-img-top" src="">
    <div class="card-body">
      <h5 class="card-title">Wpis 3</h5>
      <p class="card-text">This is a wider card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
    </div>
    <div class="card-footer">
      <small class="text-muted">Last updated 3 mins ago</small>
    </div>
  </div>
  <div class="card">
    <img class="card-img-top" src="">
    <div class="card-body">
      <h5 class="card-title">Wpis 2</h5>
      <p class="card-text">This card has supporting text below as a natural lead-in to additional content.</p>
    </div>
    <div class="card-footer">
      <small class="text-muted">Last updated 3 mins ago</small>
    </div>
  </div>
  <div class="card">
    <img class="card-img-top" src="">
    <div class="card-body">
      <h5 class="card-title">Wpis 1</h5>
      <p class="card-text">This is a wider card with supporting text below as a natural lead-in to additional content. This card has even longer content than the first to show that equal height action.</p>
    </div>
    <div class="card-footer">
      <small class="text-muted">Last updated 3 mins ago</small>
    </div>
  </div>
</div>
