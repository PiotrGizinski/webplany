<script src="{JS}lokalizacja.js"></script>

<div id="content">
	<div class="accordion" id="accordionBulding">
		<div class="card text-center">
			{* Lista nazw wszystkich budynków *}
			<div class="card-header">
				<ul class="nav nav-tabs card-header-tabs">
					{foreach item=row from=$content}
					<li class="nav-item">
						<a class="nav-link {if $row@first}active{/if}" id="{$row['id']}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{$row['id']}" aria-expanded="false" aria-controls="collapse{$row['id']}">
							{$row['nazwa']}
						</a>
					</li>
					{/foreach}
					{if 'admin'|in_array:$access}
						<div class="col text-right">
							<li class="nav-item">
								<button type="button" class="btn btn-outline-success" id="addBulding" data-toggle="modal" data-target="#addBuldingModal">
									<i class="fas fa-plus-circle"></i>
									Dodaj
								</button>
							</li>
						</div>
					{/if}
				</ul>
			</div>
			{* END Lista nazw wszystkich budynków *}
			{* Rozwijane pole z zawartością danych budynków oraz listą sal *}
			{foreach item=row from=$content}
				<div class="collapse {if $row@first}show{/if}" id="collapse{$row['id']}" data-parent="#accordionBulding">
					<div class="card-body">
						<div class="alert alert-success" role="alert">
							{* Lista danych aktualnie wyświetlanego budynku *}
							<div class="row">
								<div class="col"><h6 class="card-title mb-2"><i>Adres:</i> {$row['Adres']}</h6></div>
								<div class="col"><h6 class="card-title mb-2">
								{if isset($row['Opis'])}
									<i>Opis:</i> {$row['Opis']}</h6>
								{/if}
								</div>
								{* Buttony do edycji i usunięcia aktualnie wyświetlanego budynku *}
								{if 'admin'|in_array:$access}
									<div class="col col-2 text-right">
										<div class="btn-group-vertical">
											<button type="button" class="btn btn-success editBulding" id="{$row['id']}">Edytuj</button>
											<button type="button" class="btn btn-danger deleteBulding" id="{$row['id']}">Usuń</button>
										</div>
									</div>
								{/if}
								{* END Buttony do edycji i usunięcia aktualnie wyświetlanego budynku *}
							</div>
							{* END Lista danych aktualnie wyświetlanego budynku *}
						</div>
						<div class="container border border-secondary">
							{* Nazwy kolumn dla danych z sali *}
							<div class="row border-bottom border-secondary" id="head">
								<div class="col">Numer</div>
								<div class="col">Typ</div>
								<div class="col">Ilość miejsc</div>
								<div class="col">Opis</div>
								<div class="col-3">Wyposażenie</div>
								{if 'admin'|in_array:$access}
								<div class="col text-right">
									<button class="btn btn-success addRoom" id="{$row['id']}">Dodaj sale</button>
								</div>
								{/if}
							</div>
							{* END Nazwy kolumn dla danych z sali *}
							{* Lista sal przypisanych do budynku *}
							<div id="body">
								{if isset($row['Sale'])}
									{foreach item=subRow from=$row['Sale']}
										<div class="row border" id="record-{$subRow['id']}">
										{foreach item=subSubRow key=subSubKey from=$subRow}
											{if $subSubKey != 'id'}
												{* Lista wyposażenia danej sali *}
												{if $subSubKey == 'Sprzet'}
													<div class="col col-3">
														<ul class="list-group list-group-flush">
														{foreach item=subSubSubRow key=subSubSubKey from=$subSubRow}
															<li class="list-group-item">{$subSubSubRow['Nazwa']}
																{* Zrobić w bazie danych ilosci sprzetu *}
																<span class="badge badge-primary badge-pill">
																	{if isset($subSubSubRow['Ilosc'])}
																		{$subSubSubRow['Ilosc']}
																	{else}
																		1
																	{/if}
																</span>
															</li>
														{/foreach}
														</ul>
												{* END Lista wyposażenia danej sali *}
												{else}
													<div class="col">
														{$subSubRow}
												{/if}
												</div>
											{/if}
										{/foreach}
										{* Buttony do edycji i usuwania sali  *}
                                        {if 'admin'|in_array:$access}
                                            <div class="col text-right">
                                                <div class="btn-group-vertical">
                                                    <button type="button" class="btn btn-success editRoom" id="{$subRow['id']}">Edytuj</button>
                                                    <button type="button" class="btn btn-danger deleteRoom" id="{$subRow['id']}">Usuń</button>
                                                </div>
                                            </div>
                                        {/if}
                                        {* END Buttony do edycji i usuwania sali  *}
										</div>
									{/foreach}
								{/if}
							</div>
							{* END Lista sal przypisanych do budynku *}
						</div>
					</div>
				</div>
			{/foreach}
			{* END Rozwijane pole z zawartością danych budynków oraz listą sal *}
		</div>
	</div>
