function adicionarVinculo()
{
    if (jQuery("#novoVinculo").val().trim() != "") {
        jQuery.post(baseUrl() + "funcionario/salvarVinculo", {nome: jQuery("#novoVinculo").val().trim()}, callbackVinculos, "json");
    } else {
        jAlert("Informe o vínculo");
    }
}

function carregaVinculos()
{
    jQuery.post(baseUrl() + "funcionario/getTableVinculo", function(table) {
        jQuery("#tableVinculos").replaceWith(table);
    });
}

function excluirVinculo(id)
{
    jConfirm("Deseja realmente excluir este registro?", 'Confirmar Exclusão',
            function(response) {
                if (response) {
                    jQuery.post(baseUrl() + "funcionario/excluiVinculo", {id: id}, callbackVinculos, "json");
                }

            }
    );
}

function editaVinculo(id)
{
    jQuery("#formEdicao").prev().show();
    jQuery("#formEdicao").remove();
    jQuery("#vinculo" + id)
            .hide()
            .parent()
            .append(
                "<span id=\"formEdicao\">"
                    +
                    "<input type='text' name='edicaoVinculo' id='edicaoVinculo' value='" + jQuery("#vinculo" + id).text() + "'/>"
                    +
                    "<input type='hidden' name='idVinculo' id='idVinculo' value='" + id + "'/>"
                    +
                    "<a href=\"javascript:editarVinculo()\" class=\"btn btn_ btn_no_icon radius2\"><span>Salvar</span></a>"
                    +
                 "</span>"
                    );
    jQuery("#novoVinculo")
            .attr('disabled', 'disabled')
            .next()
            .attr({'href': 'javascript:void(0)'})
            .toggleClass("disabled");
}


function editarVinculo()
{
    var id = jQuery("#idVinculo").val();
    var vinculo = jQuery("#edicaoVinculo").val().trim();

    jQuery.post(baseUrl() + "funcionario/salvarVinculo", {nome: vinculo, id: id}, callbackVinculos, "json");
}

function callbackVinculos(data)
{
    if (data.response == 1) {
        jQuery.jGrowl(data.success, {life: 4000, position: 'center'});
        carregaVinculos();
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
    jQuery("#novoVinculo")
            .val("")
            .removeAttr("disabled")
            .next()
            .attr({'href': 'javascript:adicionarVinculo()'})
            .removeClass("disabled");
}