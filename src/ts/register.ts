import { register } from "./api.js";
import {
    MAX_NAME_LENGTH,
    MAX_USERNAME_LENGTH,
    MIN_NAME_LENGTH,
    MIN_PASSWORD_LENGTH,
    MIN_USERNAME_LENGTH,
} from "./api_consts.js";
import {
    calculate_password_score,
    get_form,
    get_input,
    Guard,
    not_undefined,
    set_class,
    set_password_visibility,
} from "./utils.js";
import {
    Field,
    filter_confirm,
    filter_email,
    filter_email_exists,
    filter_max_length,
    filter_min_length,
    filter_username_exists,
    validate_all,
} from "./validator.js";

const PASSWORD_FIELD = new Field("register_pass", [
    filter_min_length(MIN_PASSWORD_LENGTH),
]);

const CONFIRM_FIELD = new Field("register_confirm", [
    filter_confirm(PASSWORD_FIELD.id),
]);

const FIELDS = {
    firstname: new Field("register_firstname", [
        filter_min_length(MIN_NAME_LENGTH),
        filter_max_length(MAX_NAME_LENGTH),
    ]),
    lastname: new Field("register_lastname", [
        filter_min_length(MIN_NAME_LENGTH),
        filter_max_length(MAX_NAME_LENGTH),
    ]),
    username: new Field("register_username", [
        filter_min_length(MIN_USERNAME_LENGTH),
        filter_max_length(MAX_USERNAME_LENGTH),
        filter_username_exists(),
    ]),
    email: new Field("register_email", [filter_email(), filter_email_exists()]),
    password: PASSWORD_FIELD,
    confirm: CONFIRM_FIELD,
};

const PASSWORD_STRENGTHS = {
    password_strong: 80,
    password_good: 60,
    password_weak: 0,
    password_invalid: -1,
};

const SHOW_PASSWORD = get_input("register_show_password");
const FORM = get_form("register_form");
const REMEMBER = get_input("register_rememberme");

const GUARD = new Guard();

function password_strength(): void {
    let score = calculate_password_score(PASSWORD_FIELD.value);
    for (let strength in PASSWORD_STRENGTHS)
        set_class(PASSWORD_FIELD.input, strength, false);
    for (let [strength, limit] of Object.entries(PASSWORD_STRENGTHS)) {
        if (score > limit) {
            set_class(PASSWORD_FIELD.input, strength, true);
            break;
        }
    }
}

function toggle_password_visibility(): void {
    set_password_visibility(PASSWORD_FIELD.input, SHOW_PASSWORD.checked);
    set_password_visibility(CONFIRM_FIELD.input, SHOW_PASSWORD.checked);
}

async function register_click(): Promise<void> {
    let values = await validate_all(FIELDS);
    if (values !== null) {
        register(
            not_undefined(values["email"]),
            not_undefined(values["username"]),
            not_undefined(values["password"]),
            not_undefined(values["firstname"]),
            not_undefined(values["lastname"]),
            REMEMBER.checked
        );
        location.reload();
    }
}

function on_load() {
    for (let field of Object.values(FIELDS))
        field.input.onchange = () => field.validate();

    PASSWORD_FIELD.input.onkeyup = password_strength;
    CONFIRM_FIELD.input.onkeyup = () => CONFIRM_FIELD.validate;

    //ON CLICK
    SHOW_PASSWORD.onchange = toggle_password_visibility;

    FORM.onsubmit = GUARD.guard(register_click, false);
}

on_load();
