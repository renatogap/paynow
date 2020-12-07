/**
 * @requires app/model/Ajax.js
 * @requires app/model/Mensagem.js
 * @requires app/views/Snackbar.js
 */
class PerfilController {

    constructor() {
        this._form = document.getElementById('form');
        this._salvar = document.getElementById('salvar');
        this._msg = document.getElementById('msg');
        this._erroForm = document.getElementById('erro_form');

    }

    exibirAlerta(m) {
        // let snackbar = new Snackbar();
        // snackbar.exibirVerde(m);
        this._msg.innerText = m;

        //foca na div de mensagem se ela não estiver vazia
        if(this._msg.textContent) {
            this._msg.scrollIntoView();
        }
    }

    filtrarGrupo(e) {
        //exibe todas as linhas ocultas
        let linhas = document.querySelectorAll('tbody tr');
        for (let i = 0; i < linhas.length; i++) {
            linhas[i].style = '';
        }

        //se não houver nada selecionado então retorna
        if(e.value == '') return;
        
        //oculta as linhas diferentes do grupo selecionado
        linhas = document.querySelectorAll(`tbody tr:not(.${e.value})`);

        for (let i = 0; i < linhas.length; i++) {
            linhas[i].style.display = 'none';
        }
    }

    salvar(e) {
        e.preventDefault();

        Ajax.ajax({
            url: this._form.action,
            method: 'post',
            data: this._form.serialize(),
            beforeSend: () => {
                this._salvar.innerHTML = 'Aguarde...';
                this._salvar.disabled = true;
            },
            success: (e) => {
                window.location = `${this._salvar.dataset.url}/${e.perfil}`;
            },
            error: (json) => {
                let a = new ListaDeErros(json);
                this._msg.innerHTML = a.gerarListaErro();
                this.exibirVermelho(this._erroForm);

                this._salvar.innerHTML = 'Salvar';
                this._salvar.disabled = false;

            }
        });
    }

    exibirVerde(e) {
        e.classList.remove('alert-danger');
        e.classList.add('alert-sucess');
        this.exibir(e);
    }

    exibirVermelho(e) {
        e.classList.remove('alert-success');
        e.classList.add('alert-danger');
        this.exibir(e);
    }

    /**
     * Exibe um elemento na tela
     * @param e
     */
    exibir(e) {
        e.classList.remove('d-none');
    }
}