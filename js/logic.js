/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 266);
/******/ })
/************************************************************************/
/******/ ({

/***/ 266:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


__webpack_require__(380);
__webpack_require__(381);
__webpack_require__(382);
__webpack_require__(383);
__webpack_require__(384);

var addRuleTypeCategory = BBLogic.api.addRuleTypeCategory;
var __ = BBLogic.i18n.__;


addRuleTypeCategory('metabox', {
	label: __('Meta Box')
});

/***/ }),

/***/ 380:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _BBLogic$api = BBLogic.api,
    addRuleType = _BBLogic$api.addRuleType,
    getFormPreset = _BBLogic$api.getFormPreset;
var __ = BBLogic.i18n.__;


addRuleType('metabox/archive-field', {
	label: __('Archive Field'),
	category: 'metabox',
	form: getFormPreset('key-value')
});

/***/ }),

/***/ 381:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _BBLogic$api = BBLogic.api,
    addRuleType = _BBLogic$api.addRuleType,
    getFormPreset = _BBLogic$api.getFormPreset;
var __ = BBLogic.i18n.__;


addRuleType('metabox/post-field', {
	label: __('Post Field'),
	category: 'metabox',
	form: getFormPreset('key-value')
});

/***/ }),

/***/ 382:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _BBLogic$api = BBLogic.api,
    addRuleType = _BBLogic$api.addRuleType,
    getFormPreset = _BBLogic$api.getFormPreset;
var __ = BBLogic.i18n.__;


addRuleType('metabox/post-author-field', {
	label: __('Post Author Field'),
	category: 'metabox',
	form: getFormPreset('key-value')
});

/***/ }),

/***/ 383:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _BBLogic$api = BBLogic.api,
    addRuleType = _BBLogic$api.addRuleType,
    getFormPreset = _BBLogic$api.getFormPreset;
var __ = BBLogic.i18n.__;


addRuleType('metabox/user-field', {
	label: __('User Field'),
	category: 'metabox',
	form: getFormPreset('key-value')
});

/***/ }),

/***/ 384:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _BBLogic$api = BBLogic.api,
    addRuleType = _BBLogic$api.addRuleType,
    getFormPreset = _BBLogic$api.getFormPreset;
var __ = BBLogic.i18n.__;


addRuleType('metabox/settings-page-field', {
	label: __('Settings Page Field'),
	category: 'metabox',
	form: function( props ) {
		var operator = props.rule.operator
		return {
			key: {
				type: 'text',
				placeholder: 'Key',
			},
			operator: {
				type: 'operator',
				operators: [
					'equals',
					'does_not_equal',
					'is_set',
				],
			},
			compare: {
				type: 'text',
				placeholder: 'Value',
				visible: 'is_set' !== operator,
			},
			option_name: {
				type: 'text',
				placeholder: 'Option name',
			},
		}
	}
});
/***/ }),

/******/ });