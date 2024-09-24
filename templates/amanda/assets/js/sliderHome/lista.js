var table;
jQuery(document).ready(function() {

    table = jQuery('#sliderHome').dataTable({
        "sPaginationType": "full_numbers",
        "bSort": false,
        "fnDrawCallback": function(oSettings) {
            jQuery('#sliderHome tbody input:checkbox').uniform();
        },
        "bProcessing": true,
        "bServerSide": true,
        "sAjaxSource": baseUrl() + "sliderHome/pagination",
        "fnServerParams": function(aoData) {
            aoData.push({"name": "status", "value": jQuery("#status").val()});
            aoData.push({"name": "data_inicial", "value": jQuery("#data_inicial").val()});
            aoData.push({"name": "data_final", "value": jQuery("#data_final").val()});
            aoData.push({"name": "site", "value": jQuery("#site").val()});
        }
    });

   jQuery("#site").change(function() {
        table.fnDraw();
    });
    
    jQuery("#status").change(function() {
        table.fnDraw();
    });
//
//    jQuery("#data_inicial").change(function() {
//        if (validaData(jQuery("#data_inicial").val())) {
//            table.fnDraw();
//        } else {
//            jAlert("Data inválida")
//            jQuery("#data_inicial").val("");
//        }
//    });
//
//    jQuery("#data_final").change(function() {
//        if (validaData(jQuery("#data_final").val())) {
//            table.fnDraw();
//        } else {
//            jAlert("Data inválida")
//            jQuery("#data_final").val("");
//        }
//    });

    jQuery('input:checkbox, input:radio, select').uniform();

});


//Callbakc do botão Excluir
function callBackAjax(data)
{
    if (data.response == 1) {
        table.fnDraw(false);
        jQuery.jGrowl(data.success, {life: 4000, position: 'center'});
    } else {
        jQuery.each(data.error, function(index, value) {
            jQuery.jGrowl(value, {life: 4000, position: 'center'});
        });
    }
}

function alterarStatus(status){
    var sel = getCheckboxMarcados("sliderHome");
    msg = "Deseja realmente executar esta ação? ";
    
    if (sel.length > 0) {
        jConfirm(msg, 'Confirmar Alteração',
                function(response) {
                    if (response) {
                        jQuery.post("sliderHome/alterarStatus", {sel: sel, status: status}, callBackAjax, "json");
                    }
                }
        );
    } else {
        jAlert("Nenhum registro selecionado", "Aviso");
    }
}

function excluir()
{
    var sel = getCheckboxMarcados("sliderHome");

    if (sel.length > 0) {
        jConfirm("Deseja realmente executar esta ação? ", "Confirmar Exclusão",
                function(response) {
                    if (response) {
                        jQuery.post("sliderHome/delete", {sel: sel}, callBackAjax, "json");
                    }
                }
        );
    } else {
        jAlert("Nenhum registro selecionado", "Aviso");
    }
}

function visualizar(id) {
    var url = baseUrl() + "sliderHome/visualizar/" + id;
    var data = { route: '/?preview=slider-subsite&id=#id#&hash=#hash#' };

    jQuery.post(url, data, function(data) {
        window.open(data['url'], '_blank');
    }, "json");
}

function verificaFiltros(){
    if(jQuery("#site option:selected").val() == ""){
        jAlert('Para alterar o posicionamento filtre a pesquisa por Site!', "Aviso");
        return false;
    }
    return true;
}

function salvarPosicionamento()
{
    if(verificaFiltros()){
        jConfirm("Deseja realmente executar esta ação?", 'Confirmar Posição', function(response) {
            if (response) {
            	var ordenacao = [];
            	jQuery("[name='slideHomeOrdem']").each(function(i){
            		ordenacao.push({
            			id: jQuery(this).data('id'),
            			ordenacao: jQuery(this).val()
            		});
            	});
            	jQuery.ajax('sliderHome/ajaxAtualizarOrdenacao', {
            		type: 'POST',
            		dataType: 'json',
            		data: {
            			site: jQuery('#site option:selected').val(),
            			ordenacao: ordenacao
	            	},
	            	success: function(data) {
	            		if (data.resultado == 'ok') {
	            			jQuery.jGrowl('Ação executada com sucesso.', {
	            				life: 4000,
	            				position: 'center'
	            			});
	            			table.fnDraw();
	            		}
	            	}
            	});
            }
        });
    }
}
