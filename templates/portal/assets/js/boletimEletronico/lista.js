function registerValidatorFormPesquisa(){
    
    var element = $('#boletim-form-search');

    //old máscara
    //jQuery('input[name=numero]', element).mask('00/0000');
    jQuery('input[name=dataInicial]', element).mask('00/00/0000');

    //old validador
    /*jQuery.validator.addMethod('edicao', function(value) {

            return (value.trim() == '' || value.replace(/\D+/g, '').length == 6) ? true : false;
        },
        'Esse campo não é uma edição válida'
    );*/
    
    jQuery.validator.addMethod("dateBR", function(value) { 
        return value.trim() == '' || value.match(/^(0?[1-9]|[12][0-9]|3[0-1])[/., -](0?[1-9]|1[0-2])[/., -](19|20)\d{2}$/);
    });
    
    element.validate({
        rules: {
            dataInicial: { dateBR: true },
            numero: { edicao: true }
          },
        messages: {
            dataInicial: {
                //dateBR: "Esse campo não é uma data válida", //old
                dateBR: "Esse campo não é uma data válida"
                //edicao: "Esse campo não é uma edição válida" //old
            }
        }
    }); 
}

function registerValidatorFormEmail(){
    
    var element = $('#boletim-cadastro');
    var _MSG_AGUARDE = 'Aguarde...';
    
    element.validate({
        rules: {
            email: { required: true, email: true }
        },
        messages: {
            email: {
                required: "Este campo é requerido",
                email: "Informe um e-mail válido."
            }
        },
        submitHandler: function(form) {
            $.ajax($(form).attr('action'), {
                data: element.serialize(),
                type: 'POST',
                dataType: 'json',
                beforeSend: function() {
                    element.find('.message');
                    element.find('.message').html(_MSG_AGUARDE);
                },
                success: function(data) {
                    element.find('.message').html(data.mensagem);
                    element.get(0).reset(); // Limpa o formulário
                },
                error: function() {
                    element.find('.message').html(_MSG_NAO_ENVIADA);
                }
            });
        }
    }); 
}


jQuery(document).ready(function() {
    
    registerValidatorFormEmail();
    
    registerValidatorFormPesquisa();
    
    jQuery('.datepicker').datepicker();
    
    jQuery('input[name=email]', '#boletim-cadastro').on('focus', function(){
        
        jQuery(this).parent().find('label.error').remove();
        jQuery('.message', '#boletim-cadastro').html(''); 
    });
    
});
