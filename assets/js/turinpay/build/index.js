/**
 * @license
 * Copyright (c) 2017 The Polymer Project Authors. All rights reserved.
 * This code may only be used under the BSD style license found at
 * http://polymer.github.io/LICENSE.txt
 * The complete set of authors may be found at
 * http://polymer.github.io/AUTHORS.txt
 * The complete set of contributors may be found at
 * http://polymer.github.io/CONTRIBUTORS.txt
 * Code distributed by Google as part of the polymer project is also
 * subject to an additional IP rights grant found at
 * http://polymer.github.io/PATENTS.txt
 */
const t="undefined"!=typeof window&&null!=window.customElements&&void 0!==window.customElements.polyfillWrapFlushCallback,e=(t,e,i=null)=>{for(;e!==i;){const i=e.nextSibling;t.removeChild(e),e=i}},i=`{{lit-${String(Math.random()).slice(2)}}}`,s=`\x3c!--${i}--\x3e`,n=new RegExp(`${i}|${s}`);class o{constructor(t,e){this.parts=[],this.element=e;const s=[],o=[],a=document.createTreeWalker(e.content,133,null,!1);let l=0,c=-1,d=0;const{strings:h,values:{length:p}}=t;for(;d<p;){const t=a.nextNode();if(null!==t){if(c++,1===t.nodeType){if(t.hasAttributes()){const e=t.attributes,{length:i}=e;let s=0;for(let t=0;t<i;t++)r(e[t].name,"$lit$")&&s++;for(;s-- >0;){const e=h[d],i=M.exec(e)[2],s=i.toLowerCase()+"$lit$",o=t.getAttribute(s);t.removeAttribute(s);const r=o.split(n);this.parts.push({type:"attribute",index:c,name:i,strings:r}),d+=r.length-1}}"TEMPLATE"===t.tagName&&(o.push(t),a.currentNode=t.content)}else if(3===t.nodeType){const e=t.data;if(e.indexOf(i)>=0){const i=t.parentNode,o=e.split(n),a=o.length-1;for(let e=0;e<a;e++){let s,n=o[e];if(""===n)s=u();else{const t=M.exec(n);null!==t&&r(t[2],"$lit$")&&(n=n.slice(0,t.index)+t[1]+t[2].slice(0,-"$lit$".length)+t[3]),s=document.createTextNode(n)}i.insertBefore(s,t),this.parts.push({type:"node",index:++c})}""===o[a]?(i.insertBefore(u(),t),s.push(t)):t.data=o[a],d+=a}}else if(8===t.nodeType)if(t.data===i){const e=t.parentNode;null!==t.previousSibling&&c!==l||(c++,e.insertBefore(u(),t)),l=c,this.parts.push({type:"node",index:c}),null===t.nextSibling?t.data="":(s.push(t),c--),d++}else{let e=-1;for(;-1!==(e=t.data.indexOf(i,e+1));)this.parts.push({type:"node",index:-1}),d++}}else a.currentNode=o.pop()}for(const t of s)t.parentNode.removeChild(t)}}const r=(t,e)=>{const i=t.length-e.length;return i>=0&&t.slice(i)===e},a=t=>-1!==t.index,u=()=>document.createComment(""),M=/([ \x09\x0a\x0c\x0d])([^\0-\x1F\x7F-\x9F "'>=/]+)([ \x09\x0a\x0c\x0d]*=[ \x09\x0a\x0c\x0d]*(?:[^ \x09\x0a\x0c\x0d"'`<>=]*|"[^"]*|'[^']*))$/;function l(t,e){const{element:{content:i},parts:s}=t,n=document.createTreeWalker(i,133,null,!1);let o=d(s),r=s[o],a=-1,u=0;const M=[];let l=null;for(;n.nextNode();){a++;const t=n.currentNode;for(t.previousSibling===l&&(l=null),e.has(t)&&(M.push(t),null===l&&(l=t)),null!==l&&u++;void 0!==r&&r.index===a;)r.index=null!==l?-1:r.index-u,o=d(s,o),r=s[o]}M.forEach((t=>t.parentNode.removeChild(t)))}const c=t=>{let e=11===t.nodeType?0:1;const i=document.createTreeWalker(t,133,null,!1);for(;i.nextNode();)e++;return e},d=(t,e=-1)=>{for(let i=e+1;i<t.length;i++){const e=t[i];if(a(e))return i}return-1};
/**
 * @license
 * Copyright (c) 2017 The Polymer Project Authors. All rights reserved.
 * This code may only be used under the BSD style license found at
 * http://polymer.github.io/LICENSE.txt
 * The complete set of authors may be found at
 * http://polymer.github.io/AUTHORS.txt
 * The complete set of contributors may be found at
 * http://polymer.github.io/CONTRIBUTORS.txt
 * Code distributed by Google as part of the polymer project is also
 * subject to an additional IP rights grant found at
 * http://polymer.github.io/PATENTS.txt
 */
const h=new WeakMap,p=t=>"function"==typeof t&&h.has(t),N={},j={};
/**
 * @license
 * Copyright (c) 2017 The Polymer Project Authors. All rights reserved.
 * This code may only be used under the BSD style license found at
 * http://polymer.github.io/LICENSE.txt
 * The complete set of authors may be found at
 * http://polymer.github.io/AUTHORS.txt
 * The complete set of contributors may be found at
 * http://polymer.github.io/CONTRIBUTORS.txt
 * Code distributed by Google as part of the polymer project is also
 * subject to an additional IP rights grant found at
 * http://polymer.github.io/PATENTS.txt
 */
class y{constructor(t,e,i){this.__parts=[],this.template=t,this.processor=e,this.options=i}update(t){let e=0;for(const i of this.__parts)void 0!==i&&i.setValue(t[e]),e++;for(const t of this.__parts)void 0!==t&&t.commit()}_clone(){const e=t?this.template.element.content.cloneNode(!0):document.importNode(this.template.element.content,!0),i=[],s=this.template.parts,n=document.createTreeWalker(e,133,null,!1);let o,r=0,u=0,M=n.nextNode();for(;r<s.length;)if(o=s[r],a(o)){for(;u<o.index;)u++,"TEMPLATE"===M.nodeName&&(i.push(M),n.currentNode=M.content),null===(M=n.nextNode())&&(n.currentNode=i.pop(),M=n.nextNode());if("node"===o.type){const t=this.processor.handleTextExpression(this.options);t.insertAfterNode(M.previousSibling),this.__parts.push(t)}else this.__parts.push(...this.processor.handleAttributeExpressions(M,o.name,o.strings,this.options));r++}else this.__parts.push(void 0),r++;return t&&(document.adoptNode(e),customElements.upgrade(e)),e}}
/**
 * @license
 * Copyright (c) 2017 The Polymer Project Authors. All rights reserved.
 * This code may only be used under the BSD style license found at
 * http://polymer.github.io/LICENSE.txt
 * The complete set of authors may be found at
 * http://polymer.github.io/AUTHORS.txt
 * The complete set of contributors may be found at
 * http://polymer.github.io/CONTRIBUTORS.txt
 * Code distributed by Google as part of the polymer project is also
 * subject to an additional IP rights grant found at
 * http://polymer.github.io/PATENTS.txt
 */const L=window.trustedTypes&&trustedTypes.createPolicy("lit-html",{createHTML:t=>t}),g=` ${i} `;class S{constructor(t,e,i,s){this.strings=t,this.values=e,this.type=i,this.processor=s}getHTML(){const t=this.strings.length-1;let e="",n=!1;for(let o=0;o<t;o++){const t=this.strings[o],r=t.lastIndexOf("\x3c!--");n=(r>-1||n)&&-1===t.indexOf("--\x3e",r+1);const a=M.exec(t);e+=null===a?t+(n?g:s):t.substr(0,a.index)+a[1]+a[2]+"$lit$"+a[3]+i}return e+=this.strings[t],e}getTemplateElement(){const t=document.createElement("template");let e=this.getHTML();return void 0!==L&&(e=L.createHTML(e)),t.innerHTML=e,t}}
/**
 * @license
 * Copyright (c) 2017 The Polymer Project Authors. All rights reserved.
 * This code may only be used under the BSD style license found at
 * http://polymer.github.io/LICENSE.txt
 * The complete set of authors may be found at
 * http://polymer.github.io/AUTHORS.txt
 * The complete set of contributors may be found at
 * http://polymer.github.io/CONTRIBUTORS.txt
 * Code distributed by Google as part of the polymer project is also
 * subject to an additional IP rights grant found at
 * http://polymer.github.io/PATENTS.txt
 */const I=t=>null===t||!("object"==typeof t||"function"==typeof t),T=t=>Array.isArray(t)||!(!t||!t[Symbol.iterator]);class m{constructor(t,e,i){this.dirty=!0,this.element=t,this.name=e,this.strings=i,this.parts=[];for(let t=0;t<i.length-1;t++)this.parts[t]=this._createPart()}_createPart(){return new z(this)}_getValue(){const t=this.strings,e=t.length-1,i=this.parts;if(1===e&&""===t[0]&&""===t[1]){const t=i[0].value;if("symbol"==typeof t)return String(t);if("string"==typeof t||!T(t))return t}let s="";for(let n=0;n<e;n++){s+=t[n];const e=i[n];if(void 0!==e){const t=e.value;if(I(t)||!T(t))s+="string"==typeof t?t:String(t);else for(const e of t)s+="string"==typeof e?e:String(e)}}return s+=t[e],s}commit(){this.dirty&&(this.dirty=!1,this.element.setAttribute(this.name,this._getValue()))}}class z{constructor(t){this.value=void 0,this.committer=t}setValue(t){t===N||I(t)&&t===this.value||(this.value=t,p(t)||(this.committer.dirty=!0))}commit(){for(;p(this.value);){const t=this.value;this.value=N,t(this)}this.value!==N&&this.committer.commit()}}class D{constructor(t){this.value=void 0,this.__pendingValue=void 0,this.options=t}appendInto(t){this.startNode=t.appendChild(u()),this.endNode=t.appendChild(u())}insertAfterNode(t){this.startNode=t,this.endNode=t.nextSibling}appendIntoPart(t){t.__insert(this.startNode=u()),t.__insert(this.endNode=u())}insertAfterPart(t){t.__insert(this.startNode=u()),this.endNode=t.endNode,t.endNode=this.startNode}setValue(t){this.__pendingValue=t}commit(){if(null===this.startNode.parentNode)return;for(;p(this.__pendingValue);){const t=this.__pendingValue;this.__pendingValue=N,t(this)}const t=this.__pendingValue;t!==N&&(I(t)?t!==this.value&&this.__commitText(t):t instanceof S?this.__commitTemplateResult(t):t instanceof Node?this.__commitNode(t):T(t)?this.__commitIterable(t):t===j?(this.value=j,this.clear()):this.__commitText(t))}__insert(t){this.endNode.parentNode.insertBefore(t,this.endNode)}__commitNode(t){this.value!==t&&(this.clear(),this.__insert(t),this.value=t)}__commitText(t){const e=this.startNode.nextSibling,i="string"==typeof(t=null==t?"":t)?t:String(t);e===this.endNode.previousSibling&&3===e.nodeType?e.data=i:this.__commitNode(document.createTextNode(i)),this.value=t}__commitTemplateResult(t){const e=this.options.templateFactory(t);if(this.value instanceof y&&this.value.template===e)this.value.update(t.values);else{const i=new y(e,t.processor,this.options),s=i._clone();i.update(t.values),this.__commitNode(s),this.value=i}}__commitIterable(t){Array.isArray(this.value)||(this.value=[],this.clear());const e=this.value;let i,s=0;for(const n of t)i=e[s],void 0===i&&(i=new D(this.options),e.push(i),0===s?i.appendIntoPart(this):i.insertAfterPart(e[s-1])),i.setValue(n),i.commit(),s++;s<e.length&&(e.length=s,this.clear(i&&i.endNode))}clear(t=this.startNode){e(this.startNode.parentNode,t.nextSibling,this.endNode)}}class C{constructor(t,e,i){if(this.value=void 0,this.__pendingValue=void 0,2!==i.length||""!==i[0]||""!==i[1])throw new Error("Boolean attributes can only contain a single expression");this.element=t,this.name=e,this.strings=i}setValue(t){this.__pendingValue=t}commit(){for(;p(this.__pendingValue);){const t=this.__pendingValue;this.__pendingValue=N,t(this)}if(this.__pendingValue===N)return;const t=!!this.__pendingValue;this.value!==t&&(t?this.element.setAttribute(this.name,""):this.element.removeAttribute(this.name),this.value=t),this.__pendingValue=N}}class x extends m{constructor(t,e,i){super(t,e,i),this.single=2===i.length&&""===i[0]&&""===i[1]}_createPart(){return new w(this)}_getValue(){return this.single?this.parts[0].value:super._getValue()}commit(){this.dirty&&(this.dirty=!1,this.element[this.name]=this._getValue())}}class w extends z{}let A=!1;(()=>{try{const t={get capture(){return A=!0,!1}};window.addEventListener("test",t,t),window.removeEventListener("test",t,t)}catch(t){}})();class E{constructor(t,e,i){this.value=void 0,this.__pendingValue=void 0,this.element=t,this.eventName=e,this.eventContext=i,this.__boundHandleEvent=t=>this.handleEvent(t)}setValue(t){this.__pendingValue=t}commit(){for(;p(this.__pendingValue);){const t=this.__pendingValue;this.__pendingValue=N,t(this)}if(this.__pendingValue===N)return;const t=this.__pendingValue,e=this.value,i=null==t||null!=e&&(t.capture!==e.capture||t.once!==e.once||t.passive!==e.passive),s=null!=t&&(null==e||i);i&&this.element.removeEventListener(this.eventName,this.__boundHandleEvent,this.__options),s&&(this.__options=b(t),this.element.addEventListener(this.eventName,this.__boundHandleEvent,this.__options)),this.value=t,this.__pendingValue=N}handleEvent(t){"function"==typeof this.value?this.value.call(this.eventContext||this.element,t):this.value.handleEvent(t)}}const b=t=>t&&(A?{capture:t.capture,passive:t.passive,once:t.once}:t.capture)
/**
 * @license
 * Copyright (c) 2017 The Polymer Project Authors. All rights reserved.
 * This code may only be used under the BSD style license found at
 * http://polymer.github.io/LICENSE.txt
 * The complete set of authors may be found at
 * http://polymer.github.io/AUTHORS.txt
 * The complete set of contributors may be found at
 * http://polymer.github.io/CONTRIBUTORS.txt
 * Code distributed by Google as part of the polymer project is also
 * subject to an additional IP rights grant found at
 * http://polymer.github.io/PATENTS.txt
 */;function O(t){let e=f.get(t.type);void 0===e&&(e={stringsArray:new WeakMap,keyString:new Map},f.set(t.type,e));let s=e.stringsArray.get(t.strings);if(void 0!==s)return s;const n=t.strings.join(i);return s=e.keyString.get(n),void 0===s&&(s=new o(t,t.getTemplateElement()),e.keyString.set(n,s)),e.stringsArray.set(t.strings,s),s}const f=new Map,v=new WeakMap;
/**
 * @license
 * Copyright (c) 2017 The Polymer Project Authors. All rights reserved.
 * This code may only be used under the BSD style license found at
 * http://polymer.github.io/LICENSE.txt
 * The complete set of authors may be found at
 * http://polymer.github.io/AUTHORS.txt
 * The complete set of contributors may be found at
 * http://polymer.github.io/CONTRIBUTORS.txt
 * Code distributed by Google as part of the polymer project is also
 * subject to an additional IP rights grant found at
 * http://polymer.github.io/PATENTS.txt
 */const _=new
/**
 * @license
 * Copyright (c) 2017 The Polymer Project Authors. All rights reserved.
 * This code may only be used under the BSD style license found at
 * http://polymer.github.io/LICENSE.txt
 * The complete set of authors may be found at
 * http://polymer.github.io/AUTHORS.txt
 * The complete set of contributors may be found at
 * http://polymer.github.io/CONTRIBUTORS.txt
 * Code distributed by Google as part of the polymer project is also
 * subject to an additional IP rights grant found at
 * http://polymer.github.io/PATENTS.txt
 */
class{handleAttributeExpressions(t,e,i,s){const n=e[0];if("."===n){return new x(t,e.slice(1),i).parts}if("@"===n)return[new E(t,e.slice(1),s.eventContext)];if("?"===n)return[new C(t,e.slice(1),i)];return new m(t,e,i).parts}handleTextExpression(t){return new D(t)}};
/**
 * @license
 * Copyright (c) 2017 The Polymer Project Authors. All rights reserved.
 * This code may only be used under the BSD style license found at
 * http://polymer.github.io/LICENSE.txt
 * The complete set of authors may be found at
 * http://polymer.github.io/AUTHORS.txt
 * The complete set of contributors may be found at
 * http://polymer.github.io/CONTRIBUTORS.txt
 * Code distributed by Google as part of the polymer project is also
 * subject to an additional IP rights grant found at
 * http://polymer.github.io/PATENTS.txt
 */"undefined"!=typeof window&&(window.litHtmlVersions||(window.litHtmlVersions=[])).push("1.4.1");const Y=(t,...e)=>new S(t,e,"html",_)
/**
 * @license
 * Copyright (c) 2017 The Polymer Project Authors. All rights reserved.
 * This code may only be used under the BSD style license found at
 * http://polymer.github.io/LICENSE.txt
 * The complete set of authors may be found at
 * http://polymer.github.io/AUTHORS.txt
 * The complete set of contributors may be found at
 * http://polymer.github.io/CONTRIBUTORS.txt
 * Code distributed by Google as part of the polymer project is also
 * subject to an additional IP rights grant found at
 * http://polymer.github.io/PATENTS.txt
 */,U=(t,e)=>`${t}--${e}`;let k=!0;void 0===window.ShadyCSS?k=!1:void 0===window.ShadyCSS.prepareTemplateDom&&(console.warn("Incompatible ShadyCSS version detected. Please update to at least @webcomponents/webcomponentsjs@2.0.2 and @webcomponents/shadycss@1.3.1."),k=!1);const Q=t=>e=>{const s=U(e.type,t);let n=f.get(s);void 0===n&&(n={stringsArray:new WeakMap,keyString:new Map},f.set(s,n));let r=n.stringsArray.get(e.strings);if(void 0!==r)return r;const a=e.strings.join(i);if(r=n.keyString.get(a),void 0===r){const i=e.getTemplateElement();k&&window.ShadyCSS.prepareTemplateDom(i,t),r=new o(e,i),n.keyString.set(a,r)}return n.stringsArray.set(e.strings,r),r},P=["html","svg"],V=new Set,R=(t,e,i)=>{V.add(t);const s=i?i.element:document.createElement("template"),n=e.querySelectorAll("style"),{length:o}=n;if(0===o)return void window.ShadyCSS.prepareTemplateStyles(s,t);const r=document.createElement("style");for(let t=0;t<o;t++){const e=n[t];e.parentNode.removeChild(e),r.textContent+=e.textContent}(t=>{P.forEach((e=>{const i=f.get(U(e,t));void 0!==i&&i.keyString.forEach((t=>{const{element:{content:e}}=t,i=new Set;Array.from(e.querySelectorAll("style")).forEach((t=>{i.add(t)})),l(t,i)}))}))})(t);const a=s.content;i?function(t,e,i=null){const{element:{content:s},parts:n}=t;if(null==i)return void s.appendChild(e);const o=document.createTreeWalker(s,133,null,!1);let r=d(n),a=0,u=-1;for(;o.nextNode();)for(u++,o.currentNode===i&&(a=c(e),i.parentNode.insertBefore(e,i));-1!==r&&n[r].index===u;){if(a>0){for(;-1!==r;)n[r].index+=a,r=d(n,r);return}r=d(n,r)}}(i,r,a.firstChild):a.insertBefore(r,a.firstChild),window.ShadyCSS.prepareTemplateStyles(s,t);const u=a.querySelector("style");if(window.ShadyCSS.nativeShadow&&null!==u)e.insertBefore(u.cloneNode(!0),e.firstChild);else if(i){a.insertBefore(r,a.firstChild);const t=new Set;t.add(r),l(i,t)}};window.JSCompiler_renameProperty=(t,e)=>t;const G={toAttribute(t,e){switch(e){case Boolean:return t?"":null;case Object:case Array:return null==t?t:JSON.stringify(t)}return t},fromAttribute(t,e){switch(e){case Boolean:return null!==t;case Number:return null===t?null:Number(t);case Object:case Array:return JSON.parse(t)}return t}},W=(t,e)=>e!==t&&(e==e||t==t),Z={attribute:!0,type:String,converter:G,reflect:!1,hasChanged:W};class H extends HTMLElement{constructor(){super(),this.initialize()}static get observedAttributes(){this.finalize();const t=[];return this._classProperties.forEach(((e,i)=>{const s=this._attributeNameForProperty(i,e);void 0!==s&&(this._attributeToPropertyMap.set(s,i),t.push(s))})),t}static _ensureClassProperties(){if(!this.hasOwnProperty(JSCompiler_renameProperty("_classProperties",this))){this._classProperties=new Map;const t=Object.getPrototypeOf(this)._classProperties;void 0!==t&&t.forEach(((t,e)=>this._classProperties.set(e,t)))}}static createProperty(t,e=Z){if(this._ensureClassProperties(),this._classProperties.set(t,e),e.noAccessor||this.prototype.hasOwnProperty(t))return;const i="symbol"==typeof t?Symbol():`__${t}`,s=this.getPropertyDescriptor(t,i,e);void 0!==s&&Object.defineProperty(this.prototype,t,s)}static getPropertyDescriptor(t,e,i){return{get(){return this[e]},set(s){const n=this[t];this[e]=s,this.requestUpdateInternal(t,n,i)},configurable:!0,enumerable:!0}}static getPropertyOptions(t){return this._classProperties&&this._classProperties.get(t)||Z}static finalize(){const t=Object.getPrototypeOf(this);if(t.hasOwnProperty("finalized")||t.finalize(),this.finalized=!0,this._ensureClassProperties(),this._attributeToPropertyMap=new Map,this.hasOwnProperty(JSCompiler_renameProperty("properties",this))){const t=this.properties,e=[...Object.getOwnPropertyNames(t),..."function"==typeof Object.getOwnPropertySymbols?Object.getOwnPropertySymbols(t):[]];for(const i of e)this.createProperty(i,t[i])}}static _attributeNameForProperty(t,e){const i=e.attribute;return!1===i?void 0:"string"==typeof i?i:"string"==typeof t?t.toLowerCase():void 0}static _valueHasChanged(t,e,i=W){return i(t,e)}static _propertyValueFromAttribute(t,e){const i=e.type,s=e.converter||G,n="function"==typeof s?s:s.fromAttribute;return n?n(t,i):t}static _propertyValueToAttribute(t,e){if(void 0===e.reflect)return;const i=e.type,s=e.converter;return(s&&s.toAttribute||G.toAttribute)(t,i)}initialize(){this._updateState=0,this._updatePromise=new Promise((t=>this._enableUpdatingResolver=t)),this._changedProperties=new Map,this._saveInstanceProperties(),this.requestUpdateInternal()}_saveInstanceProperties(){this.constructor._classProperties.forEach(((t,e)=>{if(this.hasOwnProperty(e)){const t=this[e];delete this[e],this._instanceProperties||(this._instanceProperties=new Map),this._instanceProperties.set(e,t)}}))}_applyInstanceProperties(){this._instanceProperties.forEach(((t,e)=>this[e]=t)),this._instanceProperties=void 0}connectedCallback(){this.enableUpdating()}enableUpdating(){void 0!==this._enableUpdatingResolver&&(this._enableUpdatingResolver(),this._enableUpdatingResolver=void 0)}disconnectedCallback(){}attributeChangedCallback(t,e,i){e!==i&&this._attributeToProperty(t,i)}_propertyToAttribute(t,e,i=Z){const s=this.constructor,n=s._attributeNameForProperty(t,i);if(void 0!==n){const t=s._propertyValueToAttribute(e,i);if(void 0===t)return;this._updateState=8|this._updateState,null==t?this.removeAttribute(n):this.setAttribute(n,t),this._updateState=-9&this._updateState}}_attributeToProperty(t,e){if(8&this._updateState)return;const i=this.constructor,s=i._attributeToPropertyMap.get(t);if(void 0!==s){const t=i.getPropertyOptions(s);this._updateState=16|this._updateState,this[s]=i._propertyValueFromAttribute(e,t),this._updateState=-17&this._updateState}}requestUpdateInternal(t,e,i){let s=!0;if(void 0!==t){const n=this.constructor;i=i||n.getPropertyOptions(t),n._valueHasChanged(this[t],e,i.hasChanged)?(this._changedProperties.has(t)||this._changedProperties.set(t,e),!0!==i.reflect||16&this._updateState||(void 0===this._reflectingProperties&&(this._reflectingProperties=new Map),this._reflectingProperties.set(t,i))):s=!1}!this._hasRequestedUpdate&&s&&(this._updatePromise=this._enqueueUpdate())}requestUpdate(t,e){return this.requestUpdateInternal(t,e),this.updateComplete}async _enqueueUpdate(){this._updateState=4|this._updateState;try{await this._updatePromise}catch(t){}const t=this.performUpdate();return null!=t&&await t,!this._hasRequestedUpdate}get _hasRequestedUpdate(){return 4&this._updateState}get hasUpdated(){return 1&this._updateState}performUpdate(){if(!this._hasRequestedUpdate)return;this._instanceProperties&&this._applyInstanceProperties();let t=!1;const e=this._changedProperties;try{t=this.shouldUpdate(e),t?this.update(e):this._markUpdated()}catch(e){throw t=!1,this._markUpdated(),e}t&&(1&this._updateState||(this._updateState=1|this._updateState,this.firstUpdated(e)),this.updated(e))}_markUpdated(){this._changedProperties=new Map,this._updateState=-5&this._updateState}get updateComplete(){return this._getUpdateComplete()}_getUpdateComplete(){return this.getUpdateComplete()}getUpdateComplete(){return this._updatePromise}shouldUpdate(t){return!0}update(t){void 0!==this._reflectingProperties&&this._reflectingProperties.size>0&&(this._reflectingProperties.forEach(((t,e)=>this._propertyToAttribute(e,this[e],t))),this._reflectingProperties=void 0),this._markUpdated()}updated(t){}firstUpdated(t){}}H.finalized=!0;
/**
@license
Copyright (c) 2019 The Polymer Project Authors. All rights reserved.
This code may only be used under the BSD style license found at
http://polymer.github.io/LICENSE.txt The complete set of authors may be found at
http://polymer.github.io/AUTHORS.txt The complete set of contributors may be
found at http://polymer.github.io/CONTRIBUTORS.txt Code distributed by Google as
part of the polymer project is also subject to an additional IP rights grant
found at http://polymer.github.io/PATENTS.txt
*/
const J=window.ShadowRoot&&(void 0===window.ShadyCSS||window.ShadyCSS.nativeShadow)&&"adoptedStyleSheets"in Document.prototype&&"replace"in CSSStyleSheet.prototype,B=Symbol();class F{constructor(t,e){if(e!==B)throw new Error("CSSResult is not constructable. Use `unsafeCSS` or `css` instead.");this.cssText=t}get styleSheet(){return void 0===this._styleSheet&&(J?(this._styleSheet=new CSSStyleSheet,this._styleSheet.replaceSync(this.cssText)):this._styleSheet=null),this._styleSheet}toString(){return this.cssText}}const $=(t,...e)=>{const i=e.reduce(((e,i,s)=>e+(t=>{if(t instanceof F)return t.cssText;if("number"==typeof t)return t;throw new Error(`Value passed to 'css' function must be a 'css' function result: ${t}. Use 'unsafeCSS' to pass non-literal values, but\n            take care to ensure page security.`)})(i)+t[s+1]),t[0]);return new F(i,B)};
/**
 * @license
 * Copyright (c) 2017 The Polymer Project Authors. All rights reserved.
 * This code may only be used under the BSD style license found at
 * http://polymer.github.io/LICENSE.txt
 * The complete set of authors may be found at
 * http://polymer.github.io/AUTHORS.txt
 * The complete set of contributors may be found at
 * http://polymer.github.io/CONTRIBUTORS.txt
 * Code distributed by Google as part of the polymer project is also
 * subject to an additional IP rights grant found at
 * http://polymer.github.io/PATENTS.txt
 */
(window.litElementVersions||(window.litElementVersions=[])).push("2.5.1");const X={};class q extends H{static getStyles(){return this.styles}static _getUniqueStyles(){if(this.hasOwnProperty(JSCompiler_renameProperty("_styles",this)))return;const t=this.getStyles();if(Array.isArray(t)){const e=(t,i)=>t.reduceRight(((t,i)=>Array.isArray(i)?e(i,t):(t.add(i),t)),i),i=e(t,new Set),s=[];i.forEach((t=>s.unshift(t))),this._styles=s}else this._styles=void 0===t?[]:[t];this._styles=this._styles.map((t=>{if(t instanceof CSSStyleSheet&&!J){const e=Array.prototype.slice.call(t.cssRules).reduce(((t,e)=>t+e.cssText),"");return new F(String(e),B)}return t}))}initialize(){super.initialize(),this.constructor._getUniqueStyles(),this.renderRoot=this.createRenderRoot(),window.ShadowRoot&&this.renderRoot instanceof window.ShadowRoot&&this.adoptStyles()}createRenderRoot(){return this.attachShadow(this.constructor.shadowRootOptions)}adoptStyles(){const t=this.constructor._styles;0!==t.length&&(void 0===window.ShadyCSS||window.ShadyCSS.nativeShadow?J?this.renderRoot.adoptedStyleSheets=t.map((t=>t instanceof CSSStyleSheet?t:t.styleSheet)):this._needsShimAdoptedStyleSheets=!0:window.ShadyCSS.ScopingShim.prepareAdoptedCssText(t.map((t=>t.cssText)),this.localName))}connectedCallback(){super.connectedCallback(),this.hasUpdated&&void 0!==window.ShadyCSS&&window.ShadyCSS.styleElement(this)}update(t){const e=this.render();super.update(t),e!==X&&this.constructor.render(e,this.renderRoot,{scopeName:this.localName,eventContext:this}),this._needsShimAdoptedStyleSheets&&(this._needsShimAdoptedStyleSheets=!1,this.constructor._styles.forEach((t=>{const e=document.createElement("style");e.textContent=t.cssText,this.renderRoot.appendChild(e)})))}render(){return X}}q.finalized=!0,q.render=(t,i,s)=>{if(!s||"object"!=typeof s||!s.scopeName)throw new Error("The `scopeName` option is required.");const n=s.scopeName,o=v.has(i),r=k&&11===i.nodeType&&!!i.host,a=r&&!V.has(n),u=a?document.createDocumentFragment():i;if(((t,i,s)=>{let n=v.get(i);void 0===n&&(e(i,i.firstChild),v.set(i,n=new D(Object.assign({templateFactory:O},s))),n.appendInto(i)),n.setValue(t),n.commit()})(t,u,Object.assign({templateFactory:Q(n)},s)),a){const t=v.get(u);v.delete(u);const s=t.value instanceof y?t.value.template:void 0;R(n,u,s),e(i,i.firstChild),i.appendChild(u),v.set(i,t)}!o&&r&&window.ShadyCSS.styleElement(i.host)},q.shadowRootOptions={mode:"open"};var K=Object.freeze({__proto__:null,PRO:"https://app.turinpay.com",STA:"https://sta.turinpay.com",INT:"https://int.turinpay.com"});const tt=!("undefined"==typeof window||!window.document),et=t=>(...e)=>{const i=(t=>{let e=0;for(let i=0;i<t.length;i++)e=(e<<5)-e+t.charCodeAt(i),e=Math.abs(e&e);return[(16711680&e)>>16,(65280&e)>>8,255&e]})(t),s=e=>{e?.includes(":*")&&t.startsWith(e.split(":*")[0])};tt?s(localStorage.getItem("debug"))&&console.log(t&&`%c${t}`,`color: rgb(${i[0]}, ${i[1]}, ${i[2]})`,...e):s(process.env.DEBUG)&&console.log(t&&couleur.bold(couleur.rgb(i[0],i[1],i[2])(t)),...e)};const it=et("pay:modal");customElements.define("turinpay-modal",class extends q{constructor(){super(),this.opened=!1,this.handleModalMessage=this.handleModalMessage.bind(this)}static get properties(){return{uri:{type:String},paymentIntent:{type:String},opened:{type:Boolean}}}static get styles(){return $`
      .modal {
        display: none; /* Hidden by default */
        position: fixed; /* Stay in place */
        z-index: 10000; /* Sit on top */
        left: 0;
        top: 0;
        width: 100%; /* Full width */
        height: 100%; /* Full height */
        overflow: auto; /* Enable scroll if needed */
      }

      .close {
        display: block;
        position: relative;
        z-index: 10100;
        left: calc( 100% - 5rem);
        top: 0;
        margin: 1rem;
        height: 3rem;
        width: 3rem;
        background-color: var( --pb-close-background, #fbfbfb );
        box-shadow: 2px 2px 7px 0px var( --pb-close-shadow, rgba(0,0,0,0.75) );
        border-radius: 100%;
        cursor: pointer;
      }

      .close:after {
        content: '';
        position: absolute;
        top:0;
        left:0;
        background-color: var( --pb-close-color, #0c8588 );
        border-radius: 0.5rem;
        height: 0.5rem;
        width: 100%;
        transform: translateY(20px) rotate(45deg) scale(0.8, 0.8);
      }

      .close:before {
        content: '';
        position: absolute;
        top:0;
        left:0;
        background-color: var( --pb-close-color, #0c8588 );
        border-radius: 0.5rem;
        height: 0.5rem;
        width: 100%;
        transform: translateY(20px) rotate(-45deg) scale(0.8, 0.8);
      }

      .modal.opened {
        display: block;
      }

      .modal-content {
        position: absolute;
        z-index: 10050;
        top: 0;
        left: 0;
        height: 100%;
        width: 100%;
        background-color: var( --pb-modal-background, rgba(255, 255, 255, 0.95) );
      }
      .modal-content iframe {
        position: absolute;
        top: 0;
        left: 0;
      }
      .modal-content img {
        position: absolute;
        width: 3.5rem;
        height: 3.5rem;
        top: calc(50% - 1.75rem);
        left: calc(50% - 1.75rem);
      }
    `}handleModalMessage(t){const e=t.data.paymentIntentId===this.paymentIntent;if(it(`> handleModalMessage ${this.paymentIntent} Completed? ${e}`,t.data),e){const e=new CustomEvent("invoice-completed",{detail:{completed:!0,paymentIntent:this.paymentIntent,invoiceId:t.data.invoiceId}});this.dispatchEvent(e),this.paymentIntent=void 0}}show({uri:t,paymentIntent:e}){it("> Show",t,e),this.uri=t,this.paymentIntent=e,this.opened=!0,window&&window.addEventListener("message",this.handleModalMessage,!1)}waiting(){it("> Waiting"),this.opened=!0}close(){it("> Close"),this.opened=!1,this.uri=void 0,this.paymentIntent=void 0,window&&window.removeEventListener("message",this.handleModalMessage)}render(){const t=this.uri?this.uri:"about:blank";return Y`
      <div class="modal ${this.opened?"opened":""}" @click="${this.close}">
        <span @click="${this.close}" class="close"></span>
        <div class="modal-content">
          <img
            src="${"data:image/svg+xml;base64,PHN2ZyB2ZXJzaW9uPSIxLjEiIGlkPSJMNyIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeD0iMHB4IiB5PSIwcHgiIHZpZXdCb3g9IjAgMCAxMDAgMTAwIiBlbmFibGUtYmFja2dyb3VuZD0ibmV3IDAgMCAxMDAgMTAwIiB4bWw6c3BhY2U9InByZXNlcnZlIj4KICA8cGF0aCBmaWxsPScjMGM4NTg4JyBkPSJNMzEuNiwzLjVDNS45LDEzLjYtNi42LDQyLjcsMy41LDY4LjRjMTAuMSwyNS43LDM5LjIsMzguMyw2NC45LDI4LjFsLTMuMS03LjljLTIxLjMsOC40LTQ1LjQtMi01My44LTIzLjNjLTguNC0yMS4zLDItNDUuNCwyMy4zLTUzLjhMMzEuNiwzLjV6Ij4KICAgIDxhbmltYXRlVHJhbnNmb3JtCiAgICAgIGF0dHJpYnV0ZU5hbWU9InRyYW5zZm9ybSIKICAgICAgYXR0cmlidXRlVHlwZT0iWE1MIgogICAgICB0eXBlPSJyb3RhdGUiCiAgICAgIGR1cj0iMnMiCiAgICAgIGZyb209IjAgNTAgNTAiCiAgICAgIHRvPSIzNjAgNTAgNTAiCiAgICAgIHJlcGVhdENvdW50PSJpbmRlZmluaXRlIiAvPgogIDwvcGF0aD4KICA8cGF0aCBmaWxsPScjMGM4NTg4JyBkPSJNNDIuMywzOS42YzUuNy00LjMsMTMuOS0zLjEsMTguMSwyLjdjNC4zLDUuNywzLjEsMTMuOS0yLjcsMTguMWw0LjEsNS41YzguOC02LjUsMTAuNi0xOSw0LjEtMjcuN2MtNi41LTguOC0xOS0xMC42LTI3LjctNC4xTDQyLjMsMzkuNnoiPgogICAgPGFuaW1hdGVUcmFuc2Zvcm0KICAgICAgYXR0cmlidXRlTmFtZT0idHJhbnNmb3JtIgogICAgICBhdHRyaWJ1dGVUeXBlPSJYTUwiCiAgICAgIHR5cGU9InJvdGF0ZSIKICAgICAgZHVyPSIxcyIKICAgICAgZnJvbT0iMCA1MCA1MCIKICAgICAgdG89Ii0zNjAgNTAgNTAiCiAgICAgIHJlcGVhdENvdW50PSJpbmRlZmluaXRlIiAvPgogIDwvcGF0aD4KICA8cGF0aCBmaWxsPScjMGM4NTg4JyBkPSJNODIsMzUuN0M3NC4xLDE4LDUzLjQsMTAuMSwzNS43LDE4UzEwLjEsNDYuNiwxOCw2NC4zbDcuNi0zLjRjLTYtMTMuNSwwLTI5LjMsMTMuNS0zNS4zczI5LjMsMCwzNS4zLDEzLjVMODIsMzUuN3oiPgogICAgPGFuaW1hdGVUcmFuc2Zvcm0KICAgICAgYXR0cmlidXRlTmFtZT0idHJhbnNmb3JtIgogICAgICBhdHRyaWJ1dGVUeXBlPSJYTUwiCiAgICAgIHR5cGU9InJvdGF0ZSIKICAgICAgZHVyPSIycyIKICAgICAgZnJvbT0iMCA1MCA1MCIKICAgICAgdG89IjM2MCA1MCA1MCIKICAgICAgcmVwZWF0Q291bnQ9ImluZGVmaW5pdGUiIC8+CiAgPC9wYXRoPgo8L3N2Zz4="}"
            alt="Loading..."
          />
          <iframe
            src="${t}"
            width="100%"
            height="100%"
            allowTransparency="true"
            frameborder="0"
            scrolling="yes"
          />
        </div>
      </div>
    `}});const st=et("pay:button");customElements.define("turinpay-button",class extends q{constructor(){super(),this.completed=!1,this.size="normal",this.paidText="Payment completed!!",this.titleText="Pay with TurinPay",this.timeout=3e3,this.env="pro"}static get properties(){return{completed:{attribute:!1},size:{type:String},paidText:{type:String},titleText:{type:String},timeout:{type:Number},paymentIntent:{type:String},env:{type:String}}}getUriByEnv(){return K[this.env.toUpperCase()]}composeModalUri({uri:t,paymentIntent:e}){st("> composeModalUri - paymentIntent",t,e);const i={uri:`${t}/checkout/${e}`,paymentIntent:e};return st("< composeModalUri - results",i),i}async handleClick(){const t=this.getUriByEnv();st("> handleClick - uri ",t,"paymentIntent",this.paymentIntent),this.modal=this.shadowRoot.getElementById("modal"),this.modal.waiting();const e=this.composeModalUri({uri:t,paymentIntent:this.paymentIntent});st("- handleClick - result ",e),this.modal.show(e)}async emitPaidEvent(t){st("> emitPaidEvent",t),this.completed=!0,this.titleText=this.paidText;const e={detail:{invoiceId:t.detail.invoiceId,paymentIntent:this.paymentIntent}};st("- emitPaidEvent - eventInfo",e);const i=new CustomEvent("paid",e);this.dispatchEvent(i),this.modal.close()}async onInvoiceComplete(t){st("> onInvoiceComplete",this.timeout),setTimeout((()=>{this.emitPaidEvent(t)}),this.timeout)}static get styles(){return $`
      :focus { outline: none }
      :host(:focus) { outline: none }
      button {
        font-size: var( --pb-button-size, 1rem );
        font-weight: var( --pb-button-weight, 400 );
        background-color: var( --pb-button-background, transparent );
        color: var( --pb-button-color, #0c8588 );
        border: var( --pb-button-border, 1px solid #0c8588 );
        border-radius: var( --pb-button-border-radius, 12px );
        width: var( --pb-button-width, auto );
        height: var( --pb-button-height, auto );
        transition: all 0.2s ease-out;
        cursor: pointer;
      }
      img {
        display: block;
      }
      img.big {
        height: 30px;
        padding: var( --pb-button-padding, 0.2em 1em );
      }
      img.normal {
        height: 24px;
        padding: var( --pb-button-padding, 0.2em 0.8em );
      }
      img.small {
        height: 20px;
        padding: var( --pb-button-padding, 0.3em 0.5em );
      }
      button:disabled {
        cursor: not-allowed;
      }
      button.paid {
        cursor: not-allowed;
        background-color: var( --pb-button-background-paid, #f0f0f0);
      }
      button:hover {
        font-size: var( --pb-button-hover-size, 1rem );
        font-weight: var( --pb-button-hover-weight, 400 );
        background-color: var( --pb-button-hover-background, #f0f0f0 );
        color: var( --pb-button-hover-color, #fff );
      }
    `}isButtonDisabled(){return this.completed||!this.paymentIntent}setPaidClass(t=""){return`${t} ${this.completed?"paid":""}`}render(){return Y`
      <button
        title="${this.titleText}"
        class="${this.setPaidClass()}"
        ?disabled="${this.isButtonDisabled()}"
        @click="${this.handleClick}"
      >
        <img
          class="${this.size}"
          src="${"data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxNDQiIGhlaWdodD0iMzUiIGZpbGw9Im5vbmUiPjxwYXRoIGZpbGw9IiMwMDAiIGQ9Ik01NC43MDc4IDI2LjYwMTVjLTEuMDA4IDAtMS43ODUtLjI4MTQtMi4zMzEtLjg0NDItLjUzNzYtLjU2MjgtLjgwNjQtMS4zNzM0LS44MDY0LTIuNDMxOHYtNy44MTJoLTEuNTM3MmMtLjEzNDQgMC0uMjQ3OC0uMDM3OC0uMzQwMi0uMTEzNC0uMDkyNC0uMDg0LS4xMzg2LS4yMS0uMTM4Ni0uMzc4di0uODk0NmwyLjA5MTYtLjI2NDYuNTE2Ni0zLjk0Mzc2Yy4wMTY4LS4xMjYuMDcxNC0uMjI2OC4xNjM4LS4zMDI0LjA5MjQtLjA4NC4yMS0uMTI2LjM1MjgtLjEyNmgxLjEzNHY0LjM5NzM2aDMuNjU0djEuNjI1NGgtMy42NTR2Ny42NjA4YzAgLjUzNzYuMTMwMi45MzY2LjM5MDYgMS4xOTcuMjYwNC4yNjA0LjU5NjQuMzkwNiAxLjAwOC4zOTA2LjIzNTIgMCAuNDM2OC0uMDI5NC42MDQ4LS4wODgyLjE3NjQtLjA2NzIuMzI3Ni0uMTM4Ni40NTM2LS4yMTQycy4yMzEtLjE0MjguMzE1LS4yMDE2Yy4wOTI0LS4wNjcyLjE3MjItLjEwMDguMjM5NC0uMTAwOC4xMTc2IDAgLjIyMjYuMDcxNC4zMTUuMjE0MmwuNjU1MiAxLjA3MWMtLjM4NjQuMzYxMi0uODUyNi42NDY4LTEuMzk4Ni44NTY4LS41NDYuMjAxNi0xLjEwODguMzAyNC0xLjY4ODQuMzAyNFptNy40NzMtMTIuOTY1NHY4LjEzOTZjMCAuOTY2LjIyMjYgMS43MTM2LjY2NzggMi4yNDI4LjQ0NTIuNTI5MiAxLjExNzIuNzkzOCAyLjAxNi43OTM4LjY1NTIgMCAxLjI3MjYtLjE1NTQgMS44NTIyLS40NjYyLjU3OTYtLjMxMDggMS4xMTMtLjc0MzQgMS42MDAyLTEuMjk3OHYtOS40MTIyaDIuMjQyOHYxMi43NjM4aC0xLjMzNTZjLS4zMTkyIDAtLjUyMDgtLjE1NTQtLjYwNDgtLjQ2NjJsLS4xNzY0LTEuMzczNGMtLjU1NDQuNjEzMi0xLjE3NiAxLjEwODgtMS44NjQ4IDEuNDg2OC0uNjg4OC4zNjk2LTEuNDc4NC41NTQ0LTIuMzY4OC41NTQ0LS42OTcyIDAtMS4zMTQ2LS4xMTM0LTEuODUyMi0uMzQwMi0uNTI5Mi0uMjM1Mi0uOTc0NC0uNTYyOC0xLjMzNTYtLjk4MjgtLjM2MTItLjQyLS42MzQyLS45MjgyLS44MTktMS41MjQ2LS4xNzY0LS41OTY0LS4yNjQ2LTEuMjU1OC0uMjY0Ni0xLjk3ODJ2LTguMTM5NmgyLjI0MjhabTEyLjA2MjMgMTIuNzYzOFYxMy42MzYxaDEuMjg1MmMuMjQzNiAwIC40MTE2LjA0NjIuNTA0LjEzODYuMDkyNC4wOTI0LjE1NTQuMjUyLjE4OS40Nzg4bC4xNTEyIDEuOTkwOGMuNDM2OC0uODkwNC45NzQ0LTEuNTgzNCAxLjYxMjgtMi4wNzkuNjQ2OC0uNTA0IDEuNDAyOC0uNzU2IDIuMjY4LS43NTYuMzUyOCAwIC42NzIuMDQyLjk1NzYuMTI2LjI4NTYuMDc1Ni41NTAyLjE4NDguNzkzOC4zMjc2bC0uMjg5OCAxLjY3NThjLS4wNTg4LjIxLS4xODkuMzE1LS4zOTA2LjMxNS0uMTE3NiAwLS4yOTgyLS4wMzc4LS41NDE4LS4xMTM0LS4yNDM2LS4wODQtLjU4MzgtLjEyNi0xLjAyMDYtLjEyNi0uNzgxMiAwLTEuNDM2NC4yMjY4LTEuOTY1Ni42ODA0LS41MjA4LjQ1MzYtLjk1NzYgMS4xMTMtMS4zMTA0IDEuOTc4MnY4LjEyN2gtMi4yNDI4Wm0xMi42NTg1LTEyLjc2Mzh2MTIuNzYzOGgtMi4yNDI4VjEzLjYzNjFoMi4yNDI4Wm0uNTA0LTQuMDA2NzZjMCAuMjE4NC0uMDQ2Mi40MjQxNi0uMTM4Ni42MTczNi0uMDg0LjE4NDgtLjIwMTYuMzUyOC0uMzUyOC41MDQtLjE0MjguMTQyOC0uMzEwOC4yNTYyLS41MDQuMzQwMi0uMTkzMi4wODQtLjM5OS4xMjYtLjYxNzQuMTI2LS4yMTg0IDAtLjQyNDItLjA0Mi0uNjE3NC0uMTI2LS4xODQ4LS4wODQtLjM1MjgtLjE5NzQtLjUwNC0uMzQwMi0uMTQyOC0uMTUxMi0uMjU2Mi0uMzE5Mi0uMzQwMi0uNTA0LS4wODQtLjE5MzItLjEyNi0uMzk4OTYtLjEyNi0uNjE3MzYgMC0uMjE4NC4wNDItLjQyNDIuMTI2LS42MTc0LjA4NC0uMjAxNi4xOTc0LS4zNzM4LjM0MDItLjUxNjYuMTUxMi0uMTUxMi4zMTkyLS4yNjg4LjUwNC0uMzUyOC4xOTMyLS4wODQuMzk5LS4xMjYuNjE3NC0uMTI2LjIxODQgMCAuNDI0Mi4wNDIuNjE3NC4xMjYuMTkzMi4wODQuMzYxMi4yMDE2LjUwNC4zNTI4LjE1MTIuMTQyOC4yNjg4LjMxNS4zNTI4LjUxNjYuMDkyNC4xOTMyLjEzODYuMzk5LjEzODYuNjE3NFptMy40NDg5IDE2Ljc3MDU2VjEzLjYzNjFoMS4zMzU2Yy4zMTkyIDAgLjUyMDguMTU1NC42MDQ4LjQ2NjJsLjE3NjQgMS4zODZjLjU1NDQtLjYxMzIgMS4xNzE4LTEuMTA4OCAxLjg1MjItMS40ODY4LjY4ODgtLjM3OCAxLjQ4MjYtLjU2NyAyLjM4MTQtLjU2Ny42OTcyIDAgMS4zMTA0LjExNzYgMS44Mzk2LjM1MjguNTM3Ni4yMjY4Ljk4MjUuNTU0NCAxLjMzNTUuOTgyOC4zNjEuNDIuNjM0LjkyODIuODE5IDEuNTI0Ni4xODUuNTk2NC4yNzcgMS4yNTU4LjI3NyAxLjk3ODJ2OC4xMjdoLTIuMjQyNXYtOC4xMjdjMC0uOTY2LS4yMjI2LTEuNzEzNi0uNjY3OC0yLjI0MjgtLjQzNjgtLjUzNzYtMS4xMDg4LS44MDY0LTIuMDE2LS44MDY0LS42NjM2IDAtMS4yODUyLjE1OTYtMS44NjQ4LjQ3ODgtLjU3MTIuMzE5Mi0xLjEwMDQuNzUxOC0xLjU4NzYgMS4yOTc4djkuMzk5NmgtMi4yNDI4WiIvPjxwYXRoIGZpbGw9IiMwQzg1ODgiIGQ9Ik0xMDQuNjgxIDMwLjYyMDlWMTMuNDcyM2gxLjkwMmMuMjAyIDAgLjM3NC4wNDYyLjUxNy4xMzg2LjE0My4wOTI0LjIzNS4yMzUyLjI3Ny40Mjg0bC4yNTIgMS4xOTdjLjUyMS0uNTk2NCAxLjExNy0xLjA3OTQgMS43ODktMS40NDkuNjgxLS4zNjk2IDEuNDc1LS41NTQ0IDIuMzgyLS41NTQ0LjcwNSAwIDEuMzQ4LjE0NyAxLjkyOC40NDEuNTg4LjI5NCAxLjA5Mi43MjI0IDEuNTEyIDEuMjg1Mi40MjguNTU0NC43NTYgMS4yNDMyLjk4MiAyLjA2NjQuMjM2LjgxNDguMzUzIDEuNzUxNC4zNTMgMi44MDk4IDAgLjk2Ni0uMTMgMS44NjA2LS4zOSAyLjY4MzgtLjI2MS44MjMyLS42MzUgMS41MzcyLTEuMTIyIDIuMTQyLS40NzkuNjA0OC0xLjA2MiAxLjA3OTQtMS43NTEgMS40MjM4LS42ODEuMzM2LTEuNDQ1LjUwNC0yLjI5My41MDQtLjczMSAwLTEuMzUzLS4xMDkyLTEuODY1LS4zMjc2LS41MDQtLjIyNjgtLjk1OC0uNTM3Ni0xLjM2MS0uOTMyNHY1LjI5MmgtMy4xMTJabTYuMDQ4LTE0LjkzMWMtLjY0NyAwLTEuMjAxLjEzODYtMS42NjMuNDE1OC0uNDU0LjI2ODgtLjg3OC42NTEtMS4yNzMgMS4xNDY2djUuNzk2Yy4zNTMuNDM2OC43MzUuNzQzNCAxLjE0Ny45MTk4LjQyLjE2OC44NjkuMjUyIDEuMzQ4LjI1Mi40NyAwIC44OTQtLjA4ODIgMS4yNzItLjI2NDYuMzg3LS4xNzY0LjcxLS40NDUyLjk3MS0uODA2NC4yNjgtLjM2MTIuNDc0LS44MTQ4LjYxNy0xLjM2MDguMTQzLS41NTQ0LjIxNC0xLjIwNTQuMjE0LTEuOTUzIDAtLjc1Ni0uMDYzLTEuMzk0NC0uMTg5LTEuOTE1Mi0uMTE3LS41MjkyLS4yOS0uOTU3Ni0uNTE2LTEuMjg1Mi0uMjI3LS4zMjc2LS41MDQtLjU2Ny0uODMyLS43MTgyLS4zMTktLjE1MTItLjY4NS0uMjI2OC0xLjA5Ni0uMjI2OFptMTguMzMxIDEwLjcxaC0xLjM5OWMtLjI5NCAwLS41MjUtLjA0Mi0uNjkzLS4xMjYtLjE2OC0uMDkyNC0uMjk0LS4yNzMtLjM3OC0uNTQxOGwtLjI3Ny0uOTE5OGMtLjMyNy4yOTQtLjY1MS41NTQ0LS45Ny43ODEyLS4zMTEuMjE4NC0uNjM0LjQwMzItLjk3LjU1NDQtLjMzNi4xNTEyLS42OTMuMjY0Ni0xLjA3MS4zNDAycy0uNzk4LjExMzQtMS4yNi4xMTM0Yy0uNTQ2IDAtMS4wNS0uMDcxNC0xLjUxMi0uMjE0Mi0uNDYyLS4xNTEyLS44NjEtLjM3MzgtMS4xOTctLjY2NzgtLjMyOC0uMjk0LS41ODQtLjY1OTQtLjc2OS0xLjA5NjItLjE4NS0uNDM2OC0uMjc3LS45NDUtLjI3Ny0xLjUyNDYgMC0uNDg3Mi4xMjYtLjk2Ni4zNzgtMS40MzY0LjI2LS40Nzg4LjY4OS0uOTA3MiAxLjI4NS0xLjI4NTIuNTk3LS4zODY0IDEuMzktLjcwNTYgMi4zODItLjk1NzYuOTkxLS4yNTIgMi4yMjEtLjM5NDggMy42OTEtLjQyODR2LS43NTZjMC0uODY1Mi0uMTg0LTEuNTAzNi0uNTU0LTEuOTE1Mi0uMzYxLS40Mi0uODktLjYzLTEuNTg4LS42My0uNTA0IDAtLjkyNC4wNTg4LTEuMjYuMTc2NC0uMzM2LjExNzYtLjYzLjI1Mi0uODgyLjQwMzItLjI0My4xNDI4LS40Ny4yNzMtLjY4LjM5MDYtLjIxLjExNzYtLjQ0MS4xNzY0LS42OTMuMTc2NC0uMjEgMC0uMzkxLS4wNTQ2LS41NDItLjE2MzgtLjE1MS0uMTA5Mi0uMjczLS4yNDM2LS4zNjUtLjQwMzJsLS41NjctLjk5NTRjMS40ODctMS4zNjA4IDMuMjgtMi4wNDEyIDUuMzgtMi4wNDEyLjc1NiAwIDEuNDI4LjEyNiAyLjAxNi4zNzguNTk2LjI0MzYgMS4xLjU4OCAxLjUxMiAxLjAzMzIuNDEyLjQzNjguNzIyLjk2MTguOTMyIDEuNTc1LjIxOS42MTMyLjMyOCAxLjI4NTIuMzI4IDIuMDE2djguMTY0OFptLTYuMDQ4LTEuOTQwNGMuMzE5IDAgLjYxMy0uMDI5NC44ODItLjA4ODIuMjY5LS4wNTg4LjUyMS0uMTQ3Ljc1Ni0uMjY0Ni4yNDQtLjExNzYuNDc1LS4yNjA0LjY5My0uNDI4NC4yMjctLjE3NjQuNDU0LS4zODIyLjY4LS42MTc0di0yLjE3OThjLS45MDcuMDQyLTEuNjY3LjEyMTgtMi4yOC4yMzk0LS42MDUuMTA5Mi0xLjA5Mi4yNTItMS40NjIuNDI4NC0uMzY5LjE3NjQtLjYzNC4zODIyLS43OTQuNjE3NC0uMTUxLjIzNTItLjIyNi40OTE0LS4yMjYuNzY4NiAwIC41NDYuMTU5LjkzNjYuNDc4IDEuMTcxOC4zMjguMjM1Mi43NTIuMzUyOCAxLjI3My4zNTI4Wm0xMy4yNTIgNS40OTM2Yy0uMDkzLjIxODQtLjIxNS4zODIyLS4zNjYuNDkxNC0uMTQzLjExNzYtLjM2NS4xNzY0LS42NjguMTc2NGgtMi4zMThsMi40MTktNS4xNzg2LTUuMjI5LTExLjk3aDIuNzIyYy4yNTIgMCAuNDQ1LjA1ODguNTc5LjE3NjQuMTM1LjExNzYuMjM2LjI1Mi4zMDMuNDAzMmwyLjc1OSA2LjcwMzJjLjA5My4yMTg0LjE3Mi40NDUyLjI0LjY4MDQuMDY3LjIzNTIuMTI2LjQ3MDQuMTc2LjcwNTYuMDY3LS4yNDM2LjEzOS0uNDc4OC4yMTQtLjcwNTYuMDg0LS4yMjY4LjE3Mi0uNDU3OC4yNjUtLjY5M2wyLjU5NS02LjY5MDZjLjA2OC0uMTY4LjE3Ny0uMzA2Ni4zMjgtLjQxNTguMTYtLjEwOTIuMzM2LS4xNjM4LjUyOS0uMTYzOGgyLjQ5NWwtNy4wNDMgMTYuNDgwOFoiLz48cGF0aCBmaWxsPSJ1cmwoI2EpIiBkPSJNMjguNyAwSDYuM0MyLjgyMDYxIDAgMCAyLjgyMDYxIDAgNi4zdjIyLjRDMCAzMi4xNzk0IDIuODIwNjEgMzUgNi4zIDM1aDIyLjRjMy40Nzk0IDAgNi4zLTIuODIwNiA2LjMtNi4zVjYuM0MzNSAyLjgyMDYxIDMyLjE3OTQgMCAyOC43IDBaIi8+PGNpcmNsZSByPSI3LjciIGZpbGw9IiMxQ0VCQzIiIGZpbGwtb3BhY2l0eT0iLjgiIHRyYW5zZm9ybT0ibWF0cml4KC0xIDAgMCAxIDIzLjEgMTcuNSkiLz48Y2lyY2xlIHI9IjcuNyIgZmlsbD0iI2ZmZiIgZmlsbC1vcGFjaXR5PSIuNiIgdHJhbnNmb3JtPSJtYXRyaXgoLTEgMCAwIDEgMTMuMjk5OSAxNy41KSIvPjxkZWZzPjxsaW5lYXJHcmFkaWVudCBpZD0iYSIgeDE9IjM3LjgiIHgyPSItNC45IiB5MT0iNDQuOCIgeTI9Ii03LjciIGdyYWRpZW50VW5pdHM9InVzZXJTcGFjZU9uVXNlIj48c3RvcCBzdG9wLWNvbG9yPSIjMDBBRjhDIi8+PHN0b3Agb2Zmc2V0PSIxIiBzdG9wLWNvbG9yPSIjMEU3Qzg2Ii8+PC9saW5lYXJHcmFkaWVudD48L2RlZnM+PC9zdmc+"}"
          alt="TurinPay"
        />
      </button>
      <turinpay-modal id="modal" @invoice-completed="${this.onInvoiceComplete}" />
    `}});
//# sourceMappingURL=index.js.map
