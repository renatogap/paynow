class EditarAcaoController {

    constructor() {
        this._form = document.getElementById('form');
        this._acao2 = document.getElementById('acao2');
        this._gridAcao = document.getElementById('grid');
        this._destaque = document.getElementById("destaque");
        this._nomeAmigavel = document.getElementById("nome_amigavel");
    }

    adicionarAcao() {
        if(this._acao2.value == 0) {
            alert('Selecione uma ação');
            return false;
        }

        //acao repetida
        if(this._gridAcao.querySelector(`tbody tr[id='${this._acao2.value}']`)) {
            return false;            
        }

        oTable.row.add({
            'DT_RowId': this._acao2.value,
            'acao': this._acao2.options[this._acao2.selectedIndex].text + '<input type="hidden" value="' + this._acao2.value + '" name="dependencia[]">'
        }).draw();
    }

    destaque() {
        if (destaque.checked == true){
          nomeAmigavel.style.display = "block";
        } else {
          nomeAmigavel.style.display = "none";
        }
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