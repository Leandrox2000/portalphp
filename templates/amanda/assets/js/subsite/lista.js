var table;
jQuery(document).ready(function() {

    table = jQuery('#subsite').dataTable({
        "sPaginationType": "full_numbers",
        "bSort": false,
        "fnDrawCallback": function(oSettings) {
            jQuery('#subsite tbody input:checkbox').uniform();
        },
        "bProcessing": true,
        "bServerSide": true,
        "sAjaxSource": baseUrl() + "subsite/pagination",
        "fnServerParams": function(aoData) {
            aoData.push({"name": "status", "value": jQuery("#status").val()});
            aoData.push({"name": "data_inicial", "value": jQuery("#data_inicial").val()});
            aoData.push({"name": "data_final", "value": jQuery("#data_final").val()});
        }
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



});


//Callbakc do botão Excluir
function callBackAjax(data)
{
    if (data.response == 1) {
        table.fnDraw(false);
        jQuery.jGrowl(data.success, {life: 4000, position: 'center'});
    } else if(data.response == 2){
    	jAlert(
            data.error, 
            'Status da Exclusão!'
        );
        table.fnDraw();
    } else {
        jQuery.each(data.error, function(index, value) {
            jQuery.jGrowl(value, {life: 4000, position: 'center'});
        });
    }
}

function alterarStatus(status){
    var sel = getCheckboxMarcados("subsite");
    msg = "Deseja realmente executar esta ação? ";
    
    if (sel.length > 0) {
        jConfirm(msg, 'Confirmar Alteração',
            function(response) {
                if (response) {
                    jQuery.post("subsite/alterarStatus", {sel: sel, status: status}, callBackAjax, "json");
                }
            }
        );
    } else {
        jAlert("Nenhum registro selecionado", "Aviso");
    }
}

function excluir()
{
    var sel = getCheckboxMarcados("subsite");

    if (sel.length > 0) {
        jConfirm("Deseja realmente executar esta ação? ", "Confirmar Exclusão",
                function(response) {
                    if (response) {
                        jQuery.post("subsite/delete", {sel: sel}, callBackAjax, "json");
                    }
                }
        );
    } else {
        jAlert("Nenhum registro selecionado", "Aviso");
    }
}


function visualizar(sigla) {
    jQuery.post(baseUrl() + "subsite/visualizar/" + sigla, function(data) {
        window.open(data['url'], '_blank');
    }, "json");
}

function visualizar(id) {
    var url = baseUrl() + "subsite/visualizar/" + id;
    var data = { route: '/?preview=subsite&id=#id#&hash=#hash#' };

    jQuery.post(url, data, function(data) {
        window.open(data['url'], '_blank');
    }, "json");
}
