var table;
var tipoMarcacao;
var categoriaImagem;
var ids;

/**
 * 
 * Atualiza a lista
 */
function atualizar(){
   table.fnDraw();
}


/**
 * 
 * Abre nova janela com o form de inclusão de imagem
 */
function nova() 
{
    window.open('galeria/form', '_blank');
}

/**
 * 
 * @returns Fecha o colorbox
 */
function fechar()
{
    jQuery.colorbox.close();
}

/**
 * 
 * Armazena os ids no hidden e fecha o colorbox
 */
function escolherFechar()
{
    if(tipoMarcacao == 'radio'){
        setarGaleriaRadio();
        jQuery.colorbox.close();
    } else {
        var retorno = 0;
        //  se a função for executada pelo setarGaleriaCheckbox de NOTÍCIAS haverá um retorno de 0 em caso de sucesso, caso contrário retorna um erro e 
        //  não permite que o colorbox seja fechado.
        retorno = setarGaleriaCheckbox();
        if (retorno==0) {
            jQuery.colorbox.close();
            jQuery(".galSelecionadas").sortable();
        } else {
            jQuery.colorbox.close();
        }
    }
}

/**
 * 
 * Abre colorbox com imagens agrupadas em radio
 */
function abrirGaleriaRadio(categoria)
{
    var link = baseUrl();
    ids = jQuery('#galeriaBanco').val() !== "" ? jQuery('#galeriaBanco').val() : 0;
    tipoMarcacao = 'radio';

    jQuery.colorbox({href: link + "imagem/imagens/" + tipoMarcacao, width: "75%", height: "90%", onComplete: loadDataTable});

}


function addGaleriaHidden(id) {

    if(ids == 0){
        ids = "";
        jQuery('#idsGalerias').val("");
    }

    var idsGalerias = jQuery('#idsGalerias').val();
    idsGalerias = idsGalerias + id + ',';
    jQuery('#idsGalerias').val(idsGalerias);
    ids = idsGalerias.substring(0,(idsGalerias.length - 1));;
    
}

function removeGaleriaHidden(id) {
    var idsGalerias = jQuery('#idsGalerias').val();
    idsGalerias = idsGalerias.replace(id + ',', '');
    jQuery('#idsGalerias').val(idsGalerias);
    ids = idsGalerias.substring(0,(idsGalerias.length - 1));;

}

/**
 * 
 * Abre um colorbox com as imagens agrupadas por checkbox
 */
function abrirGaleriaCheckbox(categoria)
{
    var link = baseUrl();
    ids = jQuery('#galeriaBanco').val() !== "" ? jQuery('#galeriaBanco').val() : 0;
    tipoMarcacao = 'checkbox';

    if(ids != 0){
        var ultimoCaracterIds = ids.substr(ids.length, -1);
        
        if(ultimoCaracterIds != ','){
            ids = ids + ",";
        }
    }
    
    jQuery.colorbox({href: link + "galeria/galerias/" + tipoMarcacao + "?categoria=" + categoria+"&idsMarcados="+ids, width: "75%", height: "90%", onComplete: loadDataTable});

}
/**
 * 
 * Carrega o datatable e o checkall 
 */ 
function loadDataTable() {
    table = jQuery('#galerias').dataTable({
        "sPaginationType": "full_numbers",
        "bSort": false,
        "fnDrawCallback": function(oSettings) {
            jQuery('input:checkbox,input:radio,select').uniform();
        },
        "bProcessing": true,
        "bServerSide": true,
        "sAjaxSource": "galeria/paginacaoColorbox",
        "fnServerParams": function(aoData) {
            aoData.push({"name": "tipo", "value": tipoMarcacao});
            aoData.push({"name": "ids", "value": ids});
        }
    });

    jQuery("#galerias").on( 'draw.dt', function () {
        verifyGalerias();
    } );
    
    jQuery("input.checkall").on("change", function() {
        var parentTable = jQuery(this).parents('table');
        var ch = parentTable.find('tbody input:checkbox');
        var checked;

        if ( jQuery(this).is(':checked') ) {
            checked = true;
        } else {
            checked = false;
        }
        
        var cb = 0;
        
        jQuery.each(ch, function(index, element) {
            jQuery(element).attr('checked', checked);
            
            if(checked){
                addGaleriaHidden(jQuery(element).val());
            }else{
                removeGaleriaHidden(jQuery(element).val());
            }
           
            
        });

        jQuery.uniform.update();
    });
}

function verifyGalerias(){
    var max = 3;
    var galerias = jQuery('#idsGalerias').val().split(',');
    var current = 0;
    for(var i in galerias){
        if(galerias[i].length != 0)
            current++;
    }    
    var url_atual = window.location.pathname;
    if(url_atual == '/noticia/form'){
        jQuery('.marcar').filter(':not(:checked)').prop('disabled', current >= max);
    }
}

jQuery(document).ready(function() {
    
    //Adiciona ou remove ids de imagens do campo hidden
    jQuery(document).on('click', '.marcar-galeria-colorbox', function() {
        
        if (jQuery(this).is(':checked')) {
            addGaleriaHidden(jQuery(this).val());

        } else {
            removeGaleriaHidden(jQuery(this).val());

        }
        verifyGalerias();
    });
    
});

/**
 * 
 * Armazena o id da imagem selecionada no campo hidden do formulário
 */
/*function setarImagemRadio()
{
    var v = jQuery("input[name='imagem']:checked").val();
    jQuery('#imagemBanco').val(v);
}
*/

/**
 * 
 * Armazena os ids das imagens selecionadas no campo hidden do formulário, separadas por virgula
 */
/*function setarImagemCheckbox()
{
    v = new Array();

    jQuery("input[type=checkbox][name='imagem[]']:checked").each(function() {
        v.push(jQuery(this).val());
    });

    jQuery('#imagemBanco').val(v);



}
*/
