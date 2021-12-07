const ajax = require ('../scripts/ajax');
const extendTree = require('../scripts/extendTree');

ajax.ajax();
extendTree.extendTree(Array.from(document.getElementsByClassName("explorer-caret")), ajax)