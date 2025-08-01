<link rel="stylesheet" href="../SSC/cssmaxxing.css">
<footer class="text-center py-4 navbar-custom" style="border-top-left-radius: 20px; border-top-right-radius: 20px;">
  <p style="margin: 0; font-family: 'Quicksand', sans-serif; font-size: 1rem; color: var(--text-dark);">
    ✨ © 2025 Trippi Troppi ✦ Web by Rin ft. Copii & Goku ✨
  </p>
  <?php if(basename($_SERVER['PHP_SELF']) == 'home.php'): ?>
    <script src="../SSC/js/games.js"></script>
  <?php endif; ?>
  <?php
ob_end_flush();
?>
</body>
</footer>
</html>