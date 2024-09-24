jQuery(document).ready(function() {
    jQuery('input:file').uniform();

    //Aplica máscara de validação no campo edição
    //jQuery("#edicao").mask("nº 99/9999"); //old
     jQuery("#numedicao").mask("9999");
     jQuery("#anoedicao").mask("9999");

    // prepara validações de campos
    jQuery("#frm").validate({
        rules: {
            numero: {required: true},
            ano: {required: true},
            data_inicial: {required: true},
            hora_inicial: {required: true, time:true },
            hora_final: { time:true },
            periodo_inicial: {required: true},
            periodo_final: {required: true},
            arquivoNome: {required: true}
        },
        messages: {
            numero: {required: "Este campo é requerido"},
            ano: {required: "Este campo é requerido"},
            data_inicial: {required: "Este campo é requerido"},
            hora_inicial: {required: "Este campo é requerido"},
            periodo_inicial: {required: "Este campo é requerido"},
            periodo_final: {required: "Este campo é requerido"},
            arquivoNome: {required: "Este campo é requerido"},
        },
        errorPlacement: function(error, element) {
            if (element.attr("name") == "arquivo" || element.attr("name") == "arquivoNome") {
                jQuery('#arquivo').parent().after(error);
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

    //Upload de Arquivo Simples
    jQuery('#arquivo').change(function() {
        var progressBar = ".Progressarquivo .redbar";
        var fileField = jQuery(this).parent()
        fileField.fadeOut(20, function() {
            jQuery(this).next().fadeIn(200)
        });

        jQuery('#frm').ajaxSubmit({
            dataType: 'json',
            uploadProgress: function(event, position, total, percentComplete) {
                jQuery(progressBar).css('width', percentComplete + '%').text('' + percentComplete + '%');
            },
            success: function(data) {
                var count = 0;
                jQuery(progressBar).css('width', '100%').text('Concluído (100%)');
                setTimeout(function() {
                    jQuery.each(data, function(index, value) {
                        if (data[index].error.length > 0) {
                            jQuery.each(value.error, function(index, msg) {
                                jQuery.jGrowl(msg, {life: 4000, position: 'center'});
                            });
                            fileField.fadeIn(200);
                        } else {
                            jQuery('#uniform-arquivo').after(
                                    jQuery(getHtml(value))
                                    );
                            jQuery('#arquivoNome').val(value.temp_name + "." + value.extensao);
                        }
                    });
                    jQuery('.Progressarquivo').fadeOut(700);
                }, 1000);
            },
            error: function() {
                jQuery('#arquivo').parent().next().fadeOut(600, function() {
                    jQuery(this).prev().fadeIn(600)
                });
            },
            url: baseUrl() + 'upload/adicionar',
            data: {
                field: 'arquivo',
                extensions: 'txt,pdf,xls,xlsx,doc,docx,jpg,png,csv,zip',
            }
        });
    });

    //Validação de periodo
    jQuery(".periodo").change(function() {
        validaPeriodo('data_inicial', 'data_final', 'hora_inicial', 'hora_final');

    });



//    //Validação do mês do campo de edição
//    jQuery("#edicao").change(function() {
//        if (jQuery("#edicao").val() !== "") {
//            var edicao = jQuery("#edicao").val();
//            edicao = new Number(edicao.substr(3, 2));
//
//            if (edicao <= 0 || edicao > 12) {
//                jQuery("#edicao").val("");
//            }
//
//        }
//
//    });


});


function salvarCallback(data) {
    if (data.response == 1) {
        jQuery.jGrowl(data.success, {life: 4000, position: 'center', close: function() {
         location.href = "boletimEletronico/lista";
     }});


    } else {
        jQuery('#frmdiv #arquivoloading').fadeOut(20, function() {
            jQuery('#frmdiv button').fadeIn(20);
        });
        jQuery.each(data.error, function(index, value) {
            jQuery.jGrowl(value, {life: 4000, position: 'center'});
        });
    }
}

function getHtml(data)
{
    funcao = "javascript:removerArquivoTemp('" + data.temp_name + "','" + data.extensao + "')";


    return '<div id="' + data.temp_name + '" class="photo">\n' +
            '<img src="uploads/boletimeletronico/icone.jpg" />\n' +
            '<br/>\n' +
            '<a href="' + funcao + '">Excluir Arquivo</a>\n' +
            '</div>';
}

function removerArquivoTemp(nome, extensao)
{
    jConfirm('Deseja realmente executar esta ação?', 'Confirmar Exclusão', function(response) {
        if (response) {
            var div = jQuery("#" + nome);
            jQuery('#arquivoNome').val("");
            jQuery.post("upload/remover/" + nome + "." + extensao, function(data) {
                div.fadeOut(400,
                        function() {
                            jQuery(this).parent().children(":first").fadeIn(400);
                            jQuery(this).remove();
                            jQuery.jGrowl('Ação executada com sucesso!', {life: 4000, position: 'center'});
                        }
                );
            }, "json");
        }
    });
}

function removerImagem(id)
{
    jConfirm('Deseja realmente executar esta ação?', 'Confirmar Exclusão', function(response) {
        if (response) {
            var div = jQuery("#" + id);
            jQuery('#arquivoExcluido').val(jQuery('#arquivoNome').val());
            jQuery('#arquivoNome').val("");
            div.fadeOut(400,
                    function() {
                        jQuery(this).parent().children(":first").fadeIn(400).children(":first").fadeIn(400);
                        jQuery(this).remove();
                        jQuery.jGrowl('Ação executada com sucesso!', {life: 4000, position: 'center'});
                    }
            );
        }
    });
}


