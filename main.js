document.getElementById('registrationForm').addEventListener('submit', function(event) {
    var errors = [];

    var username = document.getElementById('username').value;
    if (!/^[a-zA-Z0-9_]{3,16}$/.test(username)) {
        errors.push("A felhasználónév 3-16 betű hosszú kell, hogy legyen, és lehetnek benne betűk, és számok");
    }

    var email = document.getElementById('email').value;
    if (!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(email)) {
        errors.push("Helytelen email formátum");
    }

    var password = document.getElementById('password').value;
    var confirmPassword = document.getElementById('confirm_password').value;
    if (password.length < 8) {
        errors.push("A jelszó legalább 8 betű hosszúságú kell, hogy legyen");
    }
    if (password !== confirmPassword) {
        errors.push("Jelszavak nem egyeznek");
    }

    if (errors.length > 0) {
        event.preventDefault();
        alert(errors.join("\n"));
    }
});

