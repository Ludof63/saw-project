export function set_class(
    element: HTMLElement,
    cls: string,
    toggle: boolean
): void {
    if (toggle) {
        if (!element.classList.contains(cls)) element.classList.add(cls);
    } else {
        if (element.classList.contains(cls)) element.classList.remove(cls);
    }
}

export function set_visibility(element: HTMLElement, visible: boolean): void {
    set_class(element, "invisible", !visible);
}

export function show(element: HTMLElement): void {
    set_visibility(element, true);
}

export function hide(element: HTMLElement): void {
    set_visibility(element, false);
}

export function assert(
    condition: boolean,
    message?: string
): asserts condition {
    if (!condition) {
        let msg = "Assert failed";
        if (message !== null) msg = `${msg}: ${message}`;
        console.trace(msg);
        error_toast();
        throw new Error(msg);
    }
}

export function get_elem(id: string): HTMLElement {
    let result = document.getElementById(id);
    assert(result !== null, id);
    return result;
}

export function get_input(id: string): HTMLInputElement {
    let result = get_elem(id);
    assert(result.tagName === "INPUT");
    return result as HTMLInputElement;
}

export function get_div(id: string): HTMLDivElement {
    let result = get_elem(id);
    assert(result.tagName === "DIV");
    return result as HTMLDivElement;
}

export function get_span(id: string): HTMLSpanElement {
    let result = get_elem(id);
    assert(result.tagName === "SPAN", id);
    return result as HTMLSpanElement;
}

export function get_table(id: string): HTMLTableElement {
    let result = get_elem(id);
    assert(result.tagName === "TABLE");
    return result as HTMLTableElement;
}

export function is_bool(value: unknown): value is boolean {
    return typeof value === "boolean";
}

export function get_button(id: string): HTMLButtonElement {
    let result = get_elem(id);
    assert(result.tagName === "BUTTON");
    return result as HTMLButtonElement;
}

export function get_textarea(id: string): HTMLTextAreaElement {
    let result = get_elem(id);
    assert(result.tagName === "TEXTAREA");
    return result as HTMLTextAreaElement;
}

export function is_object(value: unknown): value is Record<any, unknown> {
    return typeof value === "object";
}

export function is_string(value: unknown): value is string {
    return typeof value === "string";
}

export function is_num(value: unknown): value is number {
    return typeof value === "number";
}

export function get_form(id: string): HTMLFormElement {
    let result = get_elem(id);
    assert(result.tagName === "FORM");
    return result as HTMLFormElement;
}

export function get_heading(id: string): HTMLHeadingElement {
    let result = get_elem(id);
    assert(result.tagName === "H5");
    return result as HTMLHeadingElement;
}

export function get_paragraph(id: string): HTMLParagraphElement {
    let result = get_elem(id);
    assert(result.tagName === "P");
    return result as HTMLParagraphElement;
}

export function get_tablerow(id: string): HTMLTableRowElement {
    let result = get_elem(id);
    assert(result.tagName === "TR");
    return result as HTMLTableRowElement;
}

export function get_icon(id: string): HTMLElement {
    let result = get_elem(id);
    assert(result.tagName === "I");
    return result;
}

export function set_password_visibility(
    input: HTMLInputElement,
    visible: boolean
): void {
    input.type = visible ? "text" : "password";
}

export class Guard {
    private running: boolean = false;
    public guard<T>(f: () => Promise<any>, return_value?: T): () => T {
        let guard = this;
        return function (): any {
            if (guard.running) return return_value;
            guard.running = true;
            f().then(() => (guard.running = false));
            return return_value;
        };
    }
}

export function get_articles(): HTMLDivElement[] {
    return Array.from(
        document.getElementsByTagName("ARTICLE")
    ) as HTMLDivElement[];
}
export function get_sections(): HTMLDivElement[] {
    return Array.from(
        document.getElementsByTagName("SECTION")
    ) as HTMLDivElement[];
}

export function not_undefined<T>(value: T | undefined): T {
    assert(value !== undefined);
    return value;
}

export function toggle_modal(id: string): void {
    get_div(id);
    (window as any).ui(`#${id}`);
}

const MIN_PASSWORD_LENGTH = 6;
export function calculate_password_score(pass: string): number {
    let score = 0;
    if (!pass || pass.length < MIN_PASSWORD_LENGTH) return score;

    // award every unique letter until 5 repetitions
    let letters: Record<string, number> = {};
    for (let element of pass) {
        let value = (letters[element] || 0) + 1;
        letters[element] = value;
        score += 5.0 / value;
    }

    // bonus points for mixing it up
    let variations = {
        digits: /\d/.test(pass),
        lower: /[a-z]/.test(pass),
        upper: /[A-Z]/.test(pass),
        nonWords: /\W/.test(pass),
    };

    let variationCount = 0;
    for (let variation of Object.values(variations)) {
        variationCount += variation ? 1 : 0;
    }
    score += (variationCount - 1) * 10;

    return score;
}

export function* range(end: number): Generator<number> {
    for (let i = 0; i < end; ++i) {
        yield i;
    }
}

export function error_toast(msg: string = "Ops, there was a problem"): void {
    get_span("error_toast_message").textContent = msg;
    (window as any).ui("#error_toast", 3000);
}
