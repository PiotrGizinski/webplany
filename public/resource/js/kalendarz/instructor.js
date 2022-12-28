var inctructor;

class Instructor {

     /**
     * Przechowuje terminy
     * @var {array}
     */
    terms;

    /**
     * Przechowuje zaznaczone dni
     * @var {array}
     */
    selectDays = [];

    constructor() {
        this.generateBasicAction();
        this.downloadTerms();
        $(document).ajaxStop(() => {
            this.generateAction();
        });
    }
    
    generateBasicAction() {
        $('.card .card-body #date').on('change',() => {
            this.downloadTerms();
        });
        $("#modalAddCongress").on("hidden.bs.modal", function() {
            $("#content #body .col:not('.holiday')").removeClass("clicked");
            $("#content #body .col #"+this.currentDay).toggleClass("currentDay");
        });
        $("#modalAddTerm").on("hidden.bs.modal", () => {
            this.clearTermForm();
        });
    }

    /**
     * Pobiera zjazdy
     */
    downloadTerms() {
        return $.ajax({
            type:'POST',
            dataType: "json",
            url: window.location.pathname,
            data:{ data: 'terms', date: $('.card .card-body #date').val() },
            success: (response) => {
                this.terms = response;
                this.putTerms();
            },
            error: () => {
                alert("Błąd pobierania danych zjazdów");
            }
        });
    }

    /**
     * Dodaje terminy do interfejsu
     */
    putTerms() {
        if (this.terms) {
            for (let term of this.terms) {
                var date = new Date(term['dataOd']);
                for (var i = 0; i <= term['liczbaDni']; i++) {
                    var element = "#content #body .row .col#" + date.toJSON().slice(0, 10);
                    if (term['dostepnosc']) {
                        $(element).append('<div class="alert term enable" role="alert">Dostępny</div>');
                    } else {
                        $(element).append('<div class="alert term disable" role="alert">Niedostępny</div>');
                    }
                    var html = '<p>' + term['wykladowca'];
                    if (term['godzinaOd'] && term['godzinaDo']) {
                        html += '</br>w godzinach: ' + term['godzinaOd'] + '-' + term['godzinaDo'];
                    } else {
                        html += '</br>cały dzień';
                    }
                    html += '</p>';
                    $(element + " .term").attr('data-toggle', 'tooltip');
                    $(element + " .term").attr('title', html);
                    date.setDate(date.getDate() + 1);
                }
                
            }
            $("[data-toggle='tooltip']").tooltip({'html' : true, 'placement': 'auto'});
        }
    }

    /**
     * Funkcja dodajaca obsługę dodawania zjazdów
     */
    generateAction() {
        $("#content #body .col:not('.holiday')").unbind('mousedown');
        $(document).unbind('mouseup');

        /* Event wciśniecia guzika myszy, rozpoczecie dodawania dni zjazdu */
        $("#content #body .col:not('.holiday')").on('mousedown', () => {
            if ($(event.currentTarget).attr('id') && !$("#context-menu").is(":visible")) {
                this.selectDay($(event.currentTarget));
            }
        });
        /* Event puszczenia guzika myszy, koniec zaznaczania dni zjazdu */
        $(document).on('mouseup', (e) => {
            if ($("#context-menu").is(":visible") && !$(".modal").is(":visible")) {
                if (!$(e.target).is("#context-menu button")) {
                    this.unselectDays();
                }
            } else
            if (!jQuery.isEmptyObject(this.selectDays) && !$(".modal").is(":visible")) {
                $("#context-menu").css({
                    display: "block",
                    top: e.pageY,
                    left: e.pageX
                }).addClass("show");
            }
            $("#content #body .col:not('.holiday')").unbind("mouseenter");

            //inicjowanie formularza do przeniesienia zajęć
            if ($(e.target).is("#context-menu #addTerm")) {
                console.log(this.selectDays);
                this.loadTermForm();
            }
        });
    }

    loadTermForm() {
        console.log(this.selectDays);
        var element = '#modalAddTerm #addTerm';
        $(element + ' #fromDate').val(this.selectDays[0]);
        $(element + ' #toDate').val(this.selectDays[this.selectDays.length-1]);
    }

    clearTermForm() {
        this.unselectDays();
    }

    /**
     * Metoda obsługująca zaznaczanie komórek dla zjazdu
     * @param {element} that
     */
    selectDay(that) {
        var id = $(that).attr("id");
        if (id) {
            if (id == this.currentDay) {
                $(that).removeClass("currentDay");
            }
            $(that).toggleClass("clicked");
            this.selectDays.push(id);
        }
        var num = parseInt($(that).attr('id').substring(8,10))+1;
        num = (num < 10) ? num = '0' + num : num;
        id = ($(that).attr('id').substring(0,8)+num).toString();
        $(that).unbind("mouseenter");
        $("#content #body .col:not('.holiday')#"+id).on("mouseenter", () => {
            this.selectDay(event.currentTarget);
        });
    }

    unselectDays() {
        $("#content #body .col").removeClass("clicked");
        $("#context-menu").hide();
        this.selectDays = [];
    }
}

$(document).ready(function(){
    instructor = new Instructor();
});