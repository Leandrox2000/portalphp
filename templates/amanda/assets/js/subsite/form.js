jQuery(document).ready(function() {

    //Validação de periodo
    jQuery(".periodo").change(function() {
        validaPeriodo('data_inicial', 'data_final', 'hora_inicial', 'hora_final');
    });
    

    idEntidade = jQuery('#id').val();
    if (idEntidade === '' || idEntidade === null) {
        jQuery('#funcionalidade option').each(function(){
            jQuery(this).attr('selected', 'selected');
        });
    }

    // prepara validações de campos
    jQuery("#frm").validate({
        rules: {
            data_inicial: {required: true},
            hora_inicial: {required: true, time: true},
            hora_final : { time: true },
            nome: {required: true},
            sigla: {required: true}
        },
        messages: {
            data_inicial: {required: "Este campo é requerido"},
            hora_inicial: {required: "Este campo é requerido"},
            nome: {required: "Este campo é requerido"},
            sigla: {required: "Este campo é requerido"}
        },
        submitHandler: function(form) {
            if(CKEDITOR.instances.descricao) {
                CKEDITOR.instances.descricao.updateElement()
                jQuery('#descricao').val(CKEDITOR.instances.descricao.getData());
            }
            var t = jQuery(form);
            var url = t.attr('action');
            jQuery('button').fadeOut(20, function() {
                jQuery('#submitloading').fadeIn(20)
            });
            jQuery.post(url, t.serialize(), salvarCallback, "json");
            return false;
        }
    });
    
    jQuery('input:checkbox, input:radio, input:file, select').uniform();
    
    
    ///// FORM TRANSFORMATION /////
    jQuery('input:checkbox, input:radio, select.uniformselect, input:file').uniform();


    ///// DUAL BOX /////
    var db = jQuery('#dualselect').find('.ds_arrow .arrow');	//get arrows of dual select
    var sel1 = jQuery('#dualselect select:first-child');		//get first select element
    var sel2 = jQuery('#dualselect select:last-child');			//get second select element

    //sel2.empty(); //empty it first from dom.

    db.click(function(){
            var t = (jQuery(this).hasClass('ds_prev'))? 0 : 1;	// 0 if arrow prev otherwise arrow next
            if(t) {
                    sel1.find('option').each(function(){
                            if(jQuery(this).is(':selected')) {
                                    jQuery(this).attr('selected',false);
                                    var op = sel2.find('option:first-child');
                                    sel2.append(jQuery(this));
                            }
                    });	
            } else {
                    sel2.find('option').each(function(){
                            if(jQuery(this).is(':selected')) {
                                    jQuery(this).attr('selected',false);
                                    sel1.append(jQuery(this));
                            }
                    });		
            }

            criarArray();
    });

    function posicionarItem(select, direcao){

        if(direcao == "up"){
            jQuery("#"+select+" option:selected").each(function(){
                var val = jQuery(this).prev().val();
                var tex = jQuery(this).prev().text();
                var cla = jQuery(this).prev().attr("class");
                jQuery(this).prev().remove();
                if(val && tex){
                    jQuery(this).after("<option value='"+val+"' class='"+cla+"'>"+tex+"</option>");
                }
            });
        }else if(direcao == "down"){
            jQuery(jQuery("#"+select+" option:selected").get().reverse()).each(function(){
                var val = jQuery(this).next().val();
                var tex = jQuery(this).next().text();
                var cla = jQuery(this).next().attr("class");
                jQuery(this).next().remove();
                if(val && tex){
                    jQuery(this).before("<option value='"+val+"' class='"+cla+"'>"+tex+"</option>");
                }
            });
        }
        

        return true;
    }


    function criarArray(){
        var funcionalidade =  new Array();
        jQuery("#resultado_funcionalidade option").each(function() {
            //console.log(jQuery(this).val());
            //alert('aaaaaa'+jQuery(this).prev().val());
            //jQuery(this).attr("selected",true);
            funcionalidade.push(jQuery(this).val());

            //console.log(jQuery(this).val());

        });
        var myString = JSON.stringify(funcionalidade);

        jQuery("#hidden_funcionalidade").val(myString);
    }

    jQuery("#acima").click(function() {
        var a = posicionarItem("resultado_funcionalidade", "up");

        if(a == true ){
            criarArray();        
        }
    });

    jQuery("#abaixo").click(function() {
        var a = posicionarItem("resultado_funcionalidade", "down");

        if(a == true ){
            criarArray();        
        }
    });



    setInterval(criarArray(), 3000);
});


function salvarCallback(data) {
    if (data.response == 1) {
        jQuery.jGrowl(data.success, {life: 4000, position: 'center', close: function() {
            location.href = "subsite/lista";
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

