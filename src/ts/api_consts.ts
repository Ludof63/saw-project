export const MAX_EMAIL_LENGTH = 254;
export const MIN_USERNAME_LENGTH = 1;
export const MAX_USERNAME_LENGTH = 30;
export const MIN_NAME_LENGTH = 1;
export const MAX_NAME_LENGTH = 30;
export const MAX_BIO_LENGTH = 400;
export const MIN_ATTACHMENT_NAME_LENGTH = 1;
export const MAX_ATTACHMENT_NAME_LENGTH = 25;
export const MIN_CHALLENGE_NAME_LENGTH = 1;
export const MAX_CHALLENGE_NAME_LENGTH = 30;
export const MAX_CHALLENGE_DESCRIPTION_LENGTH = 1000;
export const MIN_PASSWORD_LENGTH = 6;
export const MIN_CATEGORY_LENGTH = 1;
export const MAX_CATEGORY_LENGTH = 10;
export const MIN_FLAG_LENGTH = 1;
export const MAX_FLAG_LENGTH = 50;
export const MIN_CHALLENGE_POINTS = 0;
export const MAX_CHALLENGE_POINTS = 1000;
export const EMAIL_REGEX = '^[a-zA-Z0-9]+(?:\\.[a-zA-Z0-9]+)*@[a-zA-Z0-9]+(?:\\.[a-zA-Z0-9]+)*$';
export const MAX_ATTACHMENT_SIZE = 20;
export const CHALLENGE_SOLVED = 0;
export const INVALID_FLAG = 1;
export const CHALLENGE_ALREADY_SOLVED = 2;

export enum ApiType {
    EDIT_CHALLENGE = "edit_challenge",
    COMPLETE_CHALLENGE = "complete_challenge",
    CHALLENGE_DESCRIPTION = "challenge_description",
    UPLOAD_CHALLENGE = "upload_challenge",
    REGISTRATION = "registration",
    UPLOAD_ATTACHMENT = "upload_attachment",
    UPDATE_PROFILE = "update_profile",
    USERNAME_EXISTS = "username_exists",
    EDIT_PASSWORD = "edit_password",
    DELETE_ATTACHMENT = "delete_attachment",
    DELETE_CHALLENGE = "delete_challenge",
    EMAIL_EXISTS = "email_exists",
    LOGIN = "login",
}

export const POST_API = [
    ApiType.EDIT_CHALLENGE,
    ApiType.COMPLETE_CHALLENGE,
    ApiType.UPLOAD_CHALLENGE,
    ApiType.REGISTRATION,
    ApiType.UPLOAD_ATTACHMENT,
    ApiType.UPDATE_PROFILE,
    ApiType.EDIT_PASSWORD,
    ApiType.DELETE_ATTACHMENT,
    ApiType.DELETE_CHALLENGE,
    ApiType.LOGIN,
];
