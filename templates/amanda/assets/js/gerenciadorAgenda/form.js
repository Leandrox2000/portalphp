jQuery(document).ready(function() {
    
    jQuery('input:checkbox, input:radio, input:file, select').uniform();

    // prepara validações de campos
    jQuery("#frm").validate({
        rules: {
            titulo: { required: true },
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
            'sites[]': { required: true },
            'responsaveis[]': { required: true }
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
    
    var position = window.location.href.lastIndexOf("/");
    var id = window.location.href.slice(position + 1);
    
    if(!isNaN(id)) {
        var bt = jQuery(".break button").attr("disabled", true);
        jQuery.ajax({
            url: 'gerenciadorAgenda/validaSubsiteVinculadoAgenda',
            type: 'post',
            dataType: 'json',
            data: {id: id},

            success: function (data) {
                bt.attr("disabled", false);
                if (data.permissao == false) {
                    jQuery(".field input").off().attr("readonly", "readonly");
                    jQuery("#sites").rules("remove", "required");
                }
            }
        });
    }

    //Validação de periodo
    jQuery(".periodo").change(function() {
        validaPeriodo('dataInicial', 'dataFinal', 'horaInicial', 'horaFinal');
    });

});


function salvarCallback(data) {
    if (data.response == 1) {
        jQuery.jGrowl(data.success, {life: 4000, position: 'center', close: function() {
            location.href = "gerenciadorAgenda/lista";
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

