jQuery(document).ready(function () {
    jQuery('input:file').uniform();

    // prepara validações de campos
    jQuery("#frm").validate({
        rules: {
            onfocusout: true,
            onsubmit: true,
            data_inicial: {required: true},
            hora_inicial: {required: true, time: true},
            hora_final: {time: true},
            nome: {required: true, remote: 'video/validacaoNome/' + jQuery("#id").val()},
            "sites[]": {required: true},
            link: {
                required: true,
                remote: {
                    url: 'video/validacaoVideo',
                    type: 'post',
                    data: {
                        link: function () {
                            return jQuery("#link").val();
                        }
                    },
                    complete: function (request) {
                        if (request.responseText == "true") {
                            habilitarCampos();
                            return true;
                        }

                        desabilitarCampos();
                        return false;
                    }
                }
            }
        },
        errorPlacement: function (error, element) {
            if (element.is('input[type="radio"]')) {
                element.parent().append(error);
            } else {
                error.insertAfter(element);
            }

        },
        messages: {
            link: {remote: "Informe um link válido!"}
        },
        submitHandler: function (form) {
            var t = jQuery(form);
            var url = t.attr('action');
            criarHiddensVideosRelacionados();
            jQuery('button').fadeOut(20, function () {
                jQuery('#submitloading').fadeIn(20)
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
            url: 'video/validaSubsiteVinculadoVideo',
            type: 'post',
            dataType: 'json',
            data: {id: id},

            success: function (data) {
                bt.attr("disabled", false);
                if (data.permissao == false) {
                    jQuery("button").addClass("compartilhar").text("Compartilhar nos sites selecionados");
                    jQuery(".field input").off().attr("readonly", true);
                    jQuery('#estado').css({"display":"none"});
                    jQuery("#frm").attr("action", "video/compartilhar");
                    jQuery("#sites").rules("remove", "required");
                    jQuery('#resumo').attr('readonly', true);
                    jQuery("input:radio").on("click", function(){
                        return false;
                    });
                    jQuery(".field .btn_no_icon").click(false);
                    jQuery(".delete").click(false);
                }
            }
        });
    }

    //Validação de periodo
    jQuery(".periodo").change(function () {
        validaPeriodo('data_inicial', 'data_final', 'hora_inicial', 'hora_final');

    });

    jQuery("#link").change(function () {
        jQuery.post("video/getDadosVideo", {link: jQuery('#link').val()}, getDadosVideo, "json").done();
    });

    if (jQuery('#id').val() !== "") {
        habilitarCampos();
    }
    
    jQuery('#list-videos-relacionados').sortable();
});

function getDadosVideo(data) {
    if (!php.empty(data.key)) {
        jQuery('#embed').html(data.embed);
        jQuery('#tituloYoutube').val(data.titulo);
        jQuery('#resumo').val(data.descricao);
        jQuery('#autor').val(data.autor);
    } else {
        jQuery('#embed').empty();
        jQuery('#tituloYoutube').val("");
        jQuery('#resumo').val("");
        jQuery('#autor').val("");
    }

}

function salvarCallback(data) {
    if (data.response == 1) {
        jQuery.jGrowl(data.success, {life: 4000, position: 'center', close: function () {
                location.href = "video/lista";
            }});
    } else {
        jQuery.each(data.error, function (index, value) {
            jQuery.jGrowl(value, {life: 4000, position: 'center'});
        });
    }
}

function habilitarCampos() {
    jQuery('#embed').attr('disabled', false);
    jQuery('#tituloYoutube').attr('disabled', false);
    jQuery('#resumo').attr('disabled', false);
    jQuery('#autor').attr('disabled', false);
}

function desabilitarCampos() {
    jQuery('#embed').attr('disabled', true);
    jQuery('#tituloYoutube').attr('disabled', true);
    jQuery('#resumo').attr('disabled', true);
    jQuery('#autor').attr('disabled', true);
}

/**
 * Cria os hiddens para guardar a ordenação dos vídeos relacionados.
 */
function criarHiddensVideosRelacionados() {
    // Limpa as ordenações
    jQuery('.ordenacaoVideos').html('');
    
    // Recria as ordenações
    jQuery('#list-videos-relacionados li').each(function(index, element) {
        var id_video = jQuery(element).attr('id-video');
        var input    = '<input type="hidden" name="ordemVideosRelacionados['+ id_video +']" value="'+ (index + 1) +'" >';
        jQuery('.ordenacaoVideos').append(input);
    });
}
