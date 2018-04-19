define(['jquery', 'jqueryui', 'core/config', 'core/str'], function ($, jqui, mdlconfig, str) {

    return {
        init: function () {
            $(".submit-on-change").on('change', function(){
                $(this).closest('form').submit();
            });
        }
    }

});
