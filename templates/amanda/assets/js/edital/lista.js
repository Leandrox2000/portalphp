var table;
jQuery(document).ready(function() {
    table = jQuery('#editais').dataTable({
        "sPaginationType": "full_numbers",
        "bSort": false,
        "fnDrawCallback": function(oSettings) {
            jQuery('#editais tbody input:checkbox').uniform();
        },
        "bProcessing": true,
        "bServerSide": true,
        "sAjaxSource": baseUrl() + "edital/pagination",
        "fnServerParams": function(aoData) {
            aoData.push({"name": "status", "value": jQuery("#status").val()});
            aoData.push({"name": "statusEdital", "value": jQuery("#statusEdital").val()});
            aoData.push({"name": "categoria", "value": jQuery("#categoriasFiltro").val()});
            aoData.push({"name": "site", "value": jQuery("#site").val()});
            aoData.push({"name": "data_inicial", "value": jQuery("#dataInicial").val()});
            aoData.push({"name": "data_final", "value": jQuery("#dataFinal").val()});
        }
    });

    jQuery("#categoriasFiltro, #status, #statusEdital, #site").change(function() {
        table.fnDraw();
    });

    jQuery("#dataInicial").change(function() {
        if (jQuery("#dataInicial").val() !== "" && jQuery("#dataFinal").val()) {
            table.fnDraw();
        }
    });

    jQuery("#dataFinal").change(function() {
        if (jQuery("#dataInicial").val() !== "" && jQuery("#dataFinal").val()) {
            table.fnDraw();
        }
    });


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

}


function excluir()
{
    var sel = getCheckboxMarcados("editais");

    if (sel.length > 0) {
        jConfirm("Deseja realmente executar esta ação?", 'Confirmar Exclusão', function(response) {
            if (response) {
                jQuery.post("edital/delete", {sel: sel}, callBackAjax, "json");
            }
        }
        );
    } else {
        jAlert("Nenhum registro selecionado", "Aviso");
    }
}




//
//function alterarStatus(status){
//    var sel = getCheckboxMarcados("editais");
//    msg = "Deseja realmente executar esta ação? ";
//    
//    if (sel.length > 0) {
//        jConfirm(msg, 'Confirmar Alteração',
//                function(response) {
//                    if (response) {
//                        jQuery.post("edital/alterarStatus", {sel: sel, status: status}, callBackAjax, "json");
//                    }
//                }
//        );
//    } else {
//        jAlert("Nenhum registro selecionado", "Aviso");
//    }
//}
function alterarStatus(status){
    var sel = getCheckboxMarcados("editais");
    msg = "Deseja realmente executar esta ação? ";
    var table = "edital";
    var entity = "EntityEdital".replace("Entity" , "Entity\\");
    
    if (sel.length > 0) {
        jConfirm(msg, 'Confirmar Alteração',
                function(response) {
                    if (response) {
                        jQuery.ajax({
                            url: 'edital/alterarStatusValidacao',
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