class SegUsuarioController {
    constructor() {
        this._form = document.getElementById('form');
        this._perfil2 = document.getElementById('perfil2');
        this._sistema2 = document.getElementById('sistema2');
        this._cpf = document.getElementById('cpf');
        this._nascimento = document.getElementById('nascimento');
        this._gridPerfil = document.getElementById('grid');
        this._gridSistema = document.getElementById('grid2');
    }


    adicionarPerfil() {
        if(this._perfil2.value == 0) {
            alert('Selecione um perfil');
            return false;
        }

        //perfil repetido
        if(this._gridPerfil.querySelector(`tbody tr[id='${this._perfil2.value}']`)) {
            return false;            
        }

        oTable.row.add({
            'DT_RowId': this._perfil2.value,
            'perfil': this._perfil2.options[this._perfil2.selectedIndex].text + '<input type="hidden" value="' + this._perfil2.value + '" name="perfil[]">'
        }).draw();
    }

    removerPerfil() {
        oTable.rows('.selected').remove().draw();
    }

    adicionarSistema() {
        if(this._sistema2.value == 0) {
            alert('Selecione um sistema');
            return false;
        }

        //sistema repetido
        if(this._gridSistema.querySelector(`tbody tr[id='${this._sistema2.value}']`)) {
            return false;            
        }

        oTable_grid2.row.add({
            'DT_RowId': this._sistema2.value,
            'sistema': this._sistema2.options[this._sistema2.selectedIndex].text + '<input type="hidden" value="' + this._sistema2.value + '" name="sistema[]">'
        }).draw();
    }

    removerSistema() {
        oTable_grid2.rows('.selected').remove().draw();
    }

    mascara(e, m) {
        vanillaTextMask.maskInput({
            inputElement: e,
            mask: m,
        });
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
                //let snackbar = new Snackbar();
                //snackbar.exibirVermelho(e.msg);
                $('#snackbar').html(e.msg)
                            .addClass('alert')
                            .removeClass('alert-success')
                            .addClass('alert-danger')
                            .fadeIn()
                            .delay(5000)
                            .fadeOut();
            }
        });
    }
}