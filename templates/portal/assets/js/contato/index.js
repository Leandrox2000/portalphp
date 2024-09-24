(function($) {
    function contactForm(selector)
    {
        var element = $(selector);
        var _MSG_ENVIADA = 'Mensagem enviada com sucesso! Em breve entraremos em contato.';
        var _MSG_NAO_ENVIADA = 'Não foi possível enviar sua mensagem. Desculpe pelo transtorno.';
        var _MSG_AGUARDE = 'Aguarde...';

        element.validate({
            ignore: ".ignore",
            rules: {
                nome: { required: true },
                email: { required: true, email: true },
                mensagem: { required: true },
                hiddenRecaptcha: {
                    required: function () {
                        if (grecaptcha.getResponse() == '') {
                            return true;
                        } else {
                            return false;
                        }
                    }
                }
            },
            messages: {
                nome: { required: "Este campo é requerido" },
                email: {
                    required: "Este campo é requerido",
                    email: "Informe um e-mail válido."
                },
                mensagem: { required: "Este campo é requerido" },
                hiddenRecaptcha: { required: "Este campo é requerido" }
            },
            submitHandler: function(form) {
                $.ajax($(form).attr('action'), {
                    data: element.serialize(),
                    type: 'POST',
                    dataType: 'json',
                    beforeSend: function(){
                        element.find('.message').css('color', '#000');
                        element.find('.message').html(_MSG_AGUARDE);
                        element.find('.btnSubmit').attr('disabled', 'disabled');
                    },
                    success: function(data, textStatus, jqXHR){
                        if(!data.success) {
                            element.find('.message').html(data.response);
                            element.find('.btnSubmit').attr('disabled', false);
                            grecaptcha.reset();
                            return;
                        }
                        element.find('.message').html(_MSG_ENVIADA);
                        element.find('.btnSubmit').attr('disabled', false);
                        element.get(0).reset(); // Limpa o formulário
                        grecaptcha.reset();
                    },
                    error: function(){
                        element.find('.message').html(_MSG_NAO_ENVIADA);
                        element.find('.btnSubmit').attr('disabled', false);
                    }
                });
            }
        });
    }

    $(document).ready(function(){
        contactForm('#formContato');
        
        $(document).on('click', '.btnSubmit', function() {        
            $('#formContato .message').html('');
        });
    });

})(jQuery);
    
/**
 * A chamada é feita pelo ReCaptcha do google para realizar a
 * validação.
 */
function recaptchaCallback() {
    $('#hiddenRecaptcha').valid();
}