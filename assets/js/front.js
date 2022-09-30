//load sccs file
require('../scss/front.scss');

require('../../node_modules/tinymce/tinymce');
require('../../node_modules/tinymce/themes/silver/index');
require('../../node_modules/tinymce/icons/default/index');

import '@popperjs/core'
import 'bootstrap';


//init
tinymce.init({
    selector: 'textarea.wysiwyg',
    skin: 'silver'
})

window.addEventListener('load', () => {
    let searchAddress = document.querySelector('#registration_form_address');

    if(null !== searchAddress) {
        let addrSelect = document.querySelector('#address_results');
        let url = addrSelect.getAttribute('data-url');

        searchAddress.addEventListener('change', () => {

            let xhttp = new XMLHttpRequest();
            xhttp.open('GET', url + "?search=" + searchAddress.value);

            xhttp.onload = () => {
                let results = JSON.parse(xhttp.responseText);
                console.log(results);
                addrSelect.innerHTML = ""; // reset select

                for (const address of results.features) {
                    let option = document.createElement('option');
                    option.innerHTML = option.value = address.properties.label;
                    addrSelect.appendChild(option);
                }
            };
            xhttp.send();
        });

        addrSelect.addEventListener('change', () => {
            searchAddress.value = addrSelect.value;
        })
    }
});