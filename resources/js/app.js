// resources/js/app.js

import './bootstrap';
import { app, analytics } from './firebase-config'; // Import the firebase app and analytics instances

// You can now use the 'app' and 'analytics' instances to access Firebase services
// For example:
// import { getFirestore } from "firebase/firestore";
// const db = getFirestore(app);
// ... your other app logic ...

// This is an example to show that the app and analytics instances are available.
console.log("Firebase App Initialized:", app);
console.log("Firebase Analytics Initialized:", analytics);
