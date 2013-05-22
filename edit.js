navbuttons = {
    picker: null,
    csspath: null,

    init: function(Y, csspath) {
        this.csspath = csspath;

        YAHOO.util.Get.css(this.csspath+'/colorpicker.css');

        var cont = document.getElementById('yui-picker');
        cont.innerHTML = '<div id="yui-picker-inner" style="width: 400px; height: 200px; position:relative; padding: 0; margin: 0; top: 0; left: 0;"></div>';

        if (!this.picker) { //make sure that we haven't already created our Color Picker
            this.picker = new YAHOO.widget.ColorPicker("yui-picker-inner", {
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

            var hexval = document.getElementById('id_backgroundcolour').value;
            hexval = hexval.substring(1);
            var rgb = YAHOO.util.Color.hex2rgb(hexval);
            this.picker.setValue(rgb, true);

            //listen to rgbChange to be notified about new values
            this.picker.on("rgbChange", function(o) {
                var el = YAHOO.util.Dom.get('id_backgroundcolour');
                var newval = '#'+YAHOO.util.Color.rgb2hex(o.newValue);
                el.value = newval;
            });
        }
    }
}
