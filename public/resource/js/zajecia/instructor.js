var instructor;

class Instructor {

    /**
     * Przechowuje wybrane zajęcia do przeniesienia
     * @var {string}
     */
    selectActivitie = null;

    constructor() { 
        $(document).ajaxStop(() => {
            this.generateAction()
        });
    }

    /**
     * Generuje akcję dla wykładowcy
     */
    generateAction() {
        $("#content .card-body #body .row .col").unbind("mousedown");
        $(document).unbind("mouseup");
        $(".modal").unbind("hidden.bs.modal");
        $('#modalTransferActivitie #selectBuilding').unbind("change");
        $("#modalTransferActivitie #confirm").unbind("click");
        
        //klik
        $("#content .card-body #body .row .col").on("mousedown", (e) => {
            //if ($(event.currentTarget + ' .instructor')) {
                //console.log('Handler od rozpoczecia zaznaczania godzin');
                if ($("#context-menu").is(":visible")) {
                    this.unselectDay();
                }
                this.selectDay($(event.currentTarget));
            //}
        });
        //unklik
        $(document).on("mouseup", (e) => {
            if ($("#context-menu").is(":visible") && !$(".modal").is(":visible")) {
                if (!$(e.target).is("#context-menu button")) {
                    //console.log('Handler od chowania context-menu');
                    this.unselectDay();
                }
            } else
            //Pokazanie context-menu
            if (!jQuery.isEmptyObject(this.selectActivitie) && !$(".modal").is(":visible")) {
                $("#context-menu").css({
                    display: "block",
                    top: e.pageY,
                    left: e.pageX
                }).addClass("show");
            }
            //inicjowanie formularza do przeniesienia zajęć
            if ($(e.target).is("#context-menu #transferActivitie")) {
                console.log(this.selectActivitie);
                this.downloadActivitie();
                this.downloadRooms($('#modalTransferActivitie #selectBuilding option:selected').attr('value'));
            }
        });

        //Wyowałanie aktualizacji sal po zmianie budynku w formularzu przenoszenia zajęć
        $('#modalTransferActivitie #selectBuilding').on('change', () => {
            this.downloadRooms($('#modalTransferActivitie #selectBuilding option:selected').attr('value'));
        });

        //Inicjowanie czyszczenia formularza po zamknięciu okna przenoszenia zajęć
        $("#modalTransferActivitie").on("hidden.bs.modal", () => {
            this.clearTranformForm();
        });
    }

    /* ------------------- ZAZNACZANIE DNI ------------------- */

    /**
     * Funkcja służąca do zaznaczania dni
     * @param {Object} that
     */
    selectDay(that) {
        if ($(that).children().attr('id') && $(that).children().hasClass('instructor') && !$(that).children().hasClass('transfer')) {
            $(that).addClass("clicked");
            this.selectActivitie = ($(that).children().attr('id'));
        }
    }

    /**
     * Funkcja odpowiadająca za odznaczanie dni oraz chowanie context-menu
     */
    unselectDay() {
        $("#content .card-body #body .row .col").removeClass("clicked");
        $("#context-menu").hide();
        this.selectActivitie = null;
    }

    /* ------------------- END ZAZNACZANIE DNI ------------------- */


    /* ------------------- FORMULARZ DO PRZENIESIENIA ZAJĘĆ ------------------- */
    
    /**
     * Pobiera dane wybranych zajęć
     */
    downloadActivitie() {
        $.ajax({
            type: 'POST',
            dataType: "json",
            url: window.location.pathname,
            data: { data: 'selectActivitie', idActivitie: this.selectActivitie },
            success: (response) => {
                this.loadTransferForm(response);
            },
            error: () => {
                alert("Błąd pobierania danych sal");
            }
        });
    }

    /**
     * Pobiera sale dla wybranego budynku w formularzu
     * @param {int} building 
     */
    downloadRooms(building) {
        $.ajax({
            type: 'POST',
            dataType: "json",
            url: window.location.pathname,
            data: { data: 'rooms', idBuilding: building },
            success: (response) => {
                this.putRoomsInForm(response);
            },
            error: () => {
                alert("Błąd pobierania danych sal");
            }
        });
    }

    /**
     * Umieszcza sale w formularzu
     * @param {array} rooms 
     */
    putRoomsInForm(rooms) {
        var html;
        for (let room of rooms) {
            html += "<option value='" + room['id'] + "'>" + room['numer'] + "</option>";
        }
        $('#modalTransferActivitie #selectRoom').html(html);
    }

    /**
     * Umieszcza dane w formularzu
     * @param {array} activitie 
     */
    loadTransferForm(activitie) {
        $('#modalTransferActivitie #idActivitie').val(activitie['id']);
        $('#modalTransferActivitie #fromDate').val(activitie['data']);
        $('#modalTransferActivitie #selectDate').val($("#content .card-header .nav-item #selectDay").val());
        $('#modalTransferActivitie #fromRoom').val(activitie['sala']);
        $('#modalTransferActivitie #fromBuilding').val($("#content .card-header .nav-item .nav-link.active").text().toString());
        $('#modalTransferActivitie #fromHour').val(activitie['godzina']);
        $('#modalTransferActivitie #selectGroup').val(activitie['grupy']);
    }

    /**
     * Czyści formularz z danych
     */
    clearTranformForm() {
        $('#modalTransferActivitie #fromDate').val('');
        $('#modalTransferActivitie #fromRoom').val('');
        $('#modalTransferActivitie #fromBuilding').val('');
        $('#modalTransferActivitie #fromHour').val('');
        $('#modalTransferActivitie #selectGroup').val('');
        this.unselectDay();
    }

    /* ------------------- END FORMULARZ DO PRZENIESIENIA ZAJĘĆ ------------------- */
}

$(document).ready(function(){
    instructor = new Instructor();
});

