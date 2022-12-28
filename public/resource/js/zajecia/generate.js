var activities;
    
class Activities {

    /**
     * Przechowuje wybraną datę
     * @var {Date}
     */
    date;

    /**
     * Przechowuje id biężącego budynku
     * @var {int}
     */
    building;

    /**
     * Przechowuje id i numery sal dla bieżącego budynku
     * @var {array}
     */
    rooms;

    /**
     * Przechowuje zajęcia dla aktualnego dnia i wybranego budynku
     * @var {array}
     */
    activities;

    /**
     * Przechowuje propozycje przeniesienia zajęć
     * @var {array}
     */
    transfers;

    constructor() {
        this.generateBasicAction();
        //var date = new Date("May 4, 2019 03:24:00").toISOString().substring(0, 10);
        //$("#content .card-header .nav-item #selectDay").val(date);
        this.setDate();
        this.setBuilding();
        this.downloadRoomsActivities();
    }

    /**
     * Pobiera datę z interfejsu
     */
    setDate() {
        this.date = $("#content .card-header .nav-item #selectDay").val();
    }

    /**
     * Pobiera id aktualnie wybranego budynku z interfejsu
     */
    setBuilding() {
        this.building = $("#content .card-header .nav-item .nav-link.active").attr('id');
    }

    /**
     * Funkcja generująca akcję dla sekcji head (zmiana daty i budynku)
     */
    generateBasicAction() {
        $("#content .card-header .nav-item .nav-link").on('click', () => {
            if (!$(event.currentTarget).hasClass('active')) {
                $("#content .card-header .nav-link").removeClass('active');
                $(event.currentTarget).addClass('active');
            }
            this.setBuilding();
            this.downloadRoomsActivities();
        });
        $("#content .card-header .nav-item #selectDay").on('change', () => {
            this.setDate();
            this.downloadActivities();
        });
    }

    /**
     * Funkcja pobierająca dane do interfejsu
     */
    downloadRoomsActivities() {
        $.ajax({
            type: 'POST',
            dataType: "json",
            url: window.location.pathname,
            data: { data: 'rooms', idBuilding: this.building, date: this.date },
            beforeSend: () => {
                this.removeActivities();
                this.removeTransfers();
            },
            success: (response) => {
                this.rooms = response['rooms'];
                this.activities = response['activities'];
                this.transfers = response['transfers'];
                //console.log('Zaciagam sale: [' + this.rooms + '] i zajęcia: [' + this.activities + ']');
                this.putRooms();
                this.putActivities();
                this.putTransfers();
            },
            error: () => {
                alert("Błąd pobierania danych sal");
            }
        });
    }

    /**
     * Umieszcza sale w interfejsie
     */
    putRooms() {
        if (this.rooms) {
            var html = "<div class='col-md-auto border-right border-secondary text-white'>00:00 - 00:00</div>";
            for (let room of this.rooms) {
                html += "<div class='col' id='" + room['id'] + "'>" + room['numer'] + "</div>";
            }
            $("#content .card-body #head").html(html);
            $("#content .card-body #body .row .col:not('.hour')").remove();
            for (let hour of $("#content .card-body #body .row")) {
                for (let room of this.rooms) { 
                    $(hour).append('<div id="' + hour['id'] + "-" + room['id'] + '" class="col border animated fadeIn"></div>');
                }
            }
        }
    }

    /**
     * Funkcja pobierająca zajęcia i przeniesienia dla danego budynku i daty
     */
    downloadActivities() {
        $.ajax({
            type: 'POST',
            dataType: "json",
            url: window.location.pathname,
            data: { data: 'activities', idBuilding: this.building, date : this.date },
            success: (response) => {
                this.removeActivities();
                this.removeTransfers();
                this.activities = response['activities'];
                this.transfers = response['transfers'];
                this.putActivities();
                this.putTransfers();
                //console.log('Zaciagam zajęcia: ' + response);
            },
            error: () => {
                alert("Błąd pobierania zajęć");
            }
        });
    }

    /**
     * Umiesza zajęcia w interfejsie
     */
    putActivities() {
        if (this.activities) {
            for (let activitie of this.activities) {
                var element = "#content .card-body #body .row .col#" + activitie['idGodziny'] + "-" + activitie['idSala'];
                if (activitie['instructor']) {
                    $(element).append('<div class="alert activitie instructor" id="' + activitie['id'] + '">' + activitie['grupy'] + '</div>');
                } else {
                    $(element).append('<div class="alert activitie" id="' + activitie['id'] + '">' + activitie['grupy'] + '</div>');
                }
                $(element + " .activitie").attr('data-toggle', 'tooltip');

                var html = '<p>'+activitie['wykladowca'] + '</br>' + activitie['przedmiot'] + '</br>' + '</p>';
                $(element + " .activitie").attr('title', html);
            }
            $("[data-toggle='tooltip']").tooltip({'html' : true, 'placement': 'auto'});
        }
    }

    /**
     * Usuwa z interfejsu zajęcia
     */
    removeActivities() {
        if (this.activities) {
            for (let activitie of this.activities) {
                var element = "#content .card-body #body .row .col#" + activitie['idGodziny'] + "-" + activitie['idSala'];
                $(element).empty();
            }
        }
    }

    /**
     * Umiesza propozycje przeniesienia zajęć w interfejsie
     */
    putTransfers() {
        console.log(this.transfers);
        if (this.transfers) {
            for (let transfer of this.transfers) {
                var element = "#content .card-body #body .row .col#" + transfer['idGodzina'] + "-" + transfer['idSala'];
                if (transfer['instructor']) {
                    $(element).append('<div class="alert transfer instructor" id="' + transfer['id'] + '">' + transfer['grupy'] + '</div>');
                } else {
                    $(element).append('<div class="alert transfer" id="' + transfer['id'] + '">' + transfer['grupy'] + '</div>');
                }
                $(element + " .transfer").attr('data-toggle', 'tooltip');

                var html = '<p>'+transfer['wykladowca'] + '</br>' + transfer['przedmiot'] + '</br>Zajęcia z:</br>' + 
                    transfer['zData'] + '</br>' + transfer['zGodzina'] + '</br>' + transfer['zBudynek'] + '</br>Sala - ' + transfer['zSala'] + '</p>';
                $(element + " .transfer").attr('title', html);
            }
            $("[data-toggle='tooltip']").tooltip({'html' : true, 'placement': 'auto'});
        }
    }

    /**
     * Usuwa z interfejsu propozycje przeniesień
     */
    removeTransfers() {
        if (this.transfers) {
            for (let transfer of this.transfers) {
                var element = "#content .card-body #body .row .col#" + transfer['idGodzina'] + "-" + transfer['idSala'];
                $(element).empty();
            }
        }
    }
}

$(document).ready(() => {
    activities = new Activities();
});  
