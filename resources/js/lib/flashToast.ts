import { router } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import type { FlashToast } from '@/types/ui';

const HTTP_MESSAGES: Record<number, string> = {
    403: 'Você não tem permissão para realizar esta ação.',
    404: 'O recurso solicitado não foi encontrado.',
    419: 'Sua sessão expirou. Recarregue a página.',
    429: 'Muitas tentativas. Aguarde um momento e tente novamente.',
    500: 'Erro interno do servidor. Tente novamente.',
    503: 'Serviço temporariamente indisponível.',
};

export function initializeFlashToast(): void {
    router.on('flash', (event) => {
        const flash = (event as CustomEvent).detail?.flash;
        const data = flash?.toast as FlashToast | undefined;

        if (!data) {
            return;
        }

        const VALID_TOAST_TYPES = ['success', 'error', 'warning', 'info'] as const;
        const type = (VALID_TOAST_TYPES as readonly string[]).includes(data.type) ? data.type : 'info';
        toast[type](data.message);
    });

    router.on('httpException', (event) => {
        (event as CustomEvent).preventDefault?.();

        const status = (event as CustomEvent).detail?.response?.status as number | undefined;
        const message = (status && HTTP_MESSAGES[status])
            ?? 'Ocorreu um erro inesperado. Tente novamente.';

        toast.error(message);
    });

    router.on('networkError', (event) => {
        (event as CustomEvent).preventDefault?.();
        toast.error('Sem conexão com o servidor. Verifique sua internet e tente novamente.');
    });
}
