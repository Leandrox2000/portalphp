var table;
jQuery(document).ready(function() {

    table = jQuery('#noticias').dataTable({
        "sPaginationType": "full_numbers",
        "bSort": false,
        "fnDrawCallback": function(oSettings) {
            jQuery('#noticias tbody input:checkbox').uniform();
        },
        "bProcessing": true,
        "bServerSide": true,
        "sAjaxSource": baseUrl() + "noticia/pagination",
        "fnServerParams": function(aoData) {
            aoData.push({"name": "status", "value": jQuery("#status").val()});
            aoData.push({"name": "site", "value": jQuery("#site").val()});
            aoData.push({"name": "data_inicial", "value": jQuery("#dataInicial").val()});
            aoData.push({"name": "data_final", "value": jQuery("#dataFinal").val()});
        }
    });

    jQuery("#status, #site").change(function() {
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
    
    
    jQuery('.checkall').change(function () {
        jQuery("#uniform-site").css("width", "155px");
        jQuery("#uniform-site span").css("width", "120px");
    });

    jQuery("#uniform-site").css("width", "155px");
    jQuery("#uniform-site span").css("width", "120px");


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
    var sel = getCheckboxMarcados("noticias");

    if (sel.length > 0) {
        jConfirm("Deseja realmente executar esta ação?", 'Confirmar Exclusão', function(response) {
            if (response) {
                jQuery.post("noticia/delete", {sel: sel}, callBackAjax, "json");
            }
        }
        );
    } else {
        jAlert("Nenhum registro selecionado", "Aviso");
    }
}

//function alterarStatus(status){
//    var sel = getCheckboxMarcados("noticias");
//    msg = "Deseja realmente executar esta ação? ";
//    
//    if (sel.length > 0) {
//        jConfirm(msg, 'Confirmar Alteração',
//                function(response) {
//                    if (response) {
//                        jQuery.post("noticia/alterarStatus", {sel: sel, status: status}, callBackAjax, "json");
//                    }
//                }
//        );
//    } else {
//        jAlert("Nenhum registro selecionado", "Aviso");
//    }
//}


function alterarStatus(status){
    var sel = getCheckboxMarcados("noticias");
    msg = "Deseja realmente executar esta ação? ";
    var table = "noticia";
    var entity = "EntityNoticia".replace("Entity" , "Entity\\");
    
    if (sel.length > 0) {
        jConfirm(msg, 'Confirmar Alteração',
                function(response) {
                    if (response) {
                        jQuery.ajax({
                            url: 'noticia/alterarStatusValidacao',
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


function alterarVisivel(status){
    var sel = getCheckboxMarcados("noticias");
    msg = "Deseja realmente executar esta ação? ";
    
    if (sel.length > 0) {
        jConfirm(msg, 'Confirmar Alteração',
                function(response) {
                    if (response) {
                        jQuery.post("noticia/alterarVisivel", {sel: sel, status: status}, callBackAjax, "json");
                    }
                }
        );
    } else {
        jAlert("Nenhum registro selecionado", "Aviso");
    } 
}

function visualizar(id) {
    var url = baseUrl() + "noticia/visualizar/" + id;
    var data = { route: '/noticias/detalhes/#id#?hash=#hash#' };

    jQuery.post(url, data, function(data) {
        window.open(data['url'], '_blank');
    }, "json");
}
