var table;
jQuery(document).ready(function() {

    table = jQuery('#lcc').dataTable({
        "sPaginationType": "full_numbers",
        "bSort": false,
        "fnDrawCallback": function(oSettings) {
            jQuery('#lcc tbody input:checkbox').uniform();
        },
        "bProcessing": true,
        "bServerSide": true,
        "sAjaxSource": baseUrl() + "licitacaoConvenioContrato/pagination",
        "fnServerParams": function(aoData) {
            aoData.push({"name": "status", "value": jQuery("#status").val()});
            aoData.push({"name": "categoria", "value": jQuery("#categoria").val()});
            aoData.push({"name": "tipo", "value": jQuery("#tipo").val()});
            aoData.push({"name": "data_inicial", "value": jQuery("#data_inicial").val()});
            aoData.push({"name": "data_final", "value": jQuery("#data_final").val()});
        }
    });

    jQuery("#status").change(function() {
        table.fnDraw();
    });

    jQuery("#categoria").change(function() {
        table.fnDraw();
    });

    jQuery("#tipo").change(function() {
        table.fnDraw();
    });

    jQuery("#data_inicial").change(function() {
        table.fnDraw();
    });

    jQuery("#data_final").change(function() {
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
    } else {
        jQuery.each(data.error, function(index, value) {
            jQuery.jGrowl(value, {life: 4000, position: 'center'});
        });
    }
}



function excluir()
{
    var sel = getCheckboxMarcados("lcc");

    if (sel.length > 0) {
        jConfirm("Deseja realmente executar esta ação?", 'Confirmar Exclusão', function(response) {
            if (response) {
                jQuery.post("licitacaoConvenioContrato/delete", {sel: sel}, callBackAjax, "json");
            }
        }
        );
    } else {
        jAlert("Nenhum registro selecionado", "Aviso");
    }
}

function alterarStatus(status){
    var sel = getCheckboxMarcados("lcc");
    msg = "Deseja realmente executar esta ação? ";
    
    if (sel.length > 0) {
        jConfirm(msg, 'Confirmar Alteração',
                function(response) {
                    if (response) {
                        jQuery.post("licitacaoConvenioContrato/alterarStatus", {sel: sel, status: status}, callBackAjax, "json");
                    }
                }
        );
    } else {
        jAlert("Nenhum registro selecionado", "Aviso");
    }
}
