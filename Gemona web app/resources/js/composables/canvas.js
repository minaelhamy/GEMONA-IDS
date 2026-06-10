import { ref } from "vue";

export function useCanvas() { 
    const activeClass = ref('canvas-active')
    const hiddenClass = ref('overflow-hidden')

    function openCanvas(targetID) {
        const targetElement = document.querySelector(`#${targetID}`);
        targetElement.classList.add(activeClass.value);
        document.body.classList.add(hiddenClass.value);
    }

    function closeCanvas(targetID) {
        const targetElement = document.querySelector(`#${targetID}`);
        targetElement.classList.remove(activeClass.value);
        document.body.classList.remove(hiddenClass.value);
    }

    function closeBackdrop(event) {
        const containerElement = event.currentTarget.firstElementChild
        const isWrapperElement = event.target.contains(containerElement)

        if(isWrapperElement) {
            event.currentTarget.classList.remove(activeClass.value)
            document.body.classList.remove(hiddenClass.value)
        }
    }

    return { openCanvas, closeCanvas, closeBackdrop }
}