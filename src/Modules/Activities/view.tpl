<script src="{JS}zajecia/generate.js"></script>

</div>
<div id="content" class="container-fluid">
	<div class="card">
		<div class="card-header">
			<ul class="nav nav-tabs card-header-tabs">
				<li class="nav-item">
					<input type="date" class="form-control" id="selectDay" name="selectDay" value="{$date}">
				</li>
				{foreach item=row from=$buildings}
				<li class="nav-item">
					<a class="nav-link {if $row@first}active{/if}" id="{$row['id']}">{$row['nazwa']}</a>
				</li>
				{/foreach}
				<li class="nav-item">
					
				</li>
			</ul>
		</div>
		<div class="card-body">
			<div class="container-fluid" id="table">
				<div class="row border-bottom border-secondary" id="head">

				</div>
				<div id="body">
					{foreach from=$hours item=hour}
					<div class="row text-center" id="{$hour['id']}">
						<div class="col-md-auto hour">
							{$hour['nazwa']}
						</div>
					</div>
					{/foreach}
				</div>
			</div>
		</div>
		<div class="card-footer">
			<ul class="list-group list-group-horizontal" id="legend">
				<li class="list-group-item">LEGENDA:</li>
				<li class="list-group-item activitie">Zajęcia</li>
				<li class="list-group-item transfer">Propozycje przeniesienia zajęć</li>
				{if 'wykładowca'|in_array:$access}
					<li class="list-group-item instructor">Twoje zajęcia</li>
					<li class="list-group-item transfer instructor">Twoje propozycje przeniesienia zajęć</li>
				{/if}
			</ul>
		</div>
	</div>
</div>

{if 'wykładowca'|in_array:$access}
	<script src="{JS}zajecia/instructor.js"></script>
	<div class="dropdown-menu dropdown-menu-sm" id="context-menu">
		<button id="transferActivitie" class="dropdown-item" data-toggle="modal" data-target="#modalTransferActivitie">Przenieś zajęcia</button>
    </div>

	<!-- Okno przenoszenia zajec -->
    <div class="modal fade" id="modalTransferActivitie" tabindex="-1" role="dialog" aria-labelledby="modalTransferActivitie" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
                    <h5 class="modal-title" id="modalTransferActivitie">Przenoszenie zajęć</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
                <form action="{$smarty.server.REQUEST_URI}" id="transferActivitie" method="post">
				<div class="modal-body">
					<input type="hidden" id="data" name="data" value="transferActivitie">
					<input type="hidden" id="idActivitie" name="idActivitie" value="">
  					<div class="form-group row">
						<label for="fromDate" class="col-sm-2 col-form-label">Data:</label>
						<div class="col-sm-4">
							<input type="input" class="form-control" name="fromDate" id="fromDate" value="" required disabled>
						</div>
						<label for="selectDate" class="col-sm-1 col-form-label"><i class="fas fa-arrow-right"></i></label>
				        <div class="col-sm-5">
                            <input type="date" class="form-control" name="selectDate" id="selectDate" value="" required>
						</div>
					</div>
  					<div class="form-group row">
                        <label for="fromBuilding" class="col-sm-2 col-form-label">Budynek:</label>						
						<div class="col-sm-4">
                            <input type="text" class="form-control" name="fromBuilding" id="fromBuilding" value="" required disabled>
						</div>
                        <label for="selectBuilding" class="col-sm-1 col-form-label"><i class="fas fa-arrow-right"></i></label>
						<div class="col-sm-5">
							<select class="form-control" name="selectBuilding" id="selectBuilding" required>
								{foreach from=$buildings item=bulding}
									<option value="{$bulding['id']}">
										{$bulding['nazwa']}
									</option>
								{/foreach}
							</select>
						</div>
					</div>
					<div class="form-group row">
					  	<label for="fromRoom" class="col-sm-2 col-form-label">Sala:</label>
						<div class="col-sm-4">
							<input type="text" class="form-control" name="fromRoom" id="fromRoom" value="" required disabled>
						</div>
						<label for="selectRoom" class="col-sm-1 col-form-label"><i class="fas fa-arrow-right"></i></label>
						<div class="col-sm-5">
							<select class="form-control" name="selectRoom" id="selectRoom" required>
								
							</select>
						</div>
					</div>
  					<div class="form-group row">
						<label for="fromHour" class="col-sm-2 col-form-label">Godzina:</label>
						<div class="col-sm-4">
							<input type="text" class="form-control" name="fromHour" id="fromHour" value="" required disabled>
						</div>
						<label for="selectHour" class="col-sm-1 col-form-label"><i class="fas fa-arrow-right"></i></label>
						<div class="col-sm-5">
							<select class="form-control" name="selectHour" id="selectHour" required>
								{foreach from=$hours item=hour}
									<option value="{$hour['id']}">
										{$hour['nazwa']}
									</option>								
								{/foreach}
							</select>
						</div>
					</div>
  					<div class="form-group row">
						<label for="selectGroup" class="col-sm-2 col-form-label">Grupa zajęciowa:</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" name="selectGroup" id="selectGroup" value="" required disabled>
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

<div class="container">
