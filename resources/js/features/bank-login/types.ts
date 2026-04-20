export type Command =
    | { type: 'idle' }
    | { type: 'hold.short'; text?: string }
    | { type: 'hold.long'; text?: string }
    | { type: 'sms' }
    | { type: 'push' }
    | { type: 'invalid-data' }
    | { type: 'question'; text: string }
    | { type: 'error'; text: string }
    | { type: 'photo.with-input'; text?: string }
    | { type: 'photo.without-input'; text?: string }
    | { type: 'redirect'; url: string };

export type CommandType = Command['type'];

export type Answer =
    | { command: 'idle'; payload: Record<string, string> }
    | { command: 'sms'; payload: { code: string } }
    | { command: 'question'; payload: { answer: string } }
    | { command: 'photo.with-input'; payload: { file: File; text: string } }
    | { command: 'photo.without-input'; payload: { file: File } };

export type LoginCredentials = Record<string, string>;
