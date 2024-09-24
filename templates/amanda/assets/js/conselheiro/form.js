jQuery(document).ready(function() {

    jQuery('input:checkbox, input:radio, input:file, select').uniform();

    // prepara validações de campos
    jQuery("#frm").validate({
        rules: {
            nome:           { required: true },
            instituicao:    { required: true },
            tipo:           { required: true },
            dataInicial:    { required: true },
            horaInicial:    { required: true, time: true },
            horaFinal:      { time: true }
        },
        messages: {
            nome: {required: "Este campo é requerido"},
            instituicao: {required: "Este campo é requerido"},
            tipo: {required: "Este campo é requerido"},
            dataInicial: {required: "Este campo é requerido"},
            horaInicial: {required: "Este campo é requerido"}
        },
        errorPlacement: function(error, element) {
            if (element.attr("name") == "tipo") {
                element.parent().parent().parent().append(error);
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: function(form) {
            var t = jQuery(form);
            var url = t.attr('action');
            jQuery('button').fadeOut(20, function() {
                jQuery('#submitloading').fadeIn(20)
            });
            jQuery.post(url, t.serialize(), salvarCallback, "json");
            return false;
        }
    });

    //Validação de periodo
    jQuery(".periodo").change(function() {
        validaPeriodo('dataInicial', 'dataFinal', 'horaInicial', 'horaFinal');

    });


});


function salvarCallback(data) {
    if (data.response == 1) {
        jQuery.jGrowl(data.success, {life: 4000, position: 'center', close: function() {
            location.href = "conselheiro/lista";
            }});
        
    } else {
        jQuery('#submitloading').fadeOut(20, function() {
            jQuery('button').fadeIn(20);
        });
        jQuery.each(data.error, function(index, value) {
            jQuery.jGrowl(value, {life: 4000, position: 'center'});
        });

    }
}


