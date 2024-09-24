function adicionarAmbito()
{
    if (jQuery("#novoAmbito").val().trim() != "") {
        jQuery.post(baseUrl() + "ambitoLcc/salvarAmbito", {nome: jQuery("#novoAmbito").val().trim()}, callbackAmbitos, "json");
    } else {
        jAlert("Informe o âmbito");
    }
}

function carregaAmbitos()
{
    jQuery.post(baseUrl() + "ambitoLcc/getTableAmbitos", function(table) {
        jQuery("#tableAmbitos").replaceWith(table);
    });
}

function excluirAmbito(id)
{
    jConfirm("Deseja realmente executar esta ação?", 'Confirmar Exclusão',
            function(response) {
                if (response) {
                    jQuery.post(baseUrl() + "ambitoLcc/excluiAmbito", {id: id}, callbackAmbitos, "json");
                }

            }
    );
}

function editaAmbito(id)
{
    jQuery("#formEdicao").prev().show();
    jQuery("#formEdicao").remove();
    jQuery("#ambito" + id)
            .hide()
            .parent()
            .append(
                "<span id=\"formEdicao\">"
                    +
                    "<input type='text' name='edicaoAmbito' id='edicaoAmbito' value='" + jQuery("#ambito" + id).text() + "'/>"
                    +
                    "<input type='hidden' name='idAmbito' id='idAmbito' value='" + id + "'/>"
                    +
                    "<a href=\"javascript:editarAmbito()\" class=\"btn btn_ btn_no_icon radius2\"><span>Salvar</span></a>"
                    +
                 "</span>"
                    );
    jQuery("#novoAmbito")
            .attr('disabled', 'disabled')
            .next()
            .attr({'href': 'javascript:void(0)'})
            .toggleClass("disabled");
}


function editarAmbito()
{
    var id = jQuery("#idAmbito").val();
    var ambito = jQuery("#edicaoAmbito").val().trim();
    if (ambito != "") {
        jQuery.post(baseUrl() + "ambitoLcc/salvarAmbito", {nome: ambito, id: id}, callbackAmbitos, "json");
    } else {
        jAlert("Informe o âmbito");
    }

}

function callbackAmbitos(data)
{
    if (data.response == 1) {
        jQuery.jGrowl(data.success, {life: 4000, position: 'center'});
        carregaAmbitos();
    } else {
        jQuery.each(data.error, function(index, value) {
            jQuery.jGrowl(value, {life: 4000, position: 'center'});
        });
    }
    jQuery("#novoAmbito")
            .val("")
            .removeAttr("disabled")
            .next()
            .attr({'href': 'javascript:adicionarAmbito()'})
            .removeClass("disabled");
}