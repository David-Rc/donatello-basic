function addClass(el, className) {
    if (el)
        el.classList.add(className);
}

function rmClass(el, className) {
    if (el)
        el.classList.remove(className);
}

function addRemoveClass(target, addClass, rmClass) {
    if (target) {
        target.classList.add(addClass);
        target.classList.remove(rmClass);
    } else
        console.log('no target to addRemoveClass...');
}

/**
 * applique une liste de style à un element
 * @param el
 * @param values
 */
function style(el, values) {
    for (var p in values) {
        el.style[p] = values[p];
    }
}

/**
 * set le z-index
 * @param el
 * @param newIndex
 */
function zId(el, newIndex) {
    el.style.zIndex = newIndex;
}

function byId(elId) {
    return document.getElementById(elId)
}


function byClass(className) {
    var elements = document.getElementsByClassName(className);
    return Array.prototype.slice.call(elements);
}


function hide(el) {
    el.style.display = 'none';
}

function hidden(elId,value) {
    var op = value ? addClass : rmClass;
    if( typeof elId  === "string" )
        op(byId(elId), 'hidden');
    else
        op(elId, 'hidden');

}

/**
 * renvoie <a href="">...</a>
 * @param text
 * @param link
 * @returns {Element}
 */
function href(text, link) {
    var a = document.createElement('a');
    a.setAttribute('href', link);
    a.textContent = text;
    return a;
}

function hrev(text, callback) {
    var a = document.createElement('a');
    a.setAttribute('href', '#');
    a.addEventListener('click', callback);
    a.textContent = text;
    return a;
}

function removeChildren(container) {
    [].slice.call(container.children).forEach(function (item) {
            container.removeChild(item);
        }
    )
}

function children(parent){
    return [].slice.call(parent.children);
}

function mapChild(parent, action){
    return child(parent).map(action);
}

function _el(tag) {
    return document.createElement(tag);
}

function tNode(text) {
    return document.createTextNode(text);
}

/**
 * renvoie couleur aléatoire
 * @returns {string}
 */
function randColr() {
    return '#' + Math.floor(Math.random() * 16777215).toString(16);
}

function guid() {
    function s4() {
        return Math.floor((1 + Math.random()) * 0x10000)
            .toString(16)
            .substring(1);
    }

    return s4() + s4() + '-' + s4() + '-' + s4() + '-' +
        s4() + '-' + s4() + s4() + s4();
}

/* DATA ******************/
function setData(el, dataField, value) {
    el.setAttribute("data-" + dataField.toLowerCase(), value);
}

function getData(el, dataField) {
    return el.getAttribute("data-" + dataField.toLowerCase());
}