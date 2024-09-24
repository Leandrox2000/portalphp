/**
 * jQuery Validation Plugin 1.8.1
 *
 * http://bassistance.de/jquery-plugins/jquery-plugin-validation/
 * http://docs.jquery.com/Plugins/Validation
 *
 * Copyright (c) 2006 - 2011 Jörn Zaefferer
 *
 * Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 */

(function () {
    function stripHtml(value) {
        // remove html tags and space chars
        return value.replace(/<.[^<>]*?>/g, ' ').replace(/&nbsp;|&#160;/gi, ' ')
                // remove numbers and punctuation
                .replace(/[0-9.(),;:!?%#$'"_+=\/-]*/g, '');
    }

    jQuery.validator.addMethod("maxWords", function (value, element, params) {
        return this.optional(element) || stripHtml(value).match(/\b\w+\b/g).length < params;
    }, jQuery.validator.format("Please enter {0} words or less."));

    jQuery.validator.addMethod("minWords", function (value, element, params) {
        return this.optional(element) || stripHtml(value).match(/\b\w+\b/g).length >= params;
    }, jQuery.validator.format("Please enter at least {0} words."));

    jQuery.validator.addMethod("rangeWords", function (value, element, params) {
        return this.optional(element) || stripHtml(value).match(/\b\w+\b/g).length >= params[0] && value.match(/bw+b/g).length < params[1];
    }, jQuery.validator.format("Please enter between {0} and {1} words."));

})();

jQuery.validator.addMethod("letterswithbasicpunc", function (value, element) {
    return this.optional(element) || /^[a-z-.,()'\"\s]+$/i.test(value);
}, "Letters or punctuation only please");

jQuery.validator.addMethod("alphanumeric", function (value, element) {
    return this.optional(element) || /^\w+$/i.test(value);
}, "Letters, numbers, spaces or underscores only please");

jQuery.validator.addMethod("lettersonly", function (value, element) {
    return this.optional(element) || /^[a-z]+$/i.test(value);
}, "Letters only please");

jQuery.validator.addMethod("nowhitespace", function (value, element) {
    return this.optional(element) || /^\S+$/i.test(value);
}, "No white space please");

jQuery.validator.addMethod("ziprange", function (value, element) {
    return this.optional(element) || /^90[2-5]\d\{2}-\d{4}$/.test(value);
}, "Your ZIP-code must be in the range 902xx-xxxx to 905-xx-xxxx");

jQuery.validator.addMethod("integer", function (value, element) {
    return this.optional(element) || /^-?\d+$/.test(value);
}, "A positive or negative non-decimal number please");

/**
 * Return true, if the value is a valid date, also making this formal check dd/mm/yyyy.
 *
 * @example jQuery.validator.methods.date("01/01/1900")
 * @result true
 *
 * @example jQuery.validator.methods.date("01/13/1990")
 * @result false
 *
 * @example jQuery.validator.methods.date("01.01.1900")
 * @result false
 *
 * @example <input name="pippo" class="{dateITA:true}" />
 * @desc Declares an optional input element whose value must be a valid date.
 *
 * @name jQuery.validator.methods.dateITA
 * @type Boolean
 * @cat Plugins/Validate/Methods
 */
jQuery.validator.addMethod("dateITA", function (value, element) {
    var check = false;
    var re = /^\d{1,2}\/\d{1,2}\/\d{4}$/;
    if (re.test(value)) {
        var adata = value.split('/');
        var gg = parseInt(adata[0], 10);
        var mm = parseInt(adata[1], 10);
        var aaaa = parseInt(adata[2], 10);
        var xdata = new Date(aaaa, mm - 1, gg);
        if ((xdata.getFullYear() == aaaa) && (xdata.getMonth() == mm - 1) && (xdata.getDate() == gg))
            check = true;
        else
            check = false;
    } else
        check = false;
    return this.optional(element) || check;
}, "Please enter a correct date");

jQuery.validator.addMethod("dateNL", function (value, element) {
    return this.optional(element) || /^\d\d?[\.\/-]\d\d?[\.\/-]\d\d\d?\d?$/.test(value);
}, "Vul hier een geldige datum in.");

jQuery.validator.addMethod("time", function (value, element) {
    return this.optional(element) || /^([01][0-9])|(2[0123]):([0-5])([0-9])$/.test(value);
}, "Insira uma hora válida, entre 00:00 e 23:59");

// NOTICE: Modified version of Castle.Components.Validator.CreditCardValidator
// Redistributed under the the Apache License 2.0 at http://www.apache.org/licenses/LICENSE-2.0
// Valid Types: mastercard, visa, amex, dinersclub, enroute, discover, jcb, unknown, all (overrides all other settings)
jQuery.validator.addMethod("creditcardtypes", function (value, element, param) {

    if (/[^0-9-]+/.test(value))
        return false;

    value = value.replace(/\D/g, "");

    var validTypes = 0x0000;

    if (param.mastercard)
        validTypes |= 0x0001;
    if (param.visa)
        validTypes |= 0x0002;
    if (param.amex)
        validTypes |= 0x0004;
    if (param.dinersclub)
        validTypes |= 0x0008;
    if (param.enroute)
        validTypes |= 0x0010;
    if (param.discover)
        validTypes |= 0x0020;
    if (param.jcb)
        validTypes |= 0x0040;
    if (param.unknown)
        validTypes |= 0x0080;
    if (param.all)
        validTypes = 0x0001 | 0x0002 | 0x0004 | 0x0008 | 0x0010 | 0x0020 | 0x0040 | 0x0080;

    if (validTypes & 0x0001 && /^(51|52|53|54|55)/.test(value)) { //mastercard
        return value.length == 16;
    }
    if (validTypes & 0x0002 && /^(4)/.test(value)) { //visa
        return value.length == 16;
    }
    if (validTypes & 0x0004 && /^(34|37)/.test(value)) { //amex
        return value.length == 15;
    }
    if (validTypes & 0x0008 && /^(300|301|302|303|304|305|36|38)/.test(value)) { //dinersclub
        return value.length == 14;
    }
    if (validTypes & 0x0010 && /^(2014|2149)/.test(value)) { //enroute
        return value.length == 15;
    }
    if (validTypes & 0x0020 && /^(6011)/.test(value)) { //discover
        return value.length == 16;
    }
    if (validTypes & 0x0040 && /^(3)/.test(value)) { //jcb
        return value.length == 16;
    }
    if (validTypes & 0x0040 && /^(2131|1800)/.test(value)) { //jcb
        return value.length == 15;
    }
    if (validTypes & 0x0080) { //unknown
        return true;
    }
    return false;
}, "Please enter a valid credit card number.");

jQuery.validator.addMethod("ipv4", function (value, element, param) {
    return this.optional(element) || /^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/i.test(value);
}, "Please enter a valid IP v4 address.");

jQuery.validator.addMethod("ipv6", function (value, element, param) {
    return this.optional(element) || /^((([0-9A-Fa-f]{1,4}:){7}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}:[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){5}:([0-9A-Fa-f]{1,4}:)?[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){4}:([0-9A-Fa-f]{1,4}:){0,2}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){3}:([0-9A-Fa-f]{1,4}:){0,3}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){2}:([0-9A-Fa-f]{1,4}:){0,4}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(([0-9A-Fa-f]{1,4}:){0,5}:((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(::([0-9A-Fa-f]{1,4}:){0,5}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|([0-9A-Fa-f]{1,4}::([0-9A-Fa-f]{1,4}:){0,5}[0-9A-Fa-f]{1,4})|(::([0-9A-Fa-f]{1,4}:){0,6}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){1,7}:))$/i.test(value);
}, "Please enter a valid IP v6 address.");

// Data ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
jQuery.validator.addMethod("brazilianDate", function (value, element) {
    if (value == '__/__/____' || value == '')
        return true;

    //contando chars
    if (value.length != 10)
        return false;

    // verificando data
    var data = value;
    var dia = data.substr(0, 2);
    var barra1 = data.substr(2, 1);
    var mes = data.substr(3, 2);
    var barra2 = data.substr(5, 1);
    var ano = data.substr(6, 4);

    if (data.length != 10 || barra1 != "/" || barra2 != "/" || isNaN(dia) || isNaN(mes) || isNaN(ano) || dia > 31 || mes > 12)
        return false;

    if ((mes == 4 || mes == 6 || mes == 9 || mes == 11) && dia == 31)
        return false;

    if (mes == 2 && (dia > 29 || (dia == 29 && ano % 4 != 0)))
        return false;

    if (ano < 1900)
        return false;


    return true;
}, "Informe uma data válida");

// CPF ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
jQuery.validator.addMethod("CPF", function (value, element) {
    value = value.replace('.', '');
    value = value.replace('.', '');
    cpf = value.replace('-', '');
    while (cpf.length < 11)
        cpf = "0" + cpf;
    var expReg = /^0+$|^1+$|^2+$|^3+$|^4+$|^5+$|^6+$|^7+$|^8+$|^9+$/;
    var a = [];
    var b = new Number;
    var c = 11;
    for (i = 0; i < 11; i++) {
        a[i] = cpf.charAt(i);
        if (i < 9)
            b += (a[i] * --c);
    }
    if ((x = b % 11) < 2) {
        a[9] = 0
    } else {
        a[9] = 11 - x
    }
    b = 0;
    c = 11;
    for (y = 0; y < 10; y++)
        b += (a[y] * c--);
    if ((x = b % 11) < 2) {
        a[10] = 0;
    } else {
        a[10] = 11 - x;
    }
    if ((cpf.charAt(9) != a[9]) || (cpf.charAt(10) != a[10]) || cpf.match(expReg))
        return false;
    return true;
}, "Informe um CPF válido.");


// CNPJ ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
jQuery.validator.addMethod("CNPJ", function (value, element) {
    value = value.replace('_', '');
    ;
    var cnpj = value.replace('_', '');
    ;
    var valida = new Array(6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2);
    var dig1 = new Number;
    var dig2 = new Number;

    exp = /\.|\-|\//g
    cnpj = cnpj.toString().replace(exp, "");
    var digito = new Number(eval(cnpj.charAt(12) + cnpj.charAt(13)));

    for (i = 0; i < valida.length; i++) {
        dig1 += (i > 0 ? (cnpj.charAt(i - 1) * valida[i]) : 0);
        dig2 += cnpj.charAt(i) * valida[i];
    }
    dig1 = (((dig1 % 11) < 2) ? 0 : (11 - (dig1 % 11)));
    dig2 = (((dig2 % 11) < 2) ? 0 : (11 - (dig2 % 11)));

    if (((dig1 * 10) + dig2) != digito)
        return false;

    return true;

}, "Informe um CNPJ válido.");

/**
 * Valida a quantidade de tags. Deve ser entre 3 e 6 tags.
 */
jQuery.validator.addMethod("tagsMaxMin", function (value, element) {
    var tags = value.split(';');
    var tagsLength = tags.length;
    
    for (tag in tags) {
        var trimTag = php.trim(tags[tag]);

        if (php.empty(trimTag)) {
            tagsLength--;
        }
    }

    if (tagsLength > 6 || tagsLength < 3) {
        return false;
    }

    return true;
}, "Este campo deve ter entre 3 e 6 tags.");