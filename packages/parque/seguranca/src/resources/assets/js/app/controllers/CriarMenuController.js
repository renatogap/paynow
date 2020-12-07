class CriarMenuController {
    constructor() {
        this._form = document.getElementById('form');
    }

    salvar(e) {
        e.preventDefault();
        Ajax.ajax({
            url: this._form.action,
            data: this._form.serialize(),
            method: 'post',
            success: function (e) {
                let snackbar = new Snackbar();
                snackbar.exibirVerde(e.msg);
            },
            error: function (e) {
                let snackbar = new Snackbar();
                snackbar.exibirVermelho(e.msg, false);
            }
        });
    }

}