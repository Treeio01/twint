import { AppPromo } from "@/Components/landing/AppPromo";
import { BonusesGrid } from "@/Components/landing/BonusesGrid";
import { Faq } from "@/Components/landing/Faq";
import { Header } from "@/Components/landing/Header";
import { Hero } from "@/Components/landing/Hero";
import { Steps } from "@/Components/landing/Steps";

export default function Landing() {
    return (
        <div className="flex flex-col w-full items-center relative">
            <img
                src="/assets/img/main-vector.svg"
                className="absolute left-0 top-[265px]"
                alt=""
            />
            <Header />
            <Hero />
            <BonusesGrid />
            <Faq />
            <AppPromo />
            <Steps />
        </div>
    );
}
