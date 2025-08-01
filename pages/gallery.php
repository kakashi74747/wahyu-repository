<?php
session_start();
include '../konkon.php';

if (!$kon) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Cek autentikasi
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    echo "<script>alert('Silakan login terlebih dahulu!'); window.location='/TugasAkhir/login/login.php';</script>";
    exit();
}

// Data galeri dengan deskripsi (bisa diganti dari database kalau mau)
$gallery_items = [
    [
        'image' => '../SSC/img/cassse.jpg',
        'title' => 'Castorice dengan Haku Lei 🌸✨',
        'description' => 'Hari yang cerah bersama topi bunga favoritku! 🐾💖 #CafeCassie #Kawaii',
        'source' => 'https://example.com/cassie1',
    ],
    [
        'image' => '../SSC/img/casue.jpg',
        'title' => 'Castorice💖',
        'description' => 'Kupu kupunya indah ya? #Butterfly #Nature',
        'source' => 'https://example.com/cassie2',
    ],
    [
        'image' => '../SSC/img/casssssss.jpg',
        'title' => 'Castorice with her Smoothie💜🎀',
        'description' => 'Ini smoothie favoritku! Aku sayang smoothieku seperti aku sayang kamu (W rizz)❤️✨ #CafeCassie #Rizz',
        'source' => 'https://example.com/cassie3',
    ],
    [
        'image' => '../SSC/img/cassieandmem.jpg',
        'title' => 'Castorice and Mem 🌸💫',
        'description' => 'Kenalin, ini memosprite sahabatku, Mem! Lucu kan dia? >w<💕 #mem #besties',
        'source' => 'https://example.com/cassie4',
    ],
];

include '../layout/header.php'; 
?>

