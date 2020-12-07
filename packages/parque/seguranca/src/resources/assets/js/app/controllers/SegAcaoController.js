class SegAcaoController {
    constructor() {
        this._form = document.getElementById('form');
        this._acao2 = document.getElementById('acao2');
        this._grid = document.getElementById('grid');
        this._checkDestaque = document.getElementById("destaque");
        this._nomeAmigavel = document.getElementById("nome_amigavel");
      
    }

    adicionarAcao() {
        if(this._acao2.value == 0) {
            alert('Selecione um acao');
            return false;
        }
        //acao repetida
        if(this._grid.querySelector(`tbody tr[id='${this._acao2.value}']`)) {
            return false;            
        }
        oTable.row.add({
            'DT_RowId': this._acao2.value,
            'acao': this._acao2.options[this._acao2.selectedIndex].text + '<input type="hidden" value="' + this._acao2.value + '" name="acao[]">'
        }).draw();
    }

    removerAcao() {
        oTable.rows('.selected').remove().draw();
    }

    destaque() {
        if (checkDestaque.checked == true){
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