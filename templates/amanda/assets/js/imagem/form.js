jQuery(document).ready(function() {
    jQuery('input:checkbox, input:radio, input:file, select').uniform();

    // prepara validações de campos
    jQuery("#frm").validate({
        rules: {
            categoria: { required: true },
            pasta: { required: true },
            nome: { required: true, maxlength: 150 },
            credito: { required: true, maxlength: 100 },
            legenda: { required: true, maxlength: 200 },
            imagemNome: { required: true },
            palavrasChave:  { required: true, tagsMaxMin: true }
        },
        messages: {
            palavrasChave: {
                tagsMaxMin: "Este campo deve ter entre 3 e 6 tags."
            }
        },
        errorPlacement: function(error, element) {
            if (element.attr("name") == "imagem" || element.attr("name") == "imagemNome") {
                jQuery('#imagem').parent().after(error);
            } else if (element.attr("name") == "pasta" || element.attr("name") == "categoria") {
                element.parent().parent().append(error);
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

    //Busca as pastas relacionadas a uma categoria
    jQuery('#categoria').change(function() {
        jQuery.post("imagem/carregaPastasSelect/" + jQuery('#categoria').val(), function(data) {
            
            var select = jQuery('#pasta');
            
            select.empty();
            select.val('');

            select.append('<option value selected >Selecione</option>');
            
            for(var i = 0; i < data.length; i++) {
                select.append('<option value=' + data[i]['id'] + '>' + data[i]['nome'] + '</option>');
            }
            
            // span
            select.prev().text('Selecione');
        }, "json");
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
                            cropImage();
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
                extensions: 'jpg,png,jpeg,JPG,JPEG,PNG',
            }
        });
    })

    //jQuery("#pasta").prop("disabled", true);

    jQuery("#categoria").change(function() {
        var valor = jQuery(this).val();
        if (valor!="") {
            //jQuery("#pasta").prop("disabled", false);
            jQuery("#uniform-pasta").removeClass("disabled");
        }
        return false;
    });


    if (jQuery('#id').val()>0) {
        cropImage();
    }
});

function salvarCallback(data) {
    if (data.response == 1) {
        jQuery.jGrowl(data.success, {life: 4000, position: 'center', close: function() {
         location.href = "imagem/lista";
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

function getHtml(data)
{
    funcao = "javascript:removerArquivoTemp('" + data.temp_name + "','" + data.extensao + "')";
    return   '<ul class="imagelist" id="' + data.temp_name + '"><li><img style="height: auto; width: auto;" id="imgCrop" src="uploads/temp/' + data.temp_name + '.' + data.extensao + '"  />\n<a class="btn btn3 btn_trash" href="' + funcao + '"></a></li></ul>';
}

function mostraLink()
{
    if (jQuery("#inserirLink").is(":checked")) {
        jQuery(".link").fadeIn(500);
    } else {
        jQuery(".link").fadeOut(500);
    }
}

function abrirPastas()
{
    var link = baseUrl();
    jQuery.colorbox({href: link + "imagemPasta/pastasColorbox", width: "75%", height: "90%", onClosed: carregaPastasSelect});
}

function carregaPastasSelect()
{
    jQuery("#pasta").html(new Option("Carregando"));

    jQuery.post(baseUrl() + "imagemPasta/getPasta/" + jQuery('#categoria').val(), function(data) {
        jQuery("#pasta").html(new Option("Selecione", ""));
        jQuery.each(data, function(index, value) {
            jQuery("#pasta").append(new Option(value.label, value.id));
        });
    }, "json");

}

//Crop Image
function cropImage(){
        // Create variables (in this scope) to hold the API and image size
        var jcrop_api, boundx, boundy;
        jQuery('#imgCrop').Jcrop({

            onChange:   showCoords,
            onSelect:   showCoords,
            onRelease:  clearCoords,
            allowSelect: true,
        },function(){
            // Use the API to get the real image size
            var bounds = this.getBounds();
            boundx = bounds[0];
            boundy = bounds[1];
        });
}

function showCoords(c){
    jQuery('#x1').val(c.x);
    jQuery('#y1').val(c.y);
    jQuery('#x2').val(c.x2);
    jQuery('#y2').val(c.y2);
    jQuery('#w').val(c.w);
    jQuery('#h').val(c.h);
}

function clearCoords(){
    jQuery('#x1, #y1, #x2, #y2, #w, #h').val('');
}