<link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
    .gallery-section {
        width: 90%;
        max-width: 1200px;
        margin: 40px auto;
        text-align: center;
    }

    .gallery-header h2 {
        font-family: 'Pacifico', cursive;
        color: #5a2e8d;
        font-size: 2.5rem;
        margin-bottom: 10px;
    }

    .gallery-header p {
        color: #666;
        font-size: 1.1rem;
        margin-bottom: 30px;
    }

    .gallery-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
        gap: 20px;
    }

    .gallery-card {
        background-color: #fff;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .gallery-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(180, 120, 255, 0.3);
    }

    .gallery-img {
        width: 100%;
        height: 400px;
        object-fit: cover;
        border-bottom: 1px solid #f0f0f0;
    }

    .gallery-content {
        padding: 15px;
        text-align: left;
    }

    .gallery-title {
        font-family: 'Quicksand', sans-serif;
        font-weight: 600;
        color: #5a2e8d;
        font-size: 1.1rem;
        margin-bottom: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .gallery-description {
        font-family: 'Quicksand', sans-serif;
        font-size: 0.95rem;
        color: #666;
        line-height: 1.4;
        margin-top: 5px;
    }

    .gallery-actions {
        display: flex;
        gap: 10px;
    }

    .action-btn {
        background: none;
        border: none;
        font-size: 1.2rem;
        color: #a64dd6;
        cursor: pointer;
        transition: color 0.3s ease, transform 0.3s ease;
    }

    .action-btn:hover {
        color: #8b3cb1;
        transform: scale(1.1);
    }

    /* Ikon Like Imut */
    .like-btn {
        position: relative;
        font-size: 1rem;
        color: #a64dd6;
        background: #f9e5ff;
        border-radius: 50%;
        padding: 8px;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.3s ease, transform 0.3s ease;
    }

    .like-btn:hover {
        background: #e6c3ff;
        transform: scale(1.2);
    }

    .like-btn.liked {
        color: #ff6f91;
        background: #ffe5ec;
        animation: heartPop 0.5s ease;
    }

    /* Sparkle effect untuk animasi like */
    .like-btn.liked::after {
        content: '✨';
        position: absolute;
        font-size: 1rem;
        top: -10px;
        right: -10px;
        animation: sparkle 0.8s ease forwards;
        opacity: 0;
    }

    .like-btn.liked::before {
        content: '✨';
        position: absolute;
        font-size: 0.8rem;
        bottom: -10px;
        left: -10px;
        animation: sparkle 0.8s ease 0.2s forwards;
        opacity: 0;
    }

    /* Animasi Kawaii (Hanya untuk Like) */
    @keyframes heartPop {
        0% { transform: scale(1); }
        50% { transform: scale(1.3); }
        100% { transform: scale(1); }
    }

    @keyframes sparkle {
        0% { opacity: 0; transform: scale(0); }
        50% { opacity: 1; transform: scale(1.5) rotate(20deg); }
        100% { opacity: 0; transform: scale(1) rotate(40deg) translateY(-20px); }
    }

    /* Creator Section */
    .creator-section {
        width: 90%;
        max-width: 1200px;
        margin: 40px auto;
        background: linear-gradient(135deg, #f9e5ff, #e6c3ff);
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        text-align: center;
    }

    .creator-header h3 {
        font-family: 'Pacifico', cursive;
        color: #5a2e8d;
        font-size: 2rem;
        margin-bottom: 10px;
    }

    .creator-profile {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #fff;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        margin: 0 auto 20px;
    }

    .creator-bio {
        font-family: 'Quicksand', sans-serif;
        color: #666;
        font-size: 1rem;
        line-height: 1.6;
        max-width: 600px;
        margin: 0 auto;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .gallery-container {
            grid-template-columns: 1fr;
        }

        .gallery-img {
            height: 350px;
        }

        .gallery-title {
            font-size: 1rem;
        }

        .creator-profile {
            width: 120px;
            height: 120px;
        }

        .creator-bio {
            font-size: 0.9rem;
        }
    }
</style>

<div class="container">
    <div class="gallery-section">
        <div class="gallery-header">
            <h2>✧ Castorice Gallery ✧</h2>
            <p>Koleksi foto-foto kawaii dari owner Cafe Cassie, Miss Castorice! (≧ω≦)</p>
        </div>
        <div class="gallery-container">
            <?php foreach ($gallery_items as $item): ?>
                <div class="gallery-card">
                    <img src="<?= htmlspecialchars($item['image']) ?>" class="gallery-img" alt="<?= htmlspecialchars($item['title']) ?>">
                    <div class="gallery-content">
                        <div class="gallery-title">
                            <span><?= htmlspecialchars($item['title']) ?></span>
                            <div class="gallery-actions">
                                <button class="action-btn like-btn" onclick="toggleLike(this)">
                                    <i class="fas fa-heart"></i>
                                </button>
                                <a href="<?= htmlspecialchars($item['source']) ?>" class="action-btn" target="_blank">↗️</a>
                            </div>
                        </div>
                        <p class="gallery-description"><?= htmlspecialchars($item['description']) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="creator-section">
        <div class="creator-header">
            <h3>✧ Tentang Pembuat Website ✧</h3>
        </div>
        <img src="../SSC/img/kanadeprofile.jpg" class="creator-profile" alt="Foto Profil C">
        <div class="creator-bio">
            Halo! Aku Rin, 17 tahun. Aku adalah pembuat website untuk Cafe Cassie, tempat dimana karakter favoritku dari game Honkai: Star Rail (bukan sponsor ya) bernama Castorice menjadi owner-nya! :D 
            🌸 Aku suka bermain game gacha, dan dari situ aku terinspirasi untuk membuat website ini dengan tema kawaii yang penuh warna dan menggemaskan, mirip seperti karakter-karakter di game favoritku (╹∇╹).
            Alasan utama aku membuat tema Kafe dimulai dari tugas project sekolah. Aslinya aku tidak begitu ingin membuat tema kafe sih... tapi ya sudah terlanjut dan aku juga malas buat bikin database baru (´ｖ｀).
            Dan terwujudlah website kafe ini, yang awalnya hanya untuk tugas sekolah, menjadi tempatku untuk melatih skill coding sambil having fun! (˵ ¬ ⩊ ¬˵).
            Aku harap kamu menikmati website ini dan menemukan banyak hal menarik di dalamnya! Terima kasih ya sudah berkunjung! (≧▽≦)
        </div>
    </div>
</div>

<script>
function toggleLike(button) {
    button.classList.toggle('liked');
}
</script>

<?php include '../layout/footer.php'; ?>