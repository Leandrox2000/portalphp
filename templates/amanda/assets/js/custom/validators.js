jQuery(document).ready(function(){
    jQuery.validator.setDefaults({
        ignoreTitle: true
    });

    // Valida uma hora no formato hh:mm:ss
    jQuery.validator.addMethod("time", function (value, element) {
        return this.optional(element) || /^(([0-1]?[0-9])|([2][0-3])):([0-5]?[0-9])(:([0-5]?[0-9]))?$/i.test(value);
    }, "Informe uma hora válida.");

    // Não deixa espaços no início do nome
    jQuery.validator.addMethod("noSpace", function (value, element) {
        return this.optional(element) || /^(([^ ]+)(.*))$/i.test(value);
    }, "Informe um nome válido.");
});
