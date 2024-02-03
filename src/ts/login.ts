import { login } from "./api.js";
import { MIN_PASSWORD_LENGTH } from "./api_consts.js";
import {
    get_form,
    get_input,
    Guard,
    not_undefined,
    set_password_visibility,
} from "./utils.js";
import {
    Field,
    filter_email,
    filter_email_not_exists,
    filter_min_length,
    validate_all,
} from "./validator.js";

const EMAIL_FIELD = new Field("login_email", [
    filter_email(),
    filter_email_not_exists(),
]);

const PASSWORD_FIELD = new Field("login_pass", [
    filter_min_length(MIN_PASSWORD_LENGTH),
]);
const REMEMBER = get_input("login_rememberme");

const FIELDS = { email: EMAIL_FIELD, password: PASSWORD_FIELD };
const SHOW_PASSWORD = get_input("login_show_password");
const FORM = get_form("login_form");

const GUARD = new Guard();

async function login_click(): Promise<void> {
    let values = await validate_all(FIELDS);
    if (values !== null) {
        let result = await login(
            not_undefined(values["email"]),
            not_undefined(values["password"]),
            REMEMBER.checked
        );
        if (result) location.reload();
        else PASSWORD_FIELD.set_error("wrong_password");
    }
}

function toggle_password_visibility(): void {
    set_password_visibility(PASSWORD_FIELD.input, SHOW_PASSWORD.checked);
}

function on_load() {
    SHOW_PASSWORD.onchange = toggle_password_visibility;
    EMAIL_FIELD.input.onchange = () => EMAIL_FIELD.validate();

    FORM.onsubmit = GUARD.guard(login_click, false);
}

on_load();
