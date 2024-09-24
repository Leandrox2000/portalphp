var table;
/**
 * Rota de base para o controller.
 */
var controllerRoute = 'backgroundHome';
/**
 * Name do checkbox utilizado para a seleção de registros.
 */
var multipleSelectionCheckboxName = 'backgroundHome';
/**
 * Elemento HTML que contém a tabela do CRUD.
 */
var tableElement = '#backgroundHome';

jQuery(document).ready(function() {

    table = jQuery(tableElement).dataTable({
        "sPaginationType": "full_numbers",
        "bSort": false,
        "fnDrawCallback": function(oSettings) {
            jQuery(tableElement).find('tbody input:checkbox').uniform();
        },
        "bProcessing": true,
        "bServerSide": true,
        "sAjaxSource": baseUrl() + controllerRoute + "/pagination",
        "fnServerParams": function(aoData) {
            aoData.push({"name": "status", "value": jQuery("#status").val()});
            aoData.push({"name": "data_inicial", "value": jQuery("#dataInicial").val()});
            aoData.push({"name": "data_final", "value": jQuery("#dataFinal").val()});
        }
    });

    jQuery("#status").change(function() {
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

function alterarStatus(status) {
    var sel = getCheckboxMarcados(multipleSelectionCheckboxName);
    msg = "Deseja realmente executar esta ação? ";

    if (sel.length > 0) {
        jConfirm(msg, 'Confirmar Alteração', function(response) {
            if (response) {
                jQuery.post(controllerRoute + "/alterarStatus", {sel: sel, status: status}, callBackAjax, "json");
            }
        });
    } else {
        jAlert("Nenhum registro selecionado", "Aviso");
    }
}

function excluir()
{
    var sel = getCheckboxMarcados(multipleSelectionCheckboxName);

    if (sel.length > 0) {
        jConfirm("Deseja realmente executar esta ação?", 'Confirmar Exclusão', function(response) {
            if (response) {
                jQuery.post(controllerRoute + "/delete", {sel: sel}, callBackAjax, "json");
            }
        });
    } else {
        jAlert("Nenhum registro selecionado", "Aviso");
    }
}

function visualizar(id) {
    var url = baseUrl() + "backgroundHome/visualizar/" + id;
    var data = { route: '/?preview=background&id=#id#&hash=#hash#' };

    jQuery.post(url, data, function(data) {
        window.open(data['url'], '_blank');
    }, "json");
}
