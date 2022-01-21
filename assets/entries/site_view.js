const $ = require('jquery');
const Ajax = require('../scripts/ajax');
const Tree = require('../scripts/tree');
const Popup = require('../scripts/popup');
const Contextmenu = require('../scripts/contextmenu');

require('bootstrap');
require('@fortawesome/fontawesome-free/js/all.js');

// Modules
Ajax.ajax();
Tree.tree();
Popup.popup();
Contextmenu.contextmenu();

/* @TODO: Clean this up */
window.fileModifier = function() {
    let qs = document.querySelector("[data-name=type]");
    if (qs.children[2].checked)
    {
        document.querySelector("[data-name=url]").classList.remove("hidden");
        document.querySelector("[data-name=url]").children[0].required = true;
        document.querySelector("[data-name=file]").classList.add("hidden");
        document.querySelector("[data-name=file]").children[0].required = false;
    }
    if (qs.children[0].checked)
    {
        document.querySelector("[data-name=file]").classList.remove("hidden");
        document.querySelector("[data-name=file]").children[0].required = true;
        document.querySelector("[data-name=url]").classList.add("hidden");
        document.querySelector("[data-name=url]").children[0].required = false;
    }
}