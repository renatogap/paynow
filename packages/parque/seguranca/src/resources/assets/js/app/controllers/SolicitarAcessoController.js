class SolicitarAcessoController {
    constructor() {
        // this._cpf = document.getElementById('cpf');
        // this._cpf2 = document.getElementById('cpf2');
        this._nascimento = document.getElementById('nascimento');

        // let mascaraCPF = [/\d/, /\d/, /\d/, '.', /\d/, /\d/, /\d/, '.', /\d/, /\d/, /\d/, '-', /\d/, /\d/];
        // this.mascara(this._cpf, mascaraCPF);
        // this.mascara(this._cpf2, mascaraCPF);

        let mascaraData = [/[0123]/, /\d/, '/', /[01]/, /\d/, '/', /[12]/, /\d/, /\d/, /\d/];
        this.mascara(this._nascimento, mascaraData);
    }

    mascara(e, m) {
        vanillaTextMask.maskInput({
            inputElement: e,
            mask: m,
        });
    }
}