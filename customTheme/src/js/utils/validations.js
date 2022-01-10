/*
    * Validation
    * */
function checkSelect(message, value) {
    if (typeof value === 'undefined' || value === '' || value == null) {
        return message;
    }
    return true;
}

function isChecked(message, value) {
    if (!value.checked) {
        return message;
    }
    return true;
}

function checkRegExp(pattern, message, value) {
    return pattern.test(value) ? true : message;
}

const validations = {
    fullName: [
        checkRegExp.bind(null, /^[A-Zа-я]{2,}$/i, 'Field may contain only letters and not be less than 2 letters'),
        checkRegExp.bind(null, /^[A-Zа-я]{2,64}$/i, 'Field may contain only letters and not be more than 64 letters'),
    ],
    email: [
        checkRegExp.bind(null,
            /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/,
            'Please enter valid email'),
    ],
    cellPhone: [
        checkRegExp.bind(null, /^[0-9]{1,}/i, 'Field may contain phone number'),
        checkRegExp.bind(null, /^[0-9]*/i, 'Field may contain only digits'),
    ],
    linkLinkedIn: [
        checkRegExp.bind(null, /^[A-Zа-я]{2,}$/i, 'Field may contain only letters and not be less than 2 letters'),
        checkRegExp.bind(null, /^[A-Zа-я]{2,64}$/i, 'Field may contain only letters and not be more than 64 letters'),
    ],
    companyName: [
        checkRegExp.bind(null, /^[A-Zа-я]{2,}$/i, 'Field may contain only letters and not be less than 2 letters'),
        checkRegExp.bind(null, /^[A-Zа-я]{2,64}$/i, 'Field may contain only letters and not be more than 64 letters'),
    ],
    companyWebsite: [
        checkRegExp.bind(null, /^[A-Zа-я]{2,}$/i, 'Field may contain only letters and not be less than 2 letters'),
        checkRegExp.bind(null, /^[A-Zа-я]{2,64}$/i, 'Field may contain only letters and not be more than 64 letters'),
    ],
    howDid: [
        checkSelect.bind(null, 'You must choose one of the options'),
    ],
    industry: [
        checkSelect.bind(null, 'You must choose one of the options'),
    ],
    businessModel: [
        checkSelect.bind(null, 'You must choose one of the options'),
    ],
    terms: [
        isChecked.bind(null, 'You must confirm'),
    ],
    privacy: [
        isChecked.bind(null, 'You must confirm'),
    ],
};

function validateField(element) {
    const fieldValidation = validations[element.id];
    const result = {valid: true, element, message: ''};

    if (fieldValidation) {
        for (let i = 0, len = fieldValidation.length; i < len; i++) {
            const validationFunction = fieldValidation[i];

            if (getInputType(element) == 'checkbox') {
                getAnswer(element, validationFunction, result);
                break;
            }

            getAnswer(element.value, validationFunction, result);
            break;
        }
    }
    return result;
}

/* events */
function toggleError(element, message) {
    const errorMessageElement = element.nextElementSibling && element.nextElementSibling.classList.contains('js-error-message')
        ? element.nextElementSibling
        : null;

    if (errorMessageElement && message) {
        element.classList.add('invalid');
        element.classList.remove('valid');
        errorMessageElement.innerHTML = message;
    } else if (errorMessageElement) {
        element.classList.remove('invalid');
        element.classList.add('valid');
        errorMessageElement.innerHTML = '';
    }
}

function formOnchange(e) {
    if (e.target.dataset && e.target.dataset.validation !== undefined) {
        toggleError(e.target, validateField(e.target).message);
    }
}

function formSubmit(e) {

    e.preventDefault();

    const inputs = e.target.querySelectorAll('input[data-validation]');
    const validElements = [...inputs, ...e.target.querySelectorAll('select[data-validation]')];
    for (let i = 0; i < validElements.length; i++) {
        formOnchange({target: validElements[i]});
    }

    if (e.target.querySelectorAll('.invalid').length === 0) {
        sendData(e.target);
    }
}

async function sendData(form) {
    const response = await fetch('/wp-content/themes/mailcon/app/front/front-ajax.php', {
        method: 'POST',
        body: new FormData(form),
    });

    const result = await response.json();

    if (result.status === 'success') {
        document.querySelector('.work-with-us__form-wrap').innerHTML = `<div class="work-with-us__congratulation">
                                                                                    <img loading="lazy" src="/wp-content/themes/mailcon/public/img/general/success_img.svg">
                                                                                    <h3>Success!</h3>
                                                                                 </div>`;
    }
}

/* Other */

function getInputType(element) {
    return element.getAttribute('type');
}

function getAnswer(element, validationFunction, result) {
    const answer = validationFunction(element);
    if (typeof answer === 'string') {
        result.valid = false;
        result.message = answer;
    }
}

/*
* Listeners
* */
document.getElementById('work-with-us').addEventListener('change', formOnchange);
document.getElementById('work-with-us').addEventListener('submit', formSubmit);
