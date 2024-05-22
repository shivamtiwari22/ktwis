importScripts("https://www.gstatic.com/firebasejs/8.3.2/firebase-app.js");
importScripts("https://www.gstatic.com/firebasejs/8.3.2/firebase-messaging.js");
/*
Initialize the Firebase app in the service worker by passing in the messagingSenderId.
*/
firebase.initializeApp({
  apiKey: "AIzaSyBujCW1mClUvZ8iROVJlNFQokjFi9_HDfw",
  authDomain: "bytelogic-spark.firebaseapp.com",
  databaseURL: "https://bytelogic-spark-default-rtdb.firebaseio.com",
  projectId: "bytelogic-spark",
  storageBucket: "bytelogic-spark.appspot.com",
  messagingSenderId: "117694769619",
  appId: "1:117694769619:web:9874dbc7e6c35fdc6ee726",
  measurementId: "G-NXFJ9BCX4K",
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
