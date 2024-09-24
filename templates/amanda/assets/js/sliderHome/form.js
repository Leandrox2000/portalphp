jQuery(document).ready(function() {

    jQuery('input:checkbox, input:radio, select').uniform();

    //Validação de periodo
    jQuery(".periodo").change(function() {
        validaPeriodo('data_inicial', 'data_final', 'hora_inicial', 'hora_final');

    });
    var jcrop_api, boundx, boundy;
    jQuery('#imgSelecionadas img').Jcrop({
        setSelect: [jQuery('#x1').val(), jQuery('#y1').val(), jQuery('#x2').val(), jQuery('#y2').val()],
        aspectRatio: 1215/400,
        onChange: showCoords,
        onSelect: showCoords,
        onRelease: clearCoords
    },function(){
        // Use the API to get the real image size
        var bounds = this.getBounds();
        boundx = bounds[0];
        boundy = bounds[1];
    });
    
    // prepara validações de campos
    jQuery("#frm").validate({
        rules: {
            nome: {required: true},
            imagemBanco: {required: true},
            data_inicial: { required: true },
            hora_inicial: {required: true, time: true},
            hora_final: { time: true },
            "sites[]": {required: true, minlength: 1},
            descricao: {
                required: function(textarea) {
                    if(CKEDITOR.instances.descricao) CKEDITOR.instances.descricao.updateElement(); // update textarea
                    var editorcontent = textarea.value.replace(/<[^>]*>/gi, ''); // strip tags
                    return editorcontent.length === 0;
                }
            }
        },
        messages: {
            nome: {required: "Este campo é requerido"},
            imagemBanco: {required: "Este campo é requerido"},
            data_inicial: {required: "Este campo é requerido"},
            hora_inicial: {required: "Este campo é requerido"},
            "sites[]": {required: "Este campo é requerido"},
            descricao: {required: "Este campo é requerido"},
        },
        errorPlacement: function(error, element) {
            if (element.attr("name") == "imagemBanco") {
                jQuery('#imgSelecionadas').append(error);
            } else {
                error.insertAfter(element);
            }

        },
        submitHandler: function(form) {
            var t = jQuery(form);
            var url = t.attr('action');
            jQuery('button').fadeOut(20, function() {
                jQuery('#submitloading').fadeIn(20)
            });
            jQuery.post(url, t.serialize(), salvarCallback, "json");
            return false;
        }
    });


});

function salvarCallback(data)
{
    if (data.response == 1) {
        jQuery.jGrowl(data.success, {life: 4000, position: 'center', close: function() {
            location.href = "sliderHome/lista";
            }});
        
    } else {
        jQuery('#submitloading').fadeOut(20, function() {
            jQuery('button').fadeIn(20);
        });
        jQuery.each(data.error, function(index, value) {
            jQuery.jGrowl(value, {life: 4000, position: 'center'});
        });
    }
}

function setarImagemRadio()
{
    var v = jQuery("input[name='imagem']:checked").val();
    jQuery('#imagemBanco').val(v);

    jQuery.get("sliderHome/getHtmlImagenJcrop/" + v).done(
        function(data) {
            jQuery('#imgSelecionadas').html(data);
            cropImage();
        }
    );
}


function excluirImagem(id)
{
    var ids = jQuery('#imagemBanco').val();
    ids = ids.replace(id + ',', '');
    ids = ids.replace(',' + id, '');
    ids = ids.replace(id, '');

    jQuery('#img' + id).remove();
    jQuery('#imagemBanco').val(ids)

}

//Crop Image
function cropImage(){
        // Create variables (in this scope) to hold the API and image size
        var jcrop_api, boundx, boundy;
        jQuery('#imgSelecionadas img').Jcrop({
            setSelect: [jQuery('#imgSelecionadas img').width(), 0, 0, 0],
            aspectRatio: 1215/400,
            onChange: showCoords,
            onSelect: showCoords,
            onRelease: clearCoords
        },function(){
            // Use the API to get the real image size
            var bounds = this.getBounds();
            boundx = bounds[0];
            boundy = bounds[1];
        });
}

function showCoords(c){
    jQuery('#x1').val(c.x);
    jQuery('#y1').val(c.y);
    jQuery('#x2').val(c.x2);
    jQuery('#y2').val(c.y2);
    jQuery('#w').val(c.w);
    jQuery('#h').val(c.h);
}

function clearCoords(){
    jQuery('#x1, #y1, #x2, #y2, #w, #h').val('');
}