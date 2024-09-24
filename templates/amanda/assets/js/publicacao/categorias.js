jQuery(document).ready(function(){
    
    carregaSortable();
    
});

function limparFormulario()
{
    jQuery('#idCategoria').val('');
    jQuery('#descricaoCategoria').val('');
    jQuery('#nomeCategoria').val('');
    jQuery('#acaoCategoria').val('adicionar');
}

function salvarCategoria()
{
    var acao = jQuery('#acaoCategoria').val();

    if (jQuery("#nomeCategoria").val().trim() != "") {
        if (acao === 'adicionar') {
            adicionarCategoria();
        } else if (acao === 'editar') {
            editarCategoria();
        }
    } else {
        jAlert("Informe a categoria");
    }
}

function adicionarCategoria()
{
    jQuery.post(baseUrl() + "publicacao/salvarCategoria", {
        nome: jQuery("#nomeCategoria").val().trim(),
        descricao: jQuery("#descricaoCategoria").val()
    }, callback, "json");
}

function carregaCategorias()
{
    jQuery.post(baseUrl() + "publicacao/getTableCategorias", function (table) {
        jQuery("#tableCategorias").replaceWith(table);
        carregaSortable();
    });
}

function excluirCategoria(id)
{
    jConfirm("Deseja realmente excluir a(s) Categorias(s) selecionada(s)?", 'Confirmar Exclusão',
            function (response) {
                if (response) {
                    jQuery.post(baseUrl() + "publicacao/excluiCategoria", {id: id}, callback, "json");
                }
            }
    );
}

function editaCategoria(id)
{
    jQuery.ajax(baseUrl() + 'publicacao/obterCategoria/' + id, {dataType: 'json'}).success(function (data) {
        jQuery('#idCategoria').val(data.id);
        jQuery('#descricaoCategoria').val(data.descricao);
        jQuery('#nomeCategoria').val(data.nome);
        jQuery('#acaoCategoria').val('editar');
    });
}

function editarCategoria()
{
    var id = jQuery("#idCategoria").val();
    var categoria = jQuery("#nomeCategoria").val().trim();
    var descricao = jQuery("#descricaoCategoria").val();

    jQuery.post(baseUrl() + "publicacao/salvarCategoria", {
        nome: categoria,
        id: id,
        descricao: descricao
    }, callback, "json");
}

function callback(data)
{
    if (data.response == 1) {
        jQuery.jGrowl(data.success, {life: 4000, position: 'center'});
        carregaCategorias();
        limparFormulario();
    } else if (data.response == 2) {
        jAlert(
            data.error, 
            'Erro ao Excluir!'
        );
        carregaVinculos();
    } else {
        jQuery.each(data.error, function (index, value) {
            jQuery.jGrowl(value, {life: 4000, position: 'center'});
        });
    }
}

function salvarPosicionamento()
{
    jConfirm("Deseja realmente executar esta ação?", 'Confirmar Posição', function(response) {
        if (response) {
            var ordenacao = [];
            jQuery("#tableCategorias tbody input[name='publicacaoCategoriaId']").each(function(i){
                ordenacao.push({
                    id: jQuery(this).val(),
                    ordenacao: (i + 1)
                });
            });
            jQuery.ajax('publicacao/ajaxAtualizarOrdenacao', {
                type: 'POST',
                dataType: 'json',
                data: {
                    ordenacao: ordenacao
                },
                success: function(data) {
                    if (data.resultado == 'ok') {
                        jQuery.jGrowl('Ação executada com sucesso.', {
                            life: 4000,
                            position: 'center'
                        });
                        // Atualiza o grid
//                        table.fnDraw();
                    }
                }
            });
        }
    });
}

function carregaSortable(){
    jQuery("#tableCategorias tbody").sortable({
        helper: function(e, tr){
            var originals = tr.children();
            var helper = tr.clone();
            helper.children().each(function(index){
              jQuery(this).width(originals.eq(index).width());
            });
            return helper;
        }
    }).disableSelection();
}