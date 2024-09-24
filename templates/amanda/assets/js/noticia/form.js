/**
 * O método da validação "tagsMaxMin" pode ser encontrado
 * no arquivo templates/assets/amanda/plugins/validate/additional-methods.js
 */

 var checkboxes = [];
 jQuery(document).ready(function () {
    jQuery('input:checkbox, input:radio, input:file, select').uniform();

    jQuery("#frm").submit(function(e){
        e.preventDefault();
        return false;
    });

    jQuery.validator.addMethod('timeorblank', function(value, element, param) {

            // quando não tem valor digitado, vem a mascará __:__
            value = value.replace(/\D/g, '');
            
            if(value) {

                if(value.length < 4) return false;

                var hour = parseInt(value[0] + value[1]);
                var min = parseInt(value[2] + value[3]);
                
                if(hour > 23 || min > 59) return false;
            }
            return true;
        }, 
        'Hora inválida'
        );
    // Prepara validações de campos 
    jQuery("#frm").validate({
        rules: {
            titulo: {required: true},
            conteudo: {
                required: function() {

                    // CKEditor não instância no mobile
                    if(CKEDITOR.instances.conteudo) {

                        CKEDITOR.instances.conteudo.updateElement();
                    }
                }
            },
            'sites[]': {required: true, minlength: 1},
            dataInicial: {required: true},
            horaInicial: {required: true, time: true},
            horaFinal: {timeorblank: true},
            palavrasChave: {required: true, tagsMaxMin: true}
        },
        messages: {
            palavrasChave: {
                tagsMaxMin: "Este campo deve ter entre 3 e 6 tags."
            }
        },
        submitHandler: function (form) {
            if(CKEDITOR.instances.conteudo) {
                jQuery('#conteudo').val(CKEDITOR.instances.conteudo.getData());
            }
            var formElement = jQuery(form);
            var url = formElement.attr('action');
            jQuery('button').fadeOut(20, function () {
                jQuery('#submitloading').fadeIn(20)
            });
            // console.log(formElement.serialize());
            jQuery.post(url, formElement.serialize(), salvarCallback, "json");
            return false;
        },
        errorPlacement: function (error, element) {
            if (element.attr("name") == "imagemBanco") {
                jQuery('#imgSelecionadas').parent().append(error);
            } else {
                error.insertAfter(element);
            }

        }
    });

    var position = window.location.href.lastIndexOf("/");
    var id = window.location.href.slice(position + 1);
    //var ckeditor = CKEDITOR.instances;
    if(!isNaN(id)) {
        var bt = jQuery(".break button").attr("disabled", true);
        jQuery.ajax({
            url: 'noticia/validaSubsiteVinculadoNoticia',
            type: 'post',
            dataType: 'json',
            data: {id: id},

            success: function (data) {
                bt.attr("disabled", false);
                if (data.permissao == false) {
                    jQuery("button").addClass("compartilhar").text("Compartilhar nos sites selecionados");
                    jQuery(".delete").click(false);
                    jQuery(".field .btn_no_icon").click(false);
                    jQuery("input, textarea").off().attr("readonly", true);
                    //ckeditor.conteudo.setReadOnly(true);
                    jQuery("#frm").attr("action", "noticia/compartilhar");
                    jQuery("#sites").rules("remove", "required");
                    jQuery(".btn_trash").click(false);
                }
            }
        });
    }

    //Validação de periodo
    jQuery(".periodo").change(function () {
        validaPeriodo('dataInicial', 'dataFinal', 'horaInicial', 'horaFinal');
    });

    jQuery( "body" ).on( "click", ".marcar", function() {
        var max = 3;

        if(this.checked){
            checkboxes.push(this);
        }else{
            for(var value in checkboxes){
                if(checkboxes[value].value == this.value){
                    checkboxes.splice(value, 1);
                }
            }
        }

        jQuery('.marcar').change(function(){
            var current = checkboxes.length;
            jQuery('.marcar').filter(':not(:checked)').prop('disabled', current >= max);
        });
    });
    jQuery(".galSelecionadas").sortable({
        update: function(){
            atualizaOrdem();
        }
    });
});


function salvarCallback(data) {

    if (data.response == 1) {
        jQuery.jGrowl(data.success, {
            life: 4000,
            position: 'center',
            close: function () {
                location.href = "noticia/lista";
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

function setarImagemRadio()
{
    var v = jQuery("input[name='imagem']:checked").val();

    if (v !== "" && v !== undefined) {
        jQuery('#imagemBanco').val(v);

        jQuery.get("noticia/getHtmlImagens/" + v).done(function (data) {
            jQuery('#imgSelecionadas').html(data);
        });
    }
}

function setarGaleriaCheckbox()
{
    v = new Array();
    jQuery(checkboxes).each(function() {
        v.push(jQuery(this).val());
    });
    if (v.length>3) {
        jAlert("Máximo de 3 galerias por notícia", "Aviso");
        return "erro";
    } else {

        if (v.length > 0) {
            jQuery('#galeriaBanco').val(v);
            jQuery.get("fototeca/getHtmlGaleriasNoticias/" + v).done(function(data) {

                jQuery('#galSelecionadas').html(data);
                jQuery('input:checkbox, input:radio, select').uniform();

                
                // jQuery(".imagelist").sortable({
                //  update: function (event, ui) {
                //      atualizaOrdem();
                //     }
                // });
                //
                jQuery(".galSelecionadas").sortable({
                    update: function(event, ui){
                        atualizaOrdem();
                    }
                });
                atualizaOrdem();
            });
        } else {
            jQuery('#galeriaBanco').val('');
            jQuery('#galSelecionadas').html('');
        }
        return 0;
    } // fecha else v.length>3

}


function excluirImagem(id)
{
    jQuery('#imagemBanco').val("");
    jQuery('#img' + id).remove();
}


function excluirGaleria(id)
{   
    for(var value in checkboxes){
        if(checkboxes[value].value == id){
            checkboxes.splice(value, 1);
        }
    }

    var ids = jQuery('#galeriaBanco').val();
    ids = ids.replace(id + ',', '');
    ids = ids.replace(',' + id, '');
    ids = ids.replace(id, '');

    jQuery('#galeria' + id).remove();
    jQuery('#galeriaBanco').val(ids);

    var count = jQuery('.galSelecionadas h4').length;

    if (count == 1 ) {
        jQuery('.radio .disabled').removeClass('disabled');
        jQuery('input').prop('disabled', false);
    }

}

function atualizaOrdem()
{
    var s = '';
    jQuery('.galSelecionadas .photo').each(function(i, e){
        if(s)
            s += ',';
        s += jQuery(e).data('id');
    });
    jQuery('#galeriaOrdem').val(s);
}
