<style>
    .dots-loader span {
        display: inline-block;
        width: 8px;
        height: 8px;
        margin: 0 2px;
        background-color: {!! empty($color) ? '#fff' : 'var(--appcolor)' !!};
        border-radius: 50%;
        animation: bounce 1.2s infinite ease-in-out both;
    }

    .dots-loader span:nth-child(1) {
        animation-delay: -0.32s;
    }

    .dots-loader span:nth-child(2) {
        animation-delay: -0.16s;
    }

    @keyframes bounce {

        0%,
        80%,
        100% {
            transform: scale(0);
        }

        40% {
            transform: scale(1);
        }
    }
</style>

<span loader class="dots-loader" style="display:none;">
    <span></span><span></span><span></span>
</span>
