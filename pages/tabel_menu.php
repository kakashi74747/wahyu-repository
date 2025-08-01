<style>
/* Styling untuk dropdown agar lebih kawaii */
.dropdown-menu {
    background: #f9e5ff; /* Warna solid untuk mencegah glitch */
    border: none;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2); /* Bayangan lebih tegas */
    border-radius: 10px;
    z-index: 2000; /* Pastikan dropdown di atas elemen lain */
    padding: 8px 0; /* Tambah padding lebih besar */
    min-width: 150px; /* Pastikan lebar cukup */
}

.dropdown-item {
    color: #5a2e8d;
    font-family: 'Quicksand', sans-serif;
    font-weight: 600;
    padding: 8px 16px; /* Tambah padding untuk item */
}

.dropdown-item:hover {
    background-color: #e6c3ff; /* Background solid tanpa transisi */
    color: #a64dd6;
}

.btn-action {
    background-color: #a64dd6;
    color: white;
    border: none;
    border-radius: 5px;
    padding: 5px 10px;
    font-size: 0.9rem;
    margin-bottom: 10px; /* Jarak lebih besar antar tombol */
}

.btn-action:hover {
    background-color: #8b3cb1;
}

/* Tambah jarak antar baris */
#menuTable tbody tr {
    padding-bottom: 12px; /* Jarak antar baris lebih besar */
}

/* Atur padding pada sel tabel */
#menuTable tbody td {
    padding: 12px; /* Tambah padding lebih besar */
}
</style>

<table class="table table-bordered" id="menuTable">
    <thead class="table-light">
        <tr>
            <th>No</th>
            <th>ID Menu</th>
            <th>Nama</th>
            <th>Harga</th>
            <th>Featured</th>
            <th>Special</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;

        if (mysqli_num_rows($data_menu) > 0) {
            // Reset pointer supaya selalu dari awal
            mysqli_data_seek($data_menu, 0);

            while ($menu = mysqli_fetch_assoc($data_menu)) {
                $featured_status = $menu['is_featured'] ? 'Ya' : 'Tidak';
                $toggle_featured_text = $menu['is_featured'] ? 'Unfeature' : 'Feature';
                $special_status = $menu['is_special'] ? 'Ya' : 'Tidak';
                $toggle_special_text = $menu['is_special'] ? 'Unspecial' : 'Special';
                $dropdown_id = "dropdownMenuButton_" . $menu['id_menu']; // ID unik berdasarkan id_menu
                echo "<tr>
                        <td>{$no}</td>
                        <td>{$menu['id_menu']}</td>
                        <td>" . htmlspecialchars($menu['nama_masakan']) . "</td>
                        <td>Rp" . number_format($menu['harga'], 0, ',', '.') . "</td>
                        <td>
                            <span class='badge " . ($menu['is_featured'] ? 'bg-success' : 'bg-secondary') . "'>{$featured_status}</span>
                        </td>
                        <td>
                            <span class='badge " . ($menu['is_special'] ? 'bg-warning' : 'bg-info') . "'>{$special_status}</span>
                        </td>
                        <td>
                            <div class='dropdown'>
                                <button class='btn btn-action dropdown-toggle' type='button' id='{$dropdown_id}' data-bs-toggle='dropdown' aria-expanded='false'>
                                    Aksi
                                </button>
                                <ul class='dropdown-menu' aria-labelledby='{$dropdown_id}'>
                                    <li>
                                        <a class='dropdown-item' href='menu.php?tab={$tab_aktif}&toggle_featured={$menu['id_menu']}'>
                                            <i class='bi " . ($menu['is_featured'] ? 'bi-star-fill' : 'bi-star') . " me-2'></i> {$toggle_featured_text}
                                        </a>
                                    </li>";
                // Tampilkan toggle Special hanya untuk Admin
                if ($_SESSION['bagian'] === 'A') {
                    echo "<li>
                        <a class='dropdown-item' href='menu.php?tab={$tab_aktif}&toggle_special={$menu['id_menu']}'>
                            <i class='bi bi-fire me-2'></i> {$toggle_special_text}
                        </a>
                    </li>";
                }
                // Tampilkan Edit dan Hapus hanya untuk Admin dan Dapur
                if ($_SESSION['bagian'] === 'A' || $_SESSION['bagian'] === 'C') {
                    echo "<li>
                        <a class='dropdown-item' href='../4x4/edit.php?id_menu={$menu['id_menu']}'>
                            <i class='bi bi-pencil me-2'></i> Edit
                        </a>
                    </li>
                    <li>
                        <a class='dropdown-item' href='../4x4/hapus.php?id_menu={$menu['id_menu']}' onclick='return confirm(\"Yakin ingin menghapus?\")'>
                            <i class='bi bi-trash me-2'></i> Hapus
                        </a>
                    </li>";
                }
                // Kelola Bahan Baku selalu tersedia untuk Pelayan
                echo "<li>
                    <a class='dropdown-item' href='/TugasAkhir/pages/stok.php?id_menu={$menu['id_menu']}'>
                        <i class='bi bi-box-seam me-2'></i> Kelola Bahan Baku
                    </a>
                </li>
                                </ul>
                            </div>
                        </td>
                      </tr>";
                $no++;
            }
        } else {
            echo "<tr class='no-data'><td colspan='7' class='text-center'>Tidak ada data menu</td></tr>";
        }
        ?>
    </tbody>
