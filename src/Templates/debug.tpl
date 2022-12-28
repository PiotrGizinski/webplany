<div class="collapse" id="collapseDebug">
	<div class="card border-primary">
		<div class="row">
			<div class="col-2">
				<div class="list-group" id="list-tab" role="tablist">
					{foreach item=value key=key from=$debug}
						<a class="list-group-item list-group-item-action" id="list-home-{$key}" data-bs-toggle="list" href="#list-{$key}" role="tab" aria-controls="home">{$key}</a>
					{/foreach}
				</div>
			</div>
			<div class="col-8">
				<div class="tab-content" id="nav-tabContent">
					{foreach item=value key=key from=$debug}
						<div class="tab-pane fade show" id="list-{$key}" role="tabpanel" aria-labelledby="list-{$key}-list">
							{$value|@debug_print_var:[]:[80]}
						</div>
					{/foreach}
				</div>
			</div>
		</div>
	</div>
</div>
