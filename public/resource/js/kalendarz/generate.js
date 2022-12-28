var calendar;

class Generate {

    /**
     * Przechowuje bieżącą datę
     * @var {Date}
     */
    currentDate;

    /**
     * Przechowuje datę
     * @var {Date}
     */
    date;

    /**
     * Przechowuje zjazdy
     * @var {array}
     */
    congresses;

    constructor() {
        
        this.date = new Date($('.card .card-body #date').val());
        this.currentDate = new Date().toJSON().slice(0, 10);
        this.generateBasicAction();
        this.generateCalendar();
        this.downloadCongresses();
        this.downloadHolidays();
        
    }
    
    /* -------------------------- GENEROWANIE KALENDARZA -------------------------- */
    /**
     * Funkcja generująca zawartość kalendarza
     */
    generateCalendar() {
        var firstDays = this.getFirstDays();
        var body = "<div class='row'>";    
        var j = this.days + firstDays;
        for (var i = 1; i < j; i++) {
            var d = i - firstDays + 1;
            if (i < firstDays) {
                body += "<div class='col border'></div>";
            } else {
                var date = new Date(this.year, this.month, d+1);
                var id = date.toJSON().slice(0, 10);
                if (this.currentDate === id) {
                    body += "<div class='col border currentDay'";
                } else {
                    body += "<div class='col border'";
                }
                body += "id='" + id + "'>" + d + "</div>";
            }
            if ((i % 7) == 0){
                body += "</div><div class='row'>";
            }
        }
        if (((j - 1) % 7) != 0) {
            for(var i = (j - 1) % 7; i < 7; i++) {
                body += "<div class='col border'></div>";
            }
        }
        body += "</div>";
        this.setContent(body);
    }
    
    /**
     * Ładuje wygenerowaną zawartość kalendarza do widoku i wywołuje przypisywanie eventów
     * @param {string} body 
     */
    setContent(body) {
        $('#content #monthYear').text(this.nameMonth + " " + this.year);
        $('#content #body').hide().html(body).show();
    }

    /**
     * Zwraca datę z pierwszym dniem wybranego miesiąca
     */
    getFirstDays() {
        var firstDays = new Date(this.date.getFullYear(), this.date.getMonth(), 1).getDay();
        if (firstDays == 0) firstDays = 7;
        return firstDays;
    }

    get nameMonth() {
        var months = ["Styczeń", "Luty", "Marzec", "Kwiecień", "Maj", "Czerwiec", "Lipiec", "Sierpień", "Wrzesień", "Październik", "Listopad", "Grudzień"];
        return months[this.month];
    }

    get year() {
        return this.date.getFullYear();
    }

    get month() {
        return this.date.getMonth();
    }

    get days() {
        return new Date(this.year, this.month + 1, 0).getDate();
    }

    
    /* ------------------------ END GENEROWANIE KALENDARZA ------------------------ */

    /**
     * Funkcja generująca podstawowe akcję (zmiana miesiąca)
     */
    generateBasicAction() {
        $('#content button.back').click(() => {
            this.date.setMonth(this.date.getMonth() - 1);
            $('.card .card-body #date').val(this.date.toISOString().substr(0, 10));
            calendar.generateCalendar();
            if (this.month == 11) {
                this.downloadHolidays();
            } else {
                this.putHolidays();
            }
            this.downloadCongresses();
            $('.card .card-body #date').trigger('change');
        });
    
        $('#content button.forward').click(() => {
            this.date.setMonth(this.date.getMonth() + 1);
            $('.card .card-body #date').val(this.date.toISOString().substr(0, 10));
            calendar.generateCalendar();
            if (this.month == 0) {
                this.downloadHolidays();
            } else {
                this.putHolidays();
            }
            this.downloadCongresses();
            $('.card .card-body #date').trigger('change');
        });
    }

    /* ------------------- ŚWIĘTA ------------------- */
    /**
     * Pobiera święta
     */
    downloadHolidays() {
        $.ajax({
            type:'POST',
            dataType: "json",
            url: window.location.pathname,
            data:{ data: 'holidays', year: this.year },
            success: (response) => {
                this.holidays = response;
                this.putHolidays();
            },
            error: () => {
                alert("Błąd pobierania danych świąt");
            }
        });
    }

    /**
     * Umieszcza święta w interfejsie
     */
    putHolidays() {
        if (this.holidays) {
            for (let holiday of this.holidays) {
                var element = "#content #body .row .col#" + holiday['data'];
                $(element).addClass('holiday');
                $(element).append('<div class="alert holiday" role="alert">' + holiday['nazwa'] + '</div>');
            }
        }
    }
    /* ------------------- END ŚWIĘTA ------------------- */

    /* ------------------- ZJAZDY ------------------- */
    /**
     * Pobiera zjazdy
     */
    downloadCongresses() {
        return $.ajax({
            type:'POST',
            dataType: "json",
            url: window.location.pathname,
            data:{ data: 'congresses', date: this.date.toJSON().slice(0, 10) },
            success: (response) => {
                this.congresses = response;
                this.putCongresses();
            },
            error: () => {
                alert("Błąd pobierania danych zjazdów");
            }
        });
    }

    /**
     * Dodaje zjazdy do interfejsu
     */
    putCongresses() {
        if (this.congresses) {
            for (let congress of this.congresses) {
                var date = new Date(congress['dataRoz']);
                for (var i = 0; i <= congress['liczbaDni']; i++) {
                    var element = "#content #body .row .col#" + date.toJSON().slice(0, 10);
                    $(element).append('<div class="alert congress" role="alert">Zjazd</div>');
                    $(element + " .congress").attr('data-toggle', 'tooltip');
                    var html = '<p>dla wydziału</br>'+congress['wydzial'] + '</br>';
                    $(element + " .congress").attr('title', html);
                    date.setDate(date.getDate() + 1);
                }
            }
            $("[data-toggle='tooltip']").tooltip({'html' : true, 'placement': 'auto'});
        }
    }
    /* ------------------- END ZJAZDY ------------------- */
}

$(document).ready(function(){
    calendar = new Generate();
    
    $('#content').css('-webkit-user-select','none');
    $('#content').css('-moz-user-select','none');
    $('#content').css('-ms-user-select','none');
    $('#content').css('-o-user-select','none');
    $('#content').css('user-select','none');
    
});