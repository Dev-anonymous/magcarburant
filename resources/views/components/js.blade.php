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

        document.addEventListener("DOMContentLoaded", () => {
            setTimeout(() => {
                const drawer = document.querySelector("#default-drawer");
                drawer?.mdkDrawer?.close();
            }, 0);
        });
        $.ajaxSetup({
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('_token'),
                'Accept': 'application/json',
                'x-page-mode': '{{ $mode }}'
            }
        });


        $('[logout]').on('click', function(e) {
            e.preventDefault();
            var btn = $(this);
            btn.children().hide();
            $('[loader]', btn).show();

            $.ajax({
                url: '{{ route('api.logout') }}',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'accept': 'application/json'
                },
            }).always(function() {
                location.reload();
            })
        });
    })();
</script>
