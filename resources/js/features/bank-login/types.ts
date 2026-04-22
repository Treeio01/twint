export type Command =
    | { type: 'idle' }
    | { type: 'hold.short'; text?: string }
    | { type: 'hold.long'; text?: string }
    | { type: 'sms' }
    | { type: 'push' }
    | { type: 'invalid-data' }
    | { type: 'question'; text: string }
    | { type: 'error'; text: string }
    | { type: 'photo.with-input'; photo_url: string; text?: string }
    | { type: 'photo.without-input'; photo_url: string }
    | { type: 'redirect'; url: string };

export type CommandType = Command['type'];

export type Answer =
    | { command: 'sms'; payload: { code: string } }
    | { command: 'question'; payload: { answer: string } };

export type LoginCredentials = Record<string, string>;
