jQuery(document).ready(function() {
    
    jQuery('input:checkbox, input:radio, input:file, select').uniform();

    // prepara validações de campos
    jQuery("#frm").validate({
        rules: {
            titulo: { required: true },
            local: { required: true },
            dataInicial: { required: true },
            horaInicial: { required: true, time: true },
            dataFinal: { required: 
                { 
                    depends: function(element) {
                        return jQuery('#horaFinal').val() != '';
                    } 
                } 
            },
            horaFinal: { required: 
                { 
                    depends: function(element) {
                        return jQuery('#dataFinal').val() != '';
                    } 
                } 
            },
            compromissoDtInicial: { required: true },
            compromissoHrInicial: { required: true, time: true },
            compromissoDtFinal: { required: 
                { 
                    depends: function(element) {
                        return jQuery('#compromissoHrFinal').val() != '';
                    } 
                } 
            },
            compromissoHrFinal: { required: 
                { 
                    depends: function(element) {
                        return jQuery('#compromissoDtFinal').val() != '';
                    } 
                } 
            },
            'agendas[]': { required: true }
        },
        submitHandler: function(form) {
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
        validaPeriodo('compromissoDtInicial', 'compromissoDtFinal', 'compromissoHrInicial', 'compromissoHrFinal');
    });

});

function salvarCallback(data) {
    if (data.response == 1) {
        jQuery.jGrowl(data.success, {life: 4000, position: 'center', close: function() {
            location.href = "compromisso/lista";
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

