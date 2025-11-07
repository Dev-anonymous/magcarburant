<script src="{{ asset('assets/vendor/jquery.min.js') }}"></script>

<script src="{{ asset('assets/vendor/popper.js') }}"></script>
<script src="{{ asset('assets/vendor/bootstrap.min.js') }}"></script>

{{-- <script src="{{ asset('assets/vendor/simplebar.js') }}"></script> --}}

{{-- <script src="{{ asset('assets/js/color_variables.js') }}"></script> --}}
<script src="{{ asset('assets/js/app.js') }}"></script>

<script src="{{ asset('assets/vendor/dom-factory.js') }}"></script>
<script src="{{ asset('assets/vendor/material-design-kit.js') }}"></script>
<script>
    (function() {
        "use strict";
        // Self Initialize DOM Factory Components
        domFactory.handler.autoInit();

        // Connect button(s) to drawer(s)
        var sidebarToggle = document.querySelectorAll(
            '[data-toggle="sidebar"]'
        );

        sidebarToggle.forEach(function(toggle) {
            toggle.addEventListener("click", function(e) {
                var selector =
                    e.currentTarget.getAttribute("data-target") || "#default-drawer";
                var drawer = document.querySelector(selector);
                if (drawer) {
                    if (selector == "#default-drawer") {
                        $(".container-fluid").toggleClass("container--max");
                    }
                    drawer.mdkDrawer.toggle();
                }
            });
        });
    })();
</script>
