jQuery(document).ready(function() {

    jQuery('input:checkbox, input:radio, input:file, select').uniform();
    jQuery("#frm").validate({
        rules: {
            dataInicial: {required: true},
            horaInicial: {required: true, time: true},
            // dataFinal: {required: true},
            // horaFinal: { time: true },
            categoria: {required: true},
            nome: {required: true},
            imagemBanco: {required: true},
            dataFinal: { required: function(element) {
                return jQuery('#horaFinal').val() != ""
            }},
            horaFinal: { required: function(element) {
                return jQuery('#dataFinal').val() != ""
            }}
         
        },
        messages: {
            dataInicial: {required: "Este campo é requerido"},
            horaInicial: {required: "Este campo é requerido"},
            nome: {required: "Este campo é requerido"},
            imagemBanco: {required: "Este campo é requerido"}
        },
        errorPlacement: function(error, element) {
            // Se for um SELECT (fix para UniformJS)
            if (element.attr('name') == 'imagemBanco') {
                jQuery('#imgSelecionadas').parent().append(error);
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
                        jQuery("#ceploading").hide();

                    });
                }}
    );


});

function salvarCallback(data) {
    if (data.response == 1) {
        jQuery.jGrowl(data.success, {life: 4000, position: 'center', close: function() {
            location.href = "backgroundHome/lista";
            }});
        
    } else {
        jQuery('#submitloading').fadeOut(20, function() {
            jQuery('button').fadeIn(20);
        });
        jQuery.each(data.error, function(index, value) {
            jQuery.jGrowl(value, {life: 40000, position: 'center'});
        });

    }
}

/* Início seleção de imagem */
function setarImagemRadio()
{
    var v = jQuery("input[name='imagem']:checked").val();

    if (v !== "" && v !== undefined) {
        jQuery('#imagemBanco').val(v);

        jQuery.get("backgroundHome/getHtmlImagens/" + v).done(function(data) {
            jQuery('#imgSelecionadas').html(data);
        });
    }
}
function excluirImagem(id)
{
    jQuery('#imagemBanco').val("");
    jQuery('#img'+id).remove();
    
}
/* Fim seleção de imagem */
