"use strict";
function _typeof(t) {
    return (_typeof =
        "function" == typeof Symbol && "symbol" == typeof Symbol.iterator
            ? function t(e) {
                  return typeof e;
              }
            : function t(e) {
                  return e && "function" == typeof Symbol && e.constructor === Symbol && e !== Symbol.prototype ? "symbol" : typeof e;
              })(t);
}
function _slicedToArray(t, e) {
    return _arrayWithHoles(t) || _iterableToArrayLimit(t, e) || _unsupportedIterableToArray(t, e) || _nonIterableRest();
}
function _nonIterableRest() {
    throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
}
function _unsupportedIterableToArray(t, e) {
    if (t) {
        if ("string" == typeof t) return _arrayLikeToArray(t, e);
        var r = Object.prototype.toString.call(t).slice(8, -1);
        return "Object" === r && t.constructor && (r = t.constructor.name), "Map" === r || "Set" === r ? Array.from(t) : "Arguments" === r || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(r) ? _arrayLikeToArray(t, e) : void 0;
    }
}
function _arrayLikeToArray(t, e) {
    (null == e || e > t.length) && (e = t.length);
    for (var r = 0, n = new Array(e); r < e; r++) n[r] = t[r];
    return n;
}
function _iterableToArrayLimit(t, e) {
    if ("undefined" != typeof Symbol && Symbol.iterator in Object(t)) {
        var r = [],
            n = !0,
            o = !1,
            i = void 0;
        try {
            for (var a = t[Symbol.iterator](), c; !(n = (c = a.next()).done) && (r.push(c.value), !e || r.length !== e); n = !0);
        } catch (t) {
            (o = !0), (i = t);
        } finally {
            try {
                n || null == a.return || a.return();
            } finally {
                if (o) throw i;
            }
        }
        return r;
    }
}
function _arrayWithHoles(t) {
    if (Array.isArray(t)) return t;
}
function _get(t, e, r) {
    return (_get =
        "undefined" != typeof Reflect && Reflect.get
            ? Reflect.get
            : function t(e, r, n) {
                  var o = _superPropBase(e, r);
                  if (o) {
                      var i = Object.getOwnPropertyDescriptor(o, r);
                      return i.get ? i.get.call(n) : i.value;
                  }
              })(t, e, r || t);
}
function _superPropBase(t, e) {
    for (; !Object.prototype.hasOwnProperty.call(t, e) && null !== (t = _getPrototypeOf(t)); );
    return t;
}
function _classCallCheck(t, e) {
    if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function");
}
function _defineProperties(t, e) {
    for (var r = 0; r < e.length; r++) {
        var n = e[r];
        (n.enumerable = n.enumerable || !1), (n.configurable = !0), "value" in n && (n.writable = !0), Object.defineProperty(t, n.key, n);
    }
}
function _createClass(t, e, r) {
    return e && _defineProperties(t.prototype, e), r && _defineProperties(t, r), t;
}
function _inherits(t, e) {
    if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function");
    (t.prototype = Object.create(e && e.prototype, { constructor: { value: t, writable: !0, configurable: !0 } })), e && _setPrototypeOf(t, e);
}
function _setPrototypeOf(t, e) {
    return (_setPrototypeOf =
        Object.setPrototypeOf ||
        function t(e, r) {
            return (e.__proto__ = r), e;
        })(t, e);
}
function _createSuper(t) {
    var e = _isNativeReflectConstruct();
    return function r() {
        var n = _getPrototypeOf(t),
            o;
        if (e) {
            var i = _getPrototypeOf(this).constructor;
            o = Reflect.construct(n, arguments, i);
        } else o = n.apply(this, arguments);
        return _possibleConstructorReturn(this, o);
    };
}
function _possibleConstructorReturn(t, e) {
    return !e || ("object" !== _typeof(e) && "function" != typeof e) ? _assertThisInitialized(t) : e;
}
function _assertThisInitialized(t) {
    if (void 0 === t) throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
    return t;
}
function _isNativeReflectConstruct() {
    if ("undefined" == typeof Reflect || !Reflect.construct) return !1;
    if (Reflect.construct.sham) return !1;
    if ("function" == typeof Proxy) return !0;
    try {
        return Date.prototype.toString.call(Reflect.construct(Date, [], function () {})), !0;
    } catch (t) {
        return !1;
    }
}
function _getPrototypeOf(t) {
    return (_getPrototypeOf = Object.setPrototypeOf
        ? Object.getPrototypeOf
        : function t(e) {
              return e.__proto__ || Object.getPrototypeOf(e);
          })(t);
}
function ownKeys(t, e) {
    var r = Object.keys(t);
    if (Object.getOwnPropertySymbols) {
        var n = Object.getOwnPropertySymbols(t);
        e &&
            (n = n.filter(function (e) {
                return Object.getOwnPropertyDescriptor(t, e).enumerable;
            })),
            r.push.apply(r, n);
    }
    return r;
}
function _objectSpread(t) {
    for (var e = 1; e < arguments.length; e++) {
        var r = null != arguments[e] ? arguments[e] : {};
        e % 2
            ? ownKeys(Object(r), !0).forEach(function (e) {
                  _defineProperty(t, e, r[e]);
              })
            : Object.getOwnPropertyDescriptors
            ? Object.defineProperties(t, Object.getOwnPropertyDescriptors(r))
            : ownKeys(Object(r)).forEach(function (e) {
                  Object.defineProperty(t, e, Object.getOwnPropertyDescriptor(r, e));
              });
    }
    return t;
}
function _defineProperty(t, e, r) {
    return e in t ? Object.defineProperty(t, e, { value: r, enumerable: !0, configurable: !0, writable: !0 }) : (t[e] = r), t;
}
!(function (t, e) {
    function r(t) {
        return H[t] || !1;
    }
    function n(t) {
        return !!H[t];
    }
    function o(t) {
        return !t.attributes.id;
    }
    function i(t) {
        return u()
            .filter(function (e) {
                return e.attributes.id === t.attributes.id;
            })
            .filter(function (e) {
                return e.clientId !== t.clientId;
            }).length;
    }
    function a(t) {
        var e = t.post_types || [],
            r;
        if (e.length) {
            e.push("wp_block");
            var n = acf.get("postType");
            if (-1 === e.indexOf(n)) return !1;
        }
        if ("string" == typeof t.icon && "<svg" === t.icon.substr(0, 4)) {
            var o = t.icon;
            t.icon = React.createElement(W, null, o);
        }
        t.icon || delete t.icon,
            wp.blocks
                .getCategories()
                .filter(function (e) {
                    return e.slug === t.category;
                })
                .pop() || (t.category = "common");
        var i = { id: { type: "string" }, name: { type: "string" }, data: { type: "object" }, align: { type: "string" }, mode: { type: "string" } },
            a = q,
            c = y;
        t.supports.align_text && ((i = w(i)), (a = C(a, t))),
            t.supports.align_content && ((i = g(i)), (a = k(a, t))),
            (t = acf.parseArgs(t, {
                title: "",
                name: "",
                category: "",
                attributes: i,
                edit: function t(e) {
                    return React.createElement(a, e);
                },
                save: function t(e) {
                    return React.createElement(c, e);
                },
            })),
            (H[t.name] = t);
        var s = wp.blocks.registerBlockType(t.name, t);
        return s.attributes.anchor && (s.attributes.anchor = { type: "string" }), s;
    }
    function c(t) {
        return "core/block-editor" === t ? wp.data.select("core/block-editor") || wp.data.select("core/editor") : wp.data.select(t);
    }
    function s(t) {
        return wp.data.dispatch(t);
    }
    function u(t) {
        for (var e = c("core/block-editor").getBlocks(), r = 0; r < e.length; ) (e = e.concat(e[r].innerBlocks)), r++;
        for (var n in t)
            e = e.filter(function (e) {
                return e.attributes[n] === t[n];
            });
        return e;
    }
    function l(e) {
        var r = e.attributes,
            n = void 0 === r ? {} : r,
            o = e.query,
            i = void 0 === o ? {} : o,
            a = e.delay,
            c = void 0 === a ? 0 : a,
            s = n.id,
            u = L[s] || { query: {}, timeout: !1, promise: t.Deferred() };
        return (
            (u.query = _objectSpread(_objectSpread({}, u.query), i)),
            clearTimeout(u.timeout),
            (u.timeout = setTimeout(function () {
                t.ajax({ url: acf.get("ajaxurl"), dataType: "json", type: "post", cache: !1, data: acf.prepareForAjax({ action: "acf/ajax/fetch-block", block: JSON.stringify(n), query: u.query }) })
                    .always(function () {
                        L[s] = null;
                    })
                    .done(function () {
                        u.promise.resolve.apply(this, arguments);
                    })
                    .fail(function () {
                        u.promise.reject.apply(this, arguments);
                    });
            }, c)),
            (L[s] = u),
            u.promise
        );
    }
    function p(t, e) {
        return JSON.stringify(t) === JSON.stringify(e);
    }
    function f(t) {
        var e = h(t.nodeName.toLowerCase());
        if (!e) return null;
        var r = {};
        acf.arrayArgs(t.attributes)
            .map(d)
            .forEach(function (t) {
                r[t.name] = t.value;
            });
        var n = [e, r];
        return (
            acf.arrayArgs(t.childNodes).forEach(function (t) {
                if (t instanceof Text) {
                    var e = t.textContent.trim();
                    e && n.push(e);
                } else n.push(f(t));
            }),
            React.createElement.apply(this, n)
        );
    }
    function h(t) {
        switch (t) {
            case "innerblocks":
                return A;
            case "script":
                return z;
            case "#comment":
                return null;
        }
        return t;
    }
    function d(t) {
        var e = t.name,
            r = t.value;
        switch (e) {
            case "class":
                e = "className";
                break;
            case "style":
                var n = {};
                r.split(";").forEach(function (t) {
                    var e = t.indexOf(":");
                    if (e > 0) {
                        var r = t.substr(0, e).trim(),
                            o = t.substr(e + 1).trim();
                        "-" !== r.charAt(0) && (r = acf.strCamelCase(r)), (n[r] = o);
                    }
                }),
                    (r = n);
                break;
            default:
                if (0 === e.indexOf("data-")) break;
                var o = acf.get("jsxAttributes");
                o[e] && (e = o[e]);
                var i = r.charAt(0);
                ("[" !== i && "{" !== i) || (r = JSON.parse(r)), ("true" !== r && "false" !== r) || (r = "true" === r);
                break;
        }
        return { name: e, value: r };
    }
    function y() {
        return React.createElement(A.Content, null);
    }
    function m() {
        wp.blockEditor || (wp.blockEditor = wp.editor);
        var t = acf.get("blockTypes");
        t && t.map(a);
    }
    function b(t) {
        var e,
            r = "top";
        return ["top", "center", "bottom"].includes(t) ? t : r;
    }
    function v(t) {
        var e = ["left", "center", "right"],
            r = acf.get("rtl") ? "right" : "left";
        return e.includes(t) ? t : r;
    }
    function _(t) {
        var e = "center center";
        if (t) {
            var r,
                n = _slicedToArray(t.split(" "), 2),
                o = n[0],
                i = n[1];
            return b(o) + " " + v(i);
        }
        return e;
    }
    function g(t) {
        return (t.align_content = { type: "string" }), t;
    }
    function k(t, r) {
        var n = r.supports.align_content,
            o,
            i;
        switch (n) {
            case "matrix":
                (o = tt || Z), (i = _);
                break;
            default:
                (o = Y), (i = b);
                break;
        }
        return o === e
            ? (console.warn('The "'.concat(n, '" alignment component was not found.')), t)
            : ((r.align_content = i(r.align_content)),
              (function (e) {
                  function r() {
                      return _classCallCheck(this, r), n.apply(this, arguments);
                  }
                  _inherits(r, e);
                  var n = _createSuper(r);
                  return (
                      _createClass(r, [
                          {
                              key: "render",
                              value: function e() {
                                  function r(t) {
                                      c({ align_content: i(t) });
                                  }
                                  var n = this.props,
                                      a = n.attributes,
                                      c = n.setAttributes,
                                      s = a.align_content;
                                  return React.createElement(
                                      D,
                                      null,
                                      React.createElement(O, { group: "block" }, React.createElement(o, { label: acf.__("Change content alignment"), value: i(s), onChange: r })),
                                      React.createElement(t, this.props)
                                  );
                              },
                          },
                      ]),
                      r
                  );
              })(I));
    }
    function w(t) {
        return (t.align_text = { type: "string" }), t;
    }
    function C(t, e) {
        var r = v;
        return (
            (e.align_text = r(e.align_text)),
            (function (e) {
                function n() {
                    return _classCallCheck(this, n), o.apply(this, arguments);
                }
                _inherits(n, e);
                var o = _createSuper(n);
                return (
                    _createClass(n, [
                        {
                            key: "render",
                            value: function e() {
                                function n(t) {
                                    a({ align_text: r(t) });
                                }
                                var o = this.props,
                                    i = o.attributes,
                                    a = o.setAttributes,
                                    c = i.align_text;
                                return React.createElement(D, null, React.createElement(O, null, React.createElement(G, { value: r(c), onChange: n })), React.createElement(t, this.props));
                            },
                        },
                    ]),
                    n
                );
            })(I)
        );
    }
    var R = wp.blockEditor,
        O = R.BlockControls,
        E = R.InspectorControls,
        A = R.InnerBlocks,
        S = wp.components,
        j = S.Toolbar,
        P = S.IconButton,
        x = S.Placeholder,
        T = S.Spinner,
        D = wp.element.Fragment,
        B,
        I = React.Component,
        M = wp.data.withSelect,
        N = wp.compose.createHigherOrderComponent,
        H = {},
        L = {};
    acf.parseJSX = function (e) {
        return f(t(e)[0]);
    };
    var $ = N(function (t) {
        return (function (n) {
            function a(t) {
                var n;
                _classCallCheck(this, a);
                var s = (n = c.call(this, t)).props,
                    u = s.name,
                    l = s.attributes,
                    p = r(u);
                if (!p) return _possibleConstructorReturn(n);
                if (o(t)) {
                    for (var f in ((l.id = acf.uniqid("block_")), p.attributes)) l[f] === e && p[f] !== e && (l[f] = p[f]);
                    return _possibleConstructorReturn(n);
                }
                return i(t) ? ((l.id = acf.uniqid("block_")), _possibleConstructorReturn(n)) : n;
            }
            _inherits(a, n);
            var c = _createSuper(a);
            return (
                _createClass(a, [
                    {
                        key: "render",
                        value: function e() {
                            return React.createElement(t, this.props);
                        },
                    },
                ]),
                a
            );
        })(I);
    }, "withDefaultAttributes");
    wp.hooks.addFilter("editor.BlockListBlock", "acf/with-default-attributes", $);
    var q = (function (t) {
            function e(t) {
                var r;
                return _classCallCheck(this, e), (r = n.call(this, t)).setup(), r;
            }
            _inherits(e, t);
            var n = _createSuper(e);
            return (
                _createClass(e, [
                    {
                        key: "setup",
                        value: function t() {
                            function e(t) {
                                -1 === t.indexOf(i.mode) && (i.mode = t[0]);
                            }
                            var n = this.props,
                                o = n.name,
                                i = n.attributes,
                                a;
                            switch (r(o).mode) {
                                case "edit":
                                    e(["edit", "preview"]);
                                    break;
                                case "preview":
                                    e(["preview", "edit"]);
                                    break;
                                default:
                                    e(["auto"]);
                                    break;
                            }
                        },
                    },
                    {
                        key: "render",
                        value: function t() {
                            function e() {
                                a({ mode: "preview" === c ? "edit" : "preview" });
                            }
                            var n = this.props,
                                o = n.name,
                                i = n.attributes,
                                a = n.setAttributes,
                                c = i.mode,
                                s,
                                u = r(o).supports.mode;
                            "auto" === c && (u = !1);
                            var l = "preview" === c ? acf.__("Switch to Edit") : acf.__("Switch to Preview"),
                                p = "preview" === c ? "edit" : "welcome-view-site";
                            return React.createElement(
                                D,
                                null,
                                React.createElement(O, null, u && React.createElement(j, null, React.createElement(P, { className: "components-icon-button components-toolbar__control", label: l, icon: p, onClick: e }))),
                                React.createElement(E, null, "preview" === c && React.createElement("div", { className: "acf-block-component acf-block-panel" }, React.createElement(Q, this.props))),
                                React.createElement(J, this.props)
                            );
                        },
                    },
                ]),
                e
            );
        })(I),
        U = (function (t) {
            function e() {
                return _classCallCheck(this, e), r.apply(this, arguments);
            }
            _inherits(e, t);
            var r = _createSuper(e);
            return (
                _createClass(e, [
                    {
                        key: "render",
                        value: function t() {
                            var e = this.props,
                                r = e.attributes,
                                n = e.isSelected,
                                o = r.mode;
                            return React.createElement(
                                "div",
                                { className: "acf-block-component acf-block-body" },
                                "auto" === o && n ? React.createElement(Q, this.props) : "auto" !== o || n ? ("preview" === o ? React.createElement(X, this.props) : React.createElement(Q, this.props)) : React.createElement(X, this.props)
                            );
                        },
                    },
                ]),
                e
            );
        })(I),
        J = M(function (t, e) {
            var r = e.clientId,
                n = t("core/block-editor").getBlockRootClientId(r),
                o;
            return { index: t("core/block-editor").getBlockIndex(r, n) };
        })(U),
        W = (function (t) {
            function e() {
                return _classCallCheck(this, e), r.apply(this, arguments);
            }
            _inherits(e, t);
            var r = _createSuper(e);
            return (
                _createClass(e, [
                    {
                        key: "render",
                        value: function t() {
                            return React.createElement("div", { dangerouslySetInnerHTML: { __html: this.props.children } });
                        },
                    },
                ]),
                e
            );
        })(I),
        z = (function (e) {
            function r() {
                return _classCallCheck(this, r), n.apply(this, arguments);
            }
            _inherits(r, e);
            var n = _createSuper(r);
            return (
                _createClass(r, [
                    {
                        key: "render",
                        value: function t() {
                            var e = this;
                            return React.createElement("div", {
                                ref: function t(r) {
                                    return (e.el = r);
                                },
                            });
                        },
                    },
                    {
                        key: "setHTML",
                        value: function e(r) {
                            t(this.el).html("<script>".concat(r, "</script>"));
                        },
                    },
                    {
                        key: "componentDidUpdate",
                        value: function t() {
                            this.setHTML(this.props.children);
                        },
                    },
                    {
                        key: "componentDidMount",
                        value: function t() {
                            this.setHTML(this.props.children);
                        },
                    },
                ]),
                r
            );
        })(I),
        F = {},
        K = (function (r) {
            function n(t) {
                var e;
                return _classCallCheck(this, n), ((e = o.call(this, t)).setRef = e.setRef.bind(_assertThisInitialized(e))), (e.id = ""), (e.el = !1), (e.subscribed = !0), (e.renderMethod = "jQuery"), e.setup(t), e.loadState(), e;
            }
            _inherits(n, r);
            var o = _createSuper(n);
            return (
                _createClass(n, [
                    { key: "setup", value: function t(e) {} },
                    { key: "fetch", value: function t() {} },
                    {
                        key: "loadState",
                        value: function t() {
                            this.state = F[this.id] || {};
                        },
                    },
                    {
                        key: "setState",
                        value: function t(e) {
                            (F[this.id] = _objectSpread(_objectSpread({}, this.state), e)), this.subscribed && _get(_getPrototypeOf(n.prototype), "setState", this).call(this, e);
                        },
                    },
                    {
                        key: "setHtml",
                        value: function e(r) {
                            if ((r = r ? r.trim() : "") !== this.state.html) {
                                var n = { html: r };
                                "jsx" === this.renderMethod ? ((n.jsx = acf.parseJSX(r)), (n.$el = t(this.el))) : (n.$el = t(r)), this.setState(n);
                            }
                        },
                    },
                    {
                        key: "setRef",
                        value: function t(e) {
                            this.el = e;
                        },
                    },
                    {
                        key: "render",
                        value: function t() {
                            return this.state.jsx ? React.createElement("div", { ref: this.setRef }, this.state.jsx) : React.createElement("div", { ref: this.setRef }, React.createElement(x, null, React.createElement(T, null)));
                        },
                    },
                    {
                        key: "shouldComponentUpdate",
                        value: function t(e, r) {
                            return e.index !== this.props.index && this.componentWillMove(), r.html !== this.state.html;
                        },
                    },
                    {
                        key: "display",
                        value: function e(r) {
                            if ("jQuery" === this.renderMethod) {
                                var n = this.state.$el,
                                    o = n.parent(),
                                    i = t(this.el);
                                i.html(n), o.length && o[0] !== i[0] && o.html(n.clone());
                            }
                            switch (r) {
                                case "append":
                                    this.componentDidAppend();
                                    break;
                                case "remount":
                                    this.componentDidRemount();
                                    break;
                            }
                        },
                    },
                    {
                        key: "componentDidMount",
                        value: function t() {
                            this.state.html === e ? this.fetch() : this.display("remount");
                        },
                    },
                    {
                        key: "componentDidUpdate",
                        value: function t(e, r) {
                            this.display("append");
                        },
                    },
                    {
                        key: "componentDidAppend",
                        value: function t() {
                            acf.doAction("append", this.state.$el);
                        },
                    },
                    {
                        key: "componentWillUnmount",
                        value: function t() {
                            acf.doAction("unmount", this.state.$el), (this.subscribed = !1);
                        },
                    },
                    {
                        key: "componentDidRemount",
                        value: function t() {
                            var e = this;
                            (this.subscribed = !0),
                                setTimeout(function () {
                                    acf.doAction("remount", e.state.$el);
                                });
                        },
                    },
                    {
                        key: "componentWillMove",
                        value: function t() {
                            var e = this;
                            acf.doAction("unmount", this.state.$el),
                                setTimeout(function () {
                                    acf.doAction("remount", e.state.$el);
                                });
                        },
                    },
                ]),
                n
            );
        })(I),
        Q = (function (t) {
            function r() {
                return _classCallCheck(this, r), n.apply(this, arguments);
            }
            _inherits(r, t);
            var n = _createSuper(r);
            return (
                _createClass(r, [
                    {
                        key: "setup",
                        value: function t(e) {
                            this.id = "BlockForm-".concat(e.attributes.id);
                        },
                    },
                    {
                        key: "fetch",
                        value: function t() {
                            var e = this,
                                r;
                            l({ attributes: this.props.attributes, query: { form: !0 } }).done(function (t) {
                                e.setHtml(t.data.form);
                            });
                        },
                    },
                    {
                        key: "componentDidAppend",
                        value: function t() {
                            function n(t) {
                                var r = arguments.length > 0 && t !== e && t,
                                    n = acf.serialize(c, "acf-".concat(i.id));
                                r ? (i.data = n) : a({ data: n });
                            }
                            _get(_getPrototypeOf(r.prototype), "componentDidAppend", this).call(this);
                            var o = this.props,
                                i = o.attributes,
                                a = o.setAttributes,
                                c = this.state.$el,
                                s = !1;
                            c.on("change keyup", function () {
                                clearTimeout(s), (s = setTimeout(n, 300));
                            }),
                                i.data || n(!0);
                        },
                    },
                ]),
                r
            );
        })(K),
        X = (function (t) {
            function n() {
                return _classCallCheck(this, n), o.apply(this, arguments);
            }
            _inherits(n, t);
            var o = _createSuper(n);
            return (
                _createClass(n, [
                    {
                        key: "setup",
                        value: function t(e) {
                            var n;
                            (this.id = "BlockPreview-".concat(e.attributes.id)), r(e.name).supports.jsx && (this.renderMethod = "jsx");
                        },
                    },
                    {
                        key: "fetch",
                        value: function t(r) {
                            var n = this,
                                o = arguments.length > 0 && r !== e ? r : {},
                                i = o.attributes,
                                a = void 0 === i ? this.props.attributes : i,
                                c = o.delay,
                                s = void 0 === c ? 0 : c;
                            if ((this.setState({ prevAttributes: a }), this.state.html === e)) {
                                var u = acf.get("preloadedBlocks");
                                if (u && u[a.id]) return void this.setHtml(u[a.id]);
                            }
                            l({ attributes: a, query: { preview: !0 }, delay: s }).done(function (t) {
                                n.setHtml(t.data.preview);
                            });
                        },
                    },
                    {
                        key: "componentDidAppend",
                        value: function t() {
                            _get(_getPrototypeOf(n.prototype), "componentDidAppend", this).call(this);
                            var e = this.props.attributes,
                                r = this.state.$el,
                                o = e.name.replace("acf/", "");
                            acf.doAction("render_block_preview", r, e), acf.doAction("render_block_preview/type=".concat(o), r, e);
                        },
                    },
                    {
                        key: "shouldComponentUpdate",
                        value: function t(e, r) {
                            var o = e.attributes,
                                i = this.props.attributes;
                            if (!p(o, i)) {
                                var a = 0;
                                o.className !== i.className && (a = 300), o.anchor !== i.anchor && (a = 300), this.fetch({ attributes: o, delay: a });
                            }
                            return _get(_getPrototypeOf(n.prototype), "shouldComponentUpdate", this).call(this, e, r);
                        },
                    },
                    {
                        key: "componentDidRemount",
                        value: function t() {
                            _get(_getPrototypeOf(n.prototype), "componentDidRemount", this).call(this), p(this.state.prevAttributes, this.props.attributes) || this.fetch();
                        },
                    },
                ]),
                n
            );
        })(K);
    acf.addAction("prepare", m);
    var V = wp.blockEditor,
        G = V.AlignmentToolbar,
        Y = V.BlockVerticalAlignmentToolbar,
        Z = wp.blockEditor.__experimentalBlockAlignmentMatrixToolbar || wp.blockEditor.BlockAlignmentMatrixToolbar,
        tt = wp.blockEditor.__experimentalBlockAlignmentMatrixControl || wp.blockEditor.BlockAlignmentMatrixControl;
})(jQuery);
