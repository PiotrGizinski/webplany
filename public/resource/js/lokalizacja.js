$(document).ready(function(){

    $("#content .card-header .nav-link").on('click', () => {
        if ($(event.currentTarget).hasClass('active')) {
            $(event.currentTarget).removeClass('active');
        } else {
            $(event.currentTarget).addClass('active');
        }
    });

    /*------ Dodawanie, edycja i usuwanie budynku ------*/
    $(".editBulding").on('click',() => {
        var id = $(event.currentTarget).attr('id');
        $("#editBuldingModal #bulding").val(id)
        $.ajax({
            type:'POST',
            dataType: "json",
            url: window.location.pathname,
            data:{ idBulding: id },
            success: (response) => {
                $("#editBuldingModal #name").val(response['nazwa']);
                $("#editBuldingModal #selectCity").val(response['idMiasto']);
                $("#editBuldingModal #street").val(response['ulica']);
                $("#editBuldingModal #addressNumber").val(response['numer']);
                $("#editBuldingModal #description").val(response['opis']);
                $("#editBuldingModal").modal('show');
            },
            error: () => {
                alert("Błąd pobierania danych sali");
            }
        });
    });

    $(".deleteBulding").on('click',() => {
        $("#deleteBuldingModal #bulding").val($(event.currentTarget).attr('id'));
        $("#deleteBuldingModal").modal('show');
    });
    /*------ END Dodawanie, edycja i usuwanie budynki ------*/

    /*------ Dodawanie, edycja i usuwanie sali ------*/
    $(".addRoom").on('click',() => {
        $("#addModal #bulding").val($(event.currentTarget).attr('id'));
        $("#addModal").modal('show');
    });

    $(".editRoom").on('click',() => {
        var id = $(event.currentTarget).attr('id');
        $("#editModal #room").val(id)
        $.ajax({
            type:'POST',
            dataType: "json",
            url: window.location.pathname,
            data:{ idRoom: id },
            success: (response) => {
                $("#editModal #number").val(response['numer']);
                $("#editModal #selectType").val(response['idTyp']);
                $("#editModal #numberSeats").val(response['miejsca']);
                $("#editModal #description").val(response['opis']);
                $.each(response['sprzet'], (index, value) => {
                    console.log(value['idSprzet']);
                    $("#editModal #selectEquipment option[value="+value['idSprzet']+"]").attr('selected','selected');
                })
                $("#editModal").modal('show');
            },
            error: () => {
                alert("Błąd pobierania danych sali");
            }
        });
    });

    $("#editModal").on("hidden.bs.modal", function () {
        $("#editModal #selectEquipment option:selected").removeAttr("selected");

    });

    $(".deleteRoom").on('click',() => {
        $("#deleteModal #room").val($(event.currentTarget).attr('id'));
        $("#deleteModal").modal('show');
    });
    /*------ END Dodawanie, edycja i usuwanie sali ------*/
});