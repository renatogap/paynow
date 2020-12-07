class EditarMenuController {

    constructor() {
        this._form = document.getElementById('form');
        this._acao = document.getElementById('acao');
    }

    adicionarAcao() {
        if(this._acao.value == 0) {
            alert('Selecione uma ação');
            return false;
        }

        //acao repetida
        if(this._gridAcao.querySelector(`tbody tr[id='${this._acao.value}']`)) {
            return false;            
        }

        oTable.row.add({
            'DT_RowId': this._acao.value,
            'acao': this._acao.options[this._acao.selectedIndex].text + '<input type="hidden" value="' + this._acao.value + '" name="dependencia[]">'
        }).draw();
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