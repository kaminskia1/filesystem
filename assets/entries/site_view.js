const $ = require('jquery');
const Ajax = require('../scripts/ajax');
const Tree = require('../scripts/tree');
const Popup = require('../scripts/popup');

require('bootstrap');
require('@fortawesome/fontawesome-free/js/all.js');

// Modules
Ajax.ajax();
Tree.tree();
Popup.register();