/*global document*/
var navbuttons = {
    picker: null,

    init: function (Y) {
        "use strict";
        Y.use('yui2-dom', 'yui2-event', 'yui2-element', 'yui2-dragdrop', 'yui2-slider', 'yui2-colorpicker', 'yui2-get', function (Y) {
            var YAHOO = Y.YUI2, cont, hexval, rgb;

            cont = document.getElementById('yui-picker');
            cont.innerHTML = '<div id="yui-picker-inner" style="width: 400px; height: 200px; position:relative; padding: 0; margin: 0; top: 0; left: 0;"></div>';

            if (!navbuttons.picker) { //make sure that we haven't already created our Color Picker
                navbuttons.picker = new YAHOO.widget.ColorPicker("yui-picker-inner", {
                    images: {
                        PICKER_THUMB: "pix/picker_thumb.png",
                        HUE_THUMB: "pix/hue_thumb.png"
                    },
                    showwebsafe: false,
                    showhexcontrols: false,
                    showhexsummary: false,
                    showrgbcontrols: false,
                    showcontrols: false,
                    txt: {
                        SHOW_CONTROLS: '',
                        HIDE_CONTROLS: ''
                    }
                });

                hexval = document.getElementById('id_backgroundcolour').value;
                hexval = hexval.substring(1);
                rgb = YAHOO.util.Color.hex2rgb(hexval);
                navbuttons.picker.setValue(rgb, true);

                //listen to rgbChange to be notified about new values
                navbuttons.picker.on("rgbChange", function (o) {
                    var el, newval;
                    el = YAHOO.util.Dom.get('id_backgroundcolour');
                    newval = '#' + YAHOO.util.Color.rgb2hex(o.newValue);
                    el.value = newval;
                });
            }
        });
    }
};
