const tags = document.getElementById("tag");
const elements = Array.from(tags.children)
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
    tag.append(div)
    i = i + 1
}