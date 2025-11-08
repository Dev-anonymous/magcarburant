    <div class="loader-wrapper" dataloader>
        <div class="dots-loader">
            <span></span><span></span><span></span>
        </div>
    </div>

    <style>
        [dataloader].loader-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 20px;
        }

        [dataloader]>.dots-loader {
            display: flex;
            gap: 8px;
        }

        [dataloader]>.dots-loader span {
            width: 12px;
            height: 12px;
            background-color: var(--appcolor) !important;
            border-radius: 50%;
            animation: bounce 0.6s infinite alternate;
        }

        [dataloader]>.dots-loader span:nth-child(2) {
            animation-delay: 0.2s;
        }

        [dataloader]>.dots-loader span:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes bounce {
            to {
                transform: translateY(-10px);
            }
        }
    </style>
