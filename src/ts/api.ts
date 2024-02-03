import { ApiType, POST_API } from "./api_consts.js";
import { assert, is_bool, is_num, is_object, is_string } from "./utils.js";

function get_url(relative_url: string): URL {
    let url = new URL(".", location.href);
    url = new URL(relative_url, url);
    return url;
}

async function get(url: URL, data: Record<string, string>): Promise<Response> {
    let copy = new URL(url);
    for (let [key, value] of Object.entries(data)) {
        copy.searchParams.append(key, value);
    }
    return await fetch(copy, { method: "GET", credentials: "same-origin" });
}

async function post(
    url: URL,
    data: Record<string, string | File>
): Promise<Response> {
    let copy = new FormData();
    for (let [key, value] of Object.entries(data)) {
        copy.append(key, value);
    }
    return await fetch(url, {
        body: copy,
        credentials: "same-origin",
        method: "POST",
    });
}

async function ajax_call(
    url: URL,
    data: Record<string, string | File>,
    is_get: boolean
): Promise<Response> {
    return await (is_get
        ? get(url, data as Record<string, string>)
        : post(url, data));
}

async function call_api(
    type: ApiType,
    params: Record<string, string | File>
): Promise<unknown> {
    let response = await ajax_call(
        get_url(`api/${type}.php`),
        params,
        !POST_API.includes(type)
    );
    assert(response.ok);
    let result: unknown = await response.json();
    return result;
}

export async function username_exists(username: string): Promise<boolean> {
    let response = await call_api(ApiType.USERNAME_EXISTS, {
        username: username,
    });
    assert(is_bool(response));
    return response;
}

export async function email_exists(email: string): Promise<boolean> {
    let response = await call_api(ApiType.EMAIL_EXISTS, { email: email });
    assert(is_bool(response));
    return response;
}

export async function register(
    email: string,
    username: string,
    password: string,
    firstname: string,
    lastname: string,
    rememberme: boolean
): Promise<void> {
    let content = await call_api(ApiType.REGISTRATION, {
        email: email,
        username: username,
        firstname: firstname,
        lastname: lastname,
        pass: password,
        confirm: password,
        remember: rememberme.toString(),
    });
    assert(is_object(content));
    assert(content["status"] === "ok");
}

export async function login(
    email: string,
    password: string,
    rememberme: boolean
): Promise<boolean> {
    let content = await call_api(ApiType.LOGIN, {
        email: email,
        pass: password,
        remember: rememberme.toString(),
    });
    assert(is_bool(content));
    return content;
}

export async function upload_challenge(
    name: string,
    points: number,
    category: string,
    flag: string,
    description: string
): Promise<number> {
    let content = await call_api(ApiType.UPLOAD_CHALLENGE, {
        name,
        points: points.toString(),
        category,
        flag,
        description,
    });
    assert(is_object(content));
    assert(content["status"] === "ok");
    let id = content["id"];
    assert(is_num(id));
    return id;
}

interface ChallengeDescription {
    description: string;
    attachments: Record<number, string>;
}

interface AdminChallengeDescription extends ChallengeDescription {
    flag: string;
}

function is_challenge_description(
    value: unknown
): value is ChallengeDescription {
    return (
        is_object(value) &&
        is_string(value["description"]) &&
        "attachments" in value
    );
}

function is_challenge_flag(value: unknown): value is AdminChallengeDescription {
    return (
        is_object(value) &&
        is_challenge_description(value) &&
        is_string(value["flag"])
    );
}

export async function get_challenge_details(
    id: number
): Promise<ChallengeDescription> {
    let content = await call_api(ApiType.CHALLENGE_DESCRIPTION, {
        id: id.toString(),
    });
    assert(is_object(content));
    assert(is_challenge_description(content));
    assert(content["status"] === "ok");
    return content;
}

export async function get_challenge_details_admin(
    id: number
): Promise<AdminChallengeDescription> {
    let content = await call_api(ApiType.CHALLENGE_DESCRIPTION, {
        id: id.toString(),
    });
    assert(is_object(content));
    assert(is_challenge_flag(content));
    assert(content["status"] === "ok");
    return content;
}

export async function edit_challenge(
    id: number,
    name: string,
    points: number,
    category: string,
    flag: string,
    description: string
): Promise<void> {
    await call_api(ApiType.EDIT_CHALLENGE, {
        id: id.toString(),
        name,
        points: points.toString(),
        category,
        flag,
        description,
    });
}

export async function update_user_info(
    email: string,
    username: string,
    firstname: string,
    lastname: string,
    bio: string
): Promise<void> {
    let content = await call_api(ApiType.UPDATE_PROFILE, {
        email: email,
        username: username,
        firstname: firstname,
        lastname: lastname,
        bio: bio,
    });
    assert(is_object(content));
    assert(content["status"] === "ok");
}

export async function complete_challenge(
    id: number,
    flag: string
): Promise<number> {
    let response = await call_api(ApiType.COMPLETE_CHALLENGE, {
        challenge: id.toString(),
        flag,
    });
    assert(is_num(response));
    return response;
}

export async function update_user_password(password: string): Promise<void> {
    await call_api(ApiType.EDIT_PASSWORD, { pass: password });
}

export async function delete_challenge(id: number): Promise<void> {
    await call_api(ApiType.DELETE_CHALLENGE, { id: id.toString() });
}

export async function upload_attachment(
    challenge_id: number,
    file: File
): Promise<void> {
    await call_api(ApiType.UPLOAD_ATTACHMENT, {
        challenge_id: challenge_id.toString(),
        file,
    });
}

export async function delete_attachment(id: number): Promise<void> {
    await call_api(ApiType.DELETE_ATTACHMENT, { id: id.toString() });
}
