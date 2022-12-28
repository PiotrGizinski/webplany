<!-- Header -->
<script>
    $(document).ready(function(){
        $(".card-header .nav-link").on('click', () => {
            if ($(event.currentTarget).hasClass('active')) {
                $(event.currentTarget).removeClass('active');
            } else {
                $(event.currentTarget).removeClass('active');
                $(event.currentTarget).addClass('active');
            }
        });
    });
</script>

<div class="card">
    <div class="accordion" id="accordion">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs">
                {foreach item=value key=key from=$content}
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="collapse" data-bs-target="#collapse-{$key}" aria-expanded="true" aria-controls="collapse-{$key}">
                            {$key}
                        </a>
                    </li>
                {/foreach}
            </ul>
        </div>
        <div class="accordion" id="subAccordion">
            {foreach item=value key=key from=$content}
                <div class="collapse" id="collapse-{$key}" data-parent="#accordion">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-2">
                                <div class="list-group" id="list-tab" role="tablist">
                                {foreach item=subValue key=subKey from=$value}
                                    <a class="list-group-item list-group-item-action nav-link" data-bs-toggle="collapse" data-bs-target="#collapse-{$key}-{$subKey}" aria-expanded="true" aria-controls="collapse-{$key}-{$subKey}">{$subKey}</a>
                                {/foreach}
                                </div>
                            </div>
                            <div class="col-8 border">
                                {foreach item=subValue key=subKey from=$value}
                                    <div class="collapse" id="collapse-{$key}-{$subKey}" data-parent="#subAccordion">
                                        {$subValue|@debug_print_var:[]:[80]}
                                    </div>
                                {/foreach}
                            </div>
                        </div>
                    </div>
                </div>
            {/foreach}
        </div>
    </div>
</div>
