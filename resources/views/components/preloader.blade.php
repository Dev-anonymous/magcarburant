<style>
    #preloader {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 999999;
    }

    .spinner {
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
    }
</style>

<div>
    <div id="preloader">
        <div class="spinner"></div>
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
