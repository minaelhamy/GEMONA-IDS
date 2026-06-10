export default {
    showTarget: function (targetID, addClass) {
        const targetElement = document.querySelector(`#${targetID}`);
        targetElement.classList.add(addClass);
        document.body.classList.add('overflow-hidden');
    },
    hideTarget: function (targetID, addClass) {
        const targetElement = document.querySelector(`#${targetID}`);
        targetElement.classList.remove(addClass);
        document.body.classList.remove('overflow-hidden');
    },
    multiTargets: function (event, commonBtnClass, commonDivClass, targetID) {
        const targetBtns = document.querySelectorAll(`.${commonBtnClass}`);
        const targetDivs = document.querySelectorAll(`.${commonDivClass}`);
        const currentBtn = event.currentTarget
        const currentDiv = document.querySelector(`#${targetID}`);

        // remove all active class
        targetBtns.forEach(item => item.classList.remove('active'));
        targetDivs.forEach(item => item.classList.remove('active'));

        // add current active class
        currentBtn.classList.add('active');
        currentDiv.classList.add('active');
    },
    colspanHideShow: function(event, targetID) {
        const targetElement = document.querySelector(`#${targetID}`);
        const clickElement = event.currentTarget;
        this.toggleValue = !this.toggleValue;

        if(this.toggleValue) {
            clickElement.classList.add('active');
            targetElement.style.height = `${targetElement.scrollHeight}px`;
        }
        else {
            clickElement.classList.remove('active');
            targetElement.style.height = '0px';
        }
    }
}
