$(document).ready(function () {
    
    var position = window.location.href.lastIndexOf("=");
    var data = window.location.href.slice(position + 1);
    var quebra = data.split("-");
    
    $(".calendario-agenda").datepicker({
        showOtherMonths: true,
        selectOtherMonths: true,
        dateFormat: 'yy-mm-dd', // Já manda a data no formato US para o backend
        dayNames: ['Domingo', 'Segunda', 'Terça', 'Quarta',
            'Quinta', 'Sexta', 'Sábado', 'Domingo'],
        dayNamesMin: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'],
        dayNamesShort: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'],
        monthNames: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
            'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
        monthNamesShort: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun',
            'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
        defaultDate: new Date(quebra[0],quebra[1] - 1,quebra[2]),
        onSelect: function (dateText, inst) {
            $('#agenda-hidden input[name="data"]').val(dateText);
            $('#agenda-hidden').submit();
        },
        beforeShowDay: function (d) {
            for (dia in eventosAgendaPorData) {
                var dateDia = eventosAgendaPorData[dia].periodoInicial.date.substring(0, 10).split('-');
                var dataEvento = new Date(dateDia[0], (dateDia[1] - 1), dateDia[2], 0, 0, 0, 0);
                var mesmoAno = (d.getFullYear() === dataEvento.getFullYear());
                var mesmoMes = (d.getMonth() === dataEvento.getMonth());
                var mesmoDia = (d.getDate() === dataEvento.getDate());

                if (mesmoAno && mesmoMes && mesmoDia) {
                    return [true, "selected", ""];
                }
            }

            return [false, "", ""];
        }
    });
});