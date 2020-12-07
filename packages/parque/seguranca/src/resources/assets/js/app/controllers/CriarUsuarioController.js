class CriarUsuarioController {
    constructor() {
        this._perfil = document.getElementById('perfil2');
        this._setor = document.getElementsByName('setor[]');
        this._grid = document.getElementById('grid');
        this._form = document.getElementById('form');
        this._nome = document.getElementById('nome');
        this._email = document.getElementById('email');
        this._cpf = document.getElementById('cpf');
        this._dtNascimento = document.getElementById('dt_nascimento');
        this._senha = document.getElementById('senha');
        this._senhaConfirmation = document.getElementById('senha_confirmation');
        this._unidade = document.getElementById('unidade');

        // let mascaraData = [/[0123]/, /\d/, '/', /[01]/, /\d/, '/', /[12]/, /\d/, /\d/, /\d/];
        // this.mascara(this._dtNascimento, mascaraData);
        
        let mascaraCPF = [/\d/, /\d/, /\d/, '.', /\d/, /\d/, /\d/, '.', /\d/, /\d/, /\d/, '-', /\d/, /\d/];
        this.mascara(this._cpf, mascaraCPF);

    } 

    baixarDados() {
        this._nome.value = '';
        this._email.value = '';
        this._dtNascimento.value = '';
        this._senha.value = '';
        this._senhaConfirmation.value = '';

        Ajax.ajax({
            url: this._cpf.dataset.url,
            data: {cpf: this._cpf.value},
            success: (e) => {
                if(e.id_usuario) {
                    this._email.disabled = true;
                    this._senha.disabled = true;
                    this._senhaConfirmation.disabled = true;

                    $('#msg-info').html(`O servidor <b>${e.servidor}</b> já é um usuário na base da Polícia Civil.<br>Sua autenticação com email e senha será o mesmo do sistema <b>${e.sistema}</b>.`)
                                  .removeClass('d-none');
                }

                this._nome.value = e.servidor;
                this._email.value = e.email;
                this._dtNascimento.value = e.nascimento;
                this._senha.value = e.cpf;
                this._senhaConfirmation.value = e.cpf;
                this._unidade.value = e.unidade;
            },
            method: 'POST',
            error: function (e) {
                if(e.url) {
                    window.location = e.url;
                    return false;
                }

                $('#snackbar').html(e.message)
                            .addClass('alert')
                            .addClass('alert-danger')
                            .fadeIn();
            }
        });
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

    removerPerfil() {
        oTable.rows('.selected').remove().draw();
    }

    salvar(e) {
        e.preventDefault();
        Ajax.ajax({
            url: this._form.action,
            data: this._form.serialize(),
            method: 'post',
            success: function (response) {
                alert(response.message);
                window.location = response.url;
            },
            error: function (response) {
                $('#snackbar').html(response.message)
                .addClass('alert')
                .addClass('alert-danger')
                .fadeIn();
   
            }
        });
    }

    mascara(e, m) {
        vanillaTextMask.maskInput({
            inputElement: e,
            mask: m,
        });
    }
}