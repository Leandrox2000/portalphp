jQuery(document).ready(function() {

    jQuery('input:checkbox, input:radio, input:file, select').uniform();

    // prepara validações de campos
    jQuery("#frm").validate({
        rules: {
            nome: {required: true},
            estado: {required: true},
            cep: {required: true},
            cidade: {required: true},
            bairro: {required: true},
            endereco: {required: true},
            numero: {required: true},
            telefone: {required: true}
        },
        messages: {
            nome: {required: "Este campo é requerido"},
            estado: {required: "Este campo é requerido"},
            cep: {required: "Este campo é requerido"},
            cidade: {required: "Este campo é requerido"},
            bairro: {required: "Este campo é requerido"},
            numero: {required: "Este campo é requerido"},
            telefone: {required: "Este campo é requerido"},
            endereco: {required: "Este campo é requerido"}
        },
        errorPlacement: function(error, element) {
            if (element.attr("name") == "estado") {
                element.parent().after(error);

            } else {
                error.insertAfter(element);

            }

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

    jQuery("#cep").mask(
            "99999-999",
            {completed: function() {
                    jQuery("#ceploading").show();
                    jQuery.getScript('http://cep.republicavirtual.com.br/web_cep.php?formato=javascript&cep=' + this.val(), function(data) {
                        jQuery('#estado').val(unescape(resultadoCEP.uf));
                        jQuery.uniform.update();
                        jQuery('#cidade').val(unescape(resultadoCEP.cidade));
                        jQuery('#bairro').val(unescape(resultadoCEP.bairro));
                        jQuery('#endereco').val(unescape(resultadoCEP.tipo_logradouro) + ' ' + unescape(resultadoCEP.logradouro));
//                if(jQuery('#estado').val()==''){
//                    jAlert('CEP Inválido', 'Alerta');
//                    jQuery("#cep").val('');
//                }
                        jQuery("#ceploading").hide();

                    })
                }}
    );


});


function salvarCallback(data) {
    if (data.response == 1) {
        jQuery.jGrowl(data.success, {life: 4000, position: 'center', close: function() {
            location.href = "unidade/lista";
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

