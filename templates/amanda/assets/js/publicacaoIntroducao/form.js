jQuery(document).ready(function () {
    jQuery('input:checkbox, input:radio, input:file').uniform();

    // prepara validações de campos
    jQuery("#frm").validate({
        rules: {
            
            conteudo: {
                required: function () {
                    CKEDITOR.instances.conteudo.updateElement();
                }
            }
        },
        messages: {
            conteudo: {required: "Este campo é requerido"},
            horaInicial: { time: true },
            horaFinal: { time: true }
        },
        errorPlacement: function (error, element) {
            if (element.attr("name") == "edicao" || element.attr("name") == "paginas") {
                element.parent().parent().append(error);
            } else if (element.attr("name") == "conteudo") {
                element.parent().append(error);
            } else if (element.attr("name") == "imagemBanco") {
                jQuery('#imgSelecionadas').append(error);
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: function (form) {
            jQuery('#conteudo').val(CKEDITOR.instances.conteudo.getData());
            var t = jQuery(form);
            var url = t.attr('action');
            jQuery('button').fadeOut(20, function () {
                jQuery('#submitloading').fadeIn(20)
            });
            jQuery.post(url, t.serialize(), salvarCallback, "json");
            return false;
        }
    });

    //Validação de periodo
    jQuery(".periodo").change(function () {
        validaPeriodo('dataInicial', 'dataFinal', 'horaInicial', 'horaFinal');

    });
});

function salvarCallback(data) {
    if (data.response == 1) {
        jQuery.jGrowl(data.success, {
            life: 4000,
            position: 'center',
            close: function () {
                location.href = "publicacao/lista";
            }
        });
    } else {
        jQuery('#submitloading').fadeOut(20, function () {
            jQuery('button').fadeIn(20);
        });
        jQuery.each(data.error, function (index, value) {
            jQuery.jGrowl(value, {life: 4000, position: 'center'});
        });
    }
}
