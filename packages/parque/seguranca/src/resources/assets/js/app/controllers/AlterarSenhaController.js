class AlterarSenhaController {
    
    constructor() {
        this._form = document.getElementById('form');
        this._salvar = document.getElementById('salvar');
    }

    salvar(e) {
        e.preventDefault();

        Ajax.ajax({
            url: this._form.action,
            data: this._form.serialize(),
            method: 'post',
            beforeSend: () => this._salvar.disabled = true,
            success: (e) => {
    
                let snackbar = new Snackbar();
                snackbar.exibirVerde(e, true);
            },
            error: (e) => {
                let snackbar = new Snackbar();
                snackbar.exibirVermelho(e, false);
            },
            complete: () => this._salvar.disabled = false
        });
    }
}