class EditarUsuarioController {
    constructor() {
        this._perfil = document.getElementById('perfil2');
        this._cardapio = document.getElementById('cardapio');
        this._setor = document.getElementsByName('setor[]');
        this._grid = document.getElementById('grid');
        this._grid2 = document.getElementById('grid2');
        this._form = document.getElementById('form');
        this._nome = document.getElementById('nome');
        this._email = document.getElementById('email');
        //this._cpf = document.getElementById('cpf');
        //this._unidade = document.getElementById('unidade');
        this._dtNascimento = document.getElementById('dt_nascimento');

        // let mascaraData = [/[0123]/, /\d/, '/', /[01]/, /\d/, '/', /[12]/, /\d/, /\d/, /\d/];
        // this.mascara(this._dtNascimento, mascaraData);

        //let mascaraCPF = [/\d/, /\d/, /\d/, '.', /\d/, /\d/, /\d/, '.', /\d/, /\d/, /\d/, '-', /\d/, /\d/];
        //this.mascara(this._cpf, mascaraCPF);
    }

    

    adicionarPerfil() {
        if (!this._perfil.value) return;

        if (this._grid.querySelectorAll(`tbody tr[id="${this._perfil.value}"]`).length > 0) {
            return false;
        }

        oTable.row.add({
            'DT_RowId': this._perfil.value,
            'perfil': this._perfil.options[this._perfil.selectedIndex].text + '<input type="hidden" value="' + this._perfil.value + '" name="perfil[]">'
        }).draw();
    }

    adicionarCardapio() {
        if (!this._cardapio.value) return;

        if (this._grid2.querySelectorAll(`tbody tr[id="${this._cardapio.value}"]`).length > 0) {
            return false;
        }

        oTable_grid2.row.add({
            'DT_RowId': this._cardapio.value,
            'cardapio': this._cardapio.options[this._cardapio.selectedIndex].text + '<input type="hidden" value="' + this._cardapio.value + '" name="cardapio[]">'
        }).draw();
    }

    removerPerfil() {
        oTable.rows('.selected').remove().draw();
    }

    removerCardapio() {
        oTable_grid2.rows('.selected').remove().draw();
    }

    salvar(e) {
        e.preventDefault();

        if (this._form.checkValidity() === false) {
            return;
        }

        Ajax.ajax({
            url: this._form.action,
            data: this._form.serialize(),
            method: 'post',
            success: function (e) {
                alert(e.message);
                //let snackbar = new Snackbar();
                //snackbar.exibirVerde(e.message);
            },
            error: function (e) {
                alert(e.message);
                //let snackbar = new Snackbar();
                //snackbar.exibirVermelho(e.message, false);
            }
        });
    }

}