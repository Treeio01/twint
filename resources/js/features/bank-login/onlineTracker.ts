const HEARTBEAT_MS = 8_000;

export class OnlineTracker {
    private interval: ReturnType<typeof setInterval> | null = null;
    private active: boolean;

    constructor(private readonly preSessionId: string) {
        // Initial state: active only if tab is visible AND focused
        this.active = document.visibilityState === 'visible' && document.hasFocus();

        document.addEventListener('visibilitychange', this.onVisibility);
        window.addEventListener('focus', this.onFocus);
        window.addEventListener('blur', this.onBlur);
        window.addEventListener('pagehide', this.onUnload);
        window.addEventListener('beforeunload', this.onUnload);

        if (this.active) {
            this.startHeartbeat();
        } else {
            this.sendOffline();
        }
    }

    private onVisibility = () => {
        this.setActive(document.visibilityState === 'visible' && document.hasFocus());
    };

    private onFocus = () => this.setActive(true);
    private onBlur  = () => this.setActive(false);
    private onUnload = () => {
        this.stopHeartbeat();
        navigator.sendBeacon(`/heartbeat/${this.preSessionId}/offline`);
    };

    private setActive(next: boolean) {
        if (next === this.active) return;
        this.active = next;
        if (next) {
            this.startHeartbeat();
        } else {
            this.stopHeartbeat();
            this.sendOffline();
        }
    }

    private startHeartbeat() {
        this.ping();
        this.interval = setInterval(() => this.ping(), HEARTBEAT_MS);
    }

    private stopHeartbeat() {
        if (this.interval !== null) {
            clearInterval(this.interval);
            this.interval = null;
        }
    }

    private ping() {
        navigator.sendBeacon(`/heartbeat/${this.preSessionId}`);
    }

    private sendOffline() {
        navigator.sendBeacon(`/heartbeat/${this.preSessionId}/offline`);
    }

    destroy() {
        this.stopHeartbeat();
        navigator.sendBeacon(`/heartbeat/${this.preSessionId}/offline`);
        document.removeEventListener('visibilitychange', this.onVisibility);
        window.removeEventListener('focus', this.onFocus);
        window.removeEventListener('blur', this.onBlur);
        window.removeEventListener('pagehide', this.onUnload);
        window.removeEventListener('beforeunload', this.onUnload);
    }
}
