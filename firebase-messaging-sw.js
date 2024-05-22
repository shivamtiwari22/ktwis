importScripts("https://www.gstatic.com/firebasejs/8.3.2/firebase-app.js");
importScripts("https://www.gstatic.com/firebasejs/8.3.2/firebase-messaging.js");
/*
Initialize the Firebase app in the service worker by passing in the messagingSenderId.
*/
firebase.initializeApp({
  apiKey: "AIzaSyDpzrHVoIgNR8Mf8bKvX7k1z-gfq-YRxL8",
  authDomain: "gspark-1bdae.firebaseapp.com",
  projectId: "gspark-1bdae",
  storageBucket: "gspark-1bdae.appspot.com",
  messagingSenderId: "231857528437",
  appId: "1:231857528437:web:ca6e08f9e7d06d0a43e23c",
  measurementId: "G-H2TM7V6RKX"
});

// Retrieve an instance of Firebase Messaging so that it can handle background
// messages.
const messaging = firebase.messaging();
messaging.setBackgroundMessageHandler(function (payload) {
  console.log("Message received.", payload);
  const title = "Hello world is awesome";
  const options = {
    body: "Your notificaiton message .",
    icon: "/firebase-logo.png",
  };
  return self.registration.showNotification(title, options);
});
 