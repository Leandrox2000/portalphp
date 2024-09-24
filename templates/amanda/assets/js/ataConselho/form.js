jQuery(document).ready(function() {

    jQuery('input:checkbox, input:radio, input:file, select').uniform();

    // prepara validações de campos
    jQuery("#frm").validate({
        rules: {
            nome:           { required: true },
            tipo:           { required: true },
            dataInicial:    { required: true },
            dataReuniao:    { required: true },
            horaInicial:    { required: true, time: true },
            horaFinal:      { time: true },
            arquivoNome:    {required: true}
        },
        messages: {
            nome:           {required: "Este campo é requerido"},
            tipo:           {required: "Este campo é requerido"},
            dataInicial:    {required: "Este campo é requerido"},
            dataReuniao:    {required: "Este campo é requerido"},
            horaInicial:    {required: "Este campo é requerido"},
            arquivoNome:    {required: "Este campo é requerido"}
        },
        errorPlacement: function(error, element) {
            if (element.attr("name") == "arquivo" || element.attr("name") == "arquivoNome") {
                jQuery('#arquivo').parent().after(error);
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: function(form) {
            if(CKEDITOR.instances.descricao) jQuery('#descricao').val(CKEDITOR.instances.descricao.getData());
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
            }
        });
    });



});


function salvarCallback(data) {
    if (data.response == 1) {
        jQuery.jGrowl(data.success, {life: 4000, position: 'center', close: function() {
            location.href = "ataConselho/lista";
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


    return '<div id="divArquivo" class="photo">\n' +
            '<a target="_blank" href="uploads/temp/' + data.real_name + '">'+
                '<img src="templates/amanda/assets/images/icons/blogperfume/Article/article 48.png" />\n' +
            '<a/>'+
            '<br/>\n' +
            '<a href="' + funcao + '">Excluir Arquivo</a>\n' +
            '</div>';
}

function removerArquivoTemp(nome, extensao)
{
    jConfirm('Deseja realmente executar esta ação?', 'Confirmar Exclusão', function(response) {
        if (response) {
            var div = jQuery("#divArquivo");
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

function removerArquivo(id)
{
    jConfirm('Deseja realmente executar esta ação?', 'Confirmar Exclusão', function(response) {
        if (response) {
            var div = jQuery("#divArquivo");
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

