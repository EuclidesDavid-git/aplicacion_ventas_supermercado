(() => {
    document.querySelectorAll('[data-metodo-pago]').forEach((boton) => {
        boton.addEventListener('click', () => {
            const metodo = document.querySelector('[name="metodo"]');
            if (metodo) metodo.value = boton.dataset.metodoPago;
        });
    });
})();