function adicionarCargo()
{
    if (jQuery("#novoCargo").val().trim() != "") {
        jQuery.post(baseUrl() + "funcionario/salvarCargo", {nome: jQuery("#novoCargo").val().trim()}, callbackCargos, "json");
    } else {
        jAlert("Informe o cargo");
    }
}

function carregaCargos()
{
    jQuery.post(baseUrl() + "funcionario/getTableCargos", function(table) {
        jQuery("#tableCargos").replaceWith(table);
    });
}

function excluirCargo(id)
{
    jConfirm("Deseja realmente excluir este registro?", 'Confirmar Exclusão',
            function(response) {
                if (response) {
                    jQuery.post(baseUrl() + "funcionario/excluiCargo", {id: id}, callbackCargos, "json");
                }
            }
    );
}

function editaCargo(id)
{
    jQuery("#formEdicao").prev().show();
    jQuery("#formEdicao").remove();
    jQuery("#cargo" + id)
            .hide()
            .parent()
            .append(
                "<span id=\"formEdicao\">"
                    +
                    "<input type='text' name='edicaoCargo' id='edicaoCargo' value='" + jQuery("#cargo" + id).text() + "'/>"
                    +
                    "<input type='hidden' name='idCargo' id='idCargo' value='" + id + "'/>"
                    +
                    "<a href=\"javascript:editarCargo()\" class=\"btn btn_ btn_no_icon radius2\"><span>Salvar</span></a>"
                    +
                 "</span>"
                    );
    jQuery("#novoCargo")
            .attr('disabled', 'disabled')
            .next()
            .attr({'href': 'javascript:void(0)'})
            .toggleClass("disabled");
}


function editarCargo()
{
    var id = jQuery("#idCargo").val();
    var cargo = jQuery("#edicaoCargo").val().trim();

    if (cargo != "") {
        jQuery.post(baseUrl() + "funcionario/salvarCargo", {nome: cargo, id: id}, callbackCargos, "json");
    } else {
        jAlert("Informe o cargo");
    }
}

function callbackCargos(data)
{
    if (data.response == 1) {
        
        jQuery.jGrowl(data.success, {life: 4000, position: 'center'});
        carregaCargos();
    } else {
        jQuery.each(data.error, function(index, value) {
            jQuery.jGrowl(value, {life: 4000, position: 'center'});
        });
    }
    jQuery("#novoCargo")
            .val("")
            .removeAttr("disabled")
            .next()
            .attr({'href': 'javascript:adicionarCargo()'})
            .removeClass("disabled");
}