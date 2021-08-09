import 'whatwg-fetch';

/*
* isNan Polyfill
* */
Number.isNaN = Number.isNaN || function(value) {
    return typeof value === 'number' && isNaN(value);
}

/*
* Element.matches + Element.closest Polyfill
* */
var ElementPrototype = window.Element.prototype;
if (typeof ElementPrototype.matches !== 'function') {
    ElementPrototype.matches = ElementPrototype.msMatchesSelector || ElementPrototype.mozMatchesSelector || ElementPrototype.webkitMatchesSelector || function matches(selector) {
        var element = this;
        var elements = (element.document || element.ownerDocument).querySelectorAll(selector);
        var index = 0;
        while (elements[index] && elements[index] !== element) {
            ++index;
        }
        return Boolean(elements[index]);
    };
}
if (typeof ElementPrototype.closest !== 'function') {
    ElementPrototype.closest = function closest(selector) {
        var element = this;
        while (element && element.nodeType === 1) {
            if (element.matches(selector)) {
                return element;
            }
            element = element.parentNode;
        }
        return null;
    };
}

/*
* String.repeat Polyfill
* */
if (!String.prototype.repeat) {
    String.prototype.repeat = function(count) {
        'use strict';
        if (this == null) {
            throw new TypeError('can\'t convert ' + this + ' to object');
        }
        var str = '' + this;
        count = +count;
        if (count != count) {
            count = 0;
        }
        if (count < 0) {
            throw new RangeError('repeat count must be non-negative');
        }
        if (count == Infinity) {
            throw new RangeError('repeat count must be less than infinity');
        }
        count = Math.floor(count);
        if (str.length == 0 || count == 0) {
            return '';
        }
        // Обеспечение того, что count является 31-битным целым числом, позволяет нам значительно
        // соптимизировать главную часть функции. Впрочем, большинство современных (на август
        // 2014 года) браузеров не обрабатывают строки, длиннее 1 << 28 символов, так что:
        if (str.length * count >= 1 << 28) {
            throw new RangeError('repeat count must not overflow maximum string size');
        }
        var rpt = '';
        for (var i = 0; i < count; i++) {
            rpt += str;
        }
        return rpt;
    }
}

/*
* Object.assign Polyfill
* */
if (typeof Object.assign !== 'function') {
    // Must be writable: true, enumerable: false, configurable: true
    Object.defineProperty(Object, "assign", {
        value: function assign(target, varArgs) { // .length of function is 2
            'use strict';
            if (target === null || target === undefined) {
                throw new TypeError('Cannot convert undefined or null to object');
            }

            var to = Object(target);

            for (var index = 1; index < arguments.length; index++) {
                var nextSource = arguments[index];

                if (nextSource !== null && nextSource !== undefined) {
                    for (var nextKey in nextSource) {
                        // Avoid bugs when hasOwnProperty is shadowed
                        if (Object.prototype.hasOwnProperty.call(nextSource, nextKey)) {
                            to[nextKey] = nextSource[nextKey];
                        }
                    }
                }
            }
            return to;
        },
        writable: true,
        configurable: true
    });
}

/*
* Array.find Polyfill
* */
if (!Array.prototype.find) {
    Object.defineProperty(Array.prototype, 'find', {
        value: function(predicate) {
            // 1. Let O be ? ToObject(this value).
            if (this == null) {
                throw new TypeError('"this" is null or not defined');
            }

            var o = Object(this);

            // 2. Let len be ? ToLength(? Get(O, "length")).
            var len = o.length >>> 0;

            // 3. If IsCallable(predicate) is false, throw a TypeError exception.
            if (typeof predicate !== 'function') {
                throw new TypeError('predicate must be a function');
            }

            // 4. If thisArg was supplied, let T be thisArg; else let T be undefined.
            var thisArg = arguments[1];

            // 5. Let k be 0.
            var k = 0;

            // 6. Repeat, while k < len
            while (k < len) {
                // a. Let Pk be ! ToString(k).
                // b. Let kValue be ? Get(O, Pk).
                // c. Let testResult be ToBoolean(? Call(predicate, T, « kValue, k, O »)).
                // d. If testResult is true, return kValue.
                var kValue = o[k];
                if (predicate.call(thisArg, kValue, k, o)) {
                    return kValue;
                }
                // e. Increase k by 1.
                k++;
            }

            // 7. Return undefined.
            return undefined;
        },
        configurable: true,
        writable: true
    });
}

/*
* CustomEvent Polyfill
* */
(function () {
    if (typeof window.CustomEvent === "function") return false;

    function CustomEvent(event, params) {
        params = params || {bubbles: false, cancelable: false, detail: null};
        var evt = document.createEvent('CustomEvent');
        evt.initCustomEvent(event, params.bubbles, params.cancelable, params.detail);
        return evt;
    }

    window.CustomEvent = CustomEvent;
})();
