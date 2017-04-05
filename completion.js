/**
 * Created by ishani on 01/03/2017.
 */
M.block_navbuttons = M.block_navbuttons || {};
M.block_navbuttons.completion = {};
M.block_navbuttons.completion.init = function(Y) {

    var handle_success = function (id, o, args) {

        if (o.responseText != 'OK') {
            alert('An error occurred when attempting to mark your activity complete.\n\n(' + o.responseText + '.)'); //TODO: localize

        } else {
            var current = args.state.get('value');
            var btntype = args.btntype.get('value');
            var modulename = args.modulename.get('value'),
                altstr,
                titlestr;
            if (current == 1) {
                // Successfully marked as complete, so change button to 'Mark incomplete'
                altstr = M.util.get_string('incompletebuttontext', 'block_navbuttons');
                titlestr = M.util.get_string('incompletebuttontext', 'block_navbuttons');
                args.state.set('value', 0);
                if(btntype == 'icon') {
                    args.image.set('src', M.util.image_url('crossicon', 'block_navbuttons'));
                    args.image.set('alt', altstr);
                    args.image.set('title', titlestr);
                } else {
                    args.submit.set('value', altstr);
                }
            } else {
                altstr = M.util.get_string('completebuttontext', 'block_navbuttons');
                titlestr = M.util.get_string('completebuttontext', 'block_navbuttons');
                args.state.set('value', 1);
                if(btntype == 'icon') {
                    args.image.set('src', M.util.image_url('tickicon', 'block_navbuttons'));
                    args.image.set('alt', altstr);
                    args.image.set('title', titlestr);
                } else {
                    args.submit.set('value', altstr);
                }
            }
        }

        args.ajax.remove();
    };

    var handle_failure = function (id, o, args) {
        alert('An error occurred when attempting to mark your activity complete.\n\n(' + o.responseText + '.)'); //TODO: localize
        args.ajax.remove();
    };

    var toggle = function (e) {
        e.preventDefault();

        var form = e.target;
        var cmid = 0;
        var completionstate = 0;
        var state = null;
        var submit = null;
        var image = null;
        var modulename = null;
        var btntype = null;

        var inputs = Y.Node.getDOMNode(form).getElementsByTagName('input');
        for (var i = 0; i < inputs.length; i++) {
            switch (inputs[i].name) {
                case 'id':
                    cmid = inputs[i].value;
                    break;
                case 'completionstate':
                    completionstate = inputs[i].value;
                    state = Y.one(inputs[i]);
                    break;
                case 'modulename':
                    modulename = Y.one(inputs[i]);
                    break;
                case 'btntype':
                    btntype = Y.one(inputs[i]);
                    break;
            }
            if (inputs[i].type == 'submit') {
                submit = Y.one(inputs[i]);
            }
            if (inputs[i].type == 'image') {
                image = Y.one(inputs[i]);
            }
        }

        // start spinning the ajax indicator
        var ajax = Y.Node.create('<div class="ajaxworking" />');
        form.append(ajax);

        var cfg = {
            method: "POST",
            data: 'id=' + cmid + '&completionstate=' + completionstate + '&btntype=' + btntype + '&fromajax=1&sesskey=' + M.cfg.sesskey,
            on: {
                success: handle_success,
                failure: handle_failure
            },
            arguments: {state: state, submit: submit, image: image, btntype: btntype, ajax: ajax, modulename: modulename}
        };

        Y.use('io-base', function (Y) {
            Y.io(M.cfg.wwwroot + '/course/togglecompletion.php', cfg);
        });
    };


    // register submit handlers on manual tick completion forms
    Y.all('form.togglecompletion').each(function (form) {
        if (!form.hasClass('preventjs')) {
            Y.on('submit', toggle, form);
        }
    });
};



