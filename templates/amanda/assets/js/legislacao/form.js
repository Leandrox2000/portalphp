jQuery(document).ready(function() {
    jQuery('input, select').uniform();

    // prepara validações de campos
    jQuery("#frm").validate({
        rules: {
            data_inicial: {required: true},
            hora_inicial: {required: true, time: true},
            hora_final: { time: true },
            titulo: {required: true},
            categoria: {required: true},
            "sites[]": {required: true, minlength: 1},
            url: {required: true, url: true}

        },
        messages: {
            data_inicial: {required: "Este campo é requerido"},
            hora_inicial: {required: "Este campo é requerido"},
            titulo: {required: "Este campo é requerido"},
            categoria: {required: "Este campo é requerido"},
            "sites[]": {required: "Este campo é requerido"},
            url: {required: "Informe a url ou selecione um arquivo"}
        },
        errorPlacement: function(error, element) {
            if (element.attr("name") == "categoria") {
                element.parent().parent().append(error);

            } else if (element.attr("name") == "url") {
                if (jQuery('#url').val() == "" && jQuery('#arquivoNome').val() == "") {
                    error.insertAfter(element);
                    jQuery('#arquivo').parent().after("<label class='error' generated='true' for='arquivoNome'>Informe a url ou selecione um arquivo</label>");
                }
            } else {
                error.insertAfter(element);
            }

        },
        success: function(label) {
            if (label.attr("for") == 'url') {
                jQuery("label[for='arquivoNome']").remove();
            }
        },
        submitHandler: function(form) {
            if(CKEDITOR.instances.descricao) jQuery('#descricao').val(CKEDITOR.instances.descricao.getData());

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
    //var ckeditor = CKEDITOR.instances;
    if(!isNaN(id)) {
        var bt = jQuery(".break button").attr("disabled", true);
        jQuery.ajax({
            url: 'legislacao/validaSubsiteVinculadoLegislacao',
            type: 'post',
            dataType: 'json',
            data: {id: id},

            success: function (data) {
                bt.attr("disabled", false);
                if (data.permissao == false) {
                    jQuery("button").addClass("compartilhar").text("Compartilhar nos sites selecionados");
//                    jQuery(".btn_no_icon, .delete").click(false);
                    jQuery(".field input").off().attr("readonly", true);
                    //ckeditor.descricao.setReadOnly(true);
                    jQuery("#arquivo").click(false);
                    jQuery(".photo a").click(false);
                    jQuery("#uniform-categoria ~ .btn").click(false);
                    jQuery("#categoria").off();
                    jQuery("#categoria").attr("disabled", true);
                    jQuery("#frm").attr("action", "legislacao/compartilhar");
                    jQuery("#sites").rules("remove", "required");
                }
            }
        });
    }

    //Desabilita a url ou o arquivo
    if (jQuery('#url').val() !== "") {
        jQuery('#arquivo').attr('disabled', true);
        jQuery('#uniform-arquivo').addClass('uniform-disabled');
    }

    if (jQuery('#arquivoAtual').val() !== "") {
        jQuery('#url').attr('disabled', true);
    }


    //Validação de periodo
    jQuery(".periodo").change(function() {
        validaPeriodo('data_inicial', 'data_final', 'hora_inicial', 'hora_final');

    });

    //Desabilita o campo url quando o arquivo é selecionado
    jQuery('#arquivo').change(function() {
        jQuery('#url').attr('disabled', true);
    });

    //Desabilita o campo url quando o arquivo é selecionado
    jQuery('#url').change(function() {

        if (jQuery('#url').val() == "") {
            jQuery('#arquivo').attr('disabled', false);
            jQuery('#uniform-arquivo').removeClass('uniform-disabled');
        } else {
            jQuery('#arquivo').attr('disabled', true);
            jQuery('#uniform-arquivo').addClass('uniform-disabled');
        }
    });


    //Upload de Arquivo Simples
    jQuery('#arquivo').change(function() {
        var progressBar = ".Progressarquivo .redbar";
        var fileField = jQuery(this).parent();
        fileField.fadeOut(20, function() {
            jQuery(this).next().fadeIn(200);
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
                    jQuery(this).prev().fadeIn(600);
                });
            },
            url: baseUrl() + 'upload/adicionar',
            data: {
                field: 'arquivo',
                extensions: 'txt,pdf,xls,xlsx,doc,docx,jpg,png,csv,zip'
            }
        });
    });




});

function salvarCallback(data) {
    if (data.response == 1) {
        jQuery.jGrowl(data.success, {life: 4000, position: 'center', close: function() {
        location.href = "legislacao/lista";
    }});
        

    } else {
        jQuery.each(data.error, function(index, value) {
            jQuery.jGrowl(value, {life: 4000, position: 'center'});
        });
    }
}

function abrirCategorias()
{
    var link = baseUrl();
    jQuery.colorbox({href: link + "legislacao/categorias", width: "75%", height: "90%", onComplete: carregaCategorias, onClosed: carregaCategoriasSelect});
}

function carregaCategoriasSelect()
{
    jQuery("#categoria").html(new Option("Carregando"));

    jQuery.post(baseUrl() + "legislacao/getCategorias", function(data) {
        jQuery("#categoria").html(new Option("Selecione", ""));
        jQuery.each(data, function(index, value) {
            jQuery("#categoria").append(new Option(value.label, value.id));
        });
    }, "json");

}


function removerArquivoTemp(nome, extensao)
{
    jConfirm('Deseja realmente executar esta ação?', 'Confirmar Exclusão', function(response) {
        if (response) {
            var div = jQuery("#" + nome);
            jQuery('#arquivoNome').val("");
            jQuery('#uniform-arquivo .filename').html("Nenhum Arquivo");
            jQuery('#url').attr('disabled', false);
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
            var div = jQuery("#" + id);
            jQuery('#arquivoExcluido').val(jQuery('#arquivoNome').val());
            jQuery('#uniform-arquivo .filename').html("Nenhum Arquivo");
            jQuery('#url').attr('disabled', false);
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

function getHtml(data)
{
    funcao = "javascript:removerArquivoTemp('" + data.temp_name + "','" + data.extensao + "')";


    return '<div id="' + data.temp_name + '" class="photo">\n' +
            '<img src="uploads/boletimeletronico/icone.jpg" />\n' +
            '<br/>\n' +
            '<a href="' + funcao + '">Excluir Arquivo</a>\n' +
            '</div>';
}
