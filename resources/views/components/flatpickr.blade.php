<script src="{{ asset('assets/vendor/flatpickr/flatpickr.js') }}"></script>
<link rel="stylesheet" href="{{ asset('assets/vendor/flatpickr/flatpickr.css') }}">
<style>
    .flatpickr-day.selected,
    .flatpickr-day.startRange,
    .flatpickr-day.endRange,
    .flatpickr-day.selected.inRange,
    .flatpickr-day.startRange.inRange,
    .flatpickr-day.endRange.inRange,
    .flatpickr-day.selected:focus,
    .flatpickr-day.startRange:focus,
    .flatpickr-day.endRange:focus,
    .flatpickr-day.selected:hover,
    .flatpickr-day.startRange:hover,
    .flatpickr-day.endRange:hover,
    .flatpickr-day.selected.prevMonthDay,
    .flatpickr-day.startRange.prevMonthDay,
    .flatpickr-day.endRange.prevMonthDay,
    .flatpickr-day.selected.nextMonthDay,
    .flatpickr-day.startRange.nextMonthDay,
    .flatpickr-day.endRange.nextMonthDay {
        background: var(--appcolor) !important;
        border-color: #2b2424 !important;
    }
</style>
<script>
    (function(global, factory) {
        typeof exports === 'object' && typeof module !== 'undefined' ? factory(exports) :
            typeof define === 'function' && define.amd ? define(['exports'], factory) :
            (global = typeof globalThis !== 'undefined' ? globalThis : global || self, factory(global.fr = {}));
    }(this, (function(exports) {
        'use strict';

        var fp = typeof window !== "undefined" && window.flatpickr !== undefined ?
            window.flatpickr : {
                l10ns: {},
            };
        var French = {
            firstDayOfWeek: 1,
            weekdays: {
                shorthand: ["dim", "lun", "mar", "mer", "jeu", "ven", "sam"],
                longhand: [
                    "dimanche",
                    "lundi",
                    "mardi",
                    "mercredi",
                    "jeudi",
                    "vendredi",
                    "samedi",
                ],
            },
            months: {
                shorthand: [
                    "janv",
                    "févr",
                    "mars",
                    "avr",
                    "mai",
                    "juin",
                    "juil",
                    "août",
                    "sept",
                    "oct",
                    "nov",
                    "déc",
                ],
                longhand: [
                    "janvier",
                    "février",
                    "mars",
                    "avril",
                    "mai",
                    "juin",
                    "juillet",
                    "août",
                    "septembre",
                    "octobre",
                    "novembre",
                    "décembre",
                ],
            },
            ordinal: function(nth) {
                if (nth > 1)
                    return "";
                return "er";
            },
            rangeSeparator: " au ",
            weekAbbreviation: "Sem",
            scrollTitle: "Défiler pour augmenter la valeur",
            toggleTitle: "Cliquer pour basculer",
            time_24hr: true,
        };
        fp.l10ns.fr = French;
        var fr = fp.l10ns;

        exports.French = French;
        exports.default = fr;

        Object.defineProperty(exports, '__esModule', {
            value: true
        });
    })));
    flatpickr.localize(flatpickr.l10ns.fr);
</script>
