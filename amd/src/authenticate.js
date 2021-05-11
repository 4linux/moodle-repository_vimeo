define(['jquery'], function($) {
    return {
        init: function(url) {
            $("#id_authenticatebutton").on('click', function() {
                window.location.replace(url);
            });
        }
    };
});