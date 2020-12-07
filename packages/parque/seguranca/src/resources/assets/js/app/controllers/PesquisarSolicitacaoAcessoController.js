class PesquisarSolicitacaoAcessoController {

    constructor() {
        this._cpf = document.getElementById('cpf');

        let mascaraCPF = [/\d/, /\d/, /\d/, '.', /\d/, /\d/, /\d/, '.', /\d/, /\d/, /\d/, '-', /\d/, /\d/];
        this.mascara(this._cpf, mascaraCPF);

        this._excluir = document.getElementById('excluir');
    }

    editar(e) {
        console.log();

        let linha = oTable.rows('.selected').data()[0];
        if (linha !== null) {
            window.location = `${e.dataset.url}/${linha.id}`;
        } else
            alert('Selecione um item');
        return false;
    }

    excluir(e) {
        let linha = oTable.rows('.selected').data()[0];

        if (linha !== null) {

            if (confirm('Deseja excluir este item?'))

                Ajax.ajax({
                    url: `${e.dataset.url}`,
                    method: 'POST',
                    data: {id: linha.id},
                    beforeSend: () => {
                        this._excluir.disabled = true;
                    },
                    success: (json) => {
                        alert(json.message);
                        this._excluir.Tooltip.hide();
                        oTable.draw();
                    },
                    error: (json) => {
                        alert(json.message);
                        this._excluir.disabled = false;
                    }
                });
        } else
            alert('Selecione uma item para excluir');

        return false;
    }

    /**
     * Faz a pesquisa do formulário quando o submit é chamado
     * Toda a regra é de submit é herdada do modelo layouts/datatables
     * @param e event
     */
    pesquisar(e) {
        e.preventDefault();
        oTable.draw();
    }

    mascara(e, m) {
        vanillaTextMask.maskInput({
            inputElement: e,
            mask: m,
        });
    }
}