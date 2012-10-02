/**
 * Clarify.
 *
 * Copyright (C) 2012 Roger Dudler <roger.dudler@gmail.com>
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */
(function($) {
    Tc.Module.KeyboardHandler = Tc.Module.extend({

        // possible modifier keys
        // TODO: support for meta-key
        modifiers: {
            'alt': false,
            'ctrl': false,
            'shift': false
        },

        // that's where we store the registered modules
        registeredModules: [],

        /**
         * Register shortcut
         *
         * @param data Object contains module-id, modifier, shortcut and callback
         */
        onRegisterShortcut: function(data) {
            var self = this;

            // check if module is already registered
            if ($.inArray({'id': data.moduleId, 'shortcut': data.modifier + '+' + data.shortcut}, self.registeredModules) > -1) {
                return;
            }

            if (data.modifier === null) {
                $(document).on('keydown', function(e) {

                    // TODO: make this bulletproof
                    if (event.target.nodeName === 'INPUT' || event.target.nodeName === 'TEXTAREA') {
                        return;
                    }

                    if (String.fromCharCode( e.which ).toLowerCase() === data.shortcut) {
                        data.callback();
                    }
                });
            } else {
                // reset all modifiers on keyup
                $(document).on('keyup', function(e) {
                    switch (e.which) {
                        case 16:
                            self.modifiers.shift = false;
                            break;

                        case 17:
                            self.modifiers.ctrl = false;
                            break;

                        case 18:
                            self.modifiers.alt = false;
                            break;
                    }
                });

                $(document).on('keydown', function(e) {
                    switch (e.which) {
                        case 16:
                            self.modifiers.shift = true;
                            break;

                        case 17:
                            self.modifiers.ctrl = true;
                            break;

                        case 18:
                            self.modifiers.alt = true;
                            break;
                    }

                    if (String.fromCharCode( e.which ).toLowerCase() === data.shortcut && self.modifiers[data.modifier]) {
                        data.callback();
                    }
                });
            }

            self.registeredModules.push({'id': data.moduleId, 'shortcut': data.modifier + '+' + data.shortcut});
        },

        /**
         * Unregister all shortcuts
         *
         * @param data Object contains module-id
         */
        onUnregisterShortcut: function(data) {
            $(document).off('keydown');
            $(document).off('keyup');

            this.registeredModules.splice( $.inArray(data.moduleID, this.registeredModules), 1 );
        }
    });
})(Tc.$);