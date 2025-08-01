<?php
// Tentukan bulan saat ini
$month = date('m');
$login_type = $_SESSION['login_type'] ?? null;

?>

<div class="seasonal-banner">
    <?php
    // Logika untuk banner berdasarkan bulan dan tipe login
    if ($month >= 3 && $month <= 5) { // Musim Semi (Maret-Mei)
        if ($login_type === 'admin') {
            echo "🌸 Oi Atmin! Kenalan dong, aku punya Macaron nih WKWKWK 😭";
        } elseif ($login_type === 'karyawan') {
            // Cek bagian karyawan
            $bagian = $_SESSION['bagian'] ?? null;
            if ($bagian === 'A') { // Admin
                echo "🌸 Yo Admin! Cek stok Macaron dong, musim semi gini nih BZZZ 🐝";
            } elseif ($bagian === 'B') { // Kasir
                echo "🌸 Kasir nih! Macaron musim semi gas pol promosi dong SKSKSK 🤪";
            } elseif ($bagian === 'C') { // Dapur
                echo "🌸 Dapur sini! Bikin Macaron musim semi biar gemes maksimal AOWKWOK 😍";
            } elseif ($bagian === 'D') { // Pelayan
                echo "🌸 Pelayan on duty! Sapa tamu pake Macaron gemes ini dong YGY 🫶";
            }
        } elseif ($login_type === 'user' || $login_type === null) { // Pelanggan
            echo "🌸 Goshujin-sama! Cobain Macaron musim semi, gemes bgt sih MEOW MEOW 😺";
        }
    } elseif ($month >= 6 && $month <= 8) { // Musim Panas (Juni-Agustus)
        if ($login_type === 'admin') {
            echo "☀️ Atmin! Ice Cream panas-panas gini siapin dong, panik nih AAAAA 🔥";
        } elseif ($login_type === 'karyawan') {
            $bagian = $_SESSION['bagian'] ?? null;
            if ($bagian === 'A') {
                echo "☀️ Atminnn! Aku mau Ice Cream donh (❁´◡`❁)";
            } elseif ($bagian === 'B') {
                echo "☀️ Kasir sini! Promoin Ice Cream, panas gini enak nih SOT SOT 🍦";
            } elseif ($bagian === 'C') {
                echo "☀️ Dapur! Aku mw ice cream😋, overheating nih";
            } elseif ($bagian === 'D') {
                echo "☀️ Woi pelayan! Semangat kerjanya yah di musim panas! aku kasih es nih🍧";
            }
        } elseif ($login_type === 'user' || $login_type === null) {
            echo "☀️ Halo Goshujin-sama! Aku punya Ice Cream🍦 Kenalan yuk, Aku Gue :D";
        }
    } elseif ($month >= 9 && $month <= 11) { // Musim Gugur (September-November)
        if ($login_type === 'admin') {
            echo "🍂 Atmin! Pumpkin Spice Latte musim gugur siapin dong, vibes bgt nih EHEHE 🍁";
        } elseif ($login_type === 'karyawan') {
            $bagian = $_SESSION['bagian'] ?? null;
            if ($bagian === 'A') {
                echo "🍂 Admin! Cek stok Pumpkin Spice Latte dong, musim gugur nih BZZZ 🍂";
            } elseif ($bagian === 'B') {
                echo "🍂 Kasir! Jual Pumpkin Spice Latte gas pol, musim gugur vibes YGY 🍁";
            } elseif ($bagian === 'C') {
                echo "🍂 Dapur! Bikin Pumpkin Spice Latte musim gugur dong, gemes bgt AOWKWOK 🍂";
            } elseif ($bagian === 'D') {
                echo "🍂 Pelayan nih! Saranin Pumpkin Spice Latte ke tamu, vibes bgt SKSKSK 🍁";
            }
        } elseif ($login_type === 'user' || $login_type === null) {
            echo "🍂 Goshujin-sama! Pumpkin Spice Latte musim gugur nih, coba dong MEOW 🍂";
        }
    } else { // Musim Dingin (Desember-Februari)
        if ($login_type === 'admin') {
            echo "☕ Atmin! Hot Chocolate musim dingin siapin dong, anget nih BRRR 🥶";
        } elseif ($login_type === 'karyawan') {
            $bagian = $_SESSION['bagian'] ?? null;
            if ($bagian === 'A') {
                echo "☕ Admin! Cek stok Hot Chocolate dong, musim dingin nih BZZZ 🥶";
            } elseif ($bagian === 'B') {
                echo "☕ Kasir! Promoin Hot Chocolate, anget maksimal YGY ☕";
            } elseif ($bagian === 'C') {
                echo "☕ Dapur! Bikin Hot Chocolate musim dingin dong, anget bgt AOWKWOK 🥵";
            } elseif ($bagian === 'D') {
                echo "☕ Pelayan nih! Tawarin Hot Chocolate ke tamu, anget dong SKSKSK ☕";
            }
        } elseif ($login_type === 'user' || $login_type === null) {
            echo "☕ Goshujin-sama! Hot Chocolate musim dingin anget bgt nih, coba dong MEOW ☕";
        }
    }
    ?>
</div>

<!-- <style>
.seasonal-banner {
    background: linear-gradient(135deg, #f9e5ff, #e6c3ff);
    border-radius: 15px;
    padding: 20px;
    text-align: center;
    font-family: 'Pacifico', cursive;
    color: #5a2e8d;
    font-size: 1.8rem;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    margin: 20px auto;
    width: 90%;
    max-width: 800px;
    position: relative;
    overflow: hidden;
    transition: transform 0.3s ease;
}

.seasonal-banner:hover {
    transform: scale(1.02);
    box-shadow: 0 6px 20px rgba(180, 120, 255, 0.3);
}

/* Efek dekoratif */
.seasonal-banner::before {
    content: '';
    position: absolute;
    top: -50px;
    left: -50px;
    width: 100px;
    height: 100px;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.5), transparent);
    opacity: 0.3;
}

.seasonal-banner::after {
    content: '';
    position: absolute;
    bottom: -50px;
    right: -50px;
    width: 100px;
    height: 100px;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.5), transparent);
    opacity: 0.3;
}

@media (max-width: 768px) {
    .seasonal-banner {
        font-size: 1.5rem;
        padding: 15px;
    }
}
</style> -->