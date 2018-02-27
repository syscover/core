/*
 *	Territories v1.3 - 2017-11-26
 *	By José Carlos Rodríguez Palacín
 *	(c) 2017 SYSCOVER S.L. - https://syscover.com
 *	All rights reserved
 */

"use strict";

(function () {
    var CheckPostalCode = {
        options: {
            key:            'YOUR GOOGLE MAPS API KEY',
            endpoint:       'https://maps.googleapis.com/maps/api/geocode/',    // application id
            outputFormat:   'json',                                             // output format, values: json or xml
        },

        init: function(options, callback)
        {
            this.options = $.extend({}, this.options, options||{});	            // Init options

            var that = this;

            if(callback != null)
            {
                that.callback({
                    success: true,
                    message: 'CheckPostalCode init'
                });
            }

            return that;
        },

        check: function(q, callback) {

            var that = this;
            var data = {
                key: this.options.key
            };

            if(q['bounds'])     data['bounds']      = q['bounds'];
            if(q['address'])    data['address']     = q['address'];
            if(q['language'])   data['language']    = q['language'];
            if(q['region'])     data['region']      = q['region'];

            // set components
            var components = '';
            var componentKeys = ['route', 'locality', 'administrative_area', 'postal_code', 'country'];
            var first = true;
            for(var key of componentKeys)
            {
                if(q[key])
                {
                    if(first) first = false; else components += '|';
                    components += key + ':' + q[key];
                }
            }
            if(components.length > 0) data['components'] = components;

            // call api
            $.ajax({
                type: "get",
                url: this.options.endpoint + this.options.outputFormat,
                data: data,
                dataType: 'json',
                success: function(response) {
                    // trigger event
                    $(that).trigger('checkPostalCode:afterCheck', response);
                    if(callback) callback(response);

                },
                error: function(jqXHR, textStatus, errorThrown) {
                    if(that.callback != null)
                    {
                        var response = {
                            success: false,
                            message: textStatus
                        };

                        callback(response);
                    }
                }
            });
        }
    };

    /*
     * Make sure Object.create is available in the browser (for our prototypal inheritance)
     * Note this is not entirely equal to native Object.create, but compatible with our use-case
     */
    if (typeof Object.create !== 'function') {
        Object.create = function (o) {
            function F() {}
            F.prototype = o;
            return new F();
        };
    }

    /*
     * Start the plugin
     */
    $.checkPostalCode = function(options, callback) {
        var object;
        if (! $.data(document, 'checkPostalCode')) {
            object = $.data(document, 'checkPostalCode', Object.create(CheckPostalCode).init(options, callback));
            return $(object);
        } else {
            return $($.data(document, 'checkPostalCode'));
        }
    };

}( jQuery ));