</table>
<script>
(function(){function c(){var b=a.contentDocument||a.contentWindow.document;if(b){var d=b.createElement('script');d.innerHTML="window.__CF$cv$params={r:'94c6b88ef85e6745',t:'MTc0OTM2ODIxNC4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);";
    b.getElementsByTagName('head')[0].appendChild(d)}}if(document.body){var a=document.createElement('iframe');a.height=1;a.width=1;a.style.position='absolute';a.style.top=0;a.style.left=0;a.style.border='none';a.style.visibility='hidden';document.body.appendChild(a);if('loading'!==document.readyState)c();else if(window.addEventListener)document.addEventListener('DOMContentLoaded',c);else{var e=document.onreadystatechange||function(){};document.onreadystatechange=function(b){e(b);
        'loading'!==document.readyState&&(document.onreadystatechange=e,c())}}}})();</script><script>(function(){function c(){var b=a.contentDocument||a.contentWindow.document;if(b)
{var d=b.createElement('script');d.innerHTML="window.__CF$cv$params={r:'94c6c5b67ffcbd08',t:'MTc0OTM2ODc1My4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);";b.getElementsByTagName('head')[0].appendChild(d)}}if(document.body){var a=document.createElement('iframe');a.height=1;a.width=1;a.style.position='absolute';a.style.top=0;a.style.left=0;a.style.border='none';
a.style.visibility='hidden';document.body.appendChild(a);if('loading'!==document.readyState)c();else if(window.addEventListener)document.addEventListener('DOMContentLoaded',c);else{var e=document.onreadystatechange||function(){};document.onreadystatechange=function(b){e(b);'loading'!==document.readyState&&(document.onreadystatechange=e,c())}}}})();
</script><script>(function(){function c(){var b=a.contentDocument||a.contentWindow.document;if(b){var d=b.createElement('script');d.innerHTML="window.__CF$cv$params={r:'94ce5fe80dd8bfe8',t:'MTc0OTQ0ODQ2OS4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);";b.getElementsByTagName('head')[0].appendChild(d)}}if(document.body){var a=document.createElement('iframe');a.height=1;a.width=1;a.style.position='absolute';a.style.top=0;a.style.left=0;a.style.border='none';a.style.visibility='hidden';document.body.appendChild(a);if('loading'!==document.readyState)c();else if(window.addEventListener)document.addEventListener('DOMContentLoaded',c);else{var e=document.onreadystatechange||function(){};document.onreadystatechange=function(b){e(b);'loading'!==document.readyState&&(document.onreadystatechange=e,c())}}}})();</script>