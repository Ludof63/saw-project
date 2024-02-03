import { email_exists, username_exists } from "./api.js";
import {
    get_input,
    get_div,
    get_span,
    set_class,
    show,
    hide,
    get_textarea,
} from "./utils.js";

type Validator = [string, (value: string) => Promise<boolean> | boolean];
const FIELD_DIV_ID = "div";

abstract class GenericField<T extends HTMLInputElement | HTMLTextAreaElement> {
    public readonly id: string;
    public readonly input: T;
    private readonly div: HTMLDivElement;
    private readonly validators: Validator[];

    constructor(id: string, validators: Validator[]) {
        this.id = id;
        this.input = this.get_field(id);
        this.div = get_div(`${id}_${FIELD_DIV_ID}`);
        this.validators = validators;
    }

    protected abstract get_field(id: string): T;

    public get value(): string {
        return this.input.value;
    }

    private get_error_span(error: string): HTMLSpanElement {
        return get_span(`${this.id}_error_${error}`);
    }

    public clear_errors(): void {
        set_class(this.div, "invalid", false);
        for (let [error_name, _] of this.validators) {
            hide(this.get_error_span(error_name));
        }
    }

    public set_error(error: string): void {
        this.clear_errors();
        set_class(this.div, "invalid", true);
        show(this.get_error_span(error));
    }

    public async validate(): Promise<boolean> {
        let value = this.value;
        for (let [error_name, validator] of this.validators) {
            let check: boolean = await validator(value);
            if (!check) {
                this.set_error(error_name);
                return false;
            }
        }
        this.clear_errors();
        return true;
    }
}

export class Field extends GenericField<HTMLInputElement> {
    protected get_field(id: string): HTMLInputElement {
        return get_input(id);
    }
}

export class TextAreaField extends GenericField<HTMLTextAreaElement> {
    protected get_field(id: string): HTMLTextAreaElement {
        return get_textarea(id);
    }
}

export function filter_min_length(min: number): Validator {
    return ["min_length", (value) => value.length >= min];
}

export function filter_max_length(max: number): Validator {
    return ["max_length", (value) => value.length <= max];
}

export function filter_min_value(min: number): Validator {
    return ["min_value", (value) => parseInt(value) >= min];
}

export function filter_max_value(max: number): Validator {
    return ["max_value", (value) => parseInt(value) <= max];
}

export function filter_username_exists(): Validator {
    return [
        "username_exists",
        async (username) => !(await username_exists(username)),
    ];
}

export function filter_update_username_exists(old_username: string): Validator {
    return [
        "username_exists",
        async (username) =>
            username !== old_username
                ? !(await username_exists(username))
                : true,
    ];
}

const EMAIL_REGEX = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;

export function filter_email(): Validator {
    return ["email_format", (email) => EMAIL_REGEX.test(email)];
}

export function filter_int(): Validator {
    return ["points_format", (value) => !isNaN(parseInt(value))];
}

export function filter_email_exists(): Validator {
    return ["email_exists", async (email) => !(await email_exists(email))];
}

export function filter_update_email_exists(old_email: string): Validator {
    return [
        "email_exists",
        async (email) =>
            email !== old_email ? !(await username_exists(email)) : true,
    ];
}

export function filter_email_not_exists(): Validator {
    return ["email_not_exists", async (email) => await email_exists(email)];
}

export function filter_confirm(password_id: string): Validator {
    return ["confirm", (password) => get_input(password_id).value === password];
}

export function filter_nothing(id: string): Validator {
    return [id, (_) => true];
}

export async function validate_all(
    fields: Record<string, Field | TextAreaField>
): Promise<Record<string, string> | null> {
    let result: Record<string, string> = {};
    let ok = true;
    for (let [key, field] of Object.entries(fields)) {
        ok = (await field.validate()) && ok;
        result[key] = field.value;
    }

    return ok ? result : null;
}
