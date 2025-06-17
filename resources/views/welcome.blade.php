<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tlogo Putri</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-r from-green-200 via-green-100 to-green-200 min-h-screen flex items-center justify-center font-[Inter]">

    <div class="bg-white rounded-3xl shadow-xl p-10 max-w-xl w-full text-center animate-fade-in">
        <!-- Title -->
        <h1 class="text-3xl sm:text-4xl font-bold text-green-800 mb-4">
            Selamat Datang di <span class="text-green-600">Tlogo Putri</span>
        </h1>

        <!-- Description -->
        <p class="text-gray-600 mb-8">
            Rasakan serunya petualangan menggunakan Jeep menyusuri alam Tlogo Putri. Jelajahi jalur menantang, pemandangan asri, dan pengalaman tak terlupakan!
        </p>

        <!-- Button -->
        <a href="https://tlogo-putri-pengguna-za4a.vercel.app/" target="_blank"
            class="inline-block px-6 py-3 bg-green-600 hover:bg-green-700 text-white text-lg font-semibold rounded-xl transition-all duration-300 shadow-md hover:shadow-lg">
            Kunjungi Website
        </a>
    </div>

    <!-- Tailwind custom animation -->
    <style>
        @keyframes fade-in {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fade-in 1s ease-out;
        }
    </style>

</body>

</html>
