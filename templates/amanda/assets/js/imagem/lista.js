var table;
jQuery(document).ready(function() {

    table = jQuery('#imagens').dataTable({
        "sPaginationType": "full_numbers",
        "bSort": false,
        "fnDrawCallback": function(oSettings) {
            jQuery('input:checkbox, input:radio, select').uniform();
        },
        "bProcessing": true,
        "bServerSide": true,
        "sAjaxSource": "imagem/pagination",
        "fnServerParams": function(aoData) {
            aoData.push({"name": "data_inicial", "value": jQuery("#data_inicial").val()});
            aoData.push({"name": "data_final", "value": jQuery("#data_final").val()});
            aoData.push({"name": "categoria", "value": jQuery("#categoria").val()});
            aoData.push({"name": "pasta", "value": jQuery("#pastas").val()});
        }
    });
     
    //Busca as pastas relacionadas a uma categoria
    jQuery('#categoria').change(function() {
        jQuery.post("imagem/carregaPastasSelect/" + jQuery('#categoria').val(), function(data) {
            jQuery('#pastas').html('<option value="">Todas as pastas</option>');

            //for (row in data) {
            //    jQuery('#pastas').append('<option value=' + row + '>' + data[row] + '</option>');
            //}

            for(var i = 0; i < data.length; i++) {
                jQuery('#pastas').append('<option value=' + data[i]['id'] + '>' + data[i]['nome'] + '</option>');
            }

        }, "json");
        table.fnDraw();
    });
    
    jQuery('#pastas').change(function() {
    table.fnDraw();
    });
    
//    
//    jQuery("#categoria").change(function() {
//            table.fnDraw();
//    });


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
    var ch = jQuery('#imagens').find('tbody input[type=checkbox]');     //get each checkbox in a table

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
                jQuery.post("imagem/delete", {sel: sel}, callBackAcoes, "json");
            }
        });
    } else {
        jAlert("Nenhum registro selecionado", "Aviso");
    }
}


