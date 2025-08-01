<?php include '../layout/header.php'; ?>

<link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;600&display=swap" rel="stylesheet">

<style>
    :root {
        --primary-color: #6c4ca3;
        --secondary-color: #e057ba;
        --accent-color: #d7b2f9;
        --text-color: #333;
        --bg-light: #fff0fb;
    }
    .greeting-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: rgba(255, 239, 239, 0.8);
        border-radius: 20px;    
        padding: 20px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        width: 770px; /* Fixed width */
        margin: 0 auto; /* Center horizontally */
    }
    .greeting-text h2 {
        font-size: 2rem;
        margin-right: 20px;
        font-family: 'Pacifico', cursive;
        color: #6c4ca3;
    }
    .greeting-text p {
        margin: 0;
        font-family: 'Pacifico', cursive;
        font-style: italic;
        color: #333;
    }
    .greeting-img {
        width: 250px;
        height: 250px; 
        border-radius: 20px;
        object-fit: cover;
        margin-left: 20px;
    }
    .special-card {
        background-color: #fff0fb;
        padding: 20px;
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        width: 100%;
        max-width: 700px;
        margin: 0 auto;
    }
    .card-header {
        background-color: #d7b2f9;
        padding: 15px;
        font-weight: bold;
        border-radius: 12px;
        color: white;
    }
    .offer-item {
        display: flex;
        align-items: center;
        margin-top: 20px;
        width: 100%;
        max-width: 700px;
        margin: 0 auto;
    }
    .offer-image {
        width: 120px; /* Adjust the size of the offer image */
        height: 120px;
        border-radius: 10px;
        object-fit: cover;
        margin-right: 20px; /* Add spacing between the image and text */
    }
    .offer-details h4 {
        margin: 0;
        color: #7343a9;
    }
    .price {
        color: #e057ba;
        font-weight: bold;
    }
    .castorice-hero {
    max-width: 400px;
    height: auto;
    filter: drop-shadow(0 8px 12px rgba(128, 90, 213, 0.3));
    transition: transform 0.3s ease;
    }

    .castorice-hero:hover {
        transform: scale(1.05);
    }

    .greeting-box {
        background: linear-gradient(to right, #f9f5ff, #f5ebff);
        border-radius: 20px;
    }

    .card-content-wrapper {
        display: flex; /* Use flexbox to align card content and GIF side by side */
        justify-content: space-between; /* Add space between the card content and GIF */
        align-items: center; /* Align items vertically */
        gap: 20px; /* Add spacing between the two sections */
    }

    .card-content {
        flex: 2; /* Make the card content take up more space */
    }

    .gif-container {
        position: absolute; /* Position the GIF container absolutely */
        top: 50%; /* Center vertically relative to the page */
        right: 20px; /* Place it 20px from the right edge of the page */
        transform: translateY(-50%); /* Adjust for perfect vertical centering */
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .castorice-gif {
        width: 200px; /* Set the width of the GIF */
        height: auto; /* Maintain the aspect ratio */
        border-radius: 12px; /* Add rounded corners */
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); /* Add a subtle shadow */
    }

    .spotify-container {
        background-color: rgba(255, 233, 233, 0.85);
        border-radius: 20px;
        padding: 20px;
        margin: 40px auto;
        width: 100%;
        max-width: 700px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        text-align: center;
    }
    .spotify-container h3 {
        font-family: 'Pacifico', cursive;
        color:rgb(172, 86, 184);
        margin-bottom: 20px;
    }
    .spotify-frame {
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .lichess-container {
    max-width: 630px;
    margin: 40px auto;
    background-color: rgba(233, 255, 253, 0.85);
    border-radius: 20px;
    padding: 20px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    text-align: center;
    }

    .responsive-video {
        position: relative;
        padding-bottom: 56.25%; /* 16:9 aspect ratio */
        height: 0;
        overflow: hidden;
        border-radius: 16px;
    }

    .responsive-video iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border: 0;
        border-radius: 16px;
    } 
</style>

    <div class="container mt-3">
        <!-- Greetingniga -->
        <div class="greeting-wrapper">
            <div class="greeting-text">
                <h2>Irrashaimase, Goshujin-sama!</h2>
                <p>Welcome to Castorice's Cafe</p>
                </div>
                <img src="../SSC/img/cassie-lucu-bgt-OMAGA.jpg" class="rounded-circle me-2 castorice-hero" style="width: 250px; height: 250px;" alt="Castorice" />
            </div>
        </div>
    </div>

    <!--Special Offer? bisa diganti harian/mingguan-->
    <div class="special-card mt-4">
        <div class="card-header text-center">
            ✩ Today's Special ✩
        </div>
        <div class="card-content-wrapper">
            <!-- Card Content -->
            <div class="card-content">
                <div class="offer-item">
                    <img src="../SSC/img/taro.jpg" style="width: 350px; height: 350px;" alt="Taro Latte" class="offer-image">
                    <class="offer-details">
                        <h4>Taro Latte Premium</h4>
                        <p>Minuman creamy dengan cita rasa taro yang lembut dan manis, cocok dinikmati saat hangat maupun dingin.</p>
                        <div class="price">Rp 45.000</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

        <!--Spotify section-->
    <div class="spotify-container">
        <h3>Now Playing🎶</h3>
        <iframe
        style="border-radius:12px" src="https://open.spotify.com/embed/track/2BBIUV8wIBbqc7HXObzdgH?utm_source=generator" width="100%" height="352" frameBorder="0" allowfullscreen="" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy">
        </iframe>
    </div>
    <div class="spotify-container">
        <h3>ただいま再生中🎶</h3>
        <iframe
        style="border-radius:12px" src="https://open.spotify.com/embed/track/2zbe90lAt3vm1WOo5OWEgD?utm_source=generator" width="100%" height="152" frameBorder="0" allowfullscreen="" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy">
        </iframe>
    </div>
    <div class="spotify-container">
        <h3>Sedang Memutar🎶</h3>
        <iframe 
        style="border-radius:12px" src="https://open.spotify.com/embed/track/5IznL77WZYvUMkNNfQa9lu?utm_source=generator" width="100%" height="152" frameBorder="0" allowfullscreen="" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy">
        </iframe>
    </div>
    <div class="spotify-container">
        <h3>正在播放🎶</h3>
        <iframe
        style="border-radius:12px" src="https://open.spotify.com/embed/playlist/4YbH8bCi3vqS7GbKNadkyw?utm_source=generator" width="100%" height="152" frameBorder="0" allowfullscreen="" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy">
        </iframe>
    </div>
    <div class="spotify-container">
        <h3>Jetzt Läuft🎶</h3>
        <iframe
        style="border-radius:12px" src="https://open.spotify.com/embed/artist/4SpbR6yFEvexJuaBpgAU5p?utm_source=generator" width="100%" height="152" frameBorder="0" allowfullscreen="" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy">
        </iframe>
    </div>
    <div class="lichess-container">
        <h3 style="font-family: 'Pacifico'; color: #47a8bd; text-align: center;">Lapwing</h3>
        <div class="responsive-video">
            <iframe 
                src="https://www.youtube.com/embed/oOIztBXox60?si=SillM5PfYgidtLhY&amp;controls=0" 
                allowfullscreen 
                title="YouTube Video Lapwing">
            </iframe>
        </div>
    </div>
</div>

<!-- GIF Container -->
<div class="gif-container">
    <img src="../SSC/img/castorice-honkai-star-rail.gif" alt="Castorice GIF" class="castorice-gif">
</div>

<?php include '../layout/footer.php';?>