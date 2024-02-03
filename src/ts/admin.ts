import {
    delete_attachment,
    delete_challenge,
    edit_challenge,
    get_challenge_details_admin,
    upload_attachment,
    upload_challenge,
} from "./api.js";
import {
    MAX_ATTACHMENT_NAME_LENGTH,
    MAX_ATTACHMENT_SIZE,
    MAX_CATEGORY_LENGTH,
    MAX_CHALLENGE_DESCRIPTION_LENGTH,
    MAX_CHALLENGE_NAME_LENGTH,
    MAX_CHALLENGE_POINTS,
    MAX_FLAG_LENGTH,
    MIN_CATEGORY_LENGTH,
    MIN_CHALLENGE_NAME_LENGTH,
    MIN_CHALLENGE_POINTS,
    MIN_FLAG_LENGTH,
} from "./api_consts.js";
import {
    assert,
    error_toast,
    get_button,
    get_div,
    get_form,
    get_input,
    get_table,
    get_tablerow,
    Guard,
    hide,
    not_undefined,
    range,
    show,
    toggle_modal,
} from "./utils.js";
import {
    Field,
    filter_max_length,
    filter_min_length,
    TextAreaField,
    validate_all,
    filter_max_value,
    filter_min_value,
    filter_int,
} from "./validator.js";

const FIELDS: Record<string, Field | TextAreaField> = {
    name: new Field("name", [
        filter_min_length(MIN_CHALLENGE_NAME_LENGTH),
        filter_max_length(MAX_CHALLENGE_NAME_LENGTH),
    ]),
    points: new Field("points", [
        filter_int(),
        filter_min_value(MIN_CHALLENGE_POINTS),
        filter_max_value(MAX_CHALLENGE_POINTS),
    ]),
    category: new Field("category", [
        filter_min_length(MIN_CATEGORY_LENGTH),
        filter_max_length(MAX_CATEGORY_LENGTH),
    ]),
    flag: new Field("flag", [
        filter_min_length(MIN_FLAG_LENGTH),
        filter_max_length(MAX_FLAG_LENGTH),
    ]),
    description: new TextAreaField("description", [
        filter_max_length(MAX_CHALLENGE_DESCRIPTION_LENGTH),
    ]),
};
const ADD = get_button("add");
const DELETE = get_button("delete");
const FORM = get_form("form");
const ADD_ATTACHMENT = get_button("add_attachment");
const ATTACHMENT = get_input("attachment");
const ATTACHMENTS_DIV = get_div("attachments");
const TEMPLATE = get_div("template");
const GLOBAL_GUARD = new Guard();
const MODAL_GUARD = new Guard();

class State {
    private modal: ModalControl | null = null;

    public on_load() {
        for (let field of Object.values(FIELDS))
            field.input.onchange = () => field.validate();
        let tbody = get_table("table").tBodies[0];
        assert(tbody !== undefined);
        for (let row of tbody.rows) {
            let id = Number.parseInt(row.id);
            get_button(`edit_${id}`).onclick = GLOBAL_GUARD.guard(() =>
                this.edit_challenge_modal(id)
            );
        }
        ADD.onclick = GLOBAL_GUARD.guard(() => this.add_challenge_modal());
        DELETE.onclick = MODAL_GUARD.guard(() => this.delete());
        FORM.onsubmit = MODAL_GUARD.guard(() => this.submit(), false);
        ADD_ATTACHMENT.onclick = () => get_input("attachment").click();
        ATTACHMENT.onchange = () => this.add_attachments();
    }

    public async add_challenge_modal(): Promise<void> {
        this.modal = new AddModalControl();
        await this.modal.show();
    }

    public async edit_challenge_modal(id: number): Promise<void> {
        this.modal = new EditModalControl(id);
        await this.modal.show();
    }

    public async submit(): Promise<void> {
        assert(this.modal !== null);
        await this.modal.submit();
    }

    public async delete(): Promise<void> {
        assert(this.modal !== null);
        await this.modal.delete();
    }

