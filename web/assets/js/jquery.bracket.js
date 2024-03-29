/* jQuery Bracket | Copyright (c) Teijo Laine 2011-2015 | Licenced under the MIT licence */
!function (a) {
    function b(a) {
        return !isNaN(parseFloat(a)) && isFinite(a)
    }

    function c(a) {
        function b(a, c) {
            return a instanceof Array ? b(a[0], c + 1) : c
        }

        return b(a, 0)
    }

    function d(a, b) {
        return b > 0 && (a = d([a], b - 1)), a
    }

    function e() {
        return {source: null, name: null, id: -1, idx: -1, score: null}
    }

    function f(a) {
        if (b(a.a.score) && b(a.b.score)) {
            if (a.a.score > a.b.score)return [a.a, a.b];
            if (a.a.score < a.b.score)return [a.b, a.a]
        }
        return []
    }

    function g(a) {
        return f(a)[0] || e()
    }

    function h(a) {
        return f(a)[1] || e()
    }

    function i(b, c, d) {
        var e = d.find(".team[data-teamid=" + b + "]"), f = c ? c : "highlight";
        return {
            highlight: function () {
                e.each(function () {
                    a(this).addClass(f);
                    a(this).hasClass("win") && a(this).parent().find(".connector").addClass(f)
                })
            }, deHighlight: function () {
                e.each(function () {
                    a(this).removeClass(f);
                    a(this).parent().find(".connector").removeClass(f)
                })
            }
        }
    }

    function j(b, c, d) {
        var e = d || c, f = e.winner(), g = e.loser();
        f && g && (i(f.idx, "highlightWinner", b).highlight(), i(g.idx, "highlightLoser", b).highlight()), b.find(".team").mouseover(function () {
            var c = a(this).attr("data-teamid"), d = i(c, null, b);
            d.highlight(), a(this).mouseout(function () {
                d.deHighlight(), a(this).unbind("mouseout")
            })
        })
    }

    function k(b, c, d) {
        var e = a('<input type="text">');
        e.val(c), b.html(e), e.focus(), e.blur(function () {
            d(e.val())
        }), e.keydown(function (a) {
            var b = a.keyCode || a.which;
            (9 === b || 13 === b || 27 === b) && (a.preventDefault(), d(e.val(), 27 !== b))
        })
    }

    function l(a, b) {
        a.append(b)
    }

    function m(a) {
        var b = a.el, c = b.find(".team.win");
        c.append('<div class="bubble">1st</div>');
        var d = b.find(".team.lose");
        return d.append('<div class="bubble">2nd</div>'), !0
    }

    function n(a) {
        var b = a.el, c = b.find(".team.win");
        c.append('<div class="bubble third">3rd</div>');
        var d = b.find(".team.lose");
        return d.append('<div class="bubble fourth">4th</div>'), !0
    }

    function o(a, b, c, d, e) {
        for (var f, g = Math.log(2 * b.length) / Math.log(2), h = b.length, i = 0; g > i; i += 1) {
            f = a.addRound();
            for (var j = 0; h > j; j += 1) {
                var k = 0 === i ? v(b, j) : null;
                if (i === g - 1 && c || i === g - 1 && e) {
                    var l = f.addMatch(k, m);
                    e || l.setAlignCb(w(l, d))
                } else f.addMatch(k)
            }
            h /= 2
        }
        if (c && (a["final"]().connectorCb(function () {
                return null
            }), b.length > 1 && !d)) {
            var o = a["final"]().round().prev().match(0).loser, p = a["final"]().round().prev().match(1).loser, q = f.addMatch(function () {
                return [{source: o}, {source: p}]
            }, n);
            q.setAlignCb(function (b) {
                var c = a.el.height() / 2;
                q.el.css("height", c + "px");
                var d = b.height();
                b.css("top", d + "px")
            }), q.connectorCb(function () {
                return null
            })
        }
    }

    function p(a, b, c, d) {
        for (var e = Math.log(2 * c) / Math.log(2) - 1, f = c / 2, g = 0; e > g; g += 1) {
            for (var h = d && g === e - 1 ? 1 : 2, i = 0; h > i; i += 1)for (var j = b.addRound(), k = 0; f > k; k += 1) {
                var l = i % 2 !== 0 || 0 === g ? x(a, b, f, k, i, g) : null, m = g === e - 1 && d, o = j.addMatch(l, m ? n : null);
                if (o.setAlignCb(y(o.el.find(".teamContainer"), o)), m)o.connectorCb(function () {
                    return null
                }); else if (e - 1 > g || 1 > i) {
                    var p = i % 2 === 0 ? function (a, b) {
                        var c = a.height() / 4, d = 0, e = 0;
                        return 0 === b.winner().id ? e = c : 1 === b.winner().id ? (d = 2 * -c, e = c) : e = 2 * c, {
                            height: d,
                            shift: e
                        }
                    } : null;
                    o.connectorCb(p)
                }
            }
            f /= 2
        }
    }

    function q(a, b, c, d, e, f) {
        var g = a.addRound(), h = g.addMatch(function () {
            return [{source: b.winner}, {source: c.winner}]
        }, function (e) {
            var g = !1;
            if (d || null === e.winner().name || e.winner().name !== c.winner().name)return m(e);
            if (2 !== a.size()) {
                var h = a.addRound(function () {
                    var b = null !== e.winner().name && e.winner().name === c.winner().name;
                    return g === !1 && b && (g = !0, f.css("width", parseInt(f.css("width"), 10) + 140 + "px")), !b && g && (g = !1, a.dropRound(), f.css("width", parseInt(f.css("width"), 10) - 140 + "px")), b
                }), i = h.addMatch(function () {
                    return [{source: e.first}, {source: e.second}]
                }, m);
                return e.connectorCb(function (a) {
                    return {height: 0, shift: a.height() / 2}
                }), i.connectorCb(function () {
                    return null
                }), i.setAlignCb(function (a) {
                    var d = b.el.height() + c.el.height();
                    i.el.css("height", d + "px");
                    var e = (b.el.height() / 2 + b.el.height() + c.el.height() / 2) / 2 - a.height();
                    a.css("top", e + "px")
                }), !1
            }
        });
        if (h.setAlignCb(function (a) {
                var d = b.el.height() + c.el.height();
                e || (d /= 2), h.el.css("height", d + "px");
                var f = (b.el.height() / 2 + b.el.height() + c.el.height() / 2) / 2 - a.height();
                a.css("top", f + "px")
            }), !e) {
            var i = c["final"]().round().prev().match(0).loser, j = g.addMatch(function () {
                return [{source: i}, {source: c.loser}]
            }, n);
            j.setAlignCb(function (a) {
                var d = (b.el.height() + c.el.height()) / 2;
                j.el.css("height", d + "px");
                var e = (b.el.height() / 2 + b.el.height() + c.el.height() / 2) / 2 + a.height() / 2 - d;
                a.css("top", e + "px")
            }), h.connectorCb(function () {
                return null
            }), j.connectorCb(function () {
                return null
            })
        }
        b["final"]().connectorCb(function (a) {
            var d, e, f = a.height() / 4, g = (b.el.height() / 2 + b.el.height() + c.el.height() / 2) / 2 - a.height() / 2, h = g - b.el.height() / 2;
            return 0 === b.winner().id ? (e = h + 2 * f, d = f) : 1 === b.winner().id ? (e = h, d = 3 * f) : (e = h + f, d = 2 * f), e -= a.height() / 2, {
                height: e,
                shift: d
            }
        }), c["final"]().connectorCb(function (a) {
            var d, e, f = a.height() / 4, g = (b.el.height() / 2 + b.el.height() + c.el.height() / 2) / 2 - a.height() / 2, h = g - b.el.height() / 2;
            return 0 === c.winner().id ? (e = h, d = 3 * f) : 1 === c.winner().id ? (e = h + 2 * f, d = f) : (e = h + f, d = 2 * f), e += a.height() / 2, {
                height: -e,
                shift: -d
            }
        })
    }

    function r(b, c, d, e, f, g) {
        var h = [], i = a('<div class="round"></div>');
        return {
            el: i, bracket: b, id: d, addMatch: function (a, c) {
                var f = h.length, i = null !== a ? a() : [{source: b.round(d - 1).match(2 * f).winner}, {source: b.round(d - 1).match(2 * f + 1).winner}], j = g(this, i, f, e ? e[f] : null, c);
                return h.push(j), j
            }, match: function (a) {
                return h[a]
            }, prev: function () {
                return c
            }, size: function () {
                return h.length
            }, render: function () {
                i.empty(), ("function" != typeof f || f()) && (i.appendTo(b.el), a.each(h, function (a, b) {
                    b.render()
                }))
            }, results: function () {
                var b = [];
                return a.each(h, function (a, c) {
                    b.push(c.results())
                }), b
            }
        }
    }

    function s(b, c, d) {
        var e = [];
        return {
            el: b, addRound: function (a) {
                var b = e.length, f = b > 0 ? e[b - 1] : null, g = r(this, f, b, c ? c[b] : null, a, d);
                return e.push(g), g
            }, dropRound: function () {
                e.pop()
            }, round: function (a) {
                return e[a]
            }, size: function () {
                return e.length
            }, "final": function () {
                return e[e.length - 1].match(0)
            }, winner: function () {
                return e[e.length - 1].match(0).winner()
            }, loser: function () {
                return e[e.length - 1].match(0).loser()
            }, render: function () {
                b.empty();
                for (var a = 0; a < e.length; a += 1)e[a].render()
            }, results: function () {
                var b = [];
                return a.each(e, function (a, c) {
                    b.push(c.results())
                }), b
            }
        }
    }

    function t(b, c, d, e) {
        var f = parseInt(a(".round:first").css("margin-right"), 10) / 2, g = !0;
        0 > b && (g = !1, b = -b), 2 > b && (b = 0);
        var h = a('<div class="connector"></div>').appendTo(d);
        h.css("height", b), h.css("width", f + "px"), h.css(e, -f - 2 + "px"), c >= 0 ? h.css("top", c + "px") : h.css("bottom", -c + "px"), g ? h.css("border-bottom", "none") : h.css("border-top", "none");
        var i = a('<div class="connector"></div>').appendTo(h);
        return i.css("width", f + "px"), i.css(e, -f + "px"), g ? i.css("bottom", "0px") : i.css("top", "0px"), h
    }

    // function u(b, c, d) {
    //     var e = a('<div class="tools"></div>').appendTo(b), f = a('<span class="increment">+</span>').appendTo(e);
    //     if (f.click(function () {
    //             for (var a = c.teams.length, b = 0; a > b; b += 1)c.teams.push(["", ""]);
    //             return z(d)
    //         }), c.teams.length > 1 && 1 === c.results.length || c.teams.length > 2 && 3 === c.results.length) {
    //         var g = a('<span class="decrement">-</span>').appendTo(e);
    //         g.click(function () {
    //             return c.teams.length > 1 ? (c.teams = c.teams.slice(0, c.teams.length / 2), z(d)) : void 0
    //         })
    //     }
    //     if (1 === c.results.length && c.teams.length > 1) {
    //         var h = a('<span class="doubleElimination">de</span>').appendTo(e);
    //         h.click(function () {
    //             return c.teams.length > 1 && c.results.length < 3 ? (c.results.push([], []), z(d)) : void 0
    //         })
    //     } else if (3 === c.results.length && c.teams.length > 1) {
    //         var h = a('<span class="singleElimination">se</span>').appendTo(e);
    //         h.click(function () {
    //             return 3 === c.results.length ? (c.results = c.results.slice(0, 1), z(d)) : void 0
    //         })
    //     }
    // }

    var v = function (a, b) {
        return function () {
            return [{
                source: function () {
                    return {name: a[b][0], idx: 2 * b}
                }
            }, {
                source: function () {
                    return {name: a[b][1], idx: 2 * b + 1}
                }
            }]
        }
    }, w = function (a, b) {
        return function (c) {
            c.css("top", ""), c.css("position", "absolute"), b ? c.css("top", a.el.height() / 2 - c.height() / 2 + "px") : c.css("bottom", -c.height() / 2 + "px")
        }
    }, x = function (a, b, c, d, e, f) {
        return function () {
            if (e % 2 === 0 && 0 === f)return [{source: a.round(0).match(2 * d).loser}, {source: a.round(0).match(2 * d + 1).loser}];
            var g = f % 2 === 0 ? c - d - 1 : d;
            return [{source: b.round(2 * f).match(d).winner}, {source: a.round(f + 1).match(g).loser}]
        }
    }, y = function (a, b) {
        return function () {
            return a.css("top", b.el.height() / 2 - a.height() / 2 + "px")
        }
    }, z = function (e) {
        function f(a) {
            n = 0, w.render(), x && x.render(), y && !e.skipGrandFinalComeback && y.render(), j(z, w, y), a && (v.results[0] = w.results(), x && (v.results[1] = x.results()), y && !e.skipGrandFinalComeback && (v.results[2] = y.results()), e.save && e.save(v, e.userData))
        }

        function i(c, d, i, j, k) {
            function l(c, d, i) {
                var j = n, k = a('<div class="score" data-resultid="result-' + j + '"></div>'), l = d.name && i && b(d.score) ? d.score : "--";
                k.append(l), n += 1;
                var o = d.name ? d.name : "--", p = a('<div class="team"></div>'), q = a('<div class="label empty"></div>').appendTo(p);
                return 0 === c && p.attr("data-resultid", "team-" + j), e.decorator.render(q, o, l), b(d.idx) && p.attr("data-teamid", d.idx), null === d.name ? p.addClass("na") : g(m).name === d.name ? p.addClass("win") : h(m).name === d.name && p.addClass("lose"), p.append(k), null !== d.name && i && e.save && e.save && (q.addClass("editable"), q.click(function () {
                    /*function b() {
                     function h(h, i) {
                     h && (e.init.teams[~~(d.idx / 2)][d.idx % 2] = h), f(!0), g.click(b);
                     var j = e.el.find(".team[data-teamid=" + (d.idx + 1) + "] div.label:first");
                     j.length && i === !0 && 0 === c && a(j).click()
                     }

                     g.unbind(), e.decorator.edit(g, d.name, h)
                     }*/

                    // var g = a(this);
                    b()
                }), d.name && (k.addClass("editable"), k.click(function () {
                    function c() {
                        e.unbind();
                        var g = b(d.score) ? e.text() : "0", h = a('<input type="text">');
                        h.val(g), e.html(h), h.focus().select(), h.keydown(function (c) {
                            b(a(this).val()) ? a(this).removeClass("error") : a(this).addClass("error");
                            var d = c.keyCode || c.which;
                            if (9 === d || 13 === d || 27 === d) {
                                if (c.preventDefault(), a(this).blur(), 27 === d)return;
                                var e = z.find("div.score[data-resultid=result-" + (j + 1) + "]");
                                e && e.click()
                            }
                        }), h.blur(function () {
                            var a = h.val();
                            a && b(a) || b(d.score) ? a && b(a) || !b(d.score) || (a = d.score) : a = "0", e.html(a), b(a) && (d.score = parseInt(a, 10), f(!0)), e.click(c)
                        })
                    }

                    var e = a(this);
                    c()
                }))), p
            }

            var m = {
                a: d[0],
                b: d[1]
            }, o = null, p = null, q = a('<div class="match"></div>'), s = a('<div class="teamContainer"></div>');
            if (!e.save) {
                var u = j ? j[2] : null;
                e.onMatchHover && s.hover(function () {
                    e.onMatchHover(u, !0)
                }, function () {
                    e.onMatchHover(u, !1)
                }), e.onMatchClick && s.click(function () {
                    e.onMatchClick(u)
                })
            }
            return m.a.id = 0, m.b.id = 1, m.a.name = m.a.source().name, m.b.name = m.b.source().name, m.a.score = j ? j[0] : null, m.b.score = j ? j[1] : null, m.a.name && m.b.name || !b(m.a.score) && !b(m.b.score) || (console.log("ERROR IN SCORE DATA: " + m.a.source().name + ": " + m.a.score + ", " + m.b.source().name + ": " + m.b.score), m.a.score = m.b.score = null), {
                el: q,
                id: i,
                round: function () {
                    return c
                },
                connectorCb: function (a) {
                    o = a
                },
                connect: function (a) {
                    var b, c, d = s.height() / 4, e = q.height() / 2;
                    if (a && null !== a) {
                        var f = a(s, this);
                        if (null === f)return;
                        b = f.shift, c = f.height
                    } else i % 2 === 0 ? 0 === this.winner().id ? (b = d, c = e) : 1 === this.winner().id ? (b = 3 * d, c = e - 2 * d) : (b = 2 * d, c = e - d) : 0 === this.winner().id ? (b = 3 * -d, c = -e + 2 * d) : 1 === this.winner().id ? (b = -d, c = -e) : (b = 2 * -d, c = -e + d);
                    s.append(t(c, b, s, r))
                },
                winner: function () {
                    return g(m)
                },
                loser: function () {
                    return h(m)
                },
                first: function () {
                    return m.a
                },
                second: function () {
                    return m.b
                },
                setAlignCb: function (a) {
                    p = a
                },
                render: function () {
                    q.empty(), s.empty(), m.a.name = m.a.source().name, m.b.name = m.b.source().name, m.a.idx = m.a.source().idx, m.b.idx = m.b.source().idx, g(m).name ? s.removeClass("np") : s.addClass("np");
                    var a = (Boolean(m.a.name) || "" === m.a.name) && (Boolean(m.b.name) || "" === m.b.name);
                    s.append(l(c.id, m.a, a)), s.append(l(c.id, m.b, a)), q.appendTo(c.el), q.append(s), this.el.css("height", c.bracket.el.height() / c.size() + "px"), s.css("top", this.el.height() / 2 - s.height() / 2 + "px"), null !== p && p(s);
                    var b = "function" == typeof k ? k(this) : !1;
                    b || this.connect(o)
                },
                results: function () {
                    return [m.a.score, m.b.score]
                }
            }
        }

        function m(a) {
            return B ? Math.log(2 * a) / Math.log(2) : e.skipGrandFinalComeback ? Math.max(2, 2 * (Math.log(2 * a) / Math.log(2) - 1) - 1) : 2 * (Math.log(2 * a) / Math.log(2) - 1) + 1
        }

        var n, r = "lr" === e.dir ? "right" : "left";
        if (!e)throw Error("Options not set");
        if (!e.el)throw Error("Invalid jQuery object as container");
        if (!e.init && !e.save)throw Error("No bracket data or save callback given");
        if (void 0 === e.userData && (e.userData = null), !(!e.decorator || e.decorator.edit && e.decorator.render))throw Error("Invalid decorator input");
        e.decorator || (e.decorator = {edit: k, render: l});
        var v;
        e.init || (e.init = {teams: [["", ""]], results: []}), v = e.init;
        var w, x, y, z = a('<div class="jQBracket ' + e.dir + '"></div>').appendTo(e.el.empty()), A = d(v.results, 4 - c(v.results));
        v.results = A;
        var B = A.length <= 1;
        e.skipSecondaryFinal && B && a.error("skipSecondaryFinal setting is viable only in double elimination mode")/*, e.save && u(z, v, e)*/;//edit butttons
        var C, D, E;
        B ? D = a('<div class="bracket"></div>').appendTo(z) : (e.skipGrandFinalComeback || (C = a('<div class="finals"></div>').appendTo(z)), D = a('<div class="bracket"></div>').appendTo(z), E = a('<div class="loserBracket"></div>').appendTo(z));
        var F = 64 * v.teams.length;
        D.css("height", F), B && v.teams.length <= 2 && !e.skipConsolationRound && z.css("height", F + 40), E && E.css("height", D.height() / 2);
        var G = m(v.teams.length);
        return e.save ? z.css("width", 140 * G + 40) : z.css("width", 140 * G + 10), w = s(D, A && A[0] ? A[0] : null, i), B || (x = s(E, A && A[1] ? A[1] : null, i), e.skipGrandFinalComeback || (y = s(C, A && A[2] ? A[2] : null, i))), o(w, v.teams, B, e.skipConsolationRound, e.skipGrandFinalComeback && !B), B || (p(w, x, v.teams.length, e.skipGrandFinalComeback), e.skipGrandFinalComeback || q(y, w, x, e.skipSecondaryFinal, e.skipConsolationRound, z)), f(!1), {
            data: function () {
                return e.init
            }
        }
    }, A = {
        init: function (b) {
            var c = a.extend(!0, {}, b), d = this;
            c.el = this, c.save && (c.onMatchClick || c.onMatchHover) && a.error("Match callbacks may not be passed in edit mode (in conjunction with save callback)"), c.dir = c.dir || "lr", c.init.teams = c.init.teams && 0 !== c.init.teams.length ? c.init.teams : [["", ""]], c.skipConsolationRound = c.skipConsolationRound || !1, c.skipSecondaryFinal = c.skipSecondaryFinal || !1, "lr" !== c.dir && "rl" !== c.dir && a.error('Direction must be either: "lr" or "rl"');
            var e = z(c);
            return a(this).data("bracket", {target: d, obj: e}), e
        }, data: function () {
            var b = a(this).data("bracket");
            return b.obj.data()
        }
    };
    a.fn.bracket = function (b) {
        return A[b] ? A[b].apply(this, Array.prototype.slice.call(arguments, 1)) : "object" != typeof b && b ? void a.error("Method " + b + " does not exist on jQuery.bracket") : A.init.apply(this, arguments)
    }
}(jQuery);