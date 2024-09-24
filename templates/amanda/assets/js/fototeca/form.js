jQuery(document).ready(function() {

    jQuery('input:checkbox, input:radio, select').uniform();

    //Validação de periodo
    jQuery(".periodo").change(function() {
        validaPeriodo('data_inicial', 'data_final', 'hora_inicial', 'hora_final');

    });

    // prepara validações de campos
    jQuery("#frm").validate({
        rules: {
            nome: {required: true},
            imagemBanco: {required: true},
            data_inicial: {required: true},
            hora_inicial: {required: true, time: true},
            hora_final: { time: true },
            galeriaBanco: {required: true},
            categoria: {required: true},
        },
        messages: {
            nome: {required: "Este campo é requerido"},
            imagemBanco: {required: "Este campo é requerido"},
            data_inicial: {required: "Este campo é requerido"},
            hora_inicial: {required: "Este campo é requerido"},
            galeriaBanco: {required: "Este campo é requerido"},
        },
        errorPlacement: function(error, element) {
            if (element.attr("name") == "categoria") {
                element.parent().parent().append(error);

            } else if (element.attr("name") == "galeriaBanco") {
                jQuery('#galSelecionadas').append(error);
            }
            else {
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

    jQuery(".imagelist").sortable({
    	update: function(){
    		atualizaOrdem();
	    }
    });

    atualizaOrdem();
    
});

function abrirCategorias()
{
    var link = baseUrl();
    jQuery.colorbox({href: link + "fototeca/categorias", width: "75%", height: "90%", onComplete: carregaCategorias, onClosed: carregaCategoriasSelect});
}

function carregaCategoriasSelect()
{
    jQuery.uniform.update('#categoria');
    jQuery("#categoria").html(new Option("Carregando"));

    jQuery.post(baseUrl() + "fototeca/getCategorias", function(data) {
        jQuery("#categoria").html(new Option("Selecione", ""));
        jQuery.each(data, function(index, value) {
            jQuery("#categoria").append(new Option(value.label, value.id));
        });
    }, "json");

}

function salvarCallback(data)
{
    if (data.response == 1) {
        jQuery.jGrowl(data.success, {life: 4000, position: 'center', close: function() {
            location.href = "fototeca/lista";
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

function setarImagemRadio()
{
    var v = jQuery("input[name='imagem']:checked").val();
    jQuery('#imagemBanco').val(v);

    jQuery.get("publicacao/getHtmlImagens/" + v).done(
            function(data) {
                jQuery('#imgSelecionadas').html(data);
            });

}

function excluirImagem(id)
{
    var ids = jQuery('#imagemBanco').val();
    ids = ids.replace(id + ',', '');
    ids = ids.replace(',' + id, '');
    ids = ids.replace(id, '');

    jQuery('#img' + id).remove();
    jQuery('#imagemBanco').val(ids)

}

function setarGaleriaCheckbox()
{
    var galeriasSelecionadas = jQuery('#idsGalerias').val();
    v = new Array();
    v = galeriasSelecionadas.split(',');
    
    
    if(v.length > 0 && v[v.length - 1] == ""){
        v.pop();
        
    }
    
    
    var ordemAtual = jQuery('#galeriaOrdem').val();
    
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
    
       
    
    v = idsExistentes.concat(novosIds);
        
    
    if (v.length > 0) {
        jQuery('#galeriaBanco').val(v);        
        jQuery.get("fototeca/getHtmlGalerias/" + v).done(function(data) {
            jQuery('#galSelecionadas').html(data);
            jQuery('input:checkbox, input:radio, select').uniform();

            jQuery(".imagelist").sortable({
            	update: function (event, ui) {
            		atualizaOrdem();
        	    }
            });
            atualizaOrdem();
        });
    } else {
        jQuery('#galeriaBanco').val('');
        jQuery('#galSelecionadas').html('');
    }


}

function excluirGaleria(id)
{
    var ids = jQuery('#galeriaBanco').val();
    ids = ids.replace(id + ',', '');
    ids = ids.replace(',' + id, '');
    ids = ids.replace(id, '');

    jQuery('#galeria' + id).remove();
    jQuery('#galeriaBanco').val(ids);

}

function atualizaOrdem()
{
	var s = '';
	jQuery('.imagelist li').each(function(i, e){
		var v = jQuery(e).attr('name');
		if(typeof(v) != 'undefined'){
			if(s)
				s += ',';
			s += v;			
		}
	});
	jQuery('#galeriaOrdem').val(s);
}