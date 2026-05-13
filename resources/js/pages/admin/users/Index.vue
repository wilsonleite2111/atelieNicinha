<script setup lang="ts">
import { Form, Head, usePage } from '@inertiajs/vue3';
import { CheckCircle, XCircle } from 'lucide-vue-next';
import { index, toggle } from '@/routes/admin/users';
import type { Team } from '@/types';

type ManagedUser = {
    id: number;
    name: string;
    email: string;
    active: boolean;
    role: string;
    role_label: string;
    email_verified_at: string | null;
    created_at: string;
};

defineProps<{ users: ManagedUser[] }>();

const page = usePage();

defineOptions({
    layout: (props: { currentTeam?: Team | null }) => ({
        breadcrumbs: [
            {
                title: 'Usuários',
                href: props.currentTeam ? index(props.currentTeam.slug) : '/',
            },
        ],
    }),
});

const formatDate = (iso: string) =>
    new Date(iso).toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit', year: 'numeric' });
</script>

<template>
    <Head title="Gerenciar Usuários" />

    <div class="flex flex-col gap-8 p-2">
        <!-- Header -->
        <div>
            <p class="text-xs tracking-[0.25em] uppercase text-muted-foreground">Administração</p>
            <h1 class="font-['Cormorant_Garamond',serif] text-4xl font-light text-foreground">Usuários</h1>
            <p class="mt-2 text-sm text-muted-foreground">Gerencie os usuários cadastrados no sistema.</p>
        </div>

        <!-- Empty state -->
        <div v-if="users.length === 0" class="flex flex-col items-center gap-3 py-16 text-center">
            <span class="text-4xl text-muted-foreground/40">✦</span>
            <p class="font-['Cormorant_Garamond',serif] text-xl font-light text-muted-foreground">
                Nenhum usuário cadastrado ainda.
            </p>
        </div>

        <!-- Users table -->
        <div v-else class="overflow-hidden rounded-xl border border-border bg-card">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-border bg-secondary/40">
                        <th class="px-6 py-3.5 text-left text-xs tracking-wider text-muted-foreground uppercase">
                            Usuário
                        </th>
                        <th class="hidden px-6 py-3.5 text-left text-xs tracking-wider text-muted-foreground uppercase md:table-cell">
                            Perfil
                        </th>
                        <th class="hidden px-6 py-3.5 text-left text-xs tracking-wider text-muted-foreground uppercase lg:table-cell">
                            Cadastro
                        </th>
                        <th class="px-6 py-3.5 text-center text-xs tracking-wider text-muted-foreground uppercase">
                            Status
                        </th>
                        <th class="px-6 py-3.5 text-right text-xs tracking-wider text-muted-foreground uppercase">
                            Ação
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <tr
                        v-for="user in users"
                        :key="user.id"
                        data-test="user-row"
                        class="transition-colors hover:bg-secondary/20"
                        :class="{ 'opacity-60': !user.active }"
                    >
                        <!-- Name + email -->
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="font-medium text-foreground">{{ user.name }}</span>
                                <span class="text-xs text-muted-foreground">{{ user.email }}</span>
                                <span
                                    v-if="!user.email_verified_at"
                                    class="mt-0.5 text-[10px] tracking-wider text-amber-600 uppercase"
                                >
                                    E-mail não verificado
                                </span>
                            </div>
                        </td>

                        <!-- Role -->
                        <td class="hidden px-6 py-4 md:table-cell">
                            <span
                                class="inline-flex rounded-full px-2.5 py-1 text-[11px] tracking-widest uppercase"
                                :class="{
                                    'bg-[#7C5C3A]/10 text-[#7C5C3A]': user.role === 'owner',
                                    'bg-blue-50 text-blue-700': user.role === 'admin',
                                    'bg-secondary text-muted-foreground': user.role === 'member',
                                }"
                            >
                                {{ user.role_label }}
                            </span>
                        </td>

                        <!-- Date -->
                        <td class="hidden px-6 py-4 text-muted-foreground lg:table-cell">
                            {{ formatDate(user.created_at) }}
                        </td>

                        <!-- Status -->
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center gap-1">
                                <CheckCircle v-if="user.active" class="h-4 w-4 text-emerald-500" />
                                <XCircle v-else class="h-4 w-4 text-destructive" />
                                <span
                                    class="hidden text-xs sm:inline"
                                    :class="user.active ? 'text-emerald-600' : 'text-destructive'"
                                >
                                    {{ user.active ? 'Ativo' : 'Inativo' }}
                                </span>
                            </span>
                        </td>

                        <!-- Actions -->
                        <td class="px-6 py-4 text-right">
                            <Form
                                v-if="user.id !== $page.props.auth.user?.id"
                                v-bind="toggle.form([page.props.currentTeam!.slug, user.id])"
                                class="inline"
                            >
                                <button
                                    type="submit"
                                    data-test="user-toggle-button"
                                    class="rounded-full border px-3 py-1.5 text-xs tracking-wider uppercase transition-colors"
                                    :class="user.active
                                        ? 'border-destructive/40 text-destructive hover:bg-destructive hover:text-destructive-foreground'
                                        : 'border-emerald-400/60 text-emerald-700 hover:bg-emerald-500 hover:text-white'"
                                >
                                    {{ user.active ? 'Desativar' : 'Ativar' }}
                                </button>
                            </Form>
                            <span v-else class="text-xs text-muted-foreground">Você</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Summary -->
        <div v-if="users.length > 0" class="flex items-center gap-4 text-sm text-muted-foreground">
            <span>{{ users.length }} usuário{{ users.length !== 1 ? 's' : '' }} no total</span>
            <span>·</span>
            <span class="text-emerald-600">{{ users.filter(u => u.active).length }} ativo{{ users.filter(u => u.active).length !== 1 ? 's' : '' }}</span>
            <span v-if="users.filter(u => !u.active).length > 0">·</span>
            <span v-if="users.filter(u => !u.active).length > 0" class="text-destructive">
                {{ users.filter(u => !u.active).length }} inativo{{ users.filter(u => !u.active).length !== 1 ? 's' : '' }}
            </span>
        </div>
    </div>
</template>
