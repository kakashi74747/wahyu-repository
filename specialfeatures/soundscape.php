<div class="soundscape">
    <button data-sound="rain">🌧️ Rainy Day</button>
    <button data-sound="jazz">🎷 Jazz Cafe</button>
</div>

<!-- fun audio :3 -->
<script>
    document.querySelectorAll('.soundscape button').forEach(btn => {
        btn.addEventListener('click', () => {
            const sound = new Audio(`../SSC/img/sounds/${btn.dataset.sound}.mp3`);
            sound.loop = true;
            sound.play();
        });
    });
</script>