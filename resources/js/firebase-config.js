// Import the functions you need from the SDKs you need
import { initializeApp } from "firebase/app";
import { getAnalytics } from "firebase/analytics";
// TODO: Add SDKs for Firebase products that you want to use
// https://firebase.google.com/docs/web/setup#available-libraries

// Your web app's Firebase configuration
// For Firebase JS SDK v7.20.0 and later, measurementId is optional
const firebaseConfig = {
  apiKey: "AIzaSyBjayBsPJAbg0kB5tHD-48BzoMCP6dJGaQ",
  authDomain: "inventory-4125a.firebaseapp.com",
  projectId: "inventory-4125a",
  storageBucket: "inventory-4125a.firebasestorage.app",
  messagingSenderId: "668621619267",
  appId: "1:668621619267:web:bf96e7a87f29adeae5d4f6",
  measurementId: "G-VWC3FS0JJ3"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);
const analytics = getAnalytics(app);

// Export the app and analytics instances for use in other files
export { app, analytics };
