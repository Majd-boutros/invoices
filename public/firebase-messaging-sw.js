/*
Give the service worker access to Firebase Messaging.
Note that you can only use Firebase Messaging here, other Firebase libraries are not available in the service worker.
*/
importScripts('https://www.gstatic.com/firebasejs/7.23.0/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/7.23.0/firebase-messaging.js');

/*
Initialize the Firebase app in the service worker by passing in the messagingSenderId.
* New configuration for app@pulseservice.com
*/
firebase.initializeApp({
    apiKey: "AIzaSyBA8EiCzulG7cjU5eIj1Kj0vKd6asyxR78",
    authDomain: "laravel-65e1b.firebaseapp.com",
    databaseURL: "https://laravel-65e1b.firebaseio.com",
    projectId: "laravel-65e1b",
    storageBucket: "laravel-65e1b.appspot.com",
    messagingSenderId: "522282824103",
    appId: "1:522282824103:web:0ecec8ef39830de8d04682",
    measurementId: "G-572BWJ0R62"
});

/*
Retrieve an instance of Firebase Messaging so that it can handle background messages.
*/
const messaging = firebase.messaging();
messaging.setBackgroundMessageHandler(function(payload) {
    console.log(
        "[firebase-messaging-sw.js] Received background message ",
        payload,
    );
    /* Customize notification here */
    const notificationTitle = "Background Message Title";
    const notificationOptions = {
        body: "Background Message body.",
        icon: "/itwonders-web-logo.png",
    };

    return self.registration.showNotification(
        notificationTitle,
        notificationOptions,
    );
});


