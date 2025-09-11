let spinner = document.createElement('i');
spinner.classList.add('fa', 'fa-spinner', 'fa-spin', 'fa-fw');

export class FrontAutoComplete {
    constructor(parameter, onClick, nbMinCarateres = 3, debounceTimeOut = 500) {
        const input = document.getElementById(parameter);
        input.insertAdjacentHTML('afterend', '<div id="' + parameter + '-list" class="autocomplete-items"></div>');
        const dropdown = document.getElementById(parameter + '-list');
        let debounceTimeout;
        input.addEventListener('input', () => {
            clearTimeout(debounceTimeout);
            debounceTimeout = setTimeout(async () => {
                dropdown.innerHTML = '';
                if (input.value.length < nbMinCarateres) {
                    return;
                }

                const formData = new FormData()
                formData.set("query", input.value)
                const response = await fetch(input.dataset.url, {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                console.log(data)
                for (let key in data) {
                    const item = data[key];
                    const div = document.createElement('div');
                    if (item.hasOwnProperty('type'))
                        div.innerHTML = "<picto class='" + item.type + "'></picto>"
                    div.dataset.type = item.type
                    div.innerHTML += item.label;
                    div.dataset.id = item.id;
                    dropdown.appendChild(div);
                    div.addEventListener('click', () => {
                        input.value = div.textContent;
                        input.dataset.value = div.dataset.id;
                        if (typeof onClick === 'function') {
                            onClick(div.dataset.id, div.dataset.type);
                        }
                        dropdown.innerHTML = '';
                    });
                }
            }, debounceTimeOut);
        });

        let currentFocus = -1;
        input.addEventListener('keydown', e => {
            const items = dropdown.getElementsByTagName('div');
            if (e.keyCode === 40) {
                currentFocus++;
                addActive(items);
            } else if (e.keyCode === 38) {
                currentFocus--;
                addActive(items);
            } else if (e.keyCode === 13) {
                e.preventDefault();
                if (currentFocus > -1) {
                    items[currentFocus].click();
                }
            }
        });

        function addActive(items) {
            if (!items) return false;
            removeActive(items);
            if (currentFocus >= items.length) currentFocus = 0;
            if (currentFocus < 0) currentFocus = (items.length - 1);
            items[currentFocus].classList.add('autocomplete-active');
        }

        function removeActive(items) {
            for (let i = 0; i < items.length; i++) {
                items[i].classList.remove('autocomplete-active');
            }
        }

        document.addEventListener('click', e => {
            if (!input.contains(e.target)) {
                dropdown.innerHTML = '';
                currentFocus = -1;
            }
        });
    }
}

import {Sortable} from "sortablejs";

export class FrontSortable {
    constructor(element) {
        Sortable.create(element, {
            animation: 150,
            store: {
                set: function (sortable) {
                    const formData = new FormData()
                    formData.set('order', sortable.toArray())
                    formData.set('entity', element.getAttribute('data-entity'))
                    const url = element.getAttribute('data-url')
                    fetch(url, {
                        method: 'POST',
                        body: formData
                    });
                }
            },
        });
    }
}

export class FrontUpdate {
    /* input de saisie doit avoir un data-url, un data-id et une value, le controleur doit renvoyer { status: true|false, message: '', resetid: '' */
    constructor(element) {
        element.parentNode.appendChild(spinner);

        const formData = new FormData()
        formData.set('id', element.dataset.id)
        formData.set('structure', element.dataset.structure)
        formData.set('action', element.dataset.action)
        formData.set('value', element.value)

        fetch(element.dataset.url, {
            method: 'POST',
            body: formData
        })
            .then(response => {
                if (response.ok) {
                    return response.json()
                } else {
                    throw new Error('Erreur lors de la requête');
                }
            })
            .then(data => {

                element.parentNode.removeChild(spinner)
                if (data.status === false) {
                    if (data.hasOwnProperty('resetid')) {
                        element.value = data.resetid
                    }
                    alert(data.message)
                }
            })
            .catch(error => {
                console.log(error);
            });
    }
}

export class FrontToggle {
    constructor(element) {
        element.appendChild(spinner);

        const formData = new FormData()
        formData.set('id', element.dataset.id)

        fetch(element.dataset.url, {
            method: 'POST',
            body: formData
        })
            .then(response => {
                if (response.ok) {
                    return response.json()
                } else {
                    throw new Error('Erreur lors de la requête');
                }
            })
            .then(data => {
                element.removeChild(spinner)
                element.classList.remove('on')
                element.classList.remove('off')

                if (data.status === false) {
                    element.classList.add('off')
                } else {
                    element.classList.add('on')
                }
            })
            .catch(error => {
                console.log(error);
            });
    }
}
