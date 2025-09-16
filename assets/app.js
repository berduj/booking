import './bootstrap.js';
import * as Bootstrap from 'bootstrap';
import './styles/app.scss';
import 'leaflet/dist/leaflet.min.css'

import {FrontAutoComplete} from 'frontTools';
import {FrontSortable} from "frontTools";

import L from "leaflet"
import 'leaflet.markercluster';


document.addEventListener("DOMContentLoaded", function (event) {

    new FrontAutoComplete('typehead', function (id) {
        window.location.href = id
    }, 2, 500);

    document.querySelectorAll('.sortable-entity[data-entity]').forEach(function (element) {
        new FrontSortable(element)
    })

    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function (event) {
            form.querySelectorAll('input[type="submit"], button').forEach(button => {
                button.disabled = true;
                if (button.tagName === 'INPUT') {
                    button.value = button.value + ' ...';
                } else {
                    button.textContent = button.textContent + ' ...';
                }
            });
        });
    });


});


