jQuery(document).ready(function() {
    jQuery('#demo1_menu').easytree();
    
    jQuery(".menuRelacional").click(function() {
        jQuery('#idMenuRelacionado').val(jQuery(this).attr("id"));
    });
    
    if(jQuery('#idMenuRelacionado').val() != ""){
        
        var elemento =  jQuery("#"+jQuery('#idMenuRelacionado').val());
        elemento.parents('ul').css("display","block");
        //elemento.parents('span').addClass("paçoca");
        //elemento.parents('span').addClass("easytree-exp-e");
        //elemento.parents('span').removeClass("easytree-exp-c");
        
        
        jQuery("#"+jQuery('#idMenuRelacionado').val()).css("background-color","#F1F6C5");
        jQuery("#"+jQuery('#idMenuRelacionado').val()).css("border-color","#26A0DA");
        
        /*jQuery("#"+jQuery('#idMenuRelacionado').val()).parent().removeClass("easytree-exp-c").addClass("easytree-exp-e");
        jQuery("#"+jQuery('#idMenuRelacionado').val()).parent().parent().removeClass("easytree-exp-c").addClass("easytree-exp-e");
        jQuery("#"+jQuery('#idMenuRelacionado').val()).parent().parent().parent().removeClass("easytree-exp-c").addClass("easytree-exp-e");*/
        //jQuery("#"+jQuery('#idMenuRelacionado').val()).parent().parent().css("display","block");
    }
    
    jQuery(".periodo").change(function() {
        validaPeriodo('data_inicial', 'data_final', 'hora_inicial', 'hora_final');

    });
    
    jQuery('input:checkbox, input:radio, select').uniform();

    //Validação de periodo
    jQuery(".periodo").change(function() {
        validaPeriodo('data_inicial', 'data_final', 'hora_inicial', 'hora_final');

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

    // prepara validações de campos
    jQuery("#frm").validate({
        rules: {
            titulo: {required: true},
            data_inicial: {required: true},
            hora_inicial: {required: true, time: true},
            hora_final: { timeorblank: true},
            "sites[]":      { required: true, minlength: 1 },
            palavrasChave:  { required: true, tagsMaxMin: true },
        },
        messages: {
            palavrasChave: {
                tagsMaxMin: "Este campo deve ter entre 3 e 6 tags."
            }
        },
        submitHandler: function(form) {
            if(CKEDITOR.instances.conteudo) jQuery('#conteudo').val(CKEDITOR.instances.conteudo.getData());

            var t = jQuery(form);
            var url = t.attr('action');
            jQuery('button').fadeOut(20, function() {
                jQuery('#submitloading').fadeIn(20)
            });
            jQuery.post(url, t.serialize(), salvarCallback, "json");
            return false;
        }
    });


});

var position = window.location.href.lastIndexOf("/");
var id = window.location.href.slice(position + 1);
if(!isNaN(id)) {
    var bt = jQuery(".break button").attr("disabled", true);
    jQuery.ajax({
        url: 'paginaEstatica/validaSubsiteVinculadoPaginaEstatica',
        type: 'post',
        dataType: 'json',
        data: {id: id},

        success: function (data) {
            bt.attr("disabled", false);
            if (data.permissao == false) {
                jQuery("button").addClass("compartilhar").text("Compartilhar nos sites selecionados");
                jQuery(".field, #palavrasChave, input").off().attr("readonly", "readonly");
                jQuery(".field .btn_no_icon").click(false);
                jQuery('#estado').css({"display":"none"});
                jQuery("#frm").attr("action", "paginaEstatica/compartilhar");
                jQuery("#sites").rules("remove", "required");
            }
        }
    });
}

function salvarCallback(data)
{
    if (data.response == 1) {
        jQuery.jGrowl(data.success, {life: 4000, position: 'center', close: function() {
            location.href = "paginaEstatica/lista"; 
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


function setarGaleriaCheckbox()
{
    v = new Array();
    
    var galerias = jQuery('#idsGalerias').val().split(',');
    for(var i in galerias){
        if(galerias[i].length != 0)
            v.push(galerias[i]);
    }

    jQuery('#galeriaBanco').val(v);
    
    jQuery.get("paginaEstatica/getHtmlGalerias/" + v).done(
            function(data) {
                jQuery('#galSelecionadas').html(data);
                jQuery('input:checkbox, input:radio, select').uniform();
            });


}


function excluirGaleria(id)
{
    var ids = jQuery('#galeriaBanco').val();
    ids = ids.replace(id + ',', '');
    ids = ids.replace(',' + id, '');
    ids = ids.replace(id, '');

    jQuery('#galeria' + id).remove();
    jQuery('#galeriaBanco').val(ids)

}


