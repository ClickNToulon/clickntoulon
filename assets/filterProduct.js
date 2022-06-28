import "./styles/filter.scss"

import noUiSlider from 'nouislider'
import 'nouislider/dist/nouislider.css'
import wNumb from './wNumb'

const slider = document.getElementById('price-slider')

if (slider) {
    const min = document.getElementById('min')
    const max = document.getElementById('max')
    const maxValue = Math.ceil(parseFloat(slider.dataset.max))
    const range = noUiSlider.create(slider, {
        start: [min.value || 0, max.value || maxValue],
        connect: true,
        tooltips: true,
        behaviour: 'tap-drag',
        format: wNumb({
            decimals: 0
        }),
        range: {
            'min': parseInt(min.value) || 0,
            'max': maxValue
        }
    })
    range.on('slide', function (values, handle) {
        if (handle === 0) {
            min.value = Math.round(values[0])
        }
        if (handle === 1) {
            max.value = Math.round(values[1])
        }
    })
    range.on('end', function (values, handle) {
        if (handle===0) {
            min.dispatchEvent(new Event('change'))
        } else {
            max.dispatchEvent(new Event('change'))
        }
    })
}


const types = document.getElementById("types");
const elements = Array.from(types.children)
elements.forEach((element) => {
    element.classList.add('hidden')
})
for (let i = 0; i < elements.length; i++) {
    const div = document.createElement("div")
    div.classList.add("flex")
    div.classList.add("flex-row")
    div.classList.add("w-full")
    div.classList.add("items-center")
    div.classList.add("space-x-2")
    div.append(elements[i])
    div.append(elements[i+1])
    const divElements = Array.from(div.children)
    divElements.forEach((element) => {
        element.classList.remove("hidden")
    })
    types.append(div)
    i = i + 1
}