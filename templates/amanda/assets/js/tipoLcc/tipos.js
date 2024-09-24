function adicionarTipo()
{
    if (jQuery("#novoTipo").val().trim() != "") {
        jQuery.post(baseUrl() + "tipoLcc/salvarTipo", {nome: jQuery("#novoTipo").val().trim()}, callbackTipos, "json");
    } else {
        jAlert("Informe o status");
    }
}

function carregaTipos()
{
    jQuery.post(baseUrl() + "tipoLcc/getTableTipos", function(table) {
        jQuery("#tableTipos").replaceWith(table);
    });
}

function excluirTipo(id)
{
    jConfirm("Deseja realmente executar esta ação?", 'Confirmar Exclusão',
            function(response) {
                if (response) {
                    jQuery.post(baseUrl() + "tipoLcc/excluiTipo", {id: id}, callbackTipos, "json");
                }

            }
    );
}

function editaTipo(id)
{
    jQuery("#formEdicao").prev().show();
    jQuery("#formEdicao").remove();
    jQuery("#tipo" + id)
            .hide()
            .parent()
            .append(
                 "<span id=\"formEdicao\">"
                    +
                    "<input type='text' name='edicaoTipo' id='edicaoTipo' value='" + jQuery("#tipo" + id).text() + "'/>"
                    +
                    "<input type='hidden' name='idTipo' id='idTipo' value='" + id + "'/>"
                    +
                    "<a href=\"javascript:editarTipo()\" class=\"btn btn_ btn_no_icon radius2\"><span>Salvar</span></a>"
                    +
                 "</span>"
                    );
    jQuery("#novaTipo")
            .attr('disabled', 'disabled')
            .next()
            .attr({'href': 'javascript:void(0)'})
            .toggleClass("disabled");
}


function editarTipo()
{
    var id = jQuery("#idTipo").val();
    var tipo = jQuery("#edicaoTipo").val().trim();
    
    if (tipo != "") {
        jQuery.post(baseUrl() + "tipoLcc/salvarTipo", {nome: tipo, id: id}, callbackTipos, "json");
    } else {
        jAlert("Informe o tipo.");
    }

}

function callbackTipos(data)
{
    if (data.response == 1) {
        jQuery.jGrowl(data.success, {life: 4000, position: 'center'});
        carregaTipos();
    } else if (data.response == 2) {
        jAlert(
            data.error, 
            'Erro ao Excluir!'
        );
        carregaVinculos();
    } else {
        jQuery.each(data.error, function(index, value) {
            jQuery.jGrowl(value, {life: 4000, position: 'center'});
        });
    }
    jQuery("#novoTipo")
            .val("")
            .removeAttr("disabled")
            .next()
            .attr({'href': 'javascript:adicionarTipo()'})
            .removeClass("disabled");
}