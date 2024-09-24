jQuery(document).ready(function () {
    jQuery('input:checkbox, input:radio, input:file, select').uniform();

    // prepara validações de campos
    jQuery("#frm").validate({
        rules: {
            endereco: {
                required: function() {
                    if(CKEDITOR.instances.endereco) CKEDITOR.instances.endereco.updateElement();
                }
            },
//            'sites[]':      { required: true, minlength: 1 },
            dataInicial:    { required: true },
            horaInicial:    { required: true, time: true },
            horaFinal:      { time: true }
        },
        messages: {
//            'sites[]':      {required: "Este campo é requerido"},
            hora_inicial:   {required: "Este campo é requerido"},
            data_inicial:   {required: "Este campo é requerido"}
        },
        submitHandler: function(form) {
            if(CKEDITOR.instances.endereco) jQuery('#endereco').val(CKEDITOR.instances.endereco.getData());
            var t = jQuery(form);
            var url = t.attr('action');
            jQuery('button').fadeOut(20, function() {
                jQuery('#submitloading').fadeIn(20);
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
        jQuery.jGrowl(data.success, {life: 3000, position: 'center', close: function() {
            location.href = "enderecoRodape/lista";
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

