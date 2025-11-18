<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Codeflix</title>
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fontawesome-free-6.6.0-web/css/all.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    @stack('styles')
    <style>
        .search-btn {
            position: absolute;
            right: 15px;
            /* Jarak dari sisi kanan */
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            padding: 0;
            color: inherit;
            cursor: pointer;
        }

        .search-box {
            padding-right: 45px;
            /* Memberi ruang agar ikon tidak tumpang tindih */
            border-radius: 30px;
            /* Efek rounded seperti di gambar */
            background-color: #111a21;
            border: none;
        }

        .search-icon {
            font-size: 1.2rem;
        }
    </style>
</head>

<body>
    <x-navbar />

    @yield('content')

    <footer>
        <div class="text-center text-white">
            <p class="footer-title">&copy;
                <script>
                    document.write(new Date().getFullYear());
                </script> CODEPOLITAN. All rights reserved.
            </p>
        </div>
    </footer>
    <script src="{{ asset('assets/js/bootstrap.bundle.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        const swiper = new Swiper('.swiper', {
            speed: 400,
            spaceBetween: 10,
            // slidesPerView: 5,
            autoplay: {
                delay: 3000, // Mengatur jeda antar slide dalam milidetik (3 detik)
                disableOnInteraction: false, // Swiper akan tetap autoplay meskipun pengguna berinteraksi dengan slide
            },
            pagination: {
                el: '.swiper-pagination',
                type: 'bullets',
                clickable: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            breakpoints: {
                325: {
                    slidesPerView: 2,
                    spaceBetween: 20,
                },
                768: {
                    slidesPerView: 4,
                    spaceBetween: 40,
                },
                1024: {
                    slidesPerView: 5,
                    spaceBetween: 50,
                },
            },
        });
    </script>
    @stack('scripts')
</body>

</html>
