function showRegisterForm() {
    document.querySelector('.login_form').style.display = 'none';
    document.querySelector('.registreer_form').style.display = 'flex';
}

function showLoginForm() {
    document.querySelector('.login_form').style.display = 'flex';
    document.querySelector('.registreer_form').style.display = 'none';
}

function zoekPersoneel() {
    let zoekInput = document.querySelector('.search_field').value.toLowerCase();
    let kaarten = document.querySelectorAll('.personeelKaart');
    kaarten.forEach(kaart => {
        let kaartText = kaart.textContent.toLowerCase();
        if (kaartText.includes(zoekInput)) {
            kaart.style.display = "";
        } else {
            kaart.style.display = "none";
        }
    });
}