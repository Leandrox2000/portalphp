var table;
jQuery(document).ready(function() {

    table = jQuery('#paginas').dataTable({
        "sPaginationType": "full_numbers",
        "bSort": false,
        "fnDrawCallback": function(oSettings) {
            jQuery('input:checkbox, select').uniform();
        },
        "bProcessing": true,
        "bServerSide": true,
        "sAjaxSource": "paginaEstatica/pagination",
        "fnServerParams": function(aoData) {
            aoData.push({"name": "data_inicial", "value": jQuery("#data_inicial").val()});
            aoData.push({"name": "data_final", "value": jQuery("#data_final").val()});
            aoData.push({"name": "status", "value": jQuery("#status").val()});
            aoData.push({"name": "site", "value": jQuery("#site").val()});

        }
    });

    jQuery("#status").change(function() {
        table.fnDraw();
    });

    jQuery("#site").change(function() {
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


});


//Callbakc das açoes em lote
function callBackAcoes(data)
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
    var ch = jQuery('#paginas').find('tbody input[type=checkbox]');     //get each checkbox in a table

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
                jQuery.post("paginaEstatica/delete", {sel: sel}, callBackAcoes, "json");
            }
        });
    } else {
        jAlert("Nenhum registro selecionado", "Aviso");
    }
}


//function alterarStatus(status) {
//    var sel = getCheckboxMarcados("paginas");
//    msg = "Deseja realmente executar esta ação? ";
//
//    if (sel.length > 0) {
//        jConfirm(msg, 'Confirmar Alteração',
//                function(response) {
//                    if (response) {
//                        jQuery.post("paginaEstatica/alterarStatus", {sel: sel, status: status}, callBackAcoes, "json");
//
//                    }
//                }
//        );
//    } else {
//        jAlert("Nenhum registro selecionado", "Aviso");
//    }
//}

function alterarStatus(status){
    var sel = getCheckboxMarcados("paginas");
    msg = "Deseja realmente executar esta ação? ";
    var table = "pagina_estatica";
    var entity = "EntityPaginaEstatica".replace("Entity" , "Entity\\");
    
    if (sel.length > 0) {
        jConfirm(msg, 'Confirmar Alteração',
                function(response) {
                    if (response) {
                        jQuery.ajax({
                            url: 'paginaEstatica/alterarStatusValidacao',
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

function visualizar(id) {
    var url = baseUrl() + "paginaEstatica/visualizar/" + id;
    var data = { route: '/pagina/detalhes/#id#/#hash#' };

    jQuery.post(url, data, function(data) {
        window.open(data['url'], '_blank');
    }, "json");
}

function callBackAjax(data)
{
    if (data.response == 1) {
        table.fnDraw();
        jQuery.jGrowl(data.success, {life: 4000, position: 'center'});
    } else {
        jQuery.each(data.error, function(index, value) {
            jQuery.jGrowl(value, {life: 4000, position: 'center'});
        });
    }
}



