{*<script src="{JS}{str_replace("tpl","js",$smarty.template)}"></script>
<link rel="stylesheet" type="text/css" href="{STYLES}{str_replace("tpl","css",$smarty.template)}">
*}

<script src="{JS}kalendarz/generate.js"></script>

<div class="card">
    <div class="card-body">
		<input type="date" class="form-control" name="date" id="date" value="{$date}" hidden>
        <div id="content">
			<div class="container border border-secondary">
				<div class="row border-bottom border-secondary" id="head">
					<div class="col"><button class="btn back"><i class="fas fa-arrow-left"></i></button></div> 
					<div class="col col-5" id="monthYear"></div>
					<div class="col"><button class="btn forward"><i class="fas fa-arrow-right"></i></button></div> 
				</div>
				<div class="row border-bottom border-secondary" id="head">
					<div class="col-sm">Pn</div>
					<div class="col-sm">Wt</div>
					<div class="col-sm">Sr</div>
					<div class="col-sm">Cz</div>
					<div class="col-sm">Pi</div>
					<div class="col-sm">So</div>
					<div class="col-sm">Nd</div>
				</div>
				<div id="body"></div>
			</div>
        </div>
    </div>
</div>

{if 'wykładowca'|in_array:$access}
	<script src="{JS}kalendarz/instructor.js"></script>
	<div class="dropdown-menu dropdown-menu-sm" id="context-menu">
		<button id="addTerm" class="dropdown-item" data-toggle="modal" data-target="#modalAddTerm">Dodaj swój termin</button>
    </div>

	<!-- Okno przenoszenia zajec -->
	<div class="modal fade" id="modalAddTerm" tabindex="-1" role="dialog" aria-labelledby="modalAddTerm" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="modalAddTerm">Dodawanie terminu dostępności lub niedostępności</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<form action="{$smarty.server.REQUEST_URI}" id="addTerm" method="post">
				<div class="modal-body">
					<input type="hidden" id="data" name="data" value="putTerm">
					<div class="row">
						<legend class="col-form-label col col-4">Dostępność:</legend>
						<div class="form-check form-check-inline">
							<input class="form-check-input" type="radio" name="availability" id="availability1" value="false" checked>
							<label class="form-check-label" for="availability1">Niedostępny</label>
						</div>
						<div class="form-check form-check-inline">
							<input class="form-check-input" type="radio" name="availability" id="availability2" value="true">
							<label class="form-check-label" for="availability2">Dostępny</label>
						</div>
					</div>
  					<div class="form-group row">
						<label for="fromDate" class="col col-1 col-form-label">Od:</label>
						<div class="col col-5">
							<input type="date" class="form-control" name="fromDate" id="fromDate" readonly="readonly" required>
						</div>
						<label for="fromHour" class="col col-2 col-form-label">Godzina: (opcjonalne)</label>
						<div class="col col-4">
							<input type="time" class="form-control" name="fromHour" id="fromHour" min="{$hourRange['min']}" max="{$hourRange['max']}">
						</div>
					</div>
  					<div class="form-group row">
					  	<label for="toDate" class="col col-1 col-form-label">Do:</label>
						<div class="col col-5">
							<input type="date" class="form-control" name="toDate" id="toDate" readonly="readonly" required>
						</div>
						<label for="toHour" class="col col-2 col-form-label">Godzina: (opcjonalne)</label>
						<div class="col col-4">
							<input type="time" class="form-control" name="toHour" id="toHour" min="{$hourRange['min']}" max="{$hourRange['max']}">
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Anuluj</button>
					<button type="submit" class="btn btn-primary" id="confirm">Przenieś</button>
				</div>
				</form>
			</div>
		</div>
	</div>
{/if}