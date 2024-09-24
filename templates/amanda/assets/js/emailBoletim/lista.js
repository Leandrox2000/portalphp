var table;
jQuery(document).ready(function() {

    table = jQuery('#emailboletim').dataTable({
        "sPaginationType": "full_numbers",
        "bSort": false,
        "fnDrawCallback": function(oSettings) {
            jQuery('input:checkbox,input:radio').uniform();
        },
        "bProcessing": true,
        "bServerSide": true,
        "sAjaxSource": "emailBoletim/paginacao"
    });

    jQuery('input:checkbox, input:radio, select').uniform();

    // ação do botão Excluir
    jQuery('.deletebutton').click(function() {
        var count = 0;
        var sel = new Array();
        var ch = jQuery('#emailboletim').find('tbody input[type=checkbox]');     //get each checkbox in a table

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
                    jQuery.post("emailBoletim/excluir", {sel: sel}, callBackExcluir, "json");
                }
            });
        } else {
            jAlert("Nenhum registro selecionado", "Aviso");
        }
    });


});


//Callbakc do botão Excluir
function callBackExcluir(data) {
    if (data.response == 1) {
        table.fnDraw(false);
        jQuery.jGrowl(data.sucess, {life: 4000, position: 'center'});
    } else {
        jQuery.each(data.error, function(index, value) {
            jQuery.jGrowl(value, {life: 4000, position: 'center'});
        });
    }

}


function exportar() {
    var count = 0;
    var sel = new Array();
    var ch = jQuery('#emailboletim').find('tbody input[type=checkbox]');     //get each checkbox in a table

    //check if there is/are selected row in table
    ch.each(function() {
        if (jQuery(this).parent().hasClass("checked")) {
            sel[count] = jQuery(this).val();
            count++;
        }
    });

    if (count > 0) {
        location.href = "emailBoletim/exportar?ids=" + sel;
    } else {
       jAlert("Nenhum registro selecionado", "Aviso");
    }
}
