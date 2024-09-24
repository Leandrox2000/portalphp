jQuery.validator.addMethod(
    "aoMenosUmaOpcao",
    function(value, element) {
        var cLivraria = jQuery('#check-tipo-livraria:checked').val();
        var cPublicacaoes = jQuery('#check-tipo-publicacao:checked').val();

        // Se nenhuma checkbox estiver marcada
        if (!cLivraria && !cPublicacaoes) {
            return false;
        }

        return true;
    },
    "Selecione ao menos uma opção."
);

jQuery(document).ready(function () {
    jQuery('input:checkbox, input:radio, input:file').uniform();

    // prepara validações de campos
    jQuery("#frm").validate({
        rules: {
            titulo: { required: true },
            autor: { required: true },
            edicao: { required: true },
            tipo_livraria: { aoMenosUmaOpcao: true },
            categoria: { required: true },
            dataInicial: { required: true },
            horaInicial: { required: true, time: true },
            horaFinal: { time: true },
            imagemBanco: { required: true },
            conteudo: {
                required: function() {
                    CKEDITOR.instances.conteudo.updateElement();
                }
            }
        },
        errorPlacement: function (error, element) {
            var opcoesTipo = ['tipo_livraria', 'tipo_publicacao'];
            
            if (element.attr("name") == "paginas") {
                element.parent().parent().append(error);
            } else if (jQuery.inArray(element.attr('name'), opcoesTipo) > -1) {
                jQuery('.par.tipo-registro .error-message').html(error);
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

    //Upload de Arquivo Simples
    jQuery('#arquivo').change(function () {
        var progressBar = ".Progressarquivo .redbar";
        var fileField = jQuery(this).parent()
        fileField.fadeOut(20, function () {
            jQuery(this).next().fadeIn(200)
        });

        jQuery('#frm').ajaxSubmit({
            dataType: 'json',
            uploadProgress: function (event, position, total, percentComplete) {
                jQuery(progressBar).css('width', percentComplete + '%').text('' + percentComplete + '%');
            },
            success: function (data) {
                var count = 0;
                jQuery(progressBar).css('width', '100%').text('Concluído (100%)');
                setTimeout(function () {
                    jQuery.each(data, function (index, value) {
                        if (data[index].error.length > 0) {
                            jQuery.each(value.error, function (index, msg) {
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
            error: function () {
                jQuery('#arquivo').parent().next().fadeOut(600, function () {
                    jQuery(this).prev().fadeIn(600)
                });
            },
            url: baseUrl() + 'upload/adicionar',
            data: {
                field: 'arquivo',
            }
        });
    });

    //Upload de Arquivo Simples
    jQuery('#imagem').change(function () {
        var progressBar = ".Progressimagem .redbar";
        var fileField = jQuery(this).parent()
        fileField.fadeOut(20, function () {
            jQuery(this).next().fadeIn(200)
        });

        jQuery('#frm').ajaxSubmit({
            dataType: 'json',
            uploadProgress: function (event, position, total, percentComplete) {
                jQuery(progressBar).css('width', percentComplete + '%').text('' + percentComplete + '%');
            },
            success: function (data) {
                var count = 0;
                jQuery(progressBar).css('width', '100%').text('Concluído (100%)');
                setTimeout(function () {
                    jQuery.each(data, function (index, value) {
                        if (data[index].error.length > 0) {
                            jQuery.each(value.error, function (index, msg) {
                                jQuery.jGrowl(msg, {life: 4000, position: 'center'});
                            });
                            fileField.fadeIn(200);
                        } else {
                            jQuery('#uniform-imagem').after(
                                    jQuery(getHtml(value))
                                    );
                            jQuery('#imagemNome').val(value.temp_name + "." + value.extensao);
                            cropImage();
                        }
                    });
                    jQuery('.Progressimagem').fadeOut(700);
                }, 1000);
            },
            error: function () {
                jAlert('Erro ao enviar imagem\n Verifique se o arquivo é do tipo jpg, png ou jpeg.');
                jQuery('#imagem').parent().next().fadeOut(600, function () {
                    jQuery(this).prev().fadeIn(600)
                });
            },
            url: baseUrl() + 'upload/adicionar',
            data: {
                field: 'imagem',
                extensions: 'jpg,png,jpeg',
            }
        });
    })
    jQuery("#inserirLink").change(mostraLink);

    mostraLink();



});


function salvarCallback(data) {
    if (data.response == 1) {
        jQuery.jGrowl(data.success, {life: 4000, position: 'center', close: function() {
            location.href = "publicacao/lista";

            }});
        
    } else {
        jQuery('#submitloading').fadeOut(20, function () {
            jQuery('button').fadeIn(20);
        });
        jQuery.each(data.error, function (index, value) {
            jQuery.jGrowl(value, {life: 4000, position: 'center'});
        });

    }
}


function getHtml(data)
{
    funcao = "javascript:removerArquivoTemp('" + data.temp_name + "','" + data.extensao + "')";


    return '<div id="' + data.temp_name + '" class="photo">\n' +
            '<img id="imgCrop" src="uploads/temp/' + data.temp_name + '.' + data.extensao + '"/>\n' +
            '<br/>\n' +
            '<a href="' + funcao + '">Excluir Arquivo</a>\n' +
            '</div>';
}



function removerArquivoTemp(nome, extensao)
{
    jConfirm('Deseja realmente Excluir a imagem selecionada?', 'Confirmar Exclusão', function (response) {
        if (response) {
            var div = jQuery("#" + nome);
            jQuery('#imagemNome').val("");
            jQuery.post("upload/remover/" + nome + "." + extensao, function (data) {
                div.fadeOut(400,
                        function () {
                            jQuery(this).parent().children(":first").fadeIn(400);
                            jQuery(this).remove();
                            jQuery.jGrowl('Imagem excluída com sucesso!', {life: 4000, position: 'center'});

                        }
                );
            }, "json");
        }
    });
}

function removerImagem(id)
{
    jConfirm('Deseja realmente Excluir a imagem selecionada?', 'Confirmar Exclusão', function (response) {
        if (response) {
            var div = jQuery("#" + id);
            jQuery('#imagemExcluida').val(jQuery('#imagemNome').val());
            jQuery('#imagemNome').val("");
            div.fadeOut(400,
                    function () {
                        jQuery(this).parent().children(":first").fadeIn(400).children(":first").fadeIn(400);
                        jQuery(this).remove();
                        jQuery.jGrowl('Imagem excluída com sucesso!', {life: 4000, position: 'center'});

                    }
            );
        }
    });
}

function cropImage()
{
    // Create variables (in this scope) to hold the API and image size
    var jcrop_api, boundx, boundy;
    jQuery('#imgCrop').Jcrop({
        //aspectRatio: 1920/1080,
        onChange: showCoords,
        onSelect: showCoords,
        onRelease: clearCoords,
        //allowSelect: false,
        // minSize: [ 100, 100 ]
    }, function () {
        // Use the API to get the real image size
        var bounds = this.getBounds();
        boundx = bounds[0];
        boundy = bounds[1];

        // Store the API in the jcrop_api variable
        jcrop_api = this;
        jcrop_api.animateTo([0, 0, 10, 10]);
    });
}

function showCoords(c)
{
    jQuery('#x1').val(c.x);
    jQuery('#y1').val(c.y);
    jQuery('#x2').val(c.x2);
    jQuery('#y2').val(c.y2);
    jQuery('#w').val(c.w);
    jQuery('#h').val(c.h);
}
;

function clearCoords()
{
    jQuery('#x1, #y1, #x2, #y2, #w, #h').val('');
}
;

function mostraLink()
{
    if (jQuery("#inserirLink").is(":checked")) {
        jQuery(".link").fadeIn(500);
    } else {
        jQuery(".link").fadeOut(500);
    }
}
function abrirCategorias()
{
    var link = baseUrl();
    jQuery.colorbox({href: link + "publicacao/categorias", width: "75%", height: "90%", onComplete: carregaCategorias, onClosed: carregaCategoriasSelect});
}

function carregaCategoriasSelect()
{
    jQuery("#categoria").html(new Option("Carregando"));

    jQuery.post(baseUrl() + "publicacao/getCategorias", function (data) {
        jQuery("#categoria").html(new Option("Selecione"));
        jQuery.each(data, function (index, value) {
            jQuery("#categoria").append(new Option(value.label, value.id));
        });
    }, "json");

}

function setarImagemRadio()
{
    var v = jQuery("input[name='imagem']:checked").val();
    jQuery('#imagemBanco').val(v);

    if (v !== "" && v !== undefined) {
        jQuery.get("publicacao/getHtmlImagens/" + v).done(function (data) {
            jQuery('#imgSelecionadas').html(data);
        });
    }

}

function excluirImagem(id)
{
    jQuery('#imagemBanco').val("");
    jQuery('#img' + id).remove();

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

