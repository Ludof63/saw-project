import { complete_challenge, get_challenge_details } from "./api.js";
import {
    CHALLENGE_ALREADY_SOLVED,
    CHALLENGE_SOLVED,
    INVALID_FLAG,
    MAX_FLAG_LENGTH,
    MIN_FLAG_LENGTH,
} from "./api_consts.js";
import {
    assert,
    get_articles,
    get_div,
    get_form,
    get_heading,
    get_paragraph,
    Guard,
    not_undefined,
    range,
    toggle_modal,
} from "./utils.js";
import {
    Field,
    filter_max_length,
    filter_min_length,
    filter_nothing,
} from "./validator.js";

const FLAG = new Field("flag", [
    filter_min_length(MIN_FLAG_LENGTH),
    filter_max_length(MAX_FLAG_LENGTH),
    filter_nothing("already_solved"),
    filter_nothing("wrong"),
]);
const FORM = get_form("form");
const NAME = get_heading("name");
const DESCRIPTION = get_paragraph("description");
const ATTACHMENTS_DIV = get_div("attachments");
const TEMPLATE = get_div("template");
const GUARD = new Guard();

class Modal {
    private id: number = 0;

    public async submit(): Promise<void> {
        if (!(await FLAG.validate())) return;
        let flag = FLAG.value;
        let result = await complete_challenge(this.id, flag);
        if (result === CHALLENGE_SOLVED) location.reload();
        else if (result === CHALLENGE_ALREADY_SOLVED)
            FLAG.set_error("already_solved");
        else if (result === INVALID_FLAG) FLAG.set_error("wrong");
        else assert(false);
    }

    public async challenge_callback(id: number): Promise<void> {
        let details = await get_challenge_details(id);
        let name = get_heading(`${id}_name`).innerText;
        let description = details.description;
        let attachments = details.attachments;
        FLAG.input.value = "";
        this.id = id;
        NAME.innerText = name;
        DESCRIPTION.innerHTML = description;
        set_attachments(attachments);
        FLAG.clear_errors();
        toggle_modal("modal");
    }
}

function on_load() {
    FLAG.input.onchange = () => FLAG.validate();
    let modal = new Modal();
    for (let article of get_articles()) {
        let id = Number.parseInt(article.id);
        article.onclick = GUARD.guard(() => modal.challenge_callback(id));
    }
    FORM.onsubmit = GUARD.guard(() => modal.submit(), false);
}

function set_attachments(attachments: Record<number, string>): void {
    clear_attachments();
    for (let [id, name] of Object.entries(attachments))
        add_attachment_node(Number.parseInt(id), name);
}

function clear_attachments(): void {
    for (let _ of range(ATTACHMENTS_DIV.children.length))
        not_undefined(ATTACHMENTS_DIV.children[0]).remove();
}

function add_attachment_node(id: number, name: string): void {
    let new_node = TEMPLATE.cloneNode(true) as HTMLDivElement;
    format_template(new_node, id, name);
    ATTACHMENTS_DIV.appendChild(new_node);
}

function format_template(
    node: HTMLDivElement,
    id: number,
    file_name: string
): void {
    let a = not_undefined(node.getElementsByTagName("A")[0]) as HTMLLinkElement;
    a.innerText = file_name;
    a.href = `attachment.php?id=${id}`;
}

on_load();
