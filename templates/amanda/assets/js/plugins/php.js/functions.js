var php = {
    empty: function (mixed_var) {
        //  discuss at: http://phpjs.org/functions/empty/
        // original by: Philippe Baumann
        //    input by: Onno Marsman
        //    input by: LH
        //    input by: Stoyan Kyosev (http://www.svest.org/)
        // bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
        // improved by: Onno Marsman
        // improved by: Francesco
        // improved by: Marc Jansen
        // improved by: Rafal Kukawski
        //   example 1: empty(null);
        //   returns 1: true
        //   example 2: empty(undefined);
        //   returns 2: true
        //   example 3: empty([]);
        //   returns 3: true
        //   example 4: empty({});
        //   returns 4: true
        //   example 5: empty({'aFunc' : function () { alert('humpty'); } });
        //   returns 5: false

        var undef, key, i, len;
        var emptyValues = [undef, null, false, 0, '', '0'];

        for (i = 0, len = emptyValues.length; i < len; i++) {
            if (mixed_var === emptyValues[i]) {
                return true;
            }
        }

        if (typeof mixed_var === 'object') {
            for (key in mixed_var) {
                // TODO: should we check for own properties only?
                //if (mixed_var.hasOwnProperty(key)) {
                return false;
                //}
            }
            return true;
        }

        return false;
    },

    trim: function (str, charlist) {
        //  discuss at: http://phpjs.org/functions/trim/
        // original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
        // improved by: mdsjack (http://www.mdsjack.bo.it)
        // improved by: Alexander Ermolaev (http://snippets.dzone.com/user/AlexanderErmolaev)
        // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
        // improved by: Steven Levithan (http://blog.stevenlevithan.com)
        // improved by: Jack
        //    input by: Erkekjetter
        //    input by: DxGx
        // bugfixed by: Onno Marsman
        //   example 1: trim('    Kevin van Zonneveld    ');
        //   returns 1: 'Kevin van Zonneveld'
        //   example 2: trim('Hello World', 'Hdle');
        //   returns 2: 'o Wor'
        //   example 3: trim(16, 1);
        //   returns 3: 6

        var whitespace, l = 0,
                i = 0;
        str += '';

        if (!charlist) {
            // default list
            whitespace =
                    ' \n\r\t\f\x0b\xa0\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u200b\u2028\u2029\u3000';
        } else {
            // preg_quote custom list
            charlist += '';
            whitespace = charlist.replace(/([\[\]\(\)\.\?\/\*\{\}\+\$\^\:])/g, '$1');
        }

        l = str.length;
        for (i = 0; i < l; i++) {
            if (whitespace.indexOf(str.charAt(i)) === -1) {
                str = str.substring(i);
                break;
            }
        }

        l = str.length;
        for (i = l - 1; i >= 0; i--) {
            if (whitespace.indexOf(str.charAt(i)) === -1) {
                str = str.substring(0, i + 1);
                break;
            }
        }

        return whitespace.indexOf(str.charAt(0)) === -1 ? str : '';
    }
};
