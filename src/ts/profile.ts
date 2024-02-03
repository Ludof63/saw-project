import { update_user_info, update_user_password } from "./api.js";
import {
    MAX_NAME_LENGTH,
    MAX_USERNAME_LENGTH,
    MIN_NAME_LENGTH,
    MIN_PASSWORD_LENGTH,
    MIN_USERNAME_LENGTH,
} from "./api_consts.js";
import {
    calculate_password_score,
    get_button,
    get_input,
    Guard,
    set_class,
    set_password_visibility,
} from "./utils.js";
import {
    Field,
    filter_min_length,
    filter_max_length,
    filter_email,
    validate_all,
    TextAreaField,
    filter_update_username_exists,
    filter_update_email_exists,
    filter_confirm,
} from "./validator.js";

const PASSWORD_STRENGTHS = {
    password_strong: 80,
    password_good: 60,
    password_weak: 0,
    password_invalid: -1,
};

const EDIT = get_input("edit_checkbox");
const SAVE = get_button("save_button");
const CHANGE_PASS = get_button("update_password_button");

const OLD_USERNAME = get_input("update_username").value;
const OLD_EMAIL = get_input("update_email").value;

const PASSWORD_FIELD = new Field("update_pass", [
    filter_min_length(MIN_PASSWORD_LENGTH),
]);

const CONFIRM_FIELD = new Field("update_confirm", [
    filter_confirm(PASSWORD_FIELD.id),
]);

const PASS = {
    password: PASSWORD_FIELD,
    confirm: CONFIRM_FIELD,
};

const SHOW_PASSWORD = get_input("update_show_password");

const FIELDS = {
    firstname: new Field("update_firstname", [
        filter_min_length(MIN_NAME_LENGTH),
        filter_max_length(MAX_NAME_LENGTH),
    ]),
    lastname: new Field("update_lastname", [
        filter_min_length(MIN_NAME_LENGTH),
        filter_max_length(MAX_NAME_LENGTH),
    ]),
    username: new Field("update_username", [
        filter_min_length(MIN_USERNAME_LENGTH),
        filter_max_length(MAX_USERNAME_LENGTH),
        filter_update_username_exists(OLD_USERNAME),
    ]),
    email: new Field("update_email", [
        filter_email(),
        filter_update_email_exists(OLD_EMAIL),
    ]),
    bio: new TextAreaField("update_bio", []),
};

const GUARD = new Guard();

class ProfileState {
    public changes = false;
    public enabled_form = false;

    public is_changed() {
        this.changes = true;
        EDIT.disabled = true;
    }

    public edit_click() {
        SAVE.disabled = !SAVE.disabled;
        this.disable_form(this.enabled_form);
        this.enabled_form = !this.enabled_form;
    }

    public reset() {
        SAVE.disabled = true;
        EDIT.checked = false;
        this.disable_form(true);

        this.changes = false;
        this.enabled_form = false;
    }

    private disable_form(value: boolean) {
        for (let field of Object.values(FIELDS)) field.input.disabled = value;
    }
}

async function save_click(): Promise<void> {
    let values = await validate_all(FIELDS);
    if (values !== null) {
        update_user_info(
            FIELDS.email.value,
            FIELDS.username.value,
            FIELDS.firstname.value,
            FIELDS.lastname.value,
            FIELDS.bio.value
        );
        location.reload();
    }
}

async function change_pass_click(): Promise<void> {
    let values = await validate_all(PASS);
    if (values !== null) {
        update_user_password(PASS.password.value);
        location.reload();
    }
}

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

function on_load() {
    let state = new ProfileState();

    EDIT.onchange = () => state.edit_click();
    for (let field of Object.values(FIELDS)) {
        field.input.onkeyup = () => state.is_changed();
        field.input.onchange = () => field.validate();
    }

    PASSWORD_FIELD.input.onkeyup = password_strength;
    PASSWORD_FIELD.input.onchange = () => PASSWORD_FIELD.validate();
    CONFIRM_FIELD.input.onkeyup = () => CONFIRM_FIELD.validate();
    SHOW_PASSWORD.onchange = toggle_password_visibility;

    SAVE.onclick = GUARD.guard(async () => {
        if (state.changes) await save_click();
        else state.reset();
    }, false);

    CHANGE_PASS.onclick = GUARD.guard(() => change_pass_click(), false);
}

on_load();
