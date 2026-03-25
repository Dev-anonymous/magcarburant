<style>
    #preloader {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: var(--appcolor);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 999999;
    }

    /* .spinner {
        width: 60px;
        height: 60px;
        border: 6px solid #e0e0e0;
        border-top-color: var(--appcolor) !important;
        ;
        border-radius: 50%;
        animation: spin 0.5s linear infinite;
    }

    @keyframes spin {
        100% {
            transform: rotate(360deg);
        }
    } */




    /* ///  */
    .loader {
        transform: rotateZ(45deg);
        perspective: 1000px;
        border-radius: 50%;
        width: 100px;
        height: 100px;
        color: #fff;
    }

    .loader:before,
    .loader:after {
        content: '';
        display: block;
        position: absolute;
        top: 0;
        left: 0;
        width: inherit;
        height: inherit;
        border-radius: 50%;
        transform: rotateX(70deg);
        animation: 1s spin linear infinite;
    }

    .loader:after {
        color: #FF3D00;
        transform: rotateY(70deg);
        animation-delay: .4s;
    }

    @keyframes rotate {
        0% {
            transform: translate(-50%, -50%) rotateZ(0deg);
        }

        100% {
            transform: translate(-50%, -50%) rotateZ(360deg);
        }
    }

    @keyframes rotateccw {
        0% {
            transform: translate(-50%, -50%) rotate(0deg);
        }

        100% {
            transform: translate(-50%, -50%) rotate(-360deg);
        }
    }

    @keyframes spin {

        0%,
        100% {
            box-shadow: .2em 0px 0 0px currentcolor;
        }

        12% {
            box-shadow: .2em .2em 0 0 currentcolor;
        }

        25% {
            box-shadow: 0 .2em 0 0px currentcolor;
        }

        37% {
            box-shadow: -.2em .2em 0 0 currentcolor;
        }

        50% {
            box-shadow: -.2em 0 0 0 currentcolor;
        }

        62% {
            box-shadow: -.2em -.2em 0 0 currentcolor;
        }

        75% {
            box-shadow: 0px -.2em 0 0 currentcolor;
        }

        87% {
            box-shadow: .2em -.2em 0 0 currentcolor;
        }
    }
</style>

<div>
    <div id="preloader">
        {{-- <div class="spinner"></div> --}}
        <span class="loader"></span>
    </div>
</div>




<script>
    window.addEventListener("load", function() {
        setTimeout(() => {
            const loader = document.getElementById("preloader");
            loader.style.opacity = "0";
            loader.style.transition = "opacity 0.4s ease";

            setTimeout(() => {
                loader.style.display = "none";
            }, 400);
        }, 900);
    });
</script>
