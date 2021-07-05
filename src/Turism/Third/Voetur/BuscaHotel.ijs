/* @robot_full_class Inatto\Turism\Third\Voetur\BuscaHotel */
/* @robot_update true */

//
$(function () {
    new class__0000000000();
});


// exemplo
class class__0000000000 {

    constructor() {
        this.autoComplete($(".Inatto_Turism_Third_Voetur_BuscaHotel"));
    }

    autoComplete($c) {

        // pega raiz do site que esta no input hidden
        let root = $c.find("input[type=hidden].Root").val();

        // auto complete
        //noinspection JSUnusedGlobalSymbols,JSValidateTypes
        $c.find("input[type=text]").autocomplete({
            noCache: false,
            serviceUrl: root + '/admin/autocomplete?type=autoCompleteMunicp', // ex: /int/admin/autocomplete
            params: class__0000000000.ufSel($c),
            deferRequestBy: 0,
            onSearchStart: function (query) {
                // se existir UF, passa para filtrar
                if ($c.find("select#convSearchUf").length)
                    query.ufConv = $c.find("select#convSearchUf").find("option:selected").val();
            },
            onSelect: function (item) {
                // procura div par para alterar o ID
                let $divHelperForm = $(this).closest(".Inatto_Turism_Third_Voetur_BuscaHotel");
                let idValue = item['id'];

                //
                $divHelperForm.find("input[type=hidden].Id").val(idValue);

                // se existir flag - faz submit automatico
                if ($divHelperForm.find("input#convSearchAutoSubmit").length)
                    $(this).closest("form").submit();

            }
        });

    }

    static ufSel($c) {
        return $c.find("input.UfConv").find("option:selected").val();
    }


}



