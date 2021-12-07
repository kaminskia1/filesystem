const $ = require('jquery');
const ajax = require('../scripts/ajax');
const extendTree = require('../scripts/extendTree');
const Swal = require('sweetalert2');

require('@fortawesome/fontawesome-free/js/all.js');

ajax.ajax();
extendTree.extendTree(ajax);

let cache;
$("#login").click((a) => {
    if (cache == null) {
        fetch(a.currentTarget.getAttribute('data-url'))
            .then(response => response.text())
            .then(response => {
                cache = response
                Swal.fire({
                    title: 'Login',
                    html: cache,
                    showCancelButton: false,
                    showConfirmButton: false,
                })
            })
    } else {
        Swal.fire({
            title: 'Login',
            html: cache,
            showCancelButton: false,
            showConfirmButton: false,
        })
    }
});