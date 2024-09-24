var table;
jQuery(document).ready(function() {

    table = jQuery('#video').dataTable({
        "sPaginationType": "full_numbers",
        "bSort": false,
        "fnDrawCallback": function(oSettings) {
            jQuery('input:checkbox,input:radio').uniform();
        },
        "bProcessing": true,
        "bServerSide": true,
        "sAjaxSource": "video/pagination",
        "fnServerParams": function(aoData) {
            aoData.push({"name": "status", "value": jQuery("#status").val()});
            aoData.push({"name": "site", "value": jQuery("#site").val()});
            aoData.push({"name": "data_inicial", "value": jQuery("#data_inicial").val()});
            aoData.push({"name": "data_final", "value": jQuery("#data_final").val()});
        }
    });

    jQuery("#status").change(function() {
        table.fnDraw();
    });

    jQuery("#site").change(function() {
        table.fnDraw();
    });

    jQuery("#data_inicial").change(function() {
        if (jQuery("#data_inicial").val() !== "")
            table.fnDraw();
    });

    jQuery("#data_final").change(function() {
        if (jQuery("#data_final").val() !== "")
            table.fnDraw();
    });


    jQuery('input:checkbox, input:radio, select').uniform();
    jQuery('.data').datepicker();


    // ação do botão Excluir
    jQuery('.deletebutton').click(function() {
        var count = 0;
        var sel = new Array();
        var ch = jQuery('#video').find('tbody input[type=checkbox]');     //get each checkbox in a table

        //check if there is/are selected row in table
        ch.each(function() {
            if (jQuery(this).parent().hasClass("checked")) {
                sel[count] = jQuery(this).val();
                count++;
            }
        });

        if (count > 0) {
            jConfirm('Deseja realmente excluir os registros selecionados?', 'Confirmar Exclusão', function(response) {
                if (response) {
                    jQuery.post("video/delete", {sel: sel}, callBackAcoes, "json");
                }
            });
        } else {
            jAlert("Nenhum registro selecionado", "Aviso");
        }
    });



  jQuery("#video tbody").sortable({
        update: function(){
            return verificaFiltros();
        },
        helper: function(e, tr){
          var originals = tr.children();
          var helper = tr.clone();
          helper.children().each(function(index)
          {
            jQuery(this).width(originals.eq(index).width());
          });
          return helper;
        }
    }).disableSelection();




});


function salvarPosicionamentoVideo()
{
    if(verificaFiltros()){
        jConfirm("Deseja realmente executar esta ação?", 'Confirmar Posição', function(response) {
            if (!response) {
                return false;
            }
            var ordenacao = [];
            var total = jQuery('[name="video_length"] option:selected').text();
            if(total == 'Todos')
                total = 10;
            var ordenacaoBase = (parseInt(jQuery('.paginate_active').text()) - 1) * parseInt(total);
            jQuery('#video tbody .checker input').each(function(i){
                ordenacao.push({
                    id: jQuery(this).val(),
                    ordenacao: (i + 1 + ordenacaoBase)
                });
            });
            jQuery.ajax('video/ajaxAtualizarOrdenacaoVideo', {
                type: 'POST',
                dataType: 'json',
                data: {
                    ordenacao: ordenacao,
                    site: jQuery("#site").val()
                },
                success: function(data) {
	                    jQuery.jGrowl(data.success, {
	                        life: 4000,
	                        position: 'center'
	                    });
	                    table.fnDraw();
                }
            });
        });
    }
}

function verificaFiltros(){
    if(jQuery("#site option:selected").val() == ""){
        jAlert('Os campos para alterar/salvar posicionamentos só ficarão habilitadas quando o filtro "Site" estiver selecionado', "Aviso");
        return false;
    }
    return true;
}




//Callbakc das açoes em lote
function callBackAcoes(data) {
    if (data.response == 1) {
        table.fnDraw(false);
        jQuery.jGrowl(data.success, {life: 4000, position: 'center'});
    } else {
        jQuery.each(data.error, function(index, value) {
            jQuery.jGrowl(value, {life: 4000, position: 'center'});
        });
    }
    
//    if (data.publicados) {
//        alterarStatus(0, true, data.publicados);
//    }

}

//function alterarStatus(status){
//    var sel = getCheckboxMarcados("video");
//    msg = "Deseja realmente executar esta ação? ";
//    
//    if (sel.length > 0) {
//        jConfirm(msg, 'Confirmar Alteração',
//                function(response) {
//                    if (response) {
//                        jQuery.post("video/alterarStatus", {sel: sel, status: status}, callBackAcoes, "json");
//                    }
//                }
//        );
//    } else {
//        jAlert("Nenhum registro selecionado", "Aviso");
//    }
//}


function alterarStatus(status, exclusao, selecionados){
    
    if (exclusao) {
        var sel = selecionados;
    } else {
        var sel = getCheckboxMarcados("video");
    }
    msg = "Deseja realmente executar esta ação? ";
    var table = "video";
    var entity = "EntityVideo".replace("Entity" , "Entity\\");

    
    if (sel.length > 0) {
        jConfirm(msg, 'Confirmar Alteração',
                function(response) {
                    if (response) {
                        jQuery.ajax({
                            url: 'video/alterarStatusValidacao',
                            type: 'post',
                            dataType: 'json',
                            data: {sel: sel, status: status, table: table, entity: entity},
                            success: function(data) {
                                callBackAjax(data, exclusao);
                            }
                        });
//                        jQuery.post,("agenda/alterarStatus", {sel: sel, status: status}, callBackAjax, "json");
                    }
                }
        );
    } else {
        jAlert("Nenhum registro selecionado", "Aviso");
    }
}


//Callbakc do botão Excluir
function callBackAjax(data, msg)
{
    if (data.response == 1) {
        table.fnDraw(false);
        if (msg != true) {
            jQuery.jGrowl(data.success, {life: 4000, position: 'center'});
        }
    } else {
        jQuery.each(data.error, function(index, value) {
            jQuery.jGrowl(value, {life: 4000, position: 'center'});
        });
    }
}