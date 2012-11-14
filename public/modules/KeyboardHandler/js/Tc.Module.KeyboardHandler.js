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

        on: function(callback) {
            var self = this;

            // subscribe to the keyboard-channel
            self.sandbox.subscribe('keyboard', this);

            // handle the shortcuts-modal
            $('.show-shortcuts', self.$ctx).on('click', function() {
                var modal = self.sandbox.getModuleById($('.modModal').data('id'));
                modal.open('keyboard-shortcuts', self._prepareModal(self.registeredModules), function() {});

                return false;
            });

            callback();
        },

        /**
         * Register shortcut
         *
         * @param data Object contains module-id, modifier, shortcut and callback
         */
        onRegisterShortcut: function(data) {
            var self = this;
            
            // check if module is already registered
            if ($.inArray({'id': data.moduleId, 'shortcut': data.modifier + '+' + data.shortcut, 'description': data.description}, self.registeredModules) > -1) {
                return;
            }

            if (data.modifier === null) {
                $(document).on('keydown', function(e) {

                    // TODO: make this bulletproof
                    if (data.exclude) {
                        if ($.inArray(event.target.nodeName.toLowerCase(), data.exclude) > -1) {
                            return;
                        }
                    }
                    if (typeof data.shortcut === 'number' && e.which == data.shortcut) {
                        data.callback();
                    } else if (String.fromCharCode(e.which).toLowerCase() === data.shortcut) {
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

            if (data.description) {
                console.log(data.description);
            }

            self.registeredModules.push({'id': data.moduleId, 'shortcut': data.modifier + '+' + data.shortcut, 'description': data.description});
        },

        /**
         * Unregisters all shortcuts
         *
         * @param data {Object} contains module-id
         */
        onUnregisterShortcuts: function(data) {
            var self = this;

            $(document).off('keydown');
            $(document).off('keyup');

            $.each(self.registeredModules, function(key, value) {
                console.log($.inArray(value, self.registeredModules));
                if (value.id === data.moduleId) {
                    self.registeredModules.splice( $.inArray(value, self.registeredModules), 1 );
                }
            });

        },

        /**
         * Makes the shortcut-string pretty for the modal
         *
         * @param data {Array} not nice looking array - not suitable for the modal
         * @return data {Array} nice looking array for the modal
         * @private
         */
        _prepareModal: function(data) {
            if (data !== null) {
                for (var i = 0, len = data.length; i < len; i++) {
                    for (var key in data[i]) {
                        if (data[i].hasOwnProperty(key)) {
                            data[i].shortcut = data[i].shortcut.split('null+').pop();
                        }
                    }
                }
            }

            return data;
        }
    });
})(Tc.$);