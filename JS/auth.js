// Function to check authentication
function checkAuth() {
    const username = localStorage.getItem('username');
    const token = localStorage.getItem('token');
    
    if (!username || !token) {
        window.location.href = '../Vue/Login.php';
        return false;
    }
    return true;
}

// Function to add auth parameters to URLs
function addAuthToUrl(url) {
    const username = localStorage.getItem('username');
    const token = localStorage.getItem('token');
    
    if (!username || !token) {
        return url;
    }
    
    const separator = url.includes('?') ? '&' : '?';
    return `${url}${separator}username=${encodeURIComponent(username)}&token=${encodeURIComponent(token)}`;
}

// Function to handle navigation with auth check
function navigateWithAuth(url) {
    if (checkAuth()) {
        window.location.href = addAuthToUrl(url);
    }
}

// Add auth parameters to all links on page load
document.addEventListener('DOMContentLoaded', function() {
    if (checkAuth()) {
        document.querySelectorAll('a').forEach(link => {
            if (link.href && !link.href.includes('Login.php')) {
                link.href = addAuthToUrl(link.href);
            }
        });
    }
}); 