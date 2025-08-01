// Game Tangkap Kopi (lanjut soon)
document.addEventListener('DOMContentLoaded', () => {
    const gameArea = document.createElement('div');
    gameArea.innerHTML = `
        <div class="coffee-game">
            <div class="coffee-bean"></div>
            <p>Click the bean 10x for discount!</p>
            <p class="score">Score: 0</p>
        </div>
    `;
    document.body.appendChild(gameArea);

    const bean = document.querySelector('.coffee-bean');
    let score = 0;

    bean.addEventListener('click', () => {
        score++;
        bean.style.top = `${Math.random() * 80}%`;
        bean.style.left = `${Math.random() * 80}%`;
        document.querySelector('.score').textContent = `Score: ${score}`;
        
        if(score >= 10) {
            alert('🎉 You got 5% discount!');
        }
    });
});