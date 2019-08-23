let H = {
    sessionStorageKey: 'H',
    onstore: 'H.onstore',
    getValue: 'H.getValue',
    setValue: 'H.setValue',
    setActiveTab: 'H.setActiveTab',
    setValidation: 'H.setValidation',
    defaultAction: 'H.defaultAction',
    formInit: 'H.formInit',

    consts: {
        offlineError: 'Você está desconectado.',
        yes: 'Sim',
        no: 'Não',
    },
    init: function () {
        if ('scrollRestoration' in history) {
            // Back off, browser, I got this...
            history.scrollRestoration = 'manual';
        }
    },
    initVue: function () {
        Vue.filter('formatDateTime', function (value) {
            return H.formatDatetime(value);
        });
    },
    initJQuery: function() {
        if (typeof jQuery.when.all === 'undefined') {
            jQuery.when.all = function (deferreds) {
                return $.Deferred(function (def) {
                    $.when.apply(jQuery, deferreds).then(
                        function () {
                            def.resolveWith(this, [Array.prototype.slice.call(arguments)]);
                        },
                        function () {
                            def.rejectWith(this, [Array.prototype.slice.call(arguments)]);
                        });
                });
            }
        }

        $.fn.serializeObject = function () {
            let result = {}, serializedArray = this.serializeArray();
            $.each(serializedArray, function () {
                let name = this.name.replace('[]', '');
                if (result[name]) {
                    if (!result[name].push) {
                        result[name] = [result[name]];
                    }
                    result[name].push(this.value || '');
                } else {
                    result[name] = this.value || '';
                }
            });
            return result;
        };
    },
    /**
     *
     * @param {string} aclass
     * @param {string} amethod
     * @param {object} aparams
     * @param {function} acallback
     */
    rpc: function (aclass, amethod, aparams, acallback, progresscb) {
        let l_callback = acallback || function (r, e) {
            console.info(r);
            console.error(e);
        };
        if (!navigator.onLine) {
            l_callback(null, H.consts.offlineError);
            return;
        }

        let l_process = function (lines) {
            lines = lines.split(/\n/);
            for (let i in lines) {
                if (!lines.hasOwnProperty(i)) continue;
                if (!lines[i]) continue;
                let l_data;
                try {
                    l_data = JSON.parse(lines[i]);
                } catch (ex) {
                    return;
                }
                if ("method" in l_data) {//Servidor enviando comandos
                    H.evalCode(l_data.method, l_data.params);
                } else if ("error" in l_data && l_data.error) {//Server sent an error
                    if (l_data.error.trace) {
                        console.warn(l_data.error.trace);
                    }

                    let errorShow = l_callback(null, l_data.error);
                    if (errorShow === undefined) {
                        H.showError(l_data.error.message);
                    }
                } else if ("result" in l_data) {//Servidor enviando a resposta
                    l_callback(l_data.result, null);
                }
            }
        };

        let l_stop = false;
        if (progresscb) {
            // Pooling for progress
            let l_progress = function () {
                $.ajax({
                    url: 'progress.php',
                    timeout: 10000,
                    async: true
                }).done(function (a_data, textStatus, xhr) {
                    progresscb();
                    if (l_stop) return;
                    l_process(xhr.responseText);
                    setTimeout(l_progress, 1000);
                });
            };
            setTimeout(l_progress, 1000);
        }
        let lastResponseLength = 0;
        $.ajax({
            url: 'rpc.php?' + aclass + '@' + amethod,
            type: 'POST',
            contentType: 'application/json',
            dataType: 'json',
            data: JSON.stringify({
                method: amethod,
                params: aparams
            }),
            processData: false,
            async: true,
        }).fail(function (xhr, textStatus, errorThrown) {
            if (textStatus === "parsererror") {
                l_callback(null, {"message": xhr.responseText});
            } else {
                l_callback(null, {"message": errorThrown});
            }
            l_stop = true;
        }).done(function (a_data, textStatus, xhr) {
            l_process(xhr.responseText.substring(lastResponseLength));
            l_stop = true;
        });
    },

    /**
     * Get coords
     *
     * @param callback
     */
    geoLocation: function (callback) {
        callback = callback || function (r, e) {
            console.info(r);
            console.error(e);
        };
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function (r) {
                    callback(r, null);
                },
                function (r) {
                    callback(null, r);
                }
            );
        } else {
            callback(null, 'No geolocation available.');
        }
    },
    sessionStorage: function (k, v) {
        var l_obj = {};
        try {
            l_obj = JSON.parse(window.sessionStorage[H.sessionStorageKey]);
        } catch (e) {

        }
        if (typeof v === "undefined") {//Get
            return l_obj[k] || {};
        } else {//Set
            l_obj[k] = v;
            window.sessionStorage[H.sessionStorageKey] = JSON.stringify(l_obj);
        }
        return true;
    },
    sessionStorageGet(k, def) {
        var l_obj = {};
        try {
            l_obj = JSON.parse(window.sessionStorage[H.sessionStorageKey]);
        } catch (e) {

        }
        return l_obj[k] || def;
    },
    sessionStorageSet(k, val) {
        let obj = {};
        try {
            obj = JSON.parse(window.sessionStorage[H.sessionStorageKey]);
        } catch (e) {

        }

        obj[k] = val;
        window.sessionStorage[H.sessionStorageKey] = JSON.stringify(obj);
        return true;
    },
    formatNumber: function (v) {
        return numeral(v).format(',0.00');
    },
    formatCurrency: function (v) {
        return numeral(v).format('$,0.00');
    },
    formatDatetime: function (v) {
        return moment(v || {}).format('L LTS');
    },
    /**
     * Serializes a container with many checkboxes
     *
     * @param a_container
     */
    serializeChecklist: function (a_container) {
        var l_data = {};
        a_container.find("input[type=checkbox]").each(function () {
            var $that = $(this);
            l_data[$that.val()] = $that.is(":checked");
        });
        return l_data;
    },
    /**
     * Serialize using custom event handler
     *
     * @param a_container
     */
    serialize: function (a_container) {
        a_container = find(a_container);
        var l_data = {};
        a_container.find(".form-group").trigger(H.getValue, [l_data]);
        return l_data;
    },
    /**
     * Un-serialize using custom event handler
     *
     * @param a_container
     * @param a_data
     */
    unserialize: function (a_container, a_data) {
        a_container = find(a_container);
        a_container.find(".form-group").trigger(H.setValue, [a_data]);
    },
    getBootstrapDevice: function () {
        if (window.innerWidth >= 1200) {
            return 'xl'
        }

        if (window.innerWidth >= 992) {
            return 'lg'
        }

        if (window.innerWidth >= 768) {
            return 'md'
        }

        if (window.innerWidth >= 576) {
            return 'sm'
        }

        return 'xs';
    },
    /**
     * Interpreta e executa uma função
     *
     * @param {string|function} a_function_or_code
     * @param a_params
     * @returns {undefined}
     */
    evalCode: function (a_function_or_code, a_params) {
        try {
            if (typeof a_function_or_code === "function") {
                a_function_or_code.apply(window, a_params);
                return;
            }

            var l_obj = null;
            var l_method = null;
            if (typeof a_function_or_code === "string") {
                var l_expr = a_function_or_code.match(/^(\w+)\.(\w+)$/);
                if (l_expr) {
                    if (l_expr[1] in window) {
                        l_obj = eval(l_expr[1]);
                    }
                    if (l_expr[2] in l_obj) {
                        l_method = l_expr[2];
                    }
                }
                if (a_function_or_code in window) {
                    l_obj = window;
                    l_method = a_function_or_code;
                }
            }
            if (l_obj && l_method && l_obj[l_method]) {
                l_obj[l_method].apply(l_obj, a_params);
                return;
            }

            (function () {
                eval(a_function_or_code);
            }).apply(window, a_params);
        } catch (ex) {
            console.warn("evalCode:", ex.stack);
        }
    },
    /**
     * Get the file content
     *
     * @param $elem
     * @param callback
     */
    fileAsBase64: function ($elem, callback) {
        let file = $elem.get(0).files[0];
        let reader = new FileReader();
        reader.onload = function (el) {
            callback({
                name: file.name,
                size: file.size,
                content: el.target.result
            });
        };

        reader.readAsDataURL(file);
    },
    /**
     * Ask for confirmation
     * options
     *   default_answer: Default action when timeout
     *
     * @param msg
     * @param callback
     * @param options
     */
    showQuery: function (msg, callback, options) {
        callback = callback || function (r) {
            console.info(r)
        };

        let answer = options.default_answer || false;
        options = $.extend({
            showProgressbar: false,
            animate: {
                enter: 'animated bounceIn',
                exit: 'animated bounceOut'
            },
            placement: {
                from: "top",
                align: "center"
            },
            onClosed: function () {
                callback(answer);
            },
            z_index: 1051,
            template: `
<div 
    data-notify="container" 
    class="col-md-6 alert alert-{0}"
    style="max-width: 600px" 
    role="alert">
	<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>
	<span data-notify="icon"></span>
	<span data-notify="title">{1}</span>
	<hr>
	<span data-notify="message">{2}</span>
	<div class="row justify-content-center mt-2">
        <button 
            class="btn btn-outline-secondary col m-2 btn-1"
            style="min-width: 150px" 
            >
            ${H.consts.yes}
        </button>
        <button 
            class="btn btn-outline-secondary col m-2 btn-2"
            style="min-width: 150px"
            >
            ${H.consts.no}
        </button>
    </div>
</div>`
        }, options || {});

        let notify = $.notify({
            title: 'Confirmação',
            message: msg
        }, options);
        notify.$ele.find('.btn-1').on('click', function () {
            answer = true;
            notify.close();
        });
        notify.$ele.find('.btn-2').on('click', function () {
            answer = false;
            notify.close();
        });
    },
    showSuccess: function (msg, options) {
        $.notify({
            message: msg
        }, $.extend({
            delay: 1000,
            animate: {
                enter: 'animated bounceIn',
                exit: 'animated bounceOut'
            },
            placement: {
                from: "top",
                align: "center"
            },
            z_index: 1051,
            type: 'success'
        }, options || {}));
    },
    showInfo: function (msg, options) {
        $.notify({
            message: msg
        }, $.extend({
            delay: 1000,
            animate: {
                enter: 'animated bounceIn',
                exit: 'animated bounceOut'
            },
            z_index: 1051,
            type: 'info'
        }, options || {}));
    },
    showError: function (msg, options) {
        $.notify({
            message: msg
        }, $.extend({
            animate: {
                enter: 'animated bounceIn',
                exit: 'animated bounceOut'
            },
            z_index: 1051,
            type: 'danger'
        }, options || {}));
    },
    loadAssets: function (links, callback) {
        let deferreds = [];
        for (let i in links) {
            if (!links.hasOwnProperty(i)) continue;
            deferreds.push(H.loadAsset(links[i]))
        }

        $.when.all(deferreds).then(() => {
            callback();
        });
    },
    loadAsset: function (href) {
        H.loaded_assets = H.loaded_assets || {};
        var $d = $.Deferred();
        var l_key = btoa(href);

        if (H.loaded_assets[l_key]) {
            $d.resolve();
            return $d.promise();
        }

        H.loaded_assets[l_key] = true;

        if (href.match(/\.css$|\.css\.gz$/)) {
            let el = document.createElement('link');
            el.type = 'text/css';
            el.rel = 'stylesheet';
            el.href = href;
            el.onload = function () {
                $d.resolve();
            };
            document.head.appendChild(el);
        } else {
            let el = document.createElement('script');
            if (href.match(/^http/)) {
                el.crossOrigin = 'anonymous';
            }
            el.type = 'text/javascript';
            el.src = href;
            el.onload = function () {
                $d.resolve();
            };
            document.head.appendChild(el);
        }

        return $d.promise();
    }
};
H.init();
H.initVue();
H.initJQuery();


