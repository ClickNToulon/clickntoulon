function quantityInput(element, options) {
    const spinner = element;

    const defaultOptions = {
        min: 1,
        max: 99,
        value: 1,
    };

    options = Object.assign({}, defaultOptions, options);

    const obj = {

        input: spinner.querySelector('input[type="number"]'),
        init() {
            this.setup();
            this.events();
            return this;
        },
        setup() {

            this.input.value = options.value;
            this.max = options.max;
            this.min = options.min;

            const qNav = document.createElement('div');
            const qUp = document.createElement('div');
            const qDown = document.createElement('div');

            qNav.setAttribute('class', 'quantity-nav');
            qUp.setAttribute('class', 'quantity-button quantity-button--up');
            qDown.setAttribute('class', 'quantity-button quantity-button--down');

            qUp.innerHTML = '+';
            qDown.innerHTML = '-';
            qNav.appendChild(qUp);
            qNav.appendChild(qDown);
            spinner.appendChild(qNav);

            this.btnUp = spinner.querySelector('.quantity-button--up');
            this.btnDown = spinner.querySelector('.quantity-button--down');
        },
        trigger() {
            const event = document.createEvent('HTMLEvents');
            event.initEvent('change', true, false);
            return event;
        },
        events() {
            this.btnUp.addEventListener('click', () => {
                const oldValue = parseFloat(this.input.value);
                let newVal;
                if (oldValue >= this.max) {
                    newVal = oldValue;
                } else {
                    newVal = oldValue + 1;
                }
                this.input.value = newVal;
                document.querySelector(".quantity-input").value = newVal;
                this.input.dispatchEvent(this.trigger());
            });

            this.btnDown.addEventListener('click', () => {
                const oldValue = parseFloat(this.input.value);
                let newVal;
                if (oldValue <= this.min) {
                    newVal = oldValue;
                } else {
                    newVal = oldValue - 1;
                }
                this.input.value = newVal;
                this.input.dispatchEvent(this.trigger());
            });
            this.input.addEventListener('change', () => {
                if (parseInt(this.input.value, 16) < this.min) {
                    this.input.value = this.min;
                }
                if (parseInt(this.input.value, 16) > this.max) {
                    this.input.value = this.max;
                }
            });
        },
    }
    return obj.init();
}

const numberInputs = document.querySelectorAll(".quantity");

if (numberInputs.length > 0) {
    numberInputs.forEach((el, index) => {
        quantityInput(el);
    });
}