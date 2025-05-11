import "../css/app.css";
import "./bootstrap";

import { createInertiaApp } from "@inertiajs/react";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { createRoot } from "react-dom/client";
import axios from "axios";

const appName = import.meta.env.VITE_APP_NAME || "Laravel";

// Send cookies (not needed for JWT but harmless)
axios.defaults.withCredentials = true;
axios.defaults.baseURL = window.location.origin;

// Set JWT header if present
const jwt = localStorage.getItem("jwt");
if (jwt) {
    axios.defaults.headers.common["Authorization"] = `Bearer ${jwt}`;
}

// Mount Inertia app
createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.jsx`,
            import.meta.glob("./Pages/**/*.jsx")
        ),
    setup({ el, App, props }) {
        createRoot(el).render(<App {...props} />);
    },
    progress: { color: "#4B5563" },
});
