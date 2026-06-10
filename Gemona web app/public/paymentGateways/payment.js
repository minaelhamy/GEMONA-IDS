"use strict";

let paymentMethod = $('input[name=paymentMethod]:checked', '#paymentForm').val();
let showStatus    = false;
for (let item in gateway) {
    if (item === paymentMethod) {
        if (gateway[item]) {
            showStatus = true;
            $('#' + item + '_div').show();
        }
    } else {
        $('#' + item + '_div').hide();
    }
}

let clickGateway = false;
for (let item in onClickGateway) {
    if (item === paymentMethod) {
        showStatus   = true;
        clickGateway = true;
        break;
    }
}

let form = document.getElementById('paymentForm');
if (showStatus) {
    $('#loading-show').addClass('hidden');
    $('#confirmBtn').removeClass('hidden');
    $('#backBtn').removeClass('hidden');

    if (clickGateway) {
        $('#confirmBtn').addClass('hidden');
        $('#backBtn').addClass('hidden');
    }

    form.addEventListener('submit', function (event) {
        event.preventDefault();
        let submit = false;
        for (let item in submitGateway) {
            if (item === paymentMethod) {
                submit = true;
                window[paymentMethod + '_payment']();
                break;
            }
        }

        if (!submit) {
            form.submit();
        }
    });
} else {
    form.submit();
}
