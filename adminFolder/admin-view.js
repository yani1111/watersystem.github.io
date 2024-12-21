//add client
function toggleAddClientForm() {
    const form = document.getElementById('add-client-form');
    form.style.display = form.style.display === 'none' || form.style.display === '' ? 'block' : 'none';
}

//logout
function handleLogout() {
    if (confirm("Are you sure you want to logout?")) {
        window.location.href = "../logInFolder/logIn.html";// Go to login page
    }
}
//handle messages from whatsapp
function directMessage() {
    window.location.href = "https://wa.me/639760998892"
}