</div>

{* Uprawnienia dla admina *}
{if 'admin'|in_array:$access}
	{* Okno dodawania nowego budynku *}
	<div class="modal fade" id="addBuldingModal" tabindex="-1" role="dialog" aria-labelledby="addBuldingModal" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="addBuldingModal">Dodawanie budynku</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<form action="{$smarty.server.REQUEST_URI}" method="post">
					<div class="modal-body">
						<div class="form-group">
							<label for="name">Nazwa budynku</label>
							<input type="text" class="form-control" name="name" id="name" required>
						</div>
						<div class="form-group">
							<label for="selectCity">Miasto</label>
							<select class="form-control" name="selectCity" id="selectCity" required>
							{foreach item=value from=$cities}
								<option value="{$value['id']}">{$value['nazwa']}</option>
							{/foreach}
							</select>
						</div>
						<div class="form-group">
							<label for="selectType">Ulica</label>
							<input type="text" class="form-control" name="street" id="street" required>
						</div>
						<div class="form-group">
							<label for="numberSeats">Numer</label>
							<input type="text" class="form-control" name="addressNumber" id="addressNumber" required>
						</div>
						<div class="form-group">
							<label for="description">Opis</label>
							<textarea class="form-control" name="description" id="description" rows="3" placeholder="Opcjonalny opis"></textarea>
						</div>
					</div>
					<div class="modal-footer">
						<input type="submit" name="add" class="btn btn-primary" value="Dodaj">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Anuluj</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	{* END Okno dodawania nowego budynku *}

	{* Okno edytowania budynku *}
	<div class="modal fade" id="editBuldingModal" tabindex="-1" role="dialog" aria-labelledby="editBuldingModal" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="editBuldingModal">Edycja budynku</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<form action="{$smarty.server.REQUEST_URI}" method="post">
					<div class="modal-body">
						<input class="form-control" name="bulding" id="bulding" value="" hidden>
						<div class="form-group">
							<label for="name">Nazwa budynku</label>
							<input type="text" class="form-control" name="name" id="name" required>
						</div>
						<div class="form-group">
							<label for="selectCity">Miasto</label>
							<select class="form-control" name="selectCity" id="selectCity" required>
							{foreach item=value from=$cities}
								<option value="{$value['id']}">{$value['nazwa']}</option>
							{/foreach}
							</select>
						</div>
						<div class="form-group">
							<label for="street">Ulica</label>
							<input type="text" class="form-control" name="street" id="street" required>
						</div>
						<div class="form-group">
							<label for="addressNumber">Numer</label>
							<input type="text" class="form-control" name="addressNumber" id="addressNumber" required>
						</div>
						<div class="form-group">
							<label for="description">Opis</label>
							<textarea class="form-control" name="description" id="description" rows="3" placeholder="Opcjonalny opis"></textarea>
						</div>
					</div>
					<div class="modal-footer">
						<input type="submit" name="add" class="btn btn-primary" value="Zatwierdź">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Anuluj</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	{* END Okno edytowania budynku *}

	{* Okno usuwania budynku *}
	<div class="modal fade" id="deleteBuldingModal" tabindex="-1" role="dialog" aria-labelledby="deleteBuldingModal" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="deleteBuldingModal">Usuwanie budynku</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<form action="{$smarty.server.REQUEST_URI}" method="post">
					<input class="form-control" name="bulding" id="bulding" value="" hidden>
					<div class="modal-footer">
						<input type="submit" class="btn btn-danger" value="Usuń">
						<button type="button" class="btn" data-dismiss="modal">Anuluj</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	{* END Okno usuwania budynku *}

	{* Okno dodawania nowej sali *}
	<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModal" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="addModal">Dodawanie sali</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<form action="{$smarty.server.REQUEST_URI}" method="post">
					<div class="modal-body">
						<input class="form-control" name="bulding" id="bulding" value="" hidden>
						<div class="form-group">
							<label for="number">Numer sali</label>
							<input class="form-control" name="number" id="number" required>
						</div>
						<div class="form-group">
							<label for="selectType">Typ sali</label>
							<select class="form-control" name="selectType" id="selectType" required>
							{foreach item=value from=$types}
								<option value="{$value['id']}">{$value['nazwa']}</option>
							{/foreach}
							</select>
						</div>
						<div class="form-group">
							<label for="numberSeats">Ilość miejsc</label>
							<input type="number" class="form-control" name="numberSeats" id="numberSeats" required>
						</div>
						<div class="form-group">
							<label for="description">Opis</label>
							<textarea class="form-control" name="description" id="description" rows="3" placeholder="Opcjonalny opis"></textarea>
						</div>
						<div class="form-group">
							<label for="selectEquipment">Sprzęt</label>
							<select class="form-control" name="selectEquipment[]" id="selectEquipment" multiple>
								{foreach item=value from=$equipment}
									<option value="{$value['id']}">{$value['nazwa']}</option>
								{/foreach}
							</select>
						</div>
					</div>
					<div class="modal-footer">
						<input type="submit" name="add" class="btn btn-primary" value="Dodaj">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Anuluj</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	{* END Okno dodawania nowej sali *}


	{* Okno edycji sali *}
	<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModal" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="editModal">Edycja sali</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<form action="{$smarty.server.REQUEST_URI}" method="post" name="">
					<div class="modal-body">
						<input class="form-control" name="room" id="room" value="" hidden>
						<div class="form-group">
							<label for="number">Numer sali</label>
							<input class="form-control" name="number" id="number" required>
						</div>
						<div class="form-group">
							<label for="selectType">Typ sali</label>
							<select class="form-control" name="selectType" id="selectType" required>
							{foreach item=value from=$types}
								<option value="{$value['id']}">{$value['nazwa']}</option>
							{/foreach}
							</select>
						</div>
						<div class="form-group">
							<label for="numberSeats">Ilość miejsc</label>
						<input type="number" class="form-control" name="numberSeats" id="numberSeats" required>
						</div>
						<div class="form-group">
							<label for="description">Opis</label>
							<textarea class="form-control" name="description" id="description" rows="3" placeholder="Opcjonalny opis"></textarea>
						</div>
						<div class="form-group">
							<label for="selectEquipment">Sprzęt</label>
							<select class="form-control" name="selectEquipment[]" id="selectEquipment" multiple>
								{foreach item=value from=$equipment}
									<option value="{$value['id']}">{$value['nazwa']}</option>
								{/foreach}
							</select>
						</div>
					</div>
					<div class="modal-footer">
						<input type="submit" class="btn btn-primary" value="Zatwierdź">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Anuluj</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	{* END Okno edycji sali *}

	{* Okno usuwania sali *}
	<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModal" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="deleteModal">Usuwanie sali</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<form action="{$smarty.server.REQUEST_URI}" method="post">
					<input class="form-control" name="room" id="room" value="" hidden>
					<div class="modal-footer">
						<input type="submit" class="btn btn-danger" value="Usuń">
						<button type="button" class="btn" data-dismiss="modal">Anuluj</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	{* END Okno usuwania sali *}
{/if}