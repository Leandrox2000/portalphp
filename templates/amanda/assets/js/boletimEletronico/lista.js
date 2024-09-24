var table;
jQuery(document).ready(function() {

    table = jQuery('#boletimeletronico').dataTable({
        "sPaginationType": "full_numbers",
        "bSort": false,
        "fnDrawCallback": function(oSettings) {
            jQuery('input:checkbox,input:radio').uniform();
        },
        "bProcessing": true,
        "bServerSide": true,
        "sAjaxSource": "boletimEletronico/pagination",
        "fnServerParams": function(aoData) {
            aoData.push({"name": "status", "value": jQuery("#status").val()});
            aoData.push({"name": "anos", "value": jQuery("#anos").val()});
            aoData.push({"name": "data_inicial", "value": jQuery("#data_inicial").val()});
            aoData.push({"name": "data_final", "value": jQuery("#data_final").val()});
        }
    });

    jQuery("#status").change(function() {
        table.fnDraw();
    });

    jQuery("#anos").change(function() {
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

function alterarStatus(status){
    var sel = getCheckboxMarcados("boletimeletronico");
    msg = "Deseja realmente executar esta ação? ";
    
    if (sel.length > 0) {
        jConfirm(msg, 'Confirmar Alteração',
                function(response) {
                    if (response) {
                        jQuery.post("boletimEletronico/alterarStatus", {sel: sel, status: status}, callBackAcoes, "json");
                    }
                }
        );
    } else {
        jAlert("Nenhum registro selecionado", "Aviso");
    }
}

function excluir()
{
    var sel = getCheckboxMarcados("boletimeletronico");

    if (sel.length > 0) {
        jConfirm("Deseja realmente executar esta ação?", 'Confirmar Exclusão',
                function(response) {
                    if (response) {
                        jQuery.post("boletimEletronico/delete", {sel: sel}, callBackAcoes, "json");
                    }
                }
        );
    } else {
        jAlert("Nenhum registro selecionado", "Aviso");
    }
}