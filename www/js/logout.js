function Logout() {
    // Notify service worker about logout
    if ('serviceWorker' in navigator && navigator.serviceWorker.controller) {
        navigator.serviceWorker.controller.postMessage({
            action: 'logout'
        });
    }

    $.ajax({
        url: 'api/destroy_session.php',
        type: "GET",
        datatype: 'json'
    }).done(function (response) {
        if (response['code'] == 0) {
            localStorage.clear(); // delete stored mode, layout and notes
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.getRegistration('/sw.js')
                    .then((registration) => {
                        registration.unregister().then((boolean) => {
                            console.log('service worker unregistered successfully')
                        }).then(() => {
                            window.location.replace('logout.php');
                        })
                    })
            }
        }
    });
}