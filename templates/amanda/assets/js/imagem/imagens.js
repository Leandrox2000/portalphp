var table;
var tipoMarcacao;
var categoriaImagem;
var ids;
var fCategoria;
var fPasta;
var ckeditor = false;
var ckeditorInstance;

/**
 * Atualiza a lista
 */
function atualizarImagens() {
    table.fnDraw();
}

/**
 * Abre nova janela com o form de inclusão de imagem
 */
function novaImagem()
{
    idsImgs = jQuery('#idsImagens').val();
    jQuery.colorbox({
        href: "imagem/formModal/0/" + idsImgs,
        width: "50%",
        height: "90%",
        iframe: true
    });
}

/**
 * @returns Fecha o colorbox
 */
function fecharImagens()
{
    jQuery.colorbox.close();
}

/**
 * Armazena os ids no hidden e fecha o colorbox
 */
function escolherFecharImagens()
{
    if (tipoMarcacao == 'radio') {
        if (ckeditor === true) {
            setarImagemCkeditor();
        } else {
            setarImagemRadio();
        }
    } else {
        setarImagemCheckbox();
    }

    jQuery.colorbox.close();

}

/**
 * Abre colorbox com imagens agrupadas em radio
 */
function abrirImagemRadio(categoria)
{
    ckeditor = false;
    var link = baseUrl();
    ids = jQuery('#imagemBanco').val() !== "" ? jQuery('#imagemBanco').val() : 0;
    tipoMarcacao = 'radio';
    categoriaImagem = categoria;

    jQuery.colorbox({href: link + "imagem/imagens/" + tipoMarcacao + "/" + categoria, width: "75%", height: "90%", onComplete: loadDataTableImagens});

}

/**
 * @TODO Separar em outro arquivo?
 * @param object editor Instância do CKEditor.
 * @returns void
 */
function abrirImagemRadioCkeditor(editor)
{
    var link = baseUrl();
    ckeditor = true;
    ids = jQuery('#imagemBanco').val() !== "" ? jQuery('#imagemBanco').val() : 0;
    tipoMarcacao = 'radio';
    ckeditorInstance = editor;
    jQuery.colorbox({
        href: link + "imagem/imagens/radio/undefined",
        width: "75%",
        height: "90%",
        onComplete: loadDataTableImagens
    });
}

function setarImagemCkeditor()
{
    // Pega ID da imagem selecionada no ColorBox
    var imageId = jQuery("input[name='imagem']:checked").val();

    // Dispara requisição ajax
    jQuery.ajax(baseUrl() + 'imagem/getImagem', {
        data: {id: imageId},
        dataType: 'json',
        success: function(data) {
            // Monta o HTML da imagem
            var htmlImagem = '<img src="/' + data.caminho + '" alt="' + data.credito + '" title="' + data.legenda + '">';

            // Insere o HTML na instância do CKEditor
            ckeditorInstance.insertHtml(htmlImagem);
        }
    });
}

/**
 * Abre um colorbox com as imagens agrupadas por checkbox
 */
function abrirImagemCheckbox(categoria)
{
    ckeditor = false;
    var link = baseUrl();
    ids = jQuery('#imagemBanco').val() !== "" ? jQuery('#imagemBanco').val() + "," : 0;
    tipoMarcacao = 'checkbox';
    categoriaImagem = categoria;

    jQuery.colorbox({href: link + "imagem/imagens/" + tipoMarcacao + "/" + categoria + "/" + ids, width: "75%", height: "90%", onComplete: loadDataTableImagens});

}

/**
 * Carrega o datatable e o checkall
 */
function loadDataTableImagens() {
    table = jQuery('#imagens').dataTable({
        "sPaginationType": "full_numbers",
        "bSort": false,
        "fnDrawCallback": function(oSettings) {
            jQuery('input:checkbox,input:radio,select').uniform();
        },
        "bProcessing": true,
        "bServerSide": true,
        "sAjaxSource": "imagem/paginacaoColorbox",
        "fnServerParams": function(aoData) {
            aoData.push({"name": "pasta", "value": jQuery('#f_pasta').val()});
            aoData.push({"name": "tipo", "value": tipoMarcacao});
            aoData.push({"name": "ids", "value": ids});
        }
    });

    jQuery("input.checkall").on("change", function() {
        var parentTable = jQuery(this).parents('table');
        var ch = parentTable.find('tbody input:checkbox');
        var checked;

        if (jQuery(this).is(':checked')) {
            checked = true;
        } else {
            checked = false;
        }

        jQuery.each(ch, function(index, element) {
            jQuery(element).attr('checked', checked);
            ;
        });

        jQuery.uniform.update();
    });
}

function addImagemHidden(id) {
    var idsImagens = jQuery('#idsImagens').val();
    idsImagens = idsImagens + id + ',';
    jQuery('#idsImagens').val(idsImagens);
    ids = idsImagens.substring(0,(idsImagens.length - 1));;
    
}

function removeImagemHidden(id) {
    var idsImagens = jQuery('#idsImagens').val();
    idsImagens = idsImagens.replace(id + ',', '');
    jQuery('#idsImagens').val(idsImagens);
    ids = idsImagens.substring(0,(idsImagens.length - 1));;

}

jQuery(document).ready(function() {
    jQuery(document).on('click', '#filtro-categorias-pastas .valor-pasta', function() {
        jQuery('#filtro-categorias-pastas .valor-pasta').find('span').removeClass('glyphicon-folder-open');
        jQuery('#filtro-categorias-pastas .valor-pasta').find('span').addClass('glyphicon-folder-close');
        valorFiltro = jQuery(this).attr('data-id');
        jQuery(this).find('span').addClass('glyphicon-folder-open');
        jQuery('#f_pasta').val(valorFiltro);
        table.fnDraw();
    });

    jQuery(document).on('click', '#filtro-categorias-pastas span.cat', function() {
        jQuery('#filtro-categorias-pastas span.cat span.glyphicon').removeClass('glyphicon-folder-open');
        jQuery('#filtro-categorias-pastas span.cat span.glyphicon').addClass('glyphicon-folder-close');
        jQuery(this).find('span.glyphicon').addClass('glyphicon-folder-open');
        jQuery('#filtro-categorias-pastas ul').hide();
        jQuery(this).siblings('ul').show();
    });

    //Adiciona ou remove ids de imagens do campo hidden
    jQuery(document).on('click', '.marcar-checkbox', function() {
        if (jQuery(this).is(':checked')) {
            addImagemHidden(jQuery(this).val());

        } else {
            removeImagemHidden(jQuery(this).val());

        }
    });

    jQuery(document).on('click', '.checkall', function() {
        if (tipoMarcacao == 'checkbox') {
            var idsImagens = jQuery('#idsImagens').val();

            //Verifica se o checkbox esta sendo marcado
            if (jQuery(this).is(':checked')) {
                jQuery('.marcar').each(function() {
                    if (idsImagens.search(jQuery(this).val() + ",") == -1) {
                        addImagemHidden(jQuery(this).val());
                    }
                });


            } else {
                jQuery('.marcar').each(function() {
                    if (idsImagens.search(jQuery(this).val() + ",") !== -1) {
                        removeImagemHidden(jQuery(this).val());
                    }

                });
            }
        }

    });


});