    public async add_attachments(): Promise<void> {
        assert(this.modal !== null);
        assert(ATTACHMENT.files != null);
        for (let file of ATTACHMENT.files) {
            if (file.size > MAX_ATTACHMENT_SIZE * 1024 * 1024) {
                error_toast(`File '${file.name}' is too large`);
                return;
            }
            if (file.name.length > MAX_ATTACHMENT_NAME_LENGTH) {
                error_toast(`The name of '${file.name}' is too long`);
                return;
            }
            this.modal.add_attachment(file);
        }
    }
}

abstract class ModalControl {
    protected readonly files: File[] = [];

    public async submit(): Promise<void> {
        let values = await validate_all(FIELDS);
        if (values !== null) {
            let data: [string, number, string, string, string] = [
                not_undefined(values["name"]),
                Number.parseInt(not_undefined(values["points"])),
                not_undefined(values["category"]),
                not_undefined(values["flag"]),
                not_undefined(values["description"]),
            ];
            let id = await this._submit(data);
            for (let file of this.files) await upload_attachment(id, file);
            location.reload();
        }
    }

    public async show(): Promise<void> {
        for (let _ of range(ATTACHMENTS_DIV.children.length - 1)) {
            not_undefined(ATTACHMENTS_DIV.children[0]).remove();
        }
        await this._show();
        toggle_modal("modal");
    }

    public add_attachment(file: File): void {
        this.files.push(file);
        add_attachment_node(file.name, () => this.remove_attachment(file));
    }

    private remove_attachment(file: File): void {
        this.files.splice(
            this.files.findIndex((el) => el === file),
            1
        );
    }

    public async delete(): Promise<void> {
        await this._delete();
    }

    protected abstract _delete(): Promise<void>;

    protected abstract _show(): Promise<void>;

    protected abstract _submit(
        data: [string, number, string, string, string]
    ): Promise<number>;
}

class EditModalControl extends ModalControl {
    public constructor(id: number) {
        super();
        this.id = id;
    }
    private readonly deleted_files: number[] = [];
    private readonly id: number;

    private remove_existing_attachment(id: number): void {
        this.deleted_files.push(id);
    }

    protected async _show(): Promise<void> {
        let details = await get_challenge_details_admin(this.id);
        let row = get_tablerow(this.id.toString());
        let [name_cell, points_cell, category_cell] = row.cells;
        let values: Record<string, string> = {
            name: not_undefined(name_cell).innerText,
            points: not_undefined(points_cell).innerText,
            category: not_undefined(category_cell).innerText,
            flag: details.flag,
            description: details.description,
        };
        for (let key in FIELDS)
            not_undefined(FIELDS[key]).input.value = not_undefined(values[key]);
        for (let [id, name] of Object.entries(details.attachments)) {
            add_attachment_node(name, () =>
                this.remove_existing_attachment(Number.parseInt(id))
            );
        }
        show(DELETE);
    }

    protected async _delete(): Promise<void> {
        await delete_challenge(this.id);
        location.reload();
    }

    protected async _submit(
        data: [string, number, string, string, string]
    ): Promise<number> {
        await edit_challenge(this.id, ...data);
        for (let file of this.deleted_files) delete_attachment(file);
        return this.id;
    }
}

class AddModalControl extends ModalControl {
    protected async _show(): Promise<void> {
        for (let field of Object.values(FIELDS)) field.input.value = "";
        hide(DELETE);
    }

    protected async _submit(
        data: [string, number, string, string, string]
    ): Promise<number> {
        return await upload_challenge(...data);
    }

    protected async _delete(): Promise<void> {
        assert(false);
    }
}

function add_attachment_node(name: string, callback: () => any): void {
    let new_node = TEMPLATE.cloneNode(true) as HTMLDivElement;
    format_template(new_node, name, callback);
    ATTACHMENTS_DIV.insertBefore(new_node, TEMPLATE);
}

function format_template(
    node: HTMLDivElement,
    file_name: string,
    callback: () => any
): void {
    let button = not_undefined(
        node.getElementsByTagName("BUTTON")[0]
    ) as HTMLButtonElement;
    button.id = "";
    button.innerHTML = `${file_name}<i>delete_forever</i>`;
    button.onclick = () => {
        node.remove();
        callback();
    };
    let input = not_undefined(
        node.getElementsByTagName("INPUT")[0]
    ) as HTMLInputElement;
    input.remove();
}

new State().on_load();
