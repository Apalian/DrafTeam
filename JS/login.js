async function login(event) {
    event.preventDefault(); // Empêche le rechargement de la page

    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;

    const response = await fetch('https://drafteamauthentication.lespi.fr/authentication.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ login: username, password: password })
    });

    const data = await response.json();

    if (data.status === 'success') {
        // Stocker les informations dans le localStorage
        localStorage.setItem('username', username);
        localStorage.setItem('role', data.data.role);

        window.location.href = '../index.php'; // Rediriger vers la page d'accueil
    } else {
        // Afficher un message d'erreur
        alert(data.status_message);
    }
} 