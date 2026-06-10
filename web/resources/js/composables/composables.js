import { ref } from "vue";

const overflowHidden = "overflow-hidden";
const toggleSlide = ref(false);
window.onload = function () {

    //paper
    window.addEventListener('click', function (event) {
        const paperGroups = document.querySelectorAll('.paper-group')

        paperGroups.forEach((paperGroup) => {
            if (!paperGroup.contains(event.target)) {
                paperGroup.classList.remove('active')
            }
        })
    })
}




export default {

    //canvas
    openCanvas: function (targetID) {
        const targetElement = document.querySelector('#' + targetID);
        targetElement.classList.add('active');
        document.body.classList.add(overflowHidden);
    },
    closeCanvas: function (targetID) {
        const targetElement = document.querySelector('#' + targetID);
        targetElement.classList.remove('active');
        document.body.classList.remove(overflowHidden);
    },
    closeBackdrop: function (event) {
        const containerElement = event.currentTarget.firstElementChild
        const isWrapperElement = event.target.contains(containerElement)

        if (isWrapperElement) {
            event.currentTarget.classList.remove('active')
            document.body.classList.remove(overflowHidden)
        }
    },


    // modal
    openModal: function (targetID) {
        const targetElement = document.querySelector(`#${targetID}`);
        targetElement.classList.add('active');
        document.body.classList.add(overflowHidden);
    },

    closeModal: function (targetID) {
        const targetElement = document.querySelector(`#${targetID}`);
        targetElement.classList.remove('active');
        document.body.classList.remove(overflowHidden);
    },

    // paper
    handlePaper: function (event) {
        const currGroup = event.currentTarget.parentElement
        const isActivate = currGroup.className.includes('active')

        if (!isActivate) currGroup.classList.add('active')
        else currGroup.classList.remove('active')
    },


    // handleTab: function (event, targetID) {
    //     const targetBtns = document.querySelectorAll(`.tab-button`);
    //     const targetDivs = document.querySelectorAll(`.tab-content`);
    //     const currentBtn = event.currentTarget;
    //     const currentDiv = document.querySelector(`#${targetID}`);

    //     // remove all active class
    //     targetBtns.forEach(item => item.classList.remove('tab-active'));
    //     targetDivs.forEach(item => item.classList.remove('tab-active'));

    //     // add current active class
    //     currentBtn.classList.add('tab-active');
    //     currentDiv.classList.add('tab-active');
    // },



    handleSlide: function (id) {
        const targetElement = document.querySelector(`#${id}`);
        toggleSlide.value = !toggleSlide.value

        if(!toggleSlide.value) targetElement.style.height = '0px'
        else targetElement.style.height = targetElement.scrollHeight + 'px'
    }

}
