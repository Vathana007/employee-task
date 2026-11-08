import "./bootstrap";
import "./dark-mode";
import "../css/app.css";

import { createApp } from "vue";
import App from "./App.vue";
import router from "./router";
import i18n from "./i18n";

// Initialize the Vue application
const app = createApp(App);

app.use(router);
app.use(i18n);

app.mount("#app");

// expose toggle for non-Vue parts or simple onclicks
import { toggleDarkMode } from './dark-mode';
(window as any).toggleDarkMode = toggleDarkMode;