function go(a_url) {
    document.location.href = a_url;
}

function find(a_param) {
    if (typeof a_param === "string") {
        if (a_param.match(/^#/)) {
            a_param = $(a_param);
        } else {
            a_param = $("#" + a_param);
        }
    }
    return a_param;
}

function initMoment() {
    window.moment.locale(window.g_locale);
}

function initNumeral() {
    window.numeral.locale(window.g_locale);
    window.g_current_locale = window.numeral.locales[window.g_locale];
}

function isValid(v) {
    switch (typeof v) {
        case "undefined":
            return false;
            break;
        case "string":
            return (v !== "" && v !== "0");
            break;
        case "number":
            return (v > 0.0);
            break;
        case "object":
            return $.isEmptyObject(v);
            break;
        default:
            return true;
    }
}

function isTrue(v) {
    if (isValid(v)) {
        if ((v + "").match(/^(off|no|false|n|f|0)$/i)) {
            return false;
        }
        return !!v;
    }
    return false;
}

/**
 * Populates a select component
 *
 * @param a_component
 * @param a_items
 * @param a_selected_value
 */
function createSelectOptions(a_component, a_items, a_selected_value) {
    if (typeof a_selected_value === "undefined" || !a_selected_value) {
        a_selected_value = a_component.val();
    }
    if (typeof a_selected_value === "undefined" || !a_selected_value) {
        a_selected_value = a_component.data("valordef") || '';//TODO Review
    }
    a_component.find("option").not(".select_text").remove();

    var lGroup = !!a_component.data("agrupar");
    var loptions = {};
    var l_data = {};
    var loptionsgroup = {};

    if (Array.isArray(a_items) && typeof a_items[0] === "number") {
        for (let i = 0; i < a_items.length; i++) {
            loptions[a_items[i] + ''] = a_items[i] + '';
        }
    } else {
        $.each(a_items, function (i, v) {
            var ltext = '';
            var lvalue = '';
            var ltextgroup = null;
            var lvaluegroup = null;
            if (typeof v === "string") {//Provavelmente chave valor. Ex: S:Sim,N:Nao
                lvalue = i;
                ltext = v;
            } else {//Multi dimensional vindo do BD
                if (lGroup) {
                    var ii = 0;
                    var lTotal = Object.keys(v).length;
                    for (let key in v) {
                        if (ii === 0) {
                            lvaluegroup = v[key];
                        } else if (ii === 1) {
                            ltextgroup = v[key];
                        } else if (ii === 2) {
                            lvalue = v[key];
                        }
                        if (ii > 2) {
                            ltext = ltext + v[key];
                            if (ii < lTotal - 1) {
                                ltext += ', ';
                            }
                        }
                        ii++;
                    }
                } else {
                    var ii = 0;
                    var lTotal = Object.keys(v).length;
                    for (let key in v) {
                        if (ii === 0) {
                            lvalue = v[key];
                        }
                        if (ii > 0) {
                            ltext = ltext + v[key];
                            if (ii < lTotal - 1) {
                                ltext += ', ';
                            }
                        }
                        ii++;
                    }
                }
            }
            if (lvaluegroup !== null && ltextgroup !== null) {
                if (!(lvaluegroup in loptionsgroup)) {
                    loptionsgroup[lvaluegroup] = {"dsc": ltextgroup, "opt": {}};
                }
                loptionsgroup[lvaluegroup]['opt'][lvalue] = ltext;

            } else {
                loptions[lvalue] = ltext;
                l_data[lvalue] = v;
            }
        });
    }
    var loptions_html = '';
    if (a_selected_value) {
        var isMultiselect = (a_component.prop("multiple"));
        //Determinando selected
        var lvalarr = (a_selected_value + '').split(/\s*[;,]+\s*/);
        if (isMultiselect) {//Mantem a ordem
            for (var i in lvalarr) {
                if (lvalarr[i] in loptions) {
                    loptions_html += `<option value="${lvalarr[i]}" selected="selected">${loptions[lvalarr[i]].replace(/[<>]/g, "")}</option>`;
                    delete loptions[lvalarr[i]];
                }
            }
        } else {
            if (lGroup) {
                for (let ig in loptionsgroup) {
                    loptions_html += `<optgroup label="${loptionsgroup[ig].dsc.replace(/[<>]/g, "")}">`;
                    for (var i in loptionsgroup[ig].opt) {
                        loptions_html += `<option data-groupvalue="${ig}" value="${i}" ${((i + '') === (a_selected_value + '')) ? "selected=\"selected\"" : ""} >${loptionsgroup[ig].opt[i].replace(/[<>]/g, "")}</option>`;
                        delete loptionsgroup[ig].opt[i];
                    }
                    loptions_html += "</optgroup>";
                }
            } else {
                for (let i in loptions) {
                    var l_option = $("<option>").data("row", l_data[i]).val(i).text(loptions[i]);
                    if ((i + '') === (a_selected_value + '')) {
                        l_option.attr("selected", "selected");
                    }
                    a_component.append(l_option);
                    delete loptions[i];
                }
            }
        }
    }
    if (lGroup) {
        for (let ig in loptionsgroup) {
            if (!Object.keys(loptionsgroup[ig].opt).length) continue;
            loptions_html += `<optgroup label="${loptionsgroup[ig].dsc}">`;
            for (let i in loptionsgroup[ig].opt) {
                loptions_html += `<option data-groupvalue="${ig}" value="${i}" >${loptionsgroup[ig].opt[i].replace(/[<>]/g, "")}</option>`;
            }
            loptions_html += "</optgroup>";
        }
    } else {

        for (let i in loptions) {
            a_component.append($("<option>").data("row", l_data[i]).val(i).text(loptions[i]));
        }
    }
    if (loptions_html) {
        a_component.append(loptions_html);
    }
}

/**
 * Create a form, from a server response
 *
 * @param a_param
 * @param a_options
 */
function createForm(a_param, a_options) {
    a_options = a_options || {};
    a_options.onupdate = a_options.onupdate || function () {
    };
    a_options.onstore = a_options.onstore || function () {
    };

    var $form = $(`
        <div id="${a_param.id}" class="card">
            <div class="card-header"></div>
            <div class="card-body tab-content"></div>
            <div class="card-footer"></div>
        </div>`);
    var l_title = a_param.title || "Form";
    var $card_header = $form.find(".card-header").empty();
    var $card_block = $form.find(".card-body").empty();
    var $card_footer = $form.find(".card-footer").empty();
    var $title = $form.find(".card-header");
    // Tab handling
    var $card_header_tabs = $('<ul class="nav nav-tabs card-header-tabs">').appendTo($card_header);
    $card_header_tabs.on("click", function (e) {
        var $target = $(e.target);
        if ($target.is(".nav-link")) {
            $form.trigger(H.setActiveTab, [$target]);
            e.preventDefault();
        }
    });
    $form.on(H.setActiveTab, function (e, $a_elem) {
        $card_header_tabs.find(".nav-link").toggleClass("active", false);
        $a_elem.toggleClass("active", true);
        $card_block.find(".tab-pane").hide();
        $card_block.find(".tab-pane" + $a_elem.attr("href")).show();
    });
    $form.on(H.onstore, function (e, data) {
        a_options.onstore(data);
    });

    if (a_param.tabs && a_param.tabs.length) {
        a_param.tabs.forEach(function (tab) {
            $(`<li class="nav-item"><a class="nav-link" href="#${tab.id}">${tab.title}</a></li>`)
                .appendTo($card_header_tabs);
        });
    } else {
        $(`<li class="nav-item"><a class="nav-link" href="#tab0" ">${l_title}</a></li>`)
            .appendTo($card_header_tabs);
    }

    if (a_param.controls && a_param.controls.length) {
        for (let i in a_param.controls) {
            if (!a_param.controls.hasOwnProperty(i)) continue;
            createComponent($card_block, a_param.controls[i]);
        }
    }

    if (a_param.buttons && a_param.buttons.length) {
        for (let i in a_param.buttons) {
            if (!a_param.buttons.hasOwnProperty(i)) continue;
            createComponent($card_footer, a_param.buttons[i], a_param);
        }
    }

    //Botão de close
    var $button_close = $('<button type="button" class="close">&times;</button>')
        .css({
            position: 'absolute',
            right: '10px'
        });
    $card_header_tabs.append($button_close);

    var $win = $(`
        <div class="modal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content" style="box-shadow: 0 0 5px gray"></div>
            </div>
        </div>`);
    $win.find(".modal-content").append($form);
    $win.appendTo($("body"));
    $win
        .off()
        .on("shown.bs.modal", function () {
            var $that = $(this);
            $button_close.off().on('click', function () {
                $that.modal('hide');
            });
        })
        .on("hidden.bs.modal", function () {
            $(".modal").css({"overflow": "auto", "overflow-y": "scroll"});
            setTimeout(function () {
                $win.remove();
            }, 10);
        })
        .modal({
            backdrop: 'static',
            keyboard: false
        });

    if (a_param.type === "grid") {
        var l_grid;
        a_param.onLoad = function (event) {
            //
        };
        a_param.onRequest = function (event) {
            event.httpHeaders["X-CSRF-Token"] = g_csrf_token || "";
            event.httpHeaders["X-Expecting"] = "ajax-json-grid";
        };
        a_param.onAdd = function (event) {
            ajax("./" + a_param.controller + "/create", {
                "onstore": function () {
                    l_grid.reload();
                }
            });
        };
        a_param.onEdit = function (event) {
            ajax("./" + a_param.controller + "/" + event.recid + "/edit", {
                "onupdate": function (r) {
                    l_grid.reload();
                }
            });
        };
        for (var i in a_param.columns) {
            var l_column = a_param.columns[i];
            if (l_column.editable && l_column.editable.type === "select") {
                l_column.render = function (record, index, col_index) {
                    var l_val = this.getCellValue(index, col_index);
                    for (var ii in l_column.editable.items) {
                        if (l_val == l_column.editable.items[ii].id) {
                            return l_column.editable.items[ii].text || '-';
                        }
                    }
                    return '-';
                }
            }
        }
        l_grid = $('<div class="grid_container"></div>').appendTo($card_block).w2grid(a_param);
        //
    }

    if (a_param.data) {
        H.unserialize($form, a_param.data);
    }

    $form.trigger(H.setActiveTab, [$card_header_tabs.find(".nav-link").first()]);
}

/**
 * Create individual components on a form
 *
 * @param a_container
 * @param a_param
 * @param a_form_param
 */
function createComponent(a_container, a_param, a_form_param) {
    if (!window.g_component_id) window.g_component_id = 1;
    var l_opts = a_param || {};
    var l_type = l_opts.type;
    var l_subtype = l_opts.subtype || "";
    var l_id = l_opts.id || ("id" + window.g_component_id++);
    var l_name = l_opts.name;
    var l_title = l_opts.title;
    var l_grid_system = l_opts.grid_system || "col";
    var l_tab_ref = l_opts.tabref || "tab0";
    var l_container = a_container;

    var l_append = function (a_element) {//Adiciona em uma celula livre ou na parte central
        var $tab = l_container.find("#" + l_tab_ref);
        if (!$tab.length) {
            $tab = $('<div class="tab-pane" id="' + l_tab_ref + '"><div class="row"></div></div>').appendTo(l_container);
        }

        $tab.find(".row").first().append(a_element);
    };

    if (l_type === "button") {
        var $button = $(`<button class="btn">${l_title}</button>`);
        if (l_subtype) {
            if (l_opts.outline) {
                $button.addClass("btn-outline-" + l_subtype);
            } else {
                $button.addClass("btn-" + l_subtype);
            }
        }
        if (l_opts.on) {
            var l_trigger = [];
            for (let i in l_opts.on) {
                if (!l_opts.on.hasOwnProperty(i)) continue;
                let l_method = i;
                let l_code = l_opts.on[i];
                let l_params = [];
                if (typeof l_code !== 'string') {
                    l_code = l_opts.on[i].method;
                    l_params = l_opts.on[i].params;
                }
                $button.on(l_method, function () {
                    H.evalCode(l_code, l_params);
                });
                l_trigger.push(l_method);
            }

            if (l_opts.action === 'save') {
                $button.on('click', function () {
                    let $form = a_container.closest('.card');
                    let l_data = H.serialize($form);

                    H.rpc(a_form_param.rpc[0], a_form_param.rpc[1], [l_data], function (r, e) {
                        if (r) {
                            H.unserialize($form, r.data);
                            H.showInfo(r.message || 'Record saved.');
                            $form.trigger(H.onstore, [r.data]);
                        }

                        if (e) {
                            H.showError(e.message);
                            if (e.data) {
                                $form.find('.form-group').trigger(H.setValidation, [e.data]);
                            }
                        }
                    });
                });
            }

            if (l_opts.default) {
                $button.addClass("gru isDefault").on(H.defaultAction, function () {
                    $button.trigger(l_trigger.join(' '));
                });
            }
        }
        a_container.append($button);
        return;
    }

    var $inputgroup_addon = null;
    var $inputgroup_preaddon = null;
    var $form_control = null;
    var $form_group = null;
    var $label = null;

    if (l_type === "text" || l_type === "number" || l_type === "datetime" || l_type === "textarea") {
        $form_group = $(`<div class='form-group ${l_grid_system}'></div>`);
        if (l_type === "textarea") {
            $form_control = $(`<textarea class='form-control' id='${l_id}' name='${l_name}'></textarea>`);
        } else {
            $form_control = $(`<input class='form-control' type='text' id='${l_id}' name='${l_name}' />`);
        }
        if (l_subtype === "password") {
            $form_control.attr("type", "password");
            $inputgroup_preaddon = $('<div class="input-group-prepend"><div class="input-group-text"><i class="fa fa-key" aria-hidden="true"></i></div></div>');
        }
        if (l_subtype === "email") {
            $inputgroup_preaddon = $('<div class="input-group-prepend "><div class="input-group-text"><i class="fa fa-envelope-o" aria-hidden="true"></i></div></div>');
        }

        $label = $("<label for='" + l_id + "'>" + l_title + "</label>");
        if (l_opts.onchange) {
            $form_control.on("change", function () {
                H.evalCode(l_opts.onchange);
            });
        }
        if (l_opts.oninput) {
            $form_control.on("input", function () {
                H.evalCode(l_opts.oninput);
            });
        }

        if (l_opts.align) {
            $form_control.css("text-align", l_opts.align);
        }
        if (l_opts.maxlength) {
            $form_control.attr("maxlength", l_opts.maxlength);
        }
        if (l_opts.placeholder) {
            $form_control.attr("placeholder", l_opts.placeholder);
        }
        if (l_opts.mask) {
            $form_control.mask(l_opts.mask);
        }
        var l_autonumeric = null;
        var l_autonumeric_options = {
            modifyValueOnWheel: false
        };
        if (l_type === "number") {
            $form_control.attr("type", "text");
            if (l_subtype === "integer") {
                l_autonumeric_options = {
                    digitGroupSeparator: '',
                    decimalPlaces: 0
                };
            }
            if (l_subtype === "float") {
                l_autonumeric_options = {
                    digitGroupSeparator: g_current_locale.delimiters.thousands,
                    decimalCharacter: g_current_locale.delimiters.decimal
                };
            }
            if (l_subtype === "currency") {
                $inputgroup_preaddon = $(`<div class="input-group-prepend"><div class="input-group-text">${g_current_locale.currency.symbol}</div></div>`);
            }
            if (window.AutoNumeric) {
                l_autonumeric = new AutoNumeric($form_control[0], l_autonumeric_options);
                // $form_control.attr("data-vmin", l_opts.min);
                // $form_control.attr("data-vmax", l_opts.max);
            }
        }
        if (l_type === "datetime") {
            $inputgroup_addon = $('<div class="input-group-append" data-toggle="datetimepicker"><div class="input-group-text"><i class="la la-calendar"></i></div></div>');
            //Pra fazer o picker funcionar
            $inputgroup_addon.data("target", $form_control);
            $form_control.data("target", $form_control);
            $form_control.addClass("datetimepicker-input");
            $form_control.datetimepicker({
                "locale": moment.locale()
            });
        }

        $form_group.on(H.setValue, function (e, a_values) {
            if (!l_name) return;
            var $target = $(e.target);
            if ($target.is($form_group)) {
                var l_value = a_values[l_name];
                if (l_autonumeric) {
                    l_autonumeric.set(l_value);
                } else {
                    $form_control.val(l_value);
                }
            }
        });
        $form_group.on(H.getValue, function (e, a_values) {
            if (!l_name) return;
            if (l_opts.readonly && l_name !== '_id') return;
            var $target = $(e.target);
            if ($target.is($form_group)) {
                if (l_autonumeric) {
                    a_values[l_name] = l_autonumeric.get();
                } else {
                    a_values[l_name] = $form_control.val();
                }
            }
        });

    } else if (l_type === "select") {
        $form_group = $("<div class='form-group " + l_grid_system + "'></div>");
        $form_control = $("<select class='form-control' id='" + l_id + "' name='" + l_name + "' />");
        $label = $("<label for='" + l_id + "'>" + l_title + "</label>");

        if (l_opts.onchange) {
            $form_control.on("change", function () {
                H.evalCode(l_opts.onchange);
            });
        }
        l_opts.multiselect = l_opts.multiselect || l_opts.multiple;
        if (isTrue(l_opts.multiselect)) {
            $form_control.attr("multiple", true);
            $form_group.on(H.formInit, function (e, a_values) {
                if ("selectpicker" in $form_control) {
                    $form_control.selectpicker();
                }
            });
        }

        if (l_opts.items) {
            createSelectOptions($form_control, l_opts.items, l_opts.value);
        }

        $form_group.on(H.setValue, function (e, a_values) {
            if (!l_name) return;
            var $target = $(e.target);
            if ($target.is($form_group)) {
                var l_value = a_values[l_name];
                if (l_subtype === "boolean") {
                    if ((l_value + "").match(/1/)) {
                        $form_control.val("1 ");
                    } else if ((l_value + "").match(/0/)) {
                        $form_control.val("0 ");
                    } else {
                        $form_control.val("");
                    }
                } else {
                    if (isTrue(l_opts.multiselect)) {
                        if (typeof l_value === 'string') {
                            l_value = l_value.split(";");
                        }
                        $form_control.selectpicker("val", l_value);
                    } else {
                        $form_control.val(l_value);
                    }
                }
            }
        });
        $form_group.on(H.getValue, function (e, a_values) {
            if (!l_name) return;
            if (l_opts.readonly && l_name !== '_id') return;
            var $target = $(e.target);
            if ($target.is($form_group)) {
                var l_val = $form_control.val();
                if (isTrue(l_opts.multiselect)) {
                    if ($.isArray(l_val)) {
                        a_values[l_name] = l_val.join(";")
                    } else {
                        a_values[l_name] = "";
                    }
                } else {
                    a_values[l_name] = l_val;
                }
            }
        });

    } else if (l_type === "label") {
        $form_group = $("<div class='form-group " + l_grid_system + "'></div>");
        $form_control = $("<p class='lead' id='" + l_id + "'></p>");
        $label = $("<label for='" + l_id + "'>" + l_title + "</label>");

        $form_group.on(H.setValue, function (e, a_values) {
            if (!l_name) return;
            var $target = $(e.target);
            if ($target.is($form_group)) {
                var l_value = a_values[l_name];
                $form_control.text(l_value);
            }
        });
    } else {
        return;
    }
    //Common
    if (isTrue(l_opts.required)) {
        $form_control.attr("required", "required");
    }
    if (isTrue(l_opts.readonly)) {
        $form_control.attr("disabled", "disabled");
    }
    if (l_opts.placeholder) {
        $form_control.attr("title", l_opts.placeholder);
    }
    if (l_opts.help) {
        $("<small class='form-text text-muted'></small>").text(l_opts.help).appendTo($form_group);
    }
    if (l_opts.on) {
        for (var i in l_opts.on) {
            let l_method = i;
            let l_code = l_opts.on[i];
            $form_control.on(l_method, function () {
                H.evalCode(l_code);
            });
        }
    }

    var $validation_message = $("<div></div>");
    $form_group.on(H.setValidation, function (e, a_values) {
        if (!l_name) return;
        var $target = $(e.target);
        var l_message = null;
        if ($target.is($form_group)) {
            l_message = a_values[l_name];
        }
        $form_control.toggleClass('is-invalid is-valid', false);
        $validation_message.toggleClass('invalid-feedback valid-feedback', false);
        if (l_message) {
            $form_control.toggleClass('is-invalid', true);
            $validation_message.toggleClass('invalid-feedback', true);
            $validation_message.text(l_message);
        } else {
            $form_control.toggleClass('is-svalid', true);
            $validation_message.toggleClass('valid-feedback', true);
            $validation_message.text("Ok");
        }
    });
    if ($inputgroup_addon || $inputgroup_preaddon) {
        var $inputgroup = $('<div class="input-group"></div>');
        $form_group.append($label);
        $form_group.append($inputgroup);
        if ($inputgroup_preaddon) $inputgroup.append($inputgroup_preaddon);
        $inputgroup.append($form_control);
        if ($inputgroup_addon) $inputgroup.append($inputgroup_addon);
        $form_group.append($validation_message);
    } else {
        $form_group.append($label);
        $form_group.append($form_control);
        $form_group.append($validation_message);
    }
    l_append($form_group);
    $form_group.trigger(H.formInit);//Alguns componentes precisam ser inicializados depois de estar renderizado
}