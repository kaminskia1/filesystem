
const $ = require('jquery');
const Ajax = require('../scripts/ajax');
const Tree = require('../scripts/tree');
const Popup = require('../scripts/popup');

import Vue from "vue";
import VueSimpleContextMenu from "vue-simple-context-menu";

require('bootstrap');
require('@fortawesome/fontawesome-free/js/all.js');

// Modules
Ajax.ajax();
Tree.tree();
Popup.register();

Vue.component("vue-simple-context-menu", VueSimpleContextMenu);
console.log(com);