jQuery.validator.addMethod(
    "exibirEmAoMenosUmLugar",
    function(value, element) {
        var cPortal = jQuery('#check-ex-portal:checked').val();
        var cIntranet = jQuery('#check-ex-intranet:checked').val();

        // Se nenhuma checkbox estiver marcada
        if (!cPortal && !cIntranet) {
            return false;
        }

        return true;
    },
    "Selecione ao menos uma opção."
);

jQuery(document).ready(function() {
    jQuery('input:checkbox, input:radio, input:file, select').uniform();

    //Validação de periodo
    jQuery(".periodo").change(function() {
        validaPeriodo('data_inicial', 'data_final', 'hora_inicial', 'hora_final');

    });

    // prepara validações de campos
    jQuery("#frm").validate({
        rules: {
            nome: {required: true},
            email: {required: true, email: true},
            unidade: {required: true},
            vinculo: {required: true},
            data_inicial: {required: true},
            hora_inicial: {required: true, time: true},
            hora_final: { time: true },
            exibir_portal: {exibirEmAoMenosUmLugar: true},
            exibir_intranet: {exibirEmAoMenosUmLugar: true},
            exibir_organograma: {exibirEmAoMenosUmLugar: true}
        },
        messages: {
            nome: {required: "Este campo é requerido"},
            email: {required: "Este campo é requerido", email: "Formato inválido"},
            curriculo: {required: "Este campo é requerido"},
            data_inicial: {required: "Este campo é requerido"},
            hora_final: {required: "Este campo é requerido"}
        },
        errorPlacement: function(error, element) {
            var exibirEm = ['exibir_portal', 'exibir_intranet', 'exibir_organograma'];

            if (element.attr("name") == "vinculo" || element.attr("name") == "unidade") {
                element.parent().parent().append(error);
            }
            else if (jQuery.inArray(element.attr('name'), exibirEm) > -1) {
                jQuery('.par.exibir-em .error-message').html(error);
            }
            else {
                error.insertAfter(element);
            }
        },
        submitHandler: function(form) {
            if(CKEDITOR.instances.curriculo) jQuery('#curriculo').val(CKEDITOR.instances.curriculo.getData());
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
    jQuery('#imagem').change(function() {
        var progressBar = ".Progressimagem .redbar";
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
                            jQuery('#uniform-imagem').after(
                                    jQuery(getHtml(value))
                                    );
                            jQuery('#imagemNome').val(value.temp_name + "." + value.extensao);
                        }
                    });
                    jQuery('.Progressimagem').fadeOut(700);
                }, 1000);
            },
            error: function() {
                jAlert('Erro ao enviar imagem\n Verifique se o arquivo é do tipo jpg, png ou jpeg.');
                jQuery('#imagem').parent().next().fadeOut(600, function() {
                    jQuery(this).prev().fadeIn(600)
                });
            },
            url: baseUrl() + 'upload/adicionar',
            data: {
                field: 'imagem',
                extensions: 'jpg,png,jpeg',
            }
        });
    });
    jQuery("#inserirLink").change(mostraLink);

    mostraLink();



});


function salvarCallback(data) {
    if (data.response == 1) {
        jQuery.jGrowl(data.success, {life: 4000, position: 'center', close: function() {
          location.href = "funcionario/lista";      
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

function getHtml(data)
{
    funcao = "javascript:removerArquivoTemp('" + data.temp_name + "','" + data.extensao + "')";
    return   '<ul class="imagelist" id="'+ data.temp_name +'"><li><img src="uploads/temp/' + data.temp_name + '.' + data.extensao + '" width="230px"/>\n<a class="btn btn3 btn_trash" href="'+ funcao + '"></a></li></ul>';
}

function removerArquivoTemp(nome, extensao)
{
    jConfirm('Deseja realmente executar esta ação?', 'Confirmar Exclusão', function(response) {
        if (response) {
            var div = jQuery("#" + nome);
            jQuery('#imagemNome').val("");
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
            var div = jQuery("#ulImagem" + id);
            jQuery('#imagemExcluida').val(jQuery('#imagemNome').val());
            jQuery('#imagemNome').val("");
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

function mostraLink()
{
    if (jQuery("#inserirLink").is(":checked")) {
        jQuery(".link").fadeIn(500);
    } else {
        jQuery(".link").fadeOut(500);
    }
}

function abrirCargos()
{
    var link = baseUrl();
    jQuery.colorbox({href: link + "funcionario/cargos", width: "75%", height: "90%", onComplete: carregaCargos, onClosed: carregaCargosSelect});
}

function carregaCargosSelect()
{
    jQuery("#cargo").html(new Option("Carregando"));

    jQuery.post(baseUrl() + "funcionario/getCargos", function(data) {
        jQuery("#cargo").html(new Option("Selecione", ""));
        jQuery.each(data, function(index, value) {
            jQuery("#cargo").append(new Option(value.label, value.id));
        });
    }, "json");

}


function abrirVinculos()
{
    var link = baseUrl();
    jQuery.colorbox({href: link + "funcionario/vinculos", width: "75%", height: "90%", onComplete: carregaCargos, onClosed: carregaVinculosSelect});
}

function carregaVinculosSelect()
{
    jQuery("#vinculo").html(new Option("Carregando"));

    jQuery.post(baseUrl() + "funcionario/getVinculos", function(data) {
        jQuery("#vinculo").html(new Option("Selecione", ""));
        jQuery.each(data, function(index, value) {
            jQuery("#vinculo").append(new Option(value.label, value.id));
        });
    }, "json");

}

function abrirUnidades() {
    window.open('unidade/form');
}

function atualizarUnidades() {
    jQuery("#unidade").html(new Option("Carregando..."));

    jQuery.post(baseUrl() + "funcionario/getUnidades", function(data) {
        jQuery("#unidade").html(new Option("Selecione"));
        jQuery.each(data, function(index, value) {
            jQuery("#unidade").append(new Option(value.label, value.id));
        });
    }, "json");
}