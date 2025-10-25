import './bootstrap';
import '../sass/app.scss';
import '@fortawesome/fontawesome-free/css/all.min.css';

import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

import Choices from 'choices.js';
import 'choices.js/public/assets/styles/choices.min.css';

const choicesDefaults = {
    removeItemButton: true,
    placeholderValue: 'Seleccione los pre-requisitos',
    searchEnabled: true,
    searchPlaceholderValue: 'Buscar curso...',
    itemSelectText: '',
};

const initChoices = (root = document) => {
    const selects = root.querySelectorAll('.choices-multiple');

    selects.forEach((select) => {
        if (select.dataset.choicesInitialized === 'true') {
            return;
        }

        const instance = new Choices(select, {
            ...choicesDefaults,
            placeholderValue: select.dataset.placeholder ?? choicesDefaults.placeholderValue,
            searchPlaceholderValue: select.dataset.searchPlaceholder ?? choicesDefaults.searchPlaceholderValue,
        });

        select.dataset.choicesInitialized = 'true';

        select.addEventListener('turbo:before-cache', () => {
            instance.destroy();
            select.dataset.choicesInitialized = 'false';
        });
    });
};

document.addEventListener('DOMContentLoaded', () => {
    initChoices();
});

document.addEventListener('shown.bs.modal', (event) => {
    initChoices(event.target);
});
