jQuery(document).ready(function() {

    jQuery('input:checkbox, input:radio, select').uniform();

    //Validação de periodo 
    jQuery(".periodo").change(function() {
        validaPeriodo('data_inicial', 'data_final', 'hora_inicial', 'hora_final');

    });

    // prepara validações de campos
    jQuery("#frm").validate({
        rules: {
            titulo: {required: true},
            imagemBanco: {required: true},
            data_inicial: {required: true},
            hora_inicial: {required: true, time: true },
            hora_final: { time: true },
            "sites[]": {required: true, minlength: 1},
        },
        messages: {
            titulo: {required: "Este campo é requerido"},
            imagemBanco: {required: "Este campo é requerido"},
            hora_inicial: {required: "Este campo é requerido"},
            data_inicial: {required: "Este campo é requerido"},
             "sites[]": {required: "Este campo é requerido"}
        },
        errorPlacement: function(error, element) {
            if (element.attr("name") == "imagemBanco") {
                jQuery('#imgSelecionadas').append(error);
            }

        },
        submitHandler: function(form) {
            if(CKEDITOR.instances.descricao) {
                jQuery('#descricao').val(CKEDITOR.instances.descricao.getData());
            }
            var t = jQuery(form);
            var url = t.attr('action');
            jQuery('button').fadeOut(20, function() {
                jQuery('#submitloading').fadeIn(20)
            });
            jQuery.post(url, t.serialize(), salvarCallback, "json");
            return false;
        }
    });

    jQuery(".imagelist").sortable({
    	update: function(){
    		atualizaOrdem();
	    }
    });

    atualizaOrdem();
    
});

function salvarCallback(data)
{
    if (data.response == 1) {
        jQuery.jGrowl(
    		data.success, {
    			life: 4000, 
    			position: 'center', 
    			close: function() {
    				location.href = "galeria/lista";
    			}
    		}
		);
    } else {
        jQuery('#submitloading').fadeOut(20, function() {
            jQuery('button').fadeIn(20);
        });
        jQuery.each(data.error, function(index, value) {
            jQuery.jGrowl(value, {life: 4000, position: 'center'});
        });
    }
}

function setarImagemCheckbox()
{
    var v = jQuery('#idsImagens').val(); 
    v = v.substring(0,(v.length - 1));
    
    jQuery('#imagemBanco').val(v);
    
    var v = v.split(',');
    var ordemAtual = jQuery('#imagemOrdem').val();
    
    if(ordemAtual !== ""){
        ordemAtual = ordemAtual.split(',');
        
    }else{
        ordemAtual = new Array();
    }
    
    var novosIds = new Array();
    
    for(var i = 0; i < v.length; i++){
        if(ordemAtual.indexOf(v[i]) == -1){
            novosIds.push(v[i]);
        }
        
    }
    
    var idsExistentes = new Array();
    
    for(var i = 0; i < ordemAtual.length; i++){
        if(v.indexOf(ordemAtual[i]) !== -1){
            idsExistentes.push(ordemAtual[i]);
        }
    }
    
       
    var ids = idsExistentes.concat(novosIds);
    
    jQuery.get("galeria/getHtmlImagens/" + ids).done(
        function(data) {
            jQuery('#imgSelecionadas').html(data);

            jQuery(".imagelist").sortable({
            	update: function (event, ui) {
            		atualizaOrdem();
        	    }
            });
            atualizaOrdem();
        }
    );
}


function excluirImagem(id)
{
    var ids = jQuery('#imagemBanco').val();
    ids = ids.replace(id + ',', '');
    ids = ids.replace(',' + id, '');
    ids = ids.replace(id, '');

    jQuery('#img' + id).remove();
    jQuery('#imagemBanco').val(ids);
    
    var ids = jQuery('#imagemOrdem').val();
    ids = ids.replace(id + ',', '');
    ids = ids.replace(',' + id, '');
    ids = ids.replace(id, '');
    jQuery('#imagemOrdem').val(ids);
}

function atualizaOrdem()
{
	var s = '';
	jQuery('.imagelist li').each(function(i, e){
		if(s)
			s += ',';
		s += jQuery(e).attr('name');
	});
	jQuery('#imagemOrdem').val(s);
}

var position = window.location.href.lastIndexOf("/");
var id = window.location.href.slice(position + 1);
//var ckeditor = CKEDITOR.instances;
if(!isNaN(id)) {
    var bt = jQuery(".break button").attr("disabled", true);
    jQuery.ajax({
        url: 'galeria/validaSubsiteVinculadoGaleria',
        type: 'post',
        dataType: 'json',
        data: {id: id},

        success: function (data) {
            bt.attr("disabled", false);
            if (data.permissao == false) {
                jQuery("button").addClass("compartilhar").text("Compartilhar nos sites selecionados");
                jQuery(".delete").click(false);
                jQuery(".field > .btn_no_icon").click(false);
                jQuery(".field input").off().attr("readonly", true);
                //ckeditor.descricao.setReadOnly(true);
                jQuery('#estado').css({"display":"none"});
                jQuery("#frm").attr("action", "galeria/compartilhar");
                jQuery("#sites").rules("remove", "required");
            }
        }
    });
}
