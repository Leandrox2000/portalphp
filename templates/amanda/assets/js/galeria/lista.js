var table;
jQuery(document).ready(function() {

    table = jQuery('#galerias').dataTable({
        "sPaginationType": "full_numbers", 
        "bSort": false,
        "fnDrawCallback": function(oSettings) {
            jQuery('input:checkbox, input:radio, select').uniform();
        },
        "bProcessing": true,
        "bServerSide": true,
        "sAjaxSource": "galeria/pagination",
        "fnServerParams": function(aoData) {
            aoData.push({"name": "data_inicial", "value": jQuery("#data_inicial").val()});
            aoData.push({"name": "data_final", "value": jQuery("#data_final").val()});
            aoData.push({"name": "status", "value": jQuery("#status").val()});
            aoData.push({"name": "site", "value": jQuery("#site").val()});
        }
    });

    jQuery("#site").change(function() {
        table.fnDraw();
    });
    
    jQuery("#status").change(function() {
        table.fnDraw();
    });


    jQuery("#data_inicial").change(function() {
        if (jQuery("#data_inicial").val() !== "" && jQuery("#data_final").val())
            table.fnDraw();
    });

    jQuery("#data_final").change(function() {
        if (jQuery("#data_inicial").val() !== "" && jQuery("#data_final").val())
            table.fnDraw();
    });


    jQuery('input:checkbox, input:radio, select').uniform();
    jQuery('.data').datepicker();

    jQuery("#galerias tbody").sortable({
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


function salvarPosicionamentoGaleria()
{
    if(verificaFiltros()){
        jConfirm("Deseja realmente executar esta ação?", 'Confirmar Posição', function(response) {
            if (!response) {
                return false;
            }
            var ordenacao = [];
            var total = jQuery('[name="galerias_length"] option:selected').text();
            if(total == 'Todos')
                total = 10;
            var ordenacaoBase = (parseInt(jQuery('.paginate_active').text()) - 1) * parseInt(total);
            jQuery('#galerias tbody .checker input').each(function(i){
                ordenacao.push({
                    id: jQuery(this).val(),
                    ordenacao: (i + 1 + ordenacaoBase)
                });
            });
            jQuery.ajax('galeria/ajaxAtualizarOrdenacaoGaleria', {
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


//Callbakc das açoes em lote
function callBackAcoes(data)
{
    if (data.response == 1) {
        table.fnDraw(false);
        jQuery.jGrowl(data.success, {life: 4000, position: 'center'});
    } else if(data.response == 2){
        table.fnDraw(false);
    	jAlert(
            data.error, 
            'Status da Exclusão!'
        );
    } else {
        jQuery.each(data.error, function(index, value) {
            jQuery.jGrowl(value, {life: 4000, position: 'center'});
        });
    }
}



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


function excluir() {
    var count = 0;
    var sel = new Array();
    var ch = jQuery('#galerias').find('tbody input[type=checkbox]');     //get each checkbox in a table

    //check if there is/are selected row in table
    ch.each(function() {
        if (jQuery(this).parent().hasClass("checked")) {
            sel[count] = jQuery(this).val();
            count++;
        }
    });

    if (count > 0) {
        jConfirm("Deseja realmente executar esta ação?", 'Confirmar Exclusão', function(response) {
            if (response) {
                jQuery.post("galeria/delete", {sel: sel}, callBackAcoes, "json");
            }
        });
    } else {
        jAlert("Nenhum registro selecionado", "Aviso");
    }
}

function verificaFiltros(){
    if(jQuery("#site option:selected").val() == ""){
        jAlert('Os campos para alterar/salvar posicionamentos só ficarão habilitadas quando o filtro "Site" estiver selecionado', "Aviso");
        return false;
    }
    return true;
}


//function alterarStatus(status){
//   var sel = getCheckboxMarcados("galerias");
//   msg = "Deseja realmente executar esta ação? ";
//   
//   if (sel.length > 0) {
//       jConfirm(msg, 'Confirmar Alteração',
//               function(response) {
//                   console.log(response);
//                   if (response) {
//                       jQuery.post("galeria/alterarStatusGaleria", {sel: sel, status: status}, callBackAcoes, "json");
//                   }
//               }
//       );
//   } else {
//       jAlert("Nenhum registro selecionado", "Aviso");
//   }
//}


function alterarStatus(status){
    var sel = getCheckboxMarcados("galerias");
    msg = "Deseja realmente executar esta ação? ";
    var table = "galeria";
    var entity = "EntityGaleria".replace("Entity" , "Entity\\");
    
    if (sel.length > 0) {
        jConfirm(msg, 'Confirmar Alteração',
                function(response) {
                    if (response) {
                        jQuery.ajax({
                            url: 'galeria/alterarStatusValidacao',
                            type: 'post',
                            dataType: 'json',
                            data: {sel: sel, status: status, table: table, entity: entity},
                            success: function(data) {
                                callBackAjax(data);
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
