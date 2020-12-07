class AtualizarDadosController {
    constructor() {
        this._cpf = document.getElementById('cpf');
        this._nascimento = document.getElementById('nascimento');

        let mascaraData = [/\d/, /\d/, '/', /\d/, /\d/, '/', /\d/, /\d/, /\d/, /\d/];
        this.mascara(this._nascimento, mascaraData);

        let mascaraCpf = [/\d/, /\d/, /\d/, '.', /\d/, /\d/, /\d/, '.', /\d/, /\d/, /\d/, '-', /\d/, /\d/];
        this.mascara(this._cpf, mascaraCpf);
    }

    mascara(e, m) {
        vanillaTextMask.maskInput({
            inputElement: e,
            mask: m,
        });
    }
}