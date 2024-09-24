jQuery(document).ready(function() {
    
    var position = window.location.href.lastIndexOf("/");
    var id = window.location.href.slice(position + 1);
    
    if (id == "form") {
        carregaCategoriasSelect();
        carregaStatusSelect();
    }

    jQuery('input:checkbox, input:radio, input:file, select').uniform();

    // prepara validações de campos
    jQuery("#frm").validate({
        rules: {
            nome: {required: true},
            categoria: {required: true},
            status: {required: true},
            dataInicial: {required: true},
            horaInicial: {required: true, time: true },
            horaFinal: { time: true },
            "sites[]": {required: true, minlength: 1},
            conteudo: {
                required: function(textarea) {
                    if(CKEDITOR.instances.conteudo) {
                        CKEDITOR.instances.conteudo.updateElement(); 
                    }
                    var editorcontent = textarea.value.replace(/<[^>]*>/gi, ''); 
                    return editorcontent.length === 0;
                }
            }
        },
        messages: {
            nome: {required: "Este campo é requerido"},
            categoria: {required: "Este campo é requerido"},
            status: {required: "Este campo é requerido"},
            "sites[]": {required: "Este campo é requerido"},
            conteudo: {required: "Este campo é requerido"}
        },
        errorPlacement: function(error, element) {
            if (element.attr("name") == "status" || element.attr("name") == "categoria") {
                element.parent().parent().append(error);
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: function(form) {
            
            if(CKEDITOR.instances.conteudo) jQuery('#conteudo').val(CKEDITOR.instances.conteudo.getData());
            if(CKEDITOR.instances.arquivo) jQuery('#arquivo').val(CKEDITOR.instances.arquivo.getData());
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
    
    //var ckeditor = CKEDITOR.instances;
    if(!isNaN(id)) {
        var bt = jQuery(".break button").attr("disabled", true);
        jQuery.ajax({
            url: 'edital/validaSubsiteVinculadoEdital',
            type: 'post',
            dataType: 'json',
            data: {id: id},

            success: function (data) {
                bt.attr("disabled", false);
                if (data.permissao == false) {
                    jQuery("button").addClass("compartilhar").text("Compartilhar nos sites selecionados");
                    jQuery(".field input").off().attr("readonly", "readonly");
                    jQuery("#categoria").attr("disabled", true);
                    jQuery("#status").attr("disabled", true);
                    jQuery(".btn").off();
                    jQuery("#uniform-categoria ~ .btn").click(false);
                    jQuery("#uniform-status ~ .btn").click(false);
                    //ckeditor.conteudo.setReadOnly(true);
                    //ckeditor.arquivo.setReadOnly(true);
                    jQuery('#estado').css({"display":"none"});
                    jQuery("#frm").attr("action", "edital/compartilhar");
                    jQuery("#sites").rules("remove", "required");
                }
            }
        });
    }


});


function salvarCallback(data) {
    if (data.response == 1) {
        jQuery.jGrowl(data.success, {life: 4000, position: 'center', close: function() {
            location.href = "edital/lista";
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


function abrirCategorias()
{
    var link = baseUrl();
    jQuery.colorbox({href: link + "edital/categorias", width: "75%", height: "90%", onComplete: carregaCategorias, onClosed: carregaCategoriasSelect});
}
function abrirStatus()
{
    var link = baseUrl();
    jQuery.colorbox({href: link + "edital/status", width: "75%", height: "90%", onComplete: carregaStatus, onClosed: carregaStatusSelect});
}

function carregaCategoriasSelect()
{
    jQuery("#categoria").html(new Option("Carregando"));

    jQuery.post(baseUrl() + "edital/getCategorias", function(data) {
        jQuery("#categoria").html(new Option("Selecione", ""));
        jQuery.each(data, function(index, value) {
            jQuery("#categoria").append(new Option(value.label, value.id));
        });
    }, "json");

}

function carregaStatusSelect()
{
    jQuery("#status").html(new Option("Carregando"));

    jQuery.post(baseUrl() + "edital/getStatus", function(data) {
        jQuery("#status").html(new Option("Selecione", ""));
        jQuery.each(data, function(index, value) {
            jQuery("#status").append(new Option(value.label, value.id));
        });
    }, "json");

}
