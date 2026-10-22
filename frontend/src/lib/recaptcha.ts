declare global {
    interface Window {
        grecaptcha?: {
            ready: (callback: () => void) => void;
            execute: (
                siteKey: string,
                options: { action: string },
            ) => Promise<string>;
        };
    }
}

let scriptPromise: Promise<void> | null = null;

function loadRecaptcha(): Promise<void> {
    if (window.grecaptcha) {
        return new Promise<void>((resolve) => {
            window.grecaptcha!.ready(() => resolve());
        });
    }

    if (!scriptPromise) {
        scriptPromise = new Promise<void>((resolve, reject) => {
            const siteKey = import.meta.env.VITE_RECAPTCHA_SITE_KEY;

            if (!siteKey) {
                reject(new Error('reCAPTCHA site key is not configured.'));
                return;
            }

            const script = document.createElement('script');

            script.src = `https://www.google.com/recaptcha/api.js?render=${encodeURIComponent(siteKey)}`;
            script.async = true;
            script.defer = true;

            script.onload = () => {
                if (!window.grecaptcha) {
                    scriptPromise = null;
                    reject(new Error('Failed to initialize reCAPTCHA.'));
                    return;
                }

                window.grecaptcha.ready(() => resolve());
            };

            script.onerror = () => {
                scriptPromise = null;
                reject(new Error('Failed to load reCAPTCHA script.'));
            };

            document.head.appendChild(script);
        });
    }

    return scriptPromise;
}

export async function getRecaptchaToken(
    action: string,
): Promise<string> {
    const siteKey = import.meta.env.VITE_RECAPTCHA_SITE_KEY;

    if (!siteKey) {
        throw new Error('reCAPTCHA site key is not configured.');
    }
    await loadRecaptcha();
    if (!window.grecaptcha) {
        throw new Error('reCAPTCHA is not available.');
    }
    return window.grecaptcha.execute(siteKey, { action });

}