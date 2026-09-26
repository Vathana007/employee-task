import "./bootstrap";
import "../css/app.css";

import { createApp } from "vue";
import App from "./App.vue";
import router from "./router";

// Initialize the Vue application
const app = createApp(App);

app.use(router);

app.mount("#app");
