import { get_button, get_icon, set_class } from "./utils.js";

const DARK_MODE_STORAGE = "S4943369.dark_mode";
const DARK_MODE_ICON = get_icon("dark_mode_icon");
const DARK_MODE_BUTTON = get_button("dark_mode");

function toggle_dark_mode(): void {
    set_dark_mode(!get_dark_mode());
}

function get_dark_mode(): boolean {
    return window.localStorage.getItem(DARK_MODE_STORAGE) === "on";
}

function set_dark_mode(on: boolean): void {
    if (on) window.localStorage.setItem(DARK_MODE_STORAGE, "on");
    else window.localStorage.removeItem(DARK_MODE_STORAGE);
    set_class(document.body, "dark", on);
    set_class(DARK_MODE_ICON, "fill", on);
}

function on_load() {
    DARK_MODE_BUTTON.onclick = toggle_dark_mode;
    set_dark_mode(get_dark_mode());
}

on_load();
