M.mod_webinarru = M.mod_webinarru || {};

M.mod_webinarru.remove_spaces = {
    init: function() {
        var input = Y.one('#id_s_mod_webinarru_accounts');
        input.on('change', function() {
            var value = input.get('value');
            value = value.replace(/ /g, '');
            input.set('value', value);
        });
    },
}
