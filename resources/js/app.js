/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');


window.Vue = require('vue');

import bFormSlider from 'vue-bootstrap-slider';
import 'bootstrap-slider/dist/css/bootstrap-slider.css'

Vue.use(bFormSlider)

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

const files = require.context('./', true, /\.vue$/i)
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

//Vue.component('example-component', require('./components/ExampleComponent.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

if (document.getElementById("consoleShareApp")) {
    const app = new Vue({
        el: '#consoleShareApp',
    });
}


window.addEventListener( "pageshow", function ( event ) {
  var historyTraversal = event.persisted ||
                         ( typeof window.performance != "undefined" &&
                              window.performance.navigation.type === 2 );
  if ( historyTraversal ) {
    // Handle page restore.
    window.location.reload();
  }
});


/*
+require('./bootstrap');
+const Tutor = require('./tutorial');
+
+$(function(){
+
+    setTimeout(() => $("#motive").fadeIn(1000), 2000);
+
+    $("#watch-preview").click(() => $(".main-message").fadeOut(2000));
+
+    
+    var typed = new Typed('.type-effect', {
+        strings: [text],
+        typeSpeed: 90,
+        onComplete: () => {
+            setTimeout(() => {
+                typed.destroy();
+                $(".type-effect").html(text+"&nbsp");
+            }, 4000)
+
+        }
+    });
+    
+
+    let tutor = new Tutor();
+    tutor.start('#tutor-terminal-window', '#tutor-chat-window', '#tutor-chat-second-window');
+})
